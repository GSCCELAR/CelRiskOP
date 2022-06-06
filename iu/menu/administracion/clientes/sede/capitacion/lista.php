<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");

	$sede_id = isset($_GET["sede_id"]) ? intval($_GET["sede_id"]) : ""; 
	
	$add_query = "";
	

	if ($sede_id != "")
		$add_query .= " AND capitacion.sede_id = ". Seguridad::escapeSQL($sede_id);
		
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("tipo_persona","cantidad");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM capitacion WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "tipo_persona DESC,";
	if ($sidx == "tipo_persona")
		$order_by = "";
	
	$query = "select capitacion.*, lista.nombre AS tipo_persona
		FROM capitacion 
		JOIN lista ON capitacion.tipopersona_id = lista.id
		WHERE 1=1 $add_query 
		ORDER BY $order_by $sidx $sord";

	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$responce->data = array();
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce->rows[$i]['id']   = $row["id"];
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarCapitacion(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode(htmlentities($row["tipo_persona"], ENT_QUOTES, "iso8859-1")),
			utf8_encode($row["cantidad"]),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarCapitacion(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>