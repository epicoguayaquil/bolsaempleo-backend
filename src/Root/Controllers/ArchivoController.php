<?php

namespace App\Root\Controllers;

use App\Root\Validators\BaseMessage;
use Exception;
use Throwable;

class ArchivoController extends BaseMessage {
    private $nombreArchivo = null;
    private $path = null;
    private $container;
    
    public function __construct(\Psr\Container\ContainerInterface $container) {
       $this->container = $container;
    }
    
    public function guardarArchivo($fileName, $nombre):bool {
        $this->reset();
        return $this->_guardarArchivo($fileName, $nombre);
    }
    
    public function getUrl($nombre = "RUTA_ARCHIVO"):string {
        $param = \App\Root\Models\SystemParamBD::where('nombre',$nombre)->first();
        if(empty($param)){
            $this->addError('URL', "No se encuentra configurado la ruta del archivo");
            $this->container->get('logger')->error("ArchivoController::getUrl Error: No se encuentra configurado la ruta del archivo en el parametro " . $nombre);
            return null;
        }
        $this->path = $param->valor;
        return $this->path;
    }
    
    public function guardarArchivoBase64($baseImagen, $nameImage, $path = null, $ext="png"):bool {
        $resp = false;
        switch (strtolower($ext)){
            case 'png': $resp = $this->guardarImagenBase64($baseImagen, $nameImage, $path, $ext);break;
            case 'jpg': $resp =  $this->guardarImagenBase64($baseImagen, $nameImage, $path, $ext);break;
            case 'jpeg':$resp = $this->guardarImagenBase64($baseImagen, $nameImage, $path, $ext);break;
            default: 
                $resp = $this->_guardarArchivosBase64($baseImagen, $nameImage, $path, $ext);break;
        }
        return $resp;
    }
    
    public function guardarImagenBase64($baseImagen, $nameImage, $path = null, $ext="png"){
        try {
            $this->reset();
            $url = !empty($path) ? $path : $this->getUrl();
            if(!file_exists($url)){
            	$this->container->get('logger')->error("ArchivoController::guardarImagenBase64 Error: No se encuentra la ruta " . $url);
                return false;
            }
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $baseImagen));
            $im = imageCreateFromString($data);
            $this->path = $url;
            $this->nombreArchivo = $nameImage.'.'.$ext;
            imagepng($im, $url .'/'. $this->nombreArchivo, 9);
            if (!$im) {
                $this->addError('IMGbase64', "No se pudo grabar la imagen");
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->addError('IMGbase64', "Error al grabar la imagen");
            $this->container->get('logger')->error("ArchivoController::guardarImagenBase64 Error: " . $e);
        } catch (Throwable $e) {
            $this->addError('IMGbase64', "Error al grabar la imagen");
            $this->container->get('logger')->error("ArchivoController::guardarImagenBase64 Error: " . $e);
        }
        return false;
    }
    
    public function _guardarArchivosBase64($base64, $nameImage, $path = null, $ext="pdf"){
        //$data = base64_decode($this->Get(self::Body));
        try {
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $url = !empty($path) ? $path : $this->getUrl();
            if(!file_exists($url)){
                $this->addError('IMGbase64', "No se encuentra la ruta " . $url);
            	$this->container->get('logger')->error("ArchivoController::_guardarArchivosBase64 Error: No se encuentra la ruta " . $url);
                return false;
            }
            $this->nombreArchivo = $nameImage.'.'.$ext;
            file_put_contents($url .'/'. $this->nombreArchivo,$data);
            return true;
        } catch (Exception $e) {
            $this->addError('Archivobase64', "Error al grabar la imagen");
            $this->container->get('logger')->error("ArchivoController::_guardarArchivosBase64 Error: " . $e);
        } catch (Throwable $e) {
            $this->addError('Archivobase64', "Error al grabar la imagen");
            $this->container->get('logger')->error("ArchivoController::_guardarArchivosBase64 Error: " . $e);
        }
        return false;
    }
    
    public function grabarFILE($nombreParametro, $nombreArchivoDestino):bool{
        $this->reset();
        if (isset($_FILES[$nombreParametro])) {
            $file = $_FILES[$nombreParametro];
            if (!is_null($file)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $this->nombreArchivo = "$nombreArchivoDestino.$ext";
                return $this->_guardarArchivo($file['tmp_name'], $nombreArchivoDestino);
            }
        }
        $this->addError($nombreParametro, 'No existe archivo');
        return false;
    }
    
    public function saveICS(\App\Root\Models\Calendar $datos) {
        if(!file_exists($this->getUrl() . 'ics/')){
            $this->container->get('logger')->error("ArchivoController::saveICS Error: No se encuentra la ruta " . $this->getUrl() . "ics/");
            return null;
        }
        $name = $this->getUrl() . 'ics/' . $datos->fileName;
        $invitados = "";
        foreach ($datos->invitados as $invitado) {
            $invitados .= "\nATTENDEE;RSVP=FALSE;CN=" . $invitado->name . ":mailto:" . $invitado->email;
        }
        $data = "BEGIN:VCALENDAR\nVERSION:2.0\nCALSCALE:GREGORIAN\nPRODID:adamgibbons/ics\nMETHOD:PUBLISH\nX-PUBLISHED-TTL:PT1H\nBEGIN:VEVENT\nUID:".
                        "\nSUMMARY:".$datos->asunto.
                        "\nDTSTAMP:".date("Ymd\THis", strtotime($datos->end)).
                        "\nDTSTART:".date("Ymd\THis", strtotime($datos->start)).
                        "\nLOCATION:".$datos->location.
                        "\nDESCRIPTION:".$datos->description.
                        "\nSTATUS:CONFIRMED".
                        "\nORGANIZER;CN=".$datos->organizador->name.":mailto:".$datos->organizador->email.
                        $invitados.
                        "\nDURATION:PT1H".
                        "\nBEGIN:VALARM".
                        "\nTRIGGER:-PT10080M".
                        "\nACTION:DISPLAY".
                        "\nDESCRIPTION:Reminder".
                        "\nEND:VALARM".
                        "\nEND:VEVENT".
                        "\nEND:VCALENDAR";
        file_put_contents($name . ".ics", $data);
        return $name . ".ics";
    }
    
    private function _guardarArchivo($fileName, $nombre):bool {
        $url = $this->getUrl();
        if(is_null($url)){
            return false;
        }
        if (move_uploaded_file($fileName, $url . $nombre)) {
            return true;
        } 
        else {
            $this->addError('archivo', "Ha ocurrido un error, trate de nuevo!" . $url);
            return false;
        }
    }
    
    private function reset(){
        $this->nombreArchivo = null;
        $this->path = null;
    }
    
    function getNombreArchivo() {
        return $this->nombreArchivo;
    }

    function getPath() {
        return $this->path;
    }

}

