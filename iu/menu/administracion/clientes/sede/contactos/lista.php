<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");

	$sede_id = isset($_GET["sede_id"]) ? intval($_GET["sede_id"]) : ""; 
	$busca_contacto = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
	
	$add_query = "";
	
	if ($busca_contacto != "")
		$add_query .= " AND UPPER(contacto.nombre) LIKE UPPER('%" . Seguridad::escapeSQL($busca_contacto) . "%')";

	if ($sede_id != "")
		$add_query .= " AND sede_contacto.sede_id = ". Seguridad::escapeSQL($sede_id);
		
	/**
	 * Validaciones para evitar inyecci�n de c�digo SQL 
	*/
	$campos = array("nombre", "telefono", "email",);
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
	$result = BD::sql_query("select COUNT(*) AS count FROM sede_contacto WHERE 1=1 $add_query");
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
	
	$query = "select sede_contacto.id,contacto.id as contacto_id, contacto.nombre, contacto.telefono, contacto.email, lista.nombre as tipo
		FROM contacto 
		JOIN sede_contacto ON contacto.id = sede_contacto.contacto_id
		JOIN lista ON contacto.contacto_tipo_id = lista.id
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
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminarContacto(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		$responce->rows[$i]['cell'] = array (
			utf8_encode("<b>".$row["nombre"]."</b>"."<br/><small>".$row["tipo"]."</small>"),
			utf8_encode($row["telefono"]),
			utf8_encode($row["email"]),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editarContacto(" . $row["contacto_id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>