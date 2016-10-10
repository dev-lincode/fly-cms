<?php

namespace Lincode\Fly\Bundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntityFormService {

    private $om;

    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    public function validadeForm($form, $repository, $entity) {
        $metadata = $this->om->getClassMetadata($repository);

        $fields = $metadata->getFieldNames();
        foreach($fields as $field) {
            if(!$form->has($field))
                continue;

            $map = $metadata->getFieldMapping($field);
            $getField = 'get' . ucwords($map['fieldName']);

            if(!$map['nullable']) {
                $error = false;

                if(is_null($entity->$getField())) {
                    $error = true;
                }

                if($form->get($field)->getConfig()->getType()->getName() == 'hidden') {
                    continue;
                }

                if($form->get($field)->getConfig()->getType()->getName() == 'file') {
                    if(gettype($entity->$getField()) == 'string' && $entity->$getField() == "@#REMOVE#@") {
                        $error = true;
                    } else {
                        $old = $this->om->getUnitOfWork()->getOriginalEntityData($entity);
                        if(is_array($old) && array_key_exists($field, $old) && is_null($entity->$getField())) {
                            continue;
                        }
                    }
                }

                if($error) {
                    $error = new FormError("Campo obrigatório. Favor, preencha com um valor válido.");
                    $form->get($field)->addError($error);
                    return false;
                }
            }

            if(!is_null($entity->$getField())) {
                if($map['unique']) {
                    $qb = $this->om->getRepository($repository)->createQueryBuilder('e');
                    $qb->where('e.' . $field . ' = :key')->setParameter('key', $entity->$getField());

                    if($entity->getId()) {
                        $qb->andWhere('e.id != :id')->setParameter('id', $entity->getId());
                    }
                    $result = $qb->setMaxResults(1)->getQuery()->getResult();
                    if(count($result)) {
                        $error = new FormError("Campo com valor único. Favor, preencha com um valor diferente.");
                        $form->get($field)->addError($error);
                        return false;
                    }
                }

                if($map['type'] == 'string') {
                    if(strlen($entity->$getField()) > $map['length']) {
                        $error = new FormError("Campo com valor maior que o permitido. Insira no máximo " . $map['length'] . " caracteres.");
                        $form->get($field)->addError($error);
                        return false;
                    }
                }

                if($map['type'] == 'integer') {
                    if(filter_var($entity->$getField(), FILTER_VALIDATE_INT) === false) {
                        $error = new FormError("Valor inválido. Insira um valor numeral inteiro.");
                        $form->get($field)->addError($error);
                        return false;
                    }
                }

                if($map['type'] == 'decimal') {
                    $setField = 'set' . ucwords($map['fieldName']);
                    if(strpos($entity->$getField(), ",") !== false) {
                        $entity->$setField(str_replace(',', '.', $entity->$getField()));
                    }

                    if(!is_numeric($entity->$getField())) {
                        $error = new FormError("Valor inválido. Insira um valor numeral decimal.");
                        $form->get($field)->addError($error);
                        return false;
                    }

                    $_float = explode(".", $entity->$getField() + 0);
                    if(strlen($_float[0]) > $map['precision'] && $map['precision'] > 0) {
                        $error = new FormError("Valor maior que o permitido. Utilize somente " . $map['precision'] . " casas numéricas à esquerda.");
                        $form->get($field)->addError($error);
                        return false;
                    }

                    $entity->$setField(round($entity->$getField(), $map['scale']));
                }

                if($map['type'] == 'array') {
                    if($form->get($field)->getConfig()->getType()->getName() == 'gallery') {
                        $old = $this->om->getUnitOfWork()->getOriginalEntityData($entity);
                        if(array_key_exists($field, $old) && !is_null($old[$field]) && is_array($entity->$getField())) {
                            foreach($old[$field] as $oldField) {
                                if($oldField && !in_array($oldField, $entity->$getField()) && file_exists($oldField)) {
                                    unlink($oldField);
                                }
                            }
                        }
                    }
                }
            }
        }

        $associationMappings = $metadata->getAssociationMappings();
        foreach($associationMappings as $association) {
            if(!$form->has($association['fieldName']))
                continue;

            if($association['type'] == ClassMetadataInfo::MANY_TO_ONE || $association['type'] == ClassMetadataInfo::ONE_TO_ONE) {
                $getField = 'get' . ucwords($association['fieldName']);

                if(is_callable(array($entity, $getField)) && $entity->$getField()) {
                    $subEntity = $entity->$getField();
                    $subForm = $form->get($association['fieldName']);

                    if(!$this->validadeForm($subForm, $association['targetEntity'], $subEntity)) {
                        return false;
                    }
                }
            }

            if($association['type'] == ClassMetadataInfo::MANY_TO_MANY || $association['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                $subForms = $form->get($association['fieldName']);
                foreach($subForms->all() as $key => $subForm) {

                    if(is_array($subForms->getData())) {
                        $subEntity = $subForms->getData()[$key];
                    } else {
                        $subEntity = $subForms->getData()->get($key);
                    }

                    if(!$this->validadeForm($subForm, $association['targetEntity'], $subEntity)) {
                        return false;
                    }
                }

            }
        }

        return true;
    }

    public function loadFieldsType($formType, &$listFields, $repository) {
        $metadata = $this->om->getClassMetadata($repository);
        $associationMappings = $metadata->getAssociationMappings();

        $form = $this->getContainer()->get('form.factory')->create($formType, null, array());
        foreach($listFields as $key => $field) {
            if(array_key_exists('type', $listFields[$key]) && $listFields[$key]['type']) {
                continue;
            }

            if(!$form->has($key)) {
                $listFields[$key]['type'] = null;
                continue;
            }

            if(isset($associationMappings[$key])) {
                if($associationMappings[$key]['type'] == ClassMetadataInfo::MANY_TO_MANY || $associationMappings[$key]['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                    $listFields[$key]['order'] = false;
                }
            }

            $listFields[$key]['type'] = $form->get($key)->getConfig()->getType()->getName();
        }
    }

    public function isDeletable($repository, $entity) {
        $metadata = $this->om->getClassMetadata($repository);

        $associationMappings = $metadata->getAssociationMappings();
        foreach($associationMappings as $association) {

            if($association['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                $getField = 'get' . ucwords($association['fieldName']);

                if(!$association['isCascadeRemove']) {
                    $dql = 'SELECT COUNT(e) FROM ' . $association['targetEntity'] . " e ";
                    if($association['mappedBy'])
                        $dql .= "WHERE e." . $association['mappedBy'] . " = ?1";
                    else if($association['inversedBy'])
                        $dql .= "WHERE e." . $association['inversedBy'] . " = ?1";

                    $query = $this->om->createQuery($dql);
                    $query->setParameter(1, $entity);

                    if($query->getSingleScalarResult() > 0) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}