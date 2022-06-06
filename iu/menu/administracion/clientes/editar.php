<?php
define("iC", true);
require_once(dirname(__FILE__) . "/../../../../conf/config.php");
Aplicacion::validarAcceso(9,10);
//define("MODO_DEBUG", true);	
$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID del cliente");

$cli = new Cliente();
if (!$cli->load($id)) die("error al cargar la información del cliente");

if (isset($_POST["tipopersona_id"]) && isset($_POST["tipodocumento_id"]) && isset($_POST["razon_social"])  && isset($_POST["sector_id"])) {
	die($cli->update($_POST) ? "ok" : "err");
}

if (isset($_POST["delete_sede"])) {
	$reg = new Sede();
	$reg->load($_POST["delete_sede"]) or die("Error al cargar la información");
	die ($reg->delete() ? "ok" : "Hay información relacionada con esta sede. No se puede borrar la información"); 
}
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value="<?php echo $cli->id; ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Tipo Persona: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:236px;' name='tipopersona_id' id='tipopersona_id'>
				<option value="">Seleccione...</option>
					<?php
					$lista = new Lista();
					$lista->writeOptions($cli->getCampo("tipopersona_id"), array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_PERSONA"));
					?>
				</select>
			</td>
			<td style="width:120px;" align=right><b>Razon Social: </b></td>
			<td>
				<input style="width:260px;" maxlength="200" type=text name='razon_social' id='razon_social' value='<?php echo $cli->getCampo("razon_social", true); ?>' placeholder="Cliente">
			</td>
		</tr>
		<tr>
			<td align=right><b>Identificación: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:70px;' name='tipodocumento_id' id='tipodocumento_id'>
				<option value="">Seleccione...</option>
					<?php
					$lista = new Lista();
					$lista->writeOptions($cli->getCampo("tipodocumento_id"), array("id", "nombre"), array("tipo_lista_codigo" => "TIPO_DOCUMENTO"));
					?>
				</select>
				<input style="width:150px;" maxlength="15" type=text name='identificacion' id='identificacion' value='<?php echo $cli->getCampo("identificacion", true); ?>'>
				<?php 	$visible = "visible";
						if($cli->getCampo("tipodocumento_id") != "14"){
							$visible = "hidden";
						} ?>
				<span id="digito" name="digito" style="visibility:<?php echo $visible; ?>">-</span>
				<input maxlength="1" style="width:30px;text-align:center;visibility:<?php echo $visible; ?>" type='text' name='digito_verificacion' id='digito_verificacion' value='<?php echo $cli->getCampo("digito_verificacion", true); ?>' ></td>
			<td align=right><b>Sector: </b></td>
			<td>
				<select style='padding:1px; height:25px; width:274px;' name='sector_id' id='sector_id'>
				<option value="">Seleccione...</option>
					<?php
					$lista = new Lista();
					$lista->writeOptions($cli->getCampo("sector_id"), array("id", "nombre"), array("tipo_lista_codigo" => "SECTOR"));
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
					$lista->writeOptions($cli->getCampo("proceso_id"), array("id", "nombre"));
					?>
				</select>
			</td>
		</tr>
	</table>
</form>
<hr>
<table>
	<tr>
		<td colspan=2 align="right">
			<button title="Nueva Sede" onclick="nuevaSede(<?php echo $cli->id;?>)" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i>Nueva</button>
		</td>
		<td colspan="2" >
			
			<div class="form-group has-search">
				<input id='busca_sede_cliente'  type=text name='busca_sede_cliente'  placeholder="Búsqueda" style="width:350px;">
				<span class="fa fa-search "></span>
			</div>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td valign="top" >
			<table id="lista_sede" class="scroll" cellpadding="0" cellspacing="0" style="font-size:12px; border-collapse: none;"></table>
			<div id="paginador_sede" class="scroll" style="text-align:left; font-size:10px;"></div>
		</td>
		<td valign="top">
			<div id="map" style="width: 430px; height: 350px;top:0px"></div>
		</td>
	</tr>
</table>
	
<script type="text/javascript">
	var marker;
	var lastSel;
	var latlng;
	$(document).ready(function() {
	var myGrid = $("#lista_sede").jqGrid({
		    url : '<?php echo DIR_WEB; ?>sede/lista.php?cliente_id='+<?php echo $cli->id; ?>,
		    datatype : "json",
		    colNames : [ '<b>Sede / Dirección - Ciudad</b>','<b>Teléfono</b>','<b>Opc.</b>'],
		    colModel : [
		        { name:'nombre', index:'nombre', width:330, align: 'left' },
		        { name:'telefono', index:'nombre', width:60, align: 'left' },
		        { name:'opciones', index:'opciones', width:80, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_sede'),
			rowNum : 15,
		    rowList : [10, 15, 20],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'nombre',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "desc",
			height: "auto",
			caption: "<i class='icon-list icon-white' /> <b>SEDES</b>",
			onSelectRow: function(data_id) { 
				var data = data_id.split("@");
				var coords = data[1].split("#");
				map.flyTo([coords[0], coords[1]], 15);
			},
			loadComplete : function (res) {

				if(res.data.length > 0){
					var all_coords = [];
				
					//Lea en leaflet cómo limpiar los markers, y en esta linea los debe borrar para que los vuelva a dibujar
					//map.limpiarMarkers();
					map.eachLayer(function (layer) { 
					//Acá debo revisar si son markers
						if(layer.options && layer.options.pane === "markerPane")
							map.removeLayer(layer);
					});
					for (i in res.data) {
						coord = [res.data[i][0], res.data[i][1]];
						marker = L.marker(coord, {
							icon: gscIcon
						}).addTo(map).bindPopup("<b>" + res.data[i][2] + "</b>");
						all_coords.push(coord);
					}
					map.flyToBounds(all_coords, 16);
				}
			}
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_sede").removeClass("ui-corner-bottom");

		$("#ventana").dialog("destroy");
		$("#ventana").dialog({
			modal: true,
			overlay: {
				opacity: 0.4,
				background: "black"
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar cliente",
			resizable: false,
			open: function() {
				var t = $(this).parent(),
					w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 950,
			buttons: {
				"Guardar": function() {
					Swal.fire({
						title: '¿Confirma?',
						text: '',
						type: 'question',
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',
						
					}).then(function(res) {
						if (res.value)
							$("#formEditar").submit();
					});
				},
				"Cancelar": function() {
					$("#ventana").html("");
					$("#ventana").dialog("destroy");
				}
			},
			close: function() {
				$("#ventana").html("");
				$("#ventana").dialog("destroy");
			}
		});

		$("#formEditar").validate({
			rules: {
				tipodocumento_id: "required",
				razon_social: "required",
				estado: "required"
			},
			messages: {
				tipodocumento_id: "",
				razon_social: "",
				estado: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditar').ajaxSubmit({
					success: function(resp) {
						hideMessage();
						if (/^ok$/.test(resp)) {
							$("#ventana").dialog("destroy");
							$("#lista").trigger("reloadGrid");
							$("#busca_nombre").focus();
						} else {
							mensaje("Error", "No fue posible actualizar el cliente<br />" + resp, "error");
						}
					}
				});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});

		$("#formEditar").css("font-size", "14px");
		$("#sector_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#proceso_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#tipopersona_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});
		$("#tipodocumento_id").select2({
			placeholder: 'Seleccione...',
			allowClear: true
		});

		$('#tipodocumento_id').on('change.select2', function (e) {
			if ($("#tipodocumento_id").val() == "14")
				$("#digito_verificacion,#digito").css("visibility", "visible");
			else
				$("#digito_verificacion,#digito").css("visibility", "hidden");
		});

		
		hideMessage();

		var mbAttr = '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors';
		var osm_tile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: mbAttr });
		map = L.map('map', {
			zoomControl: true,
			fullscreenControl: true,
			layers: [osm_tile],
			center: latlng
		});

		L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 18
		}).addTo(map);
		
		latlng = new L.LatLng(5.0625697, -75.4956072);
		map.setView(latlng, 15);

		$("#busca_sede_cliente").keyup(function (e) {
			if (e.keyCode == 13) {
				buscarSede();
				return;
			}
		});

		<?php if (isset($_POST["nueva_sede"]) && $_POST["nueva_sede"] == "true") { ?>
		nuevaSede(<?php echo $cli->id; ?>);
		<?php } ?>

	
	});

	function buscarSede() {
		var busca_sede = $("#busca_sede_cliente").val();
		hBuscar = null;
		$("#lista_sede").setGridParam({
			url: "<?php echo DIR_WEB; ?>sede/lista.php?1=1"
				+ "&cliente_id=" + <?php echo $id;?>
				+ "&sede=" + busca_sede,
			page: 1
		}).trigger("reloadGrid");
	}

	function nuevaSede(id) {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>sede/nuevo.php", { id : id });
	}

	function editarSede(id) {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>sede/editar.php", { id : id, sector_id:"<?php echo $cli->getCampo("sector_id"); ?>",proceso_id:"<?php echo $cli->getCampo("proceso_id"); ?>"});
	}
	
	function eliminarSede(id) {
		Swal.fire({
			title : '',
			text : '¿Confirma que desea borrar la sede?',
			type : 'question',
			showCancelButton : true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then(function (res) {
			if (res.value)
			{
				showMessage();
				$.post("<?php echo DIR_WEB; ?>editar.php", { delete_sede : id, id: "<?php echo $id; ?>" }, function(res) {
					hideMessage();
					if (!/^ok$/.test(res))
						mensaje("Error", "No ha sido posible borrar el registro de la sede debido a que existe información relacionada con este cliente", "error");
					$("#lista_sede").trigger("reloadGrid");
				});
			}
		});
	}

	
</script>