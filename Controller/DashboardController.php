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
    protected $configs = [
        'prefix_route' => 'fly_dashboard'
    ];

    /**
     * Dashboard Index
     *
     * @Route("/", name="fly_dashboard")
     * @Method("GET")
     * @Template("FlyBundle:Dashboard:index.html.twig")
     */
    public function indexAction()
    {
        return array('configs' => $this->configs);
    }
}
