<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Usuario extends Tabla {
		public $tabla = "usuario";
		public $vista = "vusuarios";

		public function setClave($clave) {
			return $this->setCampo("clave", md5($clave));
		}
		
		public function getClave() {
			return $this->getCampo("clave");
		}
		
		public function getNombreCompleto($htmlentities = false) {
			return $this->getCampo("nombres", $htmlentities);
		}
		
		public function getNombreIniciales($htmlentities = false) {
			$nombre = explode(" ", $this->getCampo("nombre", $htmlentities));
			$apellidos = explode(" ", $this->getCampo("apellidos", $htmlentities));
			return $nombre[0] . " " . $apellidos[0];
		}

		public function loadByUsuario($identificacion) {
			$r = BD::consultar($this->vista, array("*"), array("usuario" => $identificacion));
			if ($f = BD::obtenerRegistro($r))
				return $this->loadThis($f);
			return false;
		}

		public function getIDZonas() {
			$result = array();
			$r = BD::consultar("usuario_zona", array("*"), array("usuario_id" => $this->id));
			while ($f = BD::obtenerRegistro($r))
				$result[] = $f["zona_id"];
			return $result;
		}
	}
?>