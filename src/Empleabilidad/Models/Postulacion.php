<?php

namespace App\Empleabilidad\Models;

use App\Root\Models\BaseModel;

class Postulacion extends BaseModel{
    protected $table = "postulacion";
    protected $connection = "db_empleabilidad";
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = null;
    /**
     * @var array
     */
    public $fillable = ['id_postulante', 'id_oferta_laboral'];
    
    public $rules = [
        'id_postulante' => ['required'],
        'id_oferta_laboral' => ['required']
    ];
    
}