<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$busca_sector = (isset($_GET["sector"]) && $_GET["sector"] != "") ? intval($_GET["sector"]) : -1;
	$add_query = "";
		
	if ($busca_sector >= 0)
		$add_query .= " AND v.sector_id LIKE UPPER('%" . Seguridad::escapeSQL($busca_sector) . "%')";

	$add_query .= " AND v.dispositivo_seguridad_id IS NULL";
	
	/**
	 * Validaciones para evitar inyecci?n de c?digo SQL 
	*/
	$campos = array("sector","peligros", "probabilidad", "severidad", "nivel_riesgo");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM vmriesgo_op v WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "sector DESC,";

	
	$query = "select v.*
		FROM vmriesgo_op v
		WHERE 1=1 $add_query
		ORDER BY $order_by $sidx $sord";
   
	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce->rows[$i]['id']   = $row["id"];
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminar(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$color = ($row["nivel_riesgo"] == "CR?TICO") ? "#FFFFFF":"#000000";
		$responce->rows[$i]['cell'] = array (
			utf8_encode(htmlentities($row["sector"],ENT_QUOTES,"iso8859-1")),
			utf8_encode(htmlentities($row["peligros"],ENT_QUOTES,"iso8859-1")),
			utf8_encode(htmlentities($row["probabilidad"],ENT_QUOTES,"iso8859-1")),
			utf8_encode(htmlentities($row["severidad"],ENT_QUOTES,"iso8859-1")),
			utf8_encode("<div style='background-color:".$row["nivel_riesgo_color"].";padding-top:6px;height:100%;width:100%;'><span style='font-weight:bold;font-size:12px;color: $color;'>".htmlentities($row["nivel_riesgo"],ENT_QUOTES,"iso8859-1")."</span></div>"),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editar(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>