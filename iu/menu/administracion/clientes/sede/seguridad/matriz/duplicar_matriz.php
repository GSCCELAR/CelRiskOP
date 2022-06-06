<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9, 10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);

	if(isset($_GET["dispositivo_original_id"]) && isset($_GET["dispositivo_copia_id"]) && isset($_GET["sector_id"]) && isset($_GET["nombre_dispositivo"]))
	{
		$fecha = date("Y-m-d H:i:s");
		$sql = BD::sql_query("SELECT sede_id FROM dispositivo_seguridad WHERE id = ".$_GET["dispositivo_original_id"]);
		if($fila = BD::obtenerRegistro($sql)){
			if(BD::sql_query("INSERT INTO dispositivo_seguridad (sede_id, descripcion) VALUES (".$fila["sede_id"].",'".$_GET["nombre_dispositivo"]."') ")){
				$sql = BD::sql_query("SELECT max(id) AS id FROM dispositivo_seguridad");
				if($f = BD::obtenerRegistro($sql))
				{
					if(!BD::sql_query("INSERT INTO mriesgo ( proceso_id , actividad_id , tarea_id , oficio_id , mpeligro_id , fuente , posibles_efectos , actividad_operacional , actividad_rutinaria , expuesto_personaldirecto , expuesto_contratista , expuesto_temporal , expuesto_visitantes , expuesto_estudiantes , expuesto_practicantes , exposicion_horasxdia , ctr_fuente_ingenieria_id , ctr_medio_ingenieria_id , ctr_medio_senalizacion_id , ctr_persona_proteccion_id , ctrpersona_capacitacion_id , ctr_persona_monitoreo_id , ctr_persona_estandarizacion_id , ctr_persona_procedimiento_id , ctr_persona_observacion_id , riesgo_expresado , mclasificacion_riesgo_id , rcm_eliminacion , rcm_sustitucion , rcm_ctr_ingenieria , rcm_ctr_administrativo , rcm_senalizacion , rcm_proteccionpersonal , fecha_seguimiento , responsable , observaciones_generales , dispositivo_seguridad_id , sector_id , orden_cronologico , numero_item , fecha_inicio , fecha_fin , mriesgo_id , usuario_modifico , fecha_modificacion ) SELECT DISTINCT proceso_id , actividad_id , tarea_id , oficio_id , mpeligro_id , fuente , posibles_efectos , actividad_operacional , actividad_rutinaria , expuesto_personaldirecto , expuesto_contratista , expuesto_temporal , expuesto_visitantes , expuesto_estudiantes , expuesto_practicantes , exposicion_horasxdia , ctr_fuente_ingenieria_id , ctr_medio_ingenieria_id , ctr_medio_senalizacion_id , ctr_persona_proteccion_id , ctrpersona_capacitacion_id , ctr_persona_monitoreo_id , ctr_persona_estandarizacion_id , ctr_persona_procedimiento_id , ctr_persona_observacion_id , riesgo_expresado , mclasificacion_riesgo_id , rcm_eliminacion , rcm_sustitucion , rcm_ctr_ingenieria , rcm_ctr_administrativo , rcm_senalizacion , rcm_proteccionpersonal , fecha_seguimiento , responsable , observaciones_generales , ".$f["id"]." , ".$_GET["sector_id"]." , orden_cronologico , numero_item , '$fecha' , fecha_fin , mriesgo_id , usuario_modifico , '$fecha'  from mriesgo where dispositivo_seguridad_id = ".$_GET["dispositivo_original_id"]." AND fecha_fin IS NULL")) 
						die("Error al copiar la matriz");
					die ("ok");
				}
			}
		}
		die("Error al crear el dispositivo de seguridad");
	}
	
?>