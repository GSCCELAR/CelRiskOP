<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	//define("MODO_DEBUG",true);
	
	$id = isset($_GET["id"]) ? intval($_GET["id"]) : die("Error al recibir el ID");
	$escenario = isset($_GET["escenario"]) ? $_GET["escenario"] : die("Error al recibir el escenario");

	if (isset($_GET["riesgo_id"])) {
		$reg = new Riesgo();
		$reg->load($_GET["riesgo_id"]) or die("Error al cargar la información");
		die ($reg->delete() ? "ok" : "Hay información relacionada con el riesgo. No se puede borrar la información"); 
	}
		
?>
<table>
	<tr>
		<td >
			<select class='lista-buscador_riesgo' id='riesgo' name='riesgo' style='padding:1px; width: 320px;'>
				<option value='-1'>Seleccione un riesgo...</option>
				<?php
					//$lista = new Riesgo();
					//$lista->writeOptions(-1, array("id","nombre"), array("estado" => 1));
				?>
			</select>
		</td>
		<td align="right">
			<input  type="button" title="Agregar" id="adicionarRiesgo" style="width:100px;height:30px;" class='btn btn-success icon-plus-sign icon-white' value="Agregar">
		</td>
	</tr>
</table>
<table id="lista_riesgo" class="scroll" cellpadding="0" cellspacing="0"  style="font-size:12px; border-collapse: none;"></table>
<div id="paginador_riesgo" class="scroll" style="text-align:left; font-size:10px;"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#ventana3").dialog("destroy");
		$("#ventana3").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Adicionar Riesgos",
			resizable: false,
			width: 450,
			height: 550,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
					left: (w.width() / 2) - (t.width() / 2)
				});
			},
			buttons: {
               	"Cerrar": function() {
                    $("#ventana3").html("");
					$("#ventana3").dialog("destroy");

                }
	        },
	      	close : function () {
	      		$("#ventana3").html("");
				$("#ventana3").dialog("destroy");

	      	}
		});

		$("#lista_riesgo").jqGrid({
		    url : '<?php echo DIR_WEB; ?>lista_riesgos.php?sede_escenario_id=<?php echo $id;?>',
		    datatype : "json",
		    colNames : ['<b>Nombre del riesgo</b>', '<b>Opc</b>'],
		    colModel : [
				{ name:'nombre', index:'nombre', width:350, align: 'left' },
		        { name:'opciones', index:'opciones', width:60, align: 'center', search :false }
		    ],
		    pager: jQuery('#paginador_riesgo'),
			rowNum : 8,
		    rowList : [8],
		    imgpath : "css/jqGrid/steel/images",
		    sortname : 'nombre',
		    viewrecords : true,
		    mtype : 'POST',
			pagerpos: 'center',
		    sortorder : "asc",
			height: "auto",
			caption: "<i class='icon-list icon-white' /> <b>Riesgos del escenario: <?php echo $escenario; ?></b>",	
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
		$("#gview_lista_riesgo>div").removeClass("ui-corner-top");
		$("#gbox_lista_riesgo").removeClass("ui-corner-all");
		$("#paginador_riesgo").removeClass("ui-corner-bottom");

		$("#riesgo").select2({
			placeholder: 'Seleccione...',
			tags: true,
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
				url: '<?php echo DIR_WEB; ?>load_riesgos.php',
				dataType: 'json',
				type: "GET",
				data: function (params) {
					var query = {
                		page: params.page || 1,
						search: params.term
					}
					return query;
				},
        	cache: true
			}
		});

		hideMessage();
	});

	var select_riesgo = $('#riesgo');
	select_riesgo.on("select2:select", function (e) { 
		var riesgo = e.currentTarget.lastChild.label;
		if (select_riesgo.find("option[value='" + riesgo + "']").length)
		{
			$.ajax({
			type: 'GET',
			url: '<?php echo DIR_WEB; ?>add_riesgo.php',
			data: {nombre : riesgo, descripcion : riesgo}
			}).done(function(data){
				if(data != "")
				{
					var json = $.parseJSON(data);
					var newOption = new Option(json["nombre"], json["id"], false, true);
					select_riesgo.append(newOption).trigger("change");
				}
				$("#lista_riesgo").trigger("reloadGrid");
			});
		}
		
	});

    $("#adicionarRiesgo").on("click",function(){
		addRiesgo();
		select_riesgo.val(null).trigger("change");
	});

	function addRiesgo() {
		var id = $("#riesgo").val();
		if(id != "")
		{
			showMessage();
			$.ajax({
				url: '<?php echo DIR_WEB ;?>add_sede_escenario_riesgo.php',
				data: {riesgo_id : id, sede_escenario_id: "<?php echo $id; ?>"}
			}).done(function(data){
				if (/^ok$/.test(data)) {
					$("#lista_riesgo").trigger("reloadGrid");
					$("#lista_escenario").trigger("reloadGrid");
				} 
				
			}).complete(function(data){
				hideMessage();
			});
		}
	}

	function eliminarRiesgo(id){
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
						$.get("<?php echo DIR_WEB; ?>nuevo_riesgo.php", { id : <?php echo $id; ?>, riesgo_id : id }, function(res) {
							hideMessage();
							if (!/^ok$/.test(res))
								mensaje("Error", "No fue posible borrar el ítem<br />Ocurrió un error inesperado. Vuelva a interntarlo", "error");
							$("#lista_riesgo").trigger("reloadGrid");
						});						
					}
			});
	}
</script>
