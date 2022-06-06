<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	$busca_cliente = isset($_GET["search"]) ? $_GET["search"] : "";
	$page = isset($_GET["page"]) ? $_GET["page"] : "";
	$add_query = "";
	if ($busca_cliente != "")
			$add_query .= " AND UPPER(v.nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_cliente) . "%')
							OR  UPPER(c.razon_social) LIKE UPPER('%" . Seguridad::escapeSQL($busca_cliente) . "%')";

	$count = 0;
	$limit = 7;
	
	$result = BD::sql_query("SELECT COUNT(*) AS count FROM vsedes v JOIN cliente c ON v.cliente_id = c.id WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$query = "SELECT v.id, v.nombre AS sede, c.razon_social AS cliente
			  FROM vsedes v
			  JOIN cliente c ON v.cliente_id = c.id
			  WHERE 1 = 1 $add_query
			  ORDER BY id";

	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce[] = array(
			"id" => utf8_encode($row["id"]),
			"text" => utf8_encode($row["cliente"])." - ".utf8_encode($row["sede"]) 
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