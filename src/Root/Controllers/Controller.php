<?php

namespace App\Root\Controllers;
use App\Root\Controllers\Response as ControllersResponse;

abstract class Controller extends BaseController {

    public function __construct(\Psr\Container\ContainerInterface $container) {
        parent::__construct($container);
    }

    public function hello(\Slim\Psr7\Request $request, \Slim\Psr7\Response $resp)  {
        $this->container->get('logger')->info($request->getUri() . " route");
        $response = new ControllersResponse($resp);
        $response->setMensaje('Servicio activo');
        return $response->responseOK();
    }
    
    public abstract function create(\Slim\Psr7\Request $request, \Slim\Psr7\Response $response);
    public abstract function update(\Slim\Psr7\Request $request, \Slim\Psr7\Response $response, $arg);
}
