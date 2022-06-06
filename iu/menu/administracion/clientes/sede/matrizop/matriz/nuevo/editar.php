<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);

$id = isset($_POST["id"]) ? $_POST["id"] : die("Error al recibir el ID del �tem");
$mriesgo = new MatrizRiesgo();
if (!$mriesgo->load($id)) die("error al cargar la informaci�n de la matriz de riesgo");

if (isset($_POST["mpeligro_id"]) && isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"])) { 
	//$mriesgo->setCampo("dispositivo_seguridad_id",($mriesgo->getCampo("dispositivo_seguridad_id") != '') ? $mriesgo->getCampo("dispositivo_seguridad_id"): "_NULL");
	$sql = BD::sql_query("SELECT id FROM mclasificacion_riesgo WHERE mprobabilidad_id = " . Seguridad::escapeSQL($_POST["mprobabilidad_id"]) . " AND mseveridad_id = " . Seguridad::escapeSQL($_POST["mseveridad_id"]));
	if ($row = BD::obtenerRegistro($sql))
		$_POST["mclasificacion_riesgo_id"] = $row["id"];
	//$_POST["fecha_seguimiento"] = date("Y-m-d H:i:s");
	$_POST["tarea_id"] = 11;
	$_POST["oficio_id"] = 14;
	$_POST["ctr_fuente_ingenieria_id"] = 15;
	$_POST["ctr_medio_ingenieria_id"] = 16;
	$_POST["ctr_medio_senalizacion_id"] = 17;
	$_POST["ctr_persona_proteccion_id"] = 18;
	$_POST["ctrpersona_capacitacion_id"] = 19;
	$_POST["ctr_persona_monitoreo_id"] = 20;
	$_POST["ctr_persona_estandarizacion_id"] = 21;
	$_POST["ctr_persona_procedimiento_id"] = 22;
	$_POST["ctr_persona_observacion_id"] = 23;
	$_POST["responsable"] = "NA";
	$_POST["observaciones_generales"] = "NA";
	$_POST["fecha_seguimiento"] = "2032-12-12";

	$result = BD::sql_query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'celrisk' AND TABLE_NAME = 'mriesgo_op'");
	if($row = BD::obtenerRegistro($result)) {
		$siguiente_id = $row['AUTO_INCREMENT'];
	}
	//define("MODO_DEBUG",true);
	//$sql = BD::sql_query("DELETE FROM tratamiento_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
	$sql = BD::sql_query("INSERT INTO tratamiento_riesgo_op (riesgo_id ,c1,c2,c3 ,c4 ,c5 ,c6 ,c7) VALUES ('" . $siguiente_id . "', '" . (isset($_POST['opciontra1']) ? $_POST['opciontra1'] : ""). "', '" . (isset($_POST['opciontra2']) ? $_POST['opciontra2'] : "") . "', '" . (isset($_POST['opciontra3']) ? $_POST['opciontra3'] : ""). "', '" . (isset($_POST['opciontra4']) ? $_POST['opciontra4'] : "") . "', '" . (isset($_POST['opciontra5']) ? $_POST['opciontra5'] : "") . "', '" . (isset($_POST['opciontra6']) ? $_POST['opciontra6'] : "") . "', '" . (isset($_POST['opciontra7']) ? $_POST['opciontra7'] : "") . "')");
	//$sql = BD::sql_query($cadenasql);
	//$sql = BD::sql_query("DELETE FROM recomendaciones_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
	for ($i = 1; $i <= 10; $i++) {
		$cadena = "recome" . $i;
		$cadena2 = "respon" . $i;
		$cadena3 = "cargo" . $i;
		$cadena4 = "frec" . $i;

		if (strlen($_POST[$cadena]) > 0) {
			$sql = BD::sql_query("INSERT INTO recomendaciones_riesgo_op (riesgo_id ,recomendacion ,responsable ,cargo ,frecuencia) VALUES ('" .  $siguiente_id . "', '" . $_POST[$cadena] . "', '" . $_POST[$cadena2] . "', '" . $_POST[$cadena3] . "', '" . $_POST[$cadena4] . "')");
		}
	}
	//$sql = BD::sql_query("DELETE FROM controles_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
	//ingresamos registros en la tabla controles_riesgo_op
	for ($i = 1; $i <= 10; $i++) {
		$cadena = "control" . $i;
		$cadena2 = "sel" . $i;
		$cadena3 = "impact" . $i;
		$cadena4 = "check" . $i;

		if (strlen($_POST[$cadena]) > 0) {
			$sql = BD::sql_query("INSERT INTO controles_riesgo_op (riesgo_id , control ,ranking ,porcentaje ,aplica) VALUES ('" .  $siguiente_id . "', '" . $_POST[$cadena] . "', '" . $_POST[$cadena2] . "', '" . $_POST[$cadena3] . "', '" . $_POST[$cadena4] . "')");
		}
	}
	//$sql = BD::sql_query("DELETE FROM escenario_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
	if (!empty($_POST['escen'])) {
		foreach ($_POST['escen'] as $value) {
			$sql = BD::sql_query("INSERT INTO  escenario_riesgo_op (escenario_id,riesgo_id) values ('" . $value . "','" .  $siguiente_id . "')");
		}
	}
	
	BD::sql_query("START TRANSACTION");
	$fecha_cambio = date("Y-m-d H:i:s");
	$_POST["orden_cronologico"] = intval($mriesgo->getCampo("orden_cronologico")) + 1;
	$_POST["fecha_inicio"] = $fecha_cambio;
	$_POST["numero_item"] = $mriesgo->getCampo("numero_item");
	$_POST["mriesgo_id"] = $mriesgo->id;
	$_POST["usuario_modifico"] = Aplicacion::getNombreCompleto();
	$_POST["fecha_modificacion"] = $fecha_cambio;
	$mriesgo_nuevo = new MatrizRiesgo($_POST, $siguiente_id);
	if (!$mriesgo_nuevo->save()) {
		echo BD::getLastError();
		BD::sql_query("ROLLBACK");
	}
	if ($mriesgo->getCampo("mriesgo_id") == "")
		$mriesgo->setCampo("mriesgo_id", "_NULL");
	$mriesgo->setCampo("fecha_fin", $fecha_cambio);
	$mriesgo->setCampo("fecha_modificacion", $fecha_cambio);

	if ($mriesgo->update()) {
		BD::sql_query("COMMIT");
		die("ok");
	}
	echo BD::getLastError();
	BD::sql_query("ROLLBACK");
	die();
}

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
 
$tratamiento_riesgo_op = array();
$mclasificacion_riesgo = array();

$consulta = BD::sql_query("SELECT * FROM tratamiento_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
if($row = BD::obtenerRegistro($consulta))
{
	$tratamiento_riesgo_op["opciontra1"] = $row["c1"];
	$tratamiento_riesgo_op["opciontra2"] = $row["c2"];
	$tratamiento_riesgo_op["opciontra3"] = $row["c3"];
	$tratamiento_riesgo_op["opciontra4"] = $row["c4"];
	$tratamiento_riesgo_op["opciontra5"] = $row["c5"];
	$tratamiento_riesgo_op["opciontra6"] = $row["c6"];
	$tratamiento_riesgo_op["opciontra7"] = $row["c7"];
}

$consulta = BD::sql_query("SELECT * FROM mclasificacion_riesgo WHERE id = ". $mriesgo->getCampo("mclasificacion_riesgo_id"));
if($row = BD::obtenerRegistro($consulta))
{
	$mclasificacion_riesgo["mprobabilidad_id"] = $row["mprobabilidad_id"];
	$mclasificacion_riesgo["mseveridad_id"] = $row["mseveridad_id"];
}
?>
<form method="post" name="formEditarItem" id="formEditarItem" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id="id" name="id" value="<?php echo $mriesgo->id; ?>">
	<input type="hidden" id="mpeligro_id" name="mpeligro_id" value="<?php echo $mriesgo->getCampo("mpeligro_id"); ?>">
	<input type="hidden" id="sector_id" name="sector_id" value="<?php echo $mriesgo->getCampo("sector_id"); ?>">
	<input type="hidden" id="dispositivo_seguridad_id" name="dispositivo_seguridad_id" value="<?php echo $mriesgo->getCampo("dispositivo_seguridad_id"); ?>">
	<div id="tabs-matriz">
		<ul>
			<!--<li><a href="#seccion-0">Secci�n 1  <?php print($puesto_id . " " . $sector_id); ?></a></li>-->
			<li><a href="#seccion-0">Secci�n 1</a></li>
			<li><a href="#seccion-1">Secci�n 2</a></li>
			<li><a href="#seccion-2">Secci�n 3</a></li>
			<li><a href="#seccion-3">Secci�n 4</a></li>
			<li><a href="#seccion-4">Secci�n 5</a></li>
			<li><a href="#seccion-5">Secci�n 6</a></li>
			<li><a href="#seccion-6">Secci�n 7</a></li>
		</ul>
		<div id="seccion-0" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right><b style="font-weight: bold;">Proceso:</b></td>
					<td colspan="3">
						<select class="no-editable" style='padding:1px; height:25px; width:100%;' name='proceso_id' id='proceso_id'>
							<option value="">Seleccione...</option>
							<option value="1" <?php echo ($mriesgo->getCampo("proceso_id") == "1") ? "selected" : ""; ?>>Vigilancia Fisica</option>
							<option value="2" <?php echo ($mriesgo->getCampo("proceso_id") == "2") ? "selected" : ""; ?>>Seg Electronica</option>
							<option value="3" <?php echo ($mriesgo->getCampo("proceso_id") == "3") ? "selected" : ""; ?>>Servicios Conexos</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b style="font-weight: bold;">Actividad:</b></td>
					<td colspan="3">
						<select class="editable" style='padding:1px; height:25px; width:100%;' name='actividad_id' id='actividad_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							//Cambia solo para operaciones
							$lista->writeOptions($mriesgo->getCampo("actividad_id"), array("id", "nombre"), array("tipo_lista_codigo" => "ACTIVIDAD", "nombre" => "Administrativa"), " OR id='276' OR id='380' ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right valign=top><b style="font-weight: bold;">Factor Riesgo:</b></td>
					<td valign=top>
						<select class="no-editable" style='padding:1px; height:25px; width:100%;' name='agente_riesgo_id' id='agente_riesgo_id'>
							<option value="">Seleccione...</option>
							<?php
								$lista = new Lista();
								$lista->writeOptions(886, array("id", "nombre"), array("tipo_lista_codigo" => "AGENTE_RIESGO", "id" => "886"), " ORDER BY nombre");
							?>
						</select>
					</td>
					<td align=right valign=top><b style="font-weight: bold;">Fuente:</b></td>
					<td>
						<select name="fuente" id="fuente" style="width:260px;resize:none;">
							<option value="1" <?php echo ($mriesgo->getCampo("fuente") == "1") ? "selected" : ""; ?>>Interna</option>
							<option value="2" <?php echo ($mriesgo->getCampo("fuente") == "2") ? "selected" : ""; ?>>Externa</option>
							<option value="3" <?php echo ($mriesgo->getCampo("fuente") == "3") ? "selected" : ""; ?>>Ambas</option>
						</select>
					</td>
				</tr>
				<tr class="peligro_view" style="visibility:hidden;">
					<td align=right valign=top><b style="font-weight: bold;">Riesgo:</b></td>
					<td valign=top>
						<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='peligros_riesgo' id='peligros_riesgo'>
						</select>
					</td>
					<td align=right valign=top><b style="font-weight: bold;">Posibles Efectos:</b></td>
					<td> <textarea name="posibles_efectos" id="posibles_efectos" rows="3" style="width:260px;resize:none;" readonly><?php echo $mriesgo->getCampo("posibles_efectos"); ?></textarea></td>
				</tr>
			</table>
		</div>
		<div id="seccion-1" style="height:350px; overflow: auto;" class="tabs">

			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td valign=top align=left colspan="4"><b style="font-weight: bold;">Escenarios para este Riesgo</b></td>
				</tr>
				<?php
				$cont = 0;
				$result = BD::sql_query("SELECT *
			FROM escenario
			WHERE id
			IN (SELECT escenario_id	FROM sede_escenario	WHERE sede_id = (SELECT sede_id FROM dispositivo_seguridad WHERE id=" . $mriesgo->getCampo("dispositivo_seguridad_id") . "))");
				while ($row = BD::obtenerRegistro($result)) {
					if ($cont == 0)
						print("<tr>");
					$cont++;
					$sql = BD::sql_query("SELECT * FROM escenario_riesgo_op WHERE escenario_id = " . $row['id'] . " AND riesgo_id = " . $mriesgo->id);
					if (BD::obtenerRegistro($sql))
						print("<td valign=top style='padding-bottom:15px;'><input type='checkbox' name='escen[]' value='" . $row['id'] . "' style='margin-right:5px;margin-top:0px !important;' " . ($mriesgo->getCampo("")) . " checked><span>" . ucfirst($row['nombre']) . "</span></td>");
					else
						print("<td valign=top style='padding-bottom:15px;'><input type='checkbox' name='escen[]' value='" . $row['id'] . "' style='margin-right:5px;margin-top:0px !important;' " . ($mriesgo->getCampo("")) . "><span>" . ucfirst($row['nombre']) . "</span></td>");
					if ($cont == 3) {
						print("</tr>");
						$cont = 0;
					}
				}
				?>
			</table>
		</div>
		<div id="seccion-2" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right style="width:180px;">
						<b style="font-weight: bold;">Actividad Operacional: </b>
					</td>
					<td>
						<select class="no-editable" name="actividad_operacional" id="actividad_operacional" style='padding:1px; height:25px; width:115px;'>
							<option value="">Seleccione ...</option>
							<option value="S" <?php echo ($mriesgo->getCampo("actividad_operacional") == "S") ? "selected" : ""; ?>>SI</option>
							<option value="N" <?php echo ($mriesgo->getCampo("actividad_operacional") == "N") ? "selected" : ""; ?>>NO</option>
						</select>
					</td>
					<input type=hidden name="actividad_rutinaria" value="S">
				</tr>
				<tr>
					<td align=right>
						<b style="font-weight: bold;">Personal Directo:</b>
					</td>
					<td>
						<input type="text" name="expuesto_personaldirecto" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="<?php echo $mriesgo->getCampo("expuesto_personaldirecto"); ?>">
					</td>
					<input type=hidden name="expuesto_contratista" value=0>
				</tr>
				<tr>
					<input type=hidden name="expuesto_temporal" value=0>
					<input type=hidden name="expuesto_contratista" value=0>
					<td align=right>
						<b style="font-weight: bold;">Visitantes:</b>
					</td>
					<td>
						<input type="text" name="expuesto_visitantes" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="<?php echo $mriesgo->getCampo("expuesto_visitantes"); ?>">
					</td>
				</tr>
				<tr>
					<td align=right>
						<b style="font-weight: bold;">Personal Propio:</b>
					</td>
					<td>
						<input type="text" name="expuesto_estudiantes" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="<?php echo $mriesgo->getCampo("expuesto_estudiantes"); ?>">
					</td>
					<input type=hidden name="expuesto_practicantes" value=0>
				</tr>
				<tr>
					<td colspan="4">
						<hr>
					</td>

				</tr>
				<tr>
					<td align="right" colspan="3">
						<b style="font-weight: bold;">Total:</b>
					</td>
					<td>
						<input type="text" name="total_expuestos" style="width:100px;" value="0">
					</td>
					<input type=hidden name="exposicion_horasxdia" value=1>
				<tr>
			</table>
		</div>
		<div id="seccion-3" style="height:440px; overflow: auto;" class="tabs">
			<table width="100%" border=0 align=center>
				<tr>
					<td><span style="font-size:14px;font-weight:bold;">CONTROL</span></td>
					<td><span style="font-size:14px;font-weight:bold;">RANKING DE<br>IMPORTANCIA</span><br><button style="display: none;" id="limpiar" onclick="flimpiar()">Limpiar</button></td>
					<td><span style="font-size:14px;font-weight:bold;">PORCENTAJE<br> DE IMPACTO (%)</span></td>
					<td><span style="font-size:14px;font-weight:bold;">SE APLICA </span></td>
				</tr>

				<?php
				$pimpacto[1] = "20";
				$pimpacto[2] = "15";
				$pimpacto[3] = "15";
				$pimpacto[4] = "10";
				$pimpacto[5] = "10";
				$pimpacto[6] = "5";
				$pimpacto[7] = "5";
				$pimpacto[8] = "5";
				$pimpacto[9] = "3";
				$pimpacto[10] = "2";

				$i = 1;
				$consulta = BD::sql_query("SELECT * FROM controles_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
				while($row = BD::obtenerRegistro($consulta))
				{
					print('<tr>');
					print('<td><center>');
					print('		<input  title="Control ' . $i . ' que se debe aplicar"  onchange="cambiocontrol(this)" name="control' . $i . '" id="control' . $i . '" type=text value="'.$row["control"].'">');
					print('  </center></td>');
					print('	<td><center>');
					print('		<select title="1: Muy importante - 10: menos importante" name="sel' . $i . '" id="sel' . $i . '" onchange="cambiarranking(this)" style="width:50px;">');
					
					for ($j = 0; $j <= 10; $j++) {
						print('<option value="' . $j . '" '.($row["ranking"] == $j ? "selected" : "").'>' . $j . '</option>');
					}
					print('</select></center>');
					print('	</td>');
					print('	<td>');
					print('		<center><input id="impact' . $i . '" name="impact' . $i . '" style="width:50px;" type=text value="'.$row["porcentaje"].'"></center>');

					print('	</td>');
					print('	<td>');
					print('		<center><input onchange="evaluar(this)" type="checkbox" id="check' . $i . '" name="check' . $i . '" '.($row["aplica"] == "on" ? "checked" : "").'></center>');
					print('	</td>');
					print('<td><button type="button" class="btn"  id="limpiar' . $i .'" onclick="flimpiar(\''.$i.'\')">Limpiar <i class="fa fa-trash"></i></button></td>');
					print('</tr>');
					$i++;
				}
				$cont = $i;
				for (;$i <= 10; $i++) {
					//if ($i == 1) {
						print('<tr>');
						print('<td><center>');
						print('		<input style="'.($i > $cont ? "display: none;" : "").'" title="Control ' . $i . ' que se debe aplicar"  onchange="cambiocontrol(this)" name="control' . $i . '" id="control' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						print('		<select title="1: Muy importante - 10: menos importante" name="sel' . $i . '" id="sel' . $i . '" onchange="cambiarranking(this)" style="width:50px;'.($i > $cont ? "display: none;" : "").'">');
						for ($j = 0; $j <= 10; $j++) {
							if ($j == 0) {
								print('<option selected value="' . $j . '">' . $j . '</option>');
							} else {
								print('<option value="' . $j . '">' . $j . '</option>');
							}
						}
						print('</select></center>');
						print('	</td>');
						print('	<td>');
						print('		<center><input id="impact' . $i . '" name="impact' . $i . '" style="width:50px;'.($i > $cont ? "display: none;" : "").'" type=text value=""></center>');

						print('	</td>');
						print('	<td>');
						print('		<center><input onchange="evaluar(this)" type="checkbox" id="check' . $i . '" name="check' . $i . '" style="'.($i > $cont ? "display: none;" : "").'"></center>');
						print('	</td>');
						print('<td><button type="button" class="btn" style="'.($i > $cont ? "display: none;" : "").'" id="limpiar' . $i . '" onclick="flimpiar(\''.$i.'\')" >Limpiar <i class="fa fa-trash"></i></button></td>');
						print('</tr>');
				}
				print('<tr><td colspan=4><div id="resultado"></div></td></tr>');
				?>
			</table>
		</div>
		<div id="seccion-4" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right><b style="font-weight: bold;">Riesgo Expresado:</b></td>
					<td>
						<select class="no-editable" name="riesgo_expresado" id="riesgo_expresado" style='padding:1px; height:25px; width:220px;'>
							<option value="">Seleccione ...</option>
							<option value="S" <?php echo ($mriesgo->getCampo("riesgo_expresado") == "S") ? "selected" : ""; ?>>SI</option>
							<option value="N" <?php echo ($mriesgo->getCampo("riesgo_expresado") == "N") ? "selected" : ""; ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b style="font-weight: bold;">Probabilidad de Ocurrencia:</b></td>
					<td>
						<select name='mprobabilidad_id' id='mprobabilidad_id' onchange="nivelRiesgo();">
							<option value='5' <?php echo ($mclasificacion_riesgo["mprobabilidad_id"] == 5) ? "selected" : ""; ?>>RARO</option>
							<option value='1' <?php echo ($mclasificacion_riesgo["mprobabilidad_id"] == 1) ? "selected" : ""; ?>>IMPROBABLE</option>
							<option value='2' <?php echo ($mclasificacion_riesgo["mprobabilidad_id"] == 2) ? "selected" : ""; ?>>POSIBLE</option>
							<option value='3' <?php echo ($mclasificacion_riesgo["mprobabilidad_id"] == 3) ? "selected" : ""; ?>>PROBABLE</option>
							<option value='4' <?php echo ($mclasificacion_riesgo["mprobabilidad_id"] == 4) ? "selected" : ""; ?>>CASI CERTERO</option>
						</select>
					</td>
					<td align=right><b style="font-weight: bold;">Impacto :</b></td>
					<td>
						<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='mseveridad_id' id='mseveridad_id' onchange="nivelRiesgo();">
							<option value="">Seleccione...</option>
							<option value='5' <?php echo ($mclasificacion_riesgo["mseveridad_id"] == 5) ? "selected" : ""; ?>>INSIGNIFICANTE</option>
							<option value='1' <?php echo ($mclasificacion_riesgo["mseveridad_id"] == 1) ? "selected" : ""; ?>>MENOR</option>
							<option value='2' <?php echo ($mclasificacion_riesgo["mseveridad_id"] == 2) ? "selected" : ""; ?>>MODERADO</option>
							<option value='3' <?php echo ($mclasificacion_riesgo["mseveridad_id"] == 3) ? "selected" : ""; ?>>MAYOR</option>
							<option value='4' <?php echo ($mclasificacion_riesgo["mseveridad_id"] == 4) ? "selected" : ""; ?>>CATASTROFICO</option>
						</select>
					</td>
				</tr>
				<tr class="nivel" style="visibility:hidden;">
					<td align=right><b style="font-weight: bold;">Nivel de riesgo:</b></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div id="seccion-5" style="height:350px; overflow: auto;" class="tabs">
			<input type=hidden name="rcm_eliminacion" value=1>
			<input type=hidden name="rcm_sustitucion" value=1>
			<input type=hidden name="rcm_ctr_ingenieria" value=1>
			<input type=hidden name="rcm_ctr_administrativo" value=1>
			<input type=hidden name="rcm_senalizacion" value=1>
			<input type=hidden name="rcm_proteccionpersonal" value=1>
			<table width="100%">
				<tr>
					<td colspan="4"><span style="font-size:18px;font-weight:bold;">Medida del tratamiento</span></td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra1" <?php echo (!empty($tratamiento_riesgo_op["opciontra1"]) && $tratamiento_riesgo_op["opciontra1"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Evitar el riesgo</span>
					</td>

					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra2" <?php echo (!empty($tratamiento_riesgo_op["opciontra2"]) && $tratamiento_riesgo_op["opciontra2"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Aceptar o aumentar el riesgo en busca de una oportunidad</span>
					</td>

				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra3" <?php echo (!empty($tratamiento_riesgo_op["opciontra3"]) && $tratamiento_riesgo_op["opciontra3"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Eliminar la fuente de riesgo</span>
					</td>

					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra4" <?php echo (!empty($tratamiento_riesgo_op["opciontra4"]) && $tratamiento_riesgo_op["opciontra4"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;"> 
						<span style="margin-left:7px;margin-top:0px !important;">Modificar la probabilidad</span>
					</td>

				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra5" <?php echo (!empty($tratamiento_riesgo_op["opciontra5"]) && $tratamiento_riesgo_op["opciontra5"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Modificar las consecuencias</span>
					</td>

					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra6" <?php echo (!empty($tratamiento_riesgo_op["opciontra6"]) && $tratamiento_riesgo_op["opciontra6"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Compartir el riesgo</span>
					</td>

				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra7" <?php echo (!empty($tratamiento_riesgo_op["opciontra7"]) && $tratamiento_riesgo_op["opciontra7"] == "on") ? "checked" : "" ?>>
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Retener el riesgo con base en una decision informada.</span>
					</td>

				</tr>
			</table>
		</div>
		<div id="seccion-6" style="height:450px; overflow: auto;" class="tabs">
			<table>
				<tr>
					<td><span style="font-size:14px;font-weight:bold;">RECOMENDACION</span></td>
					<td><span style="font-size:14px;font-weight:bold;">RESPONSABLE</span></td>
					<td><span style="font-size:14px;font-weight:bold;">CARGO</span></td>
					<td><span style="font-size:14px;font-weight:bold;">FRECUENCIA DE<BR>SEGUIMIENTO</span></td>
				</tr>
				<?php

				$consulta = BD::sql_query("SELECT * FROM recomendaciones_riesgo_op WHERE riesgo_id = ". $mriesgo->id);
				$i = 1;
				while($row = BD::obtenerRegistro($consulta))
				{
					print('<tr>');
					print('<td><center>');
					print('		<input onchange="cambiocontrol2(this)" style="width:160px;" name="recome' . $i . '" id="recome' . $i . '" type=text value="'.$row["recomendacion"].'">');
					print('  </center></td>');
					print('<td><center>');
					print('		<input style="width:160px;" name="respon' . $i . '" id="respon' . $i . '" type=text value="'.$row["responsable"].'">');
					print('  </center></td>');
					print('<td><center>');
					print('		<input style="width:160px;"name="cargo' . $i . '" id="cargo' . $i . '" type=text value="'.$row["cargo"].'">');
					print('  </center></td>');
					print('	<td><center>');
					print('		<select name="frec' . $i . '" id="frec' . $i . '" onchange="cambiar(this)" style="width:100px;">');
					print('		<option value="1" '.  ($row["frecuencia"] == "1" ? "selected" : "").'>Semanal</option>');
					print('		<option value="2" '.  ($row["frecuencia"] == "2" ? "selected" : "").'>Quincenal</option>');
					print('		<option value="3" '.  ($row["frecuencia"] == "3" ? "selected" : "").'>Mensual</option>');
					print('		<option value="4" '.  ($row["frecuencia"] == "4" ? "selected" : "").'>Bimestral</option>');
					print('		<option value="5" '.  ($row["frecuencia"] == "5" ? "selected" : "").'>Trimestral</option>');
					print('		<option value="6" '.  ($row["frecuencia"] == "6" ? "selected" : "").'>Semestral</option>');
					print('		<option value="7" '.  ($row["frecuencia"] == "7" ? "selected" : "").'>Anual</option>');
					print('		</select></center>');
					print('	</td>');
					print('	</tr>');
					$i++;
				}
				$cont = $i;
				for (; $i <= 10; $i++) {
						print('<tr>');
						print('<td><center>');
						print('		<input onchange="cambiocontrol2(this)" style="width:160px;'.($i > $cont ? "display: none;" : "").'" name="recome' . $i . '" id="recome' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;'.($i > $cont ? "display: none;" : "").'" name="respon' . $i . '" id="respon' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;'.($i > $cont ? "display: none;" : "").'" name="cargo' . $i . '" id="cargo' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						print('		<select name="frec' . $i . '" id="frec' . $i . '" onchange="cambiar(this)" style="width:100px;'.($i > $cont ? "display: none;" : "").'">');
						print('		<option value="1">Semanal</option>');
						print('		<option value="2">Quincenal</option>');
						print('		<option value="3">Mensual</option>');
						print('		<option value="4">Bimestral</option>');
						print('		<option value="5">Trimestral</option>');
						print('		<option value="6">Semestral</option>');
						print('		<option value="7">Anual</option>');
						print('		</select></center>');
						print('	</td>');
						print('	</tr>');
				}
				?>
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
		$("#ventana4").dialog("destroy");
		$("#ventana4").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar �tem",
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
				"Guardar": function() {
					Swal.fire({
						title: '�Confirma?',
						text: '',
						type: 'question',
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',

					}).then(function(res) {
						if (res.value)
							$("#formEditarItem").submit();
					});
				},
				"Cancelar": function() {
					$("#ventana4").html("");
					$("#ventana4").dialog("destroy");
				}
			},
			close: function() {
				$(".datetimepicker").remove();
				$("#ventana4").html("");
				$("#ventana4").dialog("destroy");

			}
		});

		$("#tabs-matriz").tabs({
			select: function(event, ui) {}
		});
		$(".no-editable,#mprobabilidad_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$(".editable").select2({
			placeholder: 'Seleccione...',
			tags: true,
			allowClear: true,
			createTag: function(params) {
				var term = $.trim(params.term);
				if (term === '') {
					return null;
				}

				return {
					id: term,
					text: term,
					newTag: true
				}
			}
		});

		$("#dtpFecha").datetimepicker({
			language: 'es',
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
			maxDate: '-1',
			minView: 2
		});
		$("#agente_riesgo_id").on("change", function() {
			var agenteriesgo_id = $(this).val();
			json = "";
			$.ajax({
				url: "<?php echo DIR_WEB; ?>get_combo.php",
				data: {
					agenteriesgo_id: agenteriesgo_id
				},
				type: "POST",
				dataType: "JSON",
				success: function(data) {
					json = data;

					$("#peligros_riesgo").empty();
					//$("#posibles_efectos").empty();
					$("#peligros_riesgo").append("<option value=''>Seleccione...</option>");
					if (agenteriesgo_id != "") {
						$.each(json, function(i, member) {
							if(json[i].id == "<?php echo $mriesgo->getCampo("mpeligro_id"); ?>")
								$("#peligros_riesgo").append("<option value='" + i + "' selected>" + json[i].peligros + "</option>");
							else
								$("#peligros_riesgo").append("<option value='" + i + "'>" + json[i].peligros + "</option>");
						});
						console.log($("#peligros_riesgo"));
						$(".peligro_view").css("visibility", "visible");
						$("#peligros_riesgo").select2({
							placeholder: 'Seleccione...',
							allowClear: true
						});
						$("#peligros_riesgo").on("change", function() {
							var id = $(this).val();
							$("#mpeligro_id").val(json[id].id);
							$("#posibles_efectos").empty().append(json[id].consecuencia);
						});
					} else {
						$(".peligro_view").css("visibility", "hidden");
					}

				}
			});
		});

		$("#agente_riesgo_id").trigger("change");

		$("#formEditarItem").validate({
			rules: {
				centrotrabajo_id: "required",
				proceso_id: "required",
				actividad_id: "required",
				tarea_id: "required",
				oficio_id: "required",
				fuente: "required",
				posibles_efectos: "required",
				actividad_operacional: "required",
				actividad_rutinaria: "required",
				expuesto_personaldirecto: {
					required: true,
					digits: true
				},
				expuesto_contratista: {
					required: true,
					digits: true
				},
				expuesto_temporal: {
					required: true,
					digits: true
				},
				expuesto_visitantes: {
					required: true,
					digits: true
				},
				expuesto_estudiantes: {
					required: true,
					digits: true
				},
				expuesto_practicantes: {
					required: true,
					digits: true
				},
				exposicion_horasxdia: {
					required: true,
					digits: true
				},
				ctr_fuente_ingenieria_id: "required",
				ctr_medio_ingenieria_id: "required",
				ctr_medio_senalizacion_id: "required",
				ctr_persona_proteccion_id: "required",
				ctrpersona_capacitacion_id: "required",
				ctr_persona_monitoreo_id: "required",
				ctr_persona_estandarizacion_id: "required",
				ctr_persona_procedimiento_id: "required",
				ctr_persona_observacion_id: "required",
				riesgo_expresado: "required",
				mprobabilidad_id: "required",
				mseveridad_id: "required",
				rcm_eliminacion: "required",
				rcm_sustitucion: "required",
				rcm_ctr_ingenieria: "required",
				rcm_ctr_administrativo: "required",
				rcm_senalizacion: "required",
				rcm_proteccionpersonal: "required",
				responsable: "required",
				observaciones_generales: "required"
			},
			messages: {
				centrotrabajo_id: "",
				proceso_id: "",
				actividad_id: "",
				tarea_id: "",
				oficio_id: "",
				fuente: "",
				posibles_efectos: "",
				actividad_operacional: "",
				actividad_rutinaria: "",
				expuesto_personaldirecto: "",
				expuesto_contratista: "",
				expuesto_temporal: "",
				expuesto_visitantes: "",
				expuesto_estudiantes: "",
				expuesto_practicantes: "",
				exposicion_horasxdia: "",
				ctr_fuente_ingenieria_id: "",
				ctr_medio_ingenieria_id: "",
				ctr_medio_senalizacion_id: "",
				ctr_persona_proteccion_id: "",
				ctrpersona_capacitacion_id: "",
				ctr_persona_monitoreo_id: "",
				ctr_persona_estandarizacion_id: "",
				ctr_persona_procedimiento_id: "",
				ctr_persona_observacion_id: "",
				riesgo_expresado: "",
				mprobabilidad_id: "",
				mseveridad_id: "",
				rcm_eliminacion: "",
				rcm_sustitucion: "",
				rcm_ctr_ingenieria: "",
				rcm_ctr_administrativo: "",
				rcm_senalizacion: "",
				rcm_proteccionpersonal: "",
				responsable: "",
				observaciones_generales: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditarItem').ajaxSubmit({
					success: function(resp) {
						hideMessage();
						console.log(resp);
						if (/^ok$/.test(resp)) {
							$("#ventana4").dialog("destroy");
							$("#lista-matriz").trigger("reloadGrid");
						} else {
							mensaje("Error", "No fue posible actualizar el �tem de la matriz de riesgo<br />" + resp, "error");
						}
					}
				});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});

		$("#formEditarItem").css("font-size", "14px");
		totalExpuestos();
		nivelRiesgo();
		hideMessage();
	});

	function totalExpuestos(elemento) {
		if ($(elemento).val() == "") $(elemento).val(0);
		var personal_directo = parseInt(($("input[name=expuesto_personaldirecto]").val() != "") ? $("input[name=expuesto_personaldirecto]").val() : "0");
		var contratista = parseInt(($("input[name=expuesto_contratista]").val() != "") ? $("input[name=expuesto_contratista]").val() : "0");
		var temporal = parseInt(($("input[name=expuesto_temporal]").val() != "") ? $("input[name=expuesto_temporal]").val() : "0");
		var visitantes = parseInt(($("input[name=expuesto_visitantes]").val() != "") ? $("input[name=expuesto_visitantes]").val() : "0");
		var estudiantes = parseInt(($("input[name=expuesto_estudiantes]").val() != "") ? $("input[name=expuesto_estudiantes]").val() : "0");
		var practicantes = parseInt(($("input[name=expuesto_practicantes]").val() != "") ? $("input[name=expuesto_practicantes]").val() : "0");
		var total = personal_directo + contratista + temporal + visitantes + estudiantes + practicantes;
		$("input[name=total_expuestos]").val(total);
	}

	function solo_numeros(str) {
		if (!/^([0-9])*$/.test(str.value))
			$("input[name=" + str.name + "]").val("");
	}

	function nivelRiesgo() {
		var probabilidad = $("#mprobabilidad_id").val();
		var severidad = $("#mseveridad_id").val();
		if (probabilidad != "" && severidad != "") {
			$.ajax({
				url: "<?php echo DIR_WEB; ?>nivel_riesgo.php",
				data: {
					probabilidad: probabilidad,
					severidad: severidad
				},
				type: "POST",
				success: function(data) {
					$(".nivel").css("visibility", "visible");
					$(".nivel td:nth-child(2)").empty().append(data);
				}
			});
		} else {
			$(".nivel").css("visibility", "hidden");
		}
	}

	function cambiarValor(elemento) {
		if ($(elemento).val() == "0")
			$(elemento).val("");
	}

	function cambiocontrol(selectObject) //cambio en la descripci�n de un control
	{
		var value = selectObject.value;
		var num = selectObject.name.slice(7);
		var num2 = parseInt(num) + 1;

		cadena1 = "control" + num2;
		cadena2 = "sel" + num2;
		cadena3 = "impact" + num2;
		cadena4 = "check" + num2;
		cadena5 = "limpiar" + num2;
		document.getElementById(cadena1).style.display = "block";
		document.getElementById(cadena2).style.display = "block";
		document.getElementById(cadena3).style.display = "block";
		document.getElementById(cadena4).style.display = "block";
		document.getElementById(cadena5).style.display = "block";


	}


	function cambiocontrol2(selectObject) //cambio en la descripci�n de un control
	{
		var value = selectObject.value;
		var num = selectObject.name.slice(6);
		var num2 = parseInt(num) + 1;

		cadena1 = "recome" + num2;
		cadena2 = "respon" + num2;
		cadena3 = "cargo" + num2;
		cadena4 = "frec" + num2;

		document.getElementById(cadena1).style.display = "block";
		document.getElementById(cadena2).style.display = "block";
		document.getElementById(cadena3).style.display = "block";
		document.getElementById(cadena4).style.display = "block";
	}


	function cambiar(selectObject) //evaluar el estado y valor de los selects de ranking
	{
		var value = selectObject.value;
	}

/*
	function cambiarranking(selectObject) {
		var value = selectObject.value;
		const porcenta = ["20", "15", "15", "10", "10", "5", "5", "5", "3", "2"];
		str2 = selectObject.name.slice(3)

		thestring = "impact" + str2;
		thestring2 = "sel" + str2;

		str3 = value - 1;
		console.log(str3);
		if(str3 >= 0)
			document.getElementById(thestring).value = porcenta[str3];

		valorenqueva = document.getElementById(thestring2).options[document.getElementById(thestring2).selectedIndex].value;

		for (j = 1; j <= 10; j++) {
			lacadena = "sel" + j;
			valorseleccionadoj = document.getElementById(lacadena).options[document.getElementById(lacadena).selectedIndex].text;

			if ((j != str2) && (valorseleccionadoj == 0)) {
				var x = document.getElementById(thestring2);
				//document.getElementById(lacadena).remove(x.selectedIndex);
			}
		}
		//document.getElementById("limpiar").style.display = "block";
	}*/


	/*function flimpiar(id) //evaluar el estado de los check box
	{
		/*for (i = 1; i <= 10; i++) {
			opc = "sel" + i;
			document.getElementById(opc).options.length = 0;
			for (j = 0; j <= 10; j++) {
				var x = document.getElementById(opc);
				var option = document.createElement("option");
				option.text = j;
				x.add(option);
			}
		}*/
		/*$("#control" + id).val("");
		$("#sel" + id).val("0");
		$("#impact" + id).val("");
		$("#check" + id).attr("checked",false);
		cambiarranking(document.getElementById("sel" + id));
		evaluar(document.getElementById("check" + id));
		//document.getElementById("limpiar").style.display = "none";
	}


	function evaluar(selectObject) //evaluar el estado de los check box
	{
		var value = selectObject.value;

		contador = 0;
		impacto = 0;
		for (i = 1; i <= 10; i++) {
			cadena = "check" + i;
			cadena2 = "impact" + i;
			if (document.getElementById(cadena).checked == true) {
				contador++;
				impacto = impacto + parseInt(document.getElementById(cadena2).value);
			}
		}


		probabilidadevento = (100 - impacto);
		cadenenaprob = "";
		if (probabilidadevento < 18) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% ACEPTABLE</h1></td><td></td></tr></table>";
			document.getElementById('mprobabilidad_id').value = "5";
		}
		if ((probabilidadevento >= 18) && (probabilidadevento < 36)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% TOLERABLE</h1></td><td></td></tr></table>";
			document.getElementById('mprobabilidad_id').value = "1";
		}
		if ((probabilidadevento >= 36) && (probabilidadevento < 54)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% MODERADO</h1></td><td></td></tr></table>";
			document.getElementById('mprobabilidad_id').value = "2";
		}
		if ((probabilidadevento >= 54) && (probabilidadevento <= 72)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% IMPORTANTE </h1></td><td></td></tr></table>";
			document.getElementById('mprobabilidad_id').value = "3";
		}
		if (probabilidadevento > 72) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% INACEPTABLE</h1></td><td></td></tr></table>";
			document.getElementById('mprobabilidad_id').value = "4";
		}

	}*/

	function cambiarranking(selectObject) {
		
		var value = selectObject.value;
		const porcenta = ["20", "15", "15", "10", "10", "5", "5", "5", "3", "2"];
		str2 = selectObject.name.slice(3);
		//console.log("indice actual: " + str2);
		
		thestring = "impact" + str2;
		thestring2 = "sel" + str2;

		str3 = value - 1;
		if(str3 >= 0)
			document.getElementById(thestring).value = porcenta[str3];
                 
		index = document.getElementById(thestring2).selectedIndex;
		
		for (j = 1; j <= 10; j++) {
			lacadena = "sel" + j;
			valorseleccionadoj = document.getElementById(lacadena).options[document.getElementById(lacadena).selectedIndex].text;
			if ((j != str2 && value != valorseleccionadoj)) {
				var x = document.getElementById(thestring2);
				document.getElementById(lacadena).options[index].disabled = true;
			}
		}
		//document.getElementById("limpiar").style.display = "block";
	}


	function flimpiar(id) //evaluar el estado de los check box
	{
		thestring2 = "sel" + id;  
		var elemento = document.getElementById(thestring2);
		var value = elemento.value;    
		index = elemento.selectedIndex;
		for (i = 1; i <= 10; i++) {
			/*opc = "sel" + i;
			if("sel" + id == opc)
				continue;
			//document.getElementById(opc).options.length = 0;
			var valor_seleccionado = document.getElementById(opc).options[document.getElementById(opc).selectedIndex].text;
			//$("#" + opc).empty();
			for (j = 0; j <= 10; j++) {
				var x = document.getElementById(opc);
				var option = document.createElement("option");
				option.text = j;
				x.add(option);
				if(valor_seleccionado == j)
					document.getElementById(opc).value=valor_seleccionado;
				
			}*/
			lacadena = "sel" + i;
			//valorseleccionadoj = document.getElementById(lacadena).options[document.getElementById(lacadena).selectedIndex].text;
			if ((j != str2 /*&& value != valorseleccionadoj*/)) {
				var x = document.getElementById(thestring2);
				document.getElementById(lacadena).options[index].disabled = false;
			}
		}
		
		$("#control" + id).val("");
		$("#sel" + id).val("0");
		$("#impact" + id).val("");
		$("#check" + id).attr("checked",false);
		//eliminarSeleccionados();
		evaluar(document.getElementById("check" + id));
		//document.getElementById("limpiar").style.display = "none";
	}

	function eliminarSeleccionados()
	{
		for (let i = 1; i <= 10; i++) {
			let seleccionado = document.getElementById("sel" + i).options[document.getElementById("sel" + i).selectedIndex].text;
			if(seleccionado == 0)
				continue;
			for (let j = 1; j <= 10; j++) {
				if(i != j)
				{	
					//console.log(document.getElementById("sel" + j));
					let elemento = document.getElementById("sel" + j);
					if(elemento != null)
						elemento.remove(document.getElementById("sel" + i).selectedIndex);
				}
			}
		}
	}


	function evaluar(selectObject) //evaluar el estado de los check box
	{
		var value = selectObject.value;

		contador = 0;
		impacto = 0;
		for (i = 1; i <= 10; i++) {
			cadena = "check" + i;
			cadena2 = "impact" + i;
			if (document.getElementById(cadena).checked == true) {
				contador++;
				impacto = impacto + parseInt(document.getElementById(cadena2).value);
			}
		}


		probabilidadevento = (100 - impacto);
		cadenenaprob = "";
		if (probabilidadevento < 18) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% RARO</h1></td><td></td></tr></table>";
			$("#mprobabilidad_id").select2("val", "5");
			//document.getElementById('mprobabilidad_id').value = "5";
		}
		if ((probabilidadevento >= 18) && (probabilidadevento < 36)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% IMPROBABLE</h1></td><td></td></tr></table>";
			$("#mprobabilidad_id").select2("val", "1");
			//document.getElementById('mprobabilidad_id').value = "1";
		}
		if ((probabilidadevento >= 36) && (probabilidadevento < 54)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% POSIBLE</h1></td><td></td></tr></table>";
			$("#mprobabilidad_id").select2("val", "2");
			//document.getElementById('mprobabilidad_id').value = "2";
		}
		if ((probabilidadevento >= 54) && (probabilidadevento <= 72)) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% PROBABLE </h1></td><td></td></tr></table>";
			$("#mprobabilidad_id").select2("val", "3");
			//document.getElementById('mprobabilidad_id').value = "3";
		}
		if (probabilidadevento > 72) {
			document.getElementById("resultado").innerHTML = "<br><table width='100%'><tr><td><h4>Probabilidad que se materialize el Evento:</h4></td><td><h1>" + probabilidadevento + "% CASI CERTERO</h1></td><td></td></tr></table>";
			$("#mprobabilidad_id").select2("val", "4");
			//document.getElementById('mprobabilidad_id').value = "4";
		}

	}
</script>