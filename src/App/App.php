<?php

use Slim\Factory\AppFactory;
require __DIR__ . '/../../vendor/autoload.php';
require '../env.php';

$container_aux = new \DI\Container;
AppFactory::setContainer($container_aux);

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
    "ignore" =>["/auth","/usuario"],
    "secret" => getenv('clave'),
    "algorithm" => ["HS256", "HS384"]
]));

$container= $app->getContainer();

require 'Routes.php';
require 'Config.php';
require 'Conexion.php';

$app->run();