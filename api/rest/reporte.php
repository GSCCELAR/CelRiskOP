<?php
	define("iC", true);
	require_once("../conf/config.php");

    function throwError($code, $message) {
        header("Content-type: application/json");
        die (json_encode(array('error' => array('status' => $code, 'message' => utf8_encode($message)))));
    }
    
    function returnResponse($code, $data) {
        header("Content-type: application/json");
        if (is_array($data)) {
            foreach($data as $i => $v)
                if (!is_array($v))
                    $data[$i] = utf8_encode($v);
        }
        else
            $data = utf8_encode($data);
        $result = array(
            'response' => array(
                'status' => $code,
                'message' => $data
            )
        );
        die (json_encode($result));
    }


    $db = new DbConnect;
    $dbConn = $db->connect();

    $data = isset($_POST["reporte"]) ? $_POST["reporte"] : throwError(10000, "Error al recibir el parámetro reporte");
    $data = json_decode($data, true);
    $campos_insert = $values = array();
    $campos = array("usuario_id", "fecha_sistema", "fecha_reporte", "tiporeporte_id", "descripcion", "proceso_id", "escenario_id", "riesgo_id", "control_id", "lon", "lat", "accuracy", "sede_id");
    
    //Hacer validaciones a los datos recibidos
    foreach($campos as $i) {
        $campos_insert[":$i"] = ":$i";
        $values[":$i"] = "";
        if (isset($data[$i]))
            $values[":$i"] = utf8_decode(base64_decode($data[$i]));
        if ($values[":$i"] == "-100")
            $values[":$i"] = "0";
    }

    $userID = $values[":usuario_id"];
    $values[":fecha_sistema"] = date("Y-m-d H:i:s");
    $dbConn->beginTransaction();
    
    //file_put_contents("campos.txt", "INSERT INTO reporte (" . implode(",", $campos) . ") values(" . implode(",", $campos_insert) . ")\n\n" . print_r($values, true) . "\n\n" . print_r($_POST["reporte"], true));

    if (!$dbConn->prepare("INSERT INTO reporte (" . implode(",", $campos) . ") values(" . implode(",", $campos_insert) . ")")->execute($values))
        throwError(INSERT_ERROR, "Se ha producido un error al intentar registrar el formulario");
    
    //Último registro
    $statement = $dbConn->prepare("SELECT max(id) last_id FROM reporte WHERE usuario_id=:id");
    $statement->bindParam(":id", $userID);
    $statement->execute();
    $last = $statement->fetch(PDO::FETCH_ASSOC);
    if (!is_array($last)) {
        $dbConn->rollback();
        returnResponse(DATA_NOT_FOUND, "Error al consultar el ID del reporte");
    }
    //-------------------
    //Carpetas para Cargue de archivos
    @!mkdir(RUTA_SOPORTES . $last["last_id"] . "/");
    @!mkdir(RUTA_SOPORTES . $last["last_id"] . "/imagenes/");
    @!mkdir(RUTA_SOPORTES . $last["last_id"] . "/audio/");
    
    for ($x = 0; $x < 4; $x++) {
        if (isset($_FILES["image$x"])) {
            $target_path = RUTA_SOPORTES . $last["last_id"] . "/imagenes/" . basename($_FILES["image$x"]['name']);
            if (!move_uploaded_file($_FILES["image$x"]['tmp_name'], $target_path)) {
                $dbConn->rollback();
                throwError(INSERT_ERROR, "Se ha producido un error al intentar copiar una de las imagenes image$x");
            }

            /*$source = imagecreatefromjpeg($target_path);
            $rotate = imagerotate($source, -90, 0);
            imagejpeg($rotate, $target_path);*/
        }

        if (isset($_FILES["audio$x"])) {
            $target_path = RUTA_SOPORTES . $last["last_id"] . "/audio/" . basename($_FILES["audio$x"]['name']);
            if (!move_uploaded_file($_FILES["audio$x"]['tmp_name'], $target_path)) {
                $dbConn->rollback();
                throwError(INSERT_ERROR, "Se ha producido un error al intentar copiar el archivo de audio image$x");
            }

            /*$source = imagecreatefromjpeg($target_path);
            $rotate = imagerotate($source, -90, 0);
            imagejpeg($rotate, $target_path);*/
        }
    }
    sleep(2);
    $dbConn->commit();
    returnResponse(SUCCESS_RESPONSE, "ok");
?>