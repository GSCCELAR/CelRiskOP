<?php
	define("iC", true);
	//define("DEBUG",true);
	require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");


	if (isset($_POST["mpeligro_id"]) && isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"])) {
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
		$sql = BD::sql_query("SELECT IFNULL(MAX(numero_item) + 1,1) AS numero_item FROM mriesgo WHERE  dispositivo_seguridad_id = ".$_POST["dispositivo_seguridad_id"]);
		if($f = BD::obtenerRegistro($sql))
			$_POST["numero_item"] = $f["numero_item"];
		$fecha_cambio = date("Y-m-d H:i:s");
		$_POST["fecha_inicio"] = date("Y-m-d H:i:s");
		$_POST["usuario_modifico"] = Aplicacion::getNombreCompleto();
		$_POST["fecha_modificacion"] = $fecha_cambio;
		$matriz_riesgo = new MatrizRiesgo($_POST);
		die ($matriz_riesgo->save() ? "ok" . $matriz_riesgo->id : BD::getLastError());
	}
	$agente_riesgo = "";
	if(isset($_POST["agenteriesgo_id"]))
		$agente_riesgo = $_POST["agenteriesgo_id"];

	$puesto_id = isset($_POST["puesto_id"]) ? $_POST["puesto_id"] : die("Error al recibir el ID del puesto");
	$sector_id = isset($_POST["sector_id"]) ? $_POST["sector_id"] : die("Error al recibir el ID del sector");
?>

<form method="post" name="formNuevoItem" id="formNuevoItem" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id="mpeligro_id" name="mpeligro_id">
	<input type="hidden" name="sector_id" id="sector_id" value="<?php echo $sector_id;?>">
	<input type="hidden" name="dispositivo_seguridad_id" id="dispositivo_seguridad_id" value="<?php echo $puesto_id; ?>">
	
	<div id="tabs-matriz" >
	<ul>
		<li><a href="#seccion-0" >Sección 1</a></li>
		<li><a href="#seccion-1" >Sección 2</a></li>
		<li><a href="#seccion-2">Sección 3</a></li>
		<li><a href="#seccion-3">Sección 4</a></li>
		<li><a href="#seccion-4">Sección 5</a></li>
		<li><a href="#seccion-5">Sección 6</a></li>
		<li><a href="#seccion-6">Sección 7</a></li>
	</ul>


	<div id="seccion-0" style="height:350px; overflow: auto;" class="tabs">
			<table cellpadding=3 border=0 width="100%">
				<tr>
					<td valign=top align=right style="width:25%"><b style="font-weight: bold;">Escenarios para este Riesgo</b></td>
					<td valign=top>
						<?php
							/*
							SELECT sede_escenario.id, escenario.nombre, COUNT( sede_escenario_riesgo.riesgo_id ) cantidad_riesgos
							FROM escenario
							JOIN sede_escenario ON escenario.id = sede_escenario.escenario_id
							LEFT OUTER JOIN sede_escenario_riesgo ON sede_escenario.id = sede_escenario_riesgo.sede_escenario_id
							WHERE 1 =1
							AND sede_escenario.sede_id =417
							GROUP BY sede_escenario.id
							*/
							$result = BD::sql_query("SELECT *
							FROM escenario
							WHERE id
							IN (
							
							SELECT escenario_id
							FROM sede_escenario
							WHERE sede_id =414
							)
							ORDER BY escenario.nombre ASC
							LIMIT 0 , 30
							");
							while ($row = BD::obtenerRegistro($result)) 							
							{
								print("<input type='checkbox' name='v".$row['id']."' value='Bike'> ".$row['nombre']."<br>");								
							}
						?>
					</td>
				</tr>
				
			</table>
	</div>
	<div  id="seccion-1" style="height:350px; overflow: auto;" class="tabs">
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right><b style="font-weight: bold;">Proceso: </b></td>
				<td colspan="3">
					<select class="no-editable" style='padding:1px; height:25px; width:100%;' name='proceso_id' id='proceso_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Proceso();
							$lista->writeOptions(-1, array("id", "nombre"), array()," ORDER BY nombre");
						?>
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
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ACTIVIDAD")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<!--
				<tr>
				<td align=right><b style="font-weight: bold;">Tarea:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='tarea_id' id='tarea_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "TAREA")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td align=right><b style="font-weight: bold;">Oficio:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='oficio_id' id='oficio_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "OFICIO")," ORDER BY nombre");
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
							$lista->writeOptions(-1, array("id", "nombre"),array("tipo_lista_codigo" => "AGENTE_RIESGO")," ORDER BY nombre");
						?>
					</select>
				</td>		
				<td align=right valign=top><b style="font-weight: bold;">Fuente:</b></td>
				<td> <textarea name="fuente" id="fuente"  rows="3" style="width:260px;resize:none;"></textarea></td>	
			</tr>
			<tr class="peligro_view" style="visibility:hidden;">
				<td align=right valign=top><b style="font-weight: bold;">Riesgo:</b></td>
				<td valign=top> 
					<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='peligros_riesgo' id='peligros_riesgo'>
					</select>
				</td>
				<td align=right valign=top><b style="font-weight: bold;">Efecto o Consecuencia:</b></td>
				<td> <textarea name="posibles_efectos" id="posibles_efectos"   rows="3" style="width:260px;resize:none;" readonly></textarea></td>
			</tr>
		</table>
	</div>
	<div  id="seccion-2" style="height:350px; overflow: auto;" class="tabs">

	<!--	<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right><b style="font-weight: bold;">Actividad Operacional: </b></td>
				<td >
					<select class="no-editable" name="actividad_operacional" id="actividad_operacional" style='padding:1px; height:25px; width:115px;'>
						<option value="">Seleccione ...</option>
						<option value="S">SI</option>
						<option value="N">NO</option>
					</select>
				</td>
				<td align=right><b style="font-weight: bold;">Actividad Rutinaria: </b></td>
				<td>
					<select class="no-editable" name="actividad_rutinaria" id="actividad_rutinaria" style='padding:1px; height:25px; width:115px;'>
						<option value="">Seleccione ...</option>
						<option value="S">SI</option>
						<option value="N">NO</option>
					</select>
				</td>
			</tr>
			<tr>
				<td ><span style="font-size:18px;font-weight:bold;">Expuestos</span></td>
			</tr>
			<tr>				
				<td align=right><b style="font-weight: bold;">Personal Directo:</b></td>
				<td><input type="text" name="expuesto_personaldirecto" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				<td align=right><b style="font-weight: bold;">Contratista:</b></td>
				<td><input type="text" name="expuesto_contratista" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Temporal:</b></td>
				<td><input type="text" name="expuesto_temporal" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				<td align=right><b style="font-weight: bold;">Visitantes:</b></td>
				<td><input type="text" name="expuesto_visitantes" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Estudiantes:</b></td>
				<td><input type="text" name="expuesto_estudiantes" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				<td align=right><b style="font-weight: bold;">practicantes:</b></td>
				<td><input type="text" name="expuesto_practicantes" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Total:</b></td>
				<td><input type="text" name="total_expuestos" style="width:100px;" value="0" readonly></td>
				<td align=right><b style="font-weight: bold;">Tiempo de Exposición (horas/dia):</b></td>
				<td><input type="text" name="exposicion_horasxdia" style="width:100px;"></td>
		</table>
		-->
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right><b style="font-weight: bold;">Actividad Operacional: </b></td>
				<td >
					<select class="no-editable" name="actividad_operacional" id="actividad_operacional" style='padding:1px; height:25px; width:115px;'>
						<option value="">Seleccione ...</option>
						<option value="S">SI</option>
						<option value="N">NO</option>
					</select>
				</td>
				<!--<td align=right><b style="font-weight: bold;">Actividad Rutinaria: </b></td>
				<td>
					<select class="no-editable" name="actividad_rutinaria" id="actividad_rutinaria" style='padding:1px; height:25px; width:115px;'>
						<option value="">Seleccione ...</option>
						<option value="S">SI</option>
						<option value="N">NO</option>
					</select>
				</td>
				-->
			</tr>
			<tr>				
				<td align=right><b style="font-weight: bold;">Personal Directo:</b></td>
				<td><input type="text" name="expuesto_personaldirecto" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				
			</tr>
			<tr>				
				<td align=right><b style="font-weight: bold;">Visitantes:</b></td>
				<td><input type="text" name="expuesto_personaldirecto" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				
			</tr>
			<tr>				
				<td align=right><b style="font-weight: bold;">personal Propio:</b></td>
				<td><input type="text" name="expuesto_personaldirecto" style="width:100px;"  onblur="totalExpuestos(this);" onfocus="cambiarValor(this);" onkeyup="solo_numeros(this);" value="0"></td>
				
			</tr>

			<tr>
				<td align=right><b style="font-weight: bold;">Total:</b></td>
				<td><input type="text" name="total_expuestos" style="width:100px;" value="0" readonly></td>
				<!--
				<td align=right><b style="font-weight: bold;">Tiempo de Exposición (horas/dia):</b></td>
				<td><input type="text" name="exposicion_horasxdia" style="width:100px;"></td>
				-->
			</tr>
		</table>
	</div>
	<div id="seccion-3" style="height:450px; overflow: auto;" class="tabs"> 
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td colspan="4"><span style="font-size:18px;font-weight:bold;">Controles Existentes</span></td>
			</tr>


			<!--
			<tr>
				<td align=right><b style="font-weight: bold;">Fuente Ingeniería:</b></td>
				<td>
					<select class="editable" style='padding:1px; height:25px; width:220px;' name='ctr_fuente_ingenieria_id' id='ctr_fuente_ingenieria_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_FUENTE_ING")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Medio Señalización:</b></td>
				<td>
					<select class="editable" style='padding:1px; height:25px; width:220px;' name='ctr_medio_senalizacion_id' id='ctr_medio_senalizacion_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_MEDIO_SENAL")," ORDER BY nombre");
						?>
					</select>
				</td>
				<td align=right><b style="font-weight: bold;">Medio Ingeniería:</b></td>
				<td>
					<select class="editable" style='padding:1px; height:25px; width:220px;' name='ctr_medio_ingenieria_id' id='ctr_medio_ingenieria_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_MEDIO_ING")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td ><span style="font-size:18px;font-weight:bold;">Persona</span></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Elementos de protección personal:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctr_persona_proteccion_id' id='ctr_persona_proteccion_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_PROT")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
				<td align=right><b style="font-weight: bold;">Capacitación:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctrpersona_capacitacion_id' id='ctrpersona_capacitacion_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_CAP")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Monitoreo:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctr_persona_monitoreo_id' id='ctr_persona_monitoreo_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_MONI")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Estandarización:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctr_persona_estandarizacion_id' id='ctr_persona_estandarizacion_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_ESTAN")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>	
			<tr>
				<td align=right><b style="font-weight: bold;">Procedimiento:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctr_persona_procedimiento_id' id='ctr_persona_procedimiento_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_PROCED")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Observación comportamiento:</b></td>
				<td colspan="3">
					<select class="editable" style='padding:1px; height:25px; width:100%;' name='ctr_persona_observacion_id' id='ctr_persona_observacion_id'>
					<option value="">Seleccione...</option>
						<?php
							$lista = new Lista();
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "CTR_PERSONA_OBSER")," ORDER BY nombre");
						?>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td><span style="font-size:14px;font-weight:bold;">Controles</span></td>
				<td><span style="font-size:14px;font-weight:bold;">Ranking de <br>Importancia</span></td>
				<td><span style="font-size:14px;font-weight:bold;">porcentaje de <br>impacto</span></td>
				<td><span style="font-size:14px;font-weight:bold;">SI</span></td>
			</tr>

			<?php
			for($i=1;$i<=10;$i++)
			{
				print('<tr>');
				print('<td>');
				print('		<input type=text>');
				print('	</td>');
				print('	<td>');
				print('		<select name="cars'.$i.'" id="cars'.$i.'" >');
				print('			<option value="1">1</option>');
				print('			<option value="2">2</option>');
				print('			<option value="3">3</option>');
				print('			<option value="4">4</option>');
				print('			<option value="5">5</option>');
				print('			<option value="6">6</option>');
				print('			<option value="7">3</option>');
				print('			<option value="8">4</option>');
				print('			<option value="9">5</option>');
				print('			<option value="10">6</option>');  					
				print('		</select>');
				print('	</td>');
				print('	<td>');
				print('		<input type=text>');
				print('	</td>');
				print('	<td>');
				print('		<input type="checkbox" id="vehicle1" name="vehicle1" value="Bike">');
				print('	</td>');
				print('</tr>');
			}	
			?>


		</table>
	</div>
	<div  id="seccion-4" style="height:350px; overflow: auto;" class="tabs">
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
					<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='mprobabilidad_id' id='mprobabilidad_id' onchange="nivelRiesgo();">
					<option value="">Seleccione...</option>
						<?php
							$lista = new Probabilidad();
							$lista->writeOptions(-1, array("id", "calificacion"));
						?>
					</select>
				</td>
				<td align=right><b style="font-weight: bold;">Impacto:</b></td>
				<td>
					<select class="no-editable" style='padding:1px; height:25px; width:220px;' name='mseveridad_id' id='mseveridad_id' onchange="nivelRiesgo();">
					<option value="">Seleccione...</option>
						<?php
							$lista = new Severidad();
							$lista->writeOptions(-1, array("id", "calificacion"));
						?>
					</select>
				</td>
			</tr>
			<tr class="nivel" style="visibility:hidden;"> 
				<td align=right><b style="font-weight: bold;">Nivel de riesgo:</b></td>
				<td ></td>
			</tr>
		</table>
	</div>
	<div  id="seccion-5" style="height:350px; overflow: auto;" class="tabs">
		<!--
		<table cellpadding=3 border=0 width="100%">			
			<tr>
				<td colspan="4"><span style="font-size:18px;font-weight:bold;">Recomendaciones para el control de riesgo</span></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Eliminación:</b></td>
				<td><textarea name="rcm_eliminacion" id="rcm_eliminacion"  rows="3" style="width:220px;resize:none;"></textarea></td>
				<td align=right><b style="font-weight: bold;">Sustitución:</b></td>
				<td><textarea name="rcm_sustitucion" id="rcm_sustitucion"  rows="3" style="width:220px;resize:none;"></textarea></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Control de ingeniería:</b></td>
				<td><textarea name="rcm_ctr_ingenieria" id="rcm_ctr_ingenieria"  rows="3" style="width:220px;resize:none;"></textarea></td>
				<td align=right><b style="font-weight: bold;">Control Administrativo:</b></td>
				<td><textarea name="rcm_ctr_administrativo" id="rcm_ctr_administrativo"  rows="3" style="width:220px;resize:none;"></textarea></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Señalización:</b></td>
				<td><textarea name="rcm_senalizacion" id="rcm_senalizacion"  rows="3" style="width:220px;resize:none;"></textarea></td>
				<td align=right><b style="font-weight: bold;">Elementos de Protección Personal:</b></td>
				<td><textarea name="rcm_proteccionpersonal" id="rcm_proteccionpersonal"  rows="3" style="width:220px;resize:none;"></textarea></td>
			</tr>			
		</table>
		-->

		<table width="50%">			
			<tr>
				<td colspan="2"><span style="font-size:18px;font-weight:bold;">Medida del tratamiento</span></td>
			</tr>
			
			<tr>
				
				<td align=right><b style="font-weight: bold;">Evitar el riesgo</b></td>
				<td><input type="checkbox" id="opcion1" name="opcion1" value="0"></td>
			</tr>			
			<tr>
				
				<td align=right><b style="font-weight: bold;">Aceptar el riesgo</b></td>
				<td><input type="checkbox" id="opcion2" name="opcion2" value="0"></td>
			</tr>			
			<tr>				
				<td align=right><b style="font-weight: bold;">Minimizar o mitigar el riesgo</b></td>
				<td><input type="checkbox" id="opcion3" name="opcion3" value="0"></td>
			</tr>			
			<tr>				
				<td align=right><b style="font-weight: bold;">Compartir o transferir el riesgo</b></td>
				<td><input type="checkbox" id="opcion4" name="opcion4" value="0"></td>
			</tr>			
		</table>
	</div>
	<div  id="seccion-6" style="height:350px; overflow: auto;" class="tabs">
		<!--
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right style="width:15%"><b style="font-weight: bold;">Cargo del Responsable:</b></td>
				<td><input type="text" name="responsable" id="responsable"  style="width:100%;"></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Observaciones Generales:</b></td>
				<td><textarea name="observaciones_generales" id="observaciones_generales"  rows="6" style="width:100%;resize:none;"></textarea></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Fecha de Seguimiento:</b></td>
				<td><div class="input-append date form_datetime" id='dtpFecha'>
					<input style='width:80px;' type=text name='fecha_seguimiento' value='<?php echo date("Y-m-d"); ?>'>
					<span class="add-on"><i class="icon-calendar"></i></span></td>
			</tr>
		</table>
		-->
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right style="width:15%"><b style="font-weight: bold;">Recomendaciones de seguridad y/o controles:</b></td>
				<td><textarea name="recomendaciones" id="recomendaciones"  rows="4" style="width:100%;resize:none;"></textarea></td>
			</tr>
			<!--
			<tr>
				<td align=right><b style="font-weight: bold;">Actividades:</b></td>
				<td><textarea name="observaciones_generales" id="observaciones_generales"  rows="3" style="width:100%;resize:none;"></textarea></td>
			</tr>
			-->
			<tr>
				<td align=right><b style="font-weight: bold;">Responsables:</b></td>
				<td><textarea name="responsables" id="responsables"  rows="3" style="width:100%;resize:none;"></textarea></td>
			</tr>
			<tr>
				<td align=right><b style="font-weight: bold;">Frecuencia de seguimiento:</b></td>
				<td>
				
					<!--
						<input style='width:80px;' type=text name='fecha_seguimiento' value='<?php echo date("Y-m-d"); ?>'>
					-->
					<select name="cars" id="cars">
  						<option value="1">Semanal</option>
						<option value="2">Quincenal</option>
						<option value="3">Mensual</option>
						<option value="4">Bimestral</option>
						<option value="5">Trimestral</option>
						<option value="6">Semestral</option>
						<option value="7">Anual</option>
						
					</select>
				
				</td>
			</tr>
			<!--<tr>
				<td align=right><b style="font-weight: bold;">Fecha de Seguimiento:</b></td>
				<td><div class="input-append date form_datetime" id='dtpFecha'>
					<input style='width:80px;' type=text name='fecha_seguimiento' value='<?php echo date("Y-m-d"); ?>'>
					<span class="add-on"><i class="icon-calendar"></i></span></td>
			</tr>
			-->
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
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			buttons: {
                "Guardar": function() {
					Swal.fire({
						title : '¿Confirma?',
						text : '',
						type : 'question',
						showCancelButton : true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar'
					}).then(function (res) {
						if (res.value)
						$("#formNuevoItem").submit();
					});
              	},
              	"Cancelar": function() {
                    $("#ventana4").html("");
                    $("#ventana4").dialog("destroy");
                }
	        },
	      	close : function () {
				$(".datetimepicker").remove();
	      		$("#ventana4").html("");
				$("#ventana4").dialog("destroy");
	      	}
		});
		$( "#tabs-matriz" ).tabs({
			select: function(event, ui) {
			}
		});
		$(".no-editable").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$(".editable").select2({
			placeholder: 'Seleccione...',
			tags: true,
			allowClear: true,
			createTag: function (params) {
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
				
					$("#peligros_riesgo").empty();
					$("#posibles_efectos").empty();
					$("#peligros_riesgo").append("<option value=''>Seleccione...</option>");
					if(agenteriesgo_id != ""){
						$.each(json, function(i, member) {
						$("#peligros_riesgo").append("<option value='"+i+"'>"+json[i].peligros+"</option>");
						});
						console.log($("#peligros_riesgo"));
						$(".peligro_view").css("visibility","visible");
						$("#peligros_riesgo").select2({
							placeholder: 'Seleccione...',
							allowClear: true
						});
						$("#peligros_riesgo").on("change", function(){
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

		$("#formNuevoItem").validate({
			rules: {
				proceso_id : "required",
				actividad_id : "required",
				tarea_id : "required",
				oficio_id : "required",
				fuente : "required",
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
				observaciones_generales: "required",
				fecha_seguimiento: "required"
			},
			messages: {
				proceso_id : "",
				actividad_id : "",
				tarea_id : "",
				oficio_id : "",
				fuente : "",
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
					}
					else {
						mensaje("Error", "No fue posible registrar la matriz de riesgo<br />", "error");
					}
				}});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});
		$("#formNuevoItem").css("font-size", "14px");

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