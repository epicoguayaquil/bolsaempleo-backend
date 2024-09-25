<?php

namespace App\Root\Converters;

class BaseConverter{

    public function transformArray($perfiles, $id=null){
        $array = null;
        switch (gettype($perfiles)){
            case "string":
                $array = json_decode($perfiles);
                break;
            case "array":
                $array = $perfiles;
                break;
        }
        $result = array();
        
        foreach ($array as &$perfil){
            $roleArray = is_object($perfil) ? (array) $perfil : $perfil; // Convertir el objeto a array
            $id = empty($roleArray['id']) ? $id : $roleArray['id']; // Obtener el id
            unset($roleArray['id']); // Eliminar el id del array

            $result[$id] = $roleArray; // Asignar el resto de los atributos al resultado
        }
        return $result;
    }
    
    public function transformArrayValidate($perfiles, $id_usuario=null){
        $array = null;
        switch (gettype($perfiles)){
            case "string":
                $array = json_decode($perfiles);
                break;
            case "array":
                $array = $perfiles;
                break;
        }
        
        if(!is_null($id_usuario)){
            foreach ($array as &$perfil){
                $perfil->id_usuario = $id_usuario;
                unset($perfil);
            }
        }
        return $array;
    }

    public function transformEstructuraSyncAsocciacionModel($intereses){
        $array = null;
        switch (gettype($intereses)){
            case "string":
                $array = json_decode($intereses);
                break;
            case "array":
                $array = $intereses;
                break;
        }
        $newArray = array();
        foreach ($array as &$interes){
            if(is_object($interes) && !empty($interes->id)){
                $newArray[$interes->id] = get_object_vars($interes);
                unset($newArray[$interes->id]["id"]);
            }
            else{
                $newArray[] = $interes;
            }
        }
        return $newArray;
    }

    public function transformCanalesVentas($canales_ventas){
        $array = null;
        switch (gettype($canales_ventas)){
            case "string": $array = json_decode($canales_ventas); break;
            case "array": $array = $canales_ventas; break;
        }
        $newArray = array();
        foreach ($array as &$canal_venta){
            $canal_venta = is_array($canal_venta)? (Object) $canal_venta : $canal_venta;
            if(is_object($canal_venta)){
                $canal_venta->id_canal_venta = !empty($canal_venta->id_canal_venta) ? $canal_venta->id_canal_venta : $canal_venta->id;
                $newArray[] = $canal_venta;
            }
            else{
                $data = new \stdClass;
                $data->id_canal_venta = $canal_venta;
                $newArray[] = $data;
            }
        }
        return $newArray;
    }
}
