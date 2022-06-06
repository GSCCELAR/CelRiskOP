<?php
    define("iC", true);
    require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
    Aplicacion::validarAcceso(9,10);
    BD::changeInstancia("mysql");


    $probabilidad = isset($_POST["probabilidad"]) ? intval($_POST["probabilidad"]) : -1;
    $severidad = isset($_POST["severidad"]) ? intval($_POST["severidad"]) : -1;

    $add_query = "";
    if($probabilidad >= 0)
        $add_query .= " AND mprobabilidad_id = ".Seguridad::escapeSQL($probabilidad);
    if($severidad >= 0)
        $add_query .= " AND mseveridad_id = ".Seguridad::escapeSQL($severidad);

    $query = "select n.calificacion, n.color
    FROM mnivel_riesgo n, mclasificacion_riesgo c
    WHERE n.id = c.mnivel_riesgo_id $add_query";

    $result = BD::sql_query($query) or die("Error en query");
    $html = "";
    if($row = BD::obtenerRegistro($result)) {
        $color = ($row["calificacion"] == "CRÍTICO") ? "#FFFFFF":"#000000";
        $html = "<div style='background-color:".$row["color"].";padding:5px;height:100%;width:210px;text-align:center;'><span style='color:$color;font-weight:bold;'>".$row["calificacion"]."</span></div>";
    }
    echo $html;

?>