<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	BD::changeInstancia("mysql");

	$fileType = 'Excel2007';
	$objPHPExcel = new PHPExcel();
	$fileName = dirname(__FILE__).'/formato.xlsx';
	$objReader = PHPExcel_IOFactory::createReader($fileType);
	$objPHPExcel = $objReader->load($fileName);
	$wmatriz = $objPHPExcel->setActiveSheetIndex(0);

	$fecha_inicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : die("No se cargo la fecha inicio");
	$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : die("No se cargo la fecha fin");
	$puesto_id = (isset($_GET["puesto_id"])) ? intval($_GET["puesto_id"]) : die("Error al recibir el ID del puesto");
	$historico = (isset($_GET["items"])) ? explode(",", $_GET["items"]) : array();
	//$fecha_elaboracion = $fecha_inicio == "" ? "" : PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($fecha_inicio,"Y-m-d H:i:s")));
	$fecha_elaboracion = PHPExcel_Shared_Date::PHPToExcel(new DateTime(date("Y-m-d H:i:s")));
	$fecha_ultima_actualizacion = $fecha_fin == "" ? "" : PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($fecha_fin,"Y-m-d H:i:s")));
	$items = array();
	$cliente = "matriz_riesgo";
	foreach($historico as $i) {
		if (is_numeric($i))
			$items[] = intval($i);
	}

	$puesto = new DispositivoSeguridad();
	if (!$puesto->load($puesto_id)) die ("error al cargar la información del dispositivo");

	$add_query = " ";

	$wmatriz->setCellValue("A1", utf8_encode("Reporte generado el " . Fecha::getFechaCorta(date("Y-m-d H:i:s"), "d/F/Y g:ia")));

	$items_matriz = array();

	if (count($items) > 0) {
		$query = "SELECT r.id, r.numero_item, c.mprobabilidad_id, c.mseveridad_id, c.mnivel_riesgo_id, r.mriesgo_id, r.fecha_inicio, fecha_fin 
			FROM mriesgo_op r, mclasificacion_riesgo c 
			WHERE r.estado = 1 AND r.mclasificacion_riesgo_id = c.id AND r.dispositivo_seguridad_id = " . Seguridad::escapeSQL($puesto_id) . " AND r.id IN (" . implode(", ", $items) . ") 
			ORDER BY fecha_inicio asc";
	}

	//print($query);
	

	$items_matriz[5][5] = array("celda" => "D9", "datos" => array());
	$items_matriz[5][1] = array("celda" => "E9", "datos" => array());
	$items_matriz[5][2] = array("celda" => "F9", "datos" => array());
	$items_matriz[5][3] = array("celda" => "G9", "datos" => array());
	$items_matriz[5][4] = array("celda" => "H9", "datos" => array());
	$items_matriz[4][5] = array("celda" => "D8", "datos" => array());
	$items_matriz[4][1] = array("celda" => "E8", "datos" => array());
	$items_matriz[4][2] = array("celda" => "F8", "datos" => array());
	$items_matriz[4][3] = array("celda" => "G8", "datos" => array());
	$items_matriz[4][4] = array("celda" => "H8", "datos" => array());
	$items_matriz[3][5] = array("celda" => "D7", "datos" => array());
	$items_matriz[3][1] = array("celda" => "E7", "datos" => array());
	$items_matriz[3][2] = array("celda" => "F7", "datos" => array());
	$items_matriz[3][3] = array("celda" => "G7", "datos" => array());
	$items_matriz[3][4] = array("celda" => "H7", "datos" => array());
	$items_matriz[2][5] = array("celda" => "D6", "datos" => array());
	$items_matriz[2][1] = array("celda" => "E6", "datos" => array());
	$items_matriz[2][2] = array("celda" => "F6", "datos" => array());
	$items_matriz[2][3] = array("celda" => "G6", "datos" => array());
	$items_matriz[2][4] = array("celda" => "H6", "datos" => array());
	$items_matriz[1][5] = array("celda" => "D5", "datos" => array());
	$items_matriz[1][1] = array("celda" => "E5", "datos" => array());
	$items_matriz[1][2] = array("celda" => "F5", "datos" => array());
	$items_matriz[1][3] = array("celda" => "G5", "datos" => array());
	$items_matriz[1][4] = array("celda" => "H5", "datos" => array());


	$tratamientos["c1"] = "Evitar el riesgo";
	$tratamientos["c2"] = "Aceptar o aumentar el riesgo en busca de una oportunidad";
	$tratamientos["c3"] = "Eliminar la fuente de riesgo";
	$tratamientos["c4"] = "Modificar la probabilidad";
	$tratamientos["c5"] = "Modificar las consecuencias";
	$tratamientos["c6"] = "Compartir el riesgo";
	$tratamientos["c7"] = "Retener el riesgo con base en una decision informada.";

	$result = BD::sql_query($query) or die("Error en query " . BD::getLastError());
	$ids_actuales = array();
	while ($row = BD::obtenerRegistro($result)) {
		$items_matriz[$row["mprobabilidad_id"]][$row["mseveridad_id"]]["datos"][] = $row["numero_item"];
		$ids_actuales[] = $row["id"];
	}

	foreach($items_matriz as $prob)
		foreach($prob as $datos){
			$wmatriz->setCellValue($datos["celda"], implode(", ",$datos["datos"]));
		}

	$wmatriz->setCellValue("B9",Fecha::getFechaCorta($fecha_inicio, "d/F/Y g:ia")." --- ". Fecha::getFechaCorta($fecha_fin, "d/F/Y g:ia"));

	$wmatriz->getStyle("D5:H9")->getAlignment()->setWrapText(true);

	$wdetalle = $objPHPExcel->setActiveSheetIndex(1);
	$fila = 8;
	$wdetalle->setCellValue("AK3",$fecha_elaboracion);
	$imprimir_sector = $aprobada_por = true;
	$query = BD::sql_query("SELECT usuario_modifico, fecha_modificacion FROM mriesgo_op WHERE dispositivo_seguridad_id = ". Seguridad::escapeSQL($puesto_id). " AND id IN (". implode(", ", $ids_actuales) .") ORDER BY fecha_modificacion DESC LIMIT 1");
	if($row = BD::obtenerRegistro($query))
	{
		$wdetalle->setCellValue("O3",strtoupper(utf8_encode($row["usuario_modifico"])));
		$wdetalle->setCellValue("AO3",PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($row["fecha_modificacion"], "Y-m-d H:i:s"))));
	}
	$query = BD::sql_query("SELECT v.*, c.razon_social cliente, d.descripcion puesto, l.nombre zona FROM vmriesgo_op v, dispositivo_seguridad d, vsedes s, cliente c, lista l  WHERE v.dispositivo_seguridad_id = ". Seguridad::escapeSQL($puesto_id). " AND v.id IN (". implode(", ", $ids_actuales) .") AND v.dispositivo_seguridad_id = d.id AND d.sede_id = s.id AND s.cliente_id = c.id AND l.id = s.zona_id ORDER BY v.numero_item ASC");
	while ($row = BD::obtenerRegistro($query)) {
	
		$total = 0;
		$total = intval($row["expuesto_personaldirecto"]) + intval($row["expuesto_contratista"]) + intval($row["expuesto_temporal"]) + intval($row["expuesto_visitantes"])+ intval($row["expuesto_estudiantes"]) + intval($row["expuesto_practicantes"]);
		$fecha_seguimiento = $row["fecha_seguimiento"] == "" ? "" : PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($row["fecha_seguimiento"], "Y-m-d H:i:s")));
		$col = 0;
		if($imprimir_sector){
			$wdetalle->setCellValue("F3",strtoupper(utf8_encode($row["cliente"])));
			$wdetalle->setCellValue("B3",strtoupper(utf8_encode($row["zona"])));
			$wdetalle->setCellValue("B4",strtoupper(utf8_encode($row["puesto"])));
			$cliente = utf8_encode($row["cliente"]);
			$imprimir_sector = false;
		}
		
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["numero_item"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["proceso"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["actividad"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["agente_riesgo"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["peligros"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["posibles_efectos"]));
		//define("MODO_DEBUG",true);
		$escenarios = array();
		$query_escenarios = BD::sql_query("SELECT escenario.nombre FROM escenario_riesgo_op, escenario WHERE escenario_riesgo_op.escenario_id = escenario.id AND escenario_riesgo_op.riesgo_id = ".$row["id"]);
		while($escenario = BD::obtenerRegistro($query_escenarios))
		{
			$escenarios[] = ucfirst($escenario["nombre"]); 
		}

		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(implode(", ",$escenarios)));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(($row["actividad_operacional"] == "S") ? "SI" : "NO"));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_personaldirecto"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_estudiantes"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_practicantes"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($total));
		$wdetalle->setCellValueByColumnAndRow($col, $fila, utf8_encode("Ingresar"));
		$styleArray = array(
			'font' => array(
			  'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
			  'color' => array('rgb' => "ec3117")
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 
				'wrap' => TRUE
			)
		  );
		$wdetalle->getStyleByColumnAndRow($col++,$fila)->applyFromArray($styleArray);
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(($row["riesgo_expresado"] == "S") ? "SI" : "NO"));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["probabilidad"]));
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["severidad"]));
		$wdetalle->setCellValueByColumnAndRow($col, $fila, utf8_encode($row["nivel_riesgo"]));
		
		$style = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => str_replace("#", "", $row["nivel_riesgo_color"]))
			)
		);
		$wdetalle->getStyleByColumnAndRow($col++,$fila)->applyFromArray($style);
		$tratamiento = array();
		$query_tratamientos = BD::sql_query("SELECT * FROM tratamiento_riesgo_op WHERE riesgo_id = ".$row["id"]);
		while($row_tratamiento= BD::obtenerRegistro($query_tratamientos))
		{
			if($row_tratamiento["c1"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c1"]); 
			if($row_tratamiento["c2"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c2"]); 
			if($row_tratamiento["c3"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c3"]);
			if($row_tratamiento["c4"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c4"]);
			if($row_tratamiento["c5"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c5"]);
			if($row_tratamiento["c6"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c6"]);
			if($row_tratamiento["c7"] == "on")
				$tratamiento[] = ucfirst($tratamientos["c7"]);
		}
		$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(implode(", ",$tratamiento)));
		$wdetalle->setCellValueByColumnAndRow($col, $fila, utf8_encode("Ingresar"));
		$styleArray = array(
			'font' => array(
			  'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
			  'color' => array('rgb' => "ec3117")
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 
				'wrap' => TRUE
			)
		  );
		$wdetalle->getStyleByColumnAndRow($col++,$fila)->applyFromArray($styleArray);

		$query_controles = BD::sql_query("SELECT * FROM controles_riesgo_op WHERE riesgo_id = ".$row["id"]);
		if(BD::obtenerRegistro($query_controles)){
			$objWorkSheet = $objPHPExcel->createSheet($row["numero_item"]); //Setting index when creating
			$style = array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => "5aadea")
				),'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$objWorkSheet->setCellValue('A1', 'Regresar')
					->setCellValue('C3', 'CONTROL')
					->setCellValue('D3', 'RANKING DE IMPORTANCIA')
					->setCellValue('E3', 'PORCENTAJE DE IMPACTO (%)')
					->setCellValue('F3', 'SI APLICA');
			$objWorkSheet->getStyle("A1")->applyFromArray($styleArray);	
			$objWorkSheet->getCell('A1')
						 ->getHyperlink()
						 ->setUrl("sheet://'"."Detalle"."'!M".$row["numero_item"]);
			$objWorkSheet->getDefaultStyle()->getFont()->setSize(13);
			$objWorkSheet->getColumnDimension("C")->setWidth(20);
			$objWorkSheet->getColumnDimension("D")->setWidth(30);
			$objWorkSheet->getColumnDimension("E")->setWidth(30);
			$objWorkSheet->getColumnDimension("F")->setWidth(20);
			$objWorkSheet->getStyleByColumnAndRow(2,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet->getStyleByColumnAndRow(3,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet->getStyleByColumnAndRow(4,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet->getStyleByColumnAndRow(5,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet->setTitle("Controles_item_".$row["numero_item"]);
			$wdetalle->getCell('M'.$fila)
					 ->getHyperlink()
					 ->setUrl("sheet://'"."Controles_item_".$row["numero_item"]."'!C1");

			$c = 2;
			$f = 4;
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
			$query_controles = BD::sql_query("SELECT * FROM controles_riesgo_op WHERE riesgo_id = ".$row["id"]);
			while($row_control = BD::obtenerRegistro($query_controles))
			{
				$objWorkSheet->setCellValueByColumnAndRow($c++, $f, ucfirst(utf8_encode($row_control["control"])));
				$objWorkSheet->setCellValueByColumnAndRow($c++, $f, utf8_encode($row_control["ranking"]));
				$objWorkSheet->setCellValueByColumnAndRow($c++, $f, utf8_encode($row_control["porcentaje"]));
				$objWorkSheet->setCellValueByColumnAndRow($c++, $f, ($row_control["aplica"] ? "SI" : "NO"));
				$c = 2;
				$f++;
			}
			$objWorkSheet->getStyle("C4:F" . ($f - 1))->applyFromArray(
				array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 
						'wrap' => TRUE
					)
				)
			);
		}

		$query_recomendaciones = BD::sql_query("SELECT * FROM recomendaciones_riesgo_op WHERE riesgo_id = ".$row["id"]);
		if(BD::obtenerRegistro($query_recomendaciones)){
			$objWorkSheet2 = $objPHPExcel->createSheet($row["numero_item"] + 1); //Setting index when creating
			$style = array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => "5aadea")
				),'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$objWorkSheet2->setCellValue('A1', 'Regresar')
					->setCellValue('C3', 'RECOMENDACION')
					->setCellValue('D3', 'RESPONSABLE')
					->setCellValue('E3', 'CARGO')
					->setCellValue('F3', 'FRECUENCIA DE SEGUIMIENTO');
			$objWorkSheet2->getStyle("A1")->applyFromArray($styleArray);	
			$objWorkSheet2->getCell('A1')
						->getHyperlink()
						->setUrl("sheet://'"."Detalle"."'!S".$row["numero_item"]);
			$objWorkSheet2->getDefaultStyle()->getFont()->setSize(13);
			$objWorkSheet2->getColumnDimension("C")->setWidth(30);
			$objWorkSheet2->getColumnDimension("D")->setWidth(30);
			$objWorkSheet2->getColumnDimension("E")->setWidth(30);
			$objWorkSheet2->getColumnDimension("F")->setWidth(20);
			$objWorkSheet2->getStyleByColumnAndRow(2,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet2->getStyleByColumnAndRow(3,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet2->getStyleByColumnAndRow(4,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet2->getStyleByColumnAndRow(5,3)->applyFromArray($style)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objWorkSheet2->setTitle("Recomendaciones_item_".$row["numero_item"]);
			$wdetalle->getCell('S'.$fila)
						->getHyperlink()
						->setUrl("sheet://'"."Recomendaciones_item_".$row["numero_item"]."'!C1");

			$c = 2;
			$f = 4;
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
			$query_recomendaciones = BD::sql_query("SELECT * FROM recomendaciones_riesgo_op WHERE riesgo_id = ".$row["id"]);
			while($row_recomendacion = BD::obtenerRegistro($query_recomendaciones))
			{
				$objWorkSheet2->setCellValueByColumnAndRow($c++, $f, ucfirst(utf8_encode($row_recomendacion["recomendacion"])));
				$objWorkSheet2->setCellValueByColumnAndRow($c++, $f, ucfirst(utf8_encode($row_recomendacion["responsable"])));
				$objWorkSheet2->setCellValueByColumnAndRow($c++, $f, ucfirst(utf8_encode($row_recomendacion["cargo"])));
				$objWorkSheet2->setCellValueByColumnAndRow($c++, $f, ($row_recomendacion["frecuencia"] == 1 ? "SEMANAL" : ($row_recomendacion["frecuencia"] == 2 ? "QUINCENAL" : ($row_recomendacion["frecuencia"] == 3 ? "MENSUAL" : ($row_recomendacion["frecuencia"] == 4 ? "BIMESTRAL" : ($row_recomendacion["frecuencia"] == 5 ? "TRIMESTRAL" : ($row_recomendacion["frecuencia"] == 6 ? "SEMESTRAL" : "ANUAL")))))));
				$c = 2;
				$f++;
			}
			$objWorkSheet2->getStyle("C4:F" . ($f - 1))->applyFromArray(
				array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 
						'wrap' => TRUE
					)
				)
			);
		}
		$fila++;
	}
	
	$wdetalle->getStyle("A8:S" . ($fila - 1))->applyFromArray(
		array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		)
	);
	
	//$wmatriz->getStyle('N10:N' . ($fila - 1))->getAlignment()->setWrapText(true); */

	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$cliente.'.xlsx"');
	header('Cache-Control: max-age=0');
	$objWriter->save('php://output');
    unset($objPHPExcel);
    unset($writer);
?>