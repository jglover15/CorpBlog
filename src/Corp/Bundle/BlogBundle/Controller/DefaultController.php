<?php

namespace Corp\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Corp\Bundle\BlogBundle\Entity\BlogEntry;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CorpBlogBundle:Default:index.html.twig', array('name' => $name));
    }

    // simple return blog data by id
    public function defaultAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        return $this->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry));
    }

    public function manualAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        $response = new Response();
        $response->setContent($this->container->get('templating')->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry)));

        return $response;
    }

    public function privateAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');

        $response->setContent($this->container->get('templating')->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry)));

        return $response;
    }

    public function noAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        $response = new Response();
        $response->headers->set('Cache-Control', 'no-cache');

        $response->setContent($this->container->get('templating')->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry)));

        return $response;
    }

    public function noStoreAction($id)
    {
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        $response = new Response();
        $response->headers->set('Cache-Control', 'no-store');

        $response->setContent($this->container->get('templating')->render('CorpBlogBundle:Default:entry.html.twig', array('entry' => $entry)));

        return $response;
    }

    public function tagAction($id, Request $request)
    {
        // Get the minimum information to compute
        // the ETag or the Last-Modified value
        // (based on the Request, data is retrieved from
        // a database or a key-value store for instance)
        // would need to make changes here for efficiency with larger objects...
        $entry = $this->getDoctrine()->getManagerForClass('CorpBlogBundle:BlogEntry')
            ->getRepository('CorpBlogBundle:BlogEntry')
            ->find($id);

        // create a Response with an ETag and/or a Last-Modified header
        $response = new Response();
        $response->setETag($entry->getETag());
        $response->setLastModified($entry->getUpdateDate());

        $response->setMaxAge(300);

        // Set response as public. Otherwise it will be private by default.
        $response->setPublic();

        // Check that the Response is not modified for the given Request
        if ($response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }

        // do more work here - like retrieving more data
        // $comments = ...;

        // or render a template with the $response you've already started

        return $this->render(
            'CorpBlogBundle:Default:entry.html.twig',
            array('entry' => $entry),
            $response
        );
    }

    /**
     * Blog entry page - test cache directive
     *
     * This method should behave much if not exactly like tagAction, just handle via symfony via annotation
     * Draw backs - entity must be hydrated, really only saves us rendering time
     * cannot step thru the code to see what is happening
     * Could add maxage - doesn't seem to work? private must-revalidate is being served??
     *
     * @Cache(
     *      public = true,
     *      maxage = "300",
     *      lastModified = "entry.getUpdateDate()",
     *      ETag = "entry.getETag()"
     * )
     * @Template
     * @ParamConverter("entry", class="CorpBlogBundle:BlogEntry")
     */
    public function tagAnnotationAction(BlogEntry $entry)
    {
        $test = 1;
        return $this->render(
            'CorpBlogBundle:Default:entry.html.twig',
            array('entry' => $entry)
        );
    }

}
