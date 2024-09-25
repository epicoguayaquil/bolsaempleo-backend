<?php

namespace App\Root\Models;

class SystemParamBD extends BaseModel {
    //put your code here
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'parametro_sistema';
    protected $connection = "db_trans";
    public $fillable = ['nombre','valor','estado','valor_json'];

    public $rules = [
        'nombre' => ['required'],
    ];
}
