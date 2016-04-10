<?php

namespace Lincode\Fly\Bundle\Controller;

use Doctrine\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lincode\Fly\Bundle\Service\PermissionService;

abstract class BaseController extends Controller
{
	protected $configs = array();
	protected $upload_fields = null;
	protected $crop_fields = null;
	protected $permissions = array('create'=> true, 'edit' => true, 'delete' => true, 'show'=> true);
	protected $userPermissions = array();
	protected $parent = null;
	protected $permissionService;

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

	protected function createAction(Request $request)
	{
		$entity = $this->getNewEntity();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			if(count($this->upload_fields) > 0) {
				foreach ( $this->upload_fields as $key => $value ) {
					$file = $form[ $key ]->getData();
					if ( $file ) {
						$picture = $this->get( "tools" )->uploadFile( $file, $value['path'] );
						if ( isset( $this->crop_fields[ $key ] ) ) {
							foreach ( $this->crop_fields[ $key ] as $k => $v ) {
								$params['src']    = $value['path'];
								$params['dist']   = $value['path'] . '/' . $k;
								$params['width']  = $v['width'];
								$params['height'] = $v['height'];

								$this->get( "tools" )->resizeImage( $picture, $params, $v['crop'] );
							}
						}
						call_user_func_array( array( $entity, $value['set'] ), array( $picture ) );
					}
				}
			}
			$this->beforePersist($entity, $form, "new");
			$em->persist($entity);
			$this->afterPersist($entity, $form, "new");
			$em->flush();

			$this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
			$this->get('session')->getFlashBag()->add('message', 'Novo registro adicionado com sucesso');

			$urlParams['id'] = $entity->getId();

			if($this->parent){
				$urlParams['parent'] = $this->parent->getId();
			}

			return $this->redirect($this->generateUrl($this->configs['prefix_route'] . '_show', $urlParams));

		}

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
			'configs' => $this->configs,
			'parent' => $this->parent,
			'permissions' => $this->permissions,
			'userPermissions' => $this->userPermissions
		);
	}

	protected function newAction()
	{
		$entity = $this->getNewEntity();
		$form   = $this->createCreateForm($entity);

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
			'configs' => $this->configs,
			'parent' => $this->parent,
			'permissions' => $this->permissions,
			'userPermissions' => $this->userPermissions
		);
	}

	protected function showAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository($this->configs['entity'])->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Page entity.');
		}

		return array(
			'entity'      => $entity,
			'configs' => $this->configs,
			'parent' => $this->parent,
			'permissions' => $this->permissions,
			'userPermissions' => $this->userPermissions
		);
	}

	protected function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository($this->configs['entity'])->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Page entity.');
		}

		$form = $this->createEditForm($entity);

		return array(
			'entity'      => $entity,
			'form'   => $form->createView(),
			'configs' => $this->configs,
			'parent' => $this->parent,
			'permissions' => $this->permissions,
			'userPermissions' => $this->userPermissions
		);
	}

	protected function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository($this->configs['entity'])->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Page entity.');
		}
		if(count($this->upload_fields) > 0) {
			foreach ( $this->upload_fields as $key => $value ) {
				$files[ $key ] = call_user_func( array( $entity, $value['get'] ) );
			}
		}

		$form = $this->createEditForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			if(count($this->upload_fields) > 0) {
				foreach ( $this->upload_fields as $key => $value ) {
					$file = $form[ $key ]->getData();
					if ( $file ) {
						$picture = $this->get( "tools" )->uploadFile( $file, $value['path'] );

						if ( file_exists( $value['path'] . '/' . $files[ $key ] ) ) {
							@unlink( $value['path'] . '/' . $files[ $key ] );
						}

						if ( isset( $this->crop_fields[ $key ] ) ) {
							foreach ( $this->crop_fields[ $key ] as $k => $v ) {
								$params['src']    = $value['path'];
								$params['dist']   = $value['path'] . '/' . $k;
								$params['width']  = $v['width'];
								$params['height'] = $v['height'];

								$this->get( "tools" )->resizeImage( $picture, $params, $v['crop'] );

								if ( file_exists( $value['path'] . '/' . $k . '/' . $files[ $key ] ) ) {
									@unlink( $value['path'] . '/' . $k . '/' . $files[ $key ] );
								}
							}
						}
						call_user_func_array( array( $entity, $value['set'] ), array( $picture ) );
					} else {
						call_user_func_array( array( $entity, $value['set'] ), array( $files[ $key ] ) );
					}
				}
			}
			$this->beforePersist($entity, $form, "edit");
			$em->persist($entity);
			$this->afterPersist($entity, $form, "edit");

			$em->flush();

			$this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
			$this->get('session')->getFlashBag()->add('message', 'Registro editado com sucesso');

			$urlParams['id'] = $id;
			if($this->parent){
				$urlParams['parent'] = $this->parent->getId();
			}

			return $this->redirect($this->generateUrl($this->configs['prefix_route'] . '_edit', $urlParams));
		}

		return array(
			'entity'      => $entity,
			'form'   => $form->createView(),
			'configs' => $this->configs,
			'parent' => $this->parent,
			'permissions' => $this->permissions,
			'userPermissions' => $this->userPermissions
		);
	}

	protected function deleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository($this->configs['entity'])->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Page entity.');
		}

		if(count($this->upload_fields) > 0){
			foreach ( $this->upload_fields as $key => $value ) {
				$file = call_user_func(array($entity, $value['get']));

				if(file_exists($value['path'] . '/' . $file)){
					@unlink($value['path'] . '/' . $file);
				}

				if(isset($this->crop_fields[$key])){
					foreach($this->crop_fields[$key] as $k => $v){
						if(file_exists($value['path'] . '/' . $k . '/' . $file)){
							@unlink($value['path'] . '/' . $k . '/' . $file);
						}
					}
				}
			}
		}

		$this->beforeDelete($entity);
		$em->remove($entity);
		$em->flush();

		$this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
		$this->get('session')->getFlashBag()->add('message', 'Registro removido com sucesso');

		$urlParams = array();
		if($this->parent){
			$urlParams['parent'] = $this->parent->getId();
		}

		return $this->redirect($this->generateUrl($this->configs['prefix_route'], $urlParams));
	}

	protected function deleteAllAction(Request $request)
	{
		$records = $request->request->get('records');

		foreach ($records as $record) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository($this->configs['entity'])->find($record);

			if(count($this->upload_fields) > 0) {
				foreach ( $this->upload_fields as $key => $value ) {
					$file = call_user_func( array( $entity, $value['get'] ) );

					if ( file_exists( $value['path'] . '/' . $file ) ) {
						@unlink( $value['path'] . '/' . $file );
					}

					foreach ( $this->crop_fields[ $key ] as $k => $v ) {
						if ( file_exists( $value['path'] . '/' . $k . '/' . $file ) ) {
							@unlink( $value['path'] . '/' . $k . '/' . $file );
						}
					}
				}
			}

			$this->beforeDelete($entity);
			$em->remove($entity);
			$em->flush();

		}

		$this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
		$this->get('session')->getFlashBag()->add('message', 'Todos os registros foram removido com sucesso');

		$urlParams = array();
		if($this->parent){
			$urlParams['parent'] = $this->parent->getId();
		}

		return $this->redirect($this->generateUrl($this->configs['prefix_route'], $urlParams));
	}

	public function getNewEntity(){
		return new $this->configs['entity_class'];
	}

	public function getNewEntityForm(){
		return new $this->configs['entity_form'];
	}


	protected function createCreateForm($entity)
	{
		$urlParams = array();
		if($this->parent){
			$urlParams['parent'] = $this->parent->getId();
		}

		$form = $this->createForm($this->getNewEntityForm(), $entity, array(
			'action' => $this->generateUrl($this->configs['prefix_route'] . '_create', $urlParams),
			'method' => 'POST',
		));

		$form->add('submit', 'submit', array('label' => 'Salvar', 'attr' => array('class' => 'btn btn-success')));

		return $form;
	}

	protected function createEditForm($entity)
	{
		$urlParams['id'] = $entity->getId();
		if($this->parent){
			$urlParams['parent'] = $this->parent->getId();
		}

		$form = $this->createForm($this->getNewEntityForm(), $entity, array(
			'action' => $this->generateUrl($this->configs['prefix_route'] . '_update', $urlParams),
			'method' => 'PUT',
		));

		$form->add('submit', 'submit', array('label' => 'Salvar', 'attr' => array('class' => 'btn btn-success')));

		return $form;
	}

	private function needCMSPermission($className, $route) {
		$json = $this->permissionService->getJson();
		if(array_key_exists('permissions', $json)) {
			foreach($json['permissions'] as $item) {
				if(!array_key_exists('route', $item) || $item['route'] != $route)
					continue;

				if(!array_key_exists('permission', $item) || $item['permission'] != 'free')
					break;

				return false;
			}
		}

		return true;
	}

	protected function beforePersist($entity, $form, $method) {}
	protected function afterPersist($entity, $form, $method) {}
	protected function beforeDelete($entity) {}

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

	protected function showResults(){
		$em = $this->getDoctrine()->getManager();
		return $em->getRepository($this->configs['entity'])->findAll();
	}

}
