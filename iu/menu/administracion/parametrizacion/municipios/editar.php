<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al recibir el ID");
	
	$reg = new Municipio();
	$reg->load($id) or die("Error al cargar el ítem");
	
	if (isset($_POST["nombre"]))
		die ($reg->update($_POST) ? "ok" : "err");
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value='<?php echo $reg->id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>País: </b></td>
			<td>
				<select name='pais_id' id='pais_id' style="width:150px;">
					<option value=''>Seleccione...</option>
					<?php
						$p = new Pais();
						$p->writeOptions($reg->getCampo("pais_id"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Departamento: </b></td>
			<td><div id='lista_departamentos'></div></td>
		</tr>
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=50 style='width:260px;' name='nombre' value="<?php echo $reg->getCampo("nombre", true); ?>"></td>
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
				pais_id : "required",
				departamento_id : "required",
				nombre : "required"
			},
			messages: {
				pais_id : "",
				departamento_id : "",
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
						mensaje("Error", "No fue posible registrar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
		changePais(<?php echo $reg->getCampo("pais_id") ?>, <?php echo $reg->getCampo("departamento_id"); ?>);

		$("#pais_id").change(function() {
			var id = $(this).val();
			changePais(id);
		});
		});

		function changePais(pais_id, departamento_id) {
		$("#lista_departamentos").load("<?php echo DIR_WEB; ?>select_departamentos.php", { pais_id : pais_id, default : departamento_id });
	}
</script>