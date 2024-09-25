<?php

namespace App\Root\Validators;

use App\Root\Controllers\BaseController;
use App\Root\Models\BaseModel;

class BaseValidator extends BaseController {
    protected $container;
    protected $validatorFactory;
    protected $model;

    public function __construct(\Psr\Container\ContainerInterface $container, BaseModel &$model) {
        $this->container = $container;
        $this->model = $model;
        $this->validatorFactory = $this->container->get('validator');
    }

    public function setModel(&$model){
        $this->model = $model;
    }

    public function isValid(){
        if(is_null($this->validatorFactory)){
            $this->addError("message", "Validator not found");
            return false;
        }
        $validator = null;
        if($this->model->id){
            // se agrega el ID del registro para ignorar el unique del propio registro
            $reglas = array_map(function($o){return $o;}, $this->model->rules);
            foreach($reglas as $key => &$rule){
                // Separar las reglas por "|"
                $ruleParts = explode('|', $rule);
                foreach($ruleParts as &$rulePart){
                    // Si la regla contiene "unique:", capturar la palabra siguiente
                    if(preg_match('/unique:([^,]+),([^|]+)/', $rulePart, $matches)){
                        $table = $matches[1];
                        $column = $matches[2];
                        // Insertar el texto adicional
                        $rulePart = "unique:$table,$column,".$this->model->id;
                    }
                }
                // Reconstruir la regla
                $rule = implode('|', $ruleParts);
            }
            $validator = $this->validatorFactory->make($this->model->toArray(), $reglas);
        }
        else{
            $validator = $this->validatorFactory->make($this->model->toArray(), $this->model->rules);
        }

        if (!is_null($validator) && $validator->fails()){
            $this->setError($validator->messages()->toArray());
        }

        return !$validator->fails();
    }
    
    /*
     * @param('campo') es opcional, en caso de querer agregan un campo adicional sobre el objeto
     * @param('valor') es opcional, en caso de querer agregan un campo adicional sobre el objeto y su valor respectivo
     */
    public function isValidateArray(array $array, $campo=null, $valor=null){
        if(is_null($array)){
            $this->addError("Lista", "Lista vacia");
            return false;
        }
        if(!is_array($array)){
            $this->addError("Lista", "No es una lista valida");
            return false;
        }
        foreach ($array as $object) {
            if(!is_null($campo) && !is_null($valor)){
                if(is_object($object))$object->$campo = $valor;
                if(is_array($object))$object[$campo] = $valor;
            }
            $class = get_class($this);
            $entidad = new $class;
            $entidad->setValidatorFactory($this->validatorFactory);
            $entidad->convertion($object);
            if(!$entidad->isValid()){
                $this->addError("Lista", $entidad->getError());
                //$this->validatorArray[]=$entidad->getError();
            }
        }
        if(count($this->error)>0){
            return false;
        }
        return true;
    }

    public function existe($id=null){
        if(empty($id)){
            return !empty($this->model);
        }
        $this->model = $this->model->find($id);
        return !empty($this->model);
    }

    public function validateParams(array $params, array $reglas):bool{
        $validator = $this->validatorFactory->make($params, $reglas);
        if($validator->fails()){
            $this->setError($validator->messages()->toArray());
        }
        return !$validator->fails();
    }
}
