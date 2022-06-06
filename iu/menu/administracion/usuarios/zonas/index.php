<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID del usuario"));
	
	$usu = new Usuario();
	if (!$usu->load($id)) die ("error al cargar la información del usuario");
	
	if(isset($_POST["delete"]))
	{
		$reg = new ZonaUsuario();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}

	$usuario_zona = array();
	$sql = BD::sql_query("SELECT zona_id FROM usuario_zona WHERE usuario_id = ". Aplicacion::getIDUsuario());
	while ($r = BD::obtenerRegistro($sql)) {
		$usuario_zona[] = $r["zona_id"];
	}
?>

	<input type="hidden" name='id' value="<?php echo $usu->id; ?>">
	<table>
    	<tr>
			<td valign="top">
				<table>
				<tr>
					<td >
                        <select id='select_zonas' name='select_zonas' style='padding:1px; width: 480px;'>
							<option value='' >Seleccione un zona...</option>
							<?php
								$lista = new Lista();
								if(Aplicacion::getPerfilID() == 9)
									$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"), " AND id IN (".implode(",",$usuario_zona).")");
								else
									$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"));
							?>
                        </select>
					</td>
					<td align="right">
						<input  type="button" title="Agregar" id="adicionarZonas" style="width:80px;height:30px;" class='btn btn-success icon-plus-sign icon-white' value="Agregar">
					</td>
				</tr>
					<tr>
						<table id="lista_zonas" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
						<div id="paginador_zonas" class="scroll" style="text-align:left; font-size:10px;"></div>
					</tr>
				</table>
			</td>
		</tr>
	</table>

<script type="text/javascript">
	$(document).ready(function() {
		$("#lista_zonas").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista_zonas.php?usuario=<?php echo $id; ?>',
		    datatype : "json",
		    colNames : ['<b>Zona<b>','<b>Opc</b>'],
		    colModel : [
		        { name:'zona', index:'zona', width:480, align: 'left' },
		        { name:'opciones', index:'opciones', width:70, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_zonas'),
			rowNum : 10,
		    rowList : [10],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'zona',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>ZONAS USUARIO</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_zonas").removeClass("ui-corner-bottom");

		$("#select_zonas").select2({
			placeholder: 'Seleccione un zona...',
			allowClear: true
		});
		hideMessage();
	});
	

	$("#adicionarZonas").on("click",function(){
		addZonas();
	});

	function addZonas() {
		var id = $("#select_zonas").val();
		if(id != "")
		{
			showMessage();
			$.ajax({
				url: '<?php echo DIR_WEB ;?>set_zonas.php',
				data: {zona_id : id, usuario_id: "<?php echo $id; ?>"},
				type: "post"
			}).done(function(data){
				if (/^ok$/.test(data)) {
					$("#lista_zonas").trigger("reloadGrid");
				} else {
					mensaje("Error", "No fue posible adicionar la zona<br />Es posible que la zona se encuentre asignada al usuario", "error");
				}
				
			}).complete(function(data){
				hideMessage();
			});
		}
	}

	function eliminarZona(id) {
		Swal.fire({
					title : '',
					text : '¿Desea quitar la zona del usuario?',
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
								mensaje("Error", "No fue posible quitar la zona<br />", "error");
							$("#lista_zonas").trigger("reloadGrid");
						});	
					}
						
						
				});
							
	}
</script>