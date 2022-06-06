<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);

	$sede_id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : die("Error al obtener el ID de la sede"));

	if (isset($_POST["delete"])) {
		$reg = new Emergencia();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>
	<table>
		<tr>
			<td  align="right">
				<input type="button" title="Nuevo teléfono emergencia" id="nuevo_telefono_emergencia" name="nuevo_telefono_emergencia" style="width:90px;" class='btn btn-success' value="Nuevo">
			</td>
		</tr>
	</table>
<table id="lista_emergencia" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador_emergencia" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista_emergencia").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php?sede_id=<?php echo $sede_id; ?>',
		    datatype : "json",
		    colNames : ['<b>Descripción</b>','<b>Número</b>','<b>Opc</b>'],
		    colModel : [
		        { name:'nombre', index:'bombre', width:330, align: 'left' },
				{ name:'telefono', index:'telefono', width:155, align: 'center' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_emergencia'),
			rowNum : 8,
		    rowList : [3, 5, 8],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'nombre',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>TELÉFONOS DE EMERGENCIA</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_emergencia").removeClass("ui-corner-bottom");
	
		hideMessage();
	});

	

	$("#nuevo_telefono_emergencia").on("click", function(){
		nuevoTelefonoEmergencia();
	});

	function editarTelefonoEmergencia(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>editar.php", { id: id }, function () {
			hideMessage();
		});
	}

	function nuevoTelefonoEmergencia() {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo.php?sede_id=<?php echo $sede_id; ?>", function () {
			hideMessage();
		});
	}

	function eliminarTelefonoEmergencia(id) {
		Swal.fire({
					title : '',
					text : '¿Desea quitar el teléfono de emergencia?',
					type : 'question',
					showCancelButton : true,
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then(function (res) {
					if (res.value)
					{
						showMessage();
						$.post("<?php echo DIR_WEB; ?>index.php", { delete : id, sede_id: "<?php echo $sede_id; ?>" }, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible quitar el teléfono de emergencia<br />", "error");
							$("#lista_emergencia").trigger("reloadGrid");
						});	
					}
						
						
				});
							
	}
</script>