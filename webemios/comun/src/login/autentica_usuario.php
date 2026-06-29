<?php
    // Se recupera el directorio raíz del proyecto y se guarda en la variable _SESSION
	$directorio_actual = getcwd();
	$directorio_actual = str_replace('\\', '/', $directorio_actual);
	$directorio_raiz = str_replace('/comun/src/login', '', $directorio_actual);

	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	$_SESSION["directorio"] = $directorio_raiz;

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_criptografia.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    $idiomas = new Idiomas();

    // Parámetros codificados
    $cadena_datos_usuario_codificada = $_POST["datos_usuario"];
    $cadena_datos_usuario = decodifica_cadena_peticion_php($cadena_datos_usuario_codificada);
    $datos_usuario = json_decode($cadena_datos_usuario, true);
    $token = $datos_usuario["token"];
    $usuario = $datos_usuario["usuario"];
    $contrasenya = $datos_usuario['contrasenya'];

    // Se comprueba si el token es correcto
    $indice_token = array_search($token, $_SESSION["tokens_login"]);
    if (($indice_token === false) || ($indice_token === NULL))
    {
        throw new Exception("Token de login incorrecto");
    }

    // Log
    $log = dame_log();
    $log->info("Parámetros codificados: '".$cadena_datos_usuario."'");

    // Se comprueba si el usuario ya ha intentado el número máximo de "logins" por sesión
    if ((array_key_exists("info_logins_incorrectos", $_SESSION) == true) &&
        (array_key_exists($usuario, $_SESSION["info_logins_incorrectos"]) == true))
    {
        $numero_intentos = $_SESSION["info_logins_incorrectos"][$usuario]["numero_intentos"];
        if ($numero_intentos >= NUMERO_MAXIMO_INTENTOS_LOGIN_SESION)
        {
            $timestamp_utc = dame_timestamp_ahora_milisegundos_utc();
            $timestamp_ultimo_intento_utc = $_SESSION["info_logins_incorrectos"][$usuario]["timestamp_utc"];
            $numero_segundos_desde_ultimo_intento = (int) (($timestamp_utc - $timestamp_ultimo_intento_utc) / 1000);
            if ($numero_segundos_desde_ultimo_intento < NUMERO_MINIMO_SEGUNDOS_ESPERA_MAXIMO_INTENTOS_LOGIN_SESION)
            {
                $numero_segundos_espera_restantes = (NUMERO_MINIMO_SEGUNDOS_ESPERA_MAXIMO_INTENTOS_LOGIN_SESION - $numero_segundos_desde_ultimo_intento);
                $mensaje_error = ($idiomas->_("Número máximo de intentos alcanzado")." \n(".
                    $idiomas->_("segundos de espera restantes").": ".$numero_segundos_espera_restantes.")");
                print(json_encode(array(
                    "res" => "ERROR",
                    "msg" => $mensaje_error)));
                return;
            }
            else
            {
                unset($_SESSION["info_logins_incorrectos"][$usuario]);
            }
        }
    }

    // Se autentica el usuario
    $resultado = autentica_usuario(
        $usuario,
        $contrasenya);

    // Si el resultado es correcto se guarda el "token" (para identificar la sesión)
    // y se elimina el "token" del "login"
    if ($resultado["res"] == "OK")
    {
        $_SESSION["token_login"] = $token;
        unset($_SESSION["tokens_login"][$indice_token]);

        // Se elimina la información de intentos de "login" del usuario
        if (array_key_exists("info_logins_incorrectos", $_SESSION) == true)
        {
            unset($_SESSION["info_logins_incorrectos"][$usuario]);
        }

        // Se regenera la "cookie" de la sesión
        // (http://php.net/manual/es/function.session-regenerate-id.php)
        session_regenerate_id();
    }
    else
    {
        // Se actualiza el número de errores de intentos de "login" del usuario
        if (array_key_exists("info_logins_incorrectos", $_SESSION) == false)
        {
            $_SESSION["info_logins_incorrectos"] = array();
        }
        $timestamp_utc = dame_timestamp_ahora_milisegundos_utc();
        if (array_key_exists($usuario, $_SESSION["info_logins_incorrectos"]) == false)
        {
            $info_login_incorrecto = array(
                "numero_intentos" => 1,
                "timestamp_utc" => $timestamp_utc);
            $_SESSION["info_logins_incorrectos"][$usuario] = $info_login_incorrecto;
        }
        else
        {
            $numero_intentos = $_SESSION["info_logins_incorrectos"][$usuario]["numero_intentos"] + 1;
            $info_login_incorrecto = array(
                "numero_intentos" => $numero_intentos,
                "timestamp_utc" => $timestamp_utc);
        }
        $_SESSION["info_logins_incorrectos"][$usuario] = $info_login_incorrecto;
    }

    // Se devuelve el resultado
    print(json_encode(array(
        "res" => $resultado["res"],
        "msg" => $resultado["msg"]))
    );
?>
