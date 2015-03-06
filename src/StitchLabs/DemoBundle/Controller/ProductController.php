<?php

namespace StitchLabs\DemoBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use StitchLabs\DemoBundle\Channel\Shopify;
use StitchLabs\DemoBundle\Entity\Product;

class ProductController extends FOSRestController implements ClassResourceInterface
{

/**
     * Collection get action
     * @var Request $request
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StitchLabsDemoBundle:Product')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Get action
     * @var integer $id Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($id)
    {
        //needs some validation run on the $id
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StitchLabsDemoBundle:Product')->findOneById($id);


        return array(
            'entity' => $entity,
        );
    }
}
