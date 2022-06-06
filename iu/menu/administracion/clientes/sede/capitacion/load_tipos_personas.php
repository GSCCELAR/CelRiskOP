<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	$busca_tipo_persona = isset($_GET["search"]) ? $_GET["search"] : "";
	$add_query = "";
	if ($busca_tipo_persona != "")
		$add_query .= " AND UPPER(nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_tipo_persona) . "%')";

	$query = "select *
			  FROM lista
			  WHERE tipo_lista_id = 2 $add_query
			  ORDER BY nombre";

	$result = BD::sql_query($query) or die("Error en query");
	$responce;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce[] = array(
			"id" => utf8_encode($row["id"]),
			"text" => utf8_encode($row["nombre"]));
		$i++;
	}
	echo json_encode($responce);
?>