<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$busca_zona = isset($_GET["search"]) ? $_GET["search"] : "";
	$page = isset($_GET["page"]) ? $_GET["page"] : "";
	$add_query = "";
	if ($busca_zona != "")
			$add_query .= " AND UPPER(nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_zona) . "%')";

	$count = 0;
	$limit = 7;
	
	$result = BD::sql_query("SELECT COUNT(*) AS count FROM lista   WHERE tipo_lista_id = 9 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$query = "SELECT id, nombre AS zona
			  FROM lista 
			  WHERE tipo_lista_id = 9 $add_query
			  ORDER BY id";

	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce[] = array(
			"id" => utf8_encode($row["id"]),
			"text" => utf8_encode($row["zona"]) 
		);
		$i++;
	}
	$resultCount = 7;
    $offset = ($page - 1) * $resultCount;
    $endCount = $offset + $resultCount;
    $morePages = $endCount < $count;
	$res = array(
			"results" => $responce,
			"pagination" => array(
			"more" =>  $morePages
			)
		);
	echo json_encode($res);
?>