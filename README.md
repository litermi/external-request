# External Request

[![Software License][ico-license]](LICENSE.md)

## About

The `External Request` package to send request to others api-rest .


## Installation

Require the `cirelramos/external-request` package in your `composer.json` and update your dependencies:
```sh
composer require cirelramos/external-request
```


## Configuration

set provider

```php
'providers' => [
    // ...
    Cirelramos\ExternalRequest\Providers\ServiceProvider::class,
],
```


The defaults are set in `config/external-request.php`. Publish the config to copy the file to your own config:
```sh
php artisan vendor:publish --provider="Cirelramos\ExternalRequest\Providers\ServiceProvider"
```

> **Note:** this is necessary to you can change default config



## Usage

```php
use Cirelramos\ExternalRequest\ExternalServiceRequestService;

        $response = ExternalServiceRequestService::execute(
            $baseUri,
            'GET',
            $requestUrl,
            [],
            $header
        );
```


## License

Released under the MIT License, see [LICENSE](LICENSE).


[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

