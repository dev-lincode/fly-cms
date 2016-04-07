<?php

namespace Lincode\Fly\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Video controller.
 *
 * @Route("/")
 */
class LoginController extends Controller
{
	/**
	 *
	 * @Route("/login{trailingSlash}", name="cms_login", defaults={"trailingSlash": "/"}, requirements={ "trailingSlash": "[/]{0,1}" })
	 */
	public function loginAction() {
		return $this->container->get('cms.login.service')->login();
	}
	
	/**
	 * 
	 * @Route("/login/check", name="cms_login_check")
	 */
	
	public function loginCheckAction() {
	}
	
	/**
	 * 
	 * @Route("/logout", name="cms_logout")
	 */
	
	public function logoutAction() {
	}
}