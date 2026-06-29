<?php
session_start();
$_SESSION["directorio"] = __DIR__;

include_once(__DIR__ . '/comun/log/log.php');
include_once(__DIR__ . '/comun/src/lib/herramientas/util_cadenas.php');
include_once(__DIR__ . '/src/lib/herramientas/util_excepciones.php');
include_once(__DIR__ . '/src/lib/modulos/util_inicializacion.php');
include_once(__DIR__ . '/comun/src/lib/herramientas/util_tiempos.php');

// Generate login tokens
if (!isset($_SESSION['tokens_login'])) {
    $_SESSION['tokens_login'] = array();
}
$token = md5(round(microtime(true) * 1000));
$_SESSION['tokens_login'][] = $token;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>EMIOS - Login</title>
    <link rel="stylesheet" href="./comun/rsc/lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./comun/rsc/lib/font-awesome/css/font-awesome.css">
</head>
<body style="background:#f0f0f0;padding:50px">
    <div align="center">
        <img src="./rsc/imagenes/logo_web.png" style="margin-bottom:20px">
        <div style="background:white;padding:30px;border-radius:5px;width:350px">
            <h3>EMIOS v5.4.0.6</h3>
            <form method="post" action="login_simple_post.php">
                <input type="hidden" name="token" value="<?php echo $token ?>">
                <div style="margin:10px">
                    <input type="text" name="usuario" placeholder="Usuario" class="form-control" value="admin">
                </div>
                <div style="margin:10px">
                    <input type="password" name="contrasenya" placeholder="Contraseña" class="form-control" value="admin">
                </div>
                <button type="submit" class="btn btn-success btn-block">Entrar</button>
            </form>
        </div>
        <p style="margin-top:20px;color:#666">EMIOS v5.4.0.6 r1</p>
    </div>
</body>
</html>
