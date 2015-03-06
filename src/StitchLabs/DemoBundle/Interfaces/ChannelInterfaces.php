<?php

namespace StitchLabs\DemoBundle\Interfaces;

interface ChannelInterfaces {
	
	public function getBaseURI();

	public function getProductsURI();

	public function getAllProducts();

	public function getAllSkusFromResponse($response);
}