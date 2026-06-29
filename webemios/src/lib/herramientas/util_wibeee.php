<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');


    // Constantes de wibeee

    // Tipos de dato de Wibeee
    define("TIPO_DATO_WIBEEE_VRMS_FASE_1", "vrms_fase_1");
    define("TIPO_DATO_WIBEEE_VRMS_FASE_2", "vrms_fase_2");
    define("TIPO_DATO_WIBEEE_VRMS_FASE_3", "vrms_fase_3");
    define("TIPO_DATO_WIBEEE_VRMS_TOTAL", "vrms_total");
    define("TIPO_DATO_WIBEEE_IRMS_FASE_1", "irms_fase_1");
    define("TIPO_DATO_WIBEEE_IRMS_FASE_2", "irms_fase_2");
    define("TIPO_DATO_WIBEEE_IRMS_FASE_3", "irms_fase_3");
    define("TIPO_DATO_WIBEEE_IRMS_TOTAL", "irms_total");
    define("TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_1", "potencia_aparente_fase_1");
    define("TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_2", "potencia_aparente_fase_2");
    define("TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_3", "potencia_aparente_fase_3");
    define("TIPO_DATO_WIBEEE_POTENCIA_APARENTE_TOTAL", "potencia_aparente_total");
    define("TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_1", "potencia_activa_fase_1");
    define("TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_2", "potencia_activa_fase_2");
    define("TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_3", "potencia_activa_fase_3");
    define("TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_TOTAL", "potencia_activa_total");
    define("TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_1", "potencia_reactiva_fase_1");
    define("TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_2", "potencia_reactiva_fase_2");
    define("TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_3", "potencia_reactiva_fase_3");
    define("TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_TOTAL", "potencia_reactiva_total");
    define("TIPO_DATO_WIBEEE_FRECUENCIA_FASE_1", "frecuencia_fase_1");
    define("TIPO_DATO_WIBEEE_FRECUENCIA_FASE_2", "frecuencia_fase_2");
    define("TIPO_DATO_WIBEEE_FRECUENCIA_FASE_3", "frecuencia_fase_3");
    define("TIPO_DATO_WIBEEE_FRECUENCIA_TOTAL", "frecuencia_total");
    define("TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_1", "factor_potencia_fase_1");
    define("TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_2", "factor_potencia_fase_2");
    define("TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_3", "factor_potencia_fase_3");
    define("TIPO_DATO_WIBEEE_FACTOR_POTENCIA_TOTAL", "factor_potencia_total");
    define("TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_1", "energia_activa_fase_1");
    define("TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_2", "energia_activa_fase_2");
    define("TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_3", "energia_activa_fase_3");
    define("TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_TOTAL", "energia_activa_total");
    define("TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_1", "energia_reactiva_fase_1");
    define("TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_2", "energia_reactiva_fase_2");
    define("TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_3", "energia_reactiva_fase_3");
    define("TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_TOTAL", "energia_reactiva_total");

    // Número máximo de segundos de retraso de datos Wibeee
    define("NUMERO_MAXIMO_SEGUNDOS_RETRASO_DATOS_WIBEEE", 3600);

    // Causas de envio de valores de los sensores
    define("CAUSA_ENVIO_VALORES_RECEIVER_WIBEEE", "RECEIVER_WIBEEE");


    // Procesa una petición 'receiver' de un dispositivo Wibeee
    function procesa_peticion_receiver_wibeee($parametros)
    {
        $log = dame_log();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Dirección MAC del WiBeee origen
        $direccion_mac_wibeee_sin_separadores = $parametros["mac"];

        // Nota: Se establece el timestamp a ahora (por si el WiBeee no está sincronizado) (se ignora el timestamp del Wibeee)
        // $timestamp_utc_wibeee = $parametros["time"];
        $timestamp_utc_wibeee = dame_timestamp_ahora_milisegundos_utc() / 1000;
        $fecha_hora_datos_wibeee_utc = dame_fecha_hora_actual_utc();
        $fecha_hora_datos_wibeee_utc->setTimestamp($timestamp_utc_wibeee);

        // Se recorren los sensores y se notifican los valores correspondientes a los datos recibidos
        $consulta_sensores = "
            SELECT
                parametros_tipo,
                hora_ultimos_valores,
                frecuencia_envio
            FROM sensores
            WHERE
                (tipo = '".TIPO_SENSOR_EXTERNO."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".CLASE_SENSOR_EXTERNO_WIBEEE."')";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            // Se recuperan los parámetros de tipo del sensor
            $cadena_parametros_tipo = $fila_sensor["parametros_tipo"];
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
            $direccion_mac_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
            $tipo_dato_wibeee_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];

            // Si coincide la MAC del sensor, se recupera el valor correspondiente y se envía por MQTT
            $direccion_mac_sensor_sin_separadores = str_replace(":", "", $direccion_mac_sensor);
            if (strtolower($direccion_mac_sensor_sin_separadores) == strtolower($direccion_mac_wibeee_sin_separadores))
            {
                // Si hay hora de últimos valores se comprueba si la fecha de recepción es posterior
                $cadena_hora_ultimos_valores = $fila_sensor["hora_ultimos_valores"];
                if ($cadena_hora_ultimos_valores !== NULL)
                {
                    $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor["hora_ultimos_valores"];
                    $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $diferencia_fechas_ultimos_valores = $fecha_hora_ultimos_valores_utc->diff($fecha_hora_datos_wibeee_utc);
                    if ($diferencia_fechas_ultimos_valores->invert == 1)
                    {
                        $log->warn("Timestamp anterior a la hora de últimos valores (parámetros: '".json_encode($parametros)."'");
                        return;
                    }
                    $segundos_diferencia_fechas_ultimos_valores = dame_segundos_intervalo_tiempo($diferencia_fechas_ultimos_valores);
                    if ($segundos_diferencia_fechas_ultimos_valores == 0)
                    {
                        $log->warn("Timestamp igual a la hora de últimos valores (parámetros: '".json_encode($parametros)."'");
                        return;
                    }

                    // Nota: Si se ha recibido un valor con intervalo menor a la mitad de la frecuencia de envío se ignora
                    // (en ocasiones no se reprograman los wibeees y se envían datos con frecuencia de 1 minuto)
                    $frecuencia_envio = $fila_sensor["frecuencia_envio"];
                    if ($frecuencia_envio > 0)
                    {
                        if ($segundos_diferencia_fechas_ultimos_valores < ($frecuencia_envio / 2))
                        {
                            // Nota: No se añade para no llenar el log
                            // $log->warn("Se ignoran los datos (frecuencia de envío demasiado alta) (parámetros: '".json_encode($parametros)."'");
                            return;
                        }
                    }
                }

                // Se notifican los valores recibidos
                $nombre_variable_wibeee = dame_nombre_variable_tipo_dato_wibeee($tipo_dato_wibeee_sensor);
                $valor_variable_wibeee = $parametros[$nombre_variable_wibeee];
                notifica_valores_recibidos_sensor_externo(
                    $id_sensor_externo,
                    $timestamp_utc_wibeee,
                    CAUSA_ENVIO_VALORES_RECEIVER_WIBEEE,
                    TIPO_VALORES_SENSOR_PUNTUALES,
                    NULL,
                    NULL,
                    array($valor_variable_wibeee),
                    true);
            }
        }
    }


    // Devuelve el nombre de la variable correspondiente al tipo de dato Wibeee
    function dame_nombre_variable_tipo_dato_wibeee($tipo_dato)
    {
        switch ($tipo_dato)
        {
            case TIPO_DATO_WIBEEE_VRMS_FASE_1:
            {
                $nombre_variable = "v1";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_FASE_2:
            {
                $nombre_variable = "v2";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_FASE_3:
            {
                $nombre_variable = "v3";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_TOTAL:
            {
                $nombre_variable = "vt";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_1:
            {
                $nombre_variable = "i1";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_2:
            {
                $nombre_variable = "i2";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_3:
            {
                $nombre_variable = "i3";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_TOTAL:
            {
                $nombre_variable = "it";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_1:
            {
                $nombre_variable = "p1";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_2:
            {
                $nombre_variable = "p2";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_3:
            {
                $nombre_variable = "p3";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_TOTAL:
            {
                $nombre_variable = "pt";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_1:
            {
                $nombre_variable = "a1";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_2:
            {
                $nombre_variable = "a2";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_3:
            {
                $nombre_variable = "a3";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_TOTAL:
            {
                $nombre_variable = "at";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_1:
            {
                $nombre_variable = "r1";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_2:
            {
                $nombre_variable = "r2";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_3:
            {
                $nombre_variable = "r3";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_TOTAL:
            {
                $nombre_variable = "rt";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_1:
            {
                $nombre_variable = "q1";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_2:
            {
                $nombre_variable = "q2";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_3:
            {
                $nombre_variable = "q3";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_TOTAL:
            {
                $nombre_variable = "qt";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_1:
            {
                $nombre_variable = "f1";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_2:
            {
                $nombre_variable = "f2";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_3:
            {
                $nombre_variable = "f3";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_TOTAL:
            {
                $nombre_variable = "ft";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_1:
            {
                $nombre_variable = "e1";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_2:
            {
                $nombre_variable = "e2";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_3:
            {
                $nombre_variable = "e3";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_TOTAL:
            {
                $nombre_variable = "et";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_1:
            {
                $nombre_variable = "o1";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_2:
            {
                $nombre_variable = "o2";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_3:
            {
                $nombre_variable = "o3";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_TOTAL:
            {
                $nombre_variable = "ot";
                break;
            }
            default:
            {
                throw new Exception("Tipo de dato desconocido: '".$tipo_dato."'");
            }
        }
        return ($nombre_variable);
    }


    //
    // Funciones auxiliares
    //


    function dame_lista_tipos_dato_wibeee($tipo_dato_seleccionado)
    {
        $lista_tipos_dato = dame_lista_valores(
            array(
                array(TIPO_DATO_WIBEEE_VRMS_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_VRMS_FASE_1)),
                array(TIPO_DATO_WIBEEE_VRMS_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_VRMS_FASE_2)),
                array(TIPO_DATO_WIBEEE_VRMS_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_VRMS_FASE_3)),
                array(TIPO_DATO_WIBEEE_VRMS_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_VRMS_TOTAL)),
                array(TIPO_DATO_WIBEEE_IRMS_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_IRMS_FASE_1)),
                array(TIPO_DATO_WIBEEE_IRMS_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_IRMS_FASE_2)),
                array(TIPO_DATO_WIBEEE_IRMS_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_IRMS_FASE_3)),
                array(TIPO_DATO_WIBEEE_IRMS_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_IRMS_TOTAL)),
                array(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_1)),
                array(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_2)),
                array(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_3)),
                array(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_APARENTE_TOTAL)),
                array(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_1)),
                array(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_2)),
                array(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_3)),
                array(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_TOTAL)),
                array(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_1)),
                array(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_2)),
                array(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_3)),
                array(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_TOTAL)),
                array(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_1)),
                array(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_2)),
                array(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_FRECUENCIA_FASE_3)),
                array(TIPO_DATO_WIBEEE_FRECUENCIA_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_FRECUENCIA_TOTAL)),
                array(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_1)),
                array(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_2)),
                array(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_3)),
                array(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_TOTAL)),
                array(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_1, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_1)),
                array(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_2, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_2)),
                array(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_3, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_3)),
                array(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_TOTAL, dame_descripcion_tipo_dato_wibeee(TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_TOTAL))),
            array($tipo_dato_seleccionado));
        return ($lista_tipos_dato);
    }


    function dame_descripcion_tipo_dato_wibeee($tipo_dato)
    {
        switch ($tipo_dato)
        {
            case TIPO_DATO_WIBEEE_VRMS_FASE_1:
            {
                $descripcion = "VRMS (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_FASE_2:
            {
                $descripcion = "VRMS (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_FASE_3:
            {
                $descripcion = "VRMS (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_VRMS_TOTAL:
            {
                $descripcion = "VRMS (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_1:
            {
                $descripcion = "IRMS (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_2:
            {
                $descripcion = "IRMS (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_FASE_3:
            {
                $descripcion = "IRMS (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_IRMS_TOTAL:
            {
                $descripcion = "IRMS (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_1:
            {
                $descripcion = "Potencia aparente (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_2:
            {
                $descripcion = "Potencia aparente (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_FASE_3:
            {
                $descripcion = "Potencia aparente (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_APARENTE_TOTAL:
            {
                $descripcion = "Potencia aparente (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_1:
            {
                $descripcion = "Potencia activa (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_2:
            {
                $descripcion = "Potencia activa (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_FASE_3:
            {
                $descripcion = "Potencia activa (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_ACTIVA_TOTAL:
            {
                $descripcion = "Potencia activa (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_1:
            {
                $descripcion = "Potencia reactiva (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_2:
            {
                $descripcion = "Potencia reactiva (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_FASE_3:
            {
                $descripcion = "Potencia reactiva (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_POTENCIA_REACTIVA_TOTAL:
            {
                $descripcion = "Potencia reactiva (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_1:
            {
                $descripcion = "Frecuencia (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_2:
            {
                $descripcion = "Frecuencia (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_FASE_3:
            {
                $descripcion = "Frecuencia (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_FRECUENCIA_TOTAL:
            {
                $descripcion = "Frecuencia (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_1:
            {
                $descripcion = "Factor de potencia (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_2:
            {
                $descripcion = "Factor de potencia (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_FASE_3:
            {
                $descripcion = "Factor de potencia (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_FACTOR_POTENCIA_TOTAL:
            {
                $descripcion = "Factor de potencia (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_1:
            {
                $descripcion = "Energía activa (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_2:
            {
                $descripcion = "Energía activa (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_FASE_3:
            {
                $descripcion = "Energía activa (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_ACTIVA_TOTAL:
            {
                $descripcion = "Energía activa (total)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_1:
            {
                $descripcion = "Energía reactiva (fase 1)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_2:
            {
                $descripcion = "Energía reactiva (fase 2)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_FASE_3:
            {
                $descripcion = "Energía reactiva (fase 3)";
                break;
            }
            case TIPO_DATO_WIBEEE_ENERGIA_REACTIVA_TOTAL:
            {
                $descripcion = "Energía reactiva (total)";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }
?>
