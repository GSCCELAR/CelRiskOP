<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");

	$cliente_id = isset($_GET["cliente_id"]) ? intval($_GET["cliente_id"]) : ""; 
	$busca_sede = isset($_GET["sede"]) ? $_GET["sede"] : "";
	
	$add_query = "";
	
	if ($busca_sede != "")
		$add_query .= " AND UPPER(nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_sede) . "%')";

	if ($cliente_id != "")
		$add_query .= " AND cliente_id = ". Seguridad::escapeSQL($cliente_id);
		
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("nombre", "direccion", "telefono");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM vsedes WHERE 1=1 $add_query AND cliente_id = ".$cliente_id);
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "nombre DESC,";
	if ($sidx == "nombre")
		$order_by = "";
	
	$query = "select *
		FROM vsedes 
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
		$responce->rows[$i]['id']   = $row["id"] . "@" . str_replace(",",".", $row["latitud"]) . "#" . str_replace(",",".", $row["longitud"]);
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarSede(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode("<b>" . $row["nombre"] . "</b><br /><small>" . $row["direccion"]." - ". $row["municipio"]."</small>"),
			utf8_encode("<small>".$row["telefono"]."</small>"),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarSede(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$responce->data[] = array(str_replace(",",".", $row["latitud"]), str_replace(",",".", $row["longitud"]), htmlentities($row["nombre"], ENT_QUOTES, "ISO8859-1"));
		$i++;
	}
	echo json_encode($responce);
?>