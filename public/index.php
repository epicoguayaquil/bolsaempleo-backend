<?php
use Slim\Factory\AppFactory;
use DI\Container;
require __DIR__.'/../src/environment.php';
require __DIR__.'/../vendor/autoload.php';
$settings = require __DIR__.'/../src/settings.php';

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

require __DIR__.'/../src/middleware.php';
require __DIR__.'/../src/dependencies.php';

require '../src/routes.php';

try {
    $app->run();     
} catch (Exception $e) {    
  // We display a error message
  die( json_encode(array("status" => "failed", "message" => "This action is not allowed"))); 
}