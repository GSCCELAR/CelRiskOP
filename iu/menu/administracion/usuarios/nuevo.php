<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	if (isset($_POST["perfil_id"]) && isset($_POST["usuario"]) && isset($_POST["clave"])) {
		$_POST["clave"] = md5($_POST["clave"]);
		$usu = new Usuario($_POST);
		die ($usu->save() ? "ok@". $usu->id : BD::getLastError());
	}
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name="cliente_id" id="cliente_id" value="">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Identificaci�n: </b></td>
			<td><input style="width:250px;" maxlength="15" type=text name='identificacion' id='identificacion'></td>
		</tr>
		<tr>
			<td style="width:120px;" align=right><b>Nombre completo: </b></td>
			<td>
				<input style="width:115px;" maxlength="60" type=text name='nombre' id='nombre' value='' placeholder="Nombre">
				<input style="width:118px;" maxlength="60" type=text name='apellidos' id='apellidos' value='' placeholder="Apellidos">
			</td>
		</tr>
		<tr>
			<td align=right><b>Correo: </b></td>
			<td><input maxlength="120" style="width:250px;" type=text name='correo' id='correo' value=''></td>
		</tr>
		<tr>
			<td align=right><b>Perfil: </b></td>
			<td>
				<select name='perfil_id' id='perfil_id' style="width:265px;">
					<option value="">Seleccione...</option>
					<?php
						$p = new Perfil();
						if(Aplicacion::getPerfilID() == 9) 
							$p->writeOptions(-1, array("id", "nombre"),array("id" => "11"));
						else
							$p->writeOptions();

					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<hr size=1 border=1 bordercolor="#EFEFEF">
			</td>
		</tr>
		<tr>
			<td align=right style='width:100px;'><b>Usuario: </b></td>
			<td><input style="width:250px;" maxlength="30" type=text name='usuario' id='usuario' value=''></td>
		</tr>
		<tr>
			<td align=right><b>Estado: </b></td>
			<td>
				<select name='estado' id='estado' style='width:265px;'>
					<option value="">Seleccione...</option>
					<option value='1'>ACTIVO</option>
					<option value='0'>INACTIVO</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Clave: </b></td>
			<td><input style="width:250px;" type=password name='clave' id='clave' value=''></td>
		</tr>
		<tr>
			<td align=right><b>Repetir Clave: </b></td>
			<td><input style="width:250px;" maxlength="100" style="width:250px;" type=password name='clave2' id='clave2' value=''></td>
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
			title: "<i class='icon-white icon-plus-sign' /> &nbsp; Nuevo Usuario",
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
						title : '',
						text : '�Confirma?',
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
		$("#perfil_id,#estado").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		
		$("#formEditar").validate({
			rules: {
				identificacion : {
					required : true,
					digits : true
				},
				nombre : "required",
				apellidos : "required",
				correo : {
					email : true,
					required : true
				},
				usuario : "required",
				estado : "required",
				perfil_id : "required",
				clave : "required",
				clave2 : {
					required : true,
					equalTo : "#clave"
				}
			},
			messages: {
				identificacion : "",
				nombre : "",
				apellidos : "",
				correo : "",
				perfil_id : "",
				usuario : "",
				estado : "",
				clave : "",	
				clave2 : ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditar').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok/.test(resp)) {
						var data = resp.split("@");
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
						editar(data[1]);
						$("#busca_nombre").focus();
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
		$("#nombre").val("");
		$("#formEditar").css("font-size", "14px");
		hideMessage();
	});
</script>