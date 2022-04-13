# External Request

[![Software License][ico-license]](LICENSE.md)

## About

The `External Request` is a package to send request to others api-rest .

##### [Tutorial how create composer package](https://cirelramos.blogspot.com/2022/04/how-create-composer-package.html)

## Installation

Require the `litermi/external-request` package in your `composer.json` and update your dependencies:
```sh
composer require litermi/external-request
```


## Configuration

set provider

```php
'providers' => [
    // ...
    Litermi\ExternalRequest\Providers\ServiceProvider::class,
],
```


The defaults are set in `config/external-request.php`. Publish the config to copy the file to your own config:
```sh
php artisan vendor:publish --provider="Litermi\ExternalRequest\Providers\ServiceProvider"
```

> **Note:** this is necessary to you can change default config



## Usage

```php

use Litermi\ExternalRequest\ExternalServiceRequestService;

$baseUri = "yourdomain.com"
$requestPath = "/api/users"
$formParams = [];
$headers = [];

$method = "GET";
$response = ExternalServiceRequestService::execute(
    $baseUri,
    $method,
    $requestPath,
    $formParams,
    $header
);


$method = "POST";
$formParams = ["username":"cirel", "password":"you"];
$responsePost =  ExternalServiceRequestService::execute(
    $baseUri,
    $method,
    $requestPath,
    $formParams,
    $headers
);
```


## License

Released under the MIT License, see [LICENSE](LICENSE).


[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

