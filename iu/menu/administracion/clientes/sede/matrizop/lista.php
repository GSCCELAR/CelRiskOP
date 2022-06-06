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
		$add_query .= " and descripcion like '%riesgos operativos%' AND sede_id = ". Seguridad::escapeSQL($sede_id);
		
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("descripcion");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM dispositivo_seguridad WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "descripcion DESC,";
	if ($sidx == "descripcion")
		$order_by = "";
	
	$query = "select *
		FROM dispositivo_seguridad 
		
		WHERE 1=1 $add_query 
		and descripcion like '%riesgos operativos%'
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
		$btnImagenes = "<img style='margin:3px;cursor:pointer;width:22px;height:22px;' onclick='cargarImagenes(".$row["id"].")' src='imagenes/menu/imagenes.png' title='Cargar Imagenes'>";
		$btnMatriz = "<img style='margin:3px;cursor:pointer;width:22px;height:22px;' onclick='verMatriz(".$row["id"].")' src='imagenes/menu/matriz.png' title='Ver Matriz'>";
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarDispositivo(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$btnCopiar = "<img style='margin:3px;cursor:pointer' onclick='copiarMatriz(".$row["id"].")' src='imagenes/copiar.png' title='Copiar Matriz'>";
		$color_descripcion = ( $row["estado"] == 0) ? "<span style='color:#e83610'>".htmlentities($row["descripcion"], ENT_QUOTES, "iso8859-1")." (INACTIVO)</span>" : "<span style='color:#000000'>".htmlentities($row["descripcion"], ENT_QUOTES, "iso8859-1")."</span>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode($color_descripcion),
			utf8_encode("$btnImagenes$btnMatriz$btnCopiar<img style='margin:3px;cursor:pointer;' onclick='editarDispositivo(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>