<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9, 10);
	//define("MODO_DEBUG",true);

	if (isset($_POST["delete"])) {
		//define("MODO_DEBUG",true);
		$reg = new MatrizRiesgo();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		if($reg->getCampo("mriesgo_id") == "")
		{
			if(!$reg->delete())
			{
				$reg->setCampo("estado","0");
				BD::sql_query("START TRANSACTION");
				if(!$reg->update()){
					BD::sql_query("ROLLBACK");
					die("Se produjo un error al intentar eliminar el item " . BD::getLastError());
				}
				BD::sql_query("COMMIT");
			}
			die("ok");
		}else
		{
			if(!$reg->delete()){
				$reg->setCampo("estado","0");
				BD::sql_query("START TRANSACTION");
				if(!$reg->update()){
					BD::sql_query("ROLLBACK");
					die("Se produjo un error al intentar eliminar el item " . BD::getLastError());
				}
			}
			BD::sql_query("COMMIT");
			die("ok");
		}
		//die("No fue posible eliminar el item debido a que tiene modificaciones previas");
	}
	$puesto_id = isset($_POST["puesto_id"]) ? $_POST["puesto_id"] : die("Error al recibir el ID del puesto");
	$sector_id = isset($_POST["sector_id"]) ? $_POST["sector_id"] : die("Error al recibir el ID del sector");
	$proceso_id = isset($_POST["proceso_id"]) ? intval($_POST["proceso_id"]) : die("Error al obtener el ID del proceso");
	$puesto = new DispositivoSeguridad();
	if (!$puesto->load($puesto_id)) die("error al cargar la información del puesto");
	//define("MODO_DEBUG",true);
	if (count($puesto->getMatrizRiesgoActual()) == 0) {
		$res = BD::sql_query("SELECT proceso_id,actividad_id,tarea_id,oficio_id,mpeligro_id,fuente,posibles_efectos,actividad_operacional,actividad_rutinaria,expuesto_personaldirecto,expuesto_contratista,expuesto_temporal,expuesto_visitantes,expuesto_estudiantes,expuesto_practicantes,exposicion_horasxdia,ctr_fuente_ingenieria_id,ctr_medio_ingenieria_id,ctr_medio_senalizacion_id,ctr_persona_proteccion_id,ctrpersona_capacitacion_id,ctr_persona_monitoreo_id,ctr_persona_estandarizacion_id,ctr_persona_procedimiento_id,ctr_persona_observacion_id, riesgo_expresado,mclasificacion_riesgo_id,rcm_eliminacion,rcm_sustitucion,rcm_ctr_ingenieria,rcm_ctr_administrativo,rcm_senalizacion,rcm_proteccionpersonal,fecha_seguimiento,responsable,observaciones_generales,sector_id,orden_cronologico,numero_item".
		" FROM mriesgo_op".
		" WHERE estado = 1 AND sector_id = $sector_id AND proceso_id = $proceso_id AND dispositivo_seguridad_id IS NULL");
		$fecha_inicio = date("Y-m-d H:i:s");
		$item = 1;

		//BEGIN TRANSACTION
		BD::sql_query("START TRANSACTION");
		while ($f = BD::obtenerRegistro($res)) {
			$mr = new MatrizRiesgo($f);
			$mr->setCampo("numero_item", $item++);
			$mr->setCampo("dispositivo_seguridad_id", $puesto_id);
			$mr->setCampo("fecha_inicio", $fecha_inicio);
			$mr->setCampo("usuario_modifico",Aplicacion::getNombreCompleto());
			$mr->setCampo("fecha_modificacion",$fecha_inicio);
			if (!$mr->save()) {
				//ROLLBACK
				BD::sql_query("ROLLBACK");
				die("Se produjo un error al intentar registrar la matriz base en el puesto " . BD::getLastError());
			}
		}

		//COMMIT
		BD::sql_query("COMMIT");
	}

?>

<table style='margin-top:4px;'>
	<tr>
		<td align="left" style="padding:4px; padding-top:9px;">
			<button title="Registrar ítem" onclick="nuevoItem(<?php print($puesto_id); ?>)" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i>Nuevo</button>
			<!--<button title="Registrar ítem" onclick="nuevoItem(<?php print($puesto_id); ?>)" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i>Nuevo: <?php print($puesto_id); ?>  </button>
			<button title="Registrar ítem" onclick="nuevoItem2()" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i>Nuevo</button>-->
		</td>
		
	</tr>
	<tr>
		<td valign="top" style="padding:4px;">
			<table id="lista-matriz" class="scroll" cellpadding="0" cellspacing="0" style="font-size:12px; border-collapse: none;"></table>
			<div id="paginador-matriz" class="scroll" style="text-align:left; font-size:10px;"></div>
		</td>
	</tr>
</table>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
	
		$("#lista-matriz").jqGrid({
			url: "<?php echo DIR_WEB; ?>lista.php?puesto_id=<?php echo $puesto_id; ?>&sector_id=<?php echo $sector_id; ?>",
			datatype: "json",
			colNames: ['<b>Id</b>','<b>Peligro</b>', '<b>Prob Ocurrencia</b>', '<b>Impacto</b>', '<b>Nivel Riesgo</b>', '<b>Opc.</b>'],
			colModel: [{
					name: 'numero_item',
					index: 'numero_item',
					width: 40,
					align: 'center'
				},{
					name: 'peligros',
					index: 'peligros',
					width: 365,
					align: 'left'
				},
				{
					name: 'probabilidad',
					index: 'probabilidad',
					width: 100,
					align: 'center'
				},
				{
					name: 'severidad',
					index: 'severidad',
					width: 100,
					align: 'center'
				},
				{
					name: 'nivel_riesgo',
					index: 'nivel_riesgo',
					width: 100,
					align: 'center'
				},
				{
					name: 'opciones',
					index: 'opciones',
					width: 100,
					align: 'center',
					search: false
				}
			],
			pager: jQuery('#paginador-matriz'),
			rowNum: 13,
			rowList: [10, 15, 30],
			imgpath: "css/jqGrid/steel/images",
			viewrecords: true,
			mtype: 'POST',
			pagerpos: 'center',
			sortname : "numero_item",
			sortorder: "asc",
			height: "auto",
			caption: "<i class='icon-list icon-white' /> <b>ITEMS </b>"
		});

		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador-matriz").removeClass("ui-corner-bottom");
		
		hideMessage();
	});

	function editarItem(id) {
		showMessage();
		var params = {
			id: id
		}
		$("#ventana4").load("<?php echo DIR_WEB; ?>editar.php", params);
	}

	function nuevoItem() {
		showMessage();
		var params = {
			puesto_id: "<?php echo $puesto_id; ?>",
			sector_id: "<?php echo $sector_id; ?>"
		};
		$("#ventana4").load("<?php echo DIR_WEB; ?>nuevo.php", params);
	}

	function nuevoItem2() {
		showMessage();
		var params = {
			puesto_id: "<?php echo $puesto_id; ?>",
			sector_id: "<?php echo $sector_id; ?>"
		};
		
		$("#ventana4").load("<?php echo DIR_WEB; ?>nuevocg.php", params);
	}

	function eliminarItem(id) {
		Swal.fire({
			title: '',
			text: '¿Confirma que desea borrar ítem de la matriz de riesgo?',
			type: 'question',
			showCancelButton: true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then(function(res) {
			if (res.value) {
				showMessage();
				$.post("<?php echo DIR_WEB; ?>index.php", {
					delete: id
				}, function(res) {
					hideMessage();
					if (!/^ok$/.test(res))
						mensaje("Error", "No ha sido posible borrar el registro del ítem de la matriz de riesgo debido a que puede existir información relacionada ", "error");
					$("#lista-matriz").trigger("reloadGrid");
				});
			}
		});
	}
</script>