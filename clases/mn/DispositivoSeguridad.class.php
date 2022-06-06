<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class DispositivoSeguridad extends Tabla {
		public $tabla = "dispositivo_seguridad";
		public $vista = "dispositivo_seguridad";

		public $mr = array();

		public function getMatrizRiesgoActual() {
			$this->mr = array();
			$r = BD::sql_query("select * FROM vmriesgo_op  WHERE dispositivo_seguridad_id=" . $this->id . " and fecha_fin IS NULL");
			while ($f = BD::obtenerRegistro($r))
				$this->mr[] = new MatrizRiesgo( $f,$f["id"]);
			return $this->mr;
		}
	}
?>