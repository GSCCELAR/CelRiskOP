<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
		
	if (isset($_POST["calificacion"]) && isset($_POST["criterio"]) && isset($_POST["descripcion"]))
	{
		$probabilidad = new Probabilidad($_POST);
		die ($probabilidad->save() ? "ok" : BD::getLastError());		
	}		
?>
<form method="post" name="formNuevaProbabilidad" id="formNuevaProbabilidad" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Calificación: </b></td>
			<td><input type="text" maxlength="10"  style='width:220px;' id='calificacion' name='calificacion'></td>
		</tr>
		<tr>
			<td align=right><b>Criterio: </b></td>
			<td><textarea name="criterio" id="criterio"  rows="5" style='width:220px;'></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><textarea name="descripcion" id="descripcion"  rows="5" style='width:220px;'></textarea></td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Nueva Probabilidad",
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
							$("#formNuevaProbabilidad").submit();
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

	
		$("#formNuevaProbabilidad").validate({
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
			    $("#formNuevaProbabilidad").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevaProbabilidad').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger('reloadGrid');
					}
					else {
						mensaje("Error", "No fue posible crear la nueva probabilidad<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
