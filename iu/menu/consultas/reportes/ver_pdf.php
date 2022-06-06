<?php
    define("iC", true);
	require_once(dirname(__FILE__) . "/../../../../conf/config.php");
	Aplicacion::validarAcceso(9,10);

    $id = isset($_GET["id"]) ? intval($_GET["id"]) : die("Error al obtener el ID del reporte");
    
    $form = new Reporte();
	if (!$form->load($id)) die ("error al cargar la información del reporte");
    
    $f = new FormularioReportePDF($form);
    $f->generarPDF();

?>