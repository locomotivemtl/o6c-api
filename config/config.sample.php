<?php

declare(strict_types=1);

return function (): array {
    $config = [];

    $config['database'] = [
        'type' => 'mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => null,
        'port' => 3306,
        'database' => 'o6c'
    ];

    $config['domains'] = [
        'localhost:8888',
        'o6c.ca',
        's6l.ca'
    ];

    // Run the `jwt.sh` script inside this directory to generate key RSA key for the JTW token.
    $config['jwt'] = [
        'expiration' => 3600,
        'issuer' => 'o6c',
        'audience' => 'api',
        'id' => 'e6057ff1-80c6-4817-b4ca-14d31a3bc2fa',
        'privateKey' => file_get_contents(__DIR__ . '/jwt.key'),
        'publicKey' => file_get_contents(__DIR__ . '/jwt.key.pub')
    ];

    return $config;
};
