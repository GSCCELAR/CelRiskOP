<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
?>
<table style='margin-top:4px;'>
	<tr>
		<td valign="top" style="width: 285px;">
			<div style='padding: 4px;'>
				<table style="font-size:12px; color:black; background-color:#EFEFEF; width: 280px; border:1px solid #dedede;">
					<tr style="color:white;" class="ui-widget-header">
						<td colspan="2" style='padding: 4px;'><i class='icon-search icon-white'></i><b> CONSULTAS</b></td>
					</tr>
					<tr>
						<td align="right" style="padding-top:12px; width:70px;"><b>Cliente:</b> &nbsp;</td>
						<td style="padding-top:12px; "><input id='busca_cliente' style='padding:1px; width:176px;' type=text name='busca_cliente'></td>
					</tr>
					<tr>
						<td align="right" style="width:80px;"><b>Usuario:</b> &nbsp;</td>
						<td>
							<select class='lista-buscador' onchange="buscarTexto();" id='busca_usuario' name='busca_usuario' style='width:180px;'>
								<option value=''>TODOS</option>
								<?php
									$u = new Usuario();
									$u->writeOptions(-1, array("id", "nombres"), array(), " where id in (select usuario_id from reporte group by usuario_id)");
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Tipo Rep:</b> &nbsp;</td>
						<td>
							<select class='lista-buscador' onchange="buscarTexto()" id='busca_tiporeporte' name="busca_tiporeporte" style='padding:1px; width:180px;'>
								<option value=''>Todos</option>
								<?php
									$lista = new Lista();
									$lista->writeOptions(-1, array("id", "nombre"), array("tipo_lista_codigo" => 'TIPO_REPORTE'));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Proceso:</b> &nbsp;</td>
						<td>
							<select class='lista-buscador' onchange="buscarTexto()" id='busca_proceso' name="busca_proceso" style='padding:1px; width:180px;'>
								<option value=''>Todos</option>
								<?php
									$lista = new Proceso();
									$lista->writeOptions(-1, array("id", "nombre"), array("estado" => 1));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Riesgo:</b> &nbsp;</td>
						<td>
							<select class='lista-buscador' onchange="buscarTexto()" id='busca_riesgo' name="busca_riesgo" style='padding:1px; width:180px;'>
								<option value=''>Todos</option>
								<?php
									$lista = new Riesgo();
									$lista->writeOptions(-1, array("id", "nombre"), array("estado" => 1));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><b>Control:</b> &nbsp;</td>
						<td>
							<select class='lista-buscador' onchange="buscarTexto()" id='busca_control' name="busca_control" style='padding:1px; width:180px;'>
								<option value=''>Todos</option>
								<?php
									$lista = new Control();
									$lista->writeOptions(-1, array("id", "nombre"), array("estado" => 1));
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">
							<input onchange="buscarTexto()" style="cursor:pointer;" type="checkbox" id="filtro_fecha"> &nbsp;
						</td>
						<td valign="bottom" style='padding-top:10px;'>
							<label for='filtro_fecha' style='font-size:12px;'><b>Filtrar Fecha</b></label>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<div id="reportrange" class="pull-center" style="background: transparent; cursor: pointer; padding: 5px 10px; right: auto; border: 1px solid rgba(255, 255, 255, 0.3); width: 180px;">
							    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
							    <span></span> <b class="caret"></b>
							</div>
						</td>
					</tr>
					<tr>
						<td align="right" colspan="2" style="padding:12px; padding-bottom:9px; border-bottom: 1px dashed black;">
							<button onclick="buscarTexto();" title="Buscar" class='btn btn-info'><i class='icon-search icon-white' /></i></button>
							<button onclick="limpiarBusqueda();" title="Limpiar formulario de búsqueda" class='btn btn-danger'><i class='icon-remove icon-white' /></i></button>
						</td>
					</tr>
				</table>
				<table width="100%">
					<tr>
						<td align=right style='padding:12px;' colspan=2><button onclick="descargarExcel();" title="Buscar" class='btn btn-success'><i class='icon-download-alt icon-white' /></i> Descargar consulta</button>
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
	var informe_ini = moment();
	var informe_fin = moment();

	$(document).ready(function() {
		$(".datetimepicker").remove();
		$("#lista").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php',
		    datatype : "json",
		    colNames : ['<b>ID</b>', '<b>Fecha</b>', '<b>Cliente</b>', '<b>Reporte</b>', '<b>Opc.</b>'],
		    colModel : [
				{ name:'id', index:'id', width:45, align: 'center' },
		        { name:'fecha_reporte', index:'fecha_reporte', width:80, align: 'center' },
		        { name:'cliente_nombre', index:'cliente_nombre', width:240, align: 'left' },
				{ name:'tiporeporte_nombre', index:'tiporeporte_nombre', width:327, align: 'left', search : false },
		        { name:'opciones', index:'opciones', width:80, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador'),
			rowNum : 10,
		    rowList : [3,10, 20, 30, 50],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'id',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "desc",
			height: "auto",
		    caption: "<i class='icon-list icon-white' /> <b>REPORTES</b>"
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista>div").removeClass("ui-corner-top");
		$("#gbox_lista").removeClass("ui-corner-all");
		$("#paginador").removeClass("ui-corner-bottom");
		
		hideMessage();
		
		var hBuscar = null;
		$("[type=text]").keyup(function (e) {
			if (e.keyCode == 13) {
				buscarTexto();
				return;
			}
		});

		$("select.lista-buscador").select2({
			placeholder: 'Todos...',
			allowClear: true
		});

		$('#reportrange').daterangepicker({
			startDate: informe_ini,
			endDate: informe_fin,
			ranges: {
				'Hoy': [ moment(), moment() ],
				'Ayer': [ moment().subtract(1, 'days'), moment().subtract(1, 'days') ],
				'Últimos 7 días': [ moment().subtract(6, 'days'), moment() ],
				'Últimos 30 días': [ moment().subtract(29, 'days'), moment() ],
				'Este mes': [ moment().startOf('month'), moment().endOf('month') ],
				'El mes pasado': [ moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month') ]
			}
		}, cb);
		
		cb(informe_ini, informe_fin);
		
		$(".caret").css("vertical-align", "middle");
	});
	
	function limpiarBusqueda() {
		$("[type=text],select").val("");
		$("#filtro_fecha").val("");
		if ($("#filtro_fecha").is(":checked"))
			$("#filtro_fecha").trigger("click");
		$("select.lista-buscador").val('').trigger("change");
		buscarTexto();
	}
	
	function buscarTexto() {
		cb(informe_ini, informe_fin);
	}

	function verF6(id) {
		showMessage();
		$("#ventana2").load("<?php echo DIR_WEB; ?>ver.php", { id : id });
	}

	function verF6PDF(id) {
		window.open('<?php echo DIR_WEB; ?>ver_pdf.php?id=' + id, '_blank');
		//document.location.href='<?php echo DIR_WEB; ?>ver_pdf.php?id=' + id;
	}

	function descargarExcel() {
		f1 = informe_ini.format('YYYY-MM-DD');
		f2 = informe_fin.format('YYYY-MM-DD');
		
		var busca_cliente = $("#busca_cliente").val();
		var busca_usuario = $("#busca_usuario").val();
		var busca_tiporeporte = $("#busca_tiporeporte").val();
		var busca_proceso = $("#busca_proceso").val();
		var busca_riesgo = $("#busca_riesgo").val();
		var busca_control = $("#busca_control").val();
		
		if (!$("#filtro_fecha").is(":checked"))
			f1 = f2 = "";

	    document.location.href = "<?php echo DIR_WEB; ?>lista_excel.php?noConvertir" 
					+ "&cliente=" + busca_cliente
					+ "&usuario=" + busca_usuario
					+ "&tiporeporte=" + busca_tiporeporte
					+ "&proceso=" + busca_proceso
					+ "&riesgo=" + busca_riesgo
					+ "&control=" + busca_control
					+ "&fecha1=" + f1
					+ "&fecha2=" + f2;
	}
	
	function cb(start, end) {
    	informe_ini = start;
    	informe_fin = end;
    	
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        
        f1 = start.format('YYYY-MM-DD');
		f2 = end.format('YYYY-MM-DD');
		
		var busca_cliente = $("#busca_cliente").val();
		var busca_usuario = $("#busca_usuario").val();
		var busca_tiporeporte = $("#busca_tiporeporte").val();
		var busca_proceso = $("#busca_proceso").val();
		var busca_riesgo = $("#busca_riesgo").val();
		var busca_control = $("#busca_control").val();
		
		if (!$("#filtro_fecha").is(":checked"))
			f1 = f2 = "";
		
        $("#lista").setGridParam({
			url: "<?php echo DIR_WEB; ?>lista.php?noConvertir"
				+ "&cliente=" + busca_cliente
				+ "&usuario=" + busca_usuario
				+ "&tiporeporte=" + busca_tiporeporte
				+ "&proceso=" + busca_proceso
				+ "&riesgo=" + busca_riesgo
				+ "&control=" + busca_control
				+ "&fecha1=" + f1
				+ "&fecha2=" + f2,
			page: 1
		}).trigger("reloadGrid");
    }
</script>