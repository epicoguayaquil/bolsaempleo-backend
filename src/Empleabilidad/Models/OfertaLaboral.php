<?php

namespace App\Empleabilidad\Models;

use App\Root\Models\BaseModel;

class OfertaLaboral extends BaseModel{
    protected $table = "oferta_laboral";
    protected $connection = "db_empleabilidad";
    /**
     * @var array
     */
    public $fillable = ['titulo', 'id_organizacion', 'id_modalidad', 'id_tipo_contrato', 'fecha_publicacion', 
        'fecha_max_registro', 'detalle', 'url_externa', 'estado', 'registro_simple', 'orden'];
    
}