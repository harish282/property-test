<?php

namespace Lib;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PropertyApi{

    public $key;
    
    protected $http;
    protected $debug;
    protected $error;

    protected $throttle = 30;

    public function  __construct($key = null){

        $this->key = $key ?? env('API_KEY');

        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->http = new \GuzzleHttp\Client([
            'base_uri' => 'https://trial.craig.mtcserver15.com/',
            'headers' => ['Content-Type' => 'application/json'],
            'connect_timeout' => 1,
            'handler' => $handlerStack,
        ]);
    }

    protected function retryDecider()
    {
        return function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null,
            TransferException $exception = null
        ) {
            return $retries < 5 && ($exception instanceof ConnectException || $response && $response->getStatusCode() >= 500);
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s
     *
     * @return Closure
     */
    protected function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    private function parseResponse($res) {
        if ($res->getStatusCode() == 200) {
            $data = $res->getBody();
            $data = json_decode($data, true);
            //print_r($data);
            return $data;
        } else {
            print_r($res);
        }
    }

    public function getError() {
        return $this->error;
    }
    
    public function setError($e) {
        if ($e->getResponse()->getStatusCode() == 500) {
            $this->error = '500 Internal Server Error';
        }else{
            return $this->error =  json_decode($e->getResponse()->getBody()->getContents());
        }
        
    }

    public function getProperties(Array $options=[]){
        try {
            $properties = [];
            $options['page']['size'] = 100;
            $options['key'] = $this->key;
            //dd($options);
            $res = $this->http->get('api/properties', ['query' => $options]);
            $records  = $this->parseResponse($res);
            $properties = $records['data'];

            while(isset($records['next_page_url']) && !empty($records['next_page_url'])){
                $res = $this->http->get($records['next_page_url']);
                $records = $this->parseResponse($res);
                
                $properties = array_merge($properties, $records['data']);
                if($this->throttle > 0) usleep(1000/$this->throttle);
            }
                    
            return $properties;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->setError($e);
            return false;
        }
        
    }
}