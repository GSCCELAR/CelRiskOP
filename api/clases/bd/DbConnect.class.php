<?php
	class DbConnect {
		
		public function __construct() {
			
		}
		
		public function connect() {
			global $_CONFIG;
			try {
				
				$conn = new PDO('mysql:host=' . $_CONFIG["BD"]["host"] . ";dbname=" . $_CONFIG["BD"]["base"], $_CONFIG["BD"]["user"], $_CONFIG["BD"]["pass"]);
				$conn->exec("set names latin1");
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $conn;
			}
			catch (Exception $e) {
				$this->throwError(DB_ERROR, $e->getMessage());
			}
		}
		
		public function throwError($code, $message) {
			header("Content-type: application/json");
			die (json_encode(array('error' => array('status' => $code, 'message' => utf8_encode($message)))));
		}
	}
?>