<?php
    define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$sede_id = isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : -1; 
	$sede = new Sede();
	if(!$sede->load($sede_id)) die("Error al cargar la sede");

	$fileElementName = 'file_upload_archivo';
	if(isset($_FILES[$fileElementName])){
		$pathDestino = "";
		$extensionesPermitidas = array("png","jpg","jpeg");
		$error = "";
		$msg = "";
		$archivo = "";
		$extension = "";
		
		$pathDestino = RUTA_IMAGENES . $sede_id . "/";
        $fi = new ArrayIterator(array());
        if(is_dir($pathDestino))
		    $fi = new FilesystemIterator($pathDestino);
            
		if(iterator_count($fi) >= 4)
		{
			$error = "Supera el limite de imagenes";
		}else
		{	
			if (!is_dir($pathDestino))
				@mkdir($pathDestino);
			
			if (!empty($_FILES[$fileElementName]['error'])) {
				switch($_FILES[$fileElementName]['error']) {
					case '1':
						$error = 'El archivo supera la directiva upload_max_filesize de php.ini';
						break;
					case '2':
						$error = 'El archivo excede el tamaño máximo permitido';
						break;
					case '3':
						$error = 'Se ha producido un error y no se ha importado completamente';
						break;
					case '4':
						$error = 'No se ha importado el archivo.';
						break;
					case '6':
						$error = 'Error en el directorio temporal';
						break;
					case '7':
						$error = 'Error escribiendo en el disco';
						break;
					case '8':
						$error = 'Se ha parado la transferencia del archivo.';
						break;
					case '999':
					default:
						$error = 'Código de error no disponible';
				}
			}
			
			elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
				$error = 'El archivo no se ha importado';
			}
			elseif(file_exists($pathDestino . str_replace("&", "", $_FILES[$fileElementName]['name']))) {
				$error = 'El nombre del archivo <b>"' . htmlentities( $_FILES[$fileElementName]['name'], ENT_QUOTES, "ISO8859-1") .'"</b> ya existe, por favor utilice otro nombre o cambie el nombre de la imagen en su computador.';
			}
			else {
				$extension = explode(".", $_FILES[$fileElementName]['name']);
				$extension = strtolower(end($extension));
				$archivo =  $_FILES[$fileElementName]['name'];
				if (!in_array($extension, $extensionesPermitidas))
					$error = "Extensión <font color=red><b>$extension</b></font> no está permitida<br />Extensiones permitidas: <font color=green><b>" . implode(",", $extensionesPermitidas) . "</b></font>";
				else {
					if(!move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $pathDestino . str_replace("&", "", $_FILES[$fileElementName]['name'])))
						$error = 'Error al cargar el archivo.';
					else
						$msg = "El archivo ha sido cargado correctamente";
				}
			}
		}
		
		echo "{";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "',\n";
		echo				"archivo: '" .$sede_id."/".$archivo. "'\n";
		echo "}";
	}

?>