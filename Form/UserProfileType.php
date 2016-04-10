<?php

namespace Lincode\Fly\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', 'text', ["label" => "Nome"])
			->add('administrator', 'checkbox',["label" => "É administrador?", "required" => false])
		;
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Lincode\Fly\Bundle\Entity\UserProfile'
		));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'fly_userprofile';
	}
}