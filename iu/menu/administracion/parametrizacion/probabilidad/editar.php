<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
	
	$probabilidad_id = isset($_POST["probabilidad_id"]) ? intval($_POST["probabilidad_id"]) :  die("Error al recibir el ID de la probabilidad");
	
	$probabilidad = new Probabilidad();
	if (!$probabilidad->load($probabilidad_id)) die ("error al cargar la información de la probabilidad");
	
	if (isset($_POST["calificacion"]) && isset($_POST["criterio"]) && isset($_POST["descripcion"]))
	{
		die ($probabilidad->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarProbabilidad" id="formEditarProbabilidad" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='probabilidad_id' name='probabilidad_id' value='<?php echo $probabilidad_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Calificación: </b></td>
			<td><input type="text" maxlength="10"  style='width:220px;' id='calificacion' name='calificacion' value="<?php echo $probabilidad->getCampo("calificacion"); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Criterio: </b></td>
			<td><textarea name="criterio" id="criterio"  rows="5" style='width:220px;'><?php echo $probabilidad->getCampo("criterio"); ?></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><textarea name="descripcion" id="descripcion"  rows="5" style='width:220px;'><?php echo $probabilidad->getCampo("descripcion"); ?></textarea></td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar probabilidad",
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
							$("#formEditarProbabilidad").submit();
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
	
		$("#formEditarProbabilidad").validate({
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
				$("#formEditarProbabilidad").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarProbabilidad').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar la probabilidad<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
