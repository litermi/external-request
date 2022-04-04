<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel logs
    |--------------------------------------------------------------------------
    |
    |
    */

    /*
     * exclude query by words
     * example:
     *
    'exclude_log_query_by_words' => [
        'token',
        'password',
    ],
     */
    'exclude_log_query_by_words' => [
    ],

    /*
     * if you want logs of sql query
     *
     */
    'query_log_is_active' => true,


    /*
     * exclude logs by path of endpoints
     * example:
     */
    'exclude_log_by_endpoint' => [
        '/api/documentation',
        '/docs/asset',
        '/docs/api-docs.json',
    ],

    /*
     * parameters to exclude of the header
     * example:
     */
    'exclude_parameters_of_header' => [
        'password'
    ],


    /*
     * parameters to exclude of the request
     * example:
     */
    'exclude_parameters_of_request' => [
        'password'
    ],


    /*
     * get special values from request
     * example:
     */
    'get_special_values_from_request' => [
        'ip' => 'ip',
    ],
];
