<?php

namespace Cirelramos\ExternalRequest\Services;

use GuzzleHttp\Client;

/**
 *
 */
class ExternalServiceRequestService
{
    public static function execute(
        $baseUri,
        $method,
        $requestUrl = '',
        $formParams = [],
        $headers = [],
        $modeParams = 'form_params',
    ) {
        $client = new Client(
            [
                'base_uri' => $baseUri,
                'curl'     => [
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]
        );
        
        $headers[ 'cache-control' ] = 'no-cache';
        $headers[ 'Content-Type' ]  = 'application/json';
        
        $formAndHeader = [
            $modeParams => $formParams,
            'headers'   => $headers,
        ];
        
        $response = $client->request($method, $requestUrl, $formAndHeader);
        
        $content = $response->getBody()
            ->getContents();
        
        return json_decode($content, true);
        
    }
}