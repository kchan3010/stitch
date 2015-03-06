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
	//this can be stored in a conf file or have
    //different routes dictate which channels to use
	
	private static $channelsToUse = array("StitchLabs\DemoBundle\Channel\Vend", "StitchLabs\DemoBundle\Channel\Shopify");

	
    /**
     * Collection post action
     * @var Request $request
     * @return View|array
     */
    public function postAction(Request $request)
    {
    	$channelList = $this->getChannels();

    	if(is_array($channelList) && !empty($channelList)) {
    		$finalizedProducts = array();
    		foreach ($channelList as $channel) {
		    	$response = $channel->getAllProducts();
    			$normalizedProducts = $channel->normalizeProducts($response);

    			//With multiple channels having the same sku, we need to know which one was updated last
    			//so we can use that channel data

    			
    			if(empty($finalizedProducts)) {
    				//first channel sets $finalizedProducts - which is basically a lookup table now
    				$finalizedProducts = $normalizedProducts;
    			} else {
    				foreach($normalizedProducts as $key=>$product) {
    					if(isset($finalizedProducts[$key]) && !empty($finalizedProducts[$key])) {
    						//check the same product exists
    						//take the latest date    						
    						if($finalizedProducts[$key]['udate'] < $normalizedProducts[$key]['udate']) {
    							$finalizedProducts[$key] = $normalizedProducts[$key];
    						}
    					} else {
    						$finalizedProducts[$key] = $normalizedProducts[$key];
    					}
    				}
    			}
    		}
    	}


    	if(is_array($finalizedProducts) && !empty($finalizedProducts)) {
			$response = $this->syncProducts($finalizedProducts);
    	}

    	if($response) {
    		$em = $this->getDoctrine()->getManager();
    		$entities  = $em->getRepository('StitchLabsDemoBundle:Product')->findAll();
    	} else {
    		$entities = FALSE;
    	}

         return array(
            'entitiies' => $entities
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
	    
    }  

    private function getChannels()
    {
    	$channelList = array();    	
    	foreach(static::$channelsToUse as $channelName) {
    		$channelList[] = new $channelName();
    	}

    	return $channelList;
    }

    

}
