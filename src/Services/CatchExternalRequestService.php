<?php

namespace Litermi\ExternalRequest\Services;

use Litermi\Logs\Services\SendLogConsoleService;
use Exception;

/**
 *
 */
class CatchExternalRequestService
{
    /**
     * @param $request
     * @param $exception
     * @return array|null
     */
    public static function execute($request, $exception): ?array
    {
        try {
            $message        = __('error external service');
            $responseBody   = $exception->getResponse()->getBody();
            $code           = $exception->getCode();
            $data[ 'code' ] = $code;
            $host           = $exception->getRequest()->getUri()->getHost();
            $error          = json_decode($responseBody->getContents());
            $error          = $error === null || is_bool($error) ? (object) [] : $error;
            if (( $host !== null ) && ( is_string($host) === true )) {
                $error->host = $host;
            }
            $message                  = property_exists($error, 'message') ? $error->message : '';
            $data[ 'message' ]        = $message;
            $data[ 'error_external' ] = $error;

            $data[ 'response_body' ] = $responseBody;
            $sendLogConsoleService = new SendLogConsoleService();
            $sendLogConsoleService->execute('critical-errors:' . $message, $data);
            if (env('APP_DEBUG') === false) {
                $data = [];
                $data[ 'code' ] = $code;
                $data[ 'message' ]       = __('error external service');
            }

            return $data;
        }
        catch(Exception $exception) {
            $code                    = $exception->getCode();
            $data[ 'message' ]       = __('error external service');
            $data[ 'code' ]          = $code;
            $data[ 'error_explain' ] = $exception->getMessage();
            $data[ 'file' ]          = $exception->getFile();
            $data[ 'line' ]          = $exception->getLine();

            $sendLogConsoleService = new SendLogConsoleService();
            $sendLogConsoleService->execute('critical-errors:' . $exception->getMessage(), $data);
            if (env('APP_DEBUG') === false) {
                $data = [];
                $data[ 'code' ] = $code;
                $data[ 'message' ]       = __('error external service');
            }

            return $data;

        }
    }
}
