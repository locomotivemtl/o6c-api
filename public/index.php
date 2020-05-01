<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

use Pimple\Container;
use Pimple\Psr11\Container as Psr11Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container['config'] = (require(__DIR__ . '/../config/config.php'))();

(require(__DIR__ . '/../app/services.php'))($container);

AppFactory::setContainer(new Psr11Container($container));
$app = AppFactory::create();

(require(__DIR__ . '/../app/routes.php'))($app);

$app->run();