<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	BD::changeInstancia("mysql");
	//define("MODO_DEBUG", true);	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID de la sede");
	$sector_id = isset($_POST["sector_id"]) ? intval($_POST["sector_id"]) : die("Error al obtener el ID del sector");
	$proceso_id = isset($_POST["proceso_id"]) ? intval($_POST["proceso_id"]) : die("Error al obtener el ID del proceso");
	
	
	$sede = new Sede();
	if (!$sede->load($id)) die ("error al cargar la información de la sede");

	if (isset($_POST["nombre"]) && isset($_POST["direccion"]) &&isset($_POST["email"]) && isset($_POST["valor"]) && isset($_POST["municipio_id"]) && isset($_POST["tipoinmobiliario_id"]))
	{
		$return = $sede->update($_POST);
		if($return)
			file_put_contents(DIR_APP . "api/version.txt", time());
		die ( $return ? "ok" : "err");
	}

	$usuario_zona = array();
	$sql = BD::sql_query("SELECT zona_id FROM usuario_zona WHERE usuario_id = ". Aplicacion::getIDUsuario());
	while ($r = BD::obtenerRegistro($sql)) {
		$usuario_zona[] = $r["zona_id"];
	}
?>
    <table>
		<tr>
			<td >
				<form method="post" name="formEditarSede" id="formEditarSede" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
				<input type="hidden" name='id' value="<?php echo $sede->id; ?>">
				<input type="hidden" name='sector_id' value="<?php echo $sector_id; ?>">
				<input type="hidden" name='proceso_id' value="<?php echo $proceso_id; ?>">
				<input type="hidden" name='cliente_id' value="<?php echo $sede->getCampo("cliente_id"); ?>">
				<input type="hidden" name='longitud' value="<?php echo $sede->getCampo("longitud"); ?>">
				<input type="hidden" name='latitud' value="<?php echo $sede->getCampo("latitud"); ?>">
				<input type="hidden" name='estado' value="<?php echo $sede->getCampo("estado"); ?>">
				<table cellpadding=3 border=0 >
				
					<tr>
						<td align=right><b>Nombre: </b></td>
						<td><input type=text maxlength=80 style='width:150px;' id='nombre' name='nombre' value='<?php echo $sede->getCampo("nombre", true);?>'></td>
						<td align=right><b>Metros Cuadrados: </b></td>
						<td><input type=text onkeypress="return solo_numeros(event);" maxlength=120 style='width:150px;' id='metros_cuadrados' name='metros_cuadrados' value='<?php echo $sede->getCampo("metros_cuadrados", true);?>'></td>
					</tr>
					<tr>
						<td align=right><b>Dirección: </b></td>
						<td><input type=text maxlength=120 style='width:150px;' id='direccion' name='direccion' value='<?php echo $sede->getCampo("direccion", true);?>'></td>
						<td align=right><b>Teléfono: </b></td>
						<td><input type=text maxlength=120 style='width:150px;' id='telefono' name='telefono' value='<?php echo $sede->getCampo("telefono", true);?>'></td>
					</tr>
					<tr>
						<td align=right><b>Email: </b></td>
						<td><input type=text maxlength=120 style='width:150px;' id='email' name='email' value='<?php echo $sede->getCampo("email", true);?>'></td>
						<td align=right><b>Valor: </b></td>
						<td><input type=text onkeypress="return solo_numeros(event);" maxlength=120 style='width:150px;' id='valor' name='valor' value='<?php echo $sede->getCampo("valor", true);?>'></td>
					</tr>
					<tr>
						<td align=right><b>Municipio:</b></td>
						<td>
							<select style='padding:1px; height:25px; width:164px;' name='municipio_id' id='municipio_id'>
							<option value="">Seleccione...</option>
								<?php
									$municipio = new Municipio();
									$municipio->writeOptions($sede->getCampo("municipio_id"), array("id", "nombre"));
								?>
							</select>
						</td>
						<td align=right><b>Tipo Inmobiliario:</b></td>
						<td>
							<select style='padding:1px; height:25px; width:164px;' name='tipoinmobiliario_id' id='tipoinmobiliario_id'>
							<option value="">Seleccione...</option>
								<?php
									$lista = new Lista();
									$lista->writeOptions($sede->getCampo("tipoinmobiliario_id"), array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_INMOBILIARIO"));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align=right><b>Zona:</b></td>
						<td>
							<select style='padding:1px; height:25px; width:164px;' name='zona_id' id='zona_id'>
							<option value="">Seleccione...</option>
								<?php
									$lista = new Lista();
									if(Aplicacion::getPerfilID() == 9)
										$lista->writeOptions($sede->getCampo("zona_id"), array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"), " AND id IN (".implode(",",$usuario_zona).")");
									else
										$lista->writeOptions($sede->getCampo("zona_id"), array("id", "nombre"), array("tipo_lista_codigo" => "ZONAS"));
								?>
							</select>
						</td>

						<td align=right><!--<b>Total expuestos</b>--></td>
						<td><!--<input type=text maxlength=120 style='width:150px;' id='expuestos' name='expuestos'>--></td>



						<!--
						<td align=right><b>Matriz de riesgos operativos</b></td>
						<td>
							<img style='margin:3px;cursor:pointer;width:22px;height:22px;' onclick='verMatrizop(<?php echo $id; ?>)' src='imagenes/menu/matriz.png' title='Ver Matriz Operaciones'>
							<img style='margin:3px;cursor:pointer' onclick='copiarMatrizop(<?php echo $id; ?>)' src='imagenes/copiar.png' title='Copiar Matriz Operaciones'>
							<img style='margin:3px;cursor:pointer;' onclick='editarmatrizop(<?php echo $id; ?>)' src='imagenes/editar.png' title='Editar matrix'>
							<img style='margin:3px;cursor:pointer;'  onclick='eliminarMatrizop(<?php echo $id; ?>)' src='imagenes/eliminar.png' title='Eliminar'>
						</td>
						-->


					</tr>
					<tr>
						<td colspan="4">
							<div id="map_editar" style="width: 550px; height: 280px;"></div>
						</td>
					</tr>
				
				</table>
				</form>
			</td>
			<td >			
				<table border=0 >
					<tr>
						<div>
							<div id="tabs" >
									<ul>
										
										<li><a href="#escenarios" >Escenarios</a></li>
										<li><a href="#contactos">Contactos</a></li>
										<li><a href="#disp_seguridad">Disp Seg</a></li>
										<li><a href="#matrizop" >Riesgos Operativos </a></li>
										<!--<li><a href="#capitacion">Capita</a></li>-->
										<li><a href="#tels_emergencia">Telefonos</a></li>
										
									</ul>
									
									<!--
										<div url="riesgoescenario/index.php" id="escenarios" style="height:350px; overflow: auto;" class="tabs">
									-->
									<div url="escenario/index.php" id="escenarios" style="height:350px; overflow: auto;" class="tabs">
									</div>

									<div url="contactos/index.php" id="contactos" style="height:350px; overflow: auto;" class="tabs">
									</div>
									<div url="seguridad/index.php" id="disp_seguridad" style="height:350px; overflow: auto;" class="tabs"> 
									</div>
									<div url="matrizop/index.php" id="matrizop" style="height:350px; overflow: auto;" class="tabs">
									</div>
									<!--<div url="capitacion/index.php" id="capitacion" style="height:350px; overflow: auto;" class="tabs">
									</div>-->
									<div url="emergencia/index.php" id="tels_emergencia" style="height:350px; overflow: auto;" class="tabs">
									</div>
									
								</div>
						</div>
							
					</tr>
				</table>
				
			</td>
		</tr>
	</table>
	

<script type="text/javascript">
	var marker;
	var map_editar_sede;
	$(document).ready(function() {
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar sede",
			resizable: false,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 1200,
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
						$("#formEditarSede").submit();
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
		$("#formEditarSede").validate({
			rules: {
				nombre : "required",
				direccion : "required",
				telefono: "required",
				valor: "required",
				metros_cuadrados : "required",
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
				metros_cuadrados : "",
				email: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditarSede').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista_sede").trigger("reloadGrid");
					
					}
					else {
						mensaje("Error", "No fue posible actualizar la sede<br />" + resp, "error");
					}
				}});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});
		$("#formEditarSede").css("font-size", "14px");
		$( "#tabs" ).tabs({
			selected: 0,
			select: function(event, ui) {
				var id = ui.tab.attributes.href.value;
				$(id).load("<?php echo DIR_WEB; ?>/" + $(id).attr("url")+"?id="+<?php echo $sede->id ?>+"&sector_id=<?php echo $sector_id;?>&proceso_id=<?php echo $proceso_id;?>");
			},
			create: function( event, ui ) {
				$("#escenarios").load("<?php echo DIR_WEB; ?>/" + $("#escenarios").attr("url")+"?id="+<?php echo $sede->id ?>);
				//$("#contactos").load("<?php echo DIR_WEB; ?>/" + $("#contactos").attr("url")+"?id="+<?php echo $sede->id ?>);
				
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
		latlng = L.latLng($("input[name=\"latitud\"]").val(), $("input[name=\"longitud\"]").val());

		var mbAttr = '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors';
		var osm_tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: mbAttr });
		map_editar_sede = L.map('map_editar', {
			zoomControl: true,
			fullscreenControl: true,
			layers: [osm_tile],
			center: latlng
		});

		L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 18
		}).addTo(map);
		marker = L.marker(latlng, {
			icon: gscIcon,
			draggable: true
		}).addTo(map_editar_sede);
		map_editar_sede.on('click', onMapClick);
		marker.on('moveend', onMoveEnd);
		map_editar_sede.setView(latlng, 15);
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