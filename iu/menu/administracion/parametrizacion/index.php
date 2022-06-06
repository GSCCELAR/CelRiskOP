<?php
	define("iC", true);
	require_once (dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);
?>
<table style='margin-top:4px;'>
	<tr>
		<td valign="top" style="width: 250px;">
			<div style='padding: 4px;'>
				<table style="font-size:12px; color:black; background-color:#EFEFEF; width: 240px; border:1px solid #dedede;">
					<tr style="color:white;" class="ui-widget-header">
						<td colspan="2" style='padding: 4px;'><i class='icon-list icon-white'></i><b> MAESTROS</b></td>
					</tr>
					<tr>
						<td colspan="2" style='padding: 8px;'>
						<?php if(Aplicacion::getPerfilID() != 9){ ?>
							<div url="tipos_lista/index.php" class='btn-left'>Listas</div>
							<br />
							<div url="paises/index.php" class='btn-left'>Países</div>
							<div url="departamentos/index.php" class='btn-left'>Departamentos</div>
							<div url="municipios/index.php" class='btn-left'>Municipios</div>
							<br />
							<div url="escenario/index.php" class='btn-left'>Escenarios</div>
							<div url="riesgo/index.php" class='btn-left'>Riesgos</div>
							<div url="proceso/index.php" class='btn-left'>Procesos</div>
						<?php } 
							//if(Aplicacion::getPerfilID() == 9 || Aplicacion::getPerfilID() != 9){ ?>
							<div url="control/index.php" class='btn-left'>Controles</div>
						<?php //}
							if(Aplicacion::getPerfilID() != 9){	?>
							<div url="contacto/index.php" class='btn-left'>Contactos</div>
						<?php }?>
							<br />
								<div url="peligro/index.php" class='btn-left'>Factor Riesgo</div>
								<div url="nivel_riesgo/index.php" class='btn-left'>Niveles Riesgo</div>
								<div url="probabilidad/index.php" class='btn-left'>Probabilidades</div>
								<div url="severidad/index.php" class='btn-left'>Severidades</div>
								<div url="clasificacion_riesgo/index.php" class='btn-left'>Clasificaciones Riesgo</div>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td valign="top" style="padding:4px;">
			<div id='parametro_contenido'></div>
		</td>
	</tr>
</table>
<script type="text/javascript" charset="ISO-8859-1">
	$(document).ready(function() {
		$(".btn-left").click(function() {
			$(".btn-left").removeClass("btn-select");
			$(this).addClass("btn-select");
			showMessage();
			$("#parametro_contenido").load("<?php echo DIR_WEB; ?>/" + $(this).attr("url"));
		});
		$(".btn-left").eq(0).click();
	});
</script>