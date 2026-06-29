<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    $id_sensor = $_POST["id_sensor"];
    if ($id_sensor == ID_NINGUNO)
    {
        $tipo_valores_sensor = NULL;
        $cadena_fecha_valores_sensor_jqplot_local = "";
        $cadena_hora_valores_sensor_local = "";
    }
    else
    {
        // Información del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor["hora_ultimos_valores"];

        // Se establece la hora actual
        $fecha_hora_envio_valores_manuales_utc = dame_fecha_hora_actual_utc();
        $horas_incrementos_envio_valores_manuales = 0;

        // Se calcula la fecha del siguiente valor o incremento a enviar
        if ($cadena_fecha_hora_ultimos_valores_base_datos_utc !== NULL)
        {
            switch ($tipo_valores_sensor)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $info_ultimos_valores_sensor = dame_info_ultimos_valores_sensor($fila_sensor);
                    $fecha_hora_ultimos_valores_incrementos_utc = $info_ultimo_valor_sensor["fecha_hora_ultimos_valores_utc"];
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $info_ultimos_incrementos_sensor = dame_info_ultimos_incrementos_sensor($fila_sensor);
                    $fecha_hora_ultimos_valores_incrementos_utc = $info_ultimos_incrementos_sensor["fecha_hora_fin_ultimos_incrementos_utc"];
                    $horas_incrementos_envio_valores_manuales = round($info_ultimos_incrementos_sensor["segundos_ultimos_incrementos"] / 3600, 2);
                    break;
                }
            }
            if ($fecha_hora_envio_valores_manuales_utc <= $fecha_hora_ultimos_valores_incrementos_utc)
            {
                $fecha_hora_envio_valores_manuales_utc = clone $fecha_hora_ultimos_valores_incrementos_utc;
                $fecha_hora_envio_valores_manuales_utc->add(new DateInterval('PT1S'));
            }
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_envio_valores_manuales_local_utc = convierte_fecha_a_cadena($fecha_hora_envio_valores_manuales_utc, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_envio_valores_manuales_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_envio_valores_manuales_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
        $cadena_fecha_envio_valores_manuales_date_javascript_local = convierte_formato_fecha($cadena_fecha_hora_envio_valores_manuales_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_DATE_JAVASCRIPT);
        $cadena_hora_envio_valores_manuales_local = convierte_formato_fecha($cadena_fecha_hora_envio_valores_manuales_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_HORA_SIN_SEGUNDOS);
    }

    print(json_encode(array(
        "res" => "OK",
        "tipo_valores_sensor" => $tipo_valores_sensor,
        "fecha_envio_valores_manuales" => $cadena_fecha_envio_valores_manuales_date_javascript_local,
        "hora_envio_valores_manuales" => $cadena_hora_envio_valores_manuales_local,
        "horas_incrementos_envio_valores_manuales" => $horas_incrementos_envio_valores_manuales))
    );
?>

