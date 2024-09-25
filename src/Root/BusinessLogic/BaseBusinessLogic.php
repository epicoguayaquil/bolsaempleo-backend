<?php

namespace App\Root\BusinessLogic;

use App\Root\Controllers\BaseController;
use App\Root\Models\BaseModel;
use App\Root\Validators\BaseValidator;
use stdClass;

abstract class BaseBusinessLogic extends BaseController{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;
    /**
     * @var BaseValidator
     */
    protected $validator;
    /**
     * @var BaseModel
     */
    protected $model;

    public function __construct(\Psr\Container\ContainerInterface $container) {
        parent::__construct($container);
        $this->container = $container;
        $this->instanceModel();
        $this->validator = new BaseValidator($this->container, $this->model);
    }

    abstract protected function instanceModel();    

    public function create($data):bool{
        $this->model->convertion($data);
        if(!$this->validator->isValid()){
            $this->setError($this->validator->getError());
            $this->setCodeError(3);
            return false;
        }
        $this->model->save();
        $this->setCodeError(1);
        return true;
    }

    public function update($data, $id):bool{
        $this->find($id);
        $this->validator->setModel($this->model);
        if(!$this->validator->existe()){
            $this->addError("id", "Registro no existe");
            $this->setCodeError(2);
            return false;
        }
        $this->model->convertion($data);
        if(!$this->validator->isValid()){
            $this->setError($this->validator->getError());
            $this->setCodeError(3);
            return false;
        }
        $this->model->save();
        $this->setCodeError(1);
        return true;
    }

    public function delete($id):bool{
        $this->find($id);
        if(empty($this->model)){
            $this->addError('id', 'No encontrado');
            return false;
        }
        $this->model->estado = 'E';
        $this->model->save();
        return true;
    }

    public function find($id){
        $this->model = $this->model->find($id);
        return $this->model;
    }

    public function setModel(BaseModel $model){
        $this->model = $model;
    }

    public function getModel(){
        return $this->model;
    }

    protected function resetModelPaginator($query, $params){
        $paramsAux = is_array($params) ? $params : (Array) $params;
        $this->model = new stdClass;
        $this->model->records = $query->count();
        $this->model->page = $paramsAux['page'];
        $this->model->rows = $paramsAux['rows'];
        $this->model->pages = !empty($registros) ? ceil($registros/$this->model->rows) : 0;
        $this->model->data = $query->skip(($this->model->page-1)*$this->model->rows)->take($this->model->rows)->get();
    }
}
