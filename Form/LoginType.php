<?php

namespace Lincode\Fly\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType {
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('_username', 'email', ['label' => 'E-mail'])
			->add('_password', 'password', ['label' => 'Senha'])
			->add('_submit', 'submit', ["label" => 'Entrar']);
	}

	public function getName() {
	}
}
