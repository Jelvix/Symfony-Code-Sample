<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 01.02.2019
 * Time: 0:20
 */

namespace App\ServiceFactory;


use App\Whow\Api\RetryHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GuzzleHttpClientFactory
{
    public static function createClient(ParameterBagInterface $params){
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $config = $params->get('app_services');
        $timeout = 30;
        $maxRetries = isset($config['retry_policy']) && isset($config['retry_policy']['number_of_retries']) ? $config['retry_policy']['number_of_retries'] : 5;
        $delay = isset($config['retry_policy']) && isset($config['retry_policy']['delay']) ? $config['retry_policy']['delay'] : 5;
        $retryHandler = new RetryHandler($maxRetries, $delay);
        $handlerStack = HandlerStack::create( new CurlHandler() );
        $handlerStack->push( Middleware::retry( $retryHandler->retryDecider(), $retryHandler->retryDelay() ) );
        return new Client([
            'timeout' => $timeout,
            'headers' => $defaultHeaders,
            'handler' => $handlerStack
        ]);
    }

}