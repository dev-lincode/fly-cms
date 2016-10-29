<?php

namespace Lincode\Fly\Bundle\Service;

use Lincode\Fly\Bundle\Entity\User;
use Lincode\Fly\Bundle\Form\LoginType;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Form\FormError;

class LoginService
{
    private $authenticationUtils;
    private $formFactory;
    private $router;

    public function __construct(AuthenticationUtils $authenticationUtils, FormFactory $formFactory, Router $router)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    private function getForm()
    {
        $form = $this->formFactory->createNamed(null, LoginType::class, null, [
            'action' => $this->router->generate('cms_login_check'),
        ]);

        $form->get('_username')->setData($this->authenticationUtils->getLastUsername());

        return $form;
    }

    private function getFormError()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();

        if ($error) {
            return true;
        }
        return false;
    }

    public function login()
    {
        $form = $this->getForm();

        if ($this->getFormError()) {
            $form->addError(new FormError("Usuário e/ou senha incorretas"));
        }

        return $form;
    }
}
