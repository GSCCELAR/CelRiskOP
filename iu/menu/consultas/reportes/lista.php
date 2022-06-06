<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");

	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$busca_cliente = isset($_GET["cliente"]) ? $_GET["cliente"] : "";
	$busca_usuario = isset($_GET["usuario"]) ? intval($_GET["usuario"]) : -1;
	$busca_tiporeporte = isset($_GET["tiporeporte"]) ? intval($_GET["tiporeporte"]) : -1;
	$busca_proceso = isset($_GET["proceso"]) ? intval($_GET["proceso"]) : -1;
	$busca_riesgo = isset($_GET["riesgo"]) ? intval($_GET["riesgo"]) : -1;
	$busca_control = isset($_GET["control"]) ? intval($_GET["control"]) : -1;

	$fecha1 = isset($_GET["fecha1"]) ? $_GET["fecha1"] : "";
	$fecha2 = isset($_GET["fecha2"]) ? $_GET["fecha2"] : "";
	
	if ($fecha1 != "")
		if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha1)) die ("Error en formato de fecha1");
	if ($fecha2 != "")
		if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha2)) die ("Error en formato de fecha2");
	$add_query = " ";
	
	if ($fecha1 != "" && $fecha2 != "")
		$add_query .= " AND v.fecha_reporte BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' ";
	
	if ($busca_cliente != "")
		$add_query .= " AND v.cliente_razonsocial LIKE '%" . Seguridad::escapeSQL($busca_cliente) . "%'";

	
	if ($busca_usuario > 0) $add_query .= " AND usuario_id=$busca_usuario ";
	if ($busca_tiporeporte > 0) $add_query .= " AND tiporeporte_id=$busca_tiporeporte ";
	if ($busca_proceso > 0) $add_query .= " AND proceso_id=$busca_proceso ";
	if ($busca_riesgo > 0) $add_query .= " AND riesgo_id=$busca_riesgo ";
	if ($busca_control > 0) $add_query .= " AND control_id=$busca_control ";
		

	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("id", "fecha_reporte", "cliente_nombre", "tiporeporte_nombre");
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
	
	$result = "";
	if(Aplicacion::getPerfilID() == 9)
		$result = BD::sql_query("SELECT COUNT(*) AS count 
			FROM vreportes v WHERE zona_id in (" . implode(",",  Aplicacion::getUser()->getIDZonas()) . ") $add_query");
	else
		$result = BD::sql_query("SELECT COUNT(*) AS count FROM vreportes v WHERE 1=1 $add_query");

	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$query = "";
	if($sidx != 1)
		$sidx ="v.$sidx" ; 

	if(Aplicacion::getPerfilID() == 9)
		$query = "SELECT v.*  FROM vreportes v WHERE zona_id in (" . implode(",", Aplicacion::getUser()->getIDZonas() ) . ") $add_query ORDER BY $sidx $sord";
	else
		$query = "SELECT v.* FROM vreportes v WHERE 1=1 $add_query ORDER BY $sidx $sord";

	
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
			utf8_encode(Fecha::getFechaCorta($row["fecha_reporte"], "d/F/Y") . "<br />" . Fecha::getFechaCorta($row["fecha_reporte"], "g:ia")),
			utf8_encode("<b>" . $row["cliente_razonsocial"] . "</b>\n" . $row["sede_nombre"] . "\n" . $row["sede_direccion"]),
			utf8_encode("<b>Tipo de reporte: </b>" . $row["tiporeporte_nombre"] . ", &nbsp; &nbsp; &nbsp; <b>Proceso:</b>: " . $row["proceso_nombre"] . "\n" .
				"<b>Riesgo: </b>" . $row["riesgo_nombre"] . "\n" .
				"<b>Control: </b>" . $row["control_nombre"] . "\n" .
				nl2br($row["descripcion"])
			),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='verF6(" . $row["id"] . ")' src='imagenes/ver.png'> &nbsp;"
			. "<img style='cursor:pointer;' onclick='verF6PDF(" . $row["id"] . ")' src='imagenes/pdf.png'>")
		);
		$i++;
	}
	echo json_encode($responce);
?>