<?php
namespace Lincode\Fly\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Dashboard controller.
 *
 * @Route("/")
 */
class DashboardController extends Controller
{
	/**
	 * Dashboard Index
	 *
	 * @Route("/", name="cms_dashboard")
	 * @Method("GET")
	 */
	public function indexAction()
	{

		dump('olaa');
		die;
	}
}