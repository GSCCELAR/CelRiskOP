<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	if(isset($_GET["escenario_id"]) && isset($_GET["sede_id"]))
	{
		$sede_escenario = new SedeEscenario($_GET);
		die ($sede_escenario->save() ? "ok" : BD::getLastError());
	}
	
?>