<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	//define("MODO_DEBUG",true);
		
	if (isset($_POST["mprobabilidad_id"]) && isset($_POST["mseveridad_id"]) && isset($_POST["mnivel_riesgo_id"]))
	{
		$clasificacion_riesgo = new ClasificacionRiesgo($_POST);
		die ($clasificacion_riesgo->save() ? "ok" : BD::getLastError());		
	}		
?>
<form method="post" name="formNuevaClasificacionRiesgo" id="formNuevaClasificacionRiesgo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Probabilidad: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:230px;' name='mprobabilidad_id' id='mprobabilidad_id'>
				<option value="">Seleccione una probabilidad...</option>
					<?php
						$lista = new Probabilidad();
						$lista->writeOptions(-1, array("id", "calificacion"));
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
						$lista->writeOptions(-1, array("id", "calificacion"));
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
						$lista->writeOptions(-1, array("id", "calificacion"));
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Nueva Clasificación Riesgo",
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
							$("#formNuevaClasificacionRiesgo").submit();
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

		$("#mprobabilidad_id").select2({
			placeholder: 'Seleccione una probabilidad...',
			allowClear: true
		});
		$("#mseveridad_id").select2({
			placeholder: 'Seleccione una severidad...',
			allowClear: true
		});
		$("#mnivel_riesgo_id").select2({
			placeholder: 'Seleccione un nivel de riesgo...',
			allowClear: true
		});

	
		$("#formNuevaClasificacionRiesgo").validate({
			rules: {
				mprobabilidad_id : "required",
				mseveridad_id : "required",
				mnivel_riesgo_id: "required"
			},
			messages: {
				mprobabilidad_id : "",
				mseveridad_id : "",
				mnivel_riesgo_id: ""
			},
			submitHandler: function(e) {
				showMessage();
			    $("#formNuevaClasificacionRiesgo").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevaClasificacionRiesgo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#lista").trigger('reloadGrid');
					}
					else {
						mensaje("Error", "No fue posible crear la nueva clasificación de riesgo<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
