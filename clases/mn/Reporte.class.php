<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Reporte extends Tabla {
		public $tabla = "reporte";
		public $vista = "vreportes";

		public function getFecha($fecha, $formato = "d/F/Y g:ia") {
			if (preg_match("/^0000-00-00 00:00:00$/", $this->getCampo($fecha)))
				return " - ";
			return Fecha::getFechaCorta($this->getCampo($fecha), $formato);
		}

		public function getSize() {
			return Config::format_size(@Config::foldersize(RUTA_SOPORTES . $this->id . "/"));
		}
	}
?>