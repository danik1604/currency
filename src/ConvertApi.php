<?php

namespace Currency;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;


class ConvertApi
{

    protected $apiUrl;
    protected $tryCount = 0;
    protected $error;
    protected $errorMessage;

    const MAX_TRY_COUNT = 10;

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function getError()
    {
        return $this->error;
    }
    
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function exchange($ccyFrom, $ccyTo, $amount)
    {
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
                if($data['success']){
                    return $data['data'];
                }

                $this->error = 1;
                $this->errorMessage = json_encode($data);
                return false;
            }
        } catch (ConnectException $e) {
            if($this->tryCount > self::MAX_TRY_COUNT){
                $this->error = 2;
                $this->errorMessage = "Reached maximal MAX_TRY_COUNT";
                return false;
            }

            $this->tryCount++;
            return $this->exchange($ccyFrom, $ccyTo, $amount);

        } catch (RequestException $e){
            $this->error = 3;
            $this->errorMessage = $e->getMessage();
            return false;
        } catch (Exception $e){
            $this->error = 4;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

    public function getRates(array $ccy = [])
    {
        $client = new Client(['base_uri' => $this->apiUrl, 'timeout'  => 2.0]);
    
        try {
            $res = $client->request('GET', '/api/get-euro-rates', [
                'form_params' => [
                    'ccy' => $ccy
                ]
            ]);

            if($res->getStatusCode() == '200'){
                $data = (array) @json_decode($res->getBody(), true);
                if($data['success']){
                    return $data['data'];
                }

                $this->error = 1;
                $this->errorMessage = json_encode($data);
                return false;
            }
        } catch (ConnectException $e) {
            if($this->tryCount > self::MAX_TRY_COUNT){
                $this->error = 2;
                $this->errorMessage = "Reached maximal MAX_TRY_COUNT";
                return false;
            }

            $this->tryCount++;
            return $this->getRates($ccy);

        } catch (RequestException $e){
            $this->error = 3;
            $this->errorMessage = $e->getMessage();
            return false;
        } catch (Exception $e){
            $this->error = 4;
            $this->errorMessage = $e->getMessage();
            return false;
        }

        return false;
    }

}