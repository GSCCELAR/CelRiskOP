<?php
	define("iC", true);
	//define("DEBUG",true);
	require_once(dirname(__FILE__) . "/../../../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9, 10);
	BD::changeInstancia("mysql");
	
	$item_pos = isset($_POST["item"]) ? intval($_POST["item"]) : die("Error al cargar el item");
	$puesto_id = isset($_POST["puesto_id"]) ? intval($_POST["puesto_id"]) : die("Error al cargar el ID del puesto"); 
	
	
?>

<div id="data-contenedor">
	<table width=100%>
		<tr>
			<td colspan="4">
				<input onchange="loadArchivo(this.id);" id="file_upload_archivo" name="file_upload_archivo" type="file"> (Utiliza el botón <b>Examinar...</b> para cargar el archivo)
			</td>
		</tr>
	<?php
		$ruta = RUTA_TEMPORAL . $puesto_id . "/$item_pos/";
		$d = @dir($ruta);
		if ($d) {
			$tr = array();
			while($df = $d->read())  { 
				if ($df == "." || $df == "..") 
					continue;
				if (is_file($d->path . $df)) {
					$fid = $puesto_id;
					$nombre = utf8_decode(basename($df, ".jpg"));
					$tr[] = "<div style='background-color:#333;color:white;font-size:11px;padding:5px;margin-bottom:3px;border:1px solid black; border-radius: 5px;'>
							<a href='" . DIR_WEB . "ver_imagen_contenedor.php?fid=$fid&nombre=$df&item_pos=$item_pos' data-lightbox='contenedores' data-title='$nombre'>
								<img class='img-contenedores' src='" . DIR_WEB . "ver_imagen_contenedor.php?fid=$fid&nombre=$df&item_pos=$item_pos'><br /><div style='margin-top:5px;color:white;'>$nombre</div>
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
		$("#ventana5").dialog("destroy");
		$("#ventana5").dialog({
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
                    $("#ventana5").html("");
                    $("#ventana5").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana5").html("");
				$("#ventana5").dialog("destroy");
	      	}
		});
		hideMessage();
	});

	function verBoxDamage(modelo_id) {
		$("#ventana5").load("<?php echo DIR_WEB; ?>ver_box_damage.php", { modelo_id : modelo_id });
	}

	function loadArchivo(id) {
		showMessage();
		$.ajaxFileUpload({
			url:'<?php echo DIR_WEB; ?>guardar_archivo.php',
			data:{puesto_id:"<?php echo $puesto_id; ?>",item_pos : "<?php echo $item_pos; ?>"},
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
								url: "<?php echo DIR_WEB; ?>ver_nuevo.php",
								data: {puesto_id : "<?php echo $puesto_id; ?>", item : "<?php echo $item_pos; ?>"},
								type: "POST",
								dataType: "html",
								success: function(data){
									console.log(data);
									$("#ventana5").html(data);
								}
							});
						});
					}562
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
			data: {puesto_id: id, nombre: nombre, item_pos : "<?php echo $item_pos; ?>"},
			type: "POST",
			success: function(resp){
				if(/^ok$/.test(resp)){
					jAlert("Se elimino la imagen correctamente","success",function(){
						$.ajax({
								url: "<?php echo DIR_WEB; ?>ver_nuevo.php",
								data: {puesto_id : <?php echo $puesto_id; ?>, item_pos : "<?php echo $item_pos; ?>"},
								type: "POST",
								dataType: "html",
								success: function(data){
									console.log(data);
									$("#ventana5").html(data);
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