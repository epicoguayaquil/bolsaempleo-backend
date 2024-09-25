<?php

// Middleware de manejo de errores

use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$displayErrorDetails = true; // Cambia a false en producción
$logErrors = true;
$logErrorDetails = true;

// Crear una instancia del logger
$logger = new Logger('app');
if(DEBUG_ERROR){
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::DEBUG));
}else{
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::WARNING));
}

$container->set('logger',$logger);
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails, $logger);

$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app, $logger) {
        $uri = (string)$request->getUri();
        $logger->error('HttpNotFoundException: ', ['exception' => $exception, 'url' => $uri]);
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(
            json_encode(['error' => 'Recurso no encontrado'], JSON_UNESCAPED_UNICODE)
        );
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }
 );
 
// Configurar manejadores de errores personalizados
$errorMiddleware->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app, $logger) {
    $uri = (string)$request->getUri();
    $logger->error($exception->getMessage(), ['exception' => $exception, 'url' => $uri]);
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode(['error' => $exception->getMessage()], JSON_UNESCAPED_UNICODE)
    );
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(500);
});