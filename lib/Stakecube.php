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

    public function GETRequest($request = "", $parameters = "")
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

    public function getArbitrageInfo($ticker = "")
    {
        if( empty($ticker) )
        {
            throw new Exception('Ticker required!');
        }

        $request = "/exchange/spot/arbitrageInfo";
        $parameters = "ticker=$ticker&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters);      
    }

    public function getMarkets($base = "", $orderBy = "")
    {
        $orderBy = strtolower($orderBy);

        if( empty($orderBy) && $orderBy != 'volume' && $orderBy != 'change')
        {
            throw new Exception('Orderby invalid!');
        }

        if( empty($base) )
        {
            throw new Exception('Base empty!');
        }

        $request = "/exchange/spot/markets";
        $parameters = "base=$base&orderBy=$orderBy&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function getOhlcData($market = "", $interval = "")
    {
        if( empty($market) )
        {
            throw new Exception('Empty market!');
        }

        $options = ["1m", "5m", "15m", "30m", "1h", "4h", "1d", "1w", "1mo"];

        if ( !in_array($interval, $options, 1) || empty($interval) )
        {
            throw new Exception('Invalid interval! Review the documentation for the interval options!');
        }

        $request = "/exchange/spot/ohlcData";
        $parameters = "market=$market&interval=$interval&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function getMineCubeInfo()
    {
        $request = "/minecube/info";
        return $this->GETRequest($request);
    }

    public function getMineCubeMiners($coin)
    {
        $allowedcoins = ["BTC", "DASH", "ETH", "LTC"];
        if ( !in_array($coin, $allowedcoins, 1) || empty($coin) )
        {
            throw new Exception('Invalid coin! Possible coins: BTC, DASH, ETH & LTC.');
        }

        $request = "/minecube/miner";
        $parameters = "coin=$coin&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

}