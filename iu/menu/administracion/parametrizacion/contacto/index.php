<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);

	if (isset($_POST["delete"])) {
		$reg = new Contacto();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>
<div><button onclick="nuevoContacto();" class='btn btn-success'><i class='icon icon-plus icon-white' /> Nuevo</button></div>
<table id="lista" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : ['<b>ID</b>', '<b>Contacto</b>','<b>Teléfono</b>','<b>Correo</b>', '<b>Opc</b>'],
		    colModel : [
		        { name:'id', index:'id', width:65, align: 'center' },
		        { name:'nombre', index:'nombre', width:200, align: 'left' },
				{ name:'telefono', index:'telefono', width:100, align: 'left' },
				{ name:'email', index:'email', width:180, align: 'left' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador'),
			rowNum : 10,
		    rowList : [3,10, 20, 30, 50],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'id',
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
		$("#paginador").removeClass("ui-corner-bottom");
		
		hideMessage();
	});
	
	function editarContacto(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>editar.php", { contacto_id: id }, function () {
			hideMessage();
		});
	}

	function nuevoContacto() {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo.php", function () {
			hideMessage();
		});
	}

	function eliminarContacto(id) {
		Swal.fire({
					title : '',
					text : '¿Confirma que desea eliminar el contacto?',
					type : 'warning',
					showCancelButton : true,
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then(function (res) {
					if (res.value)
					{
						showMessage();
						$.post("<?php echo DIR_WEB; ?>index.php", { delete : id }, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible borrar el contacto<br />", "error");
							$("#lista").trigger("reloadGrid");
						});						
					}
			});
	}
</script>