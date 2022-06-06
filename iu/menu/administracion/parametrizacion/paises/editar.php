<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al recibir el ID");
	
	$reg = new Pais();
	$reg->load($id) or die("Error al cargar el ítem");
	
	if (isset($_POST["nombre"]))
		die ($reg->update($_POST) ? "ok" : "err");
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value='<?php echo $reg->id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=30 style='width:295px;' name='nombre' value="<?php echo $reg->getCampo("nombre", true); ?>"></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar",
			resizable: false,
			width: 440,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			buttons: {
				"Guardar": function() {
					jConfirm("¿Confirma?", "Pregunta", function(r) {
						if (r)
							$("#formEditar").submit();
					});
              	},
               	"Cancelar": function() {
                    $("#ventana2").html("");
                    $("#ventana2").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana2").html("");
				$("#ventana2").dialog("destroy");
	      	}
		});
		
		$("#formEditar").validate({
			rules: {
				nombre : "required"
			},
			messages: {
				nombre : ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditar').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible actualizar el ítem<br />" + res, "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});
</script>