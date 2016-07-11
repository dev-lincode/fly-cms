<?php

namespace Lincode\Fly\Bundle\Controller;

use Doctrine\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

abstract class BaseController extends Controller
{
    protected $configs = array();
    protected $upload_fields = null;
    protected $crop_fields = null;
    protected $permissions = array('create'=> true, 'edit' => true, 'delete' => true, 'show'=> true);
    protected $userPermissions = array();
    protected $parent = null;
    protected $permissionService = null;

    public function preExecute(){
        $this->permissionService = $this->container->get('cms.permissions.service');
        $this->getUserPermissions();
    }

    protected function indexAction()
    {
        $entities = $this->showResults();

        return array(
            'entities' => $entities,
            'configs' => $this->configs,
            'parent' => $this->parent,
            'permissions' => $this->permissions,
            'userPermissions' => $this->userPermissions
        );
    }

    public function newAction(Request $request)
    {
        $controllerService = $this->get('fly.controller.service');
        $entity = $this->getNewEntity();

        $urlResponse = $this->generateUrl($this->configs['prefix_route'] . '_new');

        $form = $controllerService->getForm($this->getNewEntityForm(), $entity, $urlResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formValidateService = $this->get('fly.form.service');
            if ($formValidateService->validadeForm($form, $this->configs['entity'], $entity)) {
                $this->beforePersist($entity, $form, "new");
                $controllerService->save($entity);

                $this->get('session')->getFlashBag()->add('message', 'Novo registro adicionado com sucesso');

                return $this->redirectToRoute($this->configs['prefix_route'] . '_show', array('id' => $entity->getId()));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'configs' => $this->configs,
            'parent' => $this->parent,
            'permissions' => $this->permissions,
            'userPermissions' => $this->userPermissions
        );
    }

    public function editAction(Request $request, $id)
    {
        $controllerService = $this->get('fly.controller.service');
        $entity = $controllerService->find($this->configs['entity'], array('id' => $id));

        $urlResponse = $this->generateUrl($this->configs['prefix_route'] . '_edit', array('id' => $id));

        $form = $controllerService->getForm($this->getNewEntityForm(), $entity, $urlResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formValidateService = $this->get('fly.form.service');
            if ($formValidateService->validadeForm($form, $this->configs['entity'], $entity)) {
                $this->beforePersist($entity, $form, "edit");
                $controllerService->save($entity);

                $this->get('session')->getFlashBag()->add('message', 'Registro editado com sucesso');

                return $this->redirectToRoute($this->configs['prefix_route'] . '_edit', array('id' => $id));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'configs' => $this->configs,
            'parent' => $this->parent,
            'permissions' => $this->permissions,
            'userPermissions' => $this->userPermissions
        );
    }

    public function showAction($id)
    {
        $controllerService = $this->get('fly.controller.service');
        $entity = $controllerService->find($this->configs['entity'], array('id' => $id));

        return array(
            'entity' => $entity,
            'configs' => $this->configs,
            'parent' => $this->parent,
            'permissions' => $this->permissions,
            'userPermissions' => $this->userPermissions
        );
    }

    public function deleteAction($id)
    {
        $controllerService = $this->get('fly.controller.service');
        $entity = $controllerService->find($this->configs['entity'], array('id' => $id));
        $this->beforeDelete($entity);
        $controllerService->remove($entity);

        $this->get('session')->getFlashBag()->add('message', 'Registro removido com sucesso');

        return $this->redirectToRoute($this->configs['prefix_route']);
    }

    public function deleteAllAction(Request $request)
    {
        $this->get('session')->getFlashBag()->add('message', 'Registros removidos com sucesso');
        return $this->redirectToRoute($this->configs['prefix_route']);
    }

    public function getNewEntity(){
        return new $this->configs['entity_class'];
    }

    public function getNewEntityForm(){
        return $this->configs['entity_form'];
    }

    protected function showResults(){
        $controllerService = $this->get('fly.controller.service');
        return $controllerService->findAll($this->configs['entity']);
    }

    protected function getUserPermissions(){
        $this->userPermissions['create'] = $this->checkPermissions($this->configs['prefix_route'] . '_new');
        $this->userPermissions['edit'] = $this->checkPermissions($this->configs['prefix_route'] . '_edit');
        $this->userPermissions['delete'] = $this->checkPermissions($this->configs['prefix_route'] . '_delete');
        $this->userPermissions['show'] = $this->checkPermissions($this->configs['prefix_route'] . '_show');

        if(isset($this->configs['custom_routes'])){
            foreach($this->configs['custom_routes'] as $route){
                $this->userPermissions[$route['route']] = $this->checkPermissions($route['route']);
            }
        }
    }

    private function checkPermissions($route){
        $user = $this->getUser();
        $params = array();

        if($user->getProfile() && $user->getProfile()->getAdministrator()) {
            return true;
        }

        if($user->getProfile()) {
            foreach($user->getProfile()->getPermissions() as $permission) {
                $pRoutes = explode("|", $permission->getRoute());
                foreach($pRoutes as $pRoute) {
                    if($pRoute == $route && $this->permissionService->hasParams($permission->getParams(), $params)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function beforePersist($entity, $form, $method) {}
    protected function beforeDelete($entity) {}
}