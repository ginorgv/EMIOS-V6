<?php
if ($_SERVER['REQUEST_METHOD'] != "GET" && $_SERVER['REQUEST_METHOD'] != "POST") {
    exit;
}

ini_set('session.gc_maxlifetime', 3600 * 8);
session_set_cookie_params(3600 * 8);
session_start();
if (isset($_SESSION["usuario_interno"])) {
    session_unset();
}
$dir = dirname(__DIR__);
$_SESSION["directorio"] = $dir;

require_once "$dir/comun/src/lib/herramientas/util_cadenas.php";
require_once "$dir/comun/src/lib/herramientas/util_tiempos.php";
require_once "$dir/src/lib/constantes/constantes.php";
require_once "$dir/src/lib/BasesDatos/BaseDatosRed.php";

// --- PROCESAR LOGIN ---
$login_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $token = $_POST['token'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';

    if (!isset($_SESSION['tokens_login']) || !in_array($token, $_SESSION['tokens_login'])) {
        $login_error = 'Token invalido';
    } else {
        $_SESSION['tokens_login'] = array_diff($_SESSION['tokens_login'], [$token]);

        try {
            $bd = BaseDatosRed::dame_base_datos();
            $res = $bd->ejecuta_consulta("SELECT id, contrasenya, nombre, perfil FROM usuarios WHERE id='" . $bd->_($usuario) . "'");
            
            if ($res && $res->dame_numero_filas() > 0) {
                $row = $res->dame_siguiente_fila();
                if (crypt($contrasenya, $row['contrasenya']) === $row['contrasenya']) {
                    // Login OK - establecer sesion manualmente
                    $_SESSION["id_usuario"] = $usuario;
                    $_SESSION["nombre_usuario"] = $row['nombre'];
                    $_SESSION["perfil"] = $row['perfil'];
                    $_SESSION["id_red"] = ID_NINGUNO;
                    $_SESSION["token_login"] = $token;
                    $_SESSION['zona_horaria'] = date_default_timezone_get();
                    
                    header('Location: ../index.php?sesion=' . session_id());
                    exit;
                }
            }
            $login_error = 'Credenciales incorrectas';
        } catch (Throwable $e) {
            $login_error = 'Error: ' . $e->getMessage();
        }
    }
}

// --- GENERAR TOKEN ---
if (!isset($_SESSION['tokens_login'])) {
    $_SESSION['tokens_login'] = array();
}
$token_login = md5((string)round(microtime(true) * 1000));
$_SESSION['tokens_login'][] = $token_login;

// --- CARGAR IDIOMAS ---
$cadenas_idiomas_comun = @json_decode(file_get_contents("$dir/comun/rsc/idiomas/idiomas.json"));
$cadenas_idiomas_web = @json_decode(file_get_contents("$dir/rsc/idiomas/idiomas.json"));
$_SESSION["cadenas_idiomas"] = (object) array_merge((array) $cadenas_idiomas_comun, (array) $cadenas_idiomas_web);

// --- ESTABLECER PREFERENCIAS ---
if ((!isset($_SESSION["id_red"])) || ($_SESSION["id_red"] == ID_NINGUNO)) {
    if (isset($_SESSION["colores"])) {
        $_SESSION["colores"]["color_tema_oscuro"] = "#298B21";
        $_SESSION["colores"]["color_tema_intermedio"] = "#52A652";
        $_SESSION["colores"]["color_tema_claro"] = "#5AB55A";
        $_SESSION["colores"]["color_tema_fondo"] = "#DDDDDD";
        $_SESSION["colores"]["color_tema_fondo_claro"] = "#F0F0F0";
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="description" content="EMIOS - Monitorizacion y telecontrol">
    <link rel="shortcut icon" href="../comun/rsc/imagenes/favicon.ico" />
    <link rel="stylesheet" href="../comun/rsc/lib/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="../comun/rsc/lib/font-awesome/css/font-awesome.css" />
    <link type="text/css" rel="stylesheet" href="../comun/rsc/estilos/estilos.php"/>
    <style>
        body { background-color: #DDDDDD; font-family: Arial, sans-serif; }
        #contenedor { width: 400px; margin: 80px auto; }
        #banner { text-align: center; margin-bottom: 20px; }
        #controles-login { background: white; padding: 25px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        #boton-login { width: 100%; margin-top: 10px; }
        #pie-pagina { text-align: center; margin-top: 20px; color: #666; }
    </style>
</head>
<body>
    <div id='contenedor'>
        <div id='banner'>
            <span id='logo-web'>
                <a href='http://www.energy-minus.es'><img src='../rsc/imagenes/logo_web.png'></a>
            </span>
        </div>
        <?php if ($login_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
        <div id='contenido-login'>
            <div align="center">
                <div id='controles-login'>
                    <h3 style="margin-top:0">EMIOS v5.4.0.6</h3>
                    <form method="post" action="">
                        <input type="hidden" name="token" value="<?php echo $token_login ?>">
                        <div style="margin:10px 0">
                            <input type='text' name='usuario' id='usuario' class='form-control' placeholder='Usuario' value='admin'>
                        </div>
                        <div style="margin:10px 0">
                            <input type='password' name='contrasenya' id='contrasenya' class='form-control' placeholder='Contrasena' value='admin'>
                        </div>
                        <button type='submit' name='login_submit' id='boton-login' class='btn btn-success'>
                            <i class='icon-signin'></i> Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div id='pie-pagina'>
            <p>EMIOS v5.4.0.6 r1</p>
        </div>
    </div>
</body>
</html>
