<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);
//define("MODO_DEBUG", true);	
$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID de la matriz de riesgo");

$mriesgo = new MatrizRiesgo();
if (!$mriesgo->load($id)) die("error al cargar la información de la matriz de riesgo");

if (isset($_POST["mpeligro_id"]) && isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"])) {

	$mriesgo->setCampo("dispositivo_seguridad_id",($mriesgo->getCampo("dispositivo_seguridad_id") != '') ? $mriesgo->getCampo("dispositivo_seguridad_id"): "_NULL");
	$sql = BD::sql_query("SELECT id FROM mclasificacion_riesgo WHERE mprobabilidad_id = ". Seguridad::escapeSQL($_POST["mprobabilidad_id"])." AND mseveridad_id = ". Seguridad::escapeSQL($_POST["mseveridad_id"]));
	if($row = BD::obtenerRegistro($sql))
		$_POST["mclasificacion_riesgo_id"] = $row["id"];
	//$_POST["fecha_seguimiento"] = date("Y-m-d H:i:s");

	if(!is_numeric($_POST["actividad_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'ACTIVIDAD'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["actividad_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["actividad_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["tarea_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'TAREA'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["tarea_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["tarea_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["oficio_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'OFICIO'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["oficio_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["oficio_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_persona_proteccion_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_PROT'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_persona_proteccion_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_persona_proteccion_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_fuente_ingenieria_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_FUENTE_ING'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_fuente_ingenieria_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_fuente_ingenieria_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_medio_ingenieria_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_MEDIO_ING'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_medio_ingenieria_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_medio_ingenieria_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_medio_senalizacion_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_MEDIO_SENAL'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_medio_senalizacion_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_medio_senalizacion_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctrpersona_capacitacion_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_CAP'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctrpersona_capacitacion_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctrpersona_capacitacion_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_persona_monitoreo_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_MONI'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_persona_monitoreo_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_persona_monitoreo_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_persona_estandarizacion_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_ESTAN'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_persona_estandarizacion_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_persona_estandarizacion_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_persona_procedimiento_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_PROCED'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_persona_procedimiento_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_persona_procedimiento_id"] = $lista->id;
		}
	}
	if(!is_numeric($_POST["ctr_persona_observacion_id"]))
	{
		$sql = BD::sql_query("SELECT id FROM tipo_lista WHERE codigo = 'CTR_PERSONA_OBSER'");
		if($f = BD::obtenerRegistro($sql))
		{
			$parametros = array("nombre" => $_POST["ctr_persona_observacion_id"], "tipo_lista_id" => $f["id"]);
			$lista = new Lista($parametros);
			if($lista->save())
				$_POST["ctr_persona_observacion_id"] = $lista->id;
		}
	}
	//$mriesgo_nuevo = new MatrizRiesgo($_POST);
	//if($mriesgo_nuevo->save()) BD::getLastError();
	//$mriesgo->setCampo("orden_cronologico",1);
	die ($mriesgo->update($_POST) ? "ok": BD::getLastError());
}

$agenteriesgo_id = "";
$sql = BD::sql_query("SELECT agenteriesgo_id FROM mpeligro WHERE id =".$mriesgo->getCampo("mpeligro_id"));
if($row = BD::obtenerRegistro($sql)){
	$agenteriesgo_id = $row["agenteriesgo_id"];
}
$mprobabilidad_id = "";
$mseveridad_id = "";
$sql = BD::sql_query("SELECT mprobabilidad_id, mseveridad_id FROM mclasificacion_riesgo WHERE id =".$mriesgo->getCampo("mclasificacion_riesgo_id"));
if($row = BD::obtenerRegistro($sql))
{
	$mprobabilidad_id = $row["mprobabilidad_id"];
	$mseveridad_id = $row["mseveridad_id"];
}
	

?>
<form method="post" name="formEditarMatrizBase" id="formEditarMatrizBase" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
<input type="hidden" id="id" name="id" value="<?php echo $mriesgo->id; ?>">
	<input type="hidden" id="mpeligro_id" name="mpeligro_id" value="<?php echo $mriesgo->getCampo("mpeligro_id"); ?>">
	<div id="tabs">
		<ul>
			<li><a href="#seccion-1">Sección 1</a></li>
			<li><a href="#seccion-2">Sección 2</a></li>
			<li><a href="#seccion-3">Sección 3</a></li>
			<li><a href="#seccion-4">Sección 4</a></li>
			<li><a href="#seccion-5">Sección 5</a></li>
			<li><a href="#seccion-6">Sección 6</a></li>
		</ul>
		<div id="seccion-1" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right><b>Sector:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:100%;' name='sector_id' id='sector_id'>
							<option value="">Seleccione...</option>
							<?php
								$lista = new Lista();
								$lista->writeOptions($mriesgo->getCampo("sector_id"), array("id", "nombre"), array("tipo_lista_codigo" => "SECTOR")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Proceso: </b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:100%;' name='proceso_id' id='proceso_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Proceso();
							$lista->writeOptions($mriesgo->getCampo("proceso_id"), array("id", "nombre"),array()," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Actividad:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:100%;' name='actividad_id' id='actividad_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("actividad_id"), array("id", "nombre"), array("tipo_lista_codigo" => "ACTIVIDAD")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Tarea:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:100%;' name='tarea_id' id='tarea_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("tarea_id"), array("id", "nombre"), array("tipo_lista_codigo" => "TAREA")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td align=right><b>Oficio:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:100%;' name='oficio_id' id='oficio_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("oficio_id"), array("id", "nombre"), array("tipo_lista_codigo" => "OFICIO")," ORDER BY nombre");
							?>
						</select>
					</td>
				
				</tr>
				<tr>
					<td align=right valign=top><b>Factor Riesgo:</b></td>
					<td valign=top>
						<select style='padding:1px; height:25px; width:100%;' name='agente_riesgo_id' id='agente_riesgo_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($agenteriesgo_id, array("id", "nombre"), array("tipo_lista_codigo" => "AGENTE_RIESGO")," ORDER BY nombre");
							?>
						</select>
					</td>
					<td align=right valign=top><b>Fuente:</b></td>
					<td> <textarea name="fuente" id="fuente" rows="3" style="width:260px;resize:none;"><?php echo $mriesgo->getCampo("fuente") ?></textarea></td>
				</tr>
				<tr class="peligro_view" style="visibility:hidden;">
					<td align=right valign=top><b>Riesgo:</b></td>
					<td valign=top>
						<select style='padding:1px; height:25px; width:220px;' name='peligro_riesgo' id='peligro_riesgo'>
						</select>
					</td>
					<td align=right valign=top><b>Posibles Efectos:</b></td>
					<td> <textarea name="posibles_efectos" id="posibles_efectos" rows="3" style="width:260px;resize:none;" readonly></textarea></td>
				</tr>
			</table>
		</div>
		<div id="seccion-2" style="height:350px; overflow: auto;" class="tabs">
			<table>
				<tr>
					<td align=right><b>Actividad Operacional: </b></td>
					<td>
						<select name="actividad_operacional" id="actividad_operacional" style='padding:1px; height:25px; width:115px;'>
							<?php $selected = "";
							   
							?>
							<option value="">Seleccione ...</option>
							<option value="S" <?php echo ($mriesgo->getCampo("actividad_operacional") == "S") ? "selected": ""; ?>>SI</option>
							<option value="N" <?php echo ($mriesgo->getCampo("actividad_operacional") == "N") ? "selected": ""; ?>>NO</option>
						</select>
					</td>
					<td align=right><b>Actividad Rutinaria: </b></td>
					<td>
						<select name="actividad_rutinaria" id="actividad_rutinaria" style='padding:1px; height:25px; width:115px;'>
							<option value="">Seleccione ...</option>
							<option value="S" <?php echo ($mriesgo->getCampo("actividad_rutinaria") == "S") ? "selected": ""; ?>>SI</option>
							<option value="N" <?php echo ($mriesgo->getCampo("actividad_rutinaria") == "N") ? "selected": ""; ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
					<td ><span style="font-size:18px;font-weight:bold;">Expuestos</span></td>
				</tr>
				<tr>
					<td align=right><b>Personal Directo:</b></td>
					<td><input type="text" name="expuesto_personaldirecto" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_personaldirecto"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" ></td>
					<td align=right><b>Contratista:</b></td>
					<td><input type="text" name="expuesto_contratista" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_contratista"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);"></td>
				</tr>
				<tr>
					<td align=right><b>Temporal:</b></td>
					<td><input type="text" name="expuesto_temporal" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_temporal"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);"></td>
					<td align=right><b>Visitantes:</b></td>
					<td><input type="text" name="expuesto_visitantes" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_visitantes"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);"></td>
				</tr>
				<tr>
					<td align=right><b>Estudiantes:</b></td>
					<td><input type="text" name="expuesto_estudiantes" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_estudiantes"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);"></td>
					<td align=right><b>practicantes:</b></td>
					<td><input type="text" name="expuesto_practicantes" style="width:100px;" value="<?php echo $mriesgo->getCampo("expuesto_practicantes"); ?>" onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);"></td>
				</tr>
				<tr>
					<td align=right><b>Total:</b></td>
					<td><input type="text" name="total_expuestos" style="width:100px;" readonly></td>
					<td align=right><b>Tiempo de Exposición (horas/dia):</b></td>
					<td><input type="text" name="exposicion_horasxdia" style="width:100px;" value="<?php echo $mriesgo->getCampo("exposicion_horasxdia"); ?>"></td>
			</table>
		</div>
		<div id="seccion-3" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td colspan="4"><span style="font-size:18px;font-weight:bold;">Controles Existentes</span></td>
				</tr>
				<tr>
					<td align=right><b>Fuente Ingeniería:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:220px;' name='ctr_fuente_ingenieria_id' id='ctr_fuente_ingenieria_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_fuente_ingenieria_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_FUENTE_ING")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Medio Ingeniería:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:220px;' name='ctr_medio_ingenieria_id' id='ctr_medio_ingenieria_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_medio_ingenieria_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_MEDIO_ING")," ORDER BY nombre");
							?>
						</select>
					</td>
					<td align=right><b>Medio Seńalización:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:220px;' name='ctr_medio_senalizacion_id' id='ctr_medio_senalizacion_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_medio_senalizacion_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_MEDIO_SENAL")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td ><span style="font-size:18px;font-weight:bold;">Persona</span></td>
				</tr>
				<tr>
					<td align=right><b>Elementos de protección personal:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctr_persona_proteccion_id' id='ctr_persona_proteccion_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_persona_proteccion_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_PROT")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Capacitación:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctrpersona_capacitacion_id' id='ctrpersona_capacitacion_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctrpersona_capacitacion_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_CAP")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
					<td align=right><b>Monitoreo:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctr_persona_monitoreo_id' id='ctr_persona_monitoreo_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_persona_monitoreo_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_MONI")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Estandarización:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctr_persona_estandarizacion_id' id='ctr_persona_estandarizacion_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_persona_estandarizacion_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_ESTAN")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Procedimiento:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctr_persona_procedimiento_id' id='ctr_persona_procedimiento_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_persona_procedimiento_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_PROCED")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Observación comportamiento:</b></td>
					<td colspan="3">
						<select style='padding:1px; height:25px; width:570px;' name='ctr_persona_observacion_id' id='ctr_persona_observacion_id'>
							<option value="">Seleccione...</option>
							<?php
							$lista = new Lista();
							$lista->writeOptions($mriesgo->getCampo("ctr_persona_observacion_id"), array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_OBSER")," ORDER BY nombre");
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<div id="seccion-4" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right><b>Riesgo Expresado:</b></td>
					<td>
						<select name="riesgo_expresado" id="riesgo_expresado" style='padding:1px; height:25px; width:220px;' >
							<option value="">Seleccione ...</option>
							<option value="S" <?php echo ($mriesgo->getCampo("riesgo_expresado") == "S") ? "selected": ""; ?>>SI</option>
							<option value="N" <?php echo ($mriesgo->getCampo("riesgo_expresado") == "N") ? "selected": ""; ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align=right><b>Probabilidad de Ocurrencia:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:220px;' name='mprobabilidad_id' id='mprobabilidad_id' onchange="nivelRiesgo();">
							<option value="">Seleccione...</option>
							<?php
							$lista = new Probabilidad();
							$lista->writeOptions($mprobabilidad_id, array("id", "calificacion"));
							?>
						</select>
					</td>
					<td align=right><b>Severidad:</b></td>
					<td>
						<select style='padding:1px; height:25px; width:220px;' name='mseveridad_id' id='mseveridad_id' onchange="nivelRiesgo();">
							<option value="">Seleccione...</option>
							<?php
							$lista = new Severidad();
							$lista->writeOptions($mseveridad_id, array("id", "calificacion"));
							?>
						</select>
					</td>
				</tr>
				<tr class="nivel" style="visibility:hidden;"> 
					<td align=right><b>Nivel de riesgo:</b></td>
					<td ></td>
				</tr>
			</table>
		</div>
		<div id="seccion-5" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td colspan="4"><span style="font-size:18px;font-weight:bold;">Recomendaciones para el control de riesgo</span></td>
				</tr>
				<tr>
					<td align=right><b>Eliminación:</b></td>
					<td><textarea name="rcm_eliminacion" id="rcm_eliminacion" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_eliminacion"); ?></textarea></td>
					<td align=right><b>Sustitución:</b></td>
					<td><textarea name="rcm_sustitucion" id="rcm_sustitucion" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_sustitucion"); ?></textarea></td>
				</tr>
				<tr>
					<td align=right><b>Control de ingeniería:</b></td>
					<td><textarea name="rcm_ctr_ingenieria" id="rcm_ctr_ingenieria" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_ctr_ingenieria"); ?></textarea></td>
					<td align=right><b>Control Administrativo:</b></td>
					<td><textarea name="rcm_ctr_administrativo" id="rcm_ctr_administrativo" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_ctr_administrativo"); ?></textarea></td>
				</tr>
				<tr>
					<td align=right><b>Seńalización:</b></td>
					<td><textarea name="rcm_senalizacion" id="rcm_senalizacion" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_senalizacion"); ?></textarea></td>
					<td align=right><b>Elementos de Protección Personal:</b></td>
					<td><textarea name="rcm_proteccionpersonal" id="rcm_proteccionpersonal" rows="3" style="width:220px;resize:none;"><?php echo $mriesgo->getCampo("rcm_proteccionpersonal"); ?></textarea></td>
				</tr>
			</table>
		</div>
		<div id="seccion-6" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td align=right style="width:15%"><b>Cargo del Responsable:</b></td>
					<td><input type="text" name="responsable" id="responsable" style="width:100%;" value="<?php echo $mriesgo->getCampo("responsable"); ?>"></td>
				</tr>
				<tr>	
					<td align=right><b>Observaciones Generales:</b></td>
					<td><textarea name="observaciones_generales" id="observaciones_generales"  style="width:100%;resize:none;"><?php echo $mriesgo->getCampo("observaciones_generales"); ?></textarea></td>
				</tr>
				<tr>
					<td align=right><b style="font-weight: bold;">Fecha de Seguimiento:</b></td>
					<td>
						<div class="input-append date form_datetime" id='dtpFecha'>
						<input style='width:80px;' type=text name='fecha_seguimiento' value="<?php echo Fecha::getFecha($mriesgo->getCampo("fecha_seguimiento"),"Y-m-d"); ?>">
						<span class="add-on"><i class="icon-calendar"></i></span>
					</td>
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
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar matriz",
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
						title: 'żConfirma?',
						text: '',
						type: 'question',
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',

					}).then(function(res) {
						if (res.value)
							$("#formEditarMatrizBase").submit();
					});
				},
				"Cancelar": function() {
					$("#ventana2").html("");
					$("#ventana2").dialog("destroy");
				}
			},
			close: function() {
				$(".datetimepicker").remove();
				$("#ventana2").html("");
				$("#ventana2").dialog("destroy");
			}
		});

		$( "#tabs" ).tabs({
			select: function(event, ui) {
			}
		});
		$("#sector_id,#centrotrabajo_id,#peligro_id,#actividad_rutinaria,#proceso_id,#actividad_operacional,#riesgo_expresado,#mprobabilidad_id,#mseveridad_id,#agente_riesgo_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#actividad_id,#tarea_id,#oficio_id,#ctr_fuente_ingenieria_id,#ctr_medio_ingenieria_id,#ctr_medio_senalizacion_id,#ctr_persona_proteccion_id,#ctrpersona_capacitacion_id,#ctr_persona_monitoreo_id,#ctr_persona_estandarizacion_id,#ctr_persona_procedimiento_id,#ctr_persona_observacion_id").select2({
			placeholder: 'Seleccione...',
			tags: true,
			allowClear: true,
			createTag: function (params) {
				console.log(params);
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
			language:  'es',
			format : 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight : true,
			maxDate : '-1',
			minView : 2
		});
		$("#agente_riesgo_id").on("change", function(){
			var agenteriesgo_id = $(this).val();
			json = "";
			$.ajax({
				url: "<?php echo DIR_WEB; ?>get_combo.php",
				data: {agenteriesgo_id:agenteriesgo_id },
				type: "POST",
				dataType: "JSON",
				success: function(data){
					json = data;
					$("#peligro_riesgo").empty();
					$("#posibles_efectos").empty();
					$("#peligro_riesgo").append("<option value=''>Seleccione...</option>");
					if(agenteriesgo_id != ""){
						$.each(json, function(i, member) {
						if(json[i].id == "<?php echo $mriesgo->getCampo("mpeligro_id"); ?>"){
							$("#peligro_riesgo").append("<option value='"+i+"' selected>"+json[i].peligros+"</option>");
							$("#posibles_efectos").empty().append(json[i].consecuencia);
						}
						else
							$("#peligro_riesgo").append("<option value='"+i+"'>"+json[i].peligros+"</option>");
						});
						$(".peligro_view").css("visibility","visible");
						$("#peligro_riesgo").select2({
							placeholder: 'Seleccione...',
							allowClear: true
						});
						$("#peligro_riesgo").on("change", function(){
							var id = $(this).val();
							$("#mpeligro_id").val(json[id].id);
							$("#posibles_efectos").empty().append(json[id].consecuencia);
						});
					}else{
						$(".peligro_view").css("visibility","hidden");
					}
				
				}
			});
		});
		$("#agente_riesgo_id").trigger("change");

		$("#formEditarMatrizBase").validate({
			rules: {
				proceso_id : "required",
				actividad_id : "required",
				tarea_id : "required",
				oficio_id : "required",
				fuente : "required",
				sector_id: "required",
				posibles_efectos : "required",
				actividad_operacional : "required",
				actividad_rutinaria : "required",
				expuesto_personaldirecto : {
											required: true,
											digits: true
											},
				expuesto_contratista :  {
										required: true,
										digits: true
										},
				expuesto_temporal :  {
									required: true,
									digits: true
									},
				expuesto_visitantes :  {
										required: true,
										digits: true
										},
				expuesto_estudiantes :  {
										required: true,
										digits: true
										},
				expuesto_practicantes :  {
										required: true,
										digits: true
										},
				exposicion_horasxdia :  {
										required: true,
										digits: true
										},
				ctr_fuente_ingenieria_id : "required",
				ctr_medio_ingenieria_id : "required",
				ctr_medio_senalizacion_id : "required",
				ctr_persona_proteccion_id : "required",
				ctrpersona_capacitacion_id : "required",
				ctr_persona_monitoreo_id : "required",
				ctr_persona_estandarizacion_id : "required",
				ctr_persona_procedimiento_id : "required",
				ctr_persona_observacion_id : "required",
				riesgo_expresado : "required",
				mprobabilidad_id : "required",
				mseveridad_id : "required",
				rcm_eliminacion : "required",
				rcm_sustitucion : "required",
				rcm_ctr_ingenieria : "required",
				rcm_ctr_administrativo : "required",
				rcm_senalizacion : "required",
				rcm_proteccionpersonal : "required",
				responsable: "required",
				observaciones_generales: "required"
			},
			messages: {
				proceso_id : "",
				actividad_id : "",
				tarea_id : "",
				oficio_id : "",
				fuente : "",
				sector_id: "",
				posibles_efectos : "",
				actividad_operacional : "",
				actividad_rutinaria : "",
				expuesto_personaldirecto : "",
				expuesto_contratista : "",
				expuesto_temporal : "",
				expuesto_visitantes : "",
				expuesto_estudiantes : "",
				expuesto_practicantes : "",
				exposicion_horasxdia : "",
				ctr_fuente_ingenieria_id : "",
				ctr_medio_ingenieria_id : "",
				ctr_medio_senalizacion_id : "",
				ctr_persona_proteccion_id : "",
				ctrpersona_capacitacion_id : "",
				ctr_persona_monitoreo_id : "",
				ctr_persona_estandarizacion_id : "",
				ctr_persona_procedimiento_id : "",
				ctr_persona_observacion_id : "",
				riesgo_expresado : "",
				mprobabilidad_id : "",
				mseveridad_id : "",
				rcm_eliminacion : "",
				rcm_sustitucion : "",
				rcm_ctr_ingenieria : "",
				rcm_ctr_administrativo : "",
				rcm_senalizacion : "",
				rcm_proteccionpersonal : "",
				responsable: "",
				observaciones_generales: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditarMatrizBase').ajaxSubmit({
					success: function(resp) {
						hideMessage();
						console.log(resp);
						if (/^ok$/.test(resp)) {
							$("#ventana2").dialog("destroy");
							$("#lista").trigger("reloadGrid");
						} else {
							mensaje("Error", "No fue posible actualizar la matriz de riesgo<br />", "error");
						}
					}
				});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});

		$("#formEditarMatrizBase").css("font-size", "14px");
		totalExpuestos();
		nivelRiesgo();
		hideMessage();
	});

	function totalExpuestos(elemento)
	{
		if($(elemento).val() == "") $(elemento).val(0);
		var personal_directo = parseInt(($("input[name=expuesto_personaldirecto]").val() != "") ? $("input[name=expuesto_personaldirecto]").val() : "0");
		var contratista = parseInt(($("input[name=expuesto_contratista]").val() != "") ? $("input[name=expuesto_contratista]").val() : "0");
		var temporal = parseInt(($("input[name=expuesto_temporal]").val() != "") ? $("input[name=expuesto_temporal]").val() : "0");
		var visitantes = parseInt(($("input[name=expuesto_visitantes]").val() != "") ? $("input[name=expuesto_visitantes]").val() : "0");
		var estudiantes = parseInt(($("input[name=expuesto_estudiantes]").val() != "") ? $("input[name=expuesto_estudiantes]").val() : "0");
		var practicantes = parseInt(($("input[name=expuesto_practicantes]").val() != "") ? $("input[name=expuesto_practicantes]").val() : "0");
		var total = personal_directo + contratista + temporal + visitantes + estudiantes + practicantes;
		$("input[name=total_expuestos]").val(total);
	}

	function solo_numeros(str){
		 if(!/^([0-9])*$/.test(str.value))
			$("input[name="+str.name+"]").val("");
	}

	function nivelRiesgo()
	{
		var probabilidad = $("#mprobabilidad_id").val();
		var severidad = $("#mseveridad_id").val();
		if(probabilidad != "" && severidad != "")
		{
			$.ajax({
				url:"<?php echo DIR_WEB; ?>nivel_riesgo.php",
				data: {probabilidad:probabilidad, severidad:severidad},
				type: "POST",
				success:function(data){
					$(".nivel").css("visibility","visible");
					$(".nivel td:nth-child(2)").empty().append(data);
				}
			});
		}else
		{
			$(".nivel").css("visibility","hidden");
		}
	}

	function cambiarValor(elemento)
	{
		if($(elemento).val() == "0")
			$(elemento).val("");
	}

</script>