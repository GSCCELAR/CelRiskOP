<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	$busca_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
	
	$add_query = "";
	
	if ($busca_nombre != "")
		$add_query .= " AND nombre LIKE UPPER('%" . Seguridad::escapeSQL($busca_nombre) . "%')";
	/**
	 * Validaciones para evitar inyecci?n de c?digo SQL 
	*/
	$campos = array("id", "nombre","telefono","email");
	$orden = array("asc", "desc");
	if (!in_array($sidx, $campos))
		die("ErrorCampos"); //Modificaci?n de la petici?n: Posible intento de inyecci?n de c?digo SQL
	if (!in_array(strtolower($sord), $orden))
		die("ErrorOrden"); //Modificaci?n de la petici?n: Posible intento de inyecci?n de c?digo SQL
	//-----------Fin validaciones---------------------------
	if (!$sidx)
		$sidx = 1;
	$count = 0;
	$add_query .= " ";
	$result = BD::sql_query("select COUNT(*) AS count FROM contacto WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	
	$query = "select contacto.*, lista.nombre as tipo FROM contacto INNER JOIN lista ON contacto.contacto_tipo_id = lista.id WHERE 1=1 $add_query ORDER BY $sidx $sord";
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
			utf8_encode(htmlentities($row["nombre"], ENT_QUOTES, "iso8859-1")."<br/><small>".htmlentities($row["tipo"], ENT_QUOTES, "iso8859-1")."</small>"),
			utf8_encode($row["telefono"]),
			utf8_encode(htmlentities($row["email"], ENT_QUOTES, "iso8859-1")),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarContacto(" . $row["id"] . ")' src='imagenes/editar.png'>"
				. "<img style='margin:3px;cursor:pointer;' onclick='eliminarContacto(" . $row["id"] . ")' src='imagenes/eliminar.png'>")
		);
		$i++;
	}
	echo json_encode($responce);
?>