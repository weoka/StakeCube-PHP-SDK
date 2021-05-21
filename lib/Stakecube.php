<?php

namespace Stakecube;

use GuzzleHttp\Client;
use Exception;
use DateTime;

class Stakecube{
    const API_BASE_URL = "https://stakecube.io/api/v2";

    public function __construct($public_key = "", $private_key = "")
    {
        if( empty($public_key) || empty($private_key) )
        {
            throw new Exception('public & private key must be a valid value');
        }

        $this->public_key = $public_key;
        $this->private_key = $private_key;
        $this->nonce = intval(microtime(true) * 1000);
        $this->client = new client();
    }

    public function signatureGenerator($data)
    {
        try{
            return hash_hmac('sha256', $data, $this->private_key);
        }
        catch(e)
        {
            throw new Exception(e);
        }
    }

    public function GETRequest($request = "", $parameters = "")
    {
        try{
            $signature = $this->signatureGenerator($parameters);
            $url = self::API_BASE_URL.$request.'?'.$parameters.'&signature='.$signature;
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'StakeCube PHP Library. Weoka sends his regards ;)',
                    'X-API-KEY'  => $this->public_key
                ]
            ]);
            //return response as an array
            return json_decode($response->getBody()->getContents(), true);
        }  
        catch(e)
        {
            throw new Exception(e);
        }  
    }

    public function POSTRequest($request = "", $parameters = [], $presignature = "")
    {
        try{
            $parameters['signature'] = $this->signatureGenerator($presignature);
            $url = self::API_BASE_URL.$request;
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'User-Agent' => 'StakeCube PHP Library. Weoka sends his regards ;)',
                    'X-API-KEY'  => $this->public_key
                ],
                'form_params' => $parameters
            ]);
            //return response as an array
            return json_decode($response->getBody()->getContents(), true);
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

    public function getMineCubeMiners($coin = "")
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

    public function getRatelimits()
    {
        $request = "/system/rateLimits";
        return $this->GETRequest($request);
    }

    public function getTrades($market = "", $limit = 100)
    {
        if( empty($market) )
        {
            throw new Exception('Empty market!');
        }

        if( !is_int($limit) )
        {
            throw new Exception('Invalid limit. It must be a number!');
        }

        $request = "/exchange/spot/trades";
        $parameters = "market=$market&limit=$limit&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function getOrderbook($market = "", $limit = 100)
    {
        if( empty($market) )
        {
            throw new Exception('Empty market!');
        }

        if( !is_int($limit) )
        {
            throw new Exception('Invalid limit. It must be a number!');
        }

        $request = "/exchange/spot/orderbook";
        $parameters = "market=$market&limit=$limit&nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function getAccount()
    {
        $request = "/user/account";
        $parameters = "nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function withdraw($ticker = "", $address = "", $amount = 0)
    {   
        if( empty($ticker) || empty($address) || empty($amount) )
        {
            throw new Exception('Missing parameters!');
        }

        $request = "/user/withdraw";

        $parameters = [
            "ticker" => $ticker,
            "address" => $address,
            "amount" => $amount,
            "nonce" => $this->nonce
        ];

        $presignature = "ticker=$ticker&address=$address&amount=$amount&nonce=$this->nonce";

        return $this->POSTRequest($request, $parameters, $presignature);
    }

    public function getOpenOrders()
    {
        $request = "/exchange/spot/myOpenOrder";
        $parameters = "nonce=$this->nonce";
        return $this->GETRequest($request, $parameters); 
    }

    public function getMyTrades($market = "", $limit = 100)
    {
        $request = "/exchange/spot/myTrades";
        $parameters = "nonce=$this->nonce&market=$market&limit=$limit";
        return $this->GETRequest($request, $parameters); 
    }

    public function getOrderHistory($market = "", $limit = 100)
    {
        $request = "/exchange/spot/myOrderHistory";
        $parameters = "nonce=$this->nonce&market=$market&limit=$limit";
        return $this->GETRequest($request, $parameters); 
    }

    public function postOrder($market = "", $side = "", $price = 0.00, $amount = 0.00)
    {   
        if( empty($market) || empty($side) || empty($price) || empty($amount) )
        {
            throw new Exception('Missing parameters!');
        }

        $side = strtoupper($side);

        $allowedsides = ["BUY", "SELL"];
        if ( !in_array($side, $allowedsides, 1) )
        {
            throw new Exception('Invalid side! Possible side: BUY or SELL.');
        }

        $request = "/exchange/spot/order";

        $parameters = [
            "market" => $market,
            "side" => $side,
            "price" => $price,
            "amount" => $amount,
            "nonce" => $this->nonce
        ];

        $presignature = "market=$market&side=$side&price=$price&amount=$amount&nonce=$this->nonce";

        return $this->POSTRequest($request, $parameters, $presignature);
    }
}