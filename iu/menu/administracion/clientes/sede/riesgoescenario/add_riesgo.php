<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$data;
	if(isset($_GET["nombre"]) && $_GET["nombre"] != null)
	{
		$riesgo = new Riesgo($_GET);
		if($riesgo->save())
		{   
			$data =
			array (
				"id" => utf8_encode($riesgo->id),
				"nombre" => utf8_encode($riesgo->getCampo("nombre"))
			);
		}
		echo json_encode($data);
	}
	
?>