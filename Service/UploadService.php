<?php

namespace Lincode\Fly\Bundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class UploadService
{
    private $paramters;
    private $om;

    public function __construct(ObjectManager $om, $paramters)
    {
        $this->om = $om;
        $this->paramters = $paramters;
    }

    private function needUploadFile($entity, $field) {
        if(is_null($entity) || is_null($field)) {
            return false;
        }

        $getter = "get" . ucwords($field);
        $setter = "set" . ucwords($field);

        if(!is_callable(array($entity, $getter)) || !is_callable(array($entity, $setter))) {
            return false;
        }

        if(gettype($entity->$getter()) != "object" || get_class($entity->$getter()) != "Symfony\Component\HttpFoundation\File\UploadedFile") {
            if(gettype($entity->$getter()) == 'string' && $entity->$getter() == "@#REMOVE#@") {
                $entity->$setter(null);
                $oldField = $this->getOldValue($entity, $field);
                if($oldField && file_exists($oldField))
                    unlink($oldField);
            } else {
                $old = $this->om->getUnitOfWork()->getOriginalEntityData($entity);
                if(is_array($old) && array_key_exists($field, $old) && is_null($entity->$getter())) {
                    $entity->$setter($old[$field]);
                }
            }
            return false;
        }

        return true;
    }

    public function generateFileName($file, $originalName) {
        if(!$originalName) {
            $originalName = explode('.', $file->getClientOriginalName());
            $fileName = md5(uniqid()) . "." . end($originalName);
        } else {
            $fileName = $file->getClientOriginalName();
        }
        return $fileName;
    }

    public function getOldValue($entity, $field) {
        $old = $this->om->getUnitOfWork()->getOriginalEntityData($entity);

        if(is_array($old) && array_key_exists($field, $old) && !is_null($old[$field])) {
            return $old[$field];
        }
        return null;
    }

    public function moveFile($file, $destiny, $fileName) {
        $path = $this->paramters['upload_path'] . '/'  . $destiny;

        if(!file_exists($path))
            mkdir($path, 0777, true);

        $file->move($path, $fileName);
        chmod($path . $fileName, 0777);
    }

    public function uploadFile($entity, $field, $destiny, $originalName = false) {
        if(is_null($destiny) || !$this->needUploadFile($entity, $field)) {
            return false;
        }

        if(substr($destiny, -1) != "/")
            $destiny .= "/";

        $getter = "get" . ucwords($field);
        $setter = "set" . ucwords($field);

        $file = $entity->$getter();
        $fileName = $this->generateFileName($file, $originalName);

        $this->moveFile($file, $destiny, $fileName);

        $entity->$setter($destiny . $fileName);

        $oldField = $this->getOldValue($entity, $field);
        if($oldField && file_exists($oldField))
            unlink($oldField);

        return true;
    }
}