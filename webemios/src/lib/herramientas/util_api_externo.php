<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_informes_facturas_electricidad_Espanya.php');


    // Constantes de peticiones y respuestas del API (HTTP)

    // Parámetros de peticiones API de autenticación
    define("PARAMETRO_PETICION_API_USUARIO", "usuario");
    define("PARAMETRO_PETICION_API_CONTRASENYA", "contrasenya");

    // Parámetros de peticiones API de funciones
    define("PARAMETRO_PETICION_API_ID_RED", "id_red");
    define("PARAMETRO_PETICION_API_ID_SENSOR", "id_sensor");
    define("PARAMETRO_PETICION_API_IDS_SENSORES", "ids_sensores");
    define("PARAMETRO_PETICION_API_FECHA_HORA_INICIO", "fecha_hora_inicio");
    define("PARAMETRO_PETICION_API_FECHA_HORA_FIN", "fecha_hora_fin");
    define("PARAMETRO_PETICION_API_INTERVALO_VALORES", "intervalo_valores");
    define("PARAMETRO_PETICION_API_ID_TARIFA", "id_tarifa");
    define("PARAMETRO_PETICION_API_IDS_TARIFAS", "ids_tarifas");

    // Parámetros de respuestas de peticiones de API generales
    define("PARAMETRO_RESPUESTA_API_RESULTADO", "resultado");
    define("PARAMETRO_RESPUESTA_API_ID_ERROR", "id_error");

    // Formato de fecha y hora de peticiones del API
    define("FORMATO_FECHA_HORA_PETICIONES_API_HTTP", "d-m-Y_H:i:s");

    // Parámetros de respuestas de peticiones de funciones
    define("PARAMETRO_RESPUESTA_API_VALORES_TIEMPO_REAL", "valores_tiempo_real");
    define("PARAMETRO_RESPUESTA_API_VALORES_CUARTOHORARIOS", "valores_cuartohorarios");
    define("PARAMETRO_RESPUESTA_API_VALORES_HORARIOS", "valores_horarios");
    define("PARAMETRO_RESPUESTA_API_FECHA_HORA", "fecha_hora");
    define("PARAMETRO_RESPUESTA_API_CAMPOS", "campos");
    define("PARAMETRO_RESPUESTA_API_TUPLA_VALORES", "tupla_valores");
    define("PARAMETRO_RESPUESTA_API_NUMERO_TUPLAS_VALORES", "numero_tuplas_valores");

    // Valores de resultado de petición de API
    define("RESULTADO_API_OK", "OK");
    define("RESULTADO_API_ERROR", "ERROR");

    // Códigos de error de peticiones API generales
    define("ID_ERROR_PETICION_API_ERROR_INTERNO", "error_interno");
    define("ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS", "parametros_incorrectos");
    define("ID_ERROR_PETICION_API_CREDENCIALES_INCORRECTAS", "credenciales_incorrectas");
    define("ID_ERROR_PETICION_API_NO_PERMITIDA", "no_permitida");
    define("ID_ERROR_PETICION_API_LIMITE_PETICIONES_SUPERADO", "limite_peticiones_superado");
    define("ID_ERROR_PETICION_API_FUNCION_NO_IMPLEMENTADA", "funcion_no_implementada");

    // Códigos de error de peticiones API específicas
    define("ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO", "id_red_sensor_incorrecto");
    define("ID_ERROR_PETICION_API_LIMITE_VALORES_SUPERADO", "limite_valores_superado");
    define("ID_ERROR_PETICION_API_LIMITE_DIAS_SUPERADO", "limite_dias_superado");
    define("ID_ERROR_PETICION_API_FECHAS_INCORRECTAS", "fechas_incorrectas");
    define("ID_ERROR_PETICION_API_INTERVALO_VALORES_INCORRECTO", "intervalo_valores_incorrecto");
    define("ID_ERROR_PETICION_API_LIMITE_SENSORES_SUPERADO", "limite_sensores_superado");
    define("ID_ERROR_PETICION_API_CLASE_SENSOR_INCORRECTA", "clase_sensor_incorrecta");
    define("ID_ERROR_PETICION_API_SENSOR_SIN_TARIFA", "sensor_sin_tarifa");
    define("ID_ERROR_PETICION_API_LIMITE_TARIFAS_SUPERADO", "limite_tarifas_superado");

    // Número máximo de peticiones por intervalo
    define("NUMERO_MAXIMO_PETICIONES_API_HTTP_MINUTO", 60);
    define("NUMERO_MAXIMO_PETICIONES_API_HTTP_HORA", 720);
    define("NUMERO_MAXIMO_PETICIONES_API_HTTP_DIA", 3600);

    // Límite de parámetros de API
    define("LIMITE_NUMERO_SENSORES_PETICION_VALORES_ACTUALES_SENSORES_API", 20);
    define("LIMITE_NUMERO_TARIFAS_PETICION_COSTES_CONSUMOS_SENSOR_TARIFAS_API", 5);

    // Límites de días en peticiones de rango de fechas
    define("LIMITE_DIAS_PETICION_VALORES_RANGO_FECHAS_INTERVALO_TIEMPO_REAL_API", 31);
    define("LIMITE_DIAS_PETICION_VALORES_RANGO_FECHAS_INTERVALO_NO_TIEMPO_REAL_API", 366);

    // Límite de número de valores en una petición de rango de fechas
    define("LIMITE_NUMERO_VALORES_PETICION_VALORES_RANGO_FECHAS_API", 50000);


    //
    // Funciones de peticiones de API (externo)
    //


    // Devuelve los valores actuales de un sensor
    function dame_valores_actuales_sensor_api($parametros, $realizar_acciones_comprobaciones_iniciales_usuario_api)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $id_sensor = $parametros[PARAMETRO_PETICION_API_ID_SENSOR];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($id_sensor == ""))
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_valores_sensor);
            }

            // Acciones y comprobaciones iniciales del usuario
            if ($realizar_acciones_comprobaciones_iniciales_usuario_api == true)
            {
                // Se borran los parámetros de sesión anteriores
                unset($_SESSION["id_red"]);
                unset($_SESSION["parametros_modulo_sensores"]);

                $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros);
                if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
                {
                    return ($res_comprobaciones_iniciales_usuario);
                }
            }

            // Se comprueba si el sensor es visible por el usuario
            $sensor_visible = dame_sensor_visible_usuario_interno($id_sensor);
            if ($sensor_visible == false)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO);
                return ($res_valores_sensor);
            }

            // Se recuperan las filas del sensor del sensor y se devuelven los valores formateados
            $fila_sensor = dame_fila_sensor($id_sensor);
            $res_valores_sensor = dame_resultado_valores_actuales_sensor_api($fila_sensor);
            return ($res_valores_sensor);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_valores_sensor = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_valores_sensor);
        }
    }


    // Devuelve los valores actuales de varios sensores
    function dame_valores_actuales_sensores_api($parametros)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $cadena_ids_sensores = $parametros[PARAMETRO_PETICION_API_IDS_SENSORES];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($cadena_ids_sensores == ""))
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_valores_sensor);
            }
            $ids_sensores = explode(",", $cadena_ids_sensores);

            // Se recuperan los valores actuales de cada uno de los sensores
            $numero_sensores = count($ids_sensores);
            $valores_actuales_sensores = array();
            if ($numero_sensores <= LIMITE_NUMERO_SENSORES_PETICION_VALORES_ACTUALES_SENSORES_API)
            {
                $realizar_acciones_comprobaciones_iniciales_usuario_api = true;
                foreach ($ids_sensores as $id_sensor)
                {
                    $parametros["id_sensor"] = $id_sensor;
                    $res_valores_sensor = dame_valores_actuales_sensor_api($parametros, $realizar_acciones_comprobaciones_iniciales_usuario_api);
                    if ($realizar_acciones_comprobaciones_iniciales_usuario_api == true)
                    {
                        $realizar_acciones_comprobaciones_iniciales_usuario_api = false;
                    }
                    if ($res_valores_sensor[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
                    {
                        if (($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_CREDENCIALES_INCORRECTAS) ||
                            ($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_NO_PERMITIDA) ||
                            ($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_LIMITE_PETICIONES_SUPERADO))
                        {
                            $res_valores_sensores = array(
                                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                                PARAMETRO_RESPUESTA_API_ID_ERROR => $res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR]);
                            return ($res_valores_sensores);
                        }
                    }
                    $valores_actuales_sensores[$id_sensor] = $res_valores_sensor;
                }
                $res_valores_sensores = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                    "valores_actuales_sensores" => $valores_actuales_sensores);
            }
            else
            {
                $res_valores_sensores = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_SENSORES_SUPERADO);
            }
            return ($res_valores_sensores);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_valores_sensores = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_valores_sensores);
        }
    }


    // Devuelve los valores dentro de un rango de fechas de un sensor
    function dame_valores_rango_fechas_sensor_api($parametros, $realizar_acciones_comprobaciones_iniciales_usuario_api)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $id_sensor = $parametros[PARAMETRO_PETICION_API_ID_SENSOR];
            $cadena_fecha_hora_inicio_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_INICIO];
            $cadena_fecha_hora_fin_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_FIN];
            $intervalo_valores = $parametros[PARAMETRO_PETICION_API_INTERVALO_VALORES];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($id_sensor == "") ||
                ($cadena_fecha_hora_inicio_peticiones_api_utc == "") ||
                ($cadena_fecha_hora_fin_peticiones_api_utc == "") ||
                ($intervalo_valores == ""))
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_valores_sensor);
            }

            // Acciones y comprobaciones iniciales del usuario
            if ($realizar_acciones_comprobaciones_iniciales_usuario_api == true)
            {
                // Se borran los parámetros de sesión anteriores
                unset($_SESSION["id_red"]);
                unset($_SESSION["parametros_modulo_sensores"]);

                $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros);
                if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
                {
                    return ($res_comprobaciones_iniciales_usuario);
                }
            }

            // Se comprueba si el sensor es visible por el usuario
            $sensor_visible = dame_sensor_visible_usuario_interno($id_sensor);
            if ($sensor_visible == false)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO);
                return ($res_valores_sensor);
            }

            // Comprobación de fechas y límite de fechas
            $fechas_inicio_fin_correctas = dame_fechas_inicio_fin_api_correctas(
                $cadena_fecha_hora_inicio_peticiones_api_utc,
                $cadena_fecha_hora_fin_peticiones_api_utc);
            if ($fechas_inicio_fin_correctas == false)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_FECHAS_INCORRECTAS);
                return ($res_valores_sensor);
            }
            $limite_fechas_peticion_superado = dame_limite_dias_rango_fechas_superado_api(
                $cadena_fecha_hora_inicio_peticiones_api_utc,
                $cadena_fecha_hora_fin_peticiones_api_utc,
                $intervalo_valores);
            if ($limite_fechas_peticion_superado == true)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_DIAS_SUPERADO);
                return ($res_valores_sensor);
            }

            // Se recupera la información del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);
            $nombre_sensor = $fila_sensor["nombre"];
            $clase_sensor = $fila_sensor["clase"];

            // Comprobación de intervalo de valores
            $intervalo_correcto = dame_intervalo_valores_api_correcto($intervalo_valores, $clase_sensor);
            if ($intervalo_correcto == false)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_INTERVALO_VALORES_INCORRECTO);
                return ($res_valores_sensor);
            }

            // Conversión de fechas
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_inicio_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_inicio_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_fin_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

            // Se recuperan los valores del del sensor y se comprueba que no superen el límite de valores
            $parametros_filas_valores_sensor = array(
                "id_sensor" => $id_sensor,
                "id_ratio" => ID_NINGUNO,
                "clase_sensor" => $clase_sensor,
                "nombre_sensor" => $nombre_sensor,
                "intervalo_valores" => $intervalo_valores,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
            $filas_valores_sensor = dame_filas_valores_sensor($parametros_filas_valores_sensor);
            if (count($filas_valores_sensor) > LIMITE_NUMERO_VALORES_PETICION_VALORES_RANGO_FECHAS_API)
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_VALORES_SUPERADO);
                return ($res_valores_sensor);
            }
            else
            {
                $resultado_valores_rango_fechas_sensor = dame_resultado_valores_rango_fechas_sensor_api(
                    $clase_sensor,
                    $intervalo_valores,
                    $filas_valores_sensor);
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                    "valores_rango_fechas_sensor" => $resultado_valores_rango_fechas_sensor);
                return ($res_valores_sensor);
            }
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_valores_sensor = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_valores_sensor);
        }
    }


    // Devuelve los valores dentro de un rango de fechas de varios sensores
    function dame_valores_rango_fechas_sensores_api($parametros)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $cadena_ids_sensores = $parametros[PARAMETRO_PETICION_API_IDS_SENSORES];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($cadena_ids_sensores == ""))
            {
                $res_valores_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_valores_sensor);
            }
            $ids_sensores = explode(",", $cadena_ids_sensores);

            // Se recuperan los valores actuales de cada uno de los sensores
            $numero_sensores = count($ids_sensores);
            $valores_rango_fechas_sensores = array();
            if ($numero_sensores <= LIMITE_NUMERO_SENSORES_PETICION_VALORES_ACTUALES_SENSORES_API)
            {
                $realizar_acciones_comprobaciones_iniciales_usuario_api = true;
                foreach ($ids_sensores as $id_sensor)
                {
                    $parametros["id_sensor"] = $id_sensor;
                    $res_valores_sensor = dame_valores_rango_fechas_sensor_api($parametros, $realizar_acciones_comprobaciones_iniciales_usuario_api);
                    if ($realizar_acciones_comprobaciones_iniciales_usuario_api == true)
                    {
                        $realizar_acciones_comprobaciones_iniciales_usuario_api = false;
                    }
                    if ($res_valores_sensor[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
                    {
                        if (($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_CREDENCIALES_INCORRECTAS) ||
                            ($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_NO_PERMITIDA) ||
                            ($res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR] == ID_ERROR_PETICION_API_LIMITE_PETICIONES_SUPERADO))
                        {
                            $res_valores_sensores = array(
                                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                                PARAMETRO_RESPUESTA_API_ID_ERROR => $res_valores_sensor[PARAMETRO_RESPUESTA_API_ID_ERROR]);
                            return ($res_valores_sensores);
                        }
                    }
                    $valores_rango_fechas_sensores[$id_sensor] = $res_valores_sensor;
                }
                $res_valores_sensores = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                    "valores_rango_fechas_sensores" => $valores_rango_fechas_sensores);
            }
            else
            {
                $res_valores_sensores = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_SENSORES_SUPERADO);
            }
            return ($res_valores_sensores);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_valores = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_valores);
        }
    }


    // Devuelve la simulación de factura de un sensor y una tarifa
    function dame_simulacion_factura_sensor_tarifa_api($parametros)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $id_sensor = $parametros[PARAMETRO_PETICION_API_ID_SENSOR];
            $id_tarifa = $parametros[PARAMETRO_PETICION_API_ID_TARIFA];
            $cadena_fecha_hora_inicio_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_INICIO];
            $cadena_fecha_hora_fin_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_FIN];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($id_sensor == "") ||
                ($id_tarifa == "") ||
                ($cadena_fecha_hora_inicio_peticiones_api_utc == "") ||
                ($cadena_fecha_hora_fin_peticiones_api_utc == ""))
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_simulacion_factura_sensor);
            }

            // Se borran los parámetros de sesión anteriores
            unset($_SESSION["id_red"]);
            unset($_SESSION["parametros_modulo_sensores"]);

            // Acciones y comprobaciones iniciales del usuario
            $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros);
            if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
            {
                return ($res_comprobaciones_iniciales_usuario);
            }

            // Se comprueba si el sensor es visible por el usuario
            $sensor_visible = dame_sensor_visible_usuario_interno($id_sensor);
            if ($sensor_visible == false)
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO);
                return ($res_simulacion_factura_sensor);
            }

            // Comprobación de fechas
            $fechas_inicio_fin_correctas = dame_fechas_inicio_fin_api_correctas(
                $cadena_fecha_hora_inicio_peticiones_api_utc,
                $cadena_fecha_hora_fin_peticiones_api_utc);
            if ($fechas_inicio_fin_correctas == false)
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_FECHAS_INCORRECTAS);
                return ($res_simulacion_factura_sensor);
            }

            // Se recupera la información del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);
            $nombre_sensor = $fila_sensor["nombre"];
            $clase_sensor = $fila_sensor["clase"];

            // Comprobación de clase
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_GAS:
                case CLASE_SENSOR_AGUA:
                {
                    $clase_sensor_correcta = true;
                    break;
                }
                default:
                {
                    $clase_sensor_correcta = false;
                    break;
                }
            }
            if ($clase_sensor_correcta == false)
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_CLASE_SENSOR_INCORRECTA);
                return ($res_simulacion_factura_sensor);
            }

            // Formato de fechas de funciones
            $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, FORMATO_FECHA_HORA_FUNCIONES);
            $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, FORMATO_FECHA_HORA_FUNCIONES);

            // Se recupera el identificador de tarifa del sensor (si no se ha pasado como parámetro)
            if ($id_tarifa == ID_NINGUNO)
            {
                $id_tarifa = dame_id_tarifa_id_sensor($id_sensor);
            }

            // Se comprueba si hay tarifa seleccionada
            if ($id_tarifa == ID_NINGUNO)
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_SENSOR_SIN_TARIFA);
                return ($res_simulacion_factura_sensor);
            }

            // Medición y país de tarifas
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $medicion = MEDICION_ELECTRICIDAD;
                    $pais_tarifas = $_SESSION["pais_tarifas_electricas"];
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $medicion = MEDICION_GAS;
                    $pais_tarifas = $_SESSION["pais_tarifas_gas"];
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $medicion = MEDICION_AGUA;
                    $pais_tarifas = $_SESSION["pais_tarifas_agua"];
                    break;
                }
            }

            // Se recupera la simulación de tarifa (con llamada a función externa)

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA,
                    "medicion" => $medicion,
                    "pais_tarifas" => $pais_tarifas,
                    "nombre_sensor" => $nombre_sensor,
                    "id_red" => $id_red,
                    "id_tarifa" => $id_tarifa,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                    "exclusion_fechas" => ""
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            $res_simulacion_factura_sensor = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                "simulacion_factura_sensor_tarifa" => $resultado_funcion_externa);
            return ($res_simulacion_factura_sensor);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_simulacion_factura_sensor = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_simulacion_factura_sensor);
        }
    }


    // Devuelve los costes del consumo actual y de las tarifas especificadas de un sensor
    function dame_costes_consumo_sensor_tarifas_api($parametros)
    {
        try
        {
            // Parámetros
            $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $id_sensor = $parametros[PARAMETRO_PETICION_API_ID_SENSOR];
            $cadena_ids_tarifas = $parametros[PARAMETRO_PETICION_API_IDS_TARIFAS];
            $cadena_fecha_hora_inicio_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_INICIO];
            $cadena_fecha_hora_fin_peticiones_api_utc = $parametros[PARAMETRO_PETICION_API_FECHA_HORA_FIN];
            if (($id_usuario == "") ||
                ($contrasenya == "") ||
                ($id_red == "") ||
                ($id_sensor == "") ||
                ($cadena_ids_tarifas == "") ||
                ($cadena_fecha_hora_inicio_peticiones_api_utc == "") ||
                ($cadena_fecha_hora_fin_peticiones_api_utc == ""))
            {
                $res_simulacion_factura_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_simulacion_factura_sensor);
            }
            $ids_tarifas = explode(",", $cadena_ids_tarifas);

            // Se borran los parámetros de sesión anteriores
            unset($_SESSION["id_red"]);
            unset($_SESSION["parametros_modulo_sensores"]);

            // Acciones y comprobaciones iniciales del usuario
            $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros);
            if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
            {
                return ($res_comprobaciones_iniciales_usuario);
            }

            // Se comprueba si el sensor es visible por el usuario
            $sensor_visible = dame_sensor_visible_usuario_interno($id_sensor);
            if ($sensor_visible == false)
            {
                $res_costes_consumo_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO);
                return ($res_costes_consumo_sensor);
            }

            // Comprobación de fechas
            $fechas_inicio_fin_correctas = dame_fechas_inicio_fin_api_correctas(
                $cadena_fecha_hora_inicio_peticiones_api_utc,
                $cadena_fecha_hora_fin_peticiones_api_utc);
            if ($fechas_inicio_fin_correctas == false)
            {
                $res_costes_consumo_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_FECHAS_INCORRECTAS);
                return ($res_costes_consumo_sensor);
            }

            // Se recupera la información del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);
            $nombre_sensor = $fila_sensor["nombre"];
            $clase_sensor = $fila_sensor["clase"];

            // Comprobación de clase
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_GAS:
                case CLASE_SENSOR_AGUA:
                {
                    $clase_sensor_correcta = true;
                    break;
                }
                default:
                {
                    $clase_sensor_correcta = false;
                    break;
                }
            }
            if ($clase_sensor_correcta == false)
            {
                $res_costes_consumo_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_CLASE_SENSOR_INCORRECTA);
                return ($res_costes_consumo_sensor);
            }

            // Formato de fechas de funciones
            $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, FORMATO_FECHA_HORA_FUNCIONES);
            $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, FORMATO_FECHA_HORA_FUNCIONES);

            // Medición y país de tarifas
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $medicion = MEDICION_ELECTRICIDAD;
                    $pais_tarifas = $_SESSION["pais_tarifas_electricas"];
                    break;
                }
                case CLASE_SENSOR_GAS:
                {
                    $medicion = MEDICION_GAS;
                    $pais_tarifas = $_SESSION["pais_tarifas_gas"];
                    break;
                }
                case CLASE_SENSOR_AGUA:
                {
                    $medicion = MEDICION_AGUA;
                    $pais_tarifas = $_SESSION["pais_tarifas_agua"];
                    break;
                }
            }

            // Se añade el id de tarifa 'NINGUNO' para recuperar el coste actual
            array_unshift($ids_tarifas, ID_NINGUNO);

            // Se recuperan los costes de consumo de las tarifas (con llamadas a funciones externas)
            $numero_tarifas = count($ids_tarifas);
            if ($numero_tarifas <= LIMITE_NUMERO_TARIFAS_PETICION_COSTES_CONSUMOS_SENSOR_TARIFAS_API + 1)
            {
                $costes_consumo_sensor_tarifas = array();
                foreach ($ids_tarifas as $id_tarifa)
                {
                    // Parámetros de la función a llamar
                    $parametros_funcion_externa =
                        array(
                            "llamante" => "web_emios",
                            "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_SENSOR_TARIFA,
                            "medicion" => $medicion,
                            "pais_tarifas" => $pais_tarifas,
                            "nombre_sensor" => $nombre_sensor,
                            "id_red" => $id_red,
                            "id_tarifa" => $id_tarifa,
                            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                            "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc
                        );

                    // Llamada a función 'externa'
                    $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
                    $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

                    // Se guardan los costes de consumos de la tarifa
                    $costes_consumo_sensor_tarifas[$id_tarifa] = $resultado_funcion_externa;
                }
                $res_costes_consumo_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                    "costes_consumo_sensor_tarifas" => $costes_consumo_sensor_tarifas);
            }
            else
            {
                $res_costes_consumo_sensor = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_TARIFAS_SUPERADO);
            }
            return ($res_costes_consumo_sensor);
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$excepcion."'");

            $res_costes_consumos_sensor = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO);
            return ($res_costes_consumos_sensor);
        }
    }
    
    // Devuelve las redes a las que tiene acceso un usuario
    function dame_redes_usuario($parametros)
    {
        try
        {
            // Parámetros
            $usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            if ($usuario == "") {
                $res_redes_usuario = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_redes_usuario);
            }

            // Acciones y comprobaciones iniciales del usuario
            $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros, false);
            if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
            {
                return ($res_comprobaciones_iniciales_usuario);
            }

            $bd_red = BaseDatosRed::dame_base_datos();
            
            $consulta_rol_usuario = "SELECT perfil FROM usuarios WHERE id = '$usuario'";
            $res_rol_usuario = $bd_red->ejecuta_consulta($consulta_rol_usuario);
            $res_sensores = $bd_red->ejecuta_consulta($consulta_rol_usuario);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_rol_usuario."'");
            }
            $fila_resultado_rol_usuario = $res_rol_usuario->dame_siguiente_fila();

            // Obtener todos los datos si es superadmin
            if ($fila_resultado_rol_usuario["perfil"] == 'superadmin') {
                $consulta_redes = "SELECT id, nombre FROM redes";
            } else {
                $consulta_redes = "SELECT id, nombre FROM redes WHERE id IN (SELECT red FROM redes_usuarios WHERE usuario = '" . $usuario . "')";
            }

            $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            if ($res_redes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_redes."'");
            }
            
            $filas_resultado_redes = array();
            while($fila_redes = $res_redes->dame_siguiente_fila()){
                array_push($filas_resultado_redes, $fila_redes);
            }

            $res_redes_usuario = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                "redes_usuario" => $filas_resultado_redes);

            return $res_redes_usuario;
        }
        catch(Exception $ex){
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$ex."'");

            $res_redes_usuario = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO
            );
            return ($res_redes_usuario);
        }
    }

    
    
    // Devuelve los sensores a los que tiene acceso un usuario para una determinada red
    function dame_sensores($parametros)
    {
        try
        {
            // Parámetros
            $usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            if ($usuario == "" or $id_red == "") {
                $res_redes_usuario = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_PARAMETROS_INCORRECTOS);
                return ($res_redes_usuario);
            }

            // Acciones y comprobaciones iniciales del usuario
            $res_comprobaciones_iniciales_usuario = realiza_acciones_comprobaciones_iniciales_usuario_api($parametros);
            if ($res_comprobaciones_iniciales_usuario[PARAMETRO_RESPUESTA_API_RESULTADO] == RESULTADO_API_ERROR)
            {
                return ($res_comprobaciones_iniciales_usuario);
            }

            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_rol_usuario = "SELECT perfil FROM usuarios WHERE id = '$usuario'";
            $res_rol_usuario = $bd_red->ejecuta_consulta($consulta_rol_usuario);
            $res_sensores = $bd_red->ejecuta_consulta($consulta_rol_usuario);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_rol_usuario."'");
            }
            $fila_resultado_rol_usuario = $res_rol_usuario->dame_siguiente_fila();

            // Obtener todos los sensores de esa red si es superadmin
            if ($fila_resultado_rol_usuario["perfil"] == 'superadmin') {
                $consulta_sensores = "SELECT id, nombre, tipo, clase FROM sensores where red = $id_red";
            } else {
                $consulta_sensores = "SELECT id, nombre, tipo, clase FROM sensores s join redes_usuarios ru on s.red=ru.red where s.red = $id_red and ru.usuario = '$usuario'";
            }

            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores."'");
            }
            
            $filas_resultado_sensores = array();
            while($fila_sensores = $res_sensores->dame_siguiente_fila()){
                array_push($filas_resultado_sensores, $fila_sensores);
            }

            $res_sensores = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
                "sensores" => $filas_resultado_sensores);

            return $res_sensores;
        }
        catch(Exception $ex){
            $log = dame_log();
            $log->error("Parámetros: '".json_encode($parametros)."'");
            $log->error("Excepción: '".$ex."'");

            $res_sensores = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ERROR_INTERNO
            );
            return ($res_sensores);
        }
    }

    
    //
    // Funciones de usuario y peticiones de API generales
    //


    function realiza_acciones_comprobaciones_iniciales_usuario_api($parametros, $comprueba_red = true)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_usuario = $parametros[PARAMETRO_PETICION_API_USUARIO];
        $contrasenya = $parametros[PARAMETRO_PETICION_API_CONTRASENYA];

        // Se recupera la información del usuario
        $consulta_usuario = "
            SELECT *
            FROM usuarios
            WHERE
                id = '".$bd_red->_($id_usuario)."'";
        $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
        if ($res_usuario == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_usuario."'");
        }
        if ($res_usuario->dame_numero_filas() == 0)
        {
            $res = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_CREDENCIALES_INCORRECTAS);
            return ($res);
        }
        else
        {
            $fila_usuario = $res_usuario->dame_siguiente_fila();
        }

        // Se comprueba si el usuario tiene permisos de API HTTP y si hay superado el límite de peticiones (temporales)
        $peticion_api_permitida = dame_peticion_api_permitida($fila_usuario);
        if ($peticion_api_permitida === false)
        {
            $res = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_NO_PERMITIDA);
            return ($res);
        }

        $limite_peticiones_api_superado = actualiza_limite_peticiones_api($id_usuario);
        if ($limite_peticiones_api_superado === true)
        {
            $res = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_LIMITE_PETICIONES_SUPERADO);
            return ($res);
        }

        // Comprobación de contraseña del API HTTP
        $contrasenya_correcta_api = dame_contrasenya_correcta_api($contrasenya, $fila_usuario);
        if ($contrasenya_correcta_api == false)
        {
            $res = array(
                PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_CREDENCIALES_INCORRECTAS);
            return ($res);
        }

        // Inicio de sesión del usuario
        if($comprueba_red) {
            $id_red = $parametros[PARAMETRO_PETICION_API_ID_RED];
            $existe_red = dame_existe_red($id_red);
            if ($existe_red == false)
            {
                $res_valores = array(
                    PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_ERROR,
                    PARAMETRO_RESPUESTA_API_ID_ERROR => ID_ERROR_PETICION_API_ID_RED_SENSOR_INCORRECTO);
                return ($res_valores);
            }
            inicializa_sesion_usuario_api($id_usuario, $fila_usuario, $id_red);
        }

        $res = array(PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK);
        return ($res);
    }


    function dame_contrasenya_correcta_api($contrasenya, $fila_usuario)
    {
        // Se verifica la contraseña del api del usuario
        if ($fila_usuario["contrasenya_api_http"] == $contrasenya)
        {
            return (true);
        }
    }


    function inicializa_sesion_usuario_api($id_usuario, $fila_usuario, $id_red)
    {
        // Usuario interno y zona horaria a UTC
        $_SESSION["usuario_interno"] = USUARIO_INTERNO_API_HTTP;
        $_SESSION["zona_horaria"] = ZONA_HORARIA_UTC;
        $_SESSION["id_red"] = $id_red;

        // Se cargan los parámetros locales de la red
        establece_parametros_locales_red($_SESSION["id_red"], NULL);

        // Se carga la información del usuario interno
        carga_informacion_usuario_interno($id_usuario, $fila_usuario);
    }


    function dame_peticion_api_permitida($fila_usuario)
    {
        $api_http = $fila_usuario["api_http"];
        if ($api_http == VALOR_SI)
        {
            return (true);
        }
        else
        {
            return (false);
        }
    }


    function actualiza_limite_peticiones_api($id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Consulta de la tabla de peticiones del usuario
        $consulta_peticiones_api_http = "
            SELECT *
            FROM peticiones_api_http
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_peticiones_api_http = $bd_red->ejecuta_consulta($consulta_peticiones_api_http);
        if ($res_peticiones_api_http == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_peticiones_api_http."'");
        }

        // Se añade o inserta la tabla de peticiones API
        $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
        $cadena_fecha_hora_actual_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_actual_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        if ($res_peticiones_api_http->dame_numero_filas() == 0)
        {
            inserta_peticion_tabla_peticiones_api($id_usuario, $cadena_fecha_hora_actual_base_datos_utc);
            $limite_peticiones_superado = false;
        }
        else
        {
            // Información de las peticiones del usuario
            $fila_peticion_api_http = $res_peticiones_api_http->dame_siguiente_fila();
            $numero_peticiones_ultimo_minuto = $fila_peticion_api_http["numero_peticiones_ultimo_minuto"];
            $numero_peticiones_ultima_hora = $fila_peticion_api_http["numero_peticiones_ultima_hora"];
            $numero_peticiones_ultimo_dia = $fila_peticion_api_http["numero_peticiones_ultimo_dia"];
            $cadena_fecha_hora_ultima_peticion_base_datos_utc = $fila_peticion_api_http["hora"];
            $fecha_hora_ultima_peticion_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultima_peticion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);

            // Se comprueba si se ha superado el número de peticiones
            $resultado_comprobacion_numero_peticiones = comprueba_numero_peticiones_api(
                $fecha_hora_actual_utc,
                $fecha_hora_ultima_peticion_utc,
                $numero_peticiones_ultimo_minuto,
                $numero_peticiones_ultima_hora,
                $numero_peticiones_ultimo_dia);

            // Se actualiza la tabla de peticiones a partir del resultado de la comprobación y los valores actuales
            actualiza_tabla_peticiones_api(
                $resultado_comprobacion_numero_peticiones,
                $id_usuario,
                $cadena_fecha_hora_actual_base_datos_utc);
            $limite_peticiones_superado = $resultado_comprobacion_numero_peticiones["limite_peticiones_superado"];
        }

        // Se devuelve si se ha superado el límite de peticiones
        return ($limite_peticiones_superado);
    }


    function comprueba_numero_peticiones_api(
        $fecha_hora_actual_utc,
        $fecha_hora_ultima_peticion_utc,
        $numero_peticiones_ultimo_minuto,
        $numero_peticiones_ultima_hora,
        $numero_peticiones_ultimo_dia)
    {
        // Para la comprobación del cambio de periodo se crean tres fechas a partir de la fecha de última petición almacenada en la base de datos:
        // - Una con los segundos a cero para comprobar si están en el mismo minuto
        // - Una con los segundos y minutos a cero para comprobar si están en la misma hora
        // - Una con los segundos, los minutos y las horas a cero para comprobar su están en el mismo día
        $fecha_ultima_peticion_minutos_utc = clone $fecha_hora_ultima_peticion_utc;
        $fecha_ultima_peticion_minutos_utc->setTime($fecha_hora_ultima_peticion_utc->format("H"), $fecha_hora_ultima_peticion_utc->format("i"), 0);
        $fecha_ultima_peticion_horas_utc = clone $fecha_hora_ultima_peticion_utc;
        $fecha_ultima_peticion_horas_utc->setTime($fecha_hora_ultima_peticion_utc->format("H"), 0, 0);
        $fecha_ultima_peticion_dias_utc = clone $fecha_hora_ultima_peticion_utc;
        $fecha_ultima_peticion_dias_utc->setTime(0, 0, 0);

        // Se calculan las diferencias entre la fecha actual y las calculadas anteriormente y se comparan con el intervalo de cada periodo
        $diferencia_segundos_ultima_peticion_minutos = $fecha_hora_actual_utc->getTimestamp() - $fecha_ultima_peticion_minutos_utc->getTimestamp();
        $diferencia_segundos_ultima_peticion_horas = $fecha_hora_actual_utc->getTimestamp() - $fecha_ultima_peticion_horas_utc->getTimestamp();
        $diferencia_segundos_ultima_peticion_dias = $fecha_hora_actual_utc->getTimestamp() - $fecha_ultima_peticion_dias_utc->getTimestamp();

        // Flag de límite de peticiones superado
        $limite_peticiones_superado = false;

        // Comprobación de cambio de minuto
        if ($diferencia_segundos_ultima_peticion_minutos > 60)
        {
            $numero_peticiones_ultimo_minuto = 1;
        }
        else
        {
            if ($numero_peticiones_ultimo_minuto >= NUMERO_MAXIMO_PETICIONES_API_HTTP_MINUTO)
            {
                $limite_peticiones_superado = true;
            }
            $numero_peticiones_ultimo_minuto++;
        }

        // Comprobación de cambio de hora
        if ($diferencia_segundos_ultima_peticion_horas > 3600)
        {
            $numero_peticiones_ultima_hora = 1;
        }
        else
        {
            if ($numero_peticiones_ultima_hora >= NUMERO_MAXIMO_PETICIONES_API_HTTP_HORA)
            {
                $limite_peticiones_superado = true;
            }
            $numero_peticiones_ultima_hora++;
        }

        // Comprobación de cambio de día
        if ($diferencia_segundos_ultima_peticion_dias > 86400)
        {
            $numero_peticiones_ultimo_dia = 1;
        }
        else
        {
            if ($numero_peticiones_ultimo_dia >= NUMERO_MAXIMO_PETICIONES_API_HTTP_DIA)
            {
                $limite_peticiones_superado = true;
            }
            $numero_peticiones_ultimo_dia++;
        }

        // Se devuelve el resultado de la comprobación
        $resultado = array(
            "limite_peticiones_superado" => $limite_peticiones_superado,
            "numero_peticiones_ultimo_minuto" => $numero_peticiones_ultimo_minuto,
            "numero_peticiones_ultima_hora" => $numero_peticiones_ultima_hora,
            "numero_peticiones_ultimo_dia" => $numero_peticiones_ultimo_dia);
        return ($resultado);
    }


    function inserta_peticion_tabla_peticiones_api($id_usuario, $cadena_fecha_hora_actual_base_datos_utc)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $numero_peticiones_ultimo_minuto = 1;
        $numero_peticiones_ultima_hora = 1;
        $numero_peticiones_ultimo_dia = 1;
        $operacion_insercion_peticiones_api_http = "
            INSERT INTO peticiones_api_http (
                usuario,
                hora,
                numero_peticiones_ultimo_minuto,
                numero_peticiones_ultima_hora,
                numero_peticiones_ultimo_dia
            ) VALUES (
                '".$bd_red->_($id_usuario)."',
                '".$bd_red->_($cadena_fecha_hora_actual_base_datos_utc)."',
                '".$bd_red->_($numero_peticiones_ultimo_minuto)."',
                '".$bd_red->_($numero_peticiones_ultima_hora)."',
                '".$bd_red->_($numero_peticiones_ultimo_dia)."'
            )";
        $res_insercion_peticiones_api_http = $bd_red->ejecuta_operacion($operacion_insercion_peticiones_api_http);
        if ($res_insercion_peticiones_api_http == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_peticiones_api_http."'");
        }
    }


    function actualiza_tabla_peticiones_api($resultado_comprobacion_numero_peticiones, $id_usuario, $cadena_fecha_hora_actual_base_datos_utc)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Si no se ha superado el límite se actualiza la hora y el número de peticiones de cada tramo,
        // de lo contrario se actualizan los números de peticiones sólo si alguno ha cambiado (sin modificar la hora de última petición)
        $operacion_actualizacion_peticiones_api_http = "
            UPDATE peticiones_api_http
            SET
                usuario = '".$bd_red->_($id_usuario)."',
                hora = '".$bd_red->_($cadena_fecha_hora_actual_base_datos_utc)."',
                numero_peticiones_ultimo_minuto = '".$bd_red->_($resultado_comprobacion_numero_peticiones["numero_peticiones_ultimo_minuto"])."',
                numero_peticiones_ultima_hora = '".$bd_red->_($resultado_comprobacion_numero_peticiones["numero_peticiones_ultima_hora"])."',
                numero_peticiones_ultimo_dia = '".$bd_red->_($resultado_comprobacion_numero_peticiones["numero_peticiones_ultimo_dia"])."'
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_actualizacion_peticiones_api_http = $bd_red->ejecuta_operacion($operacion_actualizacion_peticiones_api_http);
        if ($res_actualizacion_peticiones_api_http == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_peticiones_api_http."'");
        }
    }


    //
    // Funciones de petición de valores actuales de sensor
    //


    function dame_resultado_valores_actuales_sensor_api($fila_sensor)
    {
        // Se recupera la información de la fila del sensor
        $clase = $fila_sensor["clase"];
        $hora_ultimos_valores = $fila_sensor["hora_ultimos_valores"];
        $hora_ultimos_valores_clase_horas = $fila_sensor["hora_ultimos_valores_clase_horas"];
        $hora_ultimos_valores_clase_cuartoshora = $fila_sensor["hora_ultimos_valores_clase_cuartoshora"];
        $ultimos_valores = $fila_sensor["ultimos_valores"];
        $ultimos_valores_clase_horas = $fila_sensor["ultimos_valores_clase_horas"];
        $ultimos_valores_clase_cuartoshora = $fila_sensor["ultimos_valores_clase_cuartoshora"];

        // Valores en tiempo real
        if ($hora_ultimos_valores != NULL)
        {
            $hora_ultimos_valores = convierte_formato_fecha(
                $fila_sensor["hora_ultimos_valores"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_PETICIONES_API_HTTP);
            $resultado_campos_valores_tiempo_real = dame_campos_tupla_valores_tiempo_real_api($clase, $ultimos_valores);
            $resultados_tiempo_real = array(
                "campos" => $resultado_campos_valores_tiempo_real["campos"],
                "hora" => $hora_ultimos_valores,
                "tupla_valores" => $resultado_campos_valores_tiempo_real["tupla_valores"]
            );
        }
        else
        {
            $resultados_tiempo_real = "ND";
        }

        // Valores de clase cuartohorarios
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
        if ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true)
        {
            if ($hora_ultimos_valores_clase_cuartoshora != NULL)
            {
                $hora_ultimos_valores_clase_cuartoshora = convierte_formato_fecha(
                    $fila_sensor["hora_ultimos_valores_clase_cuartoshora"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_PETICIONES_API_HTTP);
                $resultado_campos_valores_cuartohorarios = dame_campos_tupla_valores_clase_api($clase, $ultimos_valores_clase_cuartoshora, INTERVALO_VALORES_CUARTOHORA);
                $resultados_cuartohorarios = array(
                    "campos" => $resultado_campos_valores_cuartohorarios["campos"],
                    "hora" => $hora_ultimos_valores_clase_cuartoshora,
                    "tupla_valores" => $resultado_campos_valores_cuartohorarios["tupla_valores"]
                );
            }
            else
            {
                $resultados_cuartohorarios = "ND";
            }
        }

        // Valores de clase horarios
        if ($hora_ultimos_valores_clase_horas != NULL)
        {
            $hora_ultimos_valores_clase_horas = convierte_formato_fecha(
                $fila_sensor["hora_ultimos_valores_clase_horas"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_PETICIONES_API_HTTP);
            $resultado_campos_valores_horarios = dame_campos_tupla_valores_clase_api($clase, $ultimos_valores_clase_horas, INTERVALO_VALORES_HORA);
            $resultados_horarios = array(
                "campos" => $resultado_campos_valores_horarios["campos"],
                "hora" => $hora_ultimos_valores_clase_horas,
                "tupla_valores" => $resultado_campos_valores_horarios["tupla_valores"]
            );
        }
        else
        {
            $resultados_horarios = "ND";
        }

        // Resultado
        $valores_actuales_sensor = array(
            "valores_tiempo_real" => $resultados_tiempo_real);
        if ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true)
        {
            $valores_actuales_sensor["valores_cuartohorarios"] = $resultados_cuartohorarios;
        }
        $valores_actuales_sensor["valores_horarios"] = $resultados_horarios;
        $resultado_valores_sensor = array(
            PARAMETRO_RESPUESTA_API_RESULTADO => RESULTADO_API_OK,
           "valores_actuales_sensor" => $valores_actuales_sensor);
        return ($resultado_valores_sensor);
    }


    function dame_campos_tupla_valores_tiempo_real_api($clase, $cadena_valores)
    {
        $campos = dame_campos_sensor_intervalo_valores_api($clase, INTERVALO_VALORES_TIEMPO_REAL);

        $cadenas_valores_puntuales_incrementales = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $cadena_valores);
        $cadena_valores_puntuales = $cadenas_valores_puntuales_incrementales[0];
        $cadena_valores_incrementales = $cadenas_valores_puntuales_incrementales[1];
        $segundos_valores_incrementales = $cadenas_valores_puntuales_incrementales[2];

        $valores_puntuales = explode(SEPARADOR_VALORES_SENSOR, $cadena_valores_puntuales);
        $valores_incrementales = explode(SEPARADOR_VALORES_SENSOR, $cadena_valores_incrementales);
        $valores_puntuales_incrementales = array_merge($valores_puntuales, $valores_incrementales);

        $tupla_valores = array();
        for ($i = 0; $i < count($campos); $i++)
        {
            $valor = $valores_puntuales_incrementales[$i];
            if (($valor == NULL) || ($valor == "?"))
            {
                $valor = "";
            }
            array_push($tupla_valores, $valor);
        }

        $res = array(
            "campos" => $campos,
            "tupla_valores" => $tupla_valores);
        if (count($valores_incrementales) > 0)
        {
            $res["segundos_valores_incrementales"] = $segundos_valores_incrementales;
        }
        return ($res);
    }


    function dame_campos_tupla_valores_clase_api($clase, $cadena_valores, $intervalo_valores)
    {
        $campos = dame_campos_sensor_intervalo_valores_api($clase, $intervalo_valores);

        $valores_clase = explode(SEPARADOR_VALORES_SENSOR, $cadena_valores);

        $tupla_valores = array();
        for ($i = 0; $i < count($campos); $i++)
        {
            $valor = $valores_clase[$i];
            if (($valor == NULL) || ($valor == "?"))
            {
                $valor = "";
            }
            array_push($tupla_valores, $valor);
        }

        $res = array(
            "campos" => $campos,
            "tupla_valores" => $tupla_valores);
        return ($res);
    }


    //
    // Funciones de petición de valores en un rango de fechas de un sensor
    //


    function dame_resultado_valores_rango_fechas_sensor_api($clase_sensor, $intervalo_valores, $filas_valores_sensor)
    {
        $campos = dame_campos_sensor_intervalo_valores_api($clase_sensor, $intervalo_valores);

        $numero_filas_valores_sensor = count($filas_valores_sensor);
        if ($numero_filas_valores_sensor > 0)
        {
            $tuplas_horas_valores = array();
            foreach ($filas_valores_sensor as $fila_valores_sensor)
            {
                $hora = convierte_formato_fecha($fila_valores_sensor["fecha_hora"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_PETICIONES_API_HTTP);
                $tupla_valores = array();
                foreach ($fila_valores_sensor as $campo => $valor)
                {
                    if (in_array($campo, $campos) == true)
                    {
                        if (($valor == NULL) OR ($valor == "?"))
                        {
                            $valor = "";
                        }
                        array_push($tupla_valores, $valor);
                    }
                }
                $tupla_hora_valor = array(
                    "hora" => $hora,
                    "tupla_valores" => $tupla_valores);
                array_push($tuplas_horas_valores, $tupla_hora_valor);
            }
            $numero_tuplas_valores = $numero_filas_valores_sensor;
        }
        else
        {
            $numero_tuplas_valores = 0;
            $tuplas_horas_valores = "ND";
        }
        $resultado_rango_valores_sensor = array(
            "campos" => $campos,
            "numero_tuplas_valores" => $numero_tuplas_valores,
            "tuplas_horas_valores" => $tuplas_horas_valores);
        return ($resultado_rango_valores_sensor);
    }


    function dame_limite_dias_rango_fechas_superado_api($cadena_fecha_hora_inicio_peticiones_api_utc, $cadena_fecha_hora_fin_peticiones_api_utc, $intervalo_valores)
    {
        $fecha_hora_inicio_utc = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, ZONA_HORARIA_UTC);
        $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, ZONA_HORARIA_UTC);
        $diferencia_fechas = $fecha_hora_fin_utc->diff($fecha_hora_inicio_utc);
        $dias_diferencia = $diferencia_fechas->days;
        if ((($intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL) && ($dias_diferencia > LIMITE_DIAS_PETICION_VALORES_RANGO_FECHAS_INTERVALO_TIEMPO_REAL_API)) ||
            ($dias_diferencia > LIMITE_DIAS_PETICION_VALORES_RANGO_FECHAS_INTERVALO_NO_TIEMPO_REAL_API))
        {
            return (true);
        }
        else
        {
            return (false);
        }
    }


    function dame_fechas_inicio_fin_api_correctas($cadena_fecha_hora_inicio_peticiones_api_utc, $cadena_fecha_hora_fin_peticiones_api_utc)
    {
        $fecha_hora_inicio_correcta = dame_fecha_hora_api_correcta($cadena_fecha_hora_inicio_peticiones_api_utc);
        $fecha_hora_fin_correcta = dame_fecha_hora_api_correcta($cadena_fecha_hora_fin_peticiones_api_utc);
        if (($fecha_hora_inicio_correcta == false) OR ($fecha_hora_fin_correcta == false))
        {
            return (false);
        }
        else
        {
            $fecha_hora_inicio_utc = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, ZONA_HORARIA_UTC);
            $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, ZONA_HORARIA_UTC);
            if ($fecha_hora_inicio_utc > $fecha_hora_fin_utc)
            {
                return (false);
            }
            else
            {
                return (true);
            }
        }
    }


    function dame_fecha_hora_api_correcta($cadena_fecha_hora_peticiones_api_utc)
    {
        try
        {
            convierte_cadena_a_fecha($cadena_fecha_hora_peticiones_api_utc, FORMATO_FECHA_HORA_PETICIONES_API_HTTP, ZONA_HORARIA_UTC);
            $fecha_hora_api_correcta = true;
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("Excepción: '".$excepcion."'");

            $fecha_hora_api_correcta = false;
        }
        return ($fecha_hora_api_correcta);
    }


    function dame_intervalo_valores_api_correcto($intervalo_valores, $clase_sensor)
    {
        $intervalo_valores_api_correcto = false;
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                $intervalo_valores_api_correcto = true;
                break;
            }
            case INTERVALO_VALORES_HORA:
            case INTERVALO_VALORES_DIA:
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                if ($caracteristicas_clase_sensor["procesado_valores"] == true)
                {
                    $intervalo_valores_api_correcto = true;
                }
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                if ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true)
                {
                    $intervalo_valores_api_correcto = true;
                }
                break;
            }
        }
        return ($intervalo_valores_api_correcto);
    }


    //
    // Funciones de obtención de campos del sensor
    //


    function dame_campos_sensor_intervalo_valores_api($clase, $intervalo_valores)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                $campos = dame_campos_clase_sensor($clase);
                break;
            }
            default:
            {
                $campos = dame_campos_horarios_clase_sensor($clase);
                break;
            }
        }
        return ($campos);
    }
?>