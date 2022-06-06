<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
	
	$severidad_id = isset($_POST["severidad_id"]) ? intval($_POST["severidad_id"]) :  die("Error al recibir el ID de la severidad");
	
	$severidad = new Severidad();
	if (!$severidad->load($severidad_id)) die ("error al cargar la información de la severidad");
	
	if (isset($_POST["calificacion"]) && isset($_POST["criterio"]))
	{
		die ($severidad->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarSeveridad" id="formEditarSeveridad" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='severidad_id' name='severidad_id' value='<?php echo $severidad_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Calificación: </b></td>
			<td><input type="text" maxlength="10"  style='width:220px;' id='calificacion' name='calificacion' value="<?php echo $severidad->getCampo("calificacion"); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Criterio: </b></td>
			<td><textarea name="criterio" id="criterio"  rows="5" style='width:220px;'><?php echo $severidad->getCampo("criterio"); ?></textarea></td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Severidad",
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
							$("#formEditarSeveridad").submit();
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
	
		$("#formEditarSeveridad").validate({
			rules: {
				calificacion : "required",
				criterio : "required",
				descripcion: "required"
			},
			messages: {
				calificacion : "",
				criterio : "",
				descripcion: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formEditarSeveridad").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarSeveridad').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar la severidad<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
