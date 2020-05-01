<?php

declare(strict_types=1);

use Pimple\Container;

use Lcobucci\JWT\{
    Builder,
    Parser,
    Signer,
    ValidationData
};
use Lcobucci\JWT\Signer\Rsa\Sha256;

use Only6\Services\{
    HitRepository,
    LinkRepository,
    UserRepository
};

return function (Container $container) {
    $container[PDO::class] = function (Container $container): PDO {
        $db = $container['config']['database'];
        if ($db['type'] === 'sqlite') {
            $dsn = $db['type'] . ':' . $db['database'];
        } else {
            $dsn = $db['type'] . ':host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['database'];
        }
        $pdo = new PDO($dsn, $db['username'], $db['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    };

    $container[HitRepository::class] = function(container $container): HitRepository {
        return new HitRepository($container[PDO::class]);
    };

    $container[LinkRepository::class] = function (Container $container) : LinkRepository {
        return new LinkRepository($container[PDO::class]);
    };

    $container[UserRepository::class] = function (Container $container) : UserRepository {
        return new UserRepository($container[PDO::class]);
    };

    $container[Builder::class] = function(Container $container) : Builder {
        $jwt = $container['config']['jwt'];
        $jwtBuilder = new Builder();
        $jwtBuilder
            ->issuedBy($jwt['issuer'])
            ->permittedFor($jwt['audience'])
            ->identifiedBy($jwt['id'], true)
            ->issuedAt(time())
            ->canOnlyBeUsedAfter(time())
            ->expiresAt((time() + $jwt['expiration']));

        return $jwtBuilder;
    };

    $container[Parser::class] = function (Container $container) : Parser {
        return new Parser();
    };

    $container[Signer::class] = function (Container $container) : Signer {
        return new Sha256();
    };

    $container[ValidationData::class] = function(Container $container): ValidationData {
        $jwt = $container['config']['jwt'];
        $data = new ValidationData();
        $data->setIssuer($jwt['issuer']);
        $data->setAudience($jwt['audience']);
        $data->setId($jwt['id']);
        return $data;
    };
};