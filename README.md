o6c.ca
======

Only 6 characters. URL Shortener based on Slim 4 (PSR-7 and PSR-15).

```shell
composer create-project locomotivemtl/o6c-api
```

# API

> Keep it simple.


- `GET /{code}`
    - Redirect to the original URL.
- `POST /api/v1/login`
    - Retrieve an auth token (jwt) from credentials.
- `POST /api/v1/shorten`
    - Create a new short-link. Must provide a valid access token.
    
> See [locomotivemtl/o6c-client](https://github.com/locomotivemtl/o6c-client) for a simple client library to interact with this API.

# Dependencies

- PHP 7.2+
- Slim 4
- pimple/pimple
- lcobucci/jwt


# How to install

- `composer install`
- Setup a MySQL/MariaDB database. 
    - Import the `data/schema.sql`.
- Copy the `config/config.sample.php` to `config/config.php` and edit its value to your environment.
- Generate a RSA key for the JWT token `cd config;sh jwt.sh`.
- Set up your web server (with a short URL!) to point to `public/`. 
    - In dev, try `composer start`.
    
# Adding new users

> Work in prgress.

Add them manually in the `users` table. The password format is

```php
$password = password_hash($plain, PASSWORD_DEFAULT);
```