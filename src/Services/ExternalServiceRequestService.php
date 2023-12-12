<?php

namespace Litermi\ExternalRequest\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Litermi\SimpleNotification\Facades\SimpleNotificationFacade;

/**
 *
 */
class ExternalServiceRequestService
{
    /**
     * @param $baseUri
     * @param $method
     * @param string $requestPath
     * @param array $formParams
     * @param array $headers
     * @param string $modeParams
     * @param bool $async
     * @param bool $pureResponse
     * @return mixed
     * @throws GuzzleException
     */
    public static function execute(
        $baseUri,
        $method,
        $requestPath = '',
        $formParams = [],
        $headers = [],
        $modeParams = 'form_params',
        $async = false,
        $pureResponse = false,
        $proxy=false
    ) {
        $client = new Client(
            [
                'base_uri' => $baseUri,
                'curl'     => [
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]
        );

        /** @var Request $request */
        $request = request();
        foreach (config('external-request.default_parameters_to_header') as $key => $item) {
            if ($request->$item) {
                $headers[ $key ] = $item;
            }
        }
        foreach (config('external-request.get_special_values_from_header') as $key => $item) {
            if ($request->$item) {
                $headers[ $key ] = $request->header($item);
            }
        }
        foreach (config('external-request.get_special_values_from_request') as $key => $item) {
            if ($request->$item) {
                $formParams[ $key ] = $request->$item;
            }
        }

        $formAndHeader = [
            $modeParams => $formParams,
            'headers'   => $headers,
        ];

        if ($proxy === true && empty(config('external-request.proxy_ip')) === false) {
            $formAndHeader['proxy'] = config('external-request.proxy_ip');
        }

        if($async === true){
            $formAndHeader[ 'timeout' ] = 0.4;
            $formAndHeader[ 'connect_timeout' ] = 0.4;

            try {
                return $client->request($method, $requestPath, $formAndHeader);
            }catch (Exception $exception){
                return true;
            }
        }

        $response = $client->request($method, $requestPath, $formAndHeader);

        if($pureResponse === true){
            return $response;
        }

        $content = "";
        try {
            $content = $response->getBody()->getContents();
            $jsonReturn = json_decode($content, true);
        }catch (Exception $exception){
            SimpleNotificationFacade::email()
                ->slack()
                ->email()
                ->error()
                ->notification(
                                 translateInterTerm('ERROR_REQUEST_JSON_DECODE'),
                    extraValues: ['content' => $content]
                );

            throw new HttpException(
                translateInterTerm('ERROR_REQUEST_JSON_DECODE'),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                null,
            );
        }

        if (empty($jsonReturn) && is_string($content) && empty($content) === false) {
            $jsonReturn = $content;
        }

        return $jsonReturn;

    }

    private static function getInfo($file_paths)
    {
        $stringToReturn = "";
        foreach ($file_paths as $file_path) {
            foreach ($file_path as $key => $var) {
                if ($key == 'args') {
                    foreach ($var as $key_arg => $var_arg) {
                        if (is_object($var_arg) === false && is_string($key_arg) && is_string($var_arg)) {
                            $stringToReturn .= $key_arg . ': ' . $var_arg . '<br>';
                        }
                    }
                } else {
                    if (is_object($var) === false && is_string($key) && is_string($var)) {
                        $stringToReturn .= $key . ': ' . $var . '<br>';
                    }
                }
            }
        }
        return $stringToReturn;
    }
}
