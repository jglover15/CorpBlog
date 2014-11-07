<?php

namespace Corp\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CorpBlogBundle:Default:index.html.twig', array('name' => $name));
    }

    // simple return blog data by id
    public function entryAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        return $this->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry));
    }

    public function testAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        $response = new Response();
        $response->setContent($this->container->get('templating')->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry)));

        return $response;
    }
}
