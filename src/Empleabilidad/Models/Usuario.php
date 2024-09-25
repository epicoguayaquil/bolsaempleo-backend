<?php
namespace App\Models;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Root\Models\BaseModel;
/**
 * Description of Usuario
 *
 * @author ernesto.ruales
 */
class Usuario extends BaseModel {
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    protected $table = 'usuario';
    public $fillable = ['usuario','nombre','apellido','correo','id_institucion','estado','foto','last_access','foto_banner','id_persona'];
    protected $hidden = ['fecha_creacion', 'last_access'];

    public $rules = [
        'nombre' => 'required|string|max:255',
        'usuario' => 'required|email|max:255|unique:usuario,usuario|min:8',
        'password' => 'required|string|min:8'
    ];
}
