<?php
	define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../../conf/config.php");
	Aplicacion::validarAcceso(10);
	$pais_id = isset($_POST["pais_id"]) ? intval($_POST["pais_id"]) : -1;
	$default = isset($_POST["default"]) ? intval($_POST["default"]) : -1;
?>
<select name='departamento_id' id='pais_id' style="width:150px;">
	<option value=''>Seleccione...</option>
	<?php
		$p = new Departamento();
		$p->writeOptions($default, array("id", "nombre"), array("pais_id" => $pais_id));
	?>
</select>