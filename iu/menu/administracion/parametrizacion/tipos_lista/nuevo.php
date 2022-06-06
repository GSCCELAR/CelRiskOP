<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);

	$tipo_lista_id = isset($_POST["tipo_lista_id"]) ? intval($_POST["tipo_lista_id"]) : die("Error al recibir el id del tipo de lista");
	$tipoLista = new TipoLista();
	$tipoLista->load($tipo_lista_id) or die("Error al cargar el tipo de lista seleccionado");
	
	if (isset($_POST["nombre"])) {
		$reg = new Lista($_POST);
		die ($reg->save() ? "ok" : "err");
	}
?>
<form method="post" name="formNuevo" id="formNuevo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='tipo_lista_id' value="<?php echo $tipoLista->id; ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Tipo de lista: </b></td>
			<td><?php echo $tipoLista->getCampo("codigo", true); ?></td>
		</tr>
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=200 style='width:150px;' name='nombre' value=""></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><input type=text maxlength=300 style='width:295px;' name='descripcion' value=""></td>
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
					Swal.fire({
						title : '¿Confirma?',
						text : '',
						type : 'question',
						showCancelButton : true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar'
					}).then(function (res) {
						if (res.value)
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
						$("#lista_<?php echo $tipoLista->id; ?>_t").trigger("reloadGrid");
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