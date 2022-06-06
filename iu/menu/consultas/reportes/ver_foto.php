<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	ob_start();
	$fid = isset($_GET["fid"]) ? intval($_GET["fid"]) : die("Error al recibir el ID del reporte");
	$f = new Reporte();
	$f->load($fid) or die("Error al cargar el modelo $fid");
	$nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : die("Error al recibir el NOMBRE del registro");
	$nombre = basename($nombre);
	$ruta = RUTA_SOPORTES . $f->id . "/imagenes/" . $nombre;
	$ruta_thumb = RUTA_SOPORTES . $f->id . "/imagenes/thumb/" . $nombre;
	header('Content-Type: image/jpeg');
	if (file_exists($ruta))
		die(file_get_contents($ruta));
?>