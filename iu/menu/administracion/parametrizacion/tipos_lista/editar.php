<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al recibir el ID");
	
	$reg = new Lista();
	$reg->load($id) or die("Error al cargar el tipo de lista");

	$tipoLista = new TipoLista();
	$tipoLista->load($reg->getCampo("tipo_lista_id")) or die("Error al cargar el tipo de lista");
	
	if (isset($_POST["nombre"]))
		die ($reg->update($_POST) ? "ok" : "err");
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value='<?php echo $reg->id; ?>'>
	<table cellpadding=3 border=0 width="100%">
	<tr>
			<td align=right><b>Tipo de lista: </b></td>
			<td><?php echo $tipoLista->getCampo("codigo", true); ?></td>
		</tr>
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=200 style='width:150px;' name='nombre' value="<?php echo $reg->getCampo("nombre", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><input type=text maxlength=300 style='width:295px;' name='descripcion' value="<?php echo $reg->getCampo("descripcion", true); ?>"></td>
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
						$("#lista_<?php echo $tipoLista->id ?>_t").trigger("reloadGrid");
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