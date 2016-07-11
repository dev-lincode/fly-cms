<?php

namespace Lincode\Fly\Bundle\Service;

use Lincode\Fly\Bundle\Entity\User;
use Lincode\Fly\Bundle\Form\LoginType;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Form\FormError;

class LoginService extends Service {

	private function getForm() {
		$form = $this->createForm(LoginType::class, null, [
			'action' => $this->generateUrl('cms_login_check'),
		]);

		$authenticationUtils = $this->container->get('security.authentication_utils');
		$form->get('_username')->setData($authenticationUtils->getLastUsername());
		
		return $form;
	}

	private function getFormError() {
		$authenticationUtils = $this->container->get('security.authentication_utils');
		$error = $authenticationUtils->getLastAuthenticationError();

		if($error) {
			return true;
		}
		return false;
	}

	public function login() {
		$form = $this->getForm();

		if($this->getFormError()) {
			$form->addError(new FormError("Usuário e/ou senha incorretas"));
		}

		return $form;
	}
}