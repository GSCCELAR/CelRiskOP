<?php
	class ApiLista extends Rest {
		
		public $dbConn;
		
		public function __construct() {
			parent::__construct();
			$db = new DbConnect;
			$this->dbConn = $db->connect();
		}
		
		public function processApi() {
			$api = new ApiLista;
			if (!method_exists($api, $this->serviceName))
				$this->throwError(API_DOES_NOT_EXIST, "'" . $this->serviceName . "' no está implementada.");
			$rMethod = new ReflectionMethod('ApiLista', $this->serviceName);
			$rMethod->invoke($api);
		}
		
		public function getListasCant() {
			if ($this->userID > -1) {
				try {
					$statement = $this->dbConn->prepare("select 
						(SELECT count(1) from cliente where estado=1) tlista_cliente, 
						(SELECT count(1) from sede where estado=1) tlista_sede,
						(SELECT count(1) from vsede_escenarios where escenario_estado=1) tlista_sede_escenario,
						(SELECT count(1) from vsede_escenarios_riesgos where riesgo_estado=1) tlista_sede_escenario_riesgo,
						(SELECT count(1) from proceso where estado=1) tlista_proceso,
						(SELECT count(1) from control where estado=1) tlista_control
					");
					$statement->execute();
					$listas = $statement->fetch(PDO::FETCH_ASSOC);
					if (!is_array($listas))
						$this->returnResponse(DATA_NOT_FOUND, "Error al consultar las listas");

					$this->returnResponse(SUCCESS_RESPONSE, $listas);
				}
				catch (Exception $e) {
					$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
				}
			}
		}

		public function getListas() {
			if ($this->userID > -1) {
				try {
					//CLIENTES
					$statement = $this->dbConn->prepare("select id, razon_social, tipodocumento_nombre, identificacion, digito_verificacion from vclientes where estado=1");
					$statement->execute();
					$lista_1 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["razon_social"] = utf8_encode($f["razon_social"]);
						$lista_1[] = $f;
					}

					//SEDES
					$statement = $this->dbConn->prepare("select id, cliente_id, nombre, direccion, telefono from sede where estado=1");
					$statement->execute();
					$lista_2 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["nombre"] = utf8_encode($f["nombre"]);
						$f["direccion"] = utf8_encode($f["direccion"]);
						$f["telefono"] = utf8_encode($f["telefono"]);
						$lista_2[] = $f;
					}

					//ESCENARIOS DE LAS SEDES		(escenarios por sede)
					$statement = $this->dbConn->prepare("select id, sede_id, escenario_id, escenario_nombre from vsede_escenarios where escenario_estado=1");
					$statement->execute();
					$lista_3 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["escenario_nombre"] = utf8_encode($f["escenario_nombre"]);
						$lista_3[] = $f;
					}

					//RIESGOS POR ESCENARIO Y SEDE	(riesgos por escenarios)
					$statement = $this->dbConn->prepare("select id, sede_escenario_id, riesgo_id, riesgo_nombre from vsede_escenarios_riesgos where riesgo_estado=1");
					$statement->execute();
					$lista_4 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["riesgo_nombre"] = utf8_encode($f["riesgo_nombre"]);
						$lista_4[] = $f;
					}

					//PROCESOS
					$statement = $this->dbConn->prepare("select id, nombre from proceso where estado=1");
					$statement->execute();
					$lista_5 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["nombre"] = utf8_encode($f["nombre"]);
						$lista_5[] = $f;
					}
					
					//CONTROLES
					$statement = $this->dbConn->prepare("select id, nombre, descripcion from control where estado=1");
					$statement->execute();
					$lista_6 = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$f["nombre"] = utf8_encode($f["nombre"]);
						$lista_6[] = $f;
					}


					$listas = array(
						'cliente' => $lista_1,
						'sede' => $lista_2,
						'sede_escenario' => $lista_3,
						'sede_escenario_riesgo' => $lista_4,
						'proceso' => $lista_5,
						'control' => $lista_6
					);

					$this->returnResponse(SUCCESS_RESPONSE, $listas);
				}
				catch (Exception $e) {
					$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
				}
			}
		}
				
	}
?>