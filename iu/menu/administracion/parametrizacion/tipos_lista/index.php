<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	if (isset($_POST["delete"])) {
		$reg = new Lista();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay listas relacionadas con este tipo de lista"); 
	}
?>
<table id="lista" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador" class="scroll" style="text-align:left; font-size:10px;"></div>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : [/*'<b>ID</b>',*/ '<b>Código</b>', '<b>Descripción</b>', '<b>Items</b>', '<b>Opc</b>'],
		    colModel : [
		        //{ name:'id', index:'id', width:65, align: 'center' },
		        { name:'codigo', index:'codigo', width:190, align: 'left' },
		        { name:'descripcion', index:'descripcion', width:480, align: 'left' },
		        { name:'cantidad', index:'cantidad', width:50, align: 'center' },
		        { name:'opciones', index:'opciones', width:70, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador'),
			rowNum : 20,
		    rowList : [3,10, 20, 30, 50],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'id',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "desc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>ADMINISTRACIÓN DE LISTAS</b>",
			subGrid : true,
			subGridRowExpanded: function(subgrid_id, row_id) {
				var subgrid_table_id, pager_id;
				subgrid_table_id = subgrid_id + "_t"; 
				pager_id = "p_" + subgrid_table_id;
				$("#"+subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
				$("#"+subgrid_table_id).jqGrid({
					url:"<?php echo DIR_WEB; ?>get_lista.php?tipo_lista_id=" + row_id, 
					datatype: "json",
					colNames: [/*'ID', */'Nombre', 'Descripción', 'Opc'], 
					colModel: [
						//{ name:"id", index:"id",width:50,align:"center" }, 
						{ name:"nombre",index:"nombre",width:200 },  
						{ name:"descripcion", index:"descripcion",width:513,align:"left" }, 
						{ name:"opciones", index:"opciones", width:80, align:"center", search :false }
					],
					rowNum : 10, 
					mtype: "POST",
					pager: pager_id, 
					imgpath: "css/jqGrid/steel/images", 
					sortname: 'id', 
					sortorder: "desc", 
					height: 'auto',
					viewrecords : true,
					recordtext: 'Notas',
					rowList : [10, 20, 30, 50]
				});
			}
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador").removeClass("ui-corner-bottom");
		
		hideMessage();
	});
	
	function editar(id) {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>editar.php", { id }, function () {
			hideMessage();
		})
	}

	function nuevo(tipo_lista_id) {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>nuevo.php", { tipo_lista_id : tipo_lista_id }, function () {
			hideMessage();
		})
	}

	function eliminar(tipo_lista_id, id) {
		Swal.fire({
			title : '',
			html : "Confirma que desea borrar este registro?<br />Nota: Si este registro está relacionado en otra tabla no podrá borrarlo.",
			type : 'question',
			showCancelButton : true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then(function (res) {
			if (res.value) {
				showMessage();
				$.post("<?php echo DIR_WEB; ?>index.php", { delete : id }, function(res) {
					hideMessage();
					if (!/^ok$/.test(res))
						mensaje("Error", "No ha sido posible borrar el registro.<br />" + res, "error");
					$("#lista_" + tipo_lista_id + "_t").trigger("reloadGrid");
				});
			}
		});
	}
</script>