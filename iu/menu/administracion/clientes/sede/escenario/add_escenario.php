<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$data;
	if(isset($_GET["nombre"]))
	{
		$escenario = new Escenario($_GET);
		if($escenario->save())
		{   
			$data =
			array (
				"id" => utf8_encode($escenario->id),
				"nombre" => utf8_encode($escenario->getCampo("nombre"))
			);
		}
		echo json_encode($data);
	}
	
?>