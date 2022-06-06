<?php
	define("iC", true);
	//define("DEBUG",true);
	require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9, 10);
	BD::changeInstancia("mysql");


	ob_start();
	$item = isset($_GET["item_pos"]) ? intval($_GET["item_pos"]) : die("Error al recibir el item ");
	$puesto_id = isset($_GET["fid"]) ? intval($_GET["fid"]) : die("Error al recibir el ID ");
	
	
	$nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : die("Error al recibir el NOMBRE del modelo");
	$nombre = basename($nombre);
	$ruta = ""; $ruta_thumb = "";
	if(is_file(RUTA_TEMPORAL . $puesto_id . "/" . $item . "/" . $nombre)){
		$ruta = RUTA_TEMPORAL . $puesto_id . "/" . $item . "/". $nombre;
		$ruta_thumb = RUTA_TEMPORAL . $puesto_id . "/" . $item . "/thumb/" . $nombre;
	}
	else{
		$ruta = RUTA_IMAGENES . $puesto_id . "/" . $item . "/" . $nombre;
		$ruta_thumb = RUTA_IMAGENES . $puesto_id . "/" . $item . "/thumb/" . $nombre;
	}


	
	header('Content-Type: image/jpeg');
	//if (file_exists($ruta_thumb))
	if (file_exists($ruta))
		die(file_get_contents($ruta));
		//die(file_get_contents($ruta_thumb));

	$porcentaje = 0.4;
	list($ancho, $alto) = getimagesize($ruta);
	$nuevo_ancho = $ancho * $porcentaje;
	$nuevo_alto = $alto * $porcentaje;

	$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
	$origen = imagecreatefromjpeg($ruta);
	imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

	imagejpeg($thumb);
	$data = ob_get_contents();
	@mkdir(dirname($ruta_thumb), 0777, true);
	file_put_contents($ruta_thumb, $data);
?>