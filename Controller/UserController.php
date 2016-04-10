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
     * @Route("/", name="fly_user_create")
     * @Method("POST")
     * @Template("FlyBundle:CRUD:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new $this->configs['entity_class'];
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setPassword($this->encondePassword($entity,$form->getData()->getPassword()));
            $em = $this->getDoctrine()->getManager();

            if(!$this->getUser()->getProfile()->getAdministrator()) {
                $entity->setParent($this->getUser());
            }

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
            $this->get('session')->getFlashBag()->add('message', 'Novo registro adicionado com sucesso');

            return $this->redirect($this->generateUrl($this->configs['prefix_route'] . '_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'configs' => $this->configs
        );
    }

    protected function createCreateForm($entity)
    {
        $form = $this->createForm(new $this->configs['entity_form'], $entity, array(
            'action' => $this->generateUrl($this->configs['prefix_route'] . '_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Salvar', 'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="fly_user_new")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:new.html.twig")
     */
    public function newAction()
    {
        return parent::newAction();
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="fly_user_show")
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
     * @Route("/{id}/edit", name="fly_user_edit")
     * @Method("GET")
     * @Template("FlyBundle:CRUD:edit.html.twig")
     */
    public function editAction($id)
    {
        return parent::editAction($id);
    }


    protected function createEditForm($entity)
    {
        $form = $this->createForm(new $this->configs['entity_form'], $entity, array(
            'action' => $this->generateUrl($this->configs['prefix_route'] . '_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        if($entity->getId() == $this->getUser()->getId()){
            $form->remove('profile');
        }

        $form->add('submit', 'submit', array('label' => 'Salvar', 'attr' => array('class' => 'btn btn-success')));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="fly_user_update")
     * @Method("PUT")
     * @Template("FlyBundle:CRUD:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->configs['entity'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if(count($this->upload_fields) > 0) {
            foreach ( $this->upload_fields as $key => $value ) {
                $files[ $key ] = call_user_func( array( $entity, $value['get'] ) );
            }
        }

        $oldPass = $entity->getPassword();

        $form = $this->createEditForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if($form->getData()->getPassword() != ''){
                $entity->setPassword($this->encondePassword($entity,$form->getData()->getPassword()));
            }else{
                $entity->setPassword($oldPass);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add('title', $this->configs['singular_name']);
            $this->get('session')->getFlashBag()->add('message', 'Registro editado com sucesso');

            return $this->redirect($this->generateUrl($this->configs['prefix_route'] . '_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
            'configs' => $this->configs
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/delete/{id}", name="fly_user_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        //$form = $this->createDeleteForm($user);
        //$form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
            //$em = $this->getDoctrine()->getManager();
            //$em->remove($user);
            //$em->flush();
        //}

        return $this->redirectToRoute('user_index');
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/delete_all", name="fly_user_delete_all")
     * @Method("POST")
     */
    public function deleteAllAction(Request $request)
    {
        return $this->redirectToRoute('user_index');
    }

    private function encondePassword($user, $plainPassword){
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }
}
