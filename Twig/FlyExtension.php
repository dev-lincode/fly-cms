<?php

namespace Lincode\Fly\Bundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FlyExtension extends \Twig_Extension {
    private $container;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('routeExists', array($this, 'routeExists'))
        );
    }


    public function routeExists($name) {
        $router = $this->container->get('router');
        return (null === $router->getRouteCollection()->get($name)) ? false : true;
    }
}