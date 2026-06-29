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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="EMIOS - Monitorizacion y telecontrol">
    <link rel="shortcut icon" href="../comun/rsc/imagenes/favicon.ico" />
    <link rel="stylesheet" href="../comun/rsc/lib/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="../comun/rsc/lib/font-awesome/css/font-awesome.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1b3a1b 0%, #0d260d 50%, #1a2e1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        #contenedor {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #banner {
            text-align: center;
            margin-bottom: 30px;
        }

        #banner img {
            height: 60px;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        #controles-login {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px 35px 35px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        #controles-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #298B21, #52A652, #5AB55A);
        }

        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-title h3 {
            color: #1a1a2e;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0 0 5px;
            letter-spacing: 0.5px;
        }

        .login-title p {
            color: #888;
            font-size: 0.85rem;
            margin: 0;
        }

        .input-group-modern {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
            z-index: 1;
            transition: color 0.3s;
        }

        .input-group-modern input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
            color: #333;
        }

        .input-group-modern input:focus {
            border-color: #298B21;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(41, 139, 33, 0.1);
        }

        .input-group-modern input:focus + .input-icon {
            color: #298B21;
        }

        .input-group-modern input::placeholder {
            color: #bbb;
        }

        #boton-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #298B21, #3d9f36);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        #boton-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 139, 33, 0.4);
        }

        #boton-login:active {
            transform: translateY(0);
        }

        #boton-login i {
            margin-right: 8px;
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .login-footer p {
            color: #999;
            font-size: 0.8rem;
            margin: 0;
        }

        .alert-modern {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-modern.alert-danger {
            background: #fff0f0;
            border: 1px solid #ffd4d4;
            color: #d63031;
        }

        .alert-modern.alert-danger i {
            font-size: 18px;
        }

        @media (max-width: 480px) {
            #controles-login {
                padding: 30px 20px 25px;
            }
            body { padding: 10px; }
        }
    </style>
</head>
<body>
    <div id='contenedor'>
        <div id='banner'>
            <span id='logo-web'>
                <a href='http://www.energy-minus.es'><img src='../rsc/imagenes/logo_web.png' alt='EMIOS'></a>
            </span>
        </div>

        <div id='controles-login'>
            <div class='login-title'>
                <h3>EMIOS v5.4.0.6</h3>
                <p>Monitorización y telecontrol</p>
            </div>

            <?php if ($login_error): ?>
                <div class="alert-modern alert-danger">
                    <i class='icon-warning-sign'></i>
                    <span><?php echo htmlspecialchars($login_error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="token" value="<?php echo $token_login ?>">
                
                <div class="input-group-modern">
                    <input type='text' name='usuario' id='usuario' class='form-control' 
                           placeholder='Usuario' value='admin' autocomplete='username'>
                    <i class='icon-user input-icon'></i>
                </div>

                <div class="input-group-modern">
                    <input type='password' name='contrasenya' id='contrasenya' class='form-control' 
                           placeholder='Contraseña' value='admin' autocomplete='current-password'>
                    <i class='icon-lock input-icon'></i>
                </div>

                <button type='submit' name='login_submit' id='boton-login' class='btn'>
                    <i class='icon-signin'></i> Iniciar sesión
                </button>
            </form>

            <div class='login-footer'>
                <p>EMIOS v5.4.0.6 r1 &copy; EnergyMinus</p>
            </div>
        </div>
    </div>
</body>
</html>
