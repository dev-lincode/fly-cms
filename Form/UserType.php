<?php

namespace Lincode\Fly\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('name', TextType::class, ['label' => 'Nome', 'required' => true])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
            ->add('password', RepeatedType::class, ['type' => PasswordType::class,
                'invalid_message' => 'Campos de senha não conferem',
                'required' => true,
                'first_options'  => array('label' => 'Senha'),
                'second_options' => array('label' => 'Confirmar senha')])
            ->add('isActive', CheckboxType::class, ['label' => 'Ativo?', 'required' => false])
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
