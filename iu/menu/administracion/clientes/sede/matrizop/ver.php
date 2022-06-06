<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$sede_id = isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : -1; 
	$sede = new Sede();
	if(!$sede->load($sede_id)) die("Error al cargar la sede");
	
?>
<h2><b><?php echo ucfirst($sede->getCampo("nombre")); ?></b></h2>
<div id="data-contenedor">
	<table width=100%>
		<tr>
			<td colspan="4">
				<input onchange="loadArchivo(this.id);" id="file_upload_archivo" name="file_upload_archivo" type="file"> (Utiliza el botón <b>Examinar...</b> para cargar el archivo)
			</td>
		</tr>
	<?php
		$ruta = RUTA_IMAGENES . $sede_id . "/";
		$d = @dir($ruta);
		if ($d) {
			$tr = array();
			while($df = $d->read())  { 
				if ($df == "." || $df == "..") 
					continue;
				if (is_file($d->path . $df)) {
					$fid = $sede_id;
					$nombre = utf8_decode(basename($df, ".jpg"));
					$tr[] = "<div style='background-color:#333;color:white;font-size:11px;padding:5px;margin-bottom:3px;border:1px solid black; border-radius: 5px;'>
							<a href='" . DIR_WEB . "ver_imagen_contenedor.php?fid=$fid&nombre=$df' data-lightbox='contenedores' data-title='$nombre'>
								<img class='img-contenedores' src='" . DIR_WEB . "ver_imagen_contenedor.php?fid=$fid&nombre=$df'><br /><div style='margin-top:5px;color:white;'>$nombre</div>
							</a>
							<div style='margin-top:5px;'><a href='javascript:eliminarImagen($fid,\"$df\");'><img src='imagenes/quitar.png'></a></div>
						</div>";
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
<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Imagenes",
			resizable: false,
			position : 'center',
			width: 680,
			buttons: {
               	"Cerrar": function() {
                    $("#ventana3").html("");
                    $("#ventana3").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana3").html("");
				$("#ventana3").dialog("destroy");
	      	}
		});
		hideMessage();
	});

	function verBoxDamage(modelo_id) {
		$("#ventana3").load("<?php echo DIR_WEB; ?>ver_box_damage.php", { modelo_id : modelo_id });
	}

	function loadArchivo(id) {
		showMessage();
		$.ajaxFileUpload({
			url:'<?php echo DIR_WEB; ?>guardar_archivo.php',
			data:{sede_id:"<?php echo $sede_id; ?>"},
			type:"POST",
			secureuri:false,
			fileElementId : id,
			dataType: 'json',
			success: function (data, status) {
				if(typeof(data.error) != 'undefined') {
					console.log(data.error);
					if(data.error != '') {
						jAlert(data.error, "Error");

					}else{
						jAlert("Se cargo la imagen correctamente", "success",function(){
							$.ajax({
								url: "<?php echo DIR_WEB; ?>ver.php",
								data: {sede_id : <?php echo $sede_id; ?>},
								type: "POST",
								dataType: "html",
								success: function(data){
									console.log(data);
									$("#ventana3").html(data);
								}
							});
						});
					}
				}
					
				hideMessage();
				
			},
			error: function (data, status, e) {
				console.log(data);
				hideMessage();
				jAlert(e, "Error");
			}
		});
	}

	function eliminarImagen(id,nombre)
	{
		$.ajax({
			url: "<?php echo DIR_WEB ?>borrar_imagen.php",
			data: {sede_id: id, nombre: nombre},
			type: "POST",
			success: function(resp){
				if(/^ok$/.test(resp)){
					jAlert("Se elimino la imagen correctamente","success",function(){
						$.ajax({
								url: "<?php echo DIR_WEB; ?>ver.php",
								data: {sede_id : <?php echo $sede_id; ?>},
								type: "POST",
								dataType: "html",
								success: function(data){
									console.log(data);
									$("#ventana3").html(data);
								}
							});
					});
				}else
				{
					jAlert(resp, "Error");
				}
			}
		});
	}
</script>