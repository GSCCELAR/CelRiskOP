<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	/*
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
	*/


	$riesgo=$_GET["escenario_id"];
	$result = BD::sql_query("insert into sede_escenario (sede_id,escenario_id) values ('".$_GET["sede_id"]."','1')");


	$result = BD::sql_query("SELECT max(id) as count FROM sede_escenario");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$result = BD::sql_query("insert into sede_escenario_riesgo (sede_escenario_id,riesgo_id) values ('".$count."','".$riesgo."')");

	//print("insert into sede_escenario (sede_id,escenario_id) values ('".$_GET["sede_id"]."','1')");
	//print("insert into sede_escenario_riesgo (sede_escenario_id,riesgo_id) values ('".$count."','".$riesgo."')");
	print("ok");
	//CUANDO SE LE AGREGUE UN NUEVO ESCENARIO A ESE RIESGO SE DEBE BORRAR LOS ESCENARIOS DE NOMBRE "X" A ESE RIESGO	
	//$result = BD::sql_query("insert into sede_escenario_riesgo (sede_escenario_id,riesgo_id) values ('','0')");
	//SELECT count(*) FROM riesgo	WHERE id IN (SELECT riesgo_id FROM sede_escenario_riesgo WHERE sede_escenario_id IN ( SELECT id	FROM sede_escenario	WHERE sede_id =417))
?>