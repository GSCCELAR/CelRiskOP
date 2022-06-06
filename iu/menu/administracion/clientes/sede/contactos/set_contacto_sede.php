<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$contacto_id = isset($_POST["contacto_id"]) ? $_POST["contacto_id"] : die("No se pudo cargar el ID del contacto");
	$sede_id = isset($_POST["sede_id"]) ? $_POST["sede_id"] : die("No se pudo cargar el ID de la sede");
	
	$sede_contacto = new SedeContacto($_POST);
	die ($sede_contacto->save() ? "ok" : "No se pudo asignar el contacto a la sede");

	
?>