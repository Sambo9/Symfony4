<?php

namespace CA\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();

      $posts = $em->getRepository('CABlogBundle:Post')->findAll();
  
      //$users = $em->getRepository('CABlogBundle:Users')->findAll();
        return $this->render('CABlogBundle:Blog:index.html.twig', array(
          'posts' => $posts
        ));
    }

}
