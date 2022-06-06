<?php
	define("iC", true);
    require_once (dirname(__FILE__) . "/conf/config.php");
    $token = isset($_GET["token"]) ? $_GET["token"] : die("<script>document.location.href='index.php';</script>");
    if (strlen($token) != 32) die("<script>document.location.href='index.php';</script>");

    BD::changeInstancia("mysql");
    $r = BD::consultar("usuario", array("*"), array("hash_restablecimiento" => $token));
    if ($f = BD::obtenerRegistro()) {
        if (strtotime($f["fecha_restablecimiento"]) < time() - 1800) {  //30minutos
            die("El token de restablecimiento de contraseña ha caducado. Recuerda que una vez generado el token tienes un plazo de 30 minutos para realizar esta operación");
        }
        if (isset($_POST["clave"])) {
            $clave = $_POST["clave"];
            $r = BD::sql_query("UPDATE usuario SET clave='" .md5($_POST["clave"])  . "', hash_restablecimiento=NULL, fecha_restablecimiento=NULL WHERE id=" . $f["id"]);
            if ($r)
                die("ok");
            die("Se ha producido un error al intentar actualizar tu clave de acceso");
        }
    }
    else
        die("Error al validar el token de restablecimiento de contraseña. Debes iniciar nuevamente el proceso");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title><?php echo htmlentities(NOMBRE_APP); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="favicon.ico">
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" />
		<link rel="stylesheet" href="css/jquery/themes/vtc/ui.all.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/jquery/ui.jqgrid.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/jquery/ui.multiselect.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/touchspin.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/menu.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/jquery.alerts.css" type="text/css" media="screen" />
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic,300italic">
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,700,400italic,300italic">
		<link rel="stylesheet" href="css/general.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/daterangepicker.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>
        <script src="js/jquery-ui-1.8.2.custom.min.js"></script>
		<script src="js/jquery/jqGrid/i18n/grid.locale-es.js" type="text/javascript"></script>
		<script src="js/jquery.jqGrid.js"></script>
		<script src="js/jquery.steps.js"></script>
		<script src="js/sweetalert2.js"></script>
		<script type="text/javascript" src="js/jquery/ajaxfileupload.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.center.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.alerts2.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.tools.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.forms.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.hoverIntent.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.layout.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.mbMenu.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.metadata.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.uniform.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.validate.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.uploader.js"></script>

		<script type="text/javascript" src="js/jquery/jquery.maskMoney.js"></script>
		<script type="text/javascript" src="js/jquery/ui.datepicker-es.js"></script>
		<script type="text/javascript" src="js/moment.min.js"></script>
		<script type="text/javascript" src="js/es.js"></script>
		<script type="text/javascript" src="js/hcharts.js"></script>

		<script type="text/javascript" src="js/jquery/jquery.daterangepicker.js"></script>
		<script type="text/javascript" src="js/jquery/zbootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.blockUI.js"></script>
		<script type="text/javascript" src="js/adicional.js?<?php echo time(); ?>"></script>
		<script type="text/javascript" src="js/jquery/jquery.touchspin.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.ui.touch-punch.min.js"></script>
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
                            <td align=center><img width="150" src="imagenes/logo_top.png" alt=""></td>
                        </tr>
                        <tr>
                            <td>
                                <h2>Hola <?php echo $f["nombre"]; ?>,</h2>
                                Ingresa una nueva contraseña asociada al correo <?php echo $f["correo"]; ?>.</h2>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table class="ui-corner-all" style="margin-top:20px;">
                                    <tr>
                                        <td>
                                            <div class="input-prepend" style='margin-bottom:10px;'>
                                                <span class="add-on"><i class="icon-lock"></i></span>
                                                <input style='width:200px;' autocomplete="off" name='clave' id='clave' type="password" placeholder="Nueva contraseña" />
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
                                            <input type=button class='btn btn-primary btn-block' style='font-size: 14px; font-weight: bold;' name='restablecer' id="btnRestablecer" value='Restablecer clave'>
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
                    mensaje("Información", "Por favor verifica que las claves coincidan", "warning");
                    return false;
                }
                if (c1 == "") {
                    mensaje("Información", "Debe escribir una clave válida", "warning");
                    return false;
                }

                showMessage();

                $.post("restablecer.php?token=<?php echo $token; ?>", { clave : c1 }, function(result) {
                    hideMessage();
                    if (/^ok$/.test(result)) {
                        mensaje("¡Operación existosa!", "Tu clave ha sido modificada correctamente. Ahora puedes ingresar con tu nueva clave", "success", function() {
                            document.location.href='index.php';
                        });
                    }
                    else
                        mensaje("Error", result, "error");
                })
            });
        });
        
    </script>
</html>