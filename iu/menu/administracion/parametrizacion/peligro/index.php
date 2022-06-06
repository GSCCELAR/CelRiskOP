<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);

	if (isset($_POST["delete"])) {
		$reg = new Peligro();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>
<div><button onclick="nuevoPeligro();" class='btn btn-success'><i class='icon icon-plus icon-white' /> Nuevo</button></div>
<table id="lista" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : ['<b>ID</b>', '<b>Agente Riesgo</b>','<b>Factores de Riesgo</b>','<b>Descripción</b>','<b>Efectos Salud</b>','<b>Tipo Riesgo</b>' , '<b>Opc</b>'],
		    colModel : [
		        { name:'id', index:'id', width:65, align: 'center' },
		        { name:'agente_riesgo', index:'agente_riesgo', width:120, align: 'left' },
				{ name:'peligros', index:'peligros', width:150, align: 'left' },
				{ name:'descripcion', index:'descripcion', width:150, align: 'left' },
				{ name:'efectos_salud', index:'efectos_salud', width:160, align: 'left' },
				{ name:'tipo_riesgo', index:'tipo_riesgo', width:100, align:'left'},
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
		    caption: "<i class='icon-list icon-white' /> <b>Peligro</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador").removeClass("ui-corner-bottom");
		
		hideMessage();
	});
	
	function editarPeligro(id) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>editar.php", { peligro_id: id }, function () {
			hideMessage();
		});
	}

	function nuevoPeligro() {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo.php", function () {
			hideMessage();
		});
	}

	function eliminarPeligro(id) {
		Swal.fire({
					title : '',
					text : '¿Confirma que desea eliminar el factor de riesgo?',
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
								mensaje("Error", "No fue posible borrar el riesgo SSTA<br />", "error");
							$("#lista").trigger("reloadGrid");
						});						
					}
			});
	}
</script>