<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG",true);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : (isset($_POST["cliente_id"]) ? intval($_POST["cliente_id"]) : die("Error al recibir el ID"));
	
	if (isset($_POST["nombre"]) && isset($_POST["direccion"]) &&isset($_POST["email"]) && isset($_POST["valor"]) && isset($_POST["municipio_id"]) && isset($_POST["tipoinmobiliario_id"]))
	{
		$sede = new Sede($_POST);
		die ($sede->save() ? "ok" : BD::getLastError());
	}

	$usuario_zona = array();
	$sql = BD::sql_query("SELECT zona_id FROM usuario_zona WHERE usuario_id = ". Aplicacion::getIDUsuario());
	while ($r = BD::obtenerRegistro($sql)) {
		$usuario_zona[] = $r["zona_id"];
	}
		
?>
<form method="post" name="formNuevaSede" id="formNuevaSede" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" id='cliente_id' name='cliente_id' value='<?php echo $id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=80 style='width:290px;' id='nombre' name='nombre'></td>
		</tr>
		<tr>
			<td align=right><b>Dirección: </b></td>
			<td><input type=text maxlength=120 style='width:290px;' id='direccion' name='direccion' ></td>
		</tr>
		<tr>
			<td align=right><b>Email: </b></td>
			<td><input type=text maxlength=120 style='width:290px;' id='email' name='email' ></td>
		</tr>
		<tr>
			<td align=right><b>Metros Cuadrados: </b></td>
			<td><input type=text maxlength=120 style='width:290px;' id='metros_cuadrados' name='metros_cuadrados' ></td>
		</tr>
		<tr>
			<td align=right><b>Teléfono: </b></td>
			<td><input type=text maxlength=120 style='width:290px;' id='telefono' name='telefono' ></td>
		</tr>
		<tr>
			<td align=right><b>Valor: </b></td>
			<td><input type=text maxlength=120 style='width:290px;' id='valor' name='valor' ></td>
		</tr>
		<tr>
			<td align=right><b>Municipio:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:304px;' name='municipio_id' id='municipio_id'>
				<option value="">Seleccione...</option>
					<?php
						$municipio = new Municipio();
						$municipio->writeOptions(-1, array("id", "nombre"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Tipo Inmobiliario:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:304px;' name='tipoinmobiliario_id' id='tipoinmobiliario_id'>
				<option value="">Seleccione...</option>
					<?php
						$lista = new Lista();
						$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_INMOBILIARIO"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Zona:</b></td>
			<td>
				<select style='padding:1px; height:25px; width:304px;' name='zona_id' id='zona_id'>
				<option value="">Seleccione...</option>
					<?php
						$lista = new Lista();
						if(Aplicacion::getPerfilID() == 9)
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"), " AND id IN (".implode(",",$usuario_zona).")");
						else
							$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"));
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align=right><b>Localización:</b></td>
			<td>
				<input type="text" id='longitud' name='longitud' style="width:140px;" value="">
				<input type="text" id='latitud' name='latitud' style="width:140px;" value="">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="map_nuevo" style="width: 450px; height: 250px;"></div>
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
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Nueva Sede",
			resizable: false,
			width: 500,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 30,
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
							$("#formNuevaSede").submit();
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
		
		$("#formNuevaSede").validate({
			rules: {
				nombre : "required",
				direccion : "required",
				telefono: "required",
				valor: "required",
				email: {
				required: true,
				email: true
				}
			},
			messages: {
				nombre : "",
				direccion : "",
				telefono: "",
				valor: "",
				email: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formNuevaSede').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						Swal.fire({
						title : '',
						text : '¿Desea adicionar otra sede?',
						type : 'question',
						showCancelButton : true,
						confirmButtonText: 'Si',
						cancelButtonText: 'No'
					}).then(function (res) {
						if (res.value)
						{
							$("#nombre,#email,#direccion,#metros_cuadrados,#telefono,#valor").val("");
							$('#municipio_id,#tipoinmobiliario_id').prop('selectedIndex',0);
						}else if (res.dismiss === Swal.DismissReason.cancel)
						{
							$("#ventana2").dialog("destroy");
							$("#lista_sede").trigger("reloadGrid");
						}
					});
					}
					else {
						mensaje("Error", "No fue posible actualizar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});

		$("#tipoinmobiliario_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#zona_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#municipio_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});

		hideMessage();
		latlng = new L.LatLng(5.0625697, -75.4956072);

		var mbAttr = '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors';
		var osm_tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: mbAttr });
		map_nueva_sede = L.map('map_nuevo', {
			zoomControl: true,
			fullscreenControl: true,
			layers: [osm_tile],
			center: latlng
		});

		L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 18
		}).addTo(map_nueva_sede);
		latlng = new L.LatLng(5.0625697, -75.4956072);
		marker = L.marker(latlng, {
			icon: gscIcon
		}).addTo(map_nueva_sede);
		map_nueva_sede.on('click', onMapClick);
		marker.on('moveend', onMoveEnd);
		map_nueva_sede.setView(latlng, 15);
		
		$("input[name=\"longitud\"]").val(latlng.lng);
		$("input[name=\"latitud\"]").val(latlng.lat);
	});

	function onMoveEnd(e)
	{
		marker.setLatLng([e.target._latlng.lat,e.target._latlng.lng]);
	    $("input[name=\"longitud\"]").val(e.target._latlng.lng);
		$("input[name=\"latitud\"]").val(e.target._latlng.lat);
	}
	function onMapClick(e) {
		marker.setLatLng([e.latlng.lat,e.latlng.lng]);
		$("input[name=\"longitud\"]").val(e.latlng.lng);
		$("input[name=\"latitud\"]").val(e.latlng.lat);
	}
</script>
