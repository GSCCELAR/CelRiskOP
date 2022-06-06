<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	//Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	if(isset($_POST["sede_id"]) && isset($_POST["usuario_id"]))
	{
		$usuario_sede = new SedeUsuario($_POST);
		die ($usuario_sede->save() ? "ok" : BD::getLastError());
	}
	
?>