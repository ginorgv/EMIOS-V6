
<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sesion.php');


    // Constantes

    // Números máximos de caracteres en los parámetros y el  resultado de ejecución de función en el log
    const NUMERO_MAXIMO_CARACTERES_PARAMETROS_EJECUCION_FUNCION_LOG = 2500;
    const NUMERO_MAXIMO_CARACTERES_RESULTADO_EJECUCION_FUNCION_LOG = 2500;


    // Devuelve si la sesión es correcta o no (si no es correcta se devuelve la respuesta correspondiente en la respuesta del script)
    function dame_sesion_correcta()
    {
        $sesion_correcta = true;
        $id_sesion = substr(md5($_SESSION["id_usuario"].$_SESSION["token_login"]), 0, LONGITUD_ID_SESION);
        if ($id_sesion != $_POST["id_sesion"])
        {
            $sesion_correcta = false;
            $respuesta_script = json_encode(array("res" => "ID_SESION_INCORRECTO"));
        }
        if ($sesion_correcta == true)
        {
            $resultado = comprueba_parametros_sesion();
            if ($resultado["sesion_correcta"] == false)
            {
                $sesion_correcta = false;
                $respuesta_script = $resultado["respuesta_script"];
            }
        }
        if ($sesion_correcta == false)
        {
            $log = dame_log();
            $log->warn("[".$_SESSION["id_usuario"]."] "."Sesión incorrecta (respuesta del script: ".json_encode($respuesta_script).")");
        }
        return (array(
            "sesion_correcta" => $sesion_correcta,
            "respuesta_script" => $respuesta_script
        ));
    }


    // Función que ejecuta un comando del sistema
    function ejecuta_comando($comando)
    {
        $log = dame_log();
        $log->debug("[".$_SESSION["id_usuario"]."] ".
            "Inicio (comando: '".$comando."')");

        // http://stackoverflow.com/questions/7093860/php-shell-exec-vs-exec
        // (Nota: Se cierra la escritura de la sesión para que se puedan seguir ejecutando otros scripts PHP si hay un 'timeout')
        session_write_close();
        $resultado = shell_exec($comando);
        session_start();

        $log->debug("[".$_SESSION["id_usuario"]."] ".
            "Fin (respuesta: '".$resultado."')");
        return ($resultado);
    }


    // Función que ejecuta una función externa (en formato JSON)
    function ejecuta_funcion_externa($ruta, $parametros_funcion, $usuario_administrador)
    {
        $log = dame_log();
        $cadena_log_parametros_funcion_externa = dame_cadena_log_parametros_funcion_externa($parametros_funcion);
        $log->info("Inicio de ejecución de función externa (parámetros función: '".$cadena_log_parametros_funcion_externa."'");

        // Se crea la variable global de resultados de funciones externas
        if (!isset($GLOBALS["ejecuciones_funciones_externas"]))
        {
            $GLOBALS["ejecuciones_funciones_externas"] = array();
        }

        // Si ya se ha ejecutado la función en este 'script', se devuelve el resultado guardado
        foreach ($GLOBALS["ejecuciones_funciones_externas"] as $ejecucion_funcion_externa)
        {
            if ($ejecucion_funcion_externa["parametros"] == $parametros_funcion)
            {
                $log->info("Función externa con los mismos parámetros ya ejecutada, se devuelve el resultado anterior".
                    " (parámetros función: '".json_encode($parametros_funcion)."'");
                return ($ejecucion_funcion_externa["resultado"]);
            }
        }

        // Se recupera el directorio de ficheros temporales del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);

        // Se guardan los parámetros en un fichero (para evitar el límite de caracteres en línea de comando)
        $nombre_fichero_parametros_funcion = "ejecucion_funcion_".dame_timestamp_ahora_milisegundos_utc().".json";
        $ruta_fichero_parametros_funcion_json = $directorio_absoluto_ficheros_temporales_usuario."/".$nombre_fichero_parametros_funcion;
        $res_escritura_fichero_parametros_funcion_json = file_put_contents($ruta_fichero_parametros_funcion_json, json_encode($parametros_funcion));
        if ($res_escritura_fichero_parametros_funcion_json === false)
        {
            throw new Exception("No se ha podido escribir el fichero de parámetros de función (json) (ruta: '".$ruta_fichero_parametros_funcion_json."')");
        }

        // Llamada a la función
        try
        {
            // Nota: El nombre del fichero está codificados en base64 (para evitar problemas con caracteres 'extraños')
            $cadena_ruta_fichero_parametros_funcion_json_base64 = base64_encode($ruta_fichero_parametros_funcion_json);

            // Hay que añadir comillas en los parámetros porque si no no se recupera la cadena correctamente
            // (si no hay espacios se eliminan los corchetes y las comas)
            $comando = 'python3 "'.$ruta.'ejecuta_funcion.py"'.' "'.$cadena_ruta_fichero_parametros_funcion_json_base64.'"';
            if (($usuario_administrador == true) && (dame_sistema_operativo_windows() == false))
            {
                $comando = "sudo ".$comando;
            }
            $log->debug("Comando: '".$comando."'");
            $resultado_json = ejecuta_comando($comando);
            $log->info("Fin de ejecución de función externa (parámetros función: '".$cadena_log_parametros_funcion_externa."'");

            // Nota: No hay finally en PHP (a partir de 5.5 sí lo hay)
            // Se borra el fichero de parámetros de función json
            // (Nota desarrollo: Comentar para no borrar el fichero de parámetros json)
            unlink($ruta_fichero_parametros_funcion_json);
        }
        catch (Exception $e)
        {
            // Se borra el fichero de parámetros de función json
            // (Nota desarrollo: Comentar para no borrar el fichero de parámetros json)
            unlink($ruta_fichero_parametros_funcion_json);

            // Se relanza la excepción
            throw $e;
        }

        // Resultado
        $resultado = json_decode($resultado_json, true);
        if ($resultado["res"] == "OK")
        {
            // Se guarda en globales el resultado de la función externa
            $ejecucion_funcion_externa = array(
                "parametros" => $parametros_funcion,
                "resultado" => $resultado);
            array_push($GLOBALS["ejecuciones_funciones_externas"], $ejecucion_funcion_externa);

            // Si hay ido bien se devuelve el resultado de la función
            return ($resultado);
        }
        else
        {
            $cadena_log_resultado_funcion_externa = dame_cadena_log_resultado_funcion_externa($resultado_json);
            if ($resultado["res"] == "ERROR")
            {
                // Si hay error en el resultado de la función:
                // - Se lanza excepción si no hay error controlado
                // - Si hay error controlado lo procesa el 'script' que ha llamado a esta función
                if (array_key_exists("error", $resultado) == true)
                {
                    if ($resultado["error"] == "")
                    {
                        throw new Exception("Error no controlado en la ejecución de la función (".
                            "parámetros: '".$cadena_log_parametros_funcion_externa."', resultado: '".$cadena_log_resultado_funcion_externa."')");
                    }
                    else
                    {
                        return ($resultado);
                    }
                }
                else
                {
                    throw new Exception("Error en la ejecución de la función (".
                        "parámetros: '".$cadena_log_parametros_funcion_externa."', resultado: '".$cadena_log_resultado_funcion_externa."')");
                }
            }
            else
            {
                // Si en el resultado no hay "OK" ni "ERROR"
                throw new Exception("Resultado desconocido en la ejecución de la función (".
                    "parámetros: '".json_encode($parametros_funcion)."', resultado: '".$cadena_log_resultado_funcion_externa."')");
            }
        }
    }


    // Devuelve la cadena de log de los parámetros de funciones externas
    function dame_cadena_log_parametros_funcion_externa($parametros_funcion)
    {
        $nombre_funcion = $parametros_funcion["nombre"];
        switch ($nombre_funcion)
        {
            case NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_TARIFA_CONSUMOS:
            {
                $infos_consumos = $parametros_funcion["infos_consumos"];
                $parametros_funcion["infos_consumos"] = "...";
                $cadena_log_parametros_funcion = json_encode($parametros_funcion);
                $parametros_funcion["infos_consumos"] = $infos_consumos;
                break;
            }
            default:
            {
                $cadena_log_parametros_funcion = json_encode($parametros_funcion);
                break;
            }
        }
        if (strlen($cadena_log_parametros_funcion) > NUMERO_MAXIMO_CARACTERES_PARAMETROS_EJECUCION_FUNCION_LOG)
        {
            $cadena_log_parametros_funcion = substr($cadena_log_parametros_funcion, 0, NUMERO_MAXIMO_CARACTERES_PARAMETROS_EJECUCION_FUNCION_LOG)." ...";
        }
        return ($cadena_log_parametros_funcion);
    }


    // Devuelve la cadena de log del resultado de funciones externas
    function dame_cadena_log_resultado_funcion_externa($resultado_json)
    {
        $cadena_log_resultado_funcion = $resultado_json;
        if (strlen($cadena_log_resultado_funcion) > NUMERO_MAXIMO_CARACTERES_RESULTADO_EJECUCION_FUNCION_LOG)
        {
            $cadena_log_resultado_funcion = substr($cadena_log_resultado_funcion, 0, NUMERO_MAXIMO_CARACTERES_RESULTADO_EJECUCION_FUNCION_LOG)." ...";
        }
        return ($cadena_log_resultado_funcion);
    }


    // Elimina los ficheros del directorio especificado
    function elimina_ficheros_directorio($ruta_directorio, $eliminar_directorio)
    {
        // No se hace borrado recursivo (si existen subdirectorios).
        // Devuelve el número de ficheros eliminados o -1 en caso de no poder abrir el directorio .
        if (file_exists($ruta_directorio) == true)
        {
            $directorio = opendir($ruta_directorio);
            if ($directorio == false)
            {
                $ret = -1;
            }
            else
            {
                $ret = 0;
                while ($nombre_fichero = readdir($directorio))
                {
                    $ruta_fichero = $ruta_directorio."/".$nombre_fichero;
                    if (is_file($ruta_fichero) == true)
                    {
                        if (unlink($ruta_fichero) == true)
                        {
                            $ret += 1;
                        }
                        else
                        {
                            $ret = -1;
                            break;
                        }
                    }
                }

                if ($ret != -1)
                {
                    if ($eliminar_directorio == true)
                    {
                        if (rmdir($ruta_directorio) == false)
                        {
                            $ret = -1;
                        }
                    }
                }
            }
        }

        return ($ret);
    }


    // Función que devuelve si el sistema operativo es Windows
    function dame_sistema_operativo_windows()
    {
        $sistema_operativo_windows = (strncasecmp(PHP_OS, 'WIN', 3) == 0);
        return ($sistema_operativo_windows);
    }


    // Guarda la acción inicial en la sesión (con sus parámetros correspondientes)
    function guarda_accion_inicial_sesion($accion_inicial, $parametros_accion_inicial)
    {
        $_SESSION["accion_inicial"] = $accion_inicial;
        $_SESSION["parametros_accion_inicial"] = $parametros_accion_inicial;
    }


    // Devuelve la acción inicial y la borra de la sesión
    function dame_elimina_accion_inicial_sesion()
    {
        if (isset($_SESSION["accion_inicial"]))
        {
            $accion_inicial = $_SESSION["accion_inicial"];
            $parametros_accion_inicial = $_SESSION["parametros_accion_inicial"];
            unset($_SESSION["accion_inicial"]);
        }
        else
        {
            $accion_inicial = NULL;
            $parametros_accion_inicial = NULL;
        }

        $res = array(
            "accion_inicial" => $accion_inicial,
            "parametros_accion_inicial" => $parametros_accion_inicial);
        return ($res);
    }


    // Devuelve la dirección IP del cliente
    // (https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php)
    function dame_direccion_ip_cliente()
    {
        $direccion_ip = NULL;
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $direccion_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $direccion_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_X_FORWARDED']))
        {
            $direccion_ip = $_SERVER['HTTP_X_FORWARDED'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        {
            $direccion_ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_FORWARDED_FOR']))
        {
            $direccion_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['HTTP_FORWARDED']))
        {
            $direccion_ip = $_SERVER['HTTP_FORWARDED'];
        }
        if (($direccion_ip === NULL) && isset($_SERVER['REMOTE_ADDR']))
        {
            $direccion_ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($direccion_ip === NULL)
        {
            $direccion_ip = 'UNKNOWN';
        }
        return ($direccion_ip);
    }


    // Devuelve la UTL añadiendo el protocolo HTTP (si es necesario)
    function dame_url_http($url)
    {
        if ((substr($url, 0, 7) == "http://") || (substr($url, 0, 8) == "https://"))
        {
            $url_http = $url;
        }
        else
        {
            $url_http = "http://".$url;
        }
        return ($url_http);
    }
?>