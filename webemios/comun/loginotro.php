<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir comprueba_tipo_peticion_http.php PRIMERO
$directorio_actual = __DIR__;
$archivo_comprueba = $directorio_actual . '/comprueba_tipo_peticion_http.php';
if (file_exists($archivo_comprueba)) {
    include_once($archivo_comprueba);
}

// Configurar sesión correctamente
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

// Tiempo de vida de sesión de 8 horas
ini_set('session.gc_maxlifetime', 3600 * 8);
session_set_cookie_params(3600 * 8);

// Iniciar sesión
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Limpiar sesión anterior si es necesario
if (isset($_SESSION["usuario_interno"])) {
    session_unset();
}

// Guardar directorio raíz
$_SESSION["directorio"] = str_replace('\\', '/', dirname($directorio_actual));

// Definir constantes necesarias si no están definidas
if (!defined('ID_NINGUNO')) {
    define('ID_NINGUNO', 0);
}
if (!defined('ZONA_HORARIA_UTC')) {
    define('ZONA_HORARIA_UTC', 'UTC');
}

// CORRECCIÓN: Primero incluir los archivos originales ANTES de definir cualquier función
$directorio = $_SESSION["directorio"];

// Lista de archivos a incluir en orden
$archivos_a_incluir = [
    $directorio . '/comun/src/lib/herramientas/util_cadenas.php',
    $directorio . '/src/lib/herramientas/util_excepciones.php',
    $directorio . '/src/lib/modulos/util_inicializacion.php',
    $directorio . '/comun/src/lib/herramientas/util_tiempos.php',
    $directorio . '/comun/src/lib/herramientas/util_sistema.php',
    $directorio . '/comun/src/lib/herramientas/util_pie_pagina.php',
    $directorio . '/comun/src/lib/constantes/constantes.php',
];

foreach ($archivos_a_incluir as $archivo) {
    if (file_exists($archivo)) {
        include_once($archivo);
    }
}

// SOLO definir funciones si NO existen ya
if (!function_exists('dame_log')) {
    function dame_log() {
        return new class {
            public $error_msg = '';
            function error($msg, $e) {
                $this->error_msg = "ERROR: $msg " . $e->getMessage();
                error_log($this->error_msg);
            }
        };
    }
}

if (!function_exists('dame_mensaje_error_excepcion')) {
    function dame_mensaje_error_excepcion($e) {
        return $e->getMessage();
    }
}

if (!function_exists('convierte_ascii_estandar')) {
    function convierte_ascii_estandar($texto) {
        return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('establece_parametros_locales_defecto')) {
    function establece_parametros_locales_defecto() {
        $_SESSION['idioma'] = 'es';
        date_default_timezone_set('Europe/Madrid');
    }
}

if (!function_exists('dame_url_http')) {
    function dame_url_http($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
}

if (!function_exists('dame_texto_pie_pagina')) {
    function dame_texto_pie_pagina() {
        return "© " . date('Y') . " - WebEMIOS - Monitorización y Telecontrol";
    }
}

// Configurar formato de fecha por defecto si no existe
if (!isset($_SESSION["formato_fecha_hora_local"])) {
    $_SESSION["formato_fecha_hora_local"] = "d/m/Y H:i:s";
    $_SESSION["formato_fecha_hora_local_sin_segundos"] = "d/m/Y H:i";
}

// Intentar establecer preferencias
try {
    if ((!isset($_SESSION["id_red"])) || ($_SESSION["id_red"] == ID_NINGUNO)) {
        if (function_exists('establece_parametros_locales_defecto')) {
            establece_parametros_locales_defecto();
        }
    }
} catch (Exception $e) {
    $log = dame_log();
    $log->error("[" . ($_SESSION["id_usuario"] ?? 'desconocido') . "] Excepción: ", $e);
    
    $mensaje_error = dame_mensaje_error_excepcion($e);
    $mensaje_error = convierte_ascii_estandar($mensaje_error);
    print($mensaje_error);
    exit();
}

// Token de seguridad
if (!isset($_SESSION['tokens_login'])) {
    $_SESSION['tokens_login'] = array();
}

if (function_exists('dame_timestamp_ahora_milisegundos_utc')) {
    $token_login = md5(dame_timestamp_ahora_milisegundos_utc());
} else {
    $token_login = md5(uniqid(mt_rand(), true));
}
array_push($_SESSION['tokens_login'], $token_login);

// Datos del formulario
$id_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : null;
$contrasenya = isset($_GET['contrasenya']) ? $_GET['contrasenya'] : null;
$auto_login = isset($_GET['auto']) ? $_GET['auto'] : null;

// Obtener URL base para recursos
$url_raiz = dirname(dirname($_SERVER['SCRIPT_NAME'])); // /webemios
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitorización y telecontrol con análisis visual de datos y motor de reglas en tiempo real">
    <link rel="shortcut icon" href="<?php echo $url_raiz; ?>/comun/rsc/imagenes/favicon.ico" />
    
    <!-- Estilos -->
    <?php
    // CORRECCIÓN: Incluir estilos y corregir rutas automáticamente
    $archivos_estilos = [
        '/comun/includes/estilos_librerias.php',
        '/comun/includes/estilos_web.php',
        '/includes/estilos_librerias.php',
        '/includes/estilos_web.php'
    ];
    
    foreach ($archivos_estilos as $archivo) {
        $ruta_completa = $directorio . $archivo;
        if (file_exists($ruta_completa)) {
            ob_start();
            include($ruta_completa);
            $contenido = ob_get_clean();
            
            // Corregir rutas que empiezan con /comun/ o /css/ o /js/
            $contenido = preg_replace(
                '/(src|href)=["\']\/(comun\/|css\/|js\/)/',
                '$1="' . $url_raiz . '/$2',
                $contenido
            );
            
            echo $contenido;
        }
    }
    ?>
    
    <!-- Estilos de respaldo -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
        }
        
        #banner {
            text-align: center;
            margin-bottom: 30px;
        }
        
        #logo-web img {
            max-height: 100px;
        }
        
        #contenido-login {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        fieldset {
            border: none;
        }
        
        .iconos-login {
            font-size: 24px;
            color: #2c5364;
            margin-right: 15px;
            vertical-align: middle;
        }
        
        input[type="text"], input[type="password"] {
            width: 80%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #2c5364;
            box-shadow: 0 0 0 3px rgba(44, 83, 100, 0.1);
        }
        
        input:disabled {
            background: #e9ecef;
            cursor: not-allowed;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2c5364 0%, #203a43 100%);
            color: white;
            padding: 12px 50px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 83, 100, 0.4);
        }
        
        .btn-success:disabled {
            background: #adb5bd;
            cursor: not-allowed;
            transform: none;
        }
        
        #pie-pagina {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 30px;
            font-size: 14px;
        }
        
        .elemento-no-seleccionable {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        
        #login-automatico, #token-login {
            display: none;
        }
        
        .row-fluid {
            margin-bottom: 20px;
        }
        
        .color-blanco {
            color: white;
        }
    </style>
</head>
<body>
    <noscript>
        <div style="background: #ff6b6b; color: white; padding: 20px; text-align: center; position: fixed; top: 0; width: 100%; z-index: 9999;">
            ⚠️ ¡Tu navegador no soporta JavaScript! Por favor, activa JavaScript para usar esta aplicación.
        </div>
    </noscript>

    <div id='contenedor' class='container'>
        <div id='banner' class='elemento-no-seleccionable'>
            <span id='logo-web'>
                <?php
                if (isset($_SESSION["ruta_logo"])) {
                    $ruta_logo = $_SESSION["ruta_logo"];
                    $url_logo = $_SESSION["url_logo"];
                } else {
                    $ruta_logo = $url_raiz . "/rsc/imagenes/logo_web.png";
                    $url_logo = defined('WEB_ENERGY_MINUS') ? WEB_ENERGY_MINUS : "#";
                }
                
                $html_logo = "<img src='" . $ruta_logo . "' alt='WebEMIOS Logo'>";
                if ($url_logo != "" && $url_logo != "#") {
                    if (function_exists('dame_url_http')) {
                        $url_logo_http = dame_url_http($url_logo);
                    } else {
                        $url_logo_http = $url_logo;
                    }
                    $html_logo = "<a href='" . $url_logo_http . "'>" . $html_logo . "</a>";
                }
                print($html_logo);
                ?>
            </span>
        </div>

        <div id='contenido-login' class='row-fluid elemento-no-seleccionable'>
            <div align="center">
                <div id='controles-login' class="row-fluid">
                    <fieldset>
                        <div class='row-fluid'>
                            <div align="center">
                                <i class='iconos-login icon-user'></i>
                                <input type='text' name='usuario' id='usuario' class='TLNT_input_mandatory' 
                                    value='<?php echo htmlspecialchars($id_usuario ?? ''); ?>' 
                                    placeholder="Usuario"
                                    autocomplete="username"
                                    <?php echo $id_usuario ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                        <div class='row-fluid'>
                            <div align="center">
                                <i class='iconos-login icon-lock'></i>
                                <input type='password' name='contrasenya' id='contrasenya' class='TLNT_input_mandatory' 
                                    value='<?php echo htmlspecialchars($contrasenya ?? ''); ?>' 
                                    placeholder="Contraseña"
                                    autocomplete="current-password"
                                    <?php echo $contrasenya ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                        <br/>
                        <div class='row-fluid'>
                            <div align='center'>
                                <button type='button' id='boton-login' class='btn btn-success' 
                                    <?php echo ($id_usuario && $contrasenya) ? '' : 'disabled'; ?>>
                                    <i class='icon-signin color-blanco'></i> Iniciar Sesión
                                </button>
                            </div>
                        </div>
                    </fieldset>
                    
                    <?php if ($id_usuario && $contrasenya): ?>
                    <div id='login-automatico' hidden>
                        <?php echo htmlspecialchars($auto_login ?? ''); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div id='token-login' hidden>
                        <?php echo $token_login; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id='pie-pagina' class='row-fluid elemento-no-seleccionable'>
            <p id='texto-pie-pagina'>
                <?php
                    if (function_exists('dame_texto_pie_pagina')) {
                        print(dame_texto_pie_pagina());
                    } else {
                        echo "© " . date('Y') . " - WebEMIOS";
                    }
                ?>
            </p>
        </div>
    </div>

    <!-- Fuentes y JavaScripts -->
    <?php
    $archivos_fuentes = [
        '/comun/includes/fuentes_librerias.php',
        '/comun/includes/fuentes_web.php',
        '/includes/fuentes_librerias.php',
        '/includes/fuentes_web.php'
    ];
    
    foreach ($archivos_fuentes as $archivo) {
        $ruta_completa = $directorio . $archivo;
        if (file_exists($ruta_completa)) {
            ob_start();
            include($ruta_completa);
            $contenido = ob_get_clean();
            
            // Corregir rutas en scripts
            $contenido = preg_replace(
                '/(src)=["\']\/(comun\/|js\/)/',
                '$1="' . $url_raiz . '/$2',
                $contenido
            );
            
            echo $contenido;
        }
    }
    ?>
</body>
</html>