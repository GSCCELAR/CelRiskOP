<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	if (isset($_GET["riesgo_id"]) && isset($_GET["sede_escenario_id"]))
	{
		$sede_escenario_riesgo = new SedeEscenarioRiesgo($_GET);
		die ($sede_escenario_riesgo->save() ? "ok" : BD::getLastError());
	}
	
?>