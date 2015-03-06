<?php

namespace StitchLabs\DemoBundle\Channel;

use StitchLabs\DemoBundle\Interfaces\ChannelInterfaces;
use StitchLabs\DemoBundle\Utils\CurlClient;

class Shopify implements ChannelInterfaces 
{
	
	private $curlClient;
	

	public function __construct()
	{
		$this->curlClient = new CurlClient();
	} 

	public function getBaseURI()
	{
		//this should be in a conf file
		return 'https://9c33b855687f75c47e3af63d34d287df:6f3850398fc69b49f9451f30545c5b7f@tinkerhub.myshopify.com/admin/';
	}

	public function getProductsURI()
	{
		return $this->getBaseURI() . "products.json";
	}

	public function getAllProducts()
	{
		$products =  $this->curlClient->executeCurl($this->getProductsURI(), NULL, 'GET');

		return json_decode($products, TRUE);
	}

	/*
		This will help us determine what skus are needed to be updated in the DB
	*/
	public function getAllSkusFromResponse($response)
	{

		$skuList = array();

		$result = json_decode($response, TRUE);

		foreach($result['products'] as $product) {
			if(isset($product['variants']) && is_array($product['variants'])) {
				foreach($product['variants'] as $variant) {
					$skuList[] = $variant['sku'];
				}
			}
		}


		return $skuList;
	}

	public function normalizeProducts($result)
	{
		$prodList = array();
		$index = 0;

		if(is_array($result) && !empty($result)) {
			foreach($result['products'] as $product) {
				if(isset($product['variants']) && is_array($product['variants'])) {
					foreach($product['variants'] as $variant) {
						$prodList[$index]['name'] 		= $product['title'];
						$prodList[$index]['sku'] 		= $variant['sku'];
						$prodList[$index]['quantity'] 	= $variant['inventory_quantity'];
						$prodList[$index]['price'] 		= $variant['price'];

						$index++;
					}
				}
			}
		}

		return $prodList;



	}


}
