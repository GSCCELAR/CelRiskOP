<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);

	$sede_id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : die("Error al obtener el ID de la sede"));

	if (isset($_POST["delete"])) {
		$reg = new SedeContacto();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>
	<table>
		<tr>
			<td  >
				<select class='lista-buscador_contacto' id='contacto' name='contacto' style='padding:1px; width: 435px;'>
					<option value="-1" selected="selected">Seleccione un contacto...</option>
					<?php
					?>
				</select>
			</td>
			<td  align="right">
				<input type="button" title="Agregar Contacto" id="agregar_contacto" name="agregar_contacto" style="width:90px;" class='btn btn-success' value="Agregar">
			</td>
			<td  align="right">
				<input type="button" title="Nuevo Contacto" id="nuevo_contacto" name="nuevo_contacto" class='btn btn-success'  style="width:30px;background:url(imagenes/adicionar.png) no-repeat center; ">
			</td>
		</tr>
	</table>
<table id="lista_contacto" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador_contacto" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista_contacto").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php?sede_id=<?php echo $sede_id; ?>',
		    datatype : "json",
		    colNames : ['<b>Nombre</b>','<b>Teléfono</b>','<b>Correo</b>','<b>Opc</b>'],
		    colModel : [
		        { name:'nombre', index:'nombre', width:200, align: 'left' },
				{ name:'telefono', index:'telefono', width:110, align: 'left' },
				{ name:'email', index:'email', width:170, align: 'left' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_contacto'),
			rowNum : 8,
		    rowList : [3, 5, 8],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'nombre',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>CONTACTOS</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_contacto").removeClass("ui-corner-bottom");
		
		$("#contacto").select2({
			placeholder: {
				id: '-1', // the value of the option
				text: 'Seleccione un contacto...'
			},
			allowClear: true,
			ajax: {
				delay: 250,
				url: '<?php echo DIR_WEB; ?>load_contactos.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
						search: params.term,
						page: params.page || 1
					}
					return query;
				}
			}
		});
		hideMessage();
	});

	

	$("#nuevo_contacto").on("click", function(){
			nuevoContacto();
	});

	$("#agregar_contacto").on("click", function(){
			agregarContacto();
	});
	function editarContacto(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>editar.php", { contacto_id: id }, function () {
			hideMessage();
		});
	}

	function agregarContacto()
	{
		Swal.fire({
				title : '',
				text : '¿Desea agregar el contacto a la sede?',
				type : 'question',
				showCancelButton : true,
				confirmButtonText: 'Aceptar',
				cancelButtonText: 'Cancelar'
			}).then(function (res) {
				if (res.value)
				{
					showMessage();
					var contacto_id = $("#contacto").val();
					$.post("<?php echo DIR_WEB; ?>set_contacto_sede.php", { contacto_id : contacto_id, sede_id: "<?php echo $sede_id; ?>" }, function(res) {
						hideMessage();
						if (!/^ok$/.test(res))
							mensaje("Error", "Es posible que el contacto se encuentre asignado a la sede<br />", "error");
						$("#lista_contacto").trigger("reloadGrid");
						$("#contacto").val(null).trigger('change');
					});						
				}
		});
	}

	function nuevoContacto() {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo.php?sede_id=<?php echo $sede_id; ?>", function () {
			hideMessage();
		});
	}

	function eliminarContacto(id) {
		Swal.fire({
				title : '',
				text : '¿Desea quitar el contacto de la sede?',
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
						mensaje("Error", "No fue posible quitar el contacto<br />", "error");
					$("#lista_contacto").trigger("reloadGrid");
				});						
				}
		});
							
	}
</script>