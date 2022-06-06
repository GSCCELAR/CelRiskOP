<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	if(isset($_POST["zona_id"]) && isset($_POST["usuario_id"]))
	{
		$usuario_zona = new ZonaUsuario($_POST);
		die ($usuario_zona->save() ? "ok" : BD::getLastError());
	}
	
?>