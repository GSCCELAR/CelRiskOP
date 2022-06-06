<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	if (isset($_POST["delete"])) {
		$reg = new MatrizRiesgo();
		$reg->load($_POST["delete"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay datos relacionados con este item"); 
	}
	
?>

<table style='margin-top:4px;'>
	<tr>
		<td valign="top" style="width: 250px;">
			<div style='padding: 4px;'>
				<table style="font-size:12px; color:black; background-color:#EFEFEF; width: 240px; border:1px solid #dedede;">
					<tr style="color:white;" class="ui-widget-header">
						<td colspan="2" style='padding: 4px;'><i class='icon-search icon-white'></i><b> CONSULTAS</b></td>
					</tr>
					<tr>
						<td align="right" style="padding-top:12px;"><b>Sector:</b> &nbsp;</td>
						<td  style="padding-top:12px; ">
							<select style='padding:1px; height:25px; width:150px;' name='busca_sector' id='busca_sector'>
								<option value=''>Todos...</option>
								<?php
									$lista = new Lista();
									$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => "SECTOR"));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" colspan="2" style="padding:12px; padding-bottom:9px; border-bottom: 1px dashed black;">
							<button onclick="buscarTexto();" title="Buscar" class='btn btn-info'><i class='icon-search icon-white' /></i></button>
							<button onclick="limpiarBusqueda();" title="Limpiar formulario de búsqueda" class='btn btn-danger'><i class='icon-remove icon-white' /></i></button>
						</td>
					</tr>
					<tr>
						<td colspan=2 align="right" style="padding:12px; padding-top:9px;">
							<button title="Registrar matriz base" onclick="nuevo()" style="width:90px;" class='btn btn-success'><i class='icon-plus-sign icon-white' /></i> Nuevo</button>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td valign="top" style="padding:4px;">
			<table id="lista" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
			<div id="paginador" class="scroll" style="text-align:left; font-size:10px;"></div>
		</td>
	</tr>
</table>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : ['<b>Sector</b>','<b>Factor Riesgo</b>', '<b>Probabilidad</b>','<b>Severidad</b>', '<b>Nivel Riesgo</b>', '<b>Opc.</b>'],
		    colModel : [
				{ name:'sector', index:'sector', width:120, align: 'left' },
				{ name:'peligros', index:'peligros', width:285, align: 'left' },
				{ name:'probabilidad', index:'probabilidad', width:100, align: 'center' },
				{ name:'severidad', index:'severidad', width:100, align: 'center' },
				{ name:'nivel_riesgo', index:'nivel_riesgo', width:100, align: 'center' },
		        { name:'opciones', index:'opciones', width:100, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador'),
			rowNum : 20,
		    rowList : [10, 20, 30, 50, 100],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'peligros',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "desc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>MATRIZ BASE</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador").removeClass("ui-corner-bottom");
		
		$("#busca_sector").select2({
			placeholder: 'Todos...',
			allowClear: true
		});
	
		
		hideMessage();
		
		$("#busca_sector").change(function() {
			buscarTexto();
		});
		
		var hBuscar = null;
		
		$("#formEditarMatrizBase").find("input,select").uniform();
	});
	
	
	function editar(id) {
		showMessage();
		var params = {
			id : id
		}
		$("#ventana2").load("<?php echo DIR_WEB; ?>editar.php", params );
	}
	
	function nuevo() {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>nuevo.php");
	}
	
	function limpiarBusqueda() {
		$("#busca_sector").val("").trigger("change");
		buscarTexto();
	}
	
	function eliminar(id) {
		Swal.fire({
				title : '',
				text : '¿Confirma que desea borrar la matriz de riesgo?',
				type : 'question',
				showCancelButton : true,
				confirmButtonText: 'Aceptar',
				cancelButtonText: 'Cancelar'
			}).then(function (res) {
				if (res.value)
				{
					showMessage();
					$.post("<?php echo DIR_WEB; ?>index.php", { delete : id }, function(res) {
						hideMessage();
						if (!/^ok$/.test(res))
							mensaje("Error", "No ha sido posible borrar el registro de matriz de riesgo debido a que puede existir información relacionada con la matriz", "error");
						$("#lista").trigger("reloadGrid");
					});
				}
			});
	}

	function buscarTexto() {
		var busca_sector = $("#busca_sector").val();
		hBuscar = null;
		$("#lista").setGridParam({
			url: "<?php echo DIR_WEB; ?>lista.php?1=1"
				+ "&sector=" + busca_sector,
			page: 1
		}).trigger("reloadGrid");
	}
</script>