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
		$form = $this->createForm(new LoginType(), null, [
			'action' => $this->generateUrl('cms_login_check'),
		]);

		$session = $this->getRequest()->getSession();
		$form->get('_username')->setData($session->get(SecurityContext::LAST_USERNAME));

		return $form;
	}

	private function getFormError() {
		$session = $this->getRequest()->getSession();
		$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		$session->remove(SecurityContext::AUTHENTICATION_ERROR);
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

	public function forceLogin(Member $member) {
		$request = $this->getRequest();

		$token = new UsernamePasswordToken($member, $member->getPassword(), "restricted_area", $member->getRoles());
		$this->container->get("security.context")->setToken($token);

		$event = new InteractiveLoginEvent($request, $token);
		$this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
	}
}