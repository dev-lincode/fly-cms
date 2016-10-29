<?php

namespace Lincode\Fly\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lincode\Fly\Bundle\Entity\UserProfilePermission;
use Lincode\Fly\Bundle\Controller\BaseController;

/**
 * User controller.
 *
 * @Route("/user_profile")
 */
class UserProfileController extends BaseController
{

    private $password = null;
    protected $permissionService;

    protected $configs = array(
        'prefix_route' => 'fly_user_profile',
        'singular_name' => 'Perfil de Usuário',
        'plural_name' => 'Perfis de Usuário',
        'entity' => 'FlyBundle:UserProfile',
        'entity_class' => 'Lincode\Fly\Bundle\Entity\UserProfile',
        'entity_form' => 'Lincode\Fly\Bundle\Form\UserProfileType',
        'title_field' => 'name',
        'list_fields' => array('name' => 'Nome', 'administrator'=> array('type' => 'boolean', 'label' => 'Administrator', 'class' => 'col-sm-1')),
        'show_fields' => array('name' => 'Nome', 'administrator'=> array('type' => 'boolean', 'label' => 'Administrator', 'class' => 'col-sm-1'))
    );

    /**
     * Lists all Page entities.
     *
     * @Route("/", name="fly_user_profile")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:index.html.twig")
     */
    public function indexAction()
    {
        return parent::indexAction();
    }

    /**
     * Displays a form to create a new Page entity.
     *
     * @Route("/new", name="fly_user_profile_new")
     * @Method({"GET", "POST"})
     * @Template("FlyBundle:UserProfile:new.html.twig")
     */
    public function newAction(Request $request)
    {
        return parent::newAction($request);
    }

    /**
     * Finds and displays a Page entity.
     *
     * @Route("/show/{id}", name="fly_user_profile_show")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:show.html.twig")
     */
    public function showAction($id)
    {
        return parent::showAction($id);
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/edit/{id}", name="fly_user_profile_edit")
     * @Method({"GET", "POST"})
     * @Template("FlyBundle:UserProfile:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        return parent::editAction($request, $id);
    }

    /**
     * Deletes a Page entity.
     *
     * @Route("/delete/{id}", name="fly_user_profile_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }
    /**
     * Deletes a Page entity.
     *
     * @Route("/deleteAll", name="fly_user_profile_delete_all")
     * @Method("POST")
     */
    public function deleteAllAction(Request $request)
    {
        return parent::deleteAllAction($request);
    }

    protected function beforeDelete($entity)
    {
        $controllerService = $this->get('fly.controller.service');
        $results = $controllerService->findBy('FlyBundle:UserProfilePermission', array('profile' => $entity->getId()));

        foreach ($results as $result) {
            $controllerService->remove($result);
        }
    }

    protected function afterPersist($entity, $form, $method)
    {
        $this->savePermissions($entity);
    }

    public function listPermissionsAction($id = null)
    {
        $json = $this->permissionService->getJson();

        $controllerService = $this->get('fly.controller.service');
        $listRoutes = $controllerService->getRepository('FlyBundle:UserProfilePermission')->listRoutes($id);

        $menuRoutes = array();
        if (array_key_exists('menu', $json)) {
            $menuRoutes = $this->getMenuRoutes($json['menu']);
            if (count($listRoutes) > 0) {
                foreach ($menuRoutes as $key => $menuRoute) {
                    foreach ($listRoutes as $listRoute) {
                        if ($menuRoute['params'] == $listRoute['params']) {
                            if ($menuRoute['route'] . "_new" == $listRoute['route']) {
                                $menuRoutes[$key]['create'] = true;
                            }
                            if ($menuRoute['route'] == $listRoute['route']) {
                                $menuRoutes[$key]['show'] = true;
                            }
                            if ($menuRoute['route'] . "_edit" == $listRoute['route']) {
                                $menuRoutes[$key]['update'] = true;
                            }
                            if ($menuRoute['route'] . "_delete" == $listRoute['route']) {
                                $menuRoutes[$key]['delete'] = true;
                            }
                        }
                    }
                }
            }
        }

        $specificRoutes = array();
        if (array_key_exists('permissions', $json)) {
            $specificRoutes = $this->getSpecificRoutes($json['permissions']);
            if (count($listRoutes) > 0) {
                foreach ($specificRoutes as $key => $permission) {
                    foreach ($listRoutes as $listRoute) {
                        if ($permission['route'] == $listRoute['route'] && $permission['params'] == $listRoute['params']) {
                            $specificRoutes[$key]['permitted'] = true;
                        }
                    }
                }
            }
        }

        return $this->render('FlyBundle:UserProfile:permissions.html.twig', array(
            "routes"   => $menuRoutes,
            "specifics" => $specificRoutes,
        ));
    }


    private function getMenuRoutes($json = null, $parent = "", $separator = ">")
    {
        if ($json == null) {
            return array();
        }

        $routes = array();
        foreach ($json as $item) {
            if (array_key_exists("route", $item)) {
                $params = (array_key_exists("params", $item) ? json_encode($item['params']) : null);
                $routes[] = array(
                    "route" => $item["route"],
                    "label" => (!empty($parent)?$parent . " > " . $item['label']:$item['label']),
                    "params" => $params
                );
            }

            if (array_key_exists("node", $item)) {
                $routes = array_merge($routes, $this->getMenuRoutes($item['node'], (!empty($parent)?$parent . " > " . $item['label']:$item['label'])));
            }
        }
        return $routes;
    }

    private function getSpecificRoutes($json)
    {
        $routes = array();
        foreach ($json as $line) {
            if (!array_key_exists('permission', $line) || $line['permission'] != "profile") {
                continue;
            }

            $permission['label']     = array_key_exists('label', $line) ? $line['label'] : null;
            $permission['route']     = array_key_exists('route', $line) ? $line['route'] : null;
            $permission['params']    = array_key_exists('params', $line) ? $line['params'] : null;
            $permission['permitted'] = false;
            $routes[] = $permission;
        }
        return $routes;
    }

    private function savePermissions($profile)
    {
        $request = $this->container->get('request');
        $routes  = $request->request->get("fly_profile_permission");

        $controllerService = $this->get('fly.controller.service');
        $controllerService->getRepository('FlyBundle:UserProfilePermission')->removeFromProfile($profile);

        /* Como é administrador, não precisa inserir rotas */
        if ($profile->getAdministrator()) {
            return;
        }

        if (!$routes) {
            return;
        }

        foreach ($routes as $route) {
            if (array_key_exists("create", $route)) {
                $this->addRoute($profile, $route['route'] . '_new', $route['params']);
                $this->addRoute($profile, $route['route'] . '_create', $route['params']);
            }

            if (array_key_exists("show", $route)) {
                $this->addRoute($profile, $route['route'], $route['params']);
                $this->addRoute($profile, $route['route'] . '_show', $route['params']);
            }

            if (array_key_exists("update", $route)) {
                $this->addRoute($profile, $route['route'] . '_edit', $route['params']);
                $this->addRoute($profile, $route['route'] . '_update', $route['params']);
            }

            if (array_key_exists("delete", $route)) {
                $this->addRoute($profile, $route['route'] . '_delete', $route['params']);
                $this->addRoute($profile, $route['route'] . '_delete_all', $route['params']);
            }
        }

        $routes = $request->request->get("fly_profile_specific");
        if ($routes && is_array($routes)) {
            foreach ($routes as $route) {
                if (array_key_exists('permitted', $route)) {
                    $this->addRoute($profile, $route['route'], $route['params']);
                }
            }
        }
    }

    private function addRoute($profile, $route, $params)
    {
        $controllerService = $this->get('fly.controller.service');

        $userProfilePermission = new UserProfilePermission();
        $userProfilePermission->setProfile($profile);
        $userProfilePermission->setRoute($route);
        $userProfilePermission->setParams($params);

        $controllerService->save($userProfilePermission);
    }
}
