<?php

namespace StitchLabs\DemoBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use StitchLabs\DemoBundle\Channel\Shopify;
use StitchLabs\DemoBundle\Entity\Product;

class SyncController extends FOSRestController implements ClassResourceInterface
{
	/**
     * Get action
     * @var integer $id Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($id)
    {
        

        return array(
            'entity' => $id,
        );
    }

    /**
     * Collection post action
     * @var Request $request
     * @return View|array
     */
    public function postAction(Request $request)
    {
        // $entity = new Organisation();
        // $form = $this->createForm(new OrganisationType(), $entity);
        // $form->bind($request);

        // if ($form->isValid()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($entity);
        //     $em->flush();

        //     return $this->redirectView(
        //         $this->generateUrl(
        //             'get_organisation',
        //             array('id' => $entity->getId())
        //         ),
        //         Codes::HTTP_CREATED
        //     );
        // }

    	    	
    	$channel = new Shopify();

    	$response = $channel->getAllProducts();
    	$normalizedProducts = $channel->normalizeProducts($response);

    	if(is_array($normalizedProducts) && !empty($normalizedProducts)) {
			$response = $this->syncProducts($normalizedProducts);
    	}

    	 
    	
    	// $em = $this->getDoctrine()->getManager();
    	// $entity = $em->getRepository('StitchLabsDemoBundle:Product')->findBySku('blah');

         return array(
            'form' => $response
        );
    }

    private function syncProducts($prodList)
    {
    	$em = $this->getDoctrine()->getManager();
    	foreach($prodList as $productData) {    		
    		 $entity = $em->getRepository('StitchLabsDemoBundle:Product')->findOneBySku($productData['sku']);    		 

    		if($entity) {
    			try{
    				//NEED TO HAVE MORE ROBUST ERROR CHECKING
					$this->updateProduct($entity, $productData);    			
    			} catch (Exception $e) {
    				return FALSE;
    			}
    			
    		} else {
    			try{
    				//NEED TO HAVE MORE ROBUST ERROR CHECKING
					$this->createProduct($productData);
    			} catch (Exception $e) {
    				return FALSE;
    			}

    			$this->createProduct($productData);
    		}
    	}

    	$em->flush();

    	return TRUE;
    }

    private function updateProduct($existingProdObj, $prodData)
    {
        
   	    $existingProdObj->setName($prodData['name']);
	    $existingProdObj->setSku($prodData['sku']);
	    $existingProdObj->setQuantity($prodData['quantity']);
	    $existingProdObj->setPrice($prodData['price']);

        $em = $this->getDoctrine()->getManager();
	    $em->persist($existingProdObj);
        // $em->flush();    	
    }

    private function createProduct($prodData)
    {
		$product = new Product();
	    $product->setName($prodData['name']);
	    $product->setSku($prodData['sku']);
	    $product->setQuantity($prodData['quantity']);
	    $product->setPrice($prodData['price']);
	    
	    $em = $this->getDoctrine()->getManager();

	    $em->persist($product);
	    // $em->flush();    	
    }  

    

}
