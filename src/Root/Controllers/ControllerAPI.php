<?php

namespace App\Root\Controllers;

abstract class ControllerAPI
{
    protected $container;
    protected $consumirAPI;
    protected $token;
    public $respuesta;
    
    abstract function create($param);
    abstract function update($param);
    
    public function consultarToken(){
        $this->respuesta = new \stdClass;
        $this->consumirAPI = new ConsumeApi($this->container);
        $this->consumirAPI->consultarTokenApiCE();
        if($this->consumirAPI->getStatus()!=200){
            $this->respuesta->mensaje = "Error en la comunicacion con el API-TOKEN";
            $this->respuesta->error = ["token"=>"Error en la comunicacion con el API-TOKEN", "status"=>$this->consumirAPI->getStatus()];
            return false;
        }
        $respuesta = $this->consumirAPI->getRespuestaJSON();
        $this->token = $respuesta->token;
        return true;
    }
    
    function setToken($token) {
        $this->token = $token;
    }
}
