<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	//Aplicacion::validarAcceso(10);
	ini_set('memory_limit', '-1');
	set_time_limit(0);
	BD::changeInstancia("mysql");

	$destino = DIR_APP . "backup2/";
	if (!is_dir($destino))
		mkdir($destino);
	$cont = 0;
	$sql = bd::sql_query("SELECT DISTINCT d.id FROM dispositivo_seguridad d, mriesgo m WHERE d.id = m.dispositivo_seguridad_id AND m.dispositivo_seguridad_id IS NOT NULL");
	while($p = BD::obtenerRegistro($sql))
	{
		$puesto_id = $p["id"];
		$fileType = 'Excel2007';
		$objPHPExcel = new PHPExcel();
		$fileName = dirname(__FILE__).'/formato.xlsx';
		$objReader = PHPExcel_IOFactory::createReader($fileType);
		$objPHPExcel = $objReader->load($fileName);
		$wmatriz = $objPHPExcel->setActiveSheetIndex(0);

		//$fecha_inicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : die("No se cargo la fecha inicio");
		//$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : die("No se cargo la fecha fin");
		//$puesto_id = (isset($_GET["puesto_id"])) ? intval($_GET["puesto_id"]) : die("Error al recibir el ID del puesto");
		//$historico = (isset($_GET["items"])) ? explode(",", $_GET["items"]) : array();
		//$fecha_elaboracion = $fecha_inicio == "" ? "" : PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($fecha_inicio,"Y-m-d H:i:s")));
		$fecha_elaboracion = PHPExcel_Shared_Date::PHPToExcel(new DateTime(date("Y-m-d H:i:s")));
		//$fecha_ultima_actualizacion = $fecha_fin == "" ? "" : PHPExcel_Shared_Date::PHPToExcel(new DateTime(Fecha::getFecha($fecha_fin,"Y-m-d H:i:s")));
		$fecha_ultima_actualizacion = PHPExcel_Shared_Date::PHPToExcel(new DateTime(date("Y-m-d H:i:s")));
		$items = array();
		$cliente = "matriz_riesgo";
		/*foreach($historico as $i) {
			if (is_numeric($i))
				$items[] = intval($i);
		}*/



		$puesto = new DispositivoSeguridad();
		if (!$puesto->load($puesto_id)) die ("error al cargar la información del dispositivo");

		$add_query = " ";

		$wmatriz->setCellValue("A1", utf8_encode("Reporte generado el " . Fecha::getFechaCorta(date("Y-m-d H:i:s"), "d/F/Y g:ia")));

		$items_matriz = array();

		//if (count($items) > 0) {
			$query = "SELECT r.id, r.numero_item, c.mprobabilidad_id, c.mseveridad_id, c.mnivel_riesgo_id, r.mriesgo_id, r.fecha_inicio, fecha_fin 
				FROM mriesgo r, mclasificacion_riesgo c 
				WHERE r.estado = 1 AND  r.mclasificacion_riesgo_id = c.id AND r.dispositivo_seguridad_id = " . Seguridad::escapeSQL($puesto_id) ." 
				ORDER BY fecha_inicio asc";
		//}
		
		$items_matriz[1][1] = array("celda" => "D8", "datos" => array());
		$items_matriz[1][2] = array("celda" => "E8", "datos" => array());
		$items_matriz[1][3] = array("celda" => "F8", "datos" => array());
		$items_matriz[1][4] = array("celda" => "G8", "datos" => array());
		$items_matriz[2][1] = array("celda" => "D7", "datos" => array());
		$items_matriz[2][2] = array("celda" => "E7", "datos" => array());
		$items_matriz[2][3] = array("celda" => "F7", "datos" => array());
		$items_matriz[2][4] = array("celda" => "G7", "datos" => array());
		$items_matriz[3][1] = array("celda" => "D6", "datos" => array());
		$items_matriz[3][2] = array("celda" => "E6", "datos" => array());
		$items_matriz[3][3] = array("celda" => "F6", "datos" => array());
		$items_matriz[3][4] = array("celda" => "G6", "datos" => array());
		$items_matriz[4][1] = array("celda" => "D5", "datos" => array());
		$items_matriz[4][2] = array("celda" => "E5", "datos" => array());
		$items_matriz[4][3] = array("celda" => "F5", "datos" => array());
		$items_matriz[4][4] = array("celda" => "G5", "datos" => array());

		$result = BD::sql_query($query) or die("Error en query " . BD::getLastError());
		$ids_actuales = array();
		while ($row = BD::obtenerRegistro($result)) {
			$items_matriz[$row["mprobabilidad_id"]][$row["mseveridad_id"]]["datos"][] = $row["numero_item"];
			$ids_actuales[] = $row["id"];
		}

		foreach($items_matriz as $prob)
			foreach($prob as $datos)
				$wmatriz->setCellValue($datos["celda"], implode(", ",$datos["datos"]));

		$wmatriz->setCellValue("B9","");

		$wmatriz->getStyle("D5:G8")->getAlignment()->setWrapText(true);

		$wdetalle = $objPHPExcel->setActiveSheetIndex(1);
		$fila = 8;
		$wdetalle->setCellValue("AK3",$fecha_elaboracion);
		$imprimir_sector = $aprobada_por = true;
		$query = BD::sql_query("SELECT usuario_modifico, fecha_modificacion FROM mriesgo WHERE dispositivo_seguridad_id = ". Seguridad::escapeSQL($puesto_id). " AND id IN (". implode(", ", $ids_actuales) .") ORDER BY fecha_modificacion DESC LIMIT 1");
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
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["tarea"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["oficio"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["agente_riesgo"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["peligros"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["fuente"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["posibles_efectos"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(($row["actividad_operacional"] == "S") ? "SI" : "NO"));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode(($row["actividad_rutinaria"] == "S") ? "SI" : "NO"));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_personaldirecto"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_contratista"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_temporal"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_visitantes"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_estudiantes"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["expuesto_practicantes"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($total));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["exposicion_horasxdia"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_fuente_ing"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_medio_ing"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_medio_senal"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_prot"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_cap"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_moni"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_estan"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_proced"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["ctr_persona_obser"]));
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

			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_eliminacion"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_sustitucion"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_ctr_ingenieria"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_ctr_administrativo"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_senalizacion"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["rcm_proteccionpersonal"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($fecha_seguimiento));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["responsable"]));
			$wdetalle->setCellValueByColumnAndRow($col++, $fila, utf8_encode($row["observaciones_generales"]));

			$fila++;

		}
		
		$wdetalle->getStyle("A8:AO" . ($fila - 1))->applyFromArray(
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
		$objWriter->save($destino.utf8_decode($cliente.$puesto_id).'.xlsx');
		unset($objPHPExcel);
		unset($objWriter);
		$cont++;
	}
?>