<?php
	define("iC", true);
    require_once (dirname(__FILE__) . "/../conf/config.php");
    $token = isset($_GET["token"]) ? $_GET["token"] : die("Error al recibir el token");
    if (strlen($token) != 32) die("Error en la validación del token");
    BD::changeInstancia("mysql");
    $r = BD::consultar("usuario", array("*"), array("hash_restablecimiento" => $token));
    if ($f = BD::obtenerRegistro()) {
        if (strtotime($f["fecha_restablecimiento"]) < time() - 1800) {  //30minutos
            die("Token ha caducado");
        }
        if (isset($_POST["clave"])) {
            $clave = $_POST["clave"];
            $r = BD::sql_query("UPDATE usuario SET clave='" .md5($_POST["clave"])  . "', hash_restablecimiento=NULL, fecha_restablecimiento=NULL WHERE id=" . $f["id"]);
            if ($r)
                die("Felicidades!, tu clave se ha modificado correctamente.");
            die("Se ha producido un error al intentar actualizar tu clave de acceso");
        }
    }
    else
        die("Error al validar el token de restablecimiento de contraseña");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Restablecimiento de contraseña</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="favicon.ico">
		<link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/general.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="../css/jquery.alerts.css" type="text/css" media="screen" />
        <script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.js"></script>
        <script src="../js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="../js/jquery/jquery.blockUI.js"></script>
        <script type="text/javascript" src="../js/jquery/jquery.alerts2.js"></script>
        <script src="../js/adicional.js"></script>
        <style>
            body {
                background-color:#666;
            }
        </style>
    </head>
    <body>
        <div class="login-outer">
            <div class="login-middle">
                <form id="formLogin" method="POST" class="form-signin" action="index.php">
                    <table cellspacing="3" width="240" align="center" class="animated fadeIn">
                        <tr>
                            <td>
                                <h2>Hola <?php echo $f["nombre"]; ?>,</h2>
                                Ingresa una nueva contraseña para tu cuenta CELRISK asociada al correo <?php echo $f["correo"]; ?>.</h2>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table class="ui-corner-all" style="margin-top:20px;">
                                    <tr>
                                        <td>
                                            <div class="input-prepend" style='margin-bottom:10px;'>
                                                <span class="add-on"><i class="icon-lock"></i></span>
                                                <input style='width:200px;' autocomplete="off" name='clave' id='clave' type="password" placeholder="Contraseña nueva" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            <div class="input-prepend" style='margin-bottom:10px;'>
                                                <span class="add-on"><i class="icon-lock"></i></span>
                                                <input style='width:200px;' type="password" id='clave2' name='clave2' placeholder="Vuelve a escribir la contraseña" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=right style="padding-bottom:6px;">
                                            <input type=button class='btn btn-success' style='font-size: 14px; font-weight: bold;' name='restablecer' id="btnRestablecer" value='Enviar'>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function() {
            $("#btnRestablecer").click(function() {
                var c1 = $("#clave").val();
                var c2 = $("#clave2").val();
                if (c1 != c2) {
                    jAlert("Por favor verifica que las claves coincidan", "Información");
                    return false;
                }

                $.blockUI({
                    message: '<center><table style="color:black;margin-top:15px;margin-bottom:15px;"><tr><td width=50 valign=middle><img src="img/ajax-loader2.gif" /></td><td style="font-size:18px;" valign="middle">Un momento por favor...</td></tr></table></center>',
                    baseZ: 99999
                });

                $.post("restablecer.php?token=<?php echo $token; ?>", { clave : c1 }, function(result) {
                    hideMessage();
                    jAlert(result, "Información", function() {
                        document.location.href='index.php';
                    });
                })
            });
        });
        
    </script>
</html>