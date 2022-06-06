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
	if ($row = BD::obtenerRegistro($sql)) 
	{
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
			<li><a href="#seccion-7">Sección 7</a></li>
		</ul>
		<div id="seccion-1" style="height:250px; overflow: auto;" class="tabs">
			
			<table cellpadding=3 border=0 width="100%">
				<tr style="background-color: #e9e9e9;"> 
					<td align=left><b style="font-weight: bold;">Proceso: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("proceso"); ?></span>
					</td>
				</tr>	
				<tr>
					<td align=left><b style="font-weight: bold;">Actividad: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("actividad"); ?></span>
					</td>
				</tr>
				<tr style="background-color: #e9e9e9;">
					<td align=left valign=top><b style="font-weight: bold;">Factor Riesgo: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("agente_riesgo"); ?></span>
					</td>
				</tr>
				<tr >
					<td align=left valign=top><b style="font-weight: bold;">Riesgo: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("peligros"); ?></span>
					</td>
				</tr>
				<tr style="background-color: #e9e9e9;">
					<td align=left valign=top><b style="font-weight: bold;">Posibles Efectos: </b>
					<span style="text-align: justify;"><?php echo $mriesgo->getCampo("posibles_efectos"); ?></span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-2" style="height:250px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td valign=top align=left colspan="4"><b style="font-weight: bold;">Escenarios para este Riesgo</b></td>
				</tr>
				<?php
					$i = 1;
					$result = BD::sql_query("SELECT *
											FROM escenario
											WHERE id
											IN (SELECT escenario_id	FROM sede_escenario	WHERE sede_id = (SELECT sede_id FROM dispositivo_seguridad WHERE id=" . $mriesgo->getCampo("dispositivo_seguridad_id") . "))");
					while ($row = BD::obtenerRegistro($result)) {
						print('<tr '.($i % 2 != 0 ? "style=\"background-color: #e9e9e9;\"" : "" ).'>');
						$sql = BD::sql_query("SELECT * FROM escenario_riesgo_op WHERE escenario_id = " . $row['id'] . " AND riesgo_id = " . $mriesgo->id);
						if (BD::obtenerRegistro($sql))
						{
							print("<td valign=top><span style=\"font-weight: bold;\">" . ucfirst($row['nombre']) . "</span></td>");
							print('<td><img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15></td>');
						}else
						{
							print("<td valign=top colspan=2><span style=\"font-weight: bold;\">" . ucfirst($row['nombre']) . "</span></td>");
						}
						print("</tr>");
						$i++;
					}
				?>
			</table>
		</div>
		<div id="seccion-3" style="height:250px; overflow: auto;" class="tabs">		
				<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=left colspan="2"><b style="font-weight: bold;">Actividad Operacional: </b>
						<span style="text-align: justify;"><?php echo ($mriesgo->getCampo("actividad_operacional") == "S") ? "SI" : "NO"; ?></span>
					</td>
				</tr>
				<tr>
					<td><span style="font-size:18px;font-weight:bold;">Expuestos</span></td>
				</tr>
				<tr style="background-color: #e9e9e9;">
					<td align=left><b style="font-weight: bold;">Personal Directo: </b></td>
					<td ><span style="text-align: left;"><?php echo $mriesgo->getCampo("expuesto_personaldirecto"); ?></span></td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Personal Propio: </b></td>
					<td><span style="text-align: left;"><?php echo $mriesgo->getCampo("expuesto_estudiantes"); ?></span></td>
				</tr>
				<tr style="background-color: #e9e9e9;">
					<td align=left><b style="font-weight: bold;">practicantes: </b></td>
					<td><span style="text-align: left;"><?php echo $mriesgo->getCampo("expuesto_practicantes"); ?></span></td>
				</tr>
				<tr>
					<td colspan="2">
						<hr>
					</td>
				</tr>
				<tr>
					<td align=right><b style="font-weight: bold;">Total: </b></td>
					<td><span class="total_expuestos" style="text-align: justify;"> </span></td>
				</tr>
			</table>
		</div>
		<div id="seccion-4" style="height:250px; overflow: auto;" class="tabs">
				<table cellpadding=3 border=0 width="100%">
				<tr>
					<td><center><span style="font-size:14px;font-weight:bold;">CONTROL</span></center></td>
					<td><center><span style="font-size:14px;font-weight:bold;">RANKING DE<br>IMPORTANCIA</span></center></td>
					<td><center><span style="font-size:14px;font-weight:bold;">PORCENTAJE<br> DE IMPACTO (%)</span></center></td>
					<td><center><span style="font-size:14px;font-weight:bold;">SE APLICA </span></center></td>
				</tr>
				<?php 
					$i = 1;
					$consulta = BD::sql_query("SELECT * FROM controles_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
					while($row = BD::obtenerRegistro($consulta))
					{
						print('<tr '.($i % 2 != 0 ? "style=\"background-color: #e9e9e9;\"" : "").'>');
						print('<td>');
						print('<center>');
						print('<span>'.$row["control"].'</span>');
						print('</center>');
						print('</td>');
						print('<td>');
						print('<center>');
						print('<span>'.$row["ranking"].'</span>');
						print('</center>');
						print('</td>');
						print('<td>');
						print('<center><span>'.$row["porcentaje"].'</span></center>');
						print('</td>');
						print('<td>');
						print('<center><span> '.($row["aplica"] == "on" ? "Si" : "No").'</span></center>');
						print('	</td>');
						print('</tr>');
					}
				?>
				</table>
		</div>
		<div id="seccion-5" style="height:250px; overflow: auto;" class="tabs">

		<table cellpadding=3 border=0 width="100%">
				<tr style="background-color: #e9e9e9;">
					<td align=left><b style="font-weight: bold;">Riesgo Expresado: </b>
						<span style="text-align: justify;"><?php echo ($mriesgo->getCampo("riesgo_expresado") == "S") ? "SI" : "NO"; ?></span>
					</td>
				</tr>
				<tr>
					<td align=left><b style="font-weight: bold;">Probabilidad de Ocurrencia: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("probabilidad"); ?></span>
					</td>
				</tr>
				<tr style="background-color: #e9e9e9;">
					<td align=left><b style="font-weight: bold;">Severidad: </b>
						<span style="text-align: justify;"><?php echo $mriesgo->getCampo("severidad"); ?></span>
					</td>
				</tr>
				<tr class="nivel">
					<td align=left><b style="font-weight: bold;">Nivel de riesgo: </b><span style="text-align: justify;color:<?php echo $mriesgo->getCampo("nivel_riesgo_color") ?>;font-weight: bold;"><?php echo $mriesgo->getCampo("nivel_riesgo"); ?></span></td>
				</tr>
				</table>
		</div>
		<div id="seccion-6" style="height:250px; overflow: auto;" class="tabs">
		<?php
			$result = BD::sql_query("SELECT * FROM tratamiento_riesgo_op WHERE riesgo_id = '".$id."'");
			while ($row = BD::obtenerRegistro($result)) 							
			{
				print('<table width="100%" border=0>');
				print('<tr style="background-color: #e9e9e9;">');
				print('<td colspan="2"><span style="font-weight: bold;">Medida del tratamiento</span></td>');
				print('</tr>');			
				print('<tr>');				
				print('<td align=left><span style="font-weight: bold;">Evitar el riesgo</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c1']).'</td>');
				print('</tr>');
				print('<tr style="background-color: #e9e9e9;">');				
				print('<td align=left><span style="font-weight: bold;">Aceptar o aumentar el riesgo en busca de una oportunidad</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c2']).'</td>');
				print('</tr>');
				print('<tr>');
				print('<td align=left><span style="font-weight: bold;">Eliminar la fuente de riesgo</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c3']).'</td>');
				print('</tr>');
				print('<tr style="background-color: #e9e9e9;">');
				print('<td align=left><span style="font-weight: bold;">Modificar la probabilidad</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c4']).'</td>');
				print('</tr>');
				print('<tr>');
				print('<td align=left><span style="font-weight: bold;">Modificar las consecuencias</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c5']).'</td>');
				print('</tr>');
				print('<tr style="background-color: #e9e9e9;">');				
				print('<td align=left><span style="font-weight: bold;">Compartir el riesgo</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c6']).'</td>');
				print('</tr>');
				print('<tr>');				
				print('<td align=left><span style="font-weight: bold;">Retener el riesgo con base en una decision informada.</span></td>');
				print('<td>'.str_replace('on', '<img src="https://png.pngtree.com/png-vector/20190504/ourmid/pngtree-vector-tick-icon-png-image_1020658.jpg" width=15>', $row['c7']).'</td>');
				print('</tr>');
				print('</table>');
			}
		?>
		</div>
		<div id="seccion-7" style="height:250px; overflow: auto;" class="tabs">
			<?php
				print("<h2>Recomendaciones</h2>");
				print("<table width=\"100%\" border=0>");
				print("<tr>");
				print("<td><center><b style=\"font-size:14px;font-weight:bold;\">RECOMENDACION</b></center></td>");
				print("<td><center><b style=\"font-size:14px;font-weight:bold;\">RESPONSABLE</b></center></td>");
				print("<td><center><b style=\"font-size:14px;font-weight:bold;\">CARGO</b></center></td>");
				print("<td><center><b style=\"font-size:14px;font-weight:bold;\">FRECUENCIA</b></center></td>");
				print("</tr>");
				$i=1;
				$result = BD::sql_query("SELECT * FROM recomendaciones_riesgo_op WHERE riesgo_id = '".$id."'");
				while ($row = BD::obtenerRegistro($result)) 							
				{
					print("<tr ".($i != 0 ? "style=\"background-color: #e9e9e9;\"" : "").">");
					print("<td><center>".$row['recomendacion']."</center></td>");
					print("<td><center>".$row['responsable']."</center></td>");
					print("<td><center>".$row['cargo']."</center></td>");
					
					if($row['frecuencia']=="1") $fre="Semanal";
					if($row['frecuencia']=="2") $fre="Quincenal";
					if($row['frecuencia']=="3") $fre="Mensual";
					if($row['frecuencia']=="4") $fre="Bimestral";
					if($row['frecuencia']=="5") $fre="Trimestral";
					if($row['frecuencia']=="6") $fre="Semestral";
					if($row['frecuencia']=="7") $fre="Anual";
					print("<td><center>".$fre."</center></td>");
					print("</tr>");

				}
				print("</table>");
			?>

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
		var estudiantes = parseInt("<?php echo $mriesgo->getCampo("expuesto_estudiantes") ?>");
		var practicantes = parseInt("<?php echo $mriesgo->getCampo("expuesto_practicantes") ?>");
		var total = personal_directo  + estudiantes + practicantes;
		$(".total_expuestos").text(total);
	}
</script>