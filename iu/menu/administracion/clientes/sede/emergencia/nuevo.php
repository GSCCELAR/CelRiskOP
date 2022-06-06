<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$sede_id = isset($_GET["sede_id"]) ? intval($_GET["sede_id"]) :  (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) :  die("Error al recibir el ID de la sede"));
	
	if (isset($_POST["nombre"]) && isset($_POST["telefono"]))
	{
		$telefono_emergencia = new Emergencia($_POST);
		die ($telefono_emergencia->save() ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formNuevoTelefonoEmergencia" id="formNuevoTelefonoEmergencia" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='sede_id' name='sede_id' value='<?php echo $sede_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right ><b>Descripción: </b></td>
			<td>
				<input type="text" style="width:220px;" name='nombre' id='nombre'>
			</td>
		</tr>
		<tr>
			<td align=right ><b>Número: </b></td>
			<td><input type="text" style="width:220px;" name='telefono' id='telefono'></td>
		</tr>
	</table>
</form>

<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Nuevo Teléfono de Emergencia",
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
							$("#formNuevoTelefonoEmergencia").submit();
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

		
		$("#formNuevoTelefonoEmergencia").validate({
			rules: {
				telefono : {
					required: true
				},
				nombre: "required"
			},
			messages: {
				telefono : "",
				nombre : ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formNuevoTelefonoEmergencia").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevoTelefonoEmergencia').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista_emergencia").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible crear el teléfono de emergencia<br />", "error");
					}
				}});
				return false;
			}
		});

		hideMessage();
	});

</script>
