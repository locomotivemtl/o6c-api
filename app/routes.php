<?php

declare(strict_types=1);

use Only6\Actions\{
    Retrieve,
    Generate,
    Login
};
use Only6\Middleware\{
    Auth,
    Domain
};
use Only6\Templates\Home;

use Middlewares\ClientIp;

return function (\Slim\App $app): void {

    // Ensure the domain is valid, and save it as request attribute.
    $app->add(new Domain($app->getContainer()->get('config')['domains']));

    // Public website.
    $app->get('/', Home::class);

    // Retrieve a short link.
    $app->get('/{code:[a-z0-9\-\_]{5}}', Retrieve::class)
        ->add(new ClientIp());

    // Authenticate.
    $app->post('/api/v1/login', Login::class);

    // Create a new short link. Ensure valid user with Auth middleware.
    $app->post('/api/v1/shorten', Generate::class)
        ->add(Auth::class);

};