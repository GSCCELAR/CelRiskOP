<div class="login-outer">
	<div class="login-middle">
		<form id="formLogin" method="POST" class="form-signin" action="index.php">
			<table cellspacing="3" width="240" align="center" class="animated fadeIn">
				<tr>
					<td align="center"><img width="200" src="imagenes/logo_top.png" alt=""></td>
				</tr>
				<tr>
					<td align="center">
						<table class="ui-corner-all" style="margin-top:5px;margin-bottom:5px;">
							<tr>
								<td style='text-align:center;'>&nbsp;</td>
							</tr>
							<tr>
								<td>
									<div class="input-prepend" style='margin-bottom:10px;'>
										<span class="add-on"><i class="icon-user"></i></span>
										<input style='width:200px;' autocomplete="off" name='usuario' id='usuario' type="text" placeholder="Usuario" />
									</div>
								</td>
							</tr>
							<tr>
								<td align="right">
									<div class="input-prepend" style='margin-bottom:10px;'>
										<span class="add-on"><i class="icon-lock"></i></span>
										<input style='width:200px;' type="password" id='clave' name='clave' placeholder="Clave" />
									</div>
								</td>
							</tr>
							<tr>
								<td align=right style="padding-bottom:6px;">
									<input type="submit" class='btn btn-block btn-large btn-primary' style='font-size: 14px; font-weight: bold;' name='login' id="btnLogin" value='Ingresar'>
									<input type="button" class='btn btn-link btn-error' style='font-size: 14px; font-weight: bold;' name='forgot' id="btnOlvido" value='¿Olvidaste la clave?'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		
		$("#usuario").focus();
		<?php
			if (isset($_POST["login"]) && isset($_POST["usuario"]) && $_POST["usuario"] != "")
			{
				if(Aplicacion::getPerfilID() == 11)
					echo 'mensaje("Permiso denegado", "El usuario no tiene permiso para ingresar", "error");';
				else
					echo 'mensaje("Clave incorrecta", "Usuario o contraseña inválidos", "error");';
			}    
				
		?>
		
		$("#formLogin").submit(function() {
			if ($.trim($("#usuario").val()) == "") {
				mensaje("Datos incompletos", "Ingrese un nombre de <b>usuario</b> válido", "info");
				$("#usuario").focus();
				return false;
			}
			if ($.trim($("#clave").val()) == "") {
				mensaje("Datos incompletos", "El campo <b>clave</b> está vacío", "info");
				$("#clave").focus();
				return false;
			}
		});
		
		$("#mensajes").center({
			vertical:false
		});

		$("#btnOlvido").click(function() {
			mensaje('Restablecimiento de contraseña', 'Con esta opción recibirás un correo electrónico con instrucciones para restablecer tu clave de acceso. Solo debes ingresar correctamente los datos que se solicitan a continuación', 'info', function () {
				Swal.mixin({
					input: 'text',
					confirmButtonText: 'Siguiente &rarr;',
					cancelButtonText: 'Cancelar',
					showCancelButton: true,
					progressSteps: ['1', '2']
				}).queue([
					'Usuario:',
					'Correo electrónico:'
				]).then((result) => {
					console.log(result);
					if (result.value && result.value.length == 2) {
						showMessage();
						$.post("index.php", { restablecer : true, identificacion : result.value[0], correo: result.value[1] }, function (resp) {
							hideMessage();
							if (/^ok$/.test(resp))
								mensaje("¡Proceso existoso!", "Hemos validado tus datos, sigue las instrucciones que hemos enviado a <b>" + result.value[1] + "</b> para restablecer tu clave de acceso", "success");
							else
								mensaje("Error", resp, "error");
						})
					}
				});
			});
		});
	});
</script>