<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	ob_start();
	
	$ruta =  DIR_APP . "imagenes/audio.png";
	header('Content-Type: image/png');
	if (file_exists($ruta))
		die(file_get_contents($ruta));
?>