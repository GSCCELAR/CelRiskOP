<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
	
	$nivel_riesgo_id = isset($_POST["nivel_riesgo_id"]) ? intval($_POST["nivel_riesgo_id"]) :  die("Error al recibir el ID de nivel de riesgo");
	
	$nivel_riesgo = new NivelRiesgo();
	if (!$nivel_riesgo->load($nivel_riesgo_id)) die ("error al cargar la información del nivel de riesgo");
	
	if (isset($_POST["calificacion"]) && isset($_POST["criterio"]) && isset($_POST["color"]))
	{
		die ($nivel_riesgo->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarNivelRiesgo" id="formEditarNivelRiesgo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='nivel_riesgo_id' name='nivel_riesgo_id' value='<?php echo $nivel_riesgo_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Calificación: </b></td>
			<td><input type="text" maxlength="10"  style='width:220px;' id='calificacion' name='calificacion' value="<?php echo $nivel_riesgo->getCampo("calificacion"); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Criterio: </b></td>
			<td><textarea name="criterio" id="criterio"  rows="5" style='width:220px;'><?php echo $nivel_riesgo->getCampo("criterio"); ?></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Color: </b></td>
			<td><input type="color"  style='width:230px;height:30px;' id='color' name='color' value="<?php echo $nivel_riesgo->getCampo("color"); ?>"></td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Nivel Riesgo",
			resizable: false,
			width: 400,
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
							$("#formEditarNivelRiesgo").submit();
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
	
		$("#formEditarNivelRiesgo").validate({
			rules: {
				calificacion : "required",
				criterio : "required",
				color: "required"
			},
			messages: {
				calificacion : "",
				criterio : "",
				color: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formEditarNivelRiesgo").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarNivelRiesgo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar el nivel de riesgo<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
