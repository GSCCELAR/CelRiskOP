<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);

	
	if (isset($_POST["nombre"]) && isset($_POST["descripcion"]) && isset($_POST["costo_anual"]) && isset($_POST["porcentaje_eficiencia"])){
		$_POST["porcentaje_eficiencia"] = (float)$_POST["porcentaje_eficiencia"] / 100;
		$reg = new Control($_POST);
		die ($reg->save() ? "ok" : "err");
	}
?>
<form method="post" name="formNuevo" id="formNuevo" action="<?php echo DIR_WEB . basename(__FILE__); ?>">
	<table cellpadding=3 border=0 width="100%">
		<tr>
			<td align=right><b>Nombre: </b></td>
			<td><input type=text maxlength=120 style='width:260px;' name='nombre' value=""></td>
		</tr>
		<tr>
			<td align=right><b>Descripción: </b></td>
			<td><input type=text  style='width:260px;' name='descripcion' value=""></td>
		</tr>
		<tr>
			<td align=right><b>Costo Anual: </b></td>
			<td><input type=text  style='width:260px;' name='costo_anual' value=""></td>
		</tr>
		<tr>
			<td align=right valign="top"><b>Porcentaje Eficiencia: </b></td>
			<td><input type="range" min="0" max="100" style='width:260px;' id="porcentaje_eficiencia" name='porcentaje_eficiencia'  data-rangeSlider><br><output></output></td>
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
			title: "<i class='icon-white icon-plus' /> &nbsp; Nuevo",
			resizable: false,
			width: 480,
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
							$("#formNuevo").submit();
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


		
		var validator = $("#formNuevo").validate({
			rules: {
				nombre : "required",
				descripcion: "required",
				costo_anual: { 
					required: true,
					number: true
				},
				porcentaje_eficiencia: {
					required: true
				}
			},
			messages: {
				nombre : "",
				descripcion: "",
				costo_anual: "",
				porcentaje_eficiencia: "Solo se acepta un número entero con dos decimales"
			},
			submitHandler: function(e) {
				showMessage();
				$('#formNuevo').ajaxSubmit({ success: function(resp) {
					hideMessage();
					if (/^ok$/.test(resp)) {
						$("#ventana2").dialog("destroy");
						$("#lista").trigger("reloadGrid");
					}
					else {
						mensaje("Error", "No fue posible registrar el ítem<br />" + resp, "error");
					}
				}});
				return false;
			}
		});

		$('#porcentaje_eficiencia').rangeslider({
			polyfill: true,
			onSlideEnd: function(position, value) {
				console.log(value);
			}

		});
	
		hideMessage();
	});
</script>