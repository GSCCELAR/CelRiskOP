<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);

$id = isset($_POST["id"]) ? $_POST["id"] : die("Error al recibir el ID del ítem");
$mriesgo = new MatrizRiesgo();
if (!$mriesgo->load($id)) die("error al cargar la información de la matriz de riesgo");

$agenteriesgo_id = "";
$sql = BD::sql_query("SELECT agenteriesgo_id FROM mpeligro WHERE id =" . $mriesgo->getCampo("mpeligro_id"));
if ($row = BD::obtenerRegistro($sql)) {
	$agenteriesgo_id = $row["agenteriesgo_id"];
}
$mprobabilidad_id = "";
$mseveridad_id = "";
$sql = BD::sql_query("SELECT mprobabilidad_id, mseveridad_id FROM mclasificacion_riesgo WHERE id =" . $mriesgo->getCampo("mclasificacion_riesgo_id"));
if ($row = BD::obtenerRegistro($sql)) {
	$mprobabilidad_id = $row["mprobabilidad_id"];
	$mseveridad_id = $row["mseveridad_id"];
}
?>
<style>
	.select2-container--default.select2-container--disabled .select2-selection--single {
		background-color: transparent;
	}
</style>
<form method="post" name="formEditarItem" id="formEditarItem" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id="id" name="id" value="<?php echo $mriesgo->id; ?>">
	<input type="hidden" id="mpeligro_id" name="mpeligro_id" value="<?php echo $mriesgo->getCampo("mpeligro_id"); ?>">
	<input type="hidden" id="sector_id" name="sector_id" value="<?php echo $mriesgo->getCampo("sector_id"); ?>">
	<input type="hidden" id="dispositivo_seguridad_id" name="dispositivo_seguridad_id" value="<?php echo $mriesgo->getCampo("dispositivo_seguridad_id"); ?>">

	<div id="tabs-item">
		<ul>
			<li><a href="#seccion-1">Sección 1</a></li>
			<li><a href="#seccion-2">Sección 2</a></li>
			<li><a href="#seccion-3">Sección 3</a></li>
			<li><a href="#seccion-4">Sección 4</a></li>
			<li><a href="#seccion-5">Sección 5</a></li>
			<li><a href="#seccion-6">Sección 6</a></li>
		</ul>
		<div id="seccion-1" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=left><b style="font-weight: bold;">Proceso: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("proceso"); ?></span>
					</td>
				</tr>	
				<tr>
					<td align=left><b style="font-weight: bold;">Actividad: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("actividad"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Tarea: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("tarea"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Oficio: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("oficio"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left valign=top><b style="font-weight: bold;">Factor Riesgo: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("agente_riesgo"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left valign=top><b style="font-weight: bold;">Fuente: </b>
						<span style="text-align: justify;"> <?php echo $mriesgo->getCampo("fuente"); ?></span></td>
				</tr>
				<tr >
					<td align=left valign=top><b style="font-weight: bold;">Riesgo: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("peligros"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left valign=top><b style="font-weight: bold;">Posibles Efectos: </b>
					<span style="text-align: justify;"><?php echo $mriesgo->getCampo("posibles_efectos"); ?></span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-2" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=left><b style="font-weight: bold;">Actividad Operacional: </b>
						<span style="text-align: justify;"><?php echo ($mriesgo->getCampo("actividad_operacional") == "S") ? "SI" : "NO"; ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Actividad Rutinaria: </b>
						<span style="text-align: justify;"><?php echo ($mriesgo->getCampo("actividad_rutinaria") == "S") ? "SI" : "NO"; ?></span>
					</td>
				</tr>
				<tr>
					<td><span style="font-size:18px;font-weight:bold;">Expuestos</span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Personal Directo: </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("expuesto_personaldirecto"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Contratista: </b></td>
					<td><span style="text-align: justify;"> <?php echo $mriesgo->getCampo("expuesto_contratista"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Temporal: </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("expuesto_temporal"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Visitantes: </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("expuesto_visitantes"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Estudiantes: </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("expuesto_estudiantes"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">practicantes: </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("expuesto_practicantes"); ?></span></td>
				</tr>
				<tr>
					<td colspan="2">
						<hr>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Total: </b></td>
					<td><span class="total_expuestos" style="text-align: justify;"> </span></td>
					<td align=left><b style="font-weight: bold;">Tiempo de Exposición (horas/dia): </b></td>
					<td><span style="text-align: justify;"><?php echo $mriesgo->getCampo("exposicion_horasxdia"); ?></span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-3" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td><span style="font-size:18px;font-weight:bold;">Controles Existentes</span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Fuente Ingeniería: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_fuente_ing"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Medio Ingeniería: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_medio_ing"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Medio Señalización: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_medio_senal"); ?></span>
					</td>
				</tr>
				<tr>
					<td><span style="font-size:18px;font-weight:bold;">Persona</span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Elementos de protección personal: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_prot"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Capacitación: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_cap"); ?></span>
					</td>
				</tr>
				<td align=left><b style="font-weight: bold;">Monitoreo: </b>
					<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_moni"); ?></span>
				</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Estandarización: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_estan"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Procedimiento: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_proced"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Observación comportamiento: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("ctr_persona_obser"); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div id="seccion-4" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=left><b style="font-weight: bold;">Riesgo Expresado: </b>
						<span style="text-align: justify;"><?php echo ($mriesgo->getCampo("riesgo_expresado") == "S") ? "SI" : "NO"; ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Probabilidad de Ocurrencia: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("probabilidad"); ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Severidad: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("severidad"); ?></span>
					</td>
				</tr>
				<tr class="nivel">
					<td align=left><b style="font-weight: bold;">Nivel de riesgo: </b><span style="text-align: justify;color:<?php echo $mriesgo->getCampo("nivel_riesgo_color") ?>;font-weight: bold;"><?php echo $mriesgo->getCampo("nivel_riesgo"); ?></span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-5" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td><span style="font-size:18px;font-weight:bold;">Recomendaciones para el control de riesgo</span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Eliminación: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_eliminacion"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Sustitución: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_sustitucion"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Control de ingeniería: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_ctr_ingenieria"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Control Administrativo: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_ctr_administrativo"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Señalización: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_senalizacion"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Elementos de Protección Personal: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("rcm_proteccionpersonal"); ?></span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-6" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=left style="width:15%;"><b style="font-weight: bold;">Cargo del Responsable: </b><span style="text-align: justify;"> <?php echo $mriesgo->getCampo("responsable"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Observaciones Generales: </b><span style="text-align: justify;"><?php echo $mriesgo->getCampo("observaciones_generales"); ?></span></td>
				</tr>
				<tr>
					<td align="left"><b style="font-weight: bold;">Fecha Seguimiento: </b><span style="text-align: justify;"><?php echo Fecha::getFecha($mriesgo->getCampo("fecha_seguimiento"),"d - F - Y");?></span></td>
				</tr>
			</table>
		</div>
	</div>
</form>
<script type="text/javascript">
	var marker;
	var lastSel;
	var latlng;
	$(document).ready(function() {
		var json = "";
		$("#ventana5").dialog("destroy");
		$("#ventana5").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Ver ítem",
			resizable: false,
			open: function() {
				var t = $(this).parent(),
					w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 800,
			buttons: {
				"Cerrar": function() {
					$("#ventana5").html("");
					$("#ventana5").dialog("destroy");
				}
			},
			close: function() {
				$("#ventana5").html("");
				$("#ventana5").dialog("destroy");
			}
		});

		$("#tabs-item").tabs({
			select: function(event, ui) {}
		});

		totalExpuestos();
		hideMessage();
	});

	function totalExpuestos() {
		var personal_directo = parseInt("<?php echo $mriesgo->getCampo("expuesto_personaldirecto") ?>");
		var contratista = parseInt("<?php echo $mriesgo->getCampo("expuesto_contratista") ?>");
		var temporal = parseInt("<?php echo $mriesgo->getCampo("expuesto_temporal") ?>");
		var visitantes = parseInt("<?php echo $mriesgo->getCampo("expuesto_visitantes") ?>");
		var estudiantes = parseInt("<?php echo $mriesgo->getCampo("expuesto_estudiantes") ?>");
		var practicantes = parseInt("<?php echo $mriesgo->getCampo("expuesto_practicantes") ?>");
		var total = personal_directo + contratista + temporal + visitantes + estudiantes + practicantes;
		$(".total_expuestos").text(total);
	}
</script>