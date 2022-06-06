<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$capitacion_id = isset($_POST["id"]) ? intval($_POST["id"]) :  die("Error al recibir el ID del dispositivo");
	
	$capitacion = new Capitacion();
	if (!$capitacion->load($capitacion_id)) die ("error al cargar la información del dispositivo");

	$lista = new Lista();
	if (!$lista->load($capitacion->getCampo("tipopersona_id"))) die ("error al cargar la información de tipo persona");
	
	if (isset($_POST["cantidad"]) && isset($_POST["tipopersona"]))
	{
		$_POST["tipopersona_id"] = $_POST["tipopersona"];
		die ($capitacion->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formNuevoCapitacion" id="formNuevoCapitacion" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
<input type="hidden" id='id' name='id' value='<?php echo $capitacion->id; ?>'>	
<input type="hidden" id='sede_id' name='sede_id' value='<?php echo $capitacion->getCampo("sede_id"); ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right ><b>Tipo de Persona: </b></td>
			<td>
				<select class='lista-buscador_tipopersona' id='tipopersona' name='tipopersona' style='padding:1px; width: 220px;'>
					<option value="-1" selected="selected">Seleccione un tipo de persona</option>
					<option value="<?php echo $capitacion->getCampo("tipopersona_id"); ?>" selected="selected"><?php echo $lista->getCampo("nombre") ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right ><b>Cantidad: </b></td>
			<td><input type="text" style="width:220px;" name='cantidad' id='cantidad' value="<?php echo $capitacion->getCampo("cantidad"); ?>"></td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Capitación",
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
							$("#formNuevoCapitacion").submit();
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

		
		$("#formNuevoCapitacion").validate({
			rules: {
				cantidad : {
					required: true,
					number: true
				}
			},
			messages: {
				cantidad : ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formNuevoCapitacion").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevoCapitacion').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista_capitacion").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible crear la capitación<br />", "error");
					}
				}});
				return false;
			}
		});

		$("#tipopersona").select2({
			placeholder: {
				id: '-1', // the value of the option
				text: 'Seleccione un tipo de persona...'
			},
			allowClear: true,
			ajax: {
				url: '<?php echo DIR_WEB; ?>load_tipos_personas.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
					
					return {
						results: $.map(data, function(obj) {
						return { id: obj.id, text: obj.text };
						})
					};
				}
			}
		});
		
		hideMessage();
	});

</script>
