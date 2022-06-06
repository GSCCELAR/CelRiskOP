<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	//define("MODO_DEBUG",true);
	BD::changeInstancia("mysql");

	$id = isset($_GET["id"]) ? intval($_GET["id"]) : die("Error al obtener el ID de la sede");

	if (isset($_GET["sede_escenario"])) {
		$reg = new SedeEscenario();
		$reg->load($_GET["sede_escenario"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay información relacionada con este cliente. No se puede borrar la información"); 
	}

?>
<input type="hidden" id="escenario_id" name="escenario_id">
<table>
	<tr>
		<td >
		        <select class='lista-buscador' id='escenario' name='escenario' style='padding:1px; width: 455px;'>
					<option value='-1'>Seleccione un escenario...</option>
					<?php
						//$lista = new Escenario();
						//$lista->writeOptions(-1, array("id","nombre"), array("estado" => 1));
					?>
				</select>
			<!--<div class="form-group has-search">
				<span class="fa fa-search "></span>
				<input id='busca_escenario'  type=text name='busca_escenario'  placeholder="Búsqueda" style="width:350px;">
				
			</div>-->
		</td>
		<td align="right">
			<input  type="button" title="Agregar" id="adicionarEscenario" style="width:100px;height:30px;" class='btn btn-success icon-plus-sign icon-white' value="Agregar">
		</td>
	</tr>
</table>
<table id="lista_escenario" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador_escenario" class="scroll" style="text-align:left; font-size:10px;"></div>
<!--<div style="border:1px;">
	<table id="lista_riesgos" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;margin-top:10px;"></table>
</div>-->
<script type="text/javascript" charset="ISO-8859-1">
	var subgrid_table_id, pager_id;
	$(document).ready(function() {
		$("#lista_escenario").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista.php?id='+<?php echo $id; ?>,
		    datatype : "json",
		    colNames : ['<b>Nombre del escenario</b>', '<b>Riesgos</b>', '<b>Opc</b>'],
		    colModel : [
				{ name:'nombre', index:'nombre', width:340, align: 'left' },
		        { name:'cantidad_riesgos', index:'cantidad_riesgos', width:120, align: 'center' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_escenario'),
			rowNum : 8,
		    rowList : [8],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'nombre',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
			caption: "<i class='icon-list icon-white' /> <b>ESCENARIOS</b>",
			subGrid: true,
			subGridRowExpanded: function(subgrid_id, row_id) {
				
				subgrid_table_id = subgrid_id + "_t"; 
				pager_id = "p_" + subgrid_table_id;
				$("#"+subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
				$("#"+subgrid_table_id).jqGrid({
					url:"<?php echo DIR_WEB; ?>lista_riesgos.php?sede_escenario_id=" + row_id, 
					datatype: "json",
					colNames: ['Nombre del riesgo','Opc'], 
					colModel: [
						{ name:"nombre",index:"nombre",width:280 },  
						{ name:'opciones', index:'opciones', width:60, align: 'center', search :false }
					],
					rowNum : 8, 
					mtype: "POST",
					pager: pager_id, 
					imgpath: "css/jqGrid/steel/images", 
					sortname: 'nombre', 
					sortorder: "asc", 
					height: 'auto',
					viewrecords : true,
					rowList : [8],
					caption: "",
				});
			},	
			loadComplete : function (res) {
				var sede_escenario_id = $(this).jqGrid("getGridParam", "sede_escenario_id");
				if(sede_escenario_id != undefined)
					$(this).setSelection(sede_escenario_id);
				else if(res.rows.length > 0)
					$(this).setSelection(res.rows[0]["id"]);
			}		
		});
		
		$(".ui-pg-input").css("width", "20px");
		$(".ui-pg-selbox").css("width", "50px");
		$("#gview_lista_escenario>div").removeClass("ui-corner-top");
		$("#gbox_lista_escenario").removeClass("ui-corner-all");
		$("#paginador_escenario").removeClass("ui-corner-bottom");

		$("#escenario").select2({
			placeholder: 'Seleccione un escenario...',
			tags: true,
			allowClear: true,
			createTag: function (params) {
				console.log(params);
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
				url: '<?php echo DIR_WEB; ?>load_escenarios.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
                		page: params.page || 1
					}
					return query;
				},
        	cache: true
			}
			
		});
		hideMessage();
	});
    var select = $('#escenario');
	select.on("select2:select", function (e) { 
		var escenario = e.currentTarget.lastChild.label;
		if (select.find("option[value='" + escenario + "']").length){
			$.ajax({
				type: 'GET',
				url: '<?php echo DIR_WEB; ?>add_escenario.php',
				data: {nombre : escenario}
			}).done(function(data){
				if(data != "")
				{
					var json = $.parseJSON(data);
					var newOption = new Option(json["nombre"], json["id"], false, true);
					select.append(newOption).trigger("change");
				}		
			});
		}
		
	});

    $("#adicionarEscenario").on("click",function(){
		addEscenario();
		select.val(null).trigger("change");
	});

	function addEscenario() {
		var id = $("#escenario").val();
		if(id != "")
		{
			showMessage();
			$.ajax({
				url: '<?php echo DIR_WEB ;?>set_escenarios.php',
				data: {escenario_id : id, sede_id: "<?php echo $id; ?>"}
			}).done(function(data){
				if (/^ok$/.test(data)) {
					select.select2('val', '');
					$("#lista_escenario").trigger("reloadGrid");
				} else {
					mensaje("Error", "No fue posible adicionar el escenario<br />Es posible que el escenario ya se encuentre asignado a la sede", "error");
				}
				
			}).complete(function(data){
				hideMessage();
			});
		}
	}

	function eliminarEscenario(id) {
		Swal.fire({
					title : '',
					text : '¿Confirma que desea borrar este registro?',
					type : 'question',
					showCancelButton : true,
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then(function (res) {
					if (res.value)
					{
						showMessage();
						$.get("<?php echo DIR_WEB; ?>index.php", { sede_escenario : id, id: "<?php echo $id;?>" }, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible borrar el ítem<br />Tiene riesgos asignados", "error");
							$("#lista_escenario").trigger("reloadGrid");
						});						
					}
			});
	}

	function eliminarSedeEscenarioRiesgo(id){
		Swal.fire({
					title : '',
					text : '¿Desea quitar el riesgo?',
					type : 'question',
					showCancelButton : true,
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar'
				}).then(function (res) {
					if (res.value)
					{
						showMessage();
						$.get("<?php echo DIR_WEB; ?>delete_riesgos.php", { id : id }, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible borrar el ítem<br />Ocurrió un error inesperado. Vuelva a intentarlo", "error");
							$("#lista_escenario").trigger("reloadGrid");
							if ($("#lista_riesgo").length > 0)
								$("#lista_riesgo").trigger("reloadGrid");
						});						
					}
			});
	}

	function adicionarRiesgo(id, escenario) {
		showMessage();
		$("#ventana3").load("<?php echo DIR_WEB; ?>nuevo_riesgo.php?id="+id+"&escenario="+encodeURI(escenario));
	}
</script>