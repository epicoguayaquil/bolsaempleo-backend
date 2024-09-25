<?php

$app->add(function (\Slim\Psr7\Request $request, \Psr\Http\Server\RequestHandlerInterface $handler) {
    $http_origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $remoteAddressPort = null;
    $response = new \Slim\Psr7\Response();

    if ($http_origin) {
        $remoteAddressPort = strpos($http_origin, "//") !== false ? substr($http_origin, strpos($http_origin, "//") + 2) : $http_origin;
        $remoteAddressPort = strpos($remoteAddressPort, "/") !== false ? substr($remoteAddressPort, 0, strpos($remoteAddressPort, "/")) : $remoteAddressPort;
        $remoteAddress = strpos($remoteAddressPort, ":") !== false ? substr($remoteAddressPort, 0, strpos($remoteAddressPort, ":")) : $remoteAddressPort;
    }else{
        // en caso de configurar una IP publica en especifico
        if(isset($_SERVER['REMOTE_ADDR'])){
            $remoteAddress = $_SERVER['REMOTE_ADDR'];
        }
    }

    $CORS_DOMAIN = defined('CORS_DOMAIN') ? CORS_DOMAIN : [];
    if (in_array($http_origin, $CORS_DOMAIN) || in_array($remoteAddress, $CORS_DOMAIN) || in_array($remoteAddressPort, $CORS_DOMAIN)) {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, '.JWT_TOKEN)
            ->withHeader('Access-Control-Expose-Headers', 'authorization,expiration_time')
            ->withHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,PATCH,OPTIONS');

        if ($request->getMethod() !== 'OPTIONS') {
            return $handler->handle($request);
        }
    } else {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', "")
            ->withHeader('Access-Control-Allow-Headers', "")
            ->withHeader('Access-Control-Allow-Methods', "")
            ->withStatus(401) // Cambiado a 401 para indicar acceso no autorizado
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new \Slim\Psr7\Factory\StreamFactory())->createStream(json_encode([
                'status' => 'failed',
                'message' => 'Esta acción no está permitida',
            ])));
    }

    return $response;
});
