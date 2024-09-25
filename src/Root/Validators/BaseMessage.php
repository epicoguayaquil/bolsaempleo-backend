<?php

namespace App\Root\Validators;

class BaseMessage
{
    /**
     * @OA\Property(type="object", property="error",ref="#/components/schemas/Error")
     */
    protected $error;

    /**
     * @OA\Property(type="object", property="warning",ref="#/components/schemas/Warning")
     */
    protected $warning;

    /**
     * @OA\Property(type="int", property="code_error")
     */
    protected $code_error;

    /**
     * @OA\Property(type="int", property="mensaje_error")
     */
    protected $mensaje;
    
    protected function addError($attr, $mensaje){
        $this->addMessage($this->error, $attr, $mensaje);
    }
    
    protected function addWarning($attr, $mensaje){
        $this->addMessage($this->warning, $attr, $mensaje);
    }
    
    private function addMessage(&$array, $attr, $mensaje){
        if(empty($array)){
            $array = array();
        }
        if(empty($array[$attr])){
            $array[$attr] = [];
        }
        $array[$attr][] = $mensaje;
    }
        
    public function getError() {
        return $this->error;
    }

    public function setError($error){
        $this->error = $error;
    }
    
    public function getWarning() {
        return $this->warning;
    }

    public function setWarning($warning){
        $this->warning = $warning;
    }

    public function clear(){
        $this->error = null;
        $this->warning = null;
    }

    public function getMensajeError($attr):array{
        return $this->_getMensajeError($this->error, $attr);
    }

    public function getMensajeWarning($attr):array{
        return $this->_getMensajeError($this->warning, $attr);
    }

    private function _getMensajeError($array, $attr):array{
        return $array[$attr];
    }

    public function setCodeError(int $code_error){
        $this->code_error = $code_error;
    }

    public function getCodeError():int{
        return $this->code_error;
    }

    public function isExecuteOK():bool{
        if(empty($this->code_error)){
            return true;
        }
        return $this->code_error === 1;
    }

    public function setMensaje($mensaje){
        $this->mensaje = $mensaje;
    }

    public function getMensaje(){
        return $this->mensaje;
    }
}

/**
 * @OA\Schema(
 *     title="Error",
 *     description="Error model"
 * )
 */
class Error{
    /**
     * @OA\Property(type="array", property="param_name",
     *     @OA\Items(
     *        type="string"
     *     )
     * )
     */
    public $param = [];
}

/**
 * @OA\Schema(
 *     title="Warning",
 *     description="Warning model"
 * )
 */
class Warning{
    /**
     * @OA\Property(type="array", property="param_name",
     *     @OA\Items(
     *        type="string"
     *     )
     * )
     */
    public $param = [];
}
