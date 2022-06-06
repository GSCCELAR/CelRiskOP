<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Cliente extends Tabla {
		public $tabla = "cliente";
		public $vista = "vclientes";

		public function getSedes() {
			$r = BD::consultar("sede", array("*"), array("cliente_id" => $this->id));
			$return = array();
			while ($f = BD::obtenerRegistro($r))
				$return[] = new Sede($f, $f["id"]);
			return $return;
		}
	}
?>