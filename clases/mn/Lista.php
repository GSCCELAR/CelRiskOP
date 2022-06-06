<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Lista extends Tabla {
		public $tabla = "lista";
		public $vista = "vlistas";

		public static function getSectores() {
			$r = BD::consultar("vlistas", array("*"), array("tipo_lista_codigo" => "SECTOR"));
			$return = array();
			while ($f = BD::obtenerRegistro($r))
				$return[] = new Lista($f, $f["id"]);
			return $return;
		}
	}
?>