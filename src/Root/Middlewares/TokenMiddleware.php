<?php

function matchUrlPattern($url, $patterns) {
    $urlAux = str_replace(PATH_API, '', $url);
    foreach ($patterns as $pattern) {
        if (preg_match("#^" . $pattern . "$#", $urlAux)) {
            return true;
        }
    }
    return false;
}

$app->add(function (Slim\Psr7\Request $request, \Psr\Http\Server\RequestHandlerInterface $handler) use($app) {
    $response = new \Slim\Psr7\Response();
    $url =$request->getUri()->getPath();
    $urlNoValidaToken = !empty(IGNORE_TOKEN) ? IGNORE_TOKEN : [];
    $respuesta = new stdClass();
    //if(!in_array($url, $urlNoValidaToken)){
    if (!matchUrlPattern($url, $urlNoValidaToken)) {
        $headersRequest = $request->getHeader(JWT_TOKEN);
        if(count($headersRequest)>0){
            $token = $headersRequest[0];
            $consumirAPI = new App\Root\Controllers\ConsumeApi($app->getContainer());
            $consumirAPI->verificarTokenApiCE($token);
            $httpcode = $consumirAPI->getStatus();
            $respuesta = $consumirAPI->getRespuestaJSON();
            if($httpcode != 200){
                $response = $response->withStatus($httpcode);
            }else{
                if(!empty($respuesta->token)){
                    $response = $response->withHeader(JWT_TOKEN, $respuesta->token)->withHeader("expiration_time", $respuesta->expiration_time);
                }
                if(!empty($respuesta->user)){
                    $request = $request->withAttribute("user", json_encode($respuesta->user));
                }
                return $handler->handle($request);
            }
        }else{
            if($_SERVER['REQUEST_METHOD'] != 'OPTIONS'){
                $response = $response->withBody((new \Slim\Psr7\Factory\StreamFactory())->createStream(json_encode([
                    'status' => 'failed',
                    'message' => 'Esta acción no está permitida',
                ])))->withStatus(401);
            }
        }
    }else{
        return $handler->handle($request);
    }

    return $response;
});