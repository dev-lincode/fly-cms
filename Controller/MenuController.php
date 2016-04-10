<?php

namespace Lincode\Fly\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {

	protected $permissionService;

	public function preExecute(){
		$this->permissionService = $this->container->get('cms.permissions.service');
	}

	public function menuAction($active, $activeParams) {
		$json = $this->permissionService->getJson();
		if(!array_key_exists('menu', $json)) {
			throw new \Exception('CONFIG JSON - Não possui menu declarado.');
		}

		$json = $json['menu'];
		$menu = $this->parseMenu($json, $active, $activeParams);

		return $this->render('FlyBundle:Template:menu.html.twig', array(
			"itens"  => $menu,
			"parent" => null,
			"active" => $active
		));
	}

	private function parseMenu($json, $active, $activeParams) {
		$parsed = array();
		foreach($json as $item) {
			if ( $item['show'] ) {
				if ( array_key_exists( "route", $item ) ) {
					if ( ! $this->routeExists( $item['route'] ) ) {
						throw new \Exception( 'MENU: Não existe rota "' . $item['route'] . '" informada no menu.' );
					}

					$params = ( array_key_exists( "params", $item ) ? $item['params'] : array() );
					if ( ! $this->userCanAccess( $item['route'], ( count( $params ) ? json_encode( $params ) : null ) ) ) {
						continue;
					}

					if ( $active == $item['route'] && $this->permissionService->hasParams( $activeParams, $params ) ) {
						$item['active'] = 1;
					}

					$item['route'] = $this->generateUrl( $item['route'], $params );
					if ( array_key_exists( "node", $item ) ) {
						unset( $item['node'] );
					}
				} else if ( array_key_exists( "node", $item ) ) {
					$item['node'] = $this->parseMenu( $item['node'], $active, $activeParams );
					if ( ! count( $item['node'] ) ) {
						continue;
					}

					if ( $this->isParent( $item['node'] ) ) {
						$item['parent'] = 1;
					}
				}

				if ( array_key_exists( "params", $item ) ) {
					unset( $item['params'] );
				}
				$parsed[] = $item;
			}
		}

		return $parsed;
	}

	private function userCanAccess($route, $params) {
		if(!$this->getUser()->getProfile()) {
			return false;
		}

		if($this->getUser()->getProfile()->getAdministrator()) {
			return true;
		}

		foreach($this->getUser()->getProfile()->getPermissions() as $entity) {
			if($route == $entity->getRoute() && $params == $entity->getParams())
				return true;
		}

		return false;
	}

	private function isParent($node) {
		foreach($node as $item) {
			if(array_key_exists("active", $item) || array_key_exists("parent", $item)) {
				return true;
			}
		}

		return false;
	}

	public function routeExists($name) {
		$router = $this->container->get('router');
		return (null === $router->getRouteCollection()->get($name)) ? false : true;
	}
}