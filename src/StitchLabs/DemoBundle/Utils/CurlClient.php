<?php

namespace StitchLabs\DemoBundle\Utils;

class CurlClient
{
    private $httpHeaders;

    public function __construct($headers=NULL) 
    {
        $this->httpHeaders = array('Content-Type: application/json');
        
        if($headers != NULL) {
            $this->httpHeaders = $headers;
        }
    }


	public function executeCurl($url, $params=NULL, $method=NULL, & $rc=NULL) {

        $this->curl = curl_init($url);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_TIMEOUT,        30);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS,      5);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->httpHeaders);
        if ($method == "POST") {
            curl_setopt($this->curl,CURLOPT_POST, 1);
        }

        $params = $params ? json_encode($params) : NULL;

        curl_setopt($this->curl,CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

        $response = @curl_exec($this->curl);


        return $response;
    }    	
}