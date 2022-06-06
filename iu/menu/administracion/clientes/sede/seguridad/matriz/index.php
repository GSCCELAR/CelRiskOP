<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);

$puesto_id = isset($_POST["puesto_id"]) ? $_POST["puesto_id"] : die("Error al recibir el ID del puesto");
$sector_id = isset($_POST["sector_id"]) ? $_POST["sector_id"] : die("Error al recibir el ID del sector");
$proceso_id = isset($_POST["proceso_id"]) ? $_POST["proceso_id"] : die("Error al recibir el ID del proceso");
$puesto = new DispositivoSeguridad();
if (!$puesto->load($puesto_id)) die("error al cargar la información del puesto");

?>

<table style='margin-top:4px;'>
	<tr>
		<td>
			<table border=0>
				<tr>
					<div>
						<div id="mtabs">
							<ul>
								<li><a href="#nuevo">Nuevo ítem</a></li>
								<li><a href="#ver">Ver Matriz</a></li>
							</ul>
							<div url="nuevo/index.php" id="nuevo" style="height:580px; overflow: auto;" class="tabs">
							</div>
							<div url="ver/index.php" id="ver" style="height:580px; overflow: auto;" class="tabs">
							</div>
						</div>
					</div>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Matriz <?php echo str_replace(array("\"", "'", "\n"), "", $puesto->getCampo("descripcion", true)); ?>",
			resizable: false,
			open: function() {
				var t = $(this).parent(),
					w = $(document);
				t.offset({
					top: 30,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 920,
			buttons: {
				"Cerrar": function() {
					$("#ventana3").html("");
					$("#ventana3").dialog("destroy");

				}
			},
			close: function() {
				$("#ventana3").html("");
				$("#ventana3").dialog("destroy");

			}
		});
	
		$( "#mtabs" ).tabs({
			selected: 0,
			select: function(event, ui) {
				var id = ui.tab.attributes.href.value;
				params = {
					puesto_id: "<?php echo $puesto_id; ?>",
					sector_id: "<?php echo $sector_id;?>",
					proceso_id: "<?php echo $proceso_id;?>"
				};
				$(id).load("<?php echo DIR_WEB; ?>/" + $(id).attr("url"),params);
			},
			create: function( event, ui ) {
				params = {
					puesto_id: "<?php echo $puesto_id; ?>",
					sector_id: "<?php echo $sector_id;?>",
					proceso_id: "<?php echo $proceso_id;?>"
				};
				$("#nuevo").load("<?php echo DIR_WEB; ?>/" + $("#nuevo").attr("url"), params);
			}
		});
		hideMessage();
	});

</script>