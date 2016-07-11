<?php

namespace Lincode\Fly\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType {
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('_username', EmailType::class, ['label' => 'E-mail'])
			->add('_password', PasswordType::class, ['label' => 'Senha']);
	}

	public function getBlockPrefix() { }
}
