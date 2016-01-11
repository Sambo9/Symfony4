<?php

namespace CA\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CA\BlogBundle\Entity\Post;
use CA\BlogBundle\Entity\Category;
use CA\BlogBundle\Form\PostType;


/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('CABlogBundle:Post')->findAll();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new Post entity.
     *
     */
    public function newAction(Request $request)
    {
        $post = new Post();

        $form = $this->createForm('CA\BlogBundle\Form\PostType', $post);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('CABlogBundle:Category')->findAll();

        $post->setCreated(new \DateTime());
        $post->setUpdated(null);
        // add user
        $post->setUser($this->get('security.token_storage')->getToken()->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
          // add category
          // TODO setCategories to post
        /*  if(!empty($_POST['category'])) {
                foreach($_POST['category'] as $check) {
                        $_category = new Category();
                        $_category->setName($check);
                        $post->addCategory($_category);
                }
            }
            */
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'categories' => $categories,
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
      {
        $this->denyAccessUnlessGranted('edit', $post);
      }

      $em = $this->getDoctrine()->getManager();
      $categories = $em->getRepository('CABlogBundle:Category')->findAll();

        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('CA\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);
        $post->setUpdated(new \DateTime());

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          if(!empty($_POST['category'])) {
                foreach($_POST['category'] as $check) {
                        var_dump($check);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            //return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'categories' => $categories,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
      {
          $this->denyAccessUnlessGranted('edit', $post);
      }

        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
