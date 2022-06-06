<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$puesto_id = isset($_GET["puesto_id"]) ? intval($_GET["puesto_id"]) : die("Error al recibir el ID del puesto");
	$sector_id = isset($_GET["sector_id"]) ? intval($_GET["sector_id"]) : die("Error al recibir el ID del sector");
	$add_query = " AND dispositivo_seguridad_id =" . Seguridad::escapeSQL($puesto_id) . " and fecha_fin IS NULL";
	
	//$add_query .= " AND v.orden_cronologico = (SELECT MAX(orden_cronologico) FROM vmriesgo WHERE numero_item = v.numero_item AND dispositivo_seguridad_id =" . Seguridad::escapeSQL($puesto_id).")";
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("id", "peligros", "probabilidad", "severidad", "nivel_riesgo", "numero_item");
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
	$result = BD::sql_query("SELECT COUNT(*) AS count FROM vmriesgo_op  WHERE estado = 1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = intval($row['count']);
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "numero_item ASC,";

	
	$query = "select *
		FROM vmriesgo_op 
		WHERE estado = 1
		$add_query
		ORDER BY $order_by $sidx $sord";
   
	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce->rows[$i]['id']   = $row["id"];
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarItem(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$color = ($row["nivel_riesgo"] == "CRÍTICO") ? "#FFFFFF":"#000000";
		$responce->rows[$i]['cell'] = array (
			utf8_encode($row["numero_item"]),
			utf8_encode(htmlentities($row["peligros"],ENT_QUOTES,"iso8859-1")),
			utf8_encode(htmlentities($row["probabilidad"],ENT_QUOTES,"iso8859-1")),
			utf8_encode(htmlentities($row["severidad"],ENT_QUOTES,"iso8859-1")),
			utf8_encode("<div style='background-color:".$row["nivel_riesgo_color"].";padding-top:6px;height:100%;width:100%;'><span style='font-weight:bold;font-size:12px;color: $color;'>".htmlentities($row["nivel_riesgo"],ENT_QUOTES,"iso8859-1")."</span></div>"),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarItem(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>