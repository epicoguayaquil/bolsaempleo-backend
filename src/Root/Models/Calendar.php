<?php

namespace App\Root\Models;

class Calendar
{
    public $start = null;
    public $end = null;
    public $asunto = null;
    public $description = null;
    public $location = null;
    public $organizador;
    public $invitados = array();
    public $fileName;

    public function __construct() {
        $this->organizador = new Invitado();
    }
}
