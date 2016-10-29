<?php

namespace Lincode\Fly\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends BaseController
{
    protected $configs = [
        'prefix_route' => 'fly_user',
        'singular_name' => 'Usuário',
        'plural_name' => 'Usuários',
        'entity' => 'FlyBundle:User',
        'entity_class' => 'Lincode\Fly\Bundle\Entity\User',
        'entity_form' => 'Lincode\Fly\Bundle\Form\UserType',
        'title_field' => 'name',
        'list_fields' => ['name' => 'Nome', 'email' => 'Email', 'isActive' => ['type' => 'boolean', 'label' => 'Ativo?']],
        'show_fields' => ['name' => 'Nome', 'email' => 'Email', 'isActive' => ['type' => 'boolean', 'label' => 'Ativo?']]
    ];

    /**
     * Lists all User entities.
     *
     * @Route("/", name="fly_user")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:index.html.twig")
     */
    public function indexAction()
    {
        return parent::indexAction();
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="fly_user_new")
     * @Method({"GET", "POST"})
     * @Template("FlyBundle:CRUD:new.html.twig")
     */
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
                $entity->setPassword($this->encondePassword($entity, $form->getData()->getPassword()));
                $controllerService->save($entity);

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

    /**
     * Finds and displays a User entity.
     *
     * @Route("/show/{id}", name="fly_user_show")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:show.html.twig")
     */
    public function showAction($id)
    {
        return parent::showAction($id);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/edit/{id}", name="fly_user_edit")
     * @Method({"GET", "POST"})
     * @Template("FlyBundle:CRUD:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $controllerService = $this->get('fly.controller.service');
        $entity = $controllerService->find($this->configs['entity'], array('id' => $id));
        $oldPass = $entity->getPassword();

        $urlResponse = $this->generateUrl($this->configs['prefix_route'] . '_edit', array('id' => $id));

        $form = $controllerService->getForm($this->getNewEntityForm(), $entity, $urlResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formValidateService = $this->get('fly.form.service');
            if ($formValidateService->validadeForm($form, $this->configs['entity'], $entity)) {
                if ($form->getData()->getPassword() != '') {
                    $entity->setPassword($this->encondePassword($entity, $form->getData()->getPassword()));
                } else {
                    $entity->setPassword($oldPass);
                }

                $controllerService->save($entity);

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

    /**
     * Deletes a User entity.
     *
     * @Route("/delete/{id}", name="fly_user_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }

    /**
     * Deletes a Page entity.
     *
     * @Route("/deleteAll", name="fly_user_delete_all")
     * @Method("POST")
     */
    public function deleteAllAction(Request $request)
    {
        return parent::deleteAllAction($request);
    }

    private function encondePassword($user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }
}
