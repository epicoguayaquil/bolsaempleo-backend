<?php

namespace App\Empleabilidad\Models;

use App\Root\Models\BaseModel;

class Postulante extends BaseModel{
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_modificacion';

    protected $table = 'postulante';
    protected $connection = "db_empleabilidad";
    public $fillable = ['id_persona', 'id_usuario','is_estudiante','is_trabajando','has_experiencia','perfil',
        'id_rango_salarial','id_modalidad','observacion', 'otras_habilidades'];

    public $rules = [
        'id_usuario' => ['unique:db_empleabilidad.postulante'],
        'id_persona' => ['required','unique:db_empleabilidad.postulante'],
        'has_experiencia' => ['required'],
        'is_estudiante' => ['required'],
        'is_trabajando' => ['required']
    ];
}