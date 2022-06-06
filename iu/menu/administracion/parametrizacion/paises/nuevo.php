<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	
	if (isset($_POST["nombre"])) {
		$reg = new Pais($_POST);
		die ($reg->save() ? "ok" : "err");
	}
?>
<form method="post" name="formNuevo" id="formNuevo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=30 style='width:295px;' name='nombre' value=""></td>
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
			title: "<i class='icon-white icon-plus' /> &nbsp; Nuevo",
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
							$("#formNuevo").submit();
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
		
		$("#formNuevo").validate({
			rules: {
				nombre : "required"
			},
			messages: {
				nombre : ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formNuevo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible registrar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});
</script>