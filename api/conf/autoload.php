<?php
	defined("iC") or die("Utilice los enlaces de la aplicación Web.");
	
	spl_autoload_register(function($class_name) {
		if (!preg_match("/[a-zA-Z]{1,}/", $class_name))
			return;
			
	    if (file_exists(BD . $class_name . '.class.php'))
	    	require_once BD . $class_name . '.class.php';
	    elseif (file_exists(MN . $class_name . '.class.php'))
	    	require_once MN . $class_name . '.class.php';
	    else
	    	die("Es posible que la clase '" . $class_name . "' no esté definida o no se encuentra implementada en el directorio correcto");
	});
?>