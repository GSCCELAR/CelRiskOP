<?php
	error_reporting(E_ALL|E_STRICT);
	defined("iC") or die("Utilice los enlaces de la aplicaciï¿½n Web");
	
	define("DIR_WEB", str_replace(" ", "%20", htmlentities(dirname($_SERVER['PHP_SELF']) . "/")));
	define("DIR_APP", dirname(__FILE__) . "/../");
	header("Content-type: text/html; charset=iso-8859-1");	//Codificacion ISO 8859-1
	date_default_timezone_set("America/Bogota");
	$_CONFIG["APP"]["titulo"] = "Formularois";

	$_CONFIG["BD"]["dbms"] = "mysql";
	
	$_CONFIG["BD"]["user"] = "usr_datos";
	$_CONFIG["BD"]["pass"] = "usr_datos";
	$_CONFIG["BD"]["host"] = "127.0.0.1";
	$_CONFIG["BD"]["base"] = "celrisk";
	
	/**
	 * Constantes para directorios de la aplicacion
	 */
	define("BD", dirname(__FILE__) . "/../clases/bd/");
	define("MN", dirname(__FILE__) . "/../clases/mn/");
	define("IU", dirname(__FILE__) . "/../iu/");
	define("RUTA_SOPORTES", dirname(__FILE__) . "/../../soportes/reportes/");
	
	/**
	 * Constantes para la aplicación
	 */
	define('SECRET_KEY', '9F$sa/?83');
	define('RUTA_AVATAR', dirname(__FILE__) . '/../avatar/');
	
	define('BOOLEAN', 1);
	define('INTEGER', 2);
	define('STRING', 3);
	
	/**
	 * Códigos de error
	 */ 
	define('REQUEST_METHOD_NOT_VALID', 		100);
	define('REQUEST_CONTENTTYPE_NOT_VALID', 101);
	define('REQUEST_NOT_VALID', 			102);
	define('VALIDATE_PARAMETER_REQUIRED', 	103);
	define('VALIDATE_PARAMETER_DATATYPE', 	104);
	define('API_NAME_REQUIRED', 			105);
	define('API_PARAM_REQUIRED', 			106);
	define('API_DOES_NOT_EXIST', 			107);
	define('INVALID_USER_PASS', 			108);
	define('USER_NOT_ACTIVE', 				109);
	define('DATA_NOT_FOUND', 				110);
	define('UPDATE_ERROR',					111);
	define('INSERT_ERROR',					112);
	define('USER_NOT_ALLOWED',			    113);
	
	define('SUCCESS_RESPONSE', 				200);
	
	define('DB_ERROR', 						500);
	
	define('MAX_TIME_SESSION', 				43200);	//3 horas
	define('DOMINIO_CELRISK', 				"http://intranet.celar.com.co/celrisk");
	
	/**
	 * Errores del servidor
	 */ 
	define('AUTHORIZATION_HEADER_NOT_FOUND',300);
	define('ACCESS_TOKEN_ERRORS',			301);
	
	define('JWT_PROCESSING_ERROR', 			350);
	define('JWT_SESSION_EXPIRED', 			351);
	
	
	require_once(dirname(__FILE__) . "/autoload.php");
	
	/**
	 * utf-8 a iso-8859-1
	 */
	if (!isset($_GET["noConvertir"])) {
		foreach($_POST as $i => $d)
			if (!is_array($d))
				$_POST[$i] = utf8_decode($_POST[$i]);
		foreach($_REQUEST as $i => $d)
			if (!is_array($d))
				$_REQUEST[$i] = utf8_decode($_REQUEST[$i]);
		foreach($_GET as $i => $d)
			if (!is_array($d))
				$_GET[$i] = utf8_decode($_GET[$i]);
		foreach($_COOKIE as $i => $d)
			if (!is_array($d))
				$_COOKIE[$i] = utf8_decode($_COOKIE[$i]);
	}
?>