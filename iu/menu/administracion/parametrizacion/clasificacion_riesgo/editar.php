<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
	
	$clasificacion_riesgo_id = isset($_POST["clasificacion_riesgo_id"]) ? intval($_POST["clasificacion_riesgo_id"]) :  die("Error al recibir el ID de la clasificación riesgo");
	
	$clasificacion_riesgo = new ClasificacionRiesgo();
	if (!$clasificacion_riesgo->load($clasificacion_riesgo_id)) die ("error al cargar la información de la clasificación riesgo");
	
	if (isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"]) && isset($_POST["mnivel_riesgo_id"]))
	{
		die ($clasificacion_riesgo->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarClasificacionRiesgo" id="formEditarClasificacionRiesgo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='clasificacion_riesgo_id' name='clasificacion_riesgo_id' value='<?php echo $clasificacion_riesgo_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Probabilidad: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='mprobabilidad_id' id='mprobabilidad_id'>
				<option value="">Seleccione una probabilidad...</option>
					<?php
						$lista = new Probabilidad();
						$lista->writeOptions($clasificacion_riesgo->getCampo("mprobabilidad_id"), array("id", "calificacion"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Severidad: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='mseveridad_id' id='mseveridad_id'>
				<option value="">Seleccione una severidad...</option>
					<?php
						$lista = new Severidad();
						$lista->writeOptions($clasificacion_riesgo->getCampo("mseveridad_id"), array("id", "calificacion"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Nivel Riesgo: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='mnivel_riesgo_id' id='mnivel_riesgo_id'>
				<option value="">Seleccione un nivel de riesgo...</option>
					<?php
						$lista = new NivelRiesgo();
						$lista->writeOptions($clasificacion_riesgo->getCampo("mnivel_riesgo_id"), array("id", "calificacion"));
					?>
				</select>
			</td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Clasificación Riesgo",
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
							$("#formEditarClasificacionRiesgo").submit();
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
	
		$("#formEditarClasificacionRiesgo").validate({
			rules: {
				calificacion : "required",
				criterio : "required",
				descripcion: "required"
			},
			messages: {
				calificacion : "",
				criterio : "",
				descripcion: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formEditarClasificacionRiesgo").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarClasificacionRiesgo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar la clasificación riesgo<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
