<?php

namespace App\Empleabilidad\BusinessLogic;

use App\Empleabilidad\Models\OfertaLaboral;
use App\Empleabilidad\Validators\PostulacionValidator;
use App\Root\BusinessLogic\BaseBusinessLogic;
use Illuminate\Database\Capsule\Manager as DB;

class EmpleabilidadBusinessLogic extends BaseBusinessLogic
{
    public function __construct(\Psr\Container\ContainerInterface $container){
        parent::__construct($container);
    }

    protected function instanceModel(){
        if(!$this->model){
            $this->model = new OfertaLaboral();
        }
    }

    public function getOfertaLaboral(int $id, array $params=array()){
        $attrPostulacion = ", null as id_postulacion";
        $this->model = \App\Empleabilidad\Models\OfertaLaboral::join('organizacion','organizacion.id','=','oferta_laboral.id_organizacion')
                ->leftJoin('catalogo as modalidad','modalidad.id','=','oferta_laboral.id_modalidad')
                ->leftJoin('catalogo as tipo_contrato','tipo_contrato.id','=','oferta_laboral.id_tipo_contrato');
        if(!empty($params) && count($params) ){
            $params = (object)$params;
            $this->model = $this->model->leftJoin('postulacion',function($query) use ($params){
                    $query->on('postulacion.id_oferta_laboral','=','oferta_laboral.id')->where('postulacion.id_postulante','=',$params->id_postulante);
                });
            $attrPostulacion = ", postulacion.id as id_postulacion";
        }
        
        $this->model = $this->model->where('estado', 'A')
                ->where('oferta_laboral.id', $id)
                ->selectRaw("oferta_laboral.*, modalidad.nombre as modalidad, tipo_contrato.nombre as tipo_contrato, "
                        . "organizacion.nombre as organizacion, organizacion.logo $attrPostulacion")
                ->first();
    }

    public function ofertasLaborales(array $params){
        $attrPostulacion = ", null as id_postulacion";
        $this->model = \App\Empleabilidad\Models\OfertaLaboral::join('organizacion','organizacion.id','=','oferta_laboral.id_organizacion')
                ->leftJoin('catalogo as modalidad','modalidad.id','=','oferta_laboral.id_modalidad')
                ->leftJoin('catalogo as tipo_contrato','tipo_contrato.id','=','oferta_laboral.id_tipo_contrato');
        if(!empty($params) && count($params) ){
            if(!empty($params['id_postulante'])){$attrPostulacion = ", postulacion.id as id_postulacion";}
            $this->model = $this->agregarFiltrosConsultaOfertasLaborales($params);
        }
        $this->model = $this->model->where('estado', 'A')
                ->selectRaw("distinct oferta_laboral.*, modalidad.nombre as modalidad, tipo_contrato.nombre as tipo_contrato, "
                        . "organizacion.nombre as organizacion, organizacion.logo $attrPostulacion")
                ->orderBy('oferta_laboral.orden', 'desc')
                ->orderBy('oferta_laboral.fecha_max_registro', 'desc')
                ->orderBy('organizacion.logo', 'desc');
    }

    private function agregarFiltrosConsultaOfertasLaborales(array $params){
        if(!empty($params['id_postulante'])){
            $this->model = $this->model->leftJoin('postulacion',function($query) use ($params){
                $query->on('postulacion.id_oferta_laboral','=','oferta_laboral.id')->where('postulacion.id_postulante','=',$params['id_postulante']);
            });
        }
        if(!empty($params['id_organizacion'])){
            $this->model = $this->model->where('oferta_laboral.id_organizacion', $params['id_organizacion']);
        }
        if(!empty($params['etiquetas'])){
            $params['etiquetas'] = is_string($params['etiquetas']) ? json_decode($params['etiquetas']) : ($params['etiquetas']);
            if(count($params['etiquetas'])>0){
                $this->model = $this->model->whereExists(function ($query) use ($params) {
                                    $query->select(DB::raw(1))
                                            ->from(DB_EMPL_NAME.'.oferta_laboral_etiquetas')
                                            ->whereColumn('oferta_laboral_etiquetas.id_oferta_laboral', 'oferta_laboral.id')
                                            ->whereIn('oferta_laboral_etiquetas.id_etiqueta', $params['etiquetas']);
                                });   
            }
        }
        return $this->model;
    }

    public function postular(array $params){
        $this->model = new \App\Empleabilidad\Models\Postulacion();
        $postulacionValidator = new PostulacionValidator($this->container, $this->model);
        if(!$postulacionValidator->validarParametros($params)){
            $this->setCodeError($postulacionValidator->getCodeError());
            $this->setError($postulacionValidator->getError());
            return false;
        }
        $postulante = \App\Empleabilidad\Models\Postulante::where('id_usuario', $params['id_usuario'])->first();
        if(empty($postulante)){
            $this->addError("id_usuario", "No se encuentra registrado");
            $this->setCodeError(4);
            return false;
        }
        if(!empty(\App\Empleabilidad\Models\Postulacion::where('id_postulante',$postulante->id)->where('id_oferta_laboral',$params['id_oferta_laboral'])->first())){
            $this->setCodeError(5);
            return false;
        }
        $this->model->id_postulante = $postulante->id;
        $this->model->id_oferta_laboral = $params['id_oferta_laboral'];
        $this->model->save();
        return $this->isExecuteOK();
    }
}
