<?php

namespace App\Root\Controllers;

use App\Root\Validators\BaseMessage;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Response
 *
 * @author ernesto.ruales
 */
/**
 * @OA\Schema(
 *     title="Response",
 *     description="Response Model"
 * )
 */
class Response extends BaseMessage {
    //put your code here
    /**
     * @OA\Property(type="string", property="codigo")
     */
    protected $code;
    /**
     * @OA\Property(type="string", property="mensaje")
     */
    protected $message;
    /**
     * @OA\Property(type="object", property="data")
     */
    protected $data;
    
    protected $response;
    protected $attributes=array();
    
    public static $codeStatusError = 400;
    public static $codeStatusOK = 200;
    public static $codeStatusOKUpdate = 201;
    public static $codeStatusNotFoundData = 404;
    public static $codeStatusErrorValidation = 422;

    public function __construct(\Slim\Psr7\Response $response){
        $this->response = $response;
    }
    
    public function responseError(){
        $this->code = $this->code ?? 0;
        $this->message = $this->message ?? "Error en la ejecución";
        return $this->responseJson(Response::$codeStatusError);
    }
    
    public function responseOK(){
        $this->code = $this->code ?? 0;
        return $this->responseJson(Response::$codeStatusOK);
    }
    
    public function responseNotFound(){
        $this->code = $this->code ?? 0;
        $this->message = $this->message ?? "No encontrado";
        return $this->responseJson(Response::$codeStatusNotFoundData);
    }

    public function responseErrorValidation(){
        $this->code = $this->code ?? 0;
        $this->message = $this->message ?? "Error en validaciones";
        return $this->responseJson(Response::$codeStatusErrorValidation);
    }
    
    public function responseUpdate(){
        $this->code = $this->code ? $this->code : 1;
        return $this->responseJson(Response::$codeStatusOKUpdate);
    }
    
    public function responseNoPrivilegios(){
        $this->code = $this->code ? $this->code : 0;
        $this->message = $this->message ?? "Permisos insuficientes";
        return $this->responseJson(Response::$codeStatusOKUpdate);
    }
    
    public function responseJson($status = 200){
        switch ($status){
            case 200: $this->code = $this->code ? $this->code : 1;break;
            case 201: $this->code = $this->code ? $this->code : 1;break;
            case 400:
                $this->code = $this->code ? $this->code : 0;
                $this->message = $this->message ? $this->message : "Error en la ejecucion del proceso";
                break;
            default :$this->code = $this->code ? $this->code : 0;break;
        }
        $data = new \stdClass();
        $data->codigo = $this->code;
        $data->mensaje = $this->message ? $this->message : 'Ejecutado con exito';
        $data->data = $this->data;
        $data->error = $this->error;
        $data->warning = $this->warning;
        if(count($this->attributes)>0){
            foreach ($this->attributes as $col => $val) {
                $data->$col=$val;
            }
        }
        $this->response->getBody()->write(json_encode($data));
        return $this->response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
    
    public function __call($method, $args){
        return call_user_func_array(
            [$this->response, $method],
            $args
        );
    }
    
    public function setCode($code){
        $this->code = $code;
    }

    public function getCode(){
        return $this->code;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function addAttribute($key, $value){
        $this->attributes[$key]=$value;
    }
}