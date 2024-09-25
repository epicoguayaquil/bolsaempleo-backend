<?php

namespace App\Empleabilidad\Validators;

use App\Empleabilidad\Models\Postulacion;
use App\Root\Validators\BaseValidator;

class PostulacionValidator extends BaseValidator{

    public function __construct(\Psr\Container\ContainerInterface $container, Postulacion $postulacion){
        parent::__construct($container, $postulacion);
        $this->model = $postulacion;
    }

    public function validarParametros(array $params){
        $validacion = $this->container->get('validator')->make($params, [
            'id_oferta_laboral' => ['required'],
            'id_postulante' => ['nullable','required_without:id_usuario'],
            'id_usuario' => ['nullable','required_without:id_postulante']
        ]);
        if ($validacion->fails()){
            $this->setError($validacion->messages()->toArray());
            $this->setCodeError(2);
            return false;
        }
        $ofertaLaboral = \App\Empleabilidad\Models\OfertaLaboral::find($params['id_oferta_laboral']);
        if(empty($ofertaLaboral)){
            $this->addError("oferta_laboral", "No existe la Oferta Laboral");
            $this->setCodeError(3);
            return false;
        }
        return true;
    }
}
