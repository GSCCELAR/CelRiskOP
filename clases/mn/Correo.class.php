<?php
	/**
	 * @author Julio César Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 * 
	 * Clase para el envío de correos de notificación
	**/
	class Correo {
		
		public static $DEBUG_EMAIL 		= false;
		public static $DEBUG_DESTINO 	= "juliogarcesrios@gmail.com";
		
		public static function enviarEmail($correo_destino, $correo_cc, $asunto, $mensajeHTML) {
			$mail = new PHPMailer();
			if (self::$DEBUG_EMAIL) {
				$asunto .= " [para: " . $correo_destino . "]";
				$correo_destino = self::$DEBUG_DESTINO;
			}
			
			$mensajeHTML = "<div style='font-family:Verdana; font-size:12px;'>" . $mensajeHTML . "</div>";
			$mail->IsSMTP();
			$mail->SMTPAuth   = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host       = "mail.gsc.com.co";
			$mail->Port       = 465;
			$mail->Username   = "notificaciones@gsc.com.co";
			$mail->Password   = "GSC#s1st3ma5@5832";
			$mail->From 	  = "notificaciones@gsc.com.co";
			$mail->FromName   = "Notificaciones GSC";
			$mail->AddReplyTo("notificaciones@gsc.com.co", "Sistema de notificaciones CELRISK");
			$mail->Subject    = $asunto;
			$mail->WordWrap   = 50;
			$mail->MsgHTML($mensajeHTML);
			
			$destinos = explode(",", $correo_destino);
			foreach($destinos as $destino) {
				if (trim($destino) != "")
					$mail->AddAddress(trim($destino));
			}

			$destinos = explode(",", $correo_cc);
			foreach($destinos as $destino) {
				if (trim($destino) != "")
					$mail->AddBCC(trim($destino));
			}
			$mail->IsHTML(true);
			return $mail->Send();
		}

		public static function enlaceRestablecimientoClave($usuario) {
			$token = md5(md5(time()) . $usuario->getCampo("identificacion"));
			if ($usuario->setCampo("hash_restablecimiento", $token));
			if ($usuario->setCampo("fecha_restablecimiento", date("Y-m-d H:i:s")));
			if (!$usuario->update())
				die("Error al actualizar el token de restablecimiento de clave");
			$dominio_celrisk = Config::$DOMINIO;
			$mensaje = "<table><tr><td valign='top' style='vertical-align:top;width:110px;'><img src='$dominio_celrisk/imagenes/logo_celrisk.png' width=100 style='width:100px;'></td>"
			. "<td style='vertical-align:top;' valign='top'><div style='font-family:Verdana; font-size:15px;color:black;'>Hola " . $usuario->getCampo("nombre", true) . ",<br /><br />Hemos recibido una solicitud de <b>restablecimiento de contraseña</b>, "
			. "puedes crear una nueva contraseña de acceso a la <b>CELRISK</b> dando clic en el siguiente enlace:<br /><br />"
			. "<a href='$dominio_celrisk/restablecer.php?token=$token'>$dominio_celrisk/restablecer.php?token=$token</a><br /><br />"
			. "Si no iniciaste esta solicitud o no quieres cambiar tu contraseña, ignora y elimina este mensaje.<br /><br />"
			. "Para proteger tu cuenta, <b>el enlace proporcionado solo tendrá validez por un periodo de 30 minutos</b> a partir del momento en que recibas esta notificación.<br />No reenvíes este mensaje a otras personas.</div>"
			. "</td></tr></table>";

			return Correo::enviarEmail($usuario->getCampo("correo"), "", "Restablece tu clave de acceso a CELRISK", $mensaje);
		}
	}
?>