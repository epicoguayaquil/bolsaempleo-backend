<?php

namespace App\Empleabilidad\Controllers;

use App\Empleabilidad\BusinessLogic\EmpleabilidadBusinessLogic;
use App\Root\Controllers\Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Root\Controllers\Response as ControllersResponse;

class EmpleabilidadController extends Controller
{
    private $empleabilidadBusinessLogic;

    public function __construct(\Psr\Container\ContainerInterface $container) {
        $this->container = $container;
        $this->empleabilidadBusinessLogic = new EmpleabilidadBusinessLogic($this->container);
    }

    public function getOfertaLaboral(Request $request, Response $resp, $args) {
        $this->container->get('logger')->info($request->getUri() . " route");
        $response = new ControllersResponse($resp);
        $params = $request->getParsedBody() ?? array();
        $this->empleabilidadBusinessLogic->getOfertaLaboral($args['id'], $params);
        $response->setData($this->empleabilidadBusinessLogic->getModel());
        $response->addAttribute("path", $this->getPath());
        return $response->responseOK();
    }

    public function ofertasLaborales(Request $request, Response $resp) {
        $this->container->get('logger')->info($request->getUri() . " route");
        $response = new ControllersResponse($resp);
        $params = $request->getParsedBody() ?? array();
        $params['page'] = empty($params['page']) ? 1 : intval($params['page']);
        $params['rows'] = empty($params['rows']) ? 10 : intval($params['rows']);

        $ofertasLaborales = $this->empleabilidadBusinessLogic->getModel();
        $registros = $ofertasLaborales->count();
        $response->setData($ofertasLaborales->skip(($params['page']-1)*$params['rows'])->take($params['rows'])->get());
        $response->addAttribute("page", $params['page']);
        $response->addAttribute("rows", $params['rows']);
        $response->addAttribute("records", $registros);
        $response->addAttribute("pages", !empty($registros) ? ceil($registros/$params['rows']) : 0);
        $response->addAttribute("path", $this->getPath());
        return $response->responseOK();
    }

    public function postular(Request $request, Response $resp, $args) {
        $this->container->get('logger')->info($request->getUri() . " route");
        $response = new ControllersResponse($resp);
        $this->setRequest($request);
        $params = array();
        $params['id_oferta_laboral'] = $args['id'];
        $params['id_usuario'] = $this->getUser()->id;
        if($this->empleabilidadBusinessLogic->postular($params)){
            $response->setData($this->empleabilidadBusinessLogic->getModel()->id);
            return $response->responseOK();
        }
        
        switch($this->empleabilidadBusinessLogic->getCodeError()){
            case 2:
                $response->setMessage("Faltan parametros");
                $response->setError($this->empleabilidadBusinessLogic->getError());
                return $response->responseError();
            case 3:
                $response->setMessage("No existe la Oferta Laboral");
                $response->setError($this->empleabilidadBusinessLogic->getError());
                return $response->responseNotFound();
            case 4: 
                $response->setMessage("No se encuentra registrado");
                $response->setError($this->empleabilidadBusinessLogic->getError());
                return $response->responseErrorValidation();
            case 5: 
                $response->setMessage("Ya te encuentras registrado en esta oferta");
                $response->setCode('2');
                return $response->responseOK();
        }

        $response->setMessage("No se pudo Postular");
        $response->setError($this->empleabilidadBusinessLogic->getError());
        return $response->responseError();
        
    }

    public function create(Request $request, Response $resp) {}

    public function update(Request $request, Response $resp, $arg) {}
}
