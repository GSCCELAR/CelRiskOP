<?php
   
   define("iC", true);
   require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
   Aplicacion::validarAcceso(9,10);
   BD::changeInstancia("mysql");

   $sede_id = isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : die("Error al obtener el ID de la sede"); 
   $archivo = isset($_POST["nombre"]) ? $_POST["nombre"] : die("error al obtener el path del archivo");
   $path =  RUTA_IMAGENES.$sede_id."/".$archivo;
    if (is_file($path))
    {
        die(unlink($path) ? "ok" : "Error al intentar eliminar el archivo");
    }
    die("El archivo no se encontro");

?>