<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$cliente_id = isset($_GET["cliente_id"]) ? intval($_GET["cliente_id"]) : "";
	
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("id","nombre", "municipio","departamento","pais");
	$orden = array("asc", "desc");
	if (!in_array($sidx, $campos))
		die("ErrorCampos"); //Modificación de la petición: Posible intento de inyección de código SQL
	if (!in_array(strtolower($sord), $orden))
		die("ErrorOrden"); //Modificación de la petición: Posible intento de inyección de código SQL
	//-----------Fin validaciones---------------------------
	if (!$sidx)
		$sidx = 1;
	$count = 0;
	$result = BD::sql_query("select COUNT(*) AS count FROM vsedes WHERE cliente_id=$cliente_id");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages)
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$query = "select * FROM vsedes WHERE cliente_id=$cliente_id ORDER BY $sidx $sord";
	//print($query);
	//define("MODO_DEBUG",true);
	$result = BD::sql_query_limit($query, $limit, $start) or die("Error en query" . BD::getLastError());
	$responce = new ObjetoJSON();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	
	while ($row = BD::obtenerRegistro($result)) {

		$botones = "<img style='margin:3px;cursor:pointer;' onclick='editarSede(" . $row["id"] . ")' src='imagenes/editar.png'>"
			. "<img style='margin:3px;cursor:pointer;' onclick='eliminarSede(" . $row["id"] . ")' src='imagenes/eliminar.png'>";

		$responce->rows[$i]['id']   = $row["id"];
		$responce->rows[$i]['cell'] = array (
			//utf8_encode($row["id"]),
			utf8_encode(htmlentities($row["nombre"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["direccion"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["telefono"], ENT_QUOTES, "iso8859-1")),
			utf8_encode(htmlentities($row["municipio"]." (".$row["departamento"].")", ENT_QUOTES, "iso8859-1"))
		);
		$i++;
	}
	echo json_encode($responce);
?>