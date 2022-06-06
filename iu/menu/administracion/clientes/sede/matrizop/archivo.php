<?php
		define("iC", true);
		require_once (dirname(__FILE__) . "/../../../../../../conf/config.php");
		Aplicacion::validarAcceso(9,10);
		BD::changeInstancia("mysql");
		
		$sede_id = isset($_POST["sede_id"]) ? intval($_POST["sede_id"]) : -1; 
		$sede = new Sede();
		if(!$sede->load($sede_id)) die("Error al cargar la sede");
?>

<div class='upload_file_container' id='div_requeridos'>
	<input onchange="loadArchivo(this.id);" id="file_upload_archivo" name="file_upload_archivo" type="file"> (Utiliza el botón <b>Examinar...</b> para cargar el archivo)
</div>
<div id="load_file"></div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#load_file").html("");
		$("#ventana5").dialog("destroy");
		$("#ventana5").dialog({
			modal: true,
		    overlay: {
		        opacity: 0.4,
		        background: "black"
			},
			title: "<i class='icon-white icon-plus-sign' /> &nbsp; Archivo",
			resizable: false,
			width: 700,
			position : 'top',
			buttons: {
              	"Cerrar": function() {
                    $("#ventana5").html("");
                    $("#ventana5").dialog("destroy");
                }
	        },
	      	close : function () {
	      		$("#ventana5").html("");
				$("#ventana5").dialog("destroy");
	      	}
		});
		hideMessage();
	});


	function loadArchivo(id) {
			showMessage();
			$.ajaxFileUpload({
				url:'<?php echo DIR_WEB; ?>guardar_archivo.php',
				data:{sede_id:"<?php echo $sede_id; ?>"},
				type:"POST",
				secureuri:false,
				fileElementId : id,
				dataType: 'json',
				success: function (data, status) {
					if(typeof(data.error) != 'undefined') {
						if(data.error != '') {
							jAlert(data.error, "Error");

						}else{
							$("#load_file").html("").append("<embed src=\"api/cursos/"+data.archivo+"\" type=\"application/pdf\" width=\"100%\" height=\"700px\">");
							$("#ventana4 #archivo").val("api/cursos/"+data.archivo);
							$("#btnSeleccionarArchivo").html("api/cursos/"+data.archivo);
							$("#btnQuitarArchivo").show();
						}
					}
						
					hideMessage();
					
				},
				error: function (data, status, e) {
					console.log(data);
					hideMessage();
					jAlert(e, "Error");
				}
			});
		
	}
	
</script>