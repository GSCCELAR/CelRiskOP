<?php
	define("iC", true);
	//define("DEBUG",true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	
	if (isset($_POST["razon_social"]) && isset($_POST["identificacion"]) && isset($_POST["tipopersona_id"]) && isset($_POST["sector_id"])) {
		$cliente = new Cliente($_POST);
		die ($cliente->save() ? "ok" . $cliente->id : BD::getLastError());
	}



?>
<form method="post" name="formNuevo" id="formNuevo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name="estado" id="estado" value="1">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Tipo Persona</b></td>
			<td>
				<select style='padding:1px; height:25px; width:275px;' name='tipopersona_id' id='tipopersona_id'>
					<option value="">Seleccione...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_PERSONA"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Identificación: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:80px;' name='tipodocumento_id' id='tipodocumento_id'>
					<option value="">Seleccione...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_DOCUMENTO"));
					?>
				</select>
			<input style="width:120px;" maxlength="15" type=text name='identificacion' id='identificacion'>
			<span id="digito" name="digito">-</span>
 			<input maxlength="1" style="width:30px;text-align:center;visibility:visible" type=text name='digito_verificacion' id='digito_verificacion' value=''></td>
		</tr>
		<tr>
			<td style="width:120px;" align=right><b>Razon Social: </b></td>
			<td>
				<input style="width:260px;" maxlength="200" type=text name='razon_social' id='razon_social' value='' placeholder="Cliente">
			</td>
		</tr>
		<tr>
			<td align=right><b>Sector</b></td>
			<td>
				<select style='padding:1px; height:25px; width:275px;' name='sector_id' id='sector_id'>
					<option value="">Seleccione...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "SECTOR"));	
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Proceso: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:274px;' name='proceso_id' id='proceso_id'>
				<option value="">Seleccione...</option>
					<?php
					$lista = new Proceso();
					$lista->writeOptions(-1, array("id", "nombre"));
					?>
				</select>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana").dialog("destroy");
		$("#ventana").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-plus-sign' /> &nbsp; Nuevo Cliente",
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
                    $("#ventana").html("");
                    $("#ventana").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana").html("");
				$("#ventana").dialog("destroy");
	      	}
		});
		$("#tipopersona_id,#tipodocumento_id,#sector_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});	
		$("#proceso_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#formNuevo").validate({
			rules: {
				identificacion : "required",
				cliente : "required",
				estado : "required"
			},
			messages: {
				identificacion : "",
				cliente : "",
				estado : "",
			},
			submitHandler: function(e) {
				showMessage();
				$('#formNuevo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok\d{1,}/.test(resp)) {
						var cliente_id = parseInt(resp.replace("ok", ""), 10);
						if (!isNaN(cliente_id))
							nuevaSede(cliente_id);
						$("#ventana").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible registrar el usuario<br />" + resp, "error");
					}
				}});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});
		$("#cliente").val("");
		$("#formNuevo").css("font-size", "14px");
		$("#tipodocumento_id").change(function(){
			if($("#tipodocumento_id").val() == "14")
				$("#digito_verificacion,#digito").css("visibility","visible");
			else 
				$("#digito_verificacion,#digito").css("visibility","hidden");
		});
		hideMessage();
	});
</script>