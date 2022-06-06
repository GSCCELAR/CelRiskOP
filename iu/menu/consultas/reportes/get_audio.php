<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	ob_start();
	$fid = isset($_GET["fid"]) ? intval($_GET["fid"]) : die("Error al recibir el ID del reporte");
	$f = new Reporte();
	$f->load($fid) or die("Error al cargar el modelo $fid");
	$nombre = "audio.mp4";
	$ruta = RUTA_SOPORTES . $f->id . "/audio/audio.mp4";
	header('Content-Type: application/octet-stream');
	if (file_exists($ruta))
		die(file_get_contents($ruta, FILE_USE_INCLUDE_PATH));
?>