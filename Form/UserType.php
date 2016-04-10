<?php

namespace Lincode\Fly\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile', null, ['label' => 'Perfil', 'required' => false])
            ->add('name', 'text', ['label' => 'Nome', 'required' => true])
            ->add('email', 'email', ['label' => 'Email', 'required' => true])
            ->add('password', 'password', ['label' => 'Senha', 'required' => false])
            ->add('confirmPassword', 'password', ['label' => 'Cofirmar Senha', 'required' => false])
            ->add('isActive', 'checkbox', ['label' => 'Ativo?', 'required' => false])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lincode\Fly\Bundle\Entity\User'
        ));
    }
}
