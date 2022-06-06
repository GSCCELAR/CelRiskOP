<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);

if (isset($_POST["delete"])) {
	$reg = new DispositivoSeguridad();
	$reg->load($_POST["delete"]) or die("Error al cargar la información");
	die($reg->delete() ? "ok" : "Hay datos relacionados con este item");
}
$sede_id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : die("Error al obtener el ID de la sede"));
$sector_id = isset($_GET["sector_id"]) ? intval($_GET["sector_id"]) : die("Error al obtener el ID del sector");
$proceso_id = isset($_GET["proceso_id"]) ? intval($_GET["proceso_id"]) : die("Error al obtener el ID del proceso");

?>
<table>
	<tr>
		<td align="right">
			<input type="button" title="Nuevo dispositivo" id="nuevo_dispositivo" name="nuevo_dispositivo" style="width:90px;" class='btn btn-success' value="Nuevo">
		</td>
	</tr>
</table>
<table id="lista_seguridad" class="scroll" cellpadding="0" cellspacing="0" style="font-size:12px; border-collapse: none;"></table>
<div id="paginador_seguridad" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista_seguridad").jqGrid({
			url: '<?php echo DIR_WEB; ?>lista.php?sede_id=<?php echo $sede_id; ?>',
			datatype: "json",
			colNames: ['<b>Descripción</b>', '<b>Opc</b>'],
			colModel: [{
					name: 'descripcion',
					index: 'descripcion',
					width: 400,
					align: 'left'
				},
				{
					name: 'opciones',
					index: 'opciones',
					width: 150,
					align: 'center',
					search: false
				}
			],
			pager: jQuery('#paginador_seguridad'),
			rowNum: 8,
			rowList: [3, 5, 8],
			imgpath: "css/jqGrid/steel/images",
			sortname: 'descripcion',
			viewrecords: true,
			mtype: 'POST',
			pagerpos: 'center',
			sortorder: "asc",
			height: "auto",
			caption: "<i class='icon-list icon-white' /> <b>DISPOSITIVOS DE SEGURIDAD</b>"
		});

		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_seguridad").removeClass("ui-corner-bottom");

		/*$("#dispositivo").select2({
			placeholder: {
				id: '-1', // the value of the option
				text: 'Seleccione un dispositivo...'
			},
			allowClear: true,
			ajax: {
				url: '<?php //echo DIR_WEB; 
						?>load_dispositivos.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return query;
				},
				processResults: function (data) {
					
					return {
						results: $.map(data, function(obj) {
						return { id: obj.id, text: obj.text };
						})
					};
				}
			}
		});*/
		hideMessage();
	});



	$("#nuevo_dispositivo").on("click", function() {
		nuevoDispositivo();
	});

	function editarDispositivo(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>editar.php", {
			id: id
		}, function() {
			hideMessage();
		});
	}


	function nuevoDispositivo() {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo.php?sede_id=<?php echo $sede_id; ?>", function() {
			hideMessage();
		});
	}

	function eliminarDispositivo(id) {
		Swal.fire({
			title: '',
			text: '¿Desea eliminar el dispositivo de seguridad?',
			type: 'question',
			showCancelButton: true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then(function(res) {
			if (res.value) {
				showMessage();
				$.post("<?php echo DIR_WEB; ?>index.php", {
					delete: id,
					sede_id: "<?php echo $sede_id; ?>",
					sector_id: "<?php echo $sector_id; ?>",
					proceso_id: "<?php echo $proceso_id; ?>"
				}, function(res) {
					hideMessage();
					if (!/^ok$/.test(res))
						mensaje("Error", "No fue posible borrar el dispositivo de seguridad<br />", "error");
					$("#lista_seguridad").trigger("reloadGrid");
				});
			}
		});

	}

	function verMatriz(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>matriz/index.php", {
			puesto_id: id,
			sector_id: "<?php echo $sector_id; ?>",
			proceso_id: "<?php echo $proceso_id; ?>"
		}, function(res) {
			hideMessage();
		});
	}

	function copiarMatriz(id)
	{
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>matriz/copiar.php",{
			puesto_id: id, 
			sector_id: "<?php echo $sector_id; ?>",
			proceso_id: "<?php echo $proceso_id; ?>"
		},function(res){
			hideMessage();
		});
	}
</script>