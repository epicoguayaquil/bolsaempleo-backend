<?php

namespace App\Root\Controllers;

use App\Root\Validators\BaseMessage;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;

class BaseController extends BaseMessage
{
    protected $container;
    protected $user;
    protected $token;
    protected $request;
    protected $path;
    
    public function __construct(?\Psr\Container\ContainerInterface $container = null) {
        $this->container = $container;
    }

    protected function existePerfil($idPerfil){
        if(empty($this->user)){
            return false;
        }
        if(empty($this->user->perfilesDic[$idPerfil])){
            return false;
        }
        return true;
    }
    
    public function getUser($request=null){
        if ($request instanceof \Slim\Psr7\Request) {
            return $this->_getUser($request);
        }else{
            if(empty($this->user) && !empty($this->request)){
                return $this->_getUser($this->request);
            }
            return $this->user;
        }
    }
    
    public function setUser($user){
        if ($user instanceof \Slim\Psr7\Request) {
            $this->user = $this->_getUser($user);
        }else{
            $this->user = $user;
        }
    }
    
    public function getToken($request=null){
        if ($request instanceof \Slim\Psr7\Request) {
            return $this->_getToken($request);
        }else{
            if(empty($this->token) && !empty($this->request)){
                return $this->_getToken($this->request);
            }
            return $this->token;
        }
    }
    
    public function setToken($request){
        if ($request instanceof \Slim\Psr7\Request) {
            $this->token = $this->_getToken($request);
        }else{
            $this->token = $request;
        }
    }
    
    public function setRequest(Request $request){
        $this->request = $request;
    }
    
    public function getRequest(){
        return $this->request;
    }
    
    public function getPath(){
        if (AMBIENTE == 'DESARROLLO') {
            $systemParam = \App\Root\Models\SystemParamBD::where('nombre', 'RUTA_ARCHIVOS_URL_LOCAL')->first();
        }
        else {
            $systemParam = \App\Root\Models\SystemParamBD::where('nombre', 'RUTA_ARCHIVOS_URL')->first();
        }
        if(empty($systemParam)){
            return null;
        }
        $this->path = $systemParam->valor;
        return $this->path;
    }
    
    private function _getToken(Request $request){
        $headersRequest = $request->getHeader(JWT_TOKEN);
        if(count($headersRequest)>0){
            return $headersRequest[0];
        }
        return null;
    }
    
    private function _getUser(Request $request){
        $usuario = json_decode($request->getAttribute("user"));
        if(!empty($usuario)){
            $usuario->perfilesDic=array();
            $usuario->perfiles = isset($usuario->perfiles) ? $usuario->perfiles : array();
            foreach ($usuario->perfiles as $perfil) {
                $usuario->perfilesDic[$perfil->id_perfil] = $perfil;
            }
        }else{
            $token = $request->getHeader(JWT_TOKEN);
            if (count($token) > 0) {
                $token = $token[0];
                try {
                    $data = JWT::decode($token, new Key(JWT_KEYEPICO, JWT_ALGORITMO));
                    return $data->dta;
                } 
                catch (Exception $ex) {
                    return null;
                }
            }
        }
        return $usuario;
    }

    public function info($mensaje){
        $uri = $this->request ? (string)$this->request->getUri() : "No url";
        if($this->container && $this->container->get('logger')){
            $currentClass = get_class($this);
            $this->container->get('logger')->error($currentClass, ['mensaje' => $mensaje, 'url' => $uri]);
        }
    }

    public function error($mensaje){
        $uri = $this->request ? (string)$this->request->getUri() : "No url";
        if($this->container && $this->container->get('logger')){
            $currentClass = get_class($this);
            $this->container->get('logger')->error($currentClass, ['mensaje' => $mensaje, 'url' => $uri]);
        }
    }
}
