<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
		
	if (isset($_POST["agenteriesgo_id"]) && isset($_POST["peligros"]) && isset($_POST["descripcion"]) && isset($_POST["efectos_salud"]))
	{
		if (isset($_POST["agenteriesgo_id"]) && !is_numeric($_POST["agenteriesgo_id"])) {
			$riesgos = array("nombre" => $_POST["agenteriesgo_id"],"descripcion" => $_POST["agenteriesgo_id"], "tipo_lista_id" => "12");
			$reg = new Lista($riesgos);
			if(!$reg->save())	
				"Error al crear el agente de riesgo";
			$_POST["agenteriesgo_id"] = $reg->id;
		}

		$peligro = new Peligro($_POST);
		die ($peligro->save() ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formNuevoPeligro" id="formNuevoPeligro" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Agentes de Riesgo: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='agenteriesgo_id' id='agenteriesgo_id'>
				<option value="">Seleccione un agente riesgo...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "AGENTE_RIESGO"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Factores de Riesgo: </b></td>
			<td><textarea name="peligros" id="peligros" rows="5" style='width:220px;'></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><textarea name="descripcion" id="descripcion" rows="5" style='width:220px;'></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Efectos Salud: </b></td>
			<td><textarea name="efectos_salud" id="efectos_salud" rows="5" style='width:220px;'></textarea></td>
		</tr>
		<tr>
			<td align=right><b>Tipos de Riesgo: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='tipo_riesgo' id='tipo_riesgo'>
				<option value="">Seleccione un tipo riesgo...</option>
					<?php
						$tipo_riesgo = new TipoRiesgo();
						$tipo_riesgo->writeOptions(-1, array("id", "nombre"));
					?>
				</select>
			</td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Nuevo Peligro",
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
							$("#formNuevoPeligro").submit();
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

		$("#agenteriesgo_id").select2({
			placeholder: 'Seleccione un agente riesgo...',
			tags: true,
			allowClear: true,
			createTag: function (params) {
				console.log(params);
				var term = $.trim(params.term);
				if (term === '') {
				return null;
				}

				return {
				id: term,
				text: term,
				newTag: true // add additional parameters
				}
			}
			
		});

		$("#tipo_riesgo").select2({
			placeholder: 'Seleccione un tipo riesgo...',
			tags: true,
			allowClear: true,
			createTag: function (params) {
				console.log(params);
				var term = $.trim(params.term);
				if (term === '') {
				return null;
				}

				return {
				id: term,
				text: term,
				newTag: true // add additional parameters
				}
			}
			
		});
		
		$("#formNuevoPeligro").validate({
			rules: {
				agenteriesgo_id : "required",
				peligros : "required",
				/*descripcion: "required",*/
				efectos_salud: "required",
				tipo_riesgo: "required"
			},
			messages: {
				agenteriesgo_id : "",
				peligros : "",
				/*descripcion: "",*/
				efectos_salud: "",
				tipo_riesgo: ""
			},
			submitHandler: function(e) {
				showMessage();
			    $("#formNuevoPeligro").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevoPeligro').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger('reloadGrid');
					}
					else {
						mensaje("Error", "No fue posible crear el peligro<br />", "error");
					}
				}});
				return false;
			}
		});

		$('#agenteriesgo_id').on('select2:select', function (e) {
			var id = e.params.data.id;
			console.log(id);
			if(id == 886)
			{
				$("#descripcion").empty();
				$("#formNuevoPeligro tr:nth-child(3)").css("display","none");
			}else
			{
				$("#formNuevoPeligro tr:nth-child(3)").css("display","contents");
			}
		});
		
		hideMessage();
	});

</script>
