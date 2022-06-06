<?php
	class ApiCliente extends Rest {
		
		public $dbConn;
		
		public function __construct() {
			parent::__construct();
			$db = new DbConnect;
			$this->dbConn = $db->connect();
		}
		
		public function processApi() {
			$api = new ApiCliente;
			if (!method_exists($api, $this->serviceName))
				$this->throwError(API_DOES_NOT_EXIST, "'" . $this->serviceName . "' no está implementada.");
			$rMethod = new ReflectionMethod('ApiCliente', $this->serviceName);
			$rMethod->invoke($api);
		}
		
		/**
		 * Retorna los clientes activos según los permisos del usuario
		 */
		public function getAll() {
			try {
				$statement = $this->dbConn->prepare("SELECT * FROM vclientes c WHERE c.estado=1 AND c.id in (select s.cliente_id from sede s INNER JOIN usuario_sede us ON us.usuario_id=:usuario_id WHERE s.cliente_id=c.id)");
				$statement->bindParam(":usuario_id", $this->userID);
				$statement->execute();
				$data = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach($data as $fila_id => $fila) 
					foreach($fila as $campo => $valor)
						$data[$fila_id][$campo] = utf8_encode($valor);
				$this->returnResponse(SUCCESS_RESPONSE, $data);
			}
			catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}

		/**
		 * Retorna las sedes activas del cliente según el parámetro cliente_id y los permisos del usuario
		 */
		public function getSedes() {
			$cliente_id = $this->validateParameter('cliente_id', STRING);
			try {
				$statement = $this->dbConn->prepare("select * FROM sede s
					INNER JOIN usuario_sede us ON (us.usuario_id=:usuario_id AND us.sede_id=s.id)
					WHERE s.estado=1 AND s.cliente_id=:cliente_id");
				$statement->bindParam(":usuario_id", $this->userID);
				$statement->bindParam(":cliente_id", $cliente_id);
				$statement->execute();
				$data = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach($data as $fila_id => $fila) 
					foreach($fila as $campo => $valor)
						$data[$fila_id][$campo] = utf8_encode($valor);
				$this->returnResponse(SUCCESS_RESPONSE, $data);
			}
			catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}
	}
?>