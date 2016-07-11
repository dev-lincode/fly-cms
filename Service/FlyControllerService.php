<?php

namespace Lincode\Fly\Bundle\Service;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Yaml\Yaml;

class FlyControllerService extends Service
{

    public function getRepository($repository){
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository($repository);
    }

    public function findAll($repository){
        return $this->getRepository($repository)->findAll();
    }

    public function find($repository, $filter = array()){
        return $this->getRepository($repository)->findOneBy($filter);
    }

    public function findBy($repository, $filter = array()){
        return $this->getRepository($repository)->findBy($filter);
    }

    public function getForm($formType, $entity, $route){
        $form = $this->createForm($formType, $entity, array(
			'action' => $route,
			'method' => 'POST',
		));

        $form->add('submit', SubmitType::class, ['label' => 'Salvar', 'attr' => ['class' => 'btn btn-success']]);
        return $form;
    }

    public function save($objects){

        $em = $this->getDoctrine()->getManager();

        if(is_array($objects)){
            foreach ($objects as $object){
                $em->persist($object);
            }
        }else{
            $em->persist($objects);
        }

        $em->flush();
    }

    public function remove($objects){

        $em = $this->getDoctrine()->getManager();

        if(is_array($objects)){
            foreach ($objects as $object){
                $em->remove($object);
            }
        }else{
            $em->remove($objects);
        }

        $em->flush();
    }
}