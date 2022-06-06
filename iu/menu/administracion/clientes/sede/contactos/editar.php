<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	//define("MODO_DEBUG",true);
	
	$contacto_id = isset($_POST["contacto_id"]) ? intval($_POST["contacto_id"]) :  die("Error al recibir el ID del contacto");
	
	$contacto = new Contacto();
	if (!$contacto->load($contacto_id)) die ("error al cargar la información del contacto");
	
	if (isset($_POST["nombre"]) && isset($_POST["email"]) && isset($_POST["contacto_tipo_id"]) && isset($_POST["cargo"]) && isset($_POST["identificacion"]))
	{
		die ($contacto->update($_POST) ? "ok" : BD::getLastError());		
	}
		
?>
<form method="post" name="formEditarContacto" id="formEditarContacto" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='contacto_id' name='contacto_id' value='<?php echo $contacto_id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Identificación: </b></td>
			<td><input style="width:220px;" type=text name='identificacion' id='identificacion' value="<?php echo $contacto->getCampo("identificacion");?> "></td>
		</tr>
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=80 style='width:220px;' id='nombre' name='nombre' value="<?php echo $contacto->getCampo("nombre");?> "></td>
		</tr>
		<tr>
			<td align=right><b>Cargo: </b></td>
			<td><input type=text  style='width:220px;' id='cargo' name='cargo' value="<?php echo $contacto->getCampo("cargo");?> "></td>
		</tr>
		<tr>
			<td align=right><b>Teléfono: </b></td>
			<td><input type=text  style='width:220px;' id='telefono' name='telefono' value="<?php echo $contacto->getCampo("telefono");?> "></td>
		</tr>
		<tr>
			<td align=right><b>Email: </b></td>
			<td><input type=text  style='width:220px;' id='email' name='email' value="<?php echo $contacto->getCampo("email");?> "></td>
		</tr>
		<tr>
			<td align=right><b>Tipo:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:220px;' name='contacto_tipo_id' id='contacto_tipo_id' >
					<option value="">Seleccione un tipo de contacto...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions($contacto->getCampo("contacto_tipo_id"), array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_CONTACTO"));
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar Contacto",
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
							$("#formEditarContacto").submit();
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
		
		$("#formEditarContacto").validate({
			rules: {
				nombre : "required",
				identificacion : "required",
				telefono: "required",
				cargo: "required"
			},
			messages: {
				nombre : "",
				identificacion : "",
				telefono: "",
				cargo: ""
			},
			submitHandler: function(e) {
				showMessage();
				$("#formEditarContacto").find('input:text').each(function () {
					if($(this).val()!=''){
						$(this).val($.trim($(this).val()));
					}
				});
				$('#formEditarContacto').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana3").dialog("destroy");
						$('#contacto').trigger('change.select2');
						$("#lista_contacto").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible editar el contacto<br />", "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});

</script>
