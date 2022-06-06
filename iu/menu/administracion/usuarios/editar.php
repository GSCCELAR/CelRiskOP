<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al obtener el ID del usuario");
	
	$usu = new Usuario();
	if (!$usu->load($id)) die ("error al cargar la información del usuario");
	if (isset($_POST["perfil_id"]) && isset($_POST["usuario"]) && isset($_POST["clave"])) {
		if ($_POST["clave"] == "") {
			unset($_POST["clave"]);
			unset($_POST["clave2"]);
		} else {
			$_POST["clave"] = md5($_POST["clave"]);
			unset($_POST["clave2"]);
		}
		if ($usu->getCampo("fecha_ultimoacceso") == "")
			$usu->setCampo("fecha_ultimoacceso", "_NULL");
		if ($usu->getCampo("fecha_restablecimiento") == "")
			$usu->setCampo("fecha_restablecimiento", "_NULL");
		die ($usu->update($_POST) ? "ok" : "err");
	}

	if(isset($_POST["delete"]))
	{
		$reg = new SedeUsuario();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value="<?php echo $usu->id; ?>">
	<table>
    	<tr>
			<td valign="top">
				<table cellpadding=3 border=0 width="100%">
					<tr>
						<td align=right><b>Identificación: </b></td>
						<td><input style="width:250px;" maxlength="15" type=text name='identificacion' id='identificacion' value='<?php echo $usu->getCampo("identificacion", true); ?>'></td>
					</tr>
					<tr>
						<td style="width:120px;" align=right><b>Nombre completo: </b></td>
						<td>
							<input style="width:115px;" maxlength="60" type=text name='nombre' id='nombre' value='<?php echo $usu->getCampo("nombre", true); ?>' placeholder="Nombre">
							<input style="width:118px;" maxlength="60" type=text name='apellidos' id='apellidos' value='<?php echo $usu->getCampo("apellidos", true); ?>' placeholder="Apellidos">
						</td>
					</tr>
					<tr>
						<td align=right><b>Correo: </b></td>
						<td><input maxlength="120" style="width:250px;" type=text name='correo' id='correo' value='<?php echo $usu->getCampo("correo", true); ?>'></td>
					</tr>
					<tr>
						<td align=right><b>Perfil: </b></td>
						<td>
							<select name='perfil_id' id='perfil_id' style="width:265px;">
								<option value="">Seleccione...</option>
								<?php
									$p = new Perfil();
									if(Aplicacion::getPerfilID() == 9) 
										$p->writeOptions($usu->getCampo("perfil_id"), array("id", "nombre"),array("id" => "11"));
									else
										$p->writeOptions($usu->getCampo("perfil_id"), array("id", "nombre"));
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
						<td><input style="width:250px;" maxlength="30" type=text name='usuario' id='usuario' value='<?php echo $usu->getCampo("usuario", true); ?>'></td>
					</tr>
					<tr>
						<td align=right><b>Estado: </b></td>
						<td>
							<select name='estado' id='estado' style="width:265px;">
								<option value=''>Seleccione...</option>
								<option <?php echo $usu->getCampo("estado") == "1" ? "selected='selected'" : "" ?> value='1'>ACTIVO</option>
								<option <?php echo $usu->getCampo("estado") == "0" ? "selected='selected'" : "" ?> value='0'>INACTIVO</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" style='font-size:12px; color:red;'><i><b>Nota:</b> Dejar el campo de clave vacío si desea conservar la clave actual</i></td>
					</tr>
					<tr>
						<td align=right><b>Clave: </b></td>
						<td><input autocomplete="off" style="width:250px;" type=password name='clave' id='clave' value=''></td>
					</tr>
					<tr>
						<td align=right><b>Repetir clave: </b></td>
						<td><input autocomplete="off" style="width:250px;" maxlength="100" style="width:250px;" type=password name='clave2' id='clave2' value=''></td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table border=0 >
						<tr>
							<div>
								<div id="tabs" >
										<ul>
											<li><a href="#zonas" >Zonas</a></li>
											<li><a href="#sedes">Sedes</a></li>
										</ul>
										<div url="zonas/index.php" id="zonas" style="height:350px;width:580px; overflow: auto;" class="tabs">
										</div>
										<div url="sedes/index.php" id="sedes" style="height:350px;width:580px; overflow: auto;" class="tabs">
										</div>
									</div>
							</div>
						</tr>
					</table>
			</td>
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
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar usuario",
			resizable: false,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			width: 1080,
			buttons: {
				"Guardar": function() {
					Swal.fire({
						title : '',
						text : '¿Confirma?',
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
		$("#lista_cliente").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista_cliente.php?usuario=<?php echo $id; ?>',
		    datatype : "json",
		    colNames : ['<b>Sede / Dirección</b>','<b>Ciudad</b>','<b>Teléfono</b>','<b>Opc</b>'],
		    colModel : [
		        { name:'sede', index:'sede', width:300, align: 'left' },
				{ name:'ciudad', index:'ciudad', width:100, align: 'left' },
				{ name:'telefono', index:'telefono', width:80, align: 'left' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_capitacion'),
			rowNum : 10,
		    rowList : [10],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'sede',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>SEDES USUARIO</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador_cliente").removeClass("ui-corner-bottom");

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
				codigo : "required",
				correo : {
					email : true,
					required : true
				},
				perfil_id : "required",
				usuario : "required",
				estado : "required",
				clave2 : {
					equalTo : "#clave"
				}
			},
			messages: {
				identificacion : "",
				nombre : "",
				apellidos : "",
				codigo : "",
				correo : "",
				perfil_id : "",
				usuario : "",
				estado : "",
				clave2 : ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditar').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
						$("#busca_nombre").focus();
					}
					else {
						mensaje("Error", "No fue posible actualizar el usuario<br />" + resp, "error");
					}
				}});
				return false;
			},
			success: function(label) {
				//label.html("&nbsp;").addClass("listo");
			}
		});
		$("#formEditar").css("font-size", "14px");
		$( "#tabs" ).tabs({
			selected: 0,
			select: function(event, ui) {
				var id = ui.tab.attributes.href.value;
				$(id).load("<?php echo DIR_WEB; ?>/" + $(id).attr("url")+"?id="+<?php echo $usu->id ?>);
			},
			create: function( event, ui ) {
				$("#zonas").load("<?php echo DIR_WEB; ?>/" + $("#zonas").attr("url")+"?id="+<?php echo $usu->id ?>);
			}
		});

		
		
		hideMessage();
	});

</script>