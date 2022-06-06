<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	//Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$busca_usuario = (isset($_GET["usuario"])) ? $_GET["usuario"] : "";

	
	$add_query = "";
	
	if ($busca_usuario != "")
		$add_query .= " AND us.usuario_id = '" . Seguridad::escapeSQL($busca_usuario) . "'";
	
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("sede", "ciudad", "telefono");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM usuario_sede us WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "v.nombre ASC,";
	if ($sidx == "v.nombre")
		$order_by = "";
	
	$query = "select us.id, v.nombre 'sede', v.direccion, v.municipio, v.telefono
		FROM vsedes v
		JOIN usuario_sede us ON v.id = us.sede_id
		WHERE 1=1 $add_query
		GROUP BY v.id
		ORDER BY $order_by $sidx $sord";
	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		
		$responce->rows[$i]['id']   = $row["id"];
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarSede(" . $row["id"] . ")' src='imagenes/eliminar.png'>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode("<b>".htmlentities($row["sede"], ENT_QUOTES,'iso8859-1') ."</b><br/><small>". htmlentities($row["direccion"],ENT_QUOTES,'iso8859-1')."</small>"),
			utf8_encode($row["municipio"]),
			utf8_encode($row["telefono"]),
			utf8_encode($btnBorrar)
		);
		$i++;
	}
	echo json_encode($responce);
?>