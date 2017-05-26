<?php
namespace Lincode\Fly\Bundle\FormType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends CollectionType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__name__',
            'type' => 'text',
            'options' => array(),
            'delete_empty' => false,
            'data_class' => null,
        ));
    }

    public function getExtendedType()
    {
        return CollectionType::class;
    }

    public function getBlockPrefix()
    {
        return 'gallery';
    }

}