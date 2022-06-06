<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php"); 
	//define("MODO_DEBUG", true);
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");

	$sede_id = isset($_GET["id"]) ? intval($_GET["id"]) :die("Error al obtener el ID de la sede"); 
	$busca_escenario = isset($_GET["escenario"]) ? $_GET["escenario"] : "";
	
	$add_query = "";
	
	if ($busca_escenario != "")
		$add_query .= " AND UPPER(escenario.nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_escenario) . "%')";

	if ($sede_id != "")
		$add_query .= " AND sede_escenario.sede_id = ". Seguridad::escapeSQL($sede_id);
		
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("nombre", "cantidad_riesgos");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM sede_escenario WHERE 1=1 $add_query");
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
	
	$query = "select sede_escenario.id, escenario.nombre, count(sede_escenario_riesgo.riesgo_id) cantidad_riesgos
		FROM escenario join sede_escenario on escenario.id = sede_escenario.escenario_id 
		left outer join sede_escenario_riesgo on sede_escenario.id = sede_escenario_riesgo.sede_escenario_id 
		WHERE 1=1 $add_query
		GROUP BY sede_escenario.id
		ORDER BY $order_by $sidx $sord";

	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query");
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$responce->data = array();
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {
		$responce->rows[$i]['id']   = $row["id"] ;
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarEscenario(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar Escenario'>";
		$btnAdicionar = "<img style='margin:3px;cursor:pointer;'  onclick='adicionarRiesgo(" . $row["id"] . ",\"".htmlentities($row["nombre"], ENT_QUOTES, "iso8859-1")."\")' src='imagenes/add.png' title='Adicionar Riesgo'>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode($row["nombre"]),
			utf8_encode($row["cantidad_riesgos"]),
			utf8_encode($btnAdicionar.$btnBorrar)
		);
		$i++;
	}

	echo json_encode($responce);
?>