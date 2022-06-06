<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../../../../conf/config.php");
Aplicacion::validarAcceso(9, 10);

$puesto_id = isset($_POST["puesto_id"]) ? $_POST["puesto_id"] : die("Error al recibir el ID del puesto");
$sector_id = isset($_POST["sector_id"]) ? $_POST["sector_id"] : die("Error al recibir el ID de la sector");
$puesto = new DispositivoSeguridad();
if (!$puesto->load($puesto_id)) die("error al cargar la información del puesto");
define("MODO_DEBUG",true);

?>
<form method="post" name="formEditarSede" id="formEditarSede" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table style='margin-top:4px;'>
		<tr>
			<td align=right><b>Cliente:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:350px;' name='c_id' id='c_id'>
				<option value="">Seleccione...</option>
					<?php
					
						$cliente = new Cliente();
						$cliente->writeOptions(-1, array("id", "razon_social"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Sede:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:350px;' name='s_id' id='s_id'>
				<option value="">Seleccione...</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Dispositivo Seguridad:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:350px;' name='d_id' id='d_id'>
				<option value="">Seleccione...</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Nombre:</b></td>
			<td>
				<input type="text" id="nombre_dispositivo_nuevo" name="nombre_dispositivo_nuevo" style='padding:1px; height:25px; width:350px;' />
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript" charset="ISO-8859-1">
	let dispositivo;
	let cliente;
	let sede;
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Copiar Matriz <?php echo str_replace(array("\"", "'", "\n"), "", $puesto->getCampo("descripcion", true)); ?>",
			resizable: false,
			open: function() {
				var t = $(this).parent(),
					w = $(document);
				t.offset({
					top: 30,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 450,
			buttons: {
				"Copiar": function() {
					copiarMatriz();
				},
				"Cerrar": function() {
					$("#ventana3").html("");
					$("#ventana3").dialog("destroy");

				}
			},
			close: function() {
				$("#ventana3").html("");
				$("#ventana3").dialog("destroy");

			}
		});
	
		let cliente = $("#c_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});

		cliente.on("change", function (e) { 
			let	cliente_id = $(this).val();
			sede = $("#s_id").select2({
				placeholder: 'Seleccione una sede...',
				tags: false,
				allowClear: true,
				createTag: function (params) {
					var term = $.trim(params.term);
					if (term === '') {
						return null;
					}

					return {
						id: term,
						text: term,
						newTag: true // add additional parameters
					}
				},
				ajax: {
					delay: 250,
					url: '<?php echo DIR_WEB; ?>load_sedes.php',
					data: {cliente: cliente_id},
					dataType: 'json',
					type: "GET",
					data: function (params) {
						console.log(params.cliente);
						var query = {
							page: params.page || 1,
							cliente: cliente_id
						}
						return query;
					},
				cache: true
				}
				
			});
			sede.on("change",function(e){
				let sede_id = $(this).val();
				dispositivo = $("#d_id").select2({
					placeholder: 'Seleccione una dispositivo...',
					tags: false,
					allowClear: true,
					createTag: function (params) {
						var term = $.trim(params.term);
						if (term === '') {
							return null;
						}

						return {
							id: term,
							text: term,
							newTag: true // add additional parameters
						}
					},
					ajax: {
						delay: 250,
						url: '<?php echo DIR_WEB; ?>load_dispositivos.php',
						dataType: 'json',
						type: "GET",
						data: function (params) {
							var query = {
								page: params.page || 1,
								sede: sede_id
							}
							return query;
						},
					cache: true
					}
					
				});
			});
		 });
		hideMessage();
	});

	function copiarMatriz()
	{
		let dispositivo_id = $("#d_id").val();
		let nombre_dispositivo = $("#nombre_dispositivo_nuevo").val();
		if(dispositivo_id != "" && nombre != "")
		{
			showMessage();
			$.ajax({
				url: '<?php echo DIR_WEB ;?>duplicar_matriz.php',
				data: {dispositivo_original_id : dispositivo_id, nombre_dispositivo : nombre_dispositivo, dispositivo_copia_id: "<?php echo $puesto->id; ?>",sector_id :"<?php echo $sector_id; ?>"}
			}).done(function(data){
				if (/^ok$/.test(data)) {
					Swal.fire({
						title : '',
						text : 'La copia de la matriz ha sido creada correctamente',
						showCancelButton : false,
						confirmButtonText: 'Aceptar'
					}).then(function (res) {
						if (res.value)
						{
							$("#lista_seguridad").trigger("reload");
							$("#ventana3").html("");
							$("#ventana3").dialog("destroy");			
						}
				});
				} else {
					Swal.fire({
							title : 'Error',
							text : 'No fue posible copiar la matriz.',
							type : 'warning',
							showCancelButton : false,
							confirmButtonText: 'Aceptar'
						}).then(function (res) {
							if (res.value)
							{
								$("#ventana3").html("");
								$("#ventana3").dialog("destroy");			
							}
					});
				}
				
			}).complete(function(data){
				hideMessage();
			});
		}
	}
</script>