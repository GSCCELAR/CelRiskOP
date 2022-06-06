<?php
define("iC", true);
//define("DEBUG",true);
require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);
BD::changeInstancia("mysql");


if (isset($_POST["mpeligro_id"]) && isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"])) {
	$sql = BD::sql_query("SELECT id FROM mclasificacion_riesgo WHERE mprobabilidad_id = " . Seguridad::escapeSQL($_POST["mprobabilidad_id"]) . " AND mseveridad_id = " . Seguridad::escapeSQL($_POST["mseveridad_id"]));
	if ($row = BD::obtenerRegistro($sql))
		$_POST["mclasificacion_riesgo_id"] = $row["id"];
	//$_POST["fecha_seguimiento"] = date("Y-m-d H:i:s");
	//$_POST["actividad_id"] = 10;
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

	$sql = BD::sql_query("SELECT IFNULL(MAX(numero_item) + 1,1) AS numero_item FROM mriesgo_op WHERE  dispositivo_seguridad_id = " . $_POST["dispositivo_seguridad_id"]);
	if ($f = BD::obtenerRegistro($sql))
		$_POST["numero_item"] = $f["numero_item"];
	$fecha_cambio = date("Y-m-d H:i:s");
	$_POST["fecha_inicio"] = date("Y-m-d H:i:s");
	$_POST["usuario_modifico"] = Aplicacion::getNombreCompleto();
	$_POST["fecha_modificacion"] = $fecha_cambio;

	//$result = BD::sql_query("SELECT max(id) max from mriesgo");	//sacar el siguiente id para riesgos operativos
	$result = BD::sql_query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'celrisk' AND TABLE_NAME = 'mriesgo_op'");
	while ($row = BD::obtenerRegistro($result)) {
		$siguiente = $row['AUTO_INCREMENT'];
	}

	$sql = BD::sql_query("INSERT INTO tratamiento_riesgo_op (riesgo_id ,c1,c2,c3 ,c4 ,c5 ,c6 ,c7) VALUES ('" . $siguiente . "', '" . (isset($_POST['opciontra1']) ? $_POST['opciontra1'] : ""). "', '" . (isset($_POST['opciontra2']) ? $_POST['opciontra2'] : "") . "', '" . (isset($_POST['opciontra3']) ? $_POST['opciontra3'] : ""). "', '" . (isset($_POST['opciontra4']) ? $_POST['opciontra4'] : "") . "', '" . (isset($_POST['opciontra5']) ? $_POST['opciontra5'] : "") . "', '" . (isset($_POST['opciontra6']) ? $_POST['opciontra6'] : "") . "', '" . (isset($_POST['opciontra7']) ? $_POST['opciontra7'] : "") . "')");
	//$sql = BD::sql_query($cadenasql);
	for ($i = 1; $i <= 10; $i++) {
		$cadena = "recome" . $i;
		$cadena2 = "respon" . $i;
		$cadena3 = "cargo" . $i;
		$cadena4 = "frec" . $i;

		if (strlen($_POST[$cadena]) > 0) {
			$sql = BD::sql_query("insert into recomendaciones_riesgo_op (riesgo_id ,recomendacion ,responsable ,cargo ,frecuencia) VALUES ('" . $siguiente . "', '" . $_POST[$cadena] . "', '" . $_POST[$cadena2] . "', '" . $_POST[$cadena3] . "', '" . $_POST[$cadena4] . "')");
		}
	}

	//ingresamos registros en la tabla controles_riesgo_op
	for ($i = 1; $i <= 10; $i++) {
		$cadena = "control" . $i;
		$cadena2 = "sel" . $i;
		$cadena3 = "impact" . $i;
		$cadena4 = "check" . $i;

		if (strlen($_POST[$cadena]) > 0) {
			$sql = BD::sql_query("INSERT INTO controles_riesgo_op (riesgo_id , control ,ranking ,porcentaje ,aplica) VALUES ('" . $siguiente . "', '" . $_POST[$cadena] . "', '" . $_POST[$cadena2] . "', '" . $_POST[$cadena3] . "', '" . $_POST[$cadena4] . "')");
		}
	}

	if (!empty($_POST['escen'])) {
		foreach ($_POST['escen'] as $value) {
			$sql = BD::sql_query("INSERT INTO  escenario_riesgo_op (escenario_id,riesgo_id) values ('" . $value . "','" . $siguiente . "')");
		}
	}

	$matriz_riesgo = new MatrizRiesgo($_POST, $siguiente);
	die($matriz_riesgo->save() ? "ok" . $matriz_riesgo->id : BD::getLastError());

}
$agente_riesgo = "";
if (isset($_POST["agenteriesgo_id"]))
	$agente_riesgo = $_POST["agenteriesgo_id"];

$puesto_id = isset($_POST["puesto_id"]) ? $_POST["puesto_id"] : die("Error al recibir el ID del puesto");
$sector_id = isset($_POST["sector_id"]) ? $_POST["sector_id"] : die("Error al recibir el ID del sector");
?>

<form method="post" name="formNuevoItem" id="formNuevoItem" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id="mpeligro_id" name="mpeligro_id">
	<input type="hidden" name="sector_id" id="sector_id" value="<?php echo $sector_id; ?>">
	<input type="hidden" name="dispositivo_seguridad_id" id="dispositivo_seguridad_id" value="<?php echo $puesto_id; ?>">

	<div id="tabs-matriz">
		<ul>
			<!--<li><a href="#seccion-0">Sección 1  <?php print($puesto_id . " " . $sector_id); ?></a></li>-->
			<li><a href="#seccion-0">Sección 1</a></li>
			<li><a href="#seccion-1">Sección 2</a></li>
			<li><a href="#seccion-2">Sección 3</a></li>
			<li><a href="#seccion-3">Sección 4</a></li>
			<li><a href="#seccion-4">Sección 5</a></li>
			<li><a href="#seccion-5">Sección 6</a></li>
			<li><a href="#seccion-6">Sección 7</a></li>
		</ul>
		<div id="seccion-0" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right><b style="font-weight: bold;">Proceso:</b></td>
					<td colspan="3">
						<select class="no-editable" style='padding:1px; height:25px; width:100%;' name='proceso_id' id='proceso_id'>
							<option value="">Seleccione...</option>
							<option value="1">Vigilancia Fisica</option>
							<option value="2">Seg Electronica</option>
							<option value="3">Servicios Conexos</option>
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
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ACTIVIDAD", "nombre" => "Administrativa"), " OR id='276' OR id='380' ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right valign=top><b style="font-weight: bold;">Factor Riesgo:</b></td>
					<td valign=top>
						<select class="no-editable" style='padding:1px; height:25px; width:100%;' name='agente_riesgo_id' id='agente_riesgo_id' disabled>
							<option value="">Seleccione...</option>
							<?php
								$lista = new Lista();
								//$lista->writeOptions(-1, array("id", "nombre"),array("tipo_lista_codigo" => "AGENTE_RIESGO")," ORDER BY nombre");
								$lista->writeOptions(886, array("id", "nombre"), array("tipo_lista_codigo" => "AGENTE_RIESGO", "id" => "886"), " ORDER BY nombre");
							?>
						</select>
					</td>
					<td align=right valign=top><b style="font-weight: bold;">Fuente:</b></td>
					<td>
						<select name="fuente" id="fuente" style="width:260px;resize:none;">
							<option value="1">Interna</option>
							<option value="2">Externa</option>
							<option value="3">Ambas</option>
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
					<td> <textarea name="posibles_efectos" id="posibles_efectos" rows="3" style="width:260px;resize:none;"></textarea></td>
				</tr>
			</table>
		</div>
		<div id="seccion-1" style="height:350px; overflow: auto;" class="tabs">

			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td valign=top align=left colspan="4" style="padding-bottom:15px;"><b style="font-weight: bold;">Escenarios para este Riesgo</b></td>
				</tr>
				<?php
				$cont = 0;
				$result = BD::sql_query("SELECT * FROM escenario WHERE id IN (SELECT escenario_id	FROM sede_escenario	WHERE sede_id = (SELECT sede_id FROM dispositivo_seguridad WHERE id=" . $puesto_id . "))");
				while ($row = BD::obtenerRegistro($result)) {
					if ($cont == 0)
						print("<tr>");
					$cont++;
					print("<td valign=top style='padding-bottom:15px;'><input type='checkbox' name='escen[]' value='" . $row['id'] . "' style='margin-right:5px;margin-top:0px !important;'><span>" . ucfirst($row['nombre']) . "</span></td>");
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
							<option value="S">SI</option>
							<option value="N">NO</option>
						</select>
					</td>
					<input type=hidden name="actividad_rutinaria" value="S">
				</tr>
				<tr>
					<td align=right>
						<b style="font-weight: bold;">Personal Directo:</b>
					</td>
					<td>
						<input type="text" name="expuesto_personaldirecto" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0">
					</td>
					<input type=hidden name="expuesto_contratista" value=1>
				</tr>
				<tr>
					<input type=hidden name="expuesto_temporal" value=1>
					<input type=hidden name="expuesto_contratista" value=1>
					<td align=right>
						<b style="font-weight: bold;">Visitantes:</b>
					</td>
					<td>
						<input type="text" name="expuesto_visitantes" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0">
					</td>
				</tr>
				<tr>
					<td align=right>
						<b style="font-weight: bold;">Personal Propio:</b>
					</td>
					<td>
						<input type="text" name="expuesto_estudiantes" style="width:100px;" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0">
					</td>
					<input type=hidden name="expuesto_practicantes" value=1>
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
					<td><span style="font-size:14px;font-weight:bold;">RANKING DE<br>IMPORTANCIA</span><br></td>
					<td><span style="font-size:14px;font-weight:bold;">PORCENTAJE<br> DE IMPACTO (%)</span></td>
					<td><span style="font-size:14px;font-weight:bold;">SE APLICA </span></td>
					<td></td>
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


				for ($i = 1; $i <= 10; $i++) {
					if ($i == 1) {
						print('<tr>');
						print('<td><center>');
						print('		<input  title="Control ' . $i . ' que se debe aplicar"  onchange="cambiocontrol(this)" name="control' . $i . '" id="control' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						//print('		<select name="sel'.$i.'" onchange="cambiar(this)" style="width:50px;">');					
						print('		<select title="1: Muy importante - 10: menos importante" name="sel' . $i . '" id="sel' . $i . '" onchange="cambiarranking(this)" style="width:50px;">');
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
						print('		<center><input id="impact' . $i . '" name="impact' . $i . '" style="width:50px;" type=text value=""></center>');

						print('	</td>');
						print('	<td>');
						print('		<center><input onchange="evaluar(this)" type="checkbox" id="check' . $i . '" name="check' . $i . '"></center>');
						print('	</td>');
						print('<td><button type="button" class="btn"  id="limpiar' . $i .'" onclick="flimpiar('.$i.')">Limpiar <i class="fa fa-trash"></i></button></td>');
						print('</tr>');
					} else {
						print('<tr>');
						print('<td><center>');
						print('		<input  style="display: none;"  onchange="cambiocontrol(this)" name="control' . $i . '" id="control' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						print('		<select style="display: none;width:50px" id="sel' . $i . '" name="sel' . $i . '" onchange="cambiarranking(this)">');
						for ($j = 0; $j <= 10; $j++) {
							if ($j == 0) {
								print('<option selected value="' . $j . '">' . $j . '</option>');
							} else {
								print('<option value="' . $j . '">' . $j . '</option>');
							}
						}
						print('		</select></center>');
						print('	</td>');
						print('	<td>');
						print('		<center><input style="display: none;width:50px;" id="impact' . $i . '" name="impact' . $i . '" type=text value=""></center>');

						print('	</td>');
						print('	<td>');
						print('		<center><input style="display: none;" onchange="evaluar(this)" type="checkbox" id="check' . $i . '" name="check' . $i . '"></center>');
						print('	</td>');
						print('<td><button type="button" class="btn" style="display: none;" id="limpiar' . $i . '" onclick="flimpiar('.$i.')">Limpiar <i class="fa fa-trash"></i></button></td>');
						print('</tr>');
					}
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
							<option value="S">SI</option>
							<option value="N">NO</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b style="font-weight: bold;">Probabilidad de Ocurrencia:</b></td>
					<td>
						<select name='mprobabilidad_id' id='mprobabilidad_id' onchange="nivelRiesgo();" >
							<option value="">Seleccione...</option>
							<option value='5'>RARO</option>
							<option value='1'>IMPROBABLE</option>
							<option value='2'>POSIBLE</option>
							<option value='3'>PROBABLE</option>
							<option value='4'>CASI CERTERO</option>
							<?php

							/*$result = BD::sql_query("SELECT * FROM mprobabilidad ORDER BY orden DESC");
								while ($row = BD::obtenerRegistro($result)) 							
								{
									
										print("<option value='".$row["id"]."'>".$row["calificacion"]."</option>");
										


									
								}*/
							?>
							<?php
							//$lista = new Probabilidad();
							//$lista->writeOptions(-1, array("id", "calificacion"));
							?>
						</select>
					</td>
					<td align=right><b style="font-weight: bold;">Impacto :</b></td>
					<td>
						<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='mseveridad_id' id='mseveridad_id' onchange="nivelRiesgo();">
							<option value="">Seleccione...</option>
							<option value='5'>INSIGNIFICANTE</option>
							<option value='1'>MENOR</option>
							<option value='2'>MODERADO</option>
							<option value='3'>MAYOR</option>
							<option value='4'>CATASTROFICO</option>
							<?php
							//$lista = new Severidad();
							//$lista->writeOptions(-1, array("id", "calificacion"));
							?>
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
						<input type="checkbox" name="opciontra1">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Evitar el riesgo</span>
					</td>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra2">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Aceptar o aumentar el riesgo en busca de una oportunidad</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra3">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Eliminar la fuente de riesgo</span>
					</td>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra4">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Modificar la probabilidad</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra5">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Modificar las consecuencias</span>
					</td>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra6">
					</td>
					<td align=left style="padding-bottom: 15px;">
						<span style="margin-left:7px;margin-top:0px !important;">Compartir el riesgo</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;">
						<input type="checkbox" name="opciontra7">
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
					<td style="text-align:center;"><span style="font-size:12px;font-weight:bold;">RECOMENDACION</span></td>
					<td style="text-align:center;"><span style="font-size:12px;font-weight:bold;">RESPONSABLE</span></td>
					<td style="text-align:center;"><span style="font-size:12px;font-weight:bold;">CARGO</span></td>
					<td style="text-align:center;"><span style="font-size:12px;font-weight:bold;">FRECUENCIA DE<BR>SEGUIMIENTO</span></td>
					<td style="text-align:center;"><span style="font-size:12px;font-weight:bold;">EVIDENCIAS</span></td>
				</tr>
				<?php

				for ($i = 1; $i <= 10; $i++) {
					if ($i == 1) {
						print('<tr>');
						print('<td><center>');
						print('		<input onchange="cambiocontrol2(this)" style="width:160px;" name="recome' . $i . '" id="recome' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;" name="respon' . $i . '" id="respon' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;"name="cargo' . $i . '" id="cargo' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						print('		<select name="frec' . $i . '" id="frec' . $i . '" onchange="cambiar(this)" style="width:100px;">');
						print('		<option value="1">Semanal</option>');
						print('		<option value="2">Quincenal</option>');
						print('		<option value="3">Mensual</option>');
						print('		<option value="4">Bimestral</option>');
						print('		<option value="5">Trimestral</option>');
						print('		<option value="6">Semestral</option>');
						print('		<option value="7">Anual</option>');
						print('		</select></center>');
						print('	</td>');
						print('<td><img id="img' . $i . '" style=\'margin:3px;margin-left:10px;cursor:pointer;width:22px;height:22px;\' onclick=\'cargarImagenes('.$i.')\' src=\'imagenes/menu/imagenes.png\' title=\'Cargar Imagenes\'></td>');
						print('	</tr>');
					} else {
						print('<tr>');
						print('<td><center>');
						print('		<input onchange="cambiocontrol2(this)" style="width:160px;display: none" name="recome' . $i . '" id="recome' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;display: none" name="respon' . $i . '" id="respon' . $i . '" type=text>');
						print('  </center></td>');
						print('<td><center>');
						print('		<input style="width:160px;display: none" name="cargo' . $i . '" id="cargo' . $i . '" type=text>');
						print('  </center></td>');
						print('	<td><center>');
						print('		<select name="frec' . $i . '" id="frec' . $i . '" onchange="cambiar(this)" style="width:100px;display: none">');
						print('		<option value="1">Semanal</option>');
						print('		<option value="2">Quincenal</option>');
						print('		<option value="3">Mensual</option>');
						print('		<option value="4">Bimestral</option>');
						print('		<option value="5">Trimestral</option>');
						print('		<option value="6">Semestral</option>');
						print('		<option value="7">Anual</option>');
						print('		</select></center>');
						print('	</td>');
						print('<td><img id="img' . $i . '" style=\'margin:3px;margin-left:10px;cursor:pointer;width:22px;height:22px;display: none\' onclick=\'cargarImagenes('.$i.')\' src=\'imagenes/menu/imagenes.png\' title=\'Cargar Imagenes\'></td>');
						print('	</tr>');
					}
				}
				?>
			</table>
		</div>
	</div>
</form>
<script type="text/javascript">
	var json = "";
	$(document).ready(function() {
		$("#ventana4").dialog("destroy");
		$("#ventana4").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-plus-sign' /> &nbsp; Nuevo ítem",
			resizable: false,
			width: 800,
			open: function() {
				var t = $(this).parent(),
					w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			buttons: {
				"Guardar": function() {
					Swal.fire({
						title: '¿Confirma?',
						text: '',
						type: 'question',
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar'
					}).then(function(res) {
						if (res.value)
							$("#formNuevoItem").submit();
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
							//$("#posibles_efectos").empty().append(json[id].consecuencia);
						});
					} else {
						$(".peligro_view").css("visibility", "hidden");
					}

				}
			});
		});

		$("#formNuevoItem").validate({
			rules: {
				proceso_id: "required",
				actividad_id: "required",
				tarea_id: "required",
				oficio_id: "required",
				fuente: "required",
				posibles_efectos: "required",
				actividad_operacional: "required",
				actividad_rutinaria: {
					required: false,

				},
				expuesto_personaldirecto: {
					required: true,

				},
				expuesto_contratista: {
					required: false,

				},

				expuesto_temporal: {
					required: false,

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
					required: false
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
				rcm_eliminacion: {
					required: false
				},
				rcm_sustitucion: {
					required: false
				},
				rcm_ctr_ingenieria: {
					required: false
				},
				rcm_ctr_administrativo: {
					required: false
				},
				rcm_senalizacion: {
					required: false
				},
				rcm_proteccionpersonal: {
					required: false
				},
				responsable: "required",
				observaciones_generales: "required",
				fecha_seguimiento: "required"
			},
			messages: {
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
				observaciones_generales: "",
				fecha_seguimiento: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formNuevoItem').ajaxSubmit({
					success: function(resp) {
						hideMessage();
						if (/^ok\d{1,}/.test(resp)) {
							$("#ventana4").dialog("destroy");
							$("#lista-matriz").trigger("reloadGrid");
							//alert(resp);  //ok55247 ok+ el id del nuevo registro en la tabla mriesgo
						} else {
							$("#ventana4").dialog("destroy");
							$("#lista-matriz").trigger("reloadGrid");
							//alert(resp);  //ok55247 ok+ el id del nuevo registro en la tabla mriesgo

							//mensaje("Error", "No fue posible registrar la matriz de riesg <br />", "error");

						}
					}
				});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});
		$("#formNuevoItem").css("font-size", "14px");
		$("#agente_riesgo_id").trigger("change");
		hideMessage();
	});

	function totalExpuestos(elemento) {
		if ($(elemento).val() == "") $(elemento).val(0);
		var personal_directo = parseInt(($("input[name=expuesto_personaldirecto]").val() != "") ? $("input[name=expuesto_personaldirecto]").val() : "0");
		//var contratista = parseInt(($("input[name=expuesto_contratista]").val() != "") ? $("input[name=expuesto_contratista]").val() : "0");
		//var temporal = parseInt(($("input[name=expuesto_temporal]").val() != "") ? $("input[name=expuesto_temporal]").val() : "0");
		var visitantes = parseInt(($("input[name=expuesto_visitantes]").val() != "") ? $("input[name=expuesto_visitantes]").val() : "0");
		var estudiantes = parseInt(($("input[name=expuesto_estudiantes]").val() != "") ? $("input[name=expuesto_estudiantes]").val() : "0");
		//var practicantes = parseInt(($("input[name=expuesto_practicantes]").val() != "") ? $("input[name=expuesto_practicantes]").val() : "0");
		var total = personal_directo + visitantes + estudiantes;
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


	function cambiocontrol(selectObject) //cambio en la descripción de un control
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


	function cambiocontrol2(selectObject) //cambio en la descripción de un control
	{
		var value = selectObject.value;
		var num = selectObject.name.slice(6);
		var num2 = parseInt(num) + 1;

		cadena1 = "recome" + num2;
		cadena2 = "respon" + num2;
		cadena3 = "cargo" + num2;
		cadena4 = "frec" + num2;
		cadena5 = "img" + num2;

		document.getElementById(cadena1).style.display = "block";
		document.getElementById(cadena2).style.display = "block";
		document.getElementById(cadena3).style.display = "block";
		document.getElementById(cadena4).style.display = "block";
		document.getElementById(cadena5).style.display = "block";
	}


	function cambiar(selectObject) //evaluar el estado y valor de los selects de ranking
	{
		var value = selectObject.value;
	}


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

	function cargarImagenes(id) {
		showMessage();
		$("#ventana5").load("<?php echo DIR_WEB; ?>ver_nuevo.php", {
			item: id,
			puesto_id: "<?php echo $puesto_id; ?>"
		}, function(res) {
			hideMessage();
		});
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