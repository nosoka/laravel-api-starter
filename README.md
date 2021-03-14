## Laravel Api Starter
Re-usable laravel dropin package for following api authentication methods.
- api/auth/register
- api/auth/verify.email
- api/auth/send.verification.email
- api/auth/login
- api/auth/forgot.password
- api/auth/reset.password
- compatible with laravel version 7.x/8.x

## Installation
- Add following to composer.json in your existing laravel project
    ```
    "repositories": [
        {
            "type": "path",
            "url": "./api"
        }
    ],
    ```
- Clone the repo into a subfolder in your existing laravel project
    ```
    $ git clone git@github.com:nosoka/laravel-api-starter.git api
    ```
- Install the package
    ```
    $ composer require nosoka/api
    ```

- Publish config files.
    ```
    $ php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
    $ php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

## Tests
- Run following to create/send report
    ```
    $ ./vendor/bin/codecept -c api build
    $ ./vendor/bin/codecept -c api run
    ```
