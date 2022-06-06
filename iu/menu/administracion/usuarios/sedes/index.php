<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID del usuario"));
	
	$usu = new Usuario();
	if (!$usu->load($id)) die ("error al cargar la información del usuario");
	
	if(isset($_POST["delete"]))
	{
		$reg = new SedeUsuario();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>

	<input type="hidden" name='id' value="<?php echo $usu->id; ?>">
	<table>
    	<tr>
			<td valign="top">
				<table>
				<tr>
					<td >
                        <select class='lista-buscador' id='cliente' name='cliente' style='padding:1px; width: 480px;'>
                            <option value='-1' selected="selected">Seleccione un cliente...</option>
                        </select>
					</td>
					<td align="right">
						<input  type="button" title="Agregar" id="adicionarCliente" style="width:80px;height:30px;" class='btn btn-success icon-plus-sign icon-white' value="Agregar">
					</td>
				</tr>
					<tr>
						<table id="lista_cliente" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
						<div id="paginador_cliente" class="scroll" style="text-align:left; font-size:10px;"></div>
					</tr>
				</table>
			</td>
		</tr>
	</table>

<script type="text/javascript">
	$(document).ready(function() {
		$("#lista_cliente").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista_cliente.php?usuario=<?php echo $id; ?>',
		    datatype : "json",
		    colNames : ['<b>Sede / Dirección</b>','<b>Ciudad</b>','<b>Teléfono</b>','<b>Opc</b>'],
		    colModel : [
		        { name:'sede', index:'sede', width:300, align: 'left' },
				{ name:'ciudad', index:'ciudad', width:100, align: 'left' },
				{ name:'telefono', index:'telefono', width:80, align: 'left' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_cliente'),
			rowNum : 10,
		    rowList : [10],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'sede',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>SEDES USUARIO</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_cliente").removeClass("ui-corner-bottom");

		$("#cliente").select2({
			placeholder: {
				id: '-1', // the value of the option
				text: 'Seleccione un cliente...'
			},
			allowClear: true,
			ajax: {
				url: '<?php echo DIR_WEB; ?>load_clientes.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
						search: params.term,
                		page: params.page || 1
					}
					return query;
				},
        	cache: true
			}
		});

		hideMessage();
	});

	$("#adicionarCliente").on("click",function(){
		addCliente();
		$("#cliente").val(null).trigger("change");
	});

	function addCliente() {
		var id = $("#cliente").val();
		if(id != "")
		{
			showMessage();
			$.ajax({
				url: '<?php echo DIR_WEB ;?>set_cliente.php',
				data: {sede_id : id, usuario_id: "<?php echo $id; ?>"},
				type: "post"
			}).done(function(data){
				if (/^ok$/.test(data)) {
					$("#cliente").select2('val', '');
					$("#lista_cliente").trigger("reloadGrid");
				} else {
					mensaje("Error", "No fue posible adicionar la sede<br />Es posible que la sede se encuentre asignada al usuario", "error");
				}
				
			}).complete(function(data){
				hideMessage();
			});
		}
	}

	function eliminarSede(id) {
		Swal.fire({
					title : '',
					text : '¿Desea quitar la sede del usuario?',
					type : 'question',
					showCancelButton : true,
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then(function (res) {
					if (res.value)
					{
						showMessage();
						$.post("<?php echo DIR_WEB; ?>index.php", { delete : id, id: "<?php echo $id;?>"}, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible quitar la sede<br />", "error");
							$("#lista_cliente").trigger("reloadGrid");
						});	
					}
						
						
				});
							
	}
</script>