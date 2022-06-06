<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	//define("MODO_DEBUG",true);
	
	$sede_id = isset($_GET["sede_id"]) ? intval($_GET["sede_id"]) :  (isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) :  die("Error al recibir el ID de la sede"));
	
	if (isset($_POST["nombre"]) && isset($_POST["email"]) && isset($_POST["contacto_tipo_id"]) && isset($_POST["cargo"]) && isset($_POST["identificacion"]))
	{
		$contacto = new Contacto($_POST);
		die ($contacto->save() ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formNuevoContacto" id="formNuevoContacto" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='sede_id' name='sede_id' value='<?php echo $sede_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Identificación: </b></td>
			<td><input style="width:220px;" type=text name='identificacion' id='identificacion'></td>
		</tr>
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=80 style='width:220px;' id='nombre' name='nombre'></td>
		</tr>
		<tr>
			<td align=right><b>Cargo: </b></td>
			<td><input type=text  style='width:220px;' id='cargo' name='cargo' ></td>
		</tr>
		<tr>
			<td align=right><b>Teléfono: </b></td>
			<td><input type=text  style='width:220px;' id='telefono' name='telefono' ></td>
		</tr>
		<tr>
			<td align=right><b>Email: </b></td>
			<td><input type=text  style='width:220px;' id='email' name='email' ></td>
		</tr>
		<tr>
			<td align=right><b>Tipo:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:220px;' name='contacto_tipo_id' id='contacto_tipo_id'>
					<option value="">Seleccione un tipo de contacto...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_CONTACTO"));
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Nuevo Contacto",
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
							$("#formNuevoContacto").submit();
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

		$("#contacto_tipo_id").select2({
			placeholder: 'Seleccione un tipo de contacto...',
			allowClear: true
		});
		
		$("#formNuevoContacto").validate({
			rules: {
				nombre : "required",
				identificacion : "required",
				telefono: "required",
				cargo: "required",
				email: {
				required: true,
				email: true
				}
			},
			messages: {
				nombre : "",
				identificacion : "",
				telefono: "",
				cargo: "",
				email: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formNuevoContacto").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formNuevoContacto').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$("#contacto").val(null).trigger('change');
					}
					else {
						mensaje("Error", "No fue posible crear el contacto<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
