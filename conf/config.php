<?php
	error_reporting(E_ALL|E_STRICT);
	defined("iC") or die("Utilice los enlaces de la aplicación Web");
	define("DIR_WEB", str_replace(" ", "%20", htmlentities(dirname($_SERVER['PHP_SELF']) . "/")));
	define("DIR_APP", dirname(__FILE__) . "/../");
	header("Content-type: text/html; charset=iso-8859-1");
	date_default_timezone_set("America/Bogota");
	
	$NOM_SESION = "app_CELRISK";
	$_CONFIG["BD"]["mysql"] = array("mysql", "usr_datos", "usr_datos", "127.0.0.1", "celrisk");

	define("NOMBRE_APP", "CELRISK");
	
	define("BD", dirname(__FILE__) . "/../clases/bd/");
	define("MN", dirname(__FILE__) . "/../clases/mn/");
	define("IU", dirname(__FILE__) . "/../iu/");
	define("RUTA_SOPORTES", dirname(__FILE__) . "/../soportes/reportes/");
	define("RUTA_IMAGENES", dirname(__FILE__) . "/../soportes/imagenes/");
	
	require_once(dirname(__FILE__) . "/autoload.php");

	if (!isset($_GET["noConvertir"])) {
		foreach($_POST as $i => $d) if (!is_array($d)) $_POST[$i] = utf8_decode($_POST[$i]);
		foreach($_REQUEST as $i => $d) if (!is_array($d)) $_REQUEST[$i] = utf8_decode($_REQUEST[$i]);
		foreach($_GET as $i => $d) if (!is_array($d)) $_GET[$i] = utf8_decode($_GET[$i]);
		foreach($_COOKIE as $i => $d) if (!is_array($d)) $_COOKIE[$i] = utf8_decode($_COOKIE[$i]);
	}
	
	Aplicacion::iniciarSesion();
?>