<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel External Request
    |--------------------------------------------------------------------------
    |
    |
    */


    /*
     * default header to request
     * example:
     */
    'default_parameters_to_header' => [
        'cache-control' => 'no-cache',
        'Content-Type' => 'application/json',
    ],

    /*
     * get special values from header
     * example:
     */
    'get_special_values_from_header' => [
        'special' => 'value',
    ],


    /*
     * get special values from request
     * example:
     */
    'get_special_values_from_request' => [
        'ip' => 'ip',
    ],

    'proxy_ip' => 'http://your-domain:3128',
];
