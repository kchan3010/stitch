<?php

namespace StitchLabs\DemoBundle\Channel;

use StitchLabs\DemoBundle\Interfaces\ChannelInterfaces;
use StitchLabs\DemoBundle\Utils\CurlClient;

class Vend implements ChannelInterfaces 
{
	
	private $curlClient;
	

	public function __construct()
	{
		//this should be in a conf file
		$accessToken = 'MhswwJeDPzCOm8xiQPKnkQQMxOsPSjDTB4OVyTpz';
		$httpHeaders = array('Content-Type: application/json', 'Authorization: Bearer' . $accessToken);
		$this->curlClient = new CurlClient($httpHeaders);
	} 

	public function getBaseURI()
	{
		//this should be in a conf file
		return 'https://tinkerhub.vendhq.com/api/';
	}

	public function getProductsURI()
	{
		return $this->getBaseURI() . "products?active=1";
	}

	public function getAllProducts()
	{
		$products =  $this->curlClient->executeCurl($this->getProductsURI(), NULL, 'GET');

		return json_decode($products, TRUE);
	}

	/*
		This will help us determine what skus are needed to be updated in the DB
		Expects the $result to be an array (jseon_decoded)
	*/
	public function getAllSkusFromResponse($result)
	{
		$skuList = array();
		foreach($result['products'] as $product) {
			$skuList[] = $product['sku'];
		}

		return $skuList;
	}

	/**
	* @param array $result
	*
	*/
	public function normalizeProducts($result)
	{
		$prodList = array();

		if(is_array($result) && !empty($result)) {
			foreach($result['products'] as $product) {
				$prodList[$product['sku']]['name'] 		= $product['name'];
				$prodList[$product['sku']]['sku'] 		= $product['sku'];
				$prodList[$product['sku']]['quantity'] 	= (isset($product['inventory'][0]['count'])) ? $product['inventory'][0]['count'] : "0.00000";
				$prodList[$product['sku']]['price'] 	= $product['price'] . ".00";
				$prodList[$product['sku']]['udate'] 	= strtotime($product['updated_at']);
			}
		}

		return $prodList;



	}


}

