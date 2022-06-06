<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$sede_id = isset($_GET["sede_id"]) ? intval($_GET["sede_id"]) :  (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) :  die("Error al recibir el ID de la sede"));
	
	if (isset($_POST["descripcion"]))
	{
		$dispositivo = new DispositivoSeguridad($_POST);
		die ($dispositivo->save() ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formNuevoDispositivo" id="formNuevoDispositivo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='sede_id' name='sede_id' value='<?php echo $sede_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right style=" vertical-align: top;"><b>Descripcion: </b></td>
			<td><textarea style="width:330px;"  rows="5" name='descripcion' id='descripcion'></textarea></td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Nuevo Dispositivo de Seguridad",
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
							$("#formNuevoDispositivo").submit();
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
		
		$("#formNuevoDispositivo").validate({
			rules: {
				descripcion : "required"
			},
			messages: {
				descripcion : ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formNuevoDispositivo").find('textarea').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevoDispositivo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista_seguridad").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible crear el dispositivo<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
