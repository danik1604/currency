<?php

namespace Currency;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;

class ConvertApi
{

    private $apiUrl;

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function exchange($ccyFrom, $ccyTo, $amount)
    {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());
        $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0, 'handler' => $stack]);
        try {
            $res = $client->request('POST', '/api/convert', [
                'form_params' => [
                    'ccyFrom' => $ccyFrom,
                    'ccyTo' => $ccyTo,
                    'amount' => $amount
                ]
            ]);

            if($res->getStatusCode() == '200'){
                $data = (array) @json_decode($res->getBody(), true);
                return $data;
            }
        } catch (RequestException $e) {
            return false;
        }

        return false;
    }

    public function getRates(array $ccy = [])
    {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());
        $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0, 'handler' => $stack]);
        
        try {
            $res = $client->request('POST', '/api/get-rates', [
                'form_params' => [
                    'ccy' => $ccy
                ]
            ]);

            if($res->getStatusCode() == '200'){
                $data = (array) @json_decode($res->getBody(), true);
                return $data;
            }
        } catch (RequestException $e) {
            return false;
        }

        return false;
    }

}