<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);

	
	$dispositivo_id = isset($_POST["id"]) ? intval($_POST["id"]) :  die("Error al recibir el ID del dispositivo");
	
	$dispositivo = new DispositivoSeguridad();
	if (!$dispositivo->load($dispositivo_id)) die ("error al cargar la información del dispositivo");
	
	if (isset($_POST["descripcion"]))
	{

		die ($dispositivo->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarDispositivo" id="formEditarDispositivo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='id' name='id' value='<?php echo $dispositivo_id; ?>'>
	<input type="hidden" id='sede_id' name='sede_id' value='<?php echo $dispositivo->getCampo("sede_id"); ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right style=" vertical-align: top;"><b>Descripcion: </b></td>
			<td><textarea style="width:330px;"  rows="5" name='descripcion' id='descripcion' ><?php echo $dispositivo->getCampo("descripcion") ?></textarea></td>
		</tr>
		<tr>
			<td align=right style=" vertical-align: top;"><b>Estado: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:164px;' name='estado' id='estado'>
					<option value="0" <?php echo ($dispositivo->getCampo("estado") == 0) ? "selected='selected'" : ""; ?>>Inactivo</option>
					<option value="1" <?php echo ($dispositivo->getCampo("estado") == 1) ? "selected='selected'" : ""; ?>>Activo</option>
				</select>
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	var marker;
	var lastSel;
	var latlng;
	var map_nueva_sede;
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Dispositivo de Seguridad",
			resizable: false,
			width: 500,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			buttons: {
				"Guardar": function() {
					Swal.fire({
						title : '¿Confirma?',
						text : '',
						type : 'question',
						showCancelButton : true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar'
					}).then(function (res) {
						if (res.value)
							$("#formEditarDispositivo").submit();
					});
              	},
               	"Cancelar": function() {
                    $("#ventana3").html("");
					$("#ventana3").dialog("destroy");

                }
	        },
	      	close : function () {
	      		$("#ventana3").html("");
				$("#ventana3").dialog("destroy");

	      	}
		});
		
		$("#formEditarDispositivo").validate({
			rules: {
				nombre : "required",
				identificacion : "required",
				telefono: "required",
				cargo: "required"
			},
			messages: {
				nombre : "",
				identificacion : "",
				telefono: "",
				cargo: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formEditarDispositivo").find('textarea').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarDispositivo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista_seguridad").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar el dispositivo<br />", "error");
					}
				}});
				return false;
			}
		});

		$("#estado").select2();
		hideMessage();
	});

</script>
