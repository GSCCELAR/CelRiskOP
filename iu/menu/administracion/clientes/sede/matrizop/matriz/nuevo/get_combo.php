<?php
    define("iC", true);
    require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
    Aplicacion::validarAcceso(9,10);
    BD::changeInstancia("mysql");


    $agenteriesgo_id = isset($_POST["agenteriesgo_id"]) ? intval($_POST["agenteriesgo_id"]) : -1;

    $add_query = "";
    if($agenteriesgo_id >= 0)
        $add_query .= " AND agenteriesgo_id = ".Seguridad::escapeSQL($agenteriesgo_id);

    $query = "select v.*
    FROM vmpeligro v
    WHERE 1=1 $add_query";

    $result = BD::sql_query($query) or die("Error en query");
    $responce = array();
    $i = 0;
    while ($row = BD::obtenerRegistro($result)) {
        $responce[$i] = array("id" => $row["id"],"peligros" => utf8_encode(htmlentities($row["peligros"],ENT_QUOTES,"iso8859-1")) , "consecuencia" => utf8_encode(htmlentities($row["efectos_salud"],ENT_QUOTES,"iso-8859-1")));
        $i++;
    }
    //print_r($responce);
    echo json_encode($responce);

?>