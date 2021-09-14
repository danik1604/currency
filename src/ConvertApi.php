<?php

namespace Currency;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;

class ConvertApi
{

    private $apiUrl;

    const MAX_TRY_COUNT = 10;
    private $tryCount = 0;

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function exchange($ccyFrom, $ccyTo, $amount)
    {
        // $stack = HandlerStack::create();
        // $stack->push(GuzzleRetryMiddleware::factory());
        // $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0, 'handler' => $stack]);
        $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0]);
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
        } catch (ConnectException $e) {
            if($tryCount > self::MAX_TRY_COUNT){
                return false;
            }

            $this->tryCount++;
            return $this->exchange($ccyFrom, $ccyTo, $amount);

        } catch (RequestException $e){
            return false;
        } catch (Exception $e){
            return false;
        }

        return false;
    }

    public function getRates(array $ccy = [])
    {
        // $stack = HandlerStack::create();
        // $stack->push(GuzzleRetryMiddleware::factory());
        // $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0, 'handler' => $stack]);
        $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0]);
    
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
        } catch (ConnectException $e) {
            if($tryCount > self::MAX_TRY_COUNT){
                return false;
            }

            $this->tryCount++;
            return $this->getRates($ccy);

        } catch (RequestException $e){
            return false;
        } catch (Exception $e){
            return false;
        }

        return false;
    }

}