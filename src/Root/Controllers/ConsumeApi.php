<?php

namespace App\Root\Controllers;

class ConsumeApi
{
    protected $container;
    protected $api;
    protected $api_metodo;
    protected $host;
    protected $path_domain = null;
    protected $param=array();
    protected $jsonPayload;
    protected $token;
    protected $headers=array();
    protected $configAPI = 'DOMAIN_EMPRENDEDOR';
    
    protected $httpcode;
    protected $respuesta;
    
    private $loadConfiguration = true;
    private $isJson=false;

    public function __construct(\Psr\Container\ContainerInterface $container) {
       $this->container = $container;
    }
    
    public function setContainer(\Psr\Container\ContainerInterface $container){
       $this->container = $container; 
    }
    
    public function get(){
        if(!$this->validaMetodo()){
            return;
        }
        //$url = $this->host.'/'.$this->api.'/'.$this->api_metodo;
        $url = $this->getURL();
        $curl = new \Curl\Curl;
        if(!empty($this->token)){
            //$curl->setHeader('Authorization',$this->token);
            $curl->setHeader(JWT_TOKEN,$this->token);
        }
        foreach ($this->headers as $key => $value) {
            $curl->setHeader($key,$value);
        }
        $this->setSSL($curl);
        $curl->get($url, $this->param);
        $this->httpcode = $curl->http_status_code;
        $this->respuesta = $curl->response;
        $curl->close();
    }
    
    private function _post(){
        if(!$this->validaMetodo()){
            return;
        }
        //$url = $this->host.'/'.$this->api.'/'.$this->api_metodo;
        $url = $this->getURL();
        $curl = new \Curl\Curl;
        if(!$this->setSSL($curl)){return;}
        if(!empty($this->token)){
            //$curl->setHeader('Authorization',$this->token);
            $curl->setHeader(JWT_TOKEN,$this->token);
        }
        foreach ($this->headers as $key => $value) {
            $curl->setHeader($key,$value);
        }
        
        $resp = $curl->post($url, $this->param, $this->isJson);
        $this->httpcode = $curl->httpStatusCode;
        $this->respuesta = $curl->response;
        $curl->close();
    }
    
    public function post(){
        $this->isJson = false;
        if(!is_array($this->param)){
            $this->param = get_object_vars($this->param);
        }
        $this->_post();
    }
    
    public function postJson(){
        $this->isJson = true;
        $this->_post();
    }
    
    /**
     * Make a put request with optional data.
     *
     * The put request data can be either sent via payload or as get parameters of the string.
     *
     * @param array $arg Optional data to pass to the $url
     * @param bool $payload Whether the param attribute should be transmitted trough payload or as get parameters of the string
     * @return self
     */
    public function put($arg = array(), $payload = true){
        if(!$this->validaMetodo()){
            return;
        }
        $url = $this->getURL();
        $curl = new \Curl\Curl;
        $this->setSSL($curl);
        if(!empty($this->token)){
            //$curl->setHeader('Authorization',$this->token);
            $curl->setHeader(JWT_TOKEN,$this->token);
        }
        foreach ($this->headers as $key => $value) {
            $curl->setHeader($key,$value);
        }
        if(count($arg)>0){
            $url .= '?'.http_build_query($arg);
        }
        $curl->put($url, $this->param, $payload);
        $this->httpcode = $curl->http_status_code;
        $this->respuesta = $curl->response;
        $curl->close();
    }
    
    /**
     * Make a put request with optional data.
     *
     * The put request data can be either sent via payload or as get parameters of the string.
     *
     * @param array $arg Optional data to pass to the $url
     * @param bool $payload Whether the param attribute should be transmitted trough payload or as get parameters of the string
     * @return self
     */
    public function delete($payload = true){
        if(!$this->validaMetodo()){
            return;
        }
        $url = $this->getURL();
        $curl = new \Curl\Curl;
        $this->setSSL($curl);
        if(!empty($this->token)){
            //$curl->setHeader('Authorization',$this->token);
            $curl->setHeader(JWT_TOKEN,$this->token);
        }
        foreach ($this->headers as $key => $value) {
            $curl->setHeader($key,$value);
        }
        $curl->delete($url, $this->param, $payload);
        $this->httpcode = $curl->http_status_code;
        $this->respuesta = $curl->response;
        $curl->close();
    }
    
    private function validaMetodo(){
        if(!$this->loadParam()){
            return true;
        }
        if(empty($this->api) || empty($this->api_metodo)){
            $this->httpcode = 400;
            $this->respuesta = json_encode(["mensaje"=>"Metodo no configurado", "error"=>["mensaje"=>"Metodo no configurado"]]);
            return false;
        }
        return true;
    }
    
    private function loadParam(){
        if(!$this->loadConfiguration){
            return true;
        }
        if(!empty($this->configAPI)){
            $paramSistem = \App\Root\Models\SystemParamBD::where('nombre',$this->configAPI)->first();
            if(empty($paramSistem)){
                $this->httpcode = 400;
                $this->respuesta = json_encode(["mensaje"=>"No se encuentra configurado el API", "error"=>["mensaje"=>"No se encuentra configurado el API"]]);
                return false;
            }
            if(!empty($paramSistem->valor_json)){
                $config = json_decode($paramSistem->valor_json);
                if(AMBIENTE == 'DESARROLLO'){
                    $this->host = HOSTLOCAL;
                }else{
                    $this->host = !empty($config->host) ? $config->host : $this->host;
                }
                $this->api = !empty($config->api) ? $config->api : $this->api;
                $this->path_domain = !empty($config->path_domain) ? $config->path_domain : $this->path_domain;
                $this->api_metodo = !empty($config->api_metodo) ? $config->api_metodo : $this->api_metodo;
                $this->loadConfiguration = false;
                return true;
            }
            if(!empty($paramSistem->valor)){
                $this->host = $paramSistem->valor;
                $this->loadConfiguration = false;
                return true;
            }
            $this->httpcode = 400;
            $this->respuesta = json_encode(["mensaje"=>"No se encuentra configurado el API", "error"=>["mensaje"=>"No se encuentra configurado el API"]]);
            return false;
        }
        return true;
    }
    
    public function consultarTokenApiCE(){
        $this->setConfigApiParamSystem("API_SEGURIDAD");
        $this->addParam("usuario", "ernesto.ruales@gmail.com");
        $this->addParam("password", "Rpen.782537");
        $this->addParam("app", "EPICOCEEMPRENDEDOR");
        $this->setApiMetodo("public/api/login/ingresar");
        $this->setOrigin();
        $this->post();
    }
    
    public function verificarTokenApiCE($token){
        $this->setConfigApiParamSystem("API_SEGURIDAD");
        $this->setApiMetodo("public/api/login/verificar");
        $this->addHeader(JWT_TOKEN, $token);
        $this->setOrigin();
        $this->post();
    }
    
    public function setApi($api){
        $this->api = $api;
    }
    public function setApiMetodo($api_metodo){
        $this->api_metodo = $api_metodo;
    }
    public function setHost($host){
        $this->host = $host;
    }
    public function setParam($param){
        if(is_array($param)){
            $this->param = $param;
        }else{
            $this->param = get_object_vars($param);
        }
    }
    public function setToken($token){
        $this->token = $token;
    }
    public function addHeader($key,$value){
        $this->headers[$key] = $value;
    }
    public function addParam($key,$value){
        $this->param[$key] = $value;
    }
    public function setConfigApiParamSystem($configAPI){
        $this->loadConfiguration = true;
        $this->configAPI = $configAPI;
    }
    
    public function getRespuesta(){
        return $this->respuesta;
    }
    public function getRespuestaJSON(){
        $respuesta = is_string($this->respuesta) ? json_decode($this->respuesta) : $this->respuesta;
        return $respuesta;
    }
    public function getStatus(){
        return $this->httpcode;
    }
    
    public function resetAll(){
        $this->container=null;
        $this->api=null;
        $this->api_metodo=null;
        $this->host=null;
        $this->resetParam();
        $this->resetHeader();
        $this->token=null;
        $this->configAPI = 'DOMAIN_EMPRENDEDOR';
        $this->loadConfiguration = true;

        $this->httpcode=null;
        $this->respuesta=null;
        $this->isJson = false;
    }
    
    public function reset(){
        $this->httpcode=null;
        $this->respuesta=null;
        $this->api_metodo=null;
        $this->isJson = false;
        $this->resetParam();
        $this->resetHeader();
    }
    
    public function resetParam(){
        $this->param=array();
    }
    public function resetHeader(){
        $this->headers=array();
    }
    
    public function setOrigin(){
        $uri = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'] ;
        $uri .= '://' . $_SERVER['HTTP_HOST'];
        //echo $uri;
        //$this->addHeader('Origin', "http://api.centroemprendimiento.epico.gob.ec");
        $this->addHeader('Origin', $uri);
    }

    public function getURL(){
        if(empty($this->path_domain))
            $url = $this->host.'/'.$this->api.'/'.$this->api_metodo;
        else
            $url = $this->host.'/'.$this->path_domain.'/'.$this->api.'/'.$this->api_metodo;
        return $url;
    }
    
    private function setSSL(\Curl\Curl &$curl){
        $protocolo = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'] ;
        //if($protocolo == 'https'){
            /*$paramSistem = \App\Root\Models\SystemParamBD::where('nombre','CETIFICADO_CONFIANZA_APIS')->first();
            if(empty($paramSistem)){
                $this->httpcode = 400;
                $this->respuesta = json_encode(["mensaje"=>"No se encuentra el certificado de confianza", "error"=>["mensaje"=>"No se encuentra el certificado de confianza"]]);
                return false;
            }*/
            //$curl->setOpt(CURLOPT_CAINFO, "C:/proyectos/rest_api_centro_emprendimiento/certificados/api-centroemprendimiento-epico-gob-ec.pem");
            //$curl->setOpt(CURLOPT_CAPATH, "C:/proyectos/rest_api_centro_emprendimiento/certificados/api-centroemprendimiento-epico-gob-ec.pem");
            //$curl->setOpt(CURLOPT_CAINFO, "/home3/epicoez2/api.centroemprendimiento.epico.gob.ec/api-centroemprendimiento-epico-gob-ec.pem");
            //$curl->setOpt(CURLOPT_CAPATH, "/home3/epicoez2/api.centroemprendimiento.epico.gob.ec/certificados/");
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        //}
        return true;
    }
}
