<?php
	require DIR_APP . 'clases/mn/PHPMailer.class.php';
	require DIR_APP . 'clases/mn/SMTP.class.php';

	class ApiUsuario extends Rest {
		
		public $dbConn;
		
		public function __construct() {
			parent::__construct();
			$db = new DbConnect;
			$this->dbConn = $db->connect();
		}
		
		public function processApi() {
			$api = new ApiUsuario;
			if (!method_exists($api, $this->serviceName))
				$this->throwError(API_DOES_NOT_EXIST, "'" . $this->serviceName . "' no está implementada.");
			$rMethod = new ReflectionMethod('ApiUsuario', $this->serviceName);
			$rMethod->invoke($api);
		}
		
		public function update() {
			$usuario = isset($this->param["user"]) ? $this->param["user"] : $this->throwError(JWT_PROCESSING_ERROR, "Los datos recibidos no son correctos");
			if (isset($usuario["clave"]) && $usuario["clave"] == "") unset($usuario["clave"]);
			if (isset($usuario["clave"]) && $usuario["clave"] != "") $usuario["clave"] = md5($usuario["clave"]);
			try {
				$actualizaciones = $values = array();
				$query = "UPDATE usuario SET ";
				$campos_editables = array("nombre", "apellidos", "identificacion", "clave", "correo");
				foreach($campos_editables as $i) {
					if (isset($usuario[$i])) {
						$actualizaciones[] = "$i = :$i";
				    	$values[":$i"] = utf8_decode($usuario[$i]);
					}
				}
				$values[":id"] = $this->userID;
				$query .= implode("," , $actualizaciones) . " WHERE id=:id";
				$statement = $this->dbConn->prepare($query);
				if ($statement->execute($values))
					$this->returnResponse(SUCCESS_RESPONSE, "Los datos se han actualizado correctamente");
				$this->throwError(UPDATE_ERROR, "Se ha producido un error al intentar actualizar los datos");
			}
			catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}

		public function updateAvatar() {
			$avatar = $this->validateParameter('avatar', STRING);
			if ($this->userID > -1) {
				try {
					if (!is_dir(RUTA_AVATAR))
						@mkdir(RUTA_AVATAR);
					file_put_contents(RUTA_AVATAR . $this->userID . ".png", base64_decode($avatar));
					$this->returnResponse(SUCCESS_RESPONSE, array());
				}
				catch (Exception $e) {
					$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
				}
			}
		}
		
		public function renewToken() {
			if ($this->userID > -1) {
				try {
					$statement = $this->dbConn->prepare("SELECT u.estado, u.id, u.usuario, u.clave, u.correo, u.nombre, u.apellidos, u.perfil_id
					FROM usuario u WHERE u.estado=1 and u.id=:id");
					$statement->bindParam(":id", $this->userID);
					$statement->execute();
					$user = $statement->fetch(PDO::FETCH_ASSOC);
					
					if (!is_array($user))
						$this->returnResponse(DATA_NOT_FOUND, "Error al consultar el usuario");
					if ($user['estado'] != 1)
						$this->returnResponse(USER_NOT_ACTIVE, "Usuario inactivo, por favor contacte al administrador");
					
					$payload = array(
						'iat'	=> time(),
						'iss'	=> 'localhost',
						'exp'	=> time() + MAX_TIME_SESSION,
						'userID'=> $user['id']
					);
					$token = JWT::encode($payload, SECRET_KEY);
					$nombre = current(explode(" ", $user["nombre"]));
					$nombre .= " " . current(explode(" ", $user["apellidos"]));
					$result = array('token' => $token, 'expires_at' => time() + MAX_TIME_SESSION);
					$result = array_merge($result, $user);
					$this->returnResponse(SUCCESS_RESPONSE, $result);
				}
				catch (Exception $e) {
					$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
				}
			}
		}

		public function restorePassword() {
			$email = $this->validateParameter('email', STRING);
			$identificacion = $this->validateParameter('user', STRING);
			try {
				$statement = $this->dbConn->prepare("SELECT u.* FROM usuario u WHERE u.identificacion=:identificacion AND u.correo=:correo");
				$statement->execute(array(":correo" => $email, ":identificacion" => $identificacion));
				$user = $statement->fetch(PDO::FETCH_ASSOC);

				if (!is_array($user))
					$this->returnResponse(SUCCESS_RESPONSE, "No hemos encontrado tu cuenta.<br /><br />Por favor verifica los datos ingresados.");
				if ($user["estado"] != 1)
					$this->returnResponse(SUCCESS_RESPONSE, "Usuario inactivo, por favor contacte al administrador");
				$ultimo_token = strtotime($user["fecha_restablecimiento"]);
				if (time() - $ultimo_token <= 600)
					$this->returnResponse(SUCCESS_RESPONSE, "Debes esperar la menos 10 minutos para generar un nuevo token.");
				$token = md5(md5(time()) . $user["identificacion"]);
				
				//Intentar enviar el correo
				$mail = new PHPMailer();
				$mail->Mailer 	  = "sendmail";
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.gsc.com.co";
				$mail->Port       = 587;
				$mail->Username   = "notificaciones@gsc.com.co";
				$mail->Password   = "notificaciones#1053";
				$mail->From 	  = "notificaciones@gsc.com.co";
				$mail->FromName   = "Notificaciones GSC";
				$mail->Subject    = "Restablece tu clave de acceso a CELRISK";
				$dominio_celrisk = DOMINIO_CELRISK;
				$mail->MsgHTML("<table><tr><td valign='top' style='vertical-align:top;width:90px;'><img src='$dominio_celrisk/imagenes/logo_celrisk.png' width=80 style='width:80px;'></td>"
					. "<td style='vertical-align:top;' valign='top'><div style='font-family:Verdana; font-size:15px;color:black;'>Hola " . $user["nombre"] . ",<br /><br />Hemos recibido una solicitud de restablecimiento de contraseña, "
					. "puedes crear una nueva contraseña de acceso a CELRISK dando clic en el siguiente enlace:<br /><br />"
					. "<a href='$dominio_celrisk/restablecer.php?token=$token'>$dominio_celrisk/restablecer.php?token=$token</a><br /><br />"
					. "Si no realizaste esta solicitud o no quieres cambiar tu contraseña, ignora y elimina este mensaje.<br /><br />"
					. "Para proteger tu cuenta, <b>el enlace proporcionado solo tendrá validez por un periodo de 30 minutos</b> a partir del momento en que recibas esta notificación.<br />No reenvíes este mensaje a otras personas.</div>"
					. "</td></tr></table>"
				);
				$mail->AddAddress(trim($user["correo"]));
				$mail->IsHTML(true);
				if (!$mail->Send())
					$this->returnResponse(SUCCESS_RESPONSE, "Se ha producido un error al intentar enviar el correo con el token de restablecimiento de tu contraseña.<br /><br />Por favor intenta más tarde.");

				$query = "UPDATE usuario SET fecha_restablecimiento=now(), hash_restablecimiento=:token WHERE id=:id";
				$statement = $this->dbConn->prepare($query);
				if ($statement->execute(array(":id" => $user["id"], ":token" => $token)))
					$this->returnResponse(SUCCESS_RESPONSE, "¡Revisa tu buzón de correo!<br /><br />Te hemos enviado un enlace con las indicaciones para el restablecimiento de tu contraseña");
				$this->returnResponse(SUCCESS_RESPONSE, "Se ha producido un error al intentar generar el token.<br /><br />Por favor intenta más tarde");
			}
			catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}
		
		public function generateToken() {
			$username = $this->validateParameter('user', STRING);
			$password = $this->validateParameter('pass', STRING);
			$mode = $this->validateParameter('mode', STRING);
			$pass_mode = $mode == "md5" ? "u.clave=:pass" : "u.clave=md5(:pass)";
			try {
				$statement = $this->dbConn->prepare("SELECT u.estado, u.id, u.usuario, u.accesos, u.clave, u.correo, u.nombre, u.apellidos, u.perfil_id
					FROM usuario u WHERE u.usuario=:user and $pass_mode and u.estado=1");
				$statement->bindParam(":user", $username);
				$statement->bindParam(":pass", $password);
				$statement->execute();
				$user = $statement->fetch(PDO::FETCH_ASSOC);
	
				if (!is_array($user))
					$this->returnResponse(INVALID_USER_PASS, "Usuario/Clave incorrectos");
				if ($user['estado'] != 1)
					$this->returnResponse(USER_NOT_ACTIVE, "Usuario inactivo, por favor contacte al administrador");
				/*if ($user['perfil_id'] != 11)
					$this->returnResponse(USER_NOT_ALLOWED, "Usuario no permitido, por favor contacte al administrador");*/
				$user["version"] = file_get_contents(DIR_APP . "version.txt");
				$this->dbConn
					->prepare("UPDATE usuario SET fecha_ultimoacceso=now(), ip_ultimoacceso=:ip, accesos=:accesos WHERE id=:id")
					->execute(array(
							":id" => $user["id"],
							":accesos" => $user["accesos"] + 1,
							":ip" => $_SERVER["REMOTE_ADDR"]
						));

				$payload = array(
					'iat'	=> time(),
					'iss'	=> 'localhost',
					'exp'	=> time() + MAX_TIME_SESSION,
					'userID'=> $user['id']
				);
				$token = JWT::encode($payload, SECRET_KEY);
				$nombre = current(explode(" ", $user["nombre"]));
				$nombre .= " " . current(explode(" ", $user["apellidos"]));
				$filtro = $this->getClientesSedes($user["id"]);
				$result = array('token' => $token, 'expires_at' => time() + MAX_TIME_SESSION);
				$result = array_merge($result, $filtro,$user);
				$this->returnResponse(SUCCESS_RESPONSE, $result);
			}
			catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}

		public function getClientesSedes($id)
		{
				try{
					//CLIENTES POR USUARIO
					$statement = $this->dbConn->prepare("select distinct v.id from vclientes v join sede s on v.id = s.cliente_id join usuario_sede u on s.id = u.sede_id where v.estado=1 and u.usuario_id = $id");
					$statement->execute();
					$clientes = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$clientes[] = $f["id"];
					}

					//SEDES
					$statement = $this->dbConn->prepare("select distinct s.id from sede s join usuario_sede u on s.id = u.sede_id where s.estado=1 and u.usuario_id = $id");
					$statement->execute();
					$sedes = array();
					while ($f = $statement->fetch(PDO::FETCH_ASSOC)) {
						$sedes[] = $f["id"];
					}
					
					$result = array('clientes' => implode(',',$clientes), 'sedes' => implode(',',$sedes));
					return $result;

				}catch(Exception $e){
					$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
				}
			return array('clientes' => "",'sedes' => "");
		}
				
	}
?>