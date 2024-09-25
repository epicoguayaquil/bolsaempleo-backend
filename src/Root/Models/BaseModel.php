<?php
namespace App\Root\Models;

use Illuminate\Database\Eloquent\Model;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseModel
 *
 * @author ernesto.ruales
 */
class BaseModel extends Model{
    public $rules = [];
    public $fillable = [];

    public function convertion($request){
        $datos = $request;
        if(gettype($request) == 'string'){
            $datos = json_decode($request);
        }
        if(is_object($datos)){
            if(get_class($datos) == 'Psr\Http\Message\ServerRequestInterface' || get_class($datos) == "Request" ){
                $this->convertionArray($datos->getParams());
            }
            else{
                $this->convertionObject($datos);
            }
        }
        else{
            if(is_array($datos)){
                $this->convertionArray($datos);
            }
        }
    }
    
    public function convertionObject($obj){
        if(is_object($obj)){
            foreach ($obj as $col => &$val) {
                if (!in_array($col, $this->fillable)) {
                    continue;
                }
                // set field as null if empty
                $this->$col = $val;
            }
        }
    }
    
    public function convertionArray($inputs){
        foreach ($inputs as $col => $val) {
            // continue if the provided field isn't recognisable
            if (!in_array($col, $this->fillable)) {
                continue;
            }
            // set field as null if empty
            $this->$col = $val;
        }
    }

    
    /**
    * Set the keys for a save update query.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $query
    * @return \Illuminate\Database\Eloquent\Builder
    */
   protected function setKeysForSaveQuery($query){
       $keys = $this->getKeyName();
       if(!is_array($keys)){
           return parent::setKeysForSaveQuery($query);
       }

       foreach($keys as $keyName){
           $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
       }

       return $query;
   }

   /**
    * Get the primary key value for a save query.
    *
    * @param mixed $keyName
    * @return mixed
    */
   protected function getKeyForSaveQuery($keyName = null){
       if(is_null($keyName)){
           $keyName = $this->getKeyName();
       }

       if (isset($this->original[$keyName])) {
           return $this->original[$keyName];
       }

       return $this->getAttribute($keyName);
   }

    public static function getFullTable(){
        $instance = new static(); // Crea una instancia de la clase que extiende BaseModel
        return $instance->getConnection()->getDatabaseName() . '.' . $instance->getTable();
    }
}