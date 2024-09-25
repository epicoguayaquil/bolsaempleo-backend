<?php

namespace App\Root\Models;

use Exception;

class ReadOnlyBaseModel extends BaseModel{
    public function save(array $options = []){
        throw new Exception("Este modelo no se puede guardar.");
    }
}
