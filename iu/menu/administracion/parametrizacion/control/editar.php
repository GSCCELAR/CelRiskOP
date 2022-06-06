<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
	
	$id = isset($_POST["id"]) ? intval($_POST["id"]) : die("Error al recibir el ID");
	
	$reg = new Control();
	$reg->load($id) or die("Error al cargar el ítem");
	
	if (isset($_POST["nombre"]) && isset($_POST["descripcion"]) && isset($_POST["costo_anual"]) && isset($_POST["porcentaje_eficiencia"]))
	{
		$_POST["porcentaje_eficiencia"] = (float)$_POST["porcentaje_eficiencia"] / 100;
		die ($reg->update($_POST) ? "ok" : "err");
	}
		
?>
<form method="post" name="formEditar" id="formEditar" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<input type="hidden" name='id' value='<?php echo $reg->id; ?>'>
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=120 style='width:260px;' name='nombre' value="<?php echo $reg->getCampo("nombre", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><input type=text  style='width:260px;' name='descripcion' value="<?php echo $reg->getCampo("descripcion", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Costo Anual: </b></td>
			<td><input type=text  style='width:260px;' name='costo_anual' value="<?php echo $reg->getCampo("costo_anual", true); ?>"></td>
		</tr>
		<tr>
			<td align=right><b>Porcentaje Eficiencia: </b></td>
			<td><input type="range" min="0" max="100" style='width:260px;' name='porcentaje_eficiencia' value="<?php echo ((float)$reg->getCampo("porcentaje_eficiencia", true) * 100); ?>"  data-rangeSlider><br><output></output></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		var selector = '[data-rangeSlider]',
      elements = document.querySelectorAll(selector);

  // Example functionality to demonstrate a value feedback
  function valueOutput(element) {
      var value = element.value,
        output = element.parentNode.getElementsByTagName('output')[0];
      output.innerHTML = value +"%";
    }

    for (var i = elements.length - 1; i >= 0; i--) {
      valueOutput(elements[i]);
    }

    Array.prototype.slice.call(document.querySelectorAll('input[type="range"]')).forEach(function (el) {
      el.addEventListener('input', function (e) {
        valueOutput(e.target);
      }, false);
	});
		$("#ventana2").dialog("destroy");
		$("#ventana2").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black" 
			},
			title: "<i class='icon-white icon-edit' /> &nbsp; Editar",
			resizable: false,
			width: 440,
			open : function() {
				var t = $(this).parent(), w = $(document);
				t.offset({
					top: 60,
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
						{
							$("#formEditar").submit();
						}
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
		
		$("#formEditar").validate({
			rules: {
				nombre : "required",
				descripcion: "required",
				costo_anual: "required",
				porcentaje_eficiencia: "required"
			},
			messages: {
				nombre : "",
				descripcion: "",
				costo_anual: "",
				porcentaje_eficiencia: ""
			},
			submitHandler: function(e) {
				showMessage();
				$('#formEditar').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible actualizar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});
		hideMessage();
	});
</script>