<?php

	class Rest {
		
		protected $request;
		protected $serviceName;
		protected $param;
		protected $userID = null;
		
		public function __construct() {
			if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST')
				$this->throwError(REQUEST_METHOD_NOT_VALID, 'Método de petición no válido');
			
			$handler = fopen('php://input', 'r');
			$this->request = stream_get_contents($handler);
			$this->validateRequest($this->request);
			
			if (!in_array($this->serviceName, array('generateToken', 'restorePassword')))
				$this->validateToken();
		}
		
		public function validateRequest($request) {
			if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json')
				$this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Content-type no válido');
			$data = json_decode($request, true);
			$this->serviceName = isset($data["name"]) ? $data["name"] : $this->throwError(API_NAME_REQUIRED, "Api name requerida");
			$this->param = isset($data["param"]) && is_array($data["param"]) ? $data["param"] : $this->throwError(API_PARAM_REQUIRED, "Api param is required (array)"); 
		}
		
		public function validateParameter($fieldName, $dataType, $required = true) {
			if (!isset($this->param[$fieldName]))
				$this->throwError(VALIDATE_PARAMETER_REQUIRED, "El parámetro $fieldName es nulo");
			
			$value = $this->param[$fieldName];
			if ($required == true && empty($value) == true)
				$this->throwError(VALIDATE_PARAMETER_REQUIRED, "El parámetro $fieldName está vacío");
			switch($dataType) {
				case BOOLEAN:
					if (!is_bool($value))
						$this->throwError(VALIDATE_PARAMETER_DATATYPE, "El tipo de datos no es válido para el campo '$fieldName'. Debe ser boolean.");
					break;
					
				case STRING:
					if (!is_string($value))
						$this->throwError(VALIDATE_PARAMETER_DATATYPE, "El tipo de datos no es válido para el campo '$fieldName'. Debe ser string.");
					break;
					
				case INTEGER:
					if (!is_numeric($value))
						$this->throwError(VALIDATE_PARAMETER_DATATYPE, "El tipo de datos no es válido para el campo '$fieldName'. Debe ser integer.");
					break;
				default:
					break;
			}
			
			return $value;
		}
		
		public function throwError($code, $message) {
			header("Content-type: application/json");
			die (json_encode(array('error' => array('status' => $code, 'message' => utf8_encode($message)))));
		}
		
		public function returnResponse($code, $data) {
			header("Content-type: application/json");
			if (is_array($data)) {
				foreach($data as $i => $v)
					if (!is_array($v))
						$data[$i] = utf8_encode($v);
			}
			else
				$data = utf8_encode($data);
			$result = array(
				'response' => array(
					'status' => $code,
					'message' => $data
				)
			);
			die (json_encode($result));
		}
		
		public function getAuthorizationHeader() {
			$headers = isset($_SERVER['Authorization']) ? trim($_SERVER['Authorization']) : null;
			if ($headers == null)
				$headers = isset($_SERVER['HTTP_AUTHORIZATION']) ? trim($_SERVER['HTTP_AUTHORIZATION']) : null;
			if ($headers == null && function_exists('apache_request_headers')) {
				$requestHeaders = apache_request_headers();
				$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
				if (isset($requestHeaders['Authorization']))
					$headers = $requestHeaders['Authorization'];
			}
			return $headers;
		}
		
		public function getBearerToken() {
			$headers = $this->getAuthorizationHeader();
			if (!empty($headers))
				if (preg_match('/Bearer\s(\S+)/', $headers, $matches))
					return $matches[1];
			$this->throwError(AUTHORIZATION_HEADER_NOT_FOUND, 'No se encontró el token de autenticación');
		}
		
		public function validateToken() {
			$this->userID = -1;
			try {
				$token = $this->getBearerToken();
				$payload = JWT::decode($token, SECRET_KEY);
				if ($payload->exp >= time())
					$this->userID = intval($payload->userID);
				else
					$this->throwError(JWT_SESSION_EXPIRED, 'El tiempo de la sesión ha expirado');
			}
			catch (Exception $e) {
				$this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
			}
			if ($this->userID == -1) {
				$this->throwError(ACCESS_TOKEN_ERRORS, "Error al validar la sesión del usuario");
			}
		}
		
	}
?>