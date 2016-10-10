<?php
namespace Lincode\Fly\Bundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Lincode\Fly\Bundle\Service\PermissionService;

class BeforeControllerListener {

	protected $permissionService;

	public function __construct($permissionService) {
		$this->permissionService = $permissionService;
	}

	public function onKernelController(FilterControllerEvent $event) {
		$controller = $event->getController();


		if (!is_array($controller)) {
			return;
		}

		$request = $event->getRequest();
		$route   = $request->get('_route');

		$controller = $controller[0];

		if(is_object($controller) && method_exists($controller,"preExecute") )
		{
			$controller->preExecute();
		}

		if(!$this->needCMSPermission(get_class($controller),$route, $request->getPathInfo())) {
			return;
		}

		$user = $controller->getUser();

		if(!$user){
			return;
		}

		/* Caso for um administrador, possui acesso a tudo */
		if($user->getProfile() && $user->getProfile()->getAdministrator()) {
			return;
		}

		if(empty($route))
			return;

		$params  = $request->get('_route_params');

		/* Informações do perfil do usuário */
		if($this->isPersonalInformation($user, $route, $params)) {
			return;
		}

		/* Permissoes */
		if($user->getProfile()) {
			foreach($user->getProfile()->getPermissions() as $permission) {
				$pRoutes = explode("|", $permission->getRoute());
				foreach($pRoutes as $pRoute) {
					if($pRoute == $route && $this->permissionService->hasParams($permission->getParams(), $params)) {
						return;
					}
				}
			}
		}

		$controller->get('session')->getFlashBag()->add('title', 'CMS');
		$controller->get('session')->getFlashBag()->add('error', 'Você não tem permissão para acessar essa rota.');

		$redirectUrl = $controller->generateUrl('fly_dashboard');
		$event->setController(function() use ($redirectUrl) {
			return new RedirectResponse($redirectUrl);
		});
	}

	private function needCMSPermission($className, $route, $pathInfo) {
		/* Verificar apenas as rotas que pertecem ao CMS*/
		if(strpos($pathInfo, "/") === false) {
			return false;
		}

		/* Classes pertencentes ao Symfony não devem ser verificadas */
		if(strpos($className, "Symfony\\") === 0) {
			return false;
		}

		/* Rotas do CMS que não fazem parte do controle de permissões */
		switch($className) {
			case "Lincode\\Fly\\Bundle\\Controller\\LoginController":
			case "Lincode\\Fly\\Bundle\\Controller\\MenuController":
			case "Lincode\\Fly\\Bundle\\Controller\\DashboardController":
				return false;
				break;
		}

		switch($route) {
			case "cms_gallery_upload":
				return false;
				break;
		}

		$json = $this->permissionService->getJson();
		if(array_key_exists('permissions', $json)) {
			foreach($json['permissions'] as $item) {
				if(!array_key_exists('route', $item) || $item['route'] != $route)
					continue;

				/* Já achou a rota, se não for free cancela */
				if(!array_key_exists('permission', $item) || $item['permission'] != 'free')
					break;

				return false;
			}
		}

		return true;
	}

	private function isPersonalInformation($user, $route, $params) {
		switch($route) {
			case "fly_user_edit":
				break;
			case "fly_user_update":
				break;
			default:
				return false;
				break;
		}

		/* Verificacao se o id é o do cliente corrente */
		if(!array_key_exists('id', $params) || $params['id'] != $user->getId()) {
			return false;
		}

		return true;
	}
}