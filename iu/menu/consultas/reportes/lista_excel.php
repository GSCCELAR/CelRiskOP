<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php"); 
	Aplicacion::validarAcceso(9,10);
    BD::changeInstancia("mysql");


	$fileType = 'Excel2007';
	$objPHPExcel = new PHPExcel();
	$fileName = dirname(__FILE__).'/formato.xlsx';
	$objReader = PHPExcel_IOFactory::createReader($fileType);
	$objPHPExcel = $objReader->load($fileName);
	$ws = $objPHPExcel->setActiveSheetIndex(0);
	
	$fecha1 = isset($_GET["fecha1"]) ? $_GET["fecha1"] : "";
	$fecha2 = isset($_GET["fecha2"]) ? $_GET["fecha2"] : "";
	if ($fecha1 != "")
		if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha1)) die ("Error en formato de fecha1");
	if ($fecha2 != "")
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha2)) die ("Error en formato de fecha2");
    
    $cliente =  isset($_GET["cliente"]) ? $_GET["cliente"] : "";   
    $usuario =  isset($_GET["usuario"]) ? $_GET["usuario"] : "";   
    $tiporeporte =  isset($_GET["tiporeporte"]) ? $_GET["tiporeporte"] : "";   
    $proceso =  isset($_GET["proceso"]) ? $_GET["proceso"] : "";   
    $riesgo =  isset($_GET["riesgo"]) ? $_GET["riesgo"] : "";   
    $control =  isset($_GET["control"]) ? $_GET["control"] : "";   

	$add_query = "";
    if ($fecha1 != "" && $fecha2 != "") $add_query .= " AND v.fecha_reporte BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' ";
    if($cliente != "") $add_query .= " AND UPPER(v.cliente_razonsocial) LIKE UPPER('%$cliente%') ";
    if($usuario != "") $add_query .= " AND v.usuario_id = $usuario ";
    if($tiporeporte != "") $add_query .= " AND v.tiporeporte_id = $tiporeporte ";
    if($proceso != "") $add_query .= " AND v.proceso_id = $proceso ";
    if($riesgo != "") $add_query .= " AND  v.riesgo_id = $riesgo ";
    if($control != "") $add_query .= " AND v.control_id = $control ";

	if(Aplicacion::getPerfilID() == 9)
		$query = "SELECT v.id ,CONCAT(v.cliente_identificacion,CASE WHEN(v.cliente_digitoverificacion IS NOT NULL) THEN '-' END, IFNULL(v.cliente_digitoverificacion,'')) AS nit, v.cliente_razonsocial AS cliente, v.sede_nombre AS sede, v.sede_direccion AS direccion,
					v.proceso_nombre AS proceso, v.escenario_nombre AS escenario, v.riesgo_nombre AS riesgo, v.control_nombre AS 'control', v.descripcion AS descripcion, v.tiporeporte_nombre AS 'tipo', v.fecha_reporte AS 'fecha', v.usuario_nombre AS 'usuario' 
				FROM vreportes v
				WHERE zona_id in (" . implode(",",  Aplicacion::getUser()->getIDZonas()) . " )
				$add_query
				ORDER BY v.id";
	else
		$query = "SELECT v.id ,CONCAT(v.cliente_identificacion,CASE WHEN(v.cliente_digitoverificacion IS NOT NULL) THEN '-' END, IFNULL(v.cliente_digitoverificacion,'')) AS nit, v.cliente_razonsocial AS cliente, v.sede_nombre AS sede, v.sede_direccion AS direccion,
					v.proceso_nombre AS proceso, v.escenario_nombre AS escenario, v.riesgo_nombre AS riesgo, v.control_nombre AS 'control', v.descripcion AS descripcion, v.tiporeporte_nombre AS 'tipo', v.fecha_reporte AS 'fecha', v.usuario_nombre AS 'usuario' 
				FROM vreportes v
				WHERE 1=1 $add_query
				ORDER BY v.id";
    $result = BD::sql_query($query) or die("Error en query $query");
    
	//Escribimos los filtros aplicados en las celdas específicas del formato
	if($fecha1 != "" && $fecha2 != "") $ws->setCellValueByColumnAndRow(1, 2, utf8_encode($fecha1 . " hasta " . $fecha2));
	$ws->setCellValueByColumnAndRow(0, 3, utf8_encode("Reporte generado el día " . Fecha::getFecha(date("Y-m-d H:i:s"), "d/F/Y g:ia")));

	$fila = 5;
	while ($row = BD::obtenerRegistro($result)) {
		$col = 0;

        $ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["id"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["nit"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["cliente"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["sede"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["direccion"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["proceso"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["escenario"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["riesgo"]));
		$ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["control"]));
        $ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["descripcion"]));
        $ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["tipo"]));
        $ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["fecha"]));
        $ws->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["usuario"]));

		$fila++;
	}

	$ws->getStyle("A5:M" . ($fila - 1))->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		)
	);

    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="resumen.xlsx"');
	header('Cache-Control: max-age=0');
	$objWriter->save('php://output');
    unset($objPHPExcel);
	unset($writer);
?>