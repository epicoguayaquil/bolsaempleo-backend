<?php

$app->group(PATH_API, function (Slim\Interfaces\RouteCollectorProxyInterface $group) use($validatorFactory) {
    include_once 'routes/empleabilidad.php';
});