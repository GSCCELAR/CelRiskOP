<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al recibir el ID");
	
	$reg = new Proceso();
	$reg->load($id) or die("Error al cargar el ítem");
	
	if (isset($_POST["nombre"]) && isset($_POST["objetivo"]) && isset($_POST["alcance"]))
		die ($reg->update($_POST) ? "ok" : "err");
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value='<?php echo $reg->id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Proceso: </b></td>
			<td><input type=text maxlength=120 style='width:260px;' name='nombre' value="<?php echo $reg->getCampo("nombre", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Objetivo: </b></td>
			<td><input type=text  style='width:260px;' name='objetivo' value="<?php echo $reg->getCampo("objetivo", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Alcance: </b></td>
			<td><input type=text  style='width:260px;' name='alcance' value="<?php echo $reg->getCampo("alcance", true); ?>"></td>
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
					Swal.fire({
						title : '¿Confirma?',
						text : '',
						type : 'question',
						showCancelButton : true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar'
					}).then(function (res) {
						if (res.value)
						{
							$("#formEditar").submit();
						}
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
				nombre : "required",
				objetivo: "required",
				alcance: "required"
			},
			messages: {
				nombre : "",
				objetivo: "",
				alcance: ""
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
						mensaje("Error", "No fue posible actualizar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});
</script>