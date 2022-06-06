<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$id = isset($_GET["id"]) ? intval($_GET["id"]) : die("Error al obtener el ID de la sede escenario riesgo");
	
	$sede_escenario_riesgo = new SedeEscenarioRiesgo();
	$sede_escenario_riesgo->load($id) or die("Error al cargar la información");
	die ($sede_escenario_riesgo->delete() ? "ok" : "Hay datos relacionados con este item"); 
	
?>