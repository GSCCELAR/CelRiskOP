<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9, 10);
	BD::changeInstancia("mysql");

	$cliente_id = isset($_GET["cliente"]) ? $_GET["cliente"] : -1;
	$page = isset($_GET["page"]) ? $_GET["page"] : "";
	$add_query = "";
	if ($cliente_id > -1)
			$add_query .= " AND cliente_id = $cliente_id";
							
	$count = 0;
	$limit = 10;
	
	$result = BD::sql_query("SELECT COUNT(*) AS count FROM sede  WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$query = "SELECT *
			  FROM sede
			  WHERE 1 = 1 $add_query
			  ORDER BY nombre";

	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = array();
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce[] = array(
			"id" => utf8_encode($row["id"]),
			"text" => utf8_encode($row["nombre"]) 
		);
		$i++;
	}
    $offset = ($page - 1) * $limit;
    $endCount = $offset + $limit;
    $morePages = $endCount < $count;
	$res = array(
			"results" => $responce,
			"pagination" => array(
			"more" =>  $morePages
			)
		);
	echo json_encode($res);
?>