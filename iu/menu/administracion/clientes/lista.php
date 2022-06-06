<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$page = isset($_POST['page']) ? intval($_POST['page']) : die(""); 
	$limit = isset($_POST['rows']) ? intval($_POST["rows"]) : die("");
	$sidx = isset($_POST['sidx']) ? $_POST["sidx"] : 1; //VALIDAR
	$sord = isset($_POST['sord']) ? $_POST["sord"] : die("");
	
	$busca_estado = (isset($_GET["estado"]) && $_GET["estado"] != "") ? intval($_GET["estado"]) : -1;
	$busca_cliente = isset($_GET["cliente"]) ? $_GET["cliente"] : "";
	$busca_identificacion = isset($_GET["identificacion"]) ? $_GET["identificacion"] : "";
	$busca_sector = (isset($_GET["sector"]) && $_GET["sector"] != "") ? intval($_GET["sector"]) : -1;
	
	$add_query = "";
	
	if ($busca_cliente != "")
		$add_query .= " AND v.razon_social LIKE UPPER('%" . Seguridad::escapeSQL($busca_cliente) . "%')";
		
	if ($busca_identificacion != "")
	{
		if(strpos($busca_identificacion,"-"))
		{
			$array = explode("-", $busca_identificacion);
			$add_query .= " AND v.identificacion LIKE UPPER('%" . Seguridad::escapeSQL($array[0]) . "%') AND v.digito_verificacion LIKE UPPER('%" . Seguridad::escapeSQL($array[1]) . "%')";
		}else
		{
			$add_query .= " AND v.identificacion LIKE UPPER('%" . Seguridad::escapeSQL($busca_identificacion) . "%')";
		}	
	}
		
	if ($busca_sector >= 0)
		$add_query .= " AND v.sector_id = '" .Seguridad::escapeSQL($busca_sector)."'";
	
	if ($busca_estado >= 0)
		$add_query .= " AND v.estado = '" . Seguridad::escapeSQL($busca_estado) . "'";
	
	/**
	 * Validaciones para evitar inyección de código SQL 
	*/
	$campos = array("id", "razon_social", "identificacion");
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
	$result = BD::sql_query("select COUNT(*) AS count FROM vclientes WHERE 1=1 $add_query");
	if ($row = BD::obtenerRegistro($result))
		$count = $row['count'];
	$total_pages =  ($count > 0) ? ceil($count/$limit) : 0;
	if ($page > $total_pages) 
		$page = $total_pages; 
	$start = $limit * $page - $limit;
	if ($start < 0)
		$start = 0;
	$order_by = "ESTADO DESC,";
	if ($sidx == "estado")
		$order_by = "";
	
	$query = "select v.*
		FROM vclientes v
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
		$estado = ($row["estado"] == 1)
				? "<span style='width:85%;cursor:pointer;' class='label label-success' onclick='cambiarEstado(".$row["id"].",".$row["estado"].")'>ACTIVO</span>" 
				: "<span style='width:85%;cursor:pointer;' class='label label-default' onclick='cambiarEstado(".$row["id"].",".$row["estado"].")'>INACTIVO</span>";
		$btnBorrar = "<img style='margin:3px;cursor:pointer;'  onclick='eliminar(" . $row["id"] . ")' src='imagenes/eliminar.png' title='Eliminar'>";
		//$btnInactivar = "<img style='margin:3px;cursor:pointer;' onclick='Inactivar(".$row["id"].")' src='imagenes/user_off.png' title='Activar/Inactivar'>";
		$identificacion = isset($row["digito_verificacion"]) ? $row["identificacion"]."-".$row["digito_verificacion"] : $row["identificacion"];
		$responce->rows[$i]['cell'] = array (
			utf8_encode($estado),
			utf8_encode($row["razon_social"]),
			utf8_encode($identificacion),
			utf8_encode($row["sector_nombre"]),
			utf8_encode("<img style='margin:3px;cursor:pointer;' onclick='editar(" . $row["id"] . ")' src='imagenes/editar.png' title='Editar'>$btnBorrar")
		);
		$i++;
	}
	echo json_encode($responce);
?>