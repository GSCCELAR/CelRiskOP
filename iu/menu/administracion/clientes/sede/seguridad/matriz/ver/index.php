<?php
	date_default_timezone_set('America/Bogota');
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);
	$validacion_fecha_inicio = isset($_POST["fecha_inicio"]) ? $_POST["fecha_inicio"] : date("Y-m-d H:i:s");
	if (!preg_match("/^\d{4}-\d{2}-\d{2}\s\d{2}\:\d{2}:\d{2}$/", $validacion_fecha_inicio)) die("Error en el formato de la fecha de validación");
	$puesto_id = (isset($_POST["puesto_id"]) && $_POST["puesto_id"] != "") ? intval($_POST["puesto_id"]) : die("Error al recibir el ID del puesto");
	$historico = (isset($_POST["historico"])) ? explode(",", $_POST["historico"]) : array();
	$items = array();
	foreach($historico as $i) {
		if (is_numeric($i))
			$items[] = intval($i);
	}

	$id_anterior = array();
	$id_siguiente = array();
	$id_actuales = array();
	$min_fecha = $max_fecha = 0;
	$max_fecha = date("Y-m-d H:i:s", strtotime($validacion_fecha_inicio . " +1 SECOND"));
	if (count($items) > 0) {
		$result_query = BD::sql_query("SELECT r.id, r.numero_item, c.mprobabilidad_id, c.mseveridad_id, c.mnivel_riesgo_id, r.mriesgo_id, r.fecha_inicio, fecha_fin 
			FROM mriesgo r, mclasificacion_riesgo c 
			WHERE r.estado = 1 AND r.mclasificacion_riesgo_id = c.id AND r.dispositivo_seguridad_id = " . Seguridad::escapeSQL($puesto_id) . " AND r.id IN (" . implode(",", $items) . ") 
				and fecha_inicio < '$validacion_fecha_inicio'  order by fecha_inicio asc" );
	}
	else {
		$result_query = BD::sql_query("SELECT r.id, r.numero_item, c.mprobabilidad_id, c.mseveridad_id, c.mnivel_riesgo_id, r.mriesgo_id, r.fecha_inicio , fecha_fin
			FROM mriesgo r, mclasificacion_riesgo c 
			WHERE  r.estado = 1 AND r.mclasificacion_riesgo_id = c.id AND r.dispositivo_seguridad_id = " . Seguridad::escapeSQL($puesto_id) . " AND fecha_fin is NULL 
				order by fecha_inicio asc" );
	}
	$items_matriz = array();
	$max_id = $nuevo_id = $min_fecha_id = -1;
	$fecha_fin_min = date("Y-m-d H:i:s");
	$limpiar_anterior = $limpia_siguiente = true;
	while($row = BD::obtenerRegistro($result_query)) {
		$id_actuales[$row["id"]] = $row["id"];
		$id_anterior[$row["id"]] = $row["id"];
		$id_siguiente[$row["id"]] = $row["id"];
		if (strtotime($min_fecha) < strtotime($row["fecha_inicio"])) {
			$min_fecha = $row["fecha_inicio"];
			$max_id = $row["id"];
			$nuevo_id = $row["mriesgo_id"];
			if ($nuevo_id != "")
				$limpiar_anterior = false;
		}
		if (strtotime($max_fecha) < strtotime($row["fecha_fin"])) {
			$max_fecha = $row["fecha_fin"];
			$limpia_siguiente = false;
		}
		
		if (strtotime($fecha_fin_min) > strtotime($row["fecha_fin"]) && $row["fecha_fin"] != "") {
			$min_fecha_id = $row["id"];
			$fecha_fin_min = $row["fecha_fin"];
		}
		$items_matriz[$row["mseveridad_id"]][$row["mprobabilidad_id"]][$row["mnivel_riesgo_id"]][] = "<span class='badge badge-light' style='cursor:pointer;margin-bottom:4px;' onclick='verItem(".$row["id"].")'>".$row["numero_item"]."</span>";
	}

	//Consulto si hay un item siguiente con fecha inicio inferior al max_fecha
	//if ($limpia_siguiente) {
		$q_add ="";
		if (!$limpia_siguiente) 
			$q_add .= " AND fecha_inicio < '$max_fecha' ";
		$r = BD::sql_query("SELECT r.id, r.numero_item, c.mprobabilidad_id, c.mseveridad_id, c.mnivel_riesgo_id, r.mriesgo_id, r.fecha_inicio , fecha_fin
				FROM mriesgo r, mclasificacion_riesgo c 
				WHERE r.estado = 1 AND r.mclasificacion_riesgo_id = c.id
					AND r.dispositivo_seguridad_id = " . Seguridad::escapeSQL($puesto_id) . " 
					AND r.fecha_inicio>='$validacion_fecha_inicio'
					$q_add
				ORDER BY fecha_inicio ASC LIMIT 1");
		if ($f = BD::obtenerRegistro($r)) {
			if (isset($id_siguiente[$f["mriesgo_id"]]))
				$id_siguiente[$f["mriesgo_id"]] = $f["id"];
			else
				$id_siguiente[$f["id"]] = $f["id"];
			$limpia_siguiente = false;
			$max_fecha = date("Y-m-d H:i:s", strtotime($f["fecha_inicio"] . "+1 SECOND"));
			$min_fecha_id = -1;
		}
	//}


	if ($max_id > 0) $id_anterior[$max_id] = $nuevo_id;	//Reemplazamos el id a consultar por el padre
	if ($limpiar_anterior) $id_anterior = array();	//No hay más datos hacia atrás porque todos tenía mriesgo_id en NULL, es decir no tenían padre
	if ($limpia_siguiente) {
		$max_fecha = date("Y-m-d H:i:s");
		$id_siguiente = array();
	}
	$rango_matriz = Fecha::getFechaCorta($min_fecha, "d/F/Y g:i:sa") . " --- " . Fecha::getFechaCorta(date("Y-m-d H:i:s", strtotime($max_fecha . "-1 SECOND")), "d/F/Y g:i:sa");


	$meses = array(1 => "Enero","Febrero", "Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	$severidad = array();
	$probabilidad =  array();
	$nivel_riesgo = array();
	$clasificacion = array();

	$query = BD::sql_query("SELECT * FROM mseveridad ORDER BY orden ASC");
	while($row = BD::obtenerRegistro($query))
	{
		$severidad[$row["id"]] = array(
			"calificacion" => $row["calificacion"],
			"orden" => $row["orden"]
		);
	}
	$query = BD::sql_query("SELECT * FROM mprobabilidad ORDER BY orden DESC");
	while($row = BD::obtenerRegistro($query))
	{
		$probabilidad[$row["id"]] = array(
			"calificacion" => $row["calificacion"],
			"orden" => $row["orden"]
		);
	}
	$query = BD::sql_query("SELECT * FROM mnivel_riesgo");
	while($row = BD::obtenerRegistro($query))
	{
		$nivel_riesgo[$row["id"]] = array(
			"calificacion" => $row["calificacion"],
			"color" => $row["color"]
		);
	}

	$query = BD::sql_query("SELECT * FROM mclasificacion_riesgo");
	while($row = BD::obtenerRegistro($query))
	{
		$clasificacion[$row["mseveridad_id"]][$row["mprobabilidad_id"]] = array(
			"id" => $row["id"],
			"mnivel_riesgo_id" => $row["mnivel_riesgo_id"]
		);
	}

?>
<style>
.titulo{

    text-align: center;
    font-weight: bold;
	width: 50px;
}
.titulo-vertical{
    vertical-align: middle;
    font-weight: bold;
    transform: rotate(-90deg);
	text-align:center;
	max-width: 20px;
}
.nivel_riesgo:hover {
  color: blue;
  font-weight: bold;
  font-size: 18px;
}

</style>
<!--<div style="width: 100%;text-align: center;"><span style="font-weight: bold; font-size: 18px;">Matriz</span></div>-->
<table cellpadding=3 width="100%" border=1>
    <tr class="titulo">
        <td colspan="2" ></td>
        <td colspan="<?php  echo count($severidad) ;?>">SEVERIDAD</td>
    </tr>
    <tr class="titulo">
        <td colspan="2" ></td>
        <?php 
            foreach ($severidad as $key => $value) {
                echo "<td style='width:200px;'>".$value["calificacion"]."</td>";
            }
        ?>
    </tr>
    <tr>
		<?php 
		$titulo = false;
        foreach ($probabilidad as $pk => $vp) {
			echo "<tr>";
			if (!$titulo) {
				echo "<td class='titulo-vertical' rowspan=" . count($probabilidad) . " >PROBABILIDAD</td>";
				$titulo = true;
			}
			echo "<td class='titulo' style='transform:rotate(-90deg); height:100px;width:40px;'>".$vp["calificacion"] . "</td>";
            foreach ($severidad as $ks => $vs) {
				$items = array();
			  	$indice = isset($clasificacion[$ks][$pk]["mnivel_riesgo_id"]) ? $clasificacion[$ks][$pk]["mnivel_riesgo_id"] : -1;
				$color = ($indice >= 0) ? $nivel_riesgo[$indice]["color"] : "#ffffff";
				$items = ($indice != -1 && isset($items_matriz[$ks][$pk][$indice])) ? implode(" ",$items_matriz[$ks][$pk][$indice]) :"";
			  	echo "<td style='background-color:$color; text-align:center; vertical-align:middle;'>" .$items. "</td>";
            }
            echo "</tr>";
        }
		?>
</table>
<br>
<table cellpadding=3 width="100%" border=0>
	<tr >
		<td align="right" colspan="<?php  echo count($severidad) + 2 ;?>">
			<div class="historico input-group-btn">
				<button class="btn btn-default" type="button" onclick="verHistorico('<?php echo implode(',', $id_anterior); ?>', '<?php echo $min_fecha; ?>');"><i class="icon-backward"></i></button>
			</div>
		</td>
		<td style="width:350px;text-align: center;">
			<span style="font-weight: bold;" ><?php echo $rango_matriz; ?></span>
		</td>
		<td>
			<div class="actual input-group-btn" >
				<button class="btn btn-default" type="button" onclick="verHistorico('<?php echo implode(',', $id_siguiente); ?>', '<?php echo $max_fecha; ?>');"><i class="icon-forward"></i></button>
			</div>
		</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td align=right style='padding:12px;' colspan=2><button onclick="descargarExcel();" title="Buscar" class='btn btn-success'><i class='icon-download-alt icon-white' /></i> Descargar Matriz</button>
		</td>
	</tr>
</table>

<script>
	function verHistorico(historico, fecha_inicio) {
		if (historico == "") {
			mensaje("Información", "No hay más datos", "info");
			return;
		}
		showMessage();
		$("#ver").empty();
		$.post("<?php echo DIR_WEB; ?>index.php", {
			historico: historico,
			puesto_id: "<?php echo $puesto_id; ?>",
			fecha_inicio : fecha_inicio
		}, function(res) {
			$("#ver").html(res);
			$('[data-toggle="tooltip"]').tooltip('show');
			hideMessage();
		});
	}

	function verItem(id)
	{
	 	showMessage();
		$("#ventana5").load("<?php echo DIR_WEB; ?>ver_item.php", { id: id }, function () {
			hideMessage();
		});
	}

	function descargarExcel() {
        var f1 = "<?php echo $min_fecha; ?>"
		var f2 = "<?php echo $max_fecha; ?>";
		var items = "<?php echo (count($id_actuales) > 0)? implode(",",$id_actuales) : array();?>";
		var puesto_id = "<?php echo $puesto_id; ?>"
		
		var url = "<?php echo DIR_WEB; ?>lista_excel.php?noConvertir"
				+ "&fecha_inicio=" + f1
				+ "&fecha_fin=" + f2
				+ "&items=" + items
				+ "&puesto_id=" + puesto_id;
		showMessage();
		setTimeout(function() {
			$.post("index.php", { die : "ok"}, function() {
				hideMessage();
			})
		}, 1000);
		document.location.href=url;
	
	}
</script>