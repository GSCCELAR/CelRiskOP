<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID del usuario");
	
	$form = new Reporte();
	if (!$form->load($id)) die ("error al cargar la información del reporte");
?>
<div id="tabs-formulario">
	<ul>
		<li><a href="#data-general">Datos generales</a></li>
		<li><a href="#data-soportes">Soportes</a></li>
	</ul>
	<div id="data-general">
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right><b># Reporte: </b></td>
				<td><?php echo $form->id; ?></td>
			</tr>
			<tr>
				<td style='width:150px;padding-bottom:30px;' align=right style='width:170px;'><b>Fecha recibido: </b></td>
				<td style='padding-bottom:30px;'><?php echo $form->getFecha("fecha_sistema"); ?></td>
			</tr>
			<tr>
				<td align=right><b>Usuario: </b></td>
				<td><?php echo $form->getCampo("usuario_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right><b>Fecha del Reporte: </b></td>
				<td><?php echo $form->getFecha("fecha_reporte"); ?></td>
			</tr>
			<tr>
				<td align=right><b>Tipo de reporte: </b></td>
				<td><?php echo $form->getCampo("tiporeporte_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right><b>Proceso: </b></td>
				<td><?php echo $form->getCampo("proceso_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right><b>Escenario: </b></td>
				<td><?php echo $form->getCampo("escenario_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right><b>Riesgo: </b></td>
				<td><?php echo $form->getCampo("riesgo_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right><b>Control: </b></td>
				<td><?php echo $form->getCampo("control_nombre", true); ?></td>
			</tr>
			<tr>
				<td align=right valign=top><b>Descripción: </b></td>
				<td><?php echo str_replace(array(", ", ","), ", ", nl2br($form->getCampo("descripcion", true))); ?></td>
			</tr>
			<tr>
				<td colspan=2>
					<div id='map' style='height:200px;width:100%;'></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="data-soportes">
		<table width="100%" style="margin-top:5px;">
			<tr class='ui-widget-header'>
				<td colspan=4 style='padding:4px;'><b>REGISTRO FOTOGRÁFICO</b></td>
			</tr>
			<tr>
				<td colspan=4 style='height: 200px; margin: 0;padding: 0; border-collapse:collapse;;'>
					<div style="width:100%; max-height:300px; overflow:auto">
						<table width="100%">
							<?php
								$ruta = RUTA_SOPORTES . $form->id . "/imagenes/";
								$d = @dir($ruta);
								if ($d) {
									$tr = array();
									while($df = $d->read())  { 
										if ($df == "." || $df == "..") 
											continue;
										if (is_file($d->path . $df)) {
											$fid = $form->id;
											$nombre = utf8_decode(base64_decode(basename($df, ".jpg")));
											$tr[] = "<div style='background-color:#333;color:white;font-size:11px;padding:5px;margin-bottom:3px;border:1px solid black; border-radius: 5px;'>
												<a href='" . DIR_WEB . "ver_foto.php?fid=$fid&nombre=$df' data-lightbox='jaula' data-title='$nombre'>
													<img style='width:136px;' class='img-fotos' src=\"" . DIR_WEB . "ver_foto.php?fid=$fid&nombre=$df\">
												</a>
												<br /><div style='margin-top:5px;'>$nombre</div></div>";
											if (count($tr) == 4) {
												echo "<tr>";
												echo "<td valign=top align=center width='25%'>" . implode("</td><td valign=top align=center width='25%'>", $tr) . "</td>";
												echo "</tr>";
												$tr = array();
											}
										}
									}
									if (count($tr) > 0) {
										$max_td = 4 - count($tr);
										for ($x = 0; $x < $max_td; $x++)
											$tr[] = "&nbsp;";
										echo "<tr>";
										echo "<td valign=top align=center width='25%'>" . implode("</td><td valign=top align=center width='25%'>", $tr) . "</td>";
										echo "</tr>";
									}
									$d->close();
								}
							?>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<table width="100%" style="margin-top:5px;">
			<tr class='ui-widget-header'>
				<td colspan=4 style='padding:4px;'><b>REGISTRO AUDIO</b></td>
			</tr>
			<tr>
				<td colspan=4 style='height: 200px; margin: 0;padding: 0; border-collapse:collapse;;'>
					<div style="width:100%; max-height:300px; overflow:auto">
						<table width="100%">
							<?php
								$ruta = RUTA_SOPORTES . $form->id . "/audio/";
								$d = @dir($ruta);
								if ($d) {
									$tr = array();
									while($df = $d->read())  { 
										if ($df == "." || $df == "..") 
											continue;
										if (is_file($d->path . $df)) {
											$fid = $form->id;
											
											$tr[] = "<tr height:'10%'>
													<td  width='10%' ><audio controls autoplay controlsList=\"nodownload\">
														<source src=\"".RUTA_SOPORTES . $form->id . "/audio/audio.mp4\" type=\"audio/mpeg\">
														Your browser does not support the audio element.
													</audio></td>
													<td  width='25%'><a href='" . DIR_WEB . "get_audio.php?fid=$fid'>
														<img src=\"" . DIR_WEB . "ver_imagen.php\" style=\"width:25px;height:25px;\">
													</a></td></tr>";
								
										}
										echo implode($tr);
									}
									$d->close();
								}
							?>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<table cellpadding=3 border=0 width="100%">
			<tr>
				<td align=right style='width:170px;'><b>Espacio en disco: </b></td>
				<td><?php echo $form->getSize(); ?></td>
			</tr>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-search' /> &nbsp; Ver Reporte",
			resizable: false,
			position : 'top',
			width: 680,
			buttons: {
               	"Cerrar": function() {
                    $("#ventana2").html("");
                    $("#ventana2").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana2").html("");
				$("#ventana2").dialog("destroy");
	      	}
		});
		hideMessage();

		$("#tabs-formulario" ).tabs({ 
			selected: 0,
			select : function () {
				console.log("resize");
				map.setView(new L.LatLng(<?php echo $form->getCampo("lat"); ?>, <?php echo $form->getCampo("lon"); ?>), 15);
			}
		}).css("border", "none");

		$("#data-general table tr:even").css("background-color", "#EFEFEF");

		lightbox.option({
			'resizeDuration': 200,
			'wrapAround': false,
			'albumLabel': 'Imagen %1 de %2',
		});

		var mbAttr = '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors';
		var osm_tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: mbAttr });
		var map = L.map('map', {
			layers: [osm_tile],
			zoomControl: true
		}).setView(new L.LatLng(<?php echo $form->getCampo("lat"); ?>, <?php echo $form->getCampo("lon"); ?>), 15);
		var bounds = [];
		<?php
			$x = 0;
			if ($form->getCampo("lon") == "" || $form->getCampo("lat") == "" || $form->getCampo("accuracy") == "") {

			}
			else {
			$observaciones = "<tr><td colspan=2>" . $form->getCampo("descripcion", true) . "</td></tr>";
			?>
			L.marker([<?php echo $form->getCampo("lat"); ?>, <?php echo $form->getCampo("lon"); ?>]).addTo(map)
				/*.bindPopup("<table><tr><td colspan=2><b><u><?php echo Fecha::getFecha($form->getCampo("fecha_reporte"), "d/M/Y  h:i:sa"); ?></u></b></td></tr>"
					+ "<?php echo $observaciones; ?>"
					+ "</table>"
				).openPopup()*/;
			var coords = [<?php echo $form->getCampo("lat"); ?>, <?php echo $form->getCampo("lon"); ?>];
			bounds.push(coords);
			L.circle(coords, {
				color: 'darkgreen',
				fillColor: 'darkgreen',
				fillOpacity: 0.4,
				radius: <?php echo intval($form->getCampo("accuracy") + 5); ?>
			}).addTo(map);
		<?php } ?>
		/*if (bounds.length > 0)
			map.fitBounds(bounds);*/
	});
</script>