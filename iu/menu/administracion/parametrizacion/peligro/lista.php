<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	$busca_nombre = isset($_GET["agente_riesgo"]) ? $_GET["agente_riesgo"] : "";
	
	$add_query = "";
	
	if ($busca_nombre != "")
		$add_query .= " AND agente_riesgo LIKE UPPER('%" . Seguridad::escapeSQL($busca_nombre) . "%')";
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("id", "agente_riesgo","peligros","descripcion", "efectos_salud","tipo_riesgo");
	$orden = array("asc", "desc");
	if (!in_array($sidx, $campos))
		die("ErrorCampos"); //Modificación de la petición: Posible intento de inyección de código SQL
	if (!in_array(strtolower($sord), $orden))
		die("ErrorOrden"); //Modificación de la petición: Posible intento de inyección de código SQL
	//-----------Fin validaciones---------------------------
	if (!$sidx)
		$sidx = 1;
	$count = 0;
	$add_query .= " ";
	$result = BD::sql_query("SELECT COUNT(*) AS count FROM vmpeligro WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	
	$query = "SELECT * FROM vmpeligro WHERE 1=1 $add_query ORDER BY $sidx $sord";
	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce->rows[$i]['id']   = $row["id"];
		$responce->rows[$i]['cell'] = array (
			utf8_encode($row["id"]),
			utf8_encode(htmlentities($row["agente_riesgo"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["peligros"],ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["descripcion"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["efectos_salud"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["tipo_riesgo_nombre"], ENT_QUOTES,"iso8859-1")),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarPeligro(" . $row["id"] . ")' src='imagenes/editar.png'>"
				. "<img style='margin:3px;cursor:pointer;' onclick='eliminarPeligro(" . $row["id"] . ")' src='imagenes/eliminar.png'>")
		);
		$i++;
	}
	echo json_encode($responce);
?>