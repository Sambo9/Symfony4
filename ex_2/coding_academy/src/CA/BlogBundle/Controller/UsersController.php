<?php

namespace CA\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CA\BlogBundle\Entity\Users;
use CA\BlogBundle\Form\UsersType;

/**
 * Users controller.
 *
 */
class UsersController extends Controller
{
    public function profileAction(Users $users)
    {
      $deleteForm = $this->createDeleteForm($users);

      return $this->render('users/profile.html.twig', array(
          'users' => $users,
          'delete_form' => $deleteForm->createView(),
          'posts' => $users->getPosts()
      ));
    }
    /**
     * Lists all Users entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('CABlogBundle:Users')->findAll();

        return $this->render('users/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new Users entity.
     *
     */
    public function newAction(Request $request)
    {
        $users = new Users();
        $form = $this->createForm('CA\BlogBundle\Form\UsersType', $users);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

          $plainPassword = $users->getPassword();
          $encoder = $this->container->get('security.password_encoder');
          $encoded = $encoder->encodePassword($users, $plainPassword);
          $users->setPassword($encoded);
          $users->setRoles(array('ROLE_BLOGGER'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($users);
            $em->flush();

            return $this->redirectToRoute('users_show', array('id' => $users->getId()));
        }

        return $this->render('users/new.html.twig', array(
            'users' => $users,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Users entity.
     *
     */
    public function showAction(Users $users)
    {
        $deleteForm = $this->createDeleteForm($users);

        return $this->render('users/show.html.twig', array(
            'users' => $users,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Users entity.
     *
     */
    public function editAction(Request $request, Users $users)
    {
      $this->denyAccessUnlessGranted('edit', $users);

        $deleteForm = $this->createDeleteForm($users);
        $editForm = $this->createForm('CA\BlogBundle\Form\UsersType', $users);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

          $plainPassword = $users->getPassword();
          $encoder = $this->container->get('security.password_encoder');
          $encoded = $encoder->encodePassword($users, $plainPassword);
          $users->setPassword($encoded);
            $em = $this->getDoctrine()->getManager();
            $em->persist($users);
            $em->flush();

            return $this->redirectToRoute('users_edit', array('id' => $users->getId()));
        }

        return $this->render('users/edit.html.twig', array(
            'users' => $users,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Users entity.
     *
     */
    public function deleteAction(Request $request, Users $users)
    {
        $form = $this->createDeleteForm($users);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($users);
            $em->flush();
        }

        return $this->redirectToRoute('users_index');
    }

    /**
     * Creates a form to delete a Users entity.
     *
     * @param Users $users The Users entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Users $users)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('users_delete', array('id' => $users->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
