<?php

namespace Lincode\Fly\Bundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Yaml\Yaml;

class FlyControllerService
{
    private $om;
    private $formFactory;

    public function __construct(ObjectManager $om, FormFactory $formFactory)
    {
        $this->om = $om;
        $this->formFactory = $formFactory;
    }

    public function getRepository($repository)
    {
        return $this->om->getRepository($repository);
    }

    public function findAll($repository)
    {
        return $this->getRepository($repository)->findAll();
    }

    public function find($repository, $filter = array())
    {
        return $this->getRepository($repository)->findOneBy($filter);
    }

    public function findBy($repository, $filter = array())
    {
        return $this->getRepository($repository)->findBy($filter);
    }

    public function getForm($formType, $entity, $route)
    {
        $form = $this->formFactory->createNamed('fly_form', $formType, $entity, array(
            'action' => $route,
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-success']]);
        return $form;
    }

    public function save($objects)
    {

        if (is_array($objects)) {
            foreach ($objects as $object) {
                $this->om->persist($object);
            }
        } else {
            $this->om->persist($objects);
        }

        $this->om->flush();
    }

    public function remove($objects)
    {

        if (is_array($objects)) {
            foreach ($objects as $object) {
                $this->om->remove($object);
            }
        } else {
            $this->om->remove($objects);
        }

        $this->om->flush();
    }
}
