<?php

namespace Litermi\ExternalRequest\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\StreamWrapper;
use HttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use Litermi\Logs\Facades\LogConsoleFacade;
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
     * @param bool $proxy
     * @param bool $logActive
     * @param bool $hugeJson
     * @return mixed
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
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
        $proxy = false,
        $logActive = true,
        $hugeJson = true
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
                $headers[$key] = $item;
            }
        }
        foreach (config('external-request.get_special_values_from_header') as $key => $item) {
            if ($request->$item) {
                $headers[$key] = $request->header($item);
            }
        }
        foreach (config('external-request.get_special_values_from_request') as $key => $item) {
            if ($request->$item) {
                $formParams[$key] = $request->$item;
            }
        }

        $formAndHeader = [
            $modeParams => $formParams,
            'headers'   => $headers,
        ];

        if ($proxy === true && empty(config('external-request.proxy_ip')) === false) {
            $formAndHeader['proxy'] = config('external-request.proxy_ip');
        }

        $array = ['method' => $method, 'url' => $baseUri . "" . $requestPath, 'params' => $formAndHeader];
        if ($logActive == true) {
            LogConsoleFacade::full()->log('external-response-request-before', $array);
        }
        if ($async === true) {
            $formAndHeader['timeout']         = 0.4;
            $formAndHeader['connect_timeout'] = 0.4;

            try {
                return $client->request($method, $requestPath, $formAndHeader);
            } catch (Exception $exception) {
                return true;
            }
        }

        $content = "";
        $response = $client->request($method, $requestPath, $formAndHeader);
        if ($pureResponse === true) {
            return $response;
        }
        if($hugeJson == true){
            $phpStream = StreamWrapper::getResource($response->getBody());
            $array = Items::fromStream($phpStream);
            $newResponse = [];
            foreach ($array as $key => $value) {
                $newResponse[$key]=$value;
            }

            return $newResponse;
        }


        $content = $response->getBody()->getContents();
        if ($logActive == true) {
            $array['response']  = $content;
            LogConsoleFacade::full()->log('external-response-request-after', $array);
        }

        try {
            $jsonReturn = json_decode($content, true);
        } catch (Exception $exception) {
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
}
