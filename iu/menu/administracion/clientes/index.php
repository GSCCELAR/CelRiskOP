<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	if (isset($_POST["delete"])) {
		$reg = new Cliente();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay información relacionada con este cliente. No se puede borrar la información"); 
	}
	
	if (isset($_POST["estado"])) {
		$reg = new Cliente();
		$reg->load($_POST["estado"]) or die("Error al cargar la información");
		($reg->getCampo("estado") == "1") ? $reg->setCampo("estado","0") : $reg->setCampo("estado","1"); 
		die ($reg->update() ? "ok" : "Ocurrió un error al intentar cambiar el estado del cliente"); 
	}
?>

<table style='margin-top:4px;'>
	<tr>
		<td valign="top" style="width: 250px;">
			<div style='padding: 4px;'>
				<table style="font-size:12px; color:black; background-color:#EFEFEF; width: 240px; border:1px solid #dedede;">
					<tr style="color:white;" class="ui-widget-header">
						<td colspan="2" style='padding: 4px;'><i class='icon-search icon-white'></i><b> CONSULTAS</b></td>
					</tr>
					<tr>
						<td align="right" style="padding-top:12px; width:90px;"><b>Razón Social:</b> &nbsp</td>
						<td style="padding-top:12px; "><input id='busca_cliente' style='padding:1px; width:146px;' type=text name='busca_cliente'></td>
					</tr>
					<tr>
						<td align="right"><b>Identificación:</b> &nbsp;</td>
						<td>
							<input id='busca_identificacion' name="busca_identificacion" style='padding:1px; width:146px;' type=text name=''>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Sector:</b> &nbsp;</td>
						<td>
							<select style='padding:1px; height:25px; width:150px;' name='busca_sector' id='busca_sector'>
								<option value=''>Todos...</option>
								<?php
									$lista = new Lista();
									$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "SECTOR"));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Estado:</b> &nbsp;</td>
						<td>
							<select style='padding:1px; height:25px; width:150px;' name='busca_estado' id='busca_estado'>
								<option value=''>Todos...</option>
								<option value='1'>ACTIVO</option>
								<option value='0'>INACTIVO</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" colspan="2" style="padding:12px; padding-bottom:9px; border-bottom: 1px dashed black;">
							<button onclick="buscarTexto();" title="Buscar" class='btn btn-info'><i class='icon-search icon-white' /></i></button>
							<button onclick="limpiarBusqueda();" title="Limpiar formulario de búsqueda" class='btn btn-danger'><i class='icon-remove icon-white' /></i></button>
						</td>
					</tr>
					<tr>
						<td colspan=2 align="right" style="padding:12px; padding-top:9px;">
							<button title="Registrar cliente" onclick="nuevo()" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i> Nuevo</button>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td valign="top" style="padding:4px;">
			<table id="lista" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
			<div id="paginador" class="scroll" style="text-align:left; font-size:10px;"></div>
		</td>
	</tr>
</table>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : ['<b>Estado</b>', '<b>cliente</b>','<b>Identificación</b>','<b>Sector</b>', '<b>Opc.</b>'],
		    colModel : [
		        { name:'estado', index:'estado', width:80, align: 'center' },
				{ name:'razon_social', index:'razon_social', width:250, align: 'left' },
				{ name:'identificacion', index:'identificacion', width:175, align: 'left' },
				{ name:'sector_nombre', index:'sector_nombre', width:180, align: 'left' },
		        { name:'opciones', index:'opciones', width:100, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador'),
			rowNum : 20,
		    rowList : [10, 20, 30, 50, 100],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'razon_social',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "desc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>CLIENTES</b>",
			subGrid: true,
			subGridRowExpanded: function(subgrid_id, row_id) {
				var subgrid_table_id, pager_id;
				subgrid_table_id = subgrid_id + "_t"; 
				pager_id = "p_" + subgrid_table_id;
				$("#"+subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
				$("#"+subgrid_table_id).jqGrid({
					url:"<?php echo DIR_WEB; ?>get_lista.php?cliente_id=" + row_id, 
					datatype: "json",
					colNames: [/*'ID', */'Nombre', 'Dirección', 'Teléfono','Municipio'], 
					colModel: [
						//{ name:"id", index:"id",width:50,align:"center" }, 
						{ name:"nombre",index:"nombre",width:170 },  
						{ name:"direccion", index:"direccion",width:280,align:"left" }, 
						{ name:"telefono", index:"telefono",width:100,align:"left" }, 
						{ name:"municipio", index:"municipio",width:180,align:"left" }
					],
					rowNum : 10, 
					mtype: "POST",
					pager: pager_id, 
					imgpath: "css/jqGrid/steel/images", 
					sortname: 'nombre', 
					sortorder: "asc", 
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
		
		$("#busca_sector").select2({
			placeholder: 'Todos...',
			allowClear: true
		});
		$("#busca_estado").select2({
			placeholder: 'Todos...',
			allowClear: true
		});
		
		hideMessage();
		
		$("#busca_estado,#busca_sector").change(function() {
			buscarTexto();
		});
		
		var hBuscar = null;
		$("#busca_cliente,#busca_identificacion").keyup(function (e) {
			if (e.keyCode == 13) {
				buscarTexto();
				return;
			}
		});
		
		$("#formEditar").find("input,select").uniform();
	});
	
	function nuevaSede(id) {
		showMessage();
		editar(id, "true");
	}
	function editar(id, nueva_sede = "false") {
		showMessage();
		var params = {
			id : id,
			nueva_sede : nueva_sede
		}
		$("#ventana").load("<?php echo DIR_WEB; ?>editar.php", params);
	}
	
	function nuevo() {
		showMessage();
		$("#ventana").load("<?php echo DIR_WEB; ?>nuevo.php");
	}
	
	function limpiarBusqueda() {
		$("#busca_cliente,#busca_identificacion").val("");
		$("#busca_sector,#busca_estado").val("").trigger("change");
		buscarTexto();
	}
	
	function eliminar(id) {
		Swal.fire({
				title : '',
				text : '¿Confirma que desea borrar el cliente?',
				type : 'question',
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
							mensaje("Error", "No ha sido posible borrar el registro del cliente debido a que existe información relacionada con este cliente", "error");
						$("#lista").trigger("reloadGrid");
					});
				}
			});
	}

	function buscarTexto() {
		var busca_cliente = $("#busca_cliente").val();
		var busca_identificacion = $("#busca_identificacion").val();
		var busca_sector = $("#busca_sector").val();
		var busca_estado = $("#busca_estado").val();
		
		hBuscar = null;
		$("#lista").setGridParam({
			url: "<?php echo DIR_WEB; ?>lista.php?1=1"
				+ "&cliente=" + busca_cliente
				+ "&identificacion=" + busca_identificacion
				+ "&sector=" + busca_sector
				+ "&estado=" + busca_estado,
			page: 1
		}).trigger("reloadGrid");
	}

	function cambiarEstado(id)
	{
		Swal.fire({
				title : '',
				text : '¿Confirma que desea activar/inactivar el cliente?',
				type : 'question',
				showCancelButton : true,
				confirmButtonText: 'Aceptar',
				cancelButtonText: 'Cancelar'
			}).then(function (res) {
				if (res.value)
				{
					showMessage();
					$.post("<?php echo DIR_WEB; ?>index.php", { estado : id }, function(res) {
						hideMessage();
						if (!/^ok$/.test(res))
							mensaje("Error", "No ha sido posible cambiar el estado del cliente", "error");
						$("#lista").trigger("reloadGrid");
					});
				}
			});
		
	}
</script>