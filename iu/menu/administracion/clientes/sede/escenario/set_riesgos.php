<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	if (isset($_GET["riesgo_id"]) && isset($_GET["sede_escenario_id"]) && isset($_GET["estado"]))
	{
		
		if($_GET["estado"] == "add")
		{
			$sede_escenario_riesgo = new SedeEscenarioRiesgo($_GET);
			die ($sede_escenario_riesgo->save() ? "ok" : BD::getLastError());
		}else if(isset($_GET["sede_escenario_riesgo_id"]) && $_GET["sede_escenario_riesgo_id"] != "0"){
			$sede_escenario_riesgo = new SedeEscenarioRiesgo();
			$sede_escenario_riesgo->load($_GET["sede_escenario_riesgo_id"]) or die("Error al cargar la información");
			die ($sede_escenario_riesgo->delete() ? "ok" : "Hay datos relacionados con este item"); 
		}

	}
	
?>