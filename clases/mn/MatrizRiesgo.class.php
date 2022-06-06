<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class MatrizRiesgo extends Tabla {
		public $tabla = "mriesgo_op";
		public $vista = "vmriesgo_op";

		public function update() {
			if (func_num_args() == 1) {
				$post = func_get_arg(0);
				if (!is_array($post)) return false;
				foreach($post as $campo => $valor)
					$this->setCampo($campo, $valor);
				return $this->update();
			}
			
			$nuevos_valores = $this->campos;
			
			foreach($nuevos_valores as $campo => $valor)
				if (!in_array($campo, $this->campos_tabla))
					unset($nuevos_valores[$campo]);
			if (isset($nuevos_valores[0]))
				unset($nuevos_valores[0]);

			if ($this->getCampo("fecha_inicio") == '') $this->setCampo("fecha_inicio", "_NULL");
			if ($this->getCampo("fecha_fin") == '') $this->setCampo("fecha_fin", "_NULL");
			if ($this->getCampo("fecha_seguimiento") == '') $this->setCampo("fecha_seguimiento", "_NULL");

			return parent::update();
		}

	}
?>