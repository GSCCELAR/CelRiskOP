<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Sede extends Tabla {
		public $tabla = "sede";
		public $vista = "vsedes";

		public function save($save_id = false) {
			$return = parent::save($save_id);
			file_put_contents(DIR_APP . "api/version.txt", time());
			return $return;
		}

		/*public function update() {
			$return = parent::update();
			file_put_contents(DIR_APP . "api/version.txt", time());
			return $return;
		}*/

		public function delete() {
			$return = parent::delete();
			file_put_contents(DIR_APP . "api/version.txt", time());
			return $return;
		}
	}
?>