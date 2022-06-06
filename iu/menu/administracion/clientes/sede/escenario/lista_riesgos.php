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

	$sede_escenario_id = isset($_GET["sede_escenario_id"]) ? intval($_GET["sede_escenario_id"]) : ""; 
	
	$add_query = "";
	
	if ($sede_escenario_id != "")
		$add_query .= " AND sede_escenario_riesgo.sede_escenario_id = ". Seguridad::escapeSQL($sede_escenario_id);
		
	/**
	 * Validaciones para evitar inyecci�n de c�digo SQL 
	*/
	$campos = array("nombre");
	$orden = array("asc", "desc");
	if (!in_array($sidx, $campos))
		die("ErrorCampos"); //Modificaci�n de la petici�n: Posible intento de inyecci�n de c�digo SQL
	if (!in_array(strtolower($sord), $orden))
		die("ErrorOrden"); //Modificaci�n de la petici�n: Posible intento de inyecci�n de c�digo SQL
	//-----------Fin validaciones---------------------------
	if (!$sidx)
		$sidx = 1;
	$count = 0;
	$add_query .= " ";
	$result = ($sede_escenario_id != "") ? BD::sql_query("select COUNT(*) AS count FROM sede_escenario_riesgo WHERE 1=1 $add_query") : BD::sql_query("select COUNT(*) AS count FROM riesgo WHERE 1=1 $add_query");
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

	$query = "SELECT sede_escenario_riesgo.id, riesgo.nombre
				FROM riesgo join sede_escenario_riesgo on riesgo.id = sede_escenario_riesgo.riesgo_id
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
		$responce->rows[$i]['id']   = $row["id"] ;
		$btnBorrar =  "<img style='margin:3px;cursor:pointer;'  onclick='eliminarSedeEscenarioRiesgo(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>" ;
		$responce->rows[$i]['cell'] = array (
			utf8_encode($row["nombre"]),
			utf8_encode($btnBorrar)
		);
		$i++;
	}
	echo json_encode($responce);
?>