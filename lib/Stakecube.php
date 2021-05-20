<?php

namespace Stakecube;

use GuzzleHttp\Client;
use Exception;

class Stakecube{
    const API_BASE_URL = "https://stakecube.io/api/v2";

    public function __construct($public_key = "", $private_key = "")
    {
        if( empty($public_key) || empty($private_key) )
        {
            throw new Exception('public & private key must be a valid value');
        }

        $this->public_key = $public_key;
        $this->private_hey = $private_key;
        $this->nonce = time();
        $this->client = new client();
    }

    public function signatureGenerator($data)
    {
        try{
            return hash_hmac('sha256', $data, $this->public_key);
        }
        catch(e)
        {
            throw new Exception(e);
        }
    }

    public function GETRequest($request, $parameters)
    {
        try{
            $signature = $this->signatureGenerator($request);
            $url = self::API_BASE_URL.$request.'?'.$parameters.'&'.$signature;
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'StakeCube PHP Library. Weoka sends his regards ;)',
                    'X-API-KEY'  => $this->public_key
                ]
            ]);
            //return response as an array
            return json_decode($response->getBody());
        }  
        catch(e)
        {
            throw new Exception(e);
        }  
    }

    public function getArbitrageInfo($ticker)
    {
        if( empty($ticker) )
        {
            throw new Exception('Ticker required!');
        }

        $request = "/exchange/spot/arbitrageInfo";
        $parameters = "ticker=$ticker&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters);      
    }
}