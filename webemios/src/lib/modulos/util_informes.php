<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores_externos.php');


    //
    // Funciones de tiempos
    //


    // Devuelve los segundos máximos entre valores de una gráfica
	function dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor)
	{
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $segundos_maximos_entre_valores_grafica = NULL;
                break;
            }
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $segundos_maximos_entre_valores_grafica = NULL;
                if ($id_sensor !== NULL)
                {
                    $fila_sensor = dame_fila_sensor($id_sensor);
                    $tipo_sensor = $fila_sensor["tipo"];
                    switch ($tipo_sensor)
                    {
                        case TIPO_SENSOR_REAL:
                        {
                            $frecuencia_envio = $fila_sensor["frecuencia_envio"];
                            if ($frecuencia_envio > 0)
                            {
                                $segundos_maximos_entre_valores_grafica = ($frecuencia_envio * FACTOR_FRECUENCIA_ENVIO_TIMEOUTS_SENSORES_REALES) - 1;
                            }
                            break;
                        }
                        case TIPO_SENSOR_EXTERNO:
                        {
                            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_tipo"]);
                            $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                            if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_FICHEROS_CSV)
                            {
                                $opciones_generales_sensor_externo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES]);
                                $tipo_valores_sensor_externo = $opciones_generales_sensor_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_TIPO_VALORES];
                                if ($tipo_valores_sensor_externo == TIPO_VALORES_SENSOR_INCREMENTALES)
                                {
                                    $segundos_incrementos_sensor_externo = $opciones_generales_sensor_externo[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_SEGUNDOS_INCREMENTOS];
                                    $segundos_maximos_entre_valores_grafica = $segundos_incrementos_sensor_externo;
                                }
                            }
                            else
                            {
                                $frecuencia_envio = $fila_sensor["frecuencia_envio"];
                                if ($frecuencia_envio > 0)
                                {
                                    $segundos_maximos_entre_valores_grafica = ($frecuencia_envio * FACTOR_FRECUENCIA_ENVIO_TIMEOUTS_SENSORES_EXTERNOS) - 1;
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                $segundos_maximos_entre_valores_grafica = 900;
                break;
            }
            case INTERVALO_VALORES_HORA:
            {
                $segundos_maximos_entre_valores_grafica = 3600;
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $segundos_maximos_entre_valores_grafica = (3600 * 24) + (3600 * 12);
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $segundos_maximos_entre_valores_grafica = (3600 * 24 * 7) + (3600 * 12);
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $segundos_maximos_entre_valores_grafica = (3600 * 24 * 31) + (3600 * 12);
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
            }
        }
        return ($segundos_maximos_entre_valores_grafica);
    }


    // Devuelve los segundos correspondientes a un intervalo de valores
	function dame_segundos_intervalo_valores($intervalo_valores)
	{
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $segundos_intervalo_valores = 1;
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                $segundos_intervalo_valores = 900;
                break;
            }
            case INTERVALO_VALORES_HORA:
            {
                $segundos_intervalo_valores = 3600;
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $segundos_intervalo_valores = 24 * 3600;
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $segundos_intervalo_valores = 7 * 24 * 3600;
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $segundos_intervalo_valores = 30 * 24 * 3600;
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
            }
        }
        return ($segundos_intervalo_valores);
    }


    // Devuelve la fecha de inicio a partir de la fecha de inicio y fin
    // y los parámetros de periodo de tiempo
    function dame_fecha_inicio_parametros_periodo_tiempo(
        $cadena_fecha_hora_inicio_local_local,
        $cadena_fecha_hora_fin_local_local,
        $parametros_periodo_tiempo)
    {
        // Fechas de inicio local
        $zona_horaria = dame_zona_horaria_local();
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);

        // Parámetros de periodo de tiempo
        if ($parametros_periodo_tiempo !== NULL)
        {
            $modificar_periodo_tiempo = $parametros_periodo_tiempo["modificar_periodo_tiempo"];
            $periodo_tiempo = $parametros_periodo_tiempo["periodo_tiempo"];
            $iniciar_comienzo_periodo_tiempo = $parametros_periodo_tiempo["iniciar_comienzo_periodo_tiempo"];
            $numero_periodos_tiempo = $parametros_periodo_tiempo["numero_periodos_tiempo"];
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_periodo_tiempo["fecha_inicio_periodo_tiempo"];

            // Si hay que modificar el periodo de tiempo
            if ($modificar_periodo_tiempo == VALOR_SI)
            {
                switch ($periodo_tiempo)
                {
                    case PERIODO_TIEMPO_FECHA_INICIO:
                    {
                        $zona_horaria = dame_zona_horaria_local();
                        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $zona_horaria);
                        $fecha_hora_inicio_local->setTime(0, 0, 0);
                        break;
                    }
                    default:
                    {
                        $fecha_hora_inicio_local = clone $fecha_hora_fin_local;
                        $numero_periodos_tiempo_aux = $numero_periodos_tiempo;
                        if ($iniciar_comienzo_periodo_tiempo == false)
                        {
                            $numero_periodos_tiempo_aux += 1;
                        }
                        switch ($periodo_tiempo)
                        {
                            case PERIODO_TIEMPO_DIA:
                            {
                                $fecha_hora_inicio_local->modify('-'.$numero_periodos_tiempo_aux.' day');
                                break;
                            }
                            case PERIODO_TIEMPO_SEMANA:
                            {
                                $fecha_hora_inicio_local->modify('-'.$numero_periodos_tiempo_aux.' week');
                                break;
                            }
                            case PERIODO_TIEMPO_MES:
                            {
                                $fecha_hora_inicio_local->modify('-'.$numero_periodos_tiempo_aux.' months');
                                break;
                            }
                            case PERIODO_TIEMPO_ANYO:
                            {
                                $fecha_hora_inicio_local->modify('-'.$numero_periodos_tiempo_aux.' year');
                                break;
                            }
                        }
                        if ($iniciar_comienzo_periodo_tiempo == true)
                        {
                            $fecha_hora_inicio_local->setTime(0, 0, 0);
                            switch ($periodo_tiempo)
                            {
                                case PERIODO_TIEMPO_DIA:
                                {
                                    break;
                                }
                                case PERIODO_TIEMPO_SEMANA:
                                {
                                    $numero_dia_semana = $fecha_hora_inicio_local->format('w');
                                    if ($numero_dia_semana == 0)
                                    {
                                        $numero_dia_semana = 7;
                                    }
                                    date_modify($fecha_hora_inicio_local, '-'.($numero_dia_semana - 1).' day');
                                    break;
                                }
                                case PERIODO_TIEMPO_MES:
                                {
                                    $fecha_hora_inicio_local->setDate(
                                        $fecha_hora_inicio_local->format("Y"),
                                        $fecha_hora_inicio_local->format("m"),
                                        1);
                                    break;
                                }
                                case PERIODO_TIEMPO_ANYO:
                                {
                                    $fecha_hora_inicio_local->setDate(
                                        $fecha_hora_inicio_local->format("Y"),
                                        1,
                                        1);
                                    break;
                                }
                                default:
                                {
                                    throw new Exception("Periodo desconocido");
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }

        // Se devuelve la fecha de inicio
        $cadena_fecha_hora_inicio_local_local_modificada = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        return ($cadena_fecha_hora_inicio_local_local_modificada);
    }


    // Devuelve información de los periodos a partir de la fecha de inicio y fin
    // y los parámetros de duración y separación de periodos
    function dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
        $cadena_fecha_hora_inicio_local_local,
        $cadena_fecha_hora_fin_local_local,
        $parametros_duracion_separacion_periodos)
    {
        // Fechas de inicio y fin locales
        $zona_horaria = dame_zona_horaria_local();
        $fecha_hora_inicio_posterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_fin_posterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_fin_posterior_local->setTime(0, 0, 0);

        // Parámetros de duración y separación de periodos
        if ($parametros_duracion_separacion_periodos === NULL)
        {
            $modificar_duracion_periodos = VALOR_NO;
            $modificar_desplazamiento_periodo_anterior = VALOR_NO;
            $ajustar_dias_semana = VALOR_NO;
        }
        else
        {
            $modificar_duracion_periodos = $parametros_duracion_separacion_periodos["modificar_duracion_periodos"];
            $periodo_tiempo_duracion_periodos = $parametros_duracion_separacion_periodos["periodo_tiempo_duracion_periodos"];
            $duracion_periodos_completos = $parametros_duracion_separacion_periodos["duracion_periodos_completos"];
            $iniciar_comienzo_periodo_tiempo_duracion_periodos = $parametros_duracion_separacion_periodos["iniciar_comienzo_periodo_tiempo_duracion_periodos"];
            $numero_periodos_tiempo_duracion_periodos = $parametros_duracion_separacion_periodos["numero_periodos_tiempo_duracion_periodos"];
            $modificar_desplazamiento_periodo_anterior = $parametros_duracion_separacion_periodos["modificar_desplazamiento_periodo_anterior"];
            $periodo_tiempo_desplazamiento_periodo_anterior = $parametros_duracion_separacion_periodos["periodo_tiempo_desplazamiento_periodo_anterior"];
            $numero_periodos_tiempo_desplazamiento_periodo_anterior = $parametros_duracion_separacion_periodos["numero_periodos_tiempo_desplazamiento_periodo_anterior"];
            $ajustar_dias_semana = $parametros_duracion_separacion_periodos["ajustar_dias_semana"];
        }

        // Números de días de periodos
        $numero_dias_periodo = NULL;
        $numero_dias_periodo_anterior = NULL;

        // Se calculan la duración de los periodos y la fecha de inicio del periodo posterior
        if ($modificar_duracion_periodos == VALOR_NO)
        {
            $duracion_periodo = $fecha_hora_inicio_posterior_local->diff($fecha_hora_fin_posterior_local);
            $numero_dias_periodo = $duracion_periodo->days + 1;
            $cadena_fecha_hora_inicio_posterior_local_local = $cadena_fecha_hora_inicio_local_local;
        }
        else
        {
            $numero_periodos_tiempo_duracion_periodos_aux = $numero_periodos_tiempo_duracion_periodos;
            if ($iniciar_comienzo_periodo_tiempo_duracion_periodos == false)
            {
                $numero_periodos_tiempo_duracion_periodos_aux += 1;
            }
            $fecha_hora_inicio_posterior_local = clone $fecha_hora_fin_posterior_local;
            switch ($periodo_tiempo_duracion_periodos)
            {
                case PERIODO_TIEMPO_DIA:
                {
                    $fecha_hora_inicio_posterior_local->modify('-'.$numero_periodos_tiempo_duracion_periodos_aux.' day');
                    break;
                }
                case PERIODO_TIEMPO_SEMANA:
                {
                    $fecha_hora_inicio_posterior_local->modify('-'.$numero_periodos_tiempo_duracion_periodos_aux.' week');
                    break;
                }
                case PERIODO_TIEMPO_MES:
                {
                    $fecha_hora_inicio_posterior_local->modify('-'.$numero_periodos_tiempo_duracion_periodos_aux.' months');
                    break;
                }
                case PERIODO_TIEMPO_ANYO:
                {
                    $fecha_hora_inicio_posterior_local->modify('-'.$numero_periodos_tiempo_duracion_periodos_aux.' year');
                    break;
                }
                default:
                {
                    throw new Exception("Periodo de tiempo de duración de periodos desconocido: '".$periodo_tiempo_duracion_periodos."'");
                }
            }
            if ($iniciar_comienzo_periodo_tiempo_duracion_periodos == true)
            {
                $fecha_hora_inicio_posterior_local->setTime(0, 0, 0);
                switch ($periodo_tiempo_duracion_periodos)
                {
                    case PERIODO_TIEMPO_DIA:
                    {
                        break;
                    }
                    case PERIODO_TIEMPO_SEMANA:
                    {
                        $numero_dia_semana = $fecha_hora_inicio_posterior_local->format('w');
                        if ($numero_dia_semana == 0)
                        {
                            $numero_dia_semana = 7;
                        }
                        date_modify($fecha_hora_inicio_posterior_local, '-'.($numero_dia_semana - 1).' day');
                        break;
                    }
                    case PERIODO_TIEMPO_MES:
                    {
                        $fecha_hora_inicio_posterior_local->setDate(
                            $fecha_hora_inicio_posterior_local->format("Y"),
                            $fecha_hora_inicio_posterior_local->format("m"),
                            1);
                        break;
                    }
                    case PERIODO_TIEMPO_ANYO:
                    {
                        $fecha_hora_inicio_posterior_local->setDate(
                            $fecha_hora_inicio_posterior_local->format("Y"),
                            1,
                            1);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Periodo de tiempo de duración de periodos desconocido: '".$periodo_tiempo_duracion_periodos."'");
                    }
                }
            }
            $duracion_periodo = $fecha_hora_inicio_posterior_local->diff($fecha_hora_fin_posterior_local);
            $numero_dias_periodo = $duracion_periodo->days + 1;
            $cadena_fecha_hora_inicio_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_local, $_SESSION["formato_fecha_hora_local"]);
        }

        // Se calcula la fecha inicial del periodo anterior
        // (los días de diferencia de periodos si la duración es mayor que 1 semana se modifican para que coincidan por días de la semana)
        if ($modificar_desplazamiento_periodo_anterior == VALOR_NO)
        {
            $numero_dias_diferencia_periodos = $numero_dias_periodo;
            $fecha_hora_inicio_anterior_local = clone $fecha_hora_inicio_posterior_local;
            $fecha_hora_inicio_anterior_local->sub(new DateInterval("P".$numero_dias_diferencia_periodos."D"));
        }
        else
        {
            $fecha_hora_inicio_anterior_local = clone $fecha_hora_inicio_posterior_local;
            switch ($periodo_tiempo_desplazamiento_periodo_anterior)
            {
                case PERIODO_TIEMPO_DIA:
                {
                    $fecha_hora_inicio_anterior_local->modify('-'.$numero_periodos_tiempo_desplazamiento_periodo_anterior.' day');
                    break;
                }
                case PERIODO_TIEMPO_SEMANA:
                {
                    $fecha_hora_inicio_anterior_local->modify('-'.$numero_periodos_tiempo_desplazamiento_periodo_anterior.' week');
                    break;
                }
                case PERIODO_TIEMPO_MES:
                {
                    $fecha_hora_inicio_anterior_local->modify('-'.$numero_periodos_tiempo_desplazamiento_periodo_anterior.' months');
                    break;
                }
                case PERIODO_TIEMPO_ANYO:
                {
                    $fecha_hora_inicio_anterior_local->modify('-'.$numero_periodos_tiempo_desplazamiento_periodo_anterior.' year');
                    break;
                }
                default:
                {
                    throw new Exception("Periodo de tiempo de desplazamiento de periodo anterior desconocido: '".$periodo_tiempo_desplazamiento_periodo_anterior."'");
                }
            }
            $duracion_diferencia_periodos = $fecha_hora_inicio_anterior_local->diff($fecha_hora_inicio_posterior_local);
            $numero_dias_diferencia_periodos = $duracion_diferencia_periodos->days;
        }

        // Se establecen los días de periodos a los periodos completos a comparar
        // (para que salga el periodo anterior completo, no sólo la parte correspondiente a la parte donde hay datos del periodo posterior)
        if (($modificar_duracion_periodos == VALOR_SI) && ($duracion_periodos_completos == VALOR_SI))
        {
            switch ($periodo_tiempo_duracion_periodos)
            {
                case PERIODO_TIEMPO_DIA:
                {
                    $numero_dias_periodo = ($numero_periodos_tiempo_duracion_periodos + 1);
                    break;
                }
                case PERIODO_TIEMPO_SEMANA:
                {
                    $numero_dias_periodo = ($numero_periodos_tiempo_duracion_periodos + 1) * 7;
                    break;
                }
                case PERIODO_TIEMPO_MES:
                {
                    $numero_meses = $numero_periodos_tiempo_duracion_periodos + 1;
                    $numero_dias_periodo_posterior = 0;
                    $mes_posterior = $fecha_hora_inicio_posterior_local->format('m');
                    $anyo_posterior = $fecha_hora_inicio_posterior_local->format('y');
                    for ($i = 0; $i < $numero_meses; $i++)
                    {
                        $numero_dias_mes_posterior = cal_days_in_month(
                            CAL_GREGORIAN,
                            $mes_posterior,
                            $anyo_posterior);
                        $numero_dias_periodo_posterior += $numero_dias_mes_posterior;
                        $mes_posterior += 1;
                        if ($mes_posterior == 13)
                        {
                            $mes_posterior = 1;
                            $anyo_posterior += 1;
                        }
                    }
                    $numero_dias_periodo = $numero_dias_periodo_posterior;
                    if ($ajustar_dias_semana == true)
                    {
                        $numero_dias_periodo_anterior = $numero_dias_periodo_posterior;
                    }
                    else
                    {
                        $mes_anterior = $fecha_hora_inicio_anterior_local->format('m');
                        $anyo_anterior = $fecha_hora_inicio_anterior_local->format('y');
                        for ($i = 0; $i < $numero_meses; $i++)
                        {
                            $numero_dias_mes_anterior = cal_days_in_month(
                                CAL_GREGORIAN,
                                $mes_anterior,
                                $anyo_anterior);
                            $numero_dias_periodo_anterior += $numero_dias_mes_anterior;
                            $mes_anterior += 1;
                            if ($mes_anterior == 13)
                            {
                                $mes_anterior = 1;
                                $anyo_anterior += 1;
                            }
                        }
                    }
                    break;
                }
                case PERIODO_TIEMPO_ANYO:
                {
                    $numero_anyos = $numero_periodos_tiempo_duracion_periodos + 1;
                    $numero_dias_periodo_posterior = 0;
                    $anyo_posterior = $fecha_hora_inicio_posterior_local->format('y');
                    for ($i = 0; $i < $numero_anyos; $i++)
                    {
                        $numero_dias_anyo_posterior = 0;
                        for ($mes_posterior = 1; $mes_posterior <= 12; $mes_posterior++)
                        {
                            $numero_dias_mes_posterior = cal_days_in_month(
                                CAL_GREGORIAN,
                                $mes_posterior,
                                $anyo_posterior);
                            $numero_dias_anyo_posterior += $numero_dias_mes_posterior;
                        }
                        $numero_dias_periodo_posterior += $numero_dias_anyo_posterior;
                        $anyo_posterior += 1;
                    }
                    $numero_dias_periodo = $numero_dias_periodo_posterior;
                    if ($ajustar_dias_semana == true)
                    {
                        $numero_dias_periodo_anterior = $numero_dias_periodo_posterior;
                    }
                    else
                    {
                        $numero_dias_periodo_anterior = 0;
                        $anyo_anterior = $fecha_hora_inicio_anterior_local->format('y');
                        for ($i = 0; $i < $numero_anyos; $i++)
                        {
                            $numero_dias_anyo_anterior = 0;
                            for ($mes_anterior = 1; $mes_anterior <= 12; $mes_anterior++)
                            {
                                $numero_dias_mes_anterior = cal_days_in_month(
                                    CAL_GREGORIAN,
                                    $mes_anterior,
                                    $anyo_anterior);
                                $numero_dias_anyo_anterior += $numero_dias_mes_anterior;
                            }
                            $numero_dias_periodo_anterior += $numero_dias_anyo_anterior;
                            $anyo_anterior += 1;
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Periodo de tiempo de duración de periodos desconocido: '".$periodo_tiempo_duracion_periodos."'");
                }
            }
        }

        // Se ajusta el día de la semana (si es necesario)
        if ($ajustar_dias_semana == true)
        {
            $numero_dias_diferencia_periodos = floor($numero_dias_diferencia_periodos / 7) * 7;
            $fecha_hora_inicio_anterior_local = clone $fecha_hora_inicio_posterior_local;
            $fecha_hora_inicio_anterior_local->sub(new DateInterval("P".$numero_dias_diferencia_periodos."D"));
        }
        $cadena_fecha_hora_inicio_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_inicio_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_local, $_SESSION["formato_fecha_local"]);
        $cadena_fecha_inicio_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_local, $_SESSION["formato_fecha_local"]);


        // Nota: Si una cadena no tiene segundos, al convertir esa cadena a fecha (aunque en el formato haya segundos) se establecen los segundos a 0

        // Información de periodos
        $info_periodos = array(
            "cadena_fecha_hora_inicio_anterior_local_local" => $cadena_fecha_hora_inicio_anterior_local_local,
            "cadena_fecha_hora_inicio_posterior_local_local" => $cadena_fecha_hora_inicio_posterior_local_local,
            "cadena_fecha_inicio_anterior_local_local" => $cadena_fecha_inicio_anterior_local_local,
            "cadena_fecha_inicio_posterior_local_local" => $cadena_fecha_inicio_posterior_local_local,
            "numero_dias_periodo" => $numero_dias_periodo,
            "numero_dias_periodo_anterior" => $numero_dias_periodo_anterior);
        return ($info_periodos);
    }


    // Devuelve la clave del periodo para los informes comparación de periodos
    function dame_clave_periodo_comparacion_periodos($fecha_hora, $intervalo_valores)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA:
            {
                $clave_periodo = $fecha_hora->format("Y/m/d H:i");
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $clave_periodo = $fecha_hora->format("Y/m/d");
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $clave_periodo = $fecha_hora->format("Y/W");
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $clave_periodo = $fecha_hora->format("Y/m");
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
            }
        }
        return ($clave_periodo);
    }


    // Devuelve las horas de inicio y fin del informe y medición especificados
    function dame_horas_inicio_fin_informe_medicion($tipo_informe, $medicion)
    {
        $horas_inicio_fin = array(
            "hora_inicio" => "00:00",
            "hora_fin" => "23:59");
        switch ($medicion)
        {
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                            case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                            case TIPO_INFORME_SMARTMETER_MAPA_CONSUMOS_COSTES:
                            {
                                // EMG : Cambio para que Acciona pueda visualizar el dia gas como dia natural.
																$id_red = $_SESSION["id_red"];
										            $bd_red = BaseDatosRed::dame_base_datos();
										            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
										            $res = $bd_red->ejecuta_consulta($consulta);
										            $fila = $res->dame_siguiente_fila();
										            $nombre_cliente = $fila["nombre"];

										            if ($nombre_cliente != 'Acciona')
                                {
                                    $horas_inicio_fin["hora_inicio"] = "06:00";
                                    $horas_inicio_fin["hora_fin"] = "05:59";
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }
        return ($horas_inicio_fin);
    }


    // Modifica el periodo por defecto del informe (convierte a periodo completo si es necesario)
    function modifica_periodo_defecto_informe($periodo_defecto_informe)
    {
        if ($_SESSION["periodo_completo_informes_defecto"] == VALOR_SI)
        {
            switch ($periodo_defecto_informe)
            {
                case PERIODO_DIA_INICIO_HOY:
                {
                    $periodo_defecto_informe = PERIODO_DIA;
                    break;
                }
                case PERIODO_DIA_INICIO_SEMANA:
                {
                    $periodo_defecto_informe = PERIODO_SEMANA;
                    break;
                }
                case PERIODO_DIA_INICIO_MES:
                {
                    $periodo_defecto_informe = PERIODO_MES;
                    break;
                }
            }
        }
        return ($periodo_defecto_informe);
    }


    // Modifica los días de duración de periodos del informe
    function modifica_dias_duracion_periodos_defecto_informe($periodo_defecto_informe, $dias_duracion_periodos_defecto_informe)
    {
        if ($_SESSION["periodo_completo_informes_defecto"] == VALOR_SI)
        {
            switch ($periodo_defecto_informe)
            {
                case PERIODO_DIA_INICIO_HOY:
                case PERIODO_DIA_INICIO_SEMANA:
                case PERIODO_DIA_INICIO_MES:
                {
                    $dias_duracion_periodos_defecto_informe += 1;
                    break;
                }
            }
        }
        return ($dias_duracion_periodos_defecto_informe);
    }


    //
    // Funciones de agregaciones
    //


    // Devuelve información de agregaciones de sensores de varios campos de la misma clase
    function dame_info_agregaciones_sensores_campos(
        $id_ratio,
        $ids_sensores,
        $nombres_sensores,
        $clase_sensor,
        $campos,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $filas_valores_sensores)
    {
        // Información de agregaciones
        $info_agregaciones_sensores = array();
        $numeros_sensores_valores = array();
        foreach ($campos as $campo)
        {
            $info_agregaciones_sensores[$campo] = array();
            $numeros_sensores_valores[$campo] = 0;
        }

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Filas de valores del sensor
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Se recorren las filas del sensor (por cada uno de los campos)
            $numero_valores_sensor = array();
            $indice_info_agregaciones_sensores = array();
            foreach ($campos as $campo)
            {
                $numero_valores_sensor[$campo] = 0;
                $indice_info_agregaciones_sensores[$campo] = 0;
            }
            foreach ($campos as $campo)
            {
                // Se recupera si aplica el ratio en el campo actual
                // (hay que recuperar la información del ratio para cada uno de los campos del sensor,
                //  para evitar problemas con los índices de búsqueda en los ratios variables)
                $aplicar_ratio_campo = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
                if ($aplicar_ratio_campo == true)
                {
                    $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                        $id_ratio,
                        $id_sensor,
                        $cadena_fecha_hora_inicio_base_datos_utc,
                        $cadena_fecha_hora_fin_base_datos_utc,
                        $intervalo_valores,
                        $horario_semanal,
                        $exclusion_fechas,
                        $inclusion_fechas);
                }

                // Se recorren las filas del sensor
                foreach ($filas_valores_sensor as $fila_valor_sensor)
                {
                    // Fecha y valor
                    $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                    $valor = $fila_valor_sensor[$campo];
                    if ($valor !== NULL)
                    {
                        $valor = (float) $valor;
                        if ($aplicar_ratio_campo == true)
                        {
                            aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                        }
                    }
                    if ($valor === NULL)
                    {
                        continue;
                    }

                    // Timestamp
                    $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);

                    // Si es el primer sensor con valores se añade la agregación de sensores
                    if ($numeros_sensores_valores[$campo] == 0)
                    {
                        $info_agregacion_sensores = array(
                            "cadena_fecha_hora_agregacion_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                            "timestamp_fecha_agregacion_utc" => $timestamp_fecha_hora_valor_utc,
                            "valores" => array($valor),
                            "ids_sensores" => array($id_sensor),
                            "nombres_sensores" => array($nombre_sensor));
                        array_push($info_agregaciones_sensores[$campo], $info_agregacion_sensores);
                    }
                    else
                    {
                        // Se busca el valor del sensor con la misma fecha de la agregación correspondiente
                        // - Si se encuentra la agregación se añade el valor
                        // - Si no se encuentra se añade una nueva agregación en la posición correspondiente
                        $timestamp_info_agregacion_sensores_utc = $info_agregaciones_sensores[$campo][$indice_info_agregaciones_sensores[$campo]]["timestamp_fecha_agregacion_utc"];
                        $fin_info_agregaciones_sensores_alcanzado = false;
                        while ($timestamp_info_agregacion_sensores_utc < $timestamp_fecha_hora_valor_utc)
                        {
                            $indice_info_agregaciones_sensores[$campo] += 1;
                            if ($indice_info_agregaciones_sensores[$campo] == count($info_agregaciones_sensores[$campo]))
                            {
                                $fin_info_agregaciones_sensores_alcanzado = true;
                                break;
                            }
                            $timestamp_info_agregacion_sensores_utc = $info_agregaciones_sensores[$campo][$indice_info_agregaciones_sensores[$campo]]["timestamp_fecha_agregacion_utc"];
                        }
                        if ($timestamp_info_agregacion_sensores_utc == $timestamp_fecha_hora_valor_utc)
                        {
                            array_push($info_agregaciones_sensores[$campo][$indice_info_agregaciones_sensores[$campo]]["valores"], $valor);
                            array_push($info_agregaciones_sensores[$campo][$indice_info_agregaciones_sensores[$campo]]["ids_sensores"], $id_sensor);
                            array_push($info_agregaciones_sensores[$campo][$indice_info_agregaciones_sensores[$campo]]["nombres_sensores"], $nombre_sensor);
                        }
                        else
                        {
                            $info_agregacion_sensores = array(
                                "cadena_fecha_hora_agregacion_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                                "timestamp_fecha_agregacion_utc" => $timestamp_fecha_hora_valor_utc,
                                "valores" => array($valor),
                                "ids_sensores" => array($id_sensor),
                                "nombres_sensores" => array($nombre_sensor));
                            array_splice($info_agregaciones_sensores[$campo], $indice_info_agregaciones_sensores[$campo], 0, array($info_agregacion_sensores));
                        }
                    }

                    // Contador de valores del sensor
                    $numero_valores_sensor[$campo] += 1;
                }
                if ($numero_valores_sensor[$campo] > 0)
                {
                    $numeros_sensores_valores[$campo] += 1;
                }
            }
        }

        $res = array(
            "info_agregaciones_sensores" => $info_agregaciones_sensores,
            "numeros_sensores_valores" => $numeros_sensores_valores);
        return ($res);
    }


    // Devuelve información de agregaciones de sensores de varios campos (con la misma unidad de medida) de múltiples clases
    function dame_info_agregaciones_sensores_clases(
        $id_ratio,
        $clases_sensor,
        $ids_sensores,
        $nombres_sensores,
        $ids_sensores_clases,
        $campos,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $filas_valores_sensores,
        $agrupar_clases_sensor)
    {
        // Información de agregaciones
        $info_agregaciones_sensores = array();
        if ($agrupar_clases_sensor == false)
        {
            $numeros_sensores_valores = 0;
        }
        else
        {
            $numeros_sensores_valores = array();
        }

        // Se recorren los sensores
        $primer_sensor_valores = true;
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Filas de valores del sensor
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Campo de la clase del sensor correspondiente
            for ($j = 0; $j < count($ids_sensores_clases); $j++)
            {
                $clase_sensor = $clases_sensor[$j];
                $ids_sensores_clase = $ids_sensores_clases[$clase_sensor];
                if (in_array($id_sensor, $ids_sensores_clase) == true)
                {
                    $campo = $campos[$j];
                    break;
                }
            }

            // Se recupera la información del ratio (si aplica)
            $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Se recorren las filas del sensor
            $numero_valores_sensor = 0;
            $indice_info_agregaciones_sensores = 0;
            foreach ($filas_valores_sensor as $fila_valor_sensor)
            {
                // Fecha y valor
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                if ($valor === NULL)
                {
                    continue;
                }

                // Timestamp
                $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);

                // Si es el primer sensor con valores se añade la agregación de sensores
                if ($primer_sensor_valores == true)
                {
                    $info_agregacion_sensores = array(
                        "cadena_fecha_hora_agregacion_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                        "timestamp_fecha_agregacion_utc" => $timestamp_fecha_hora_valor_utc
                    );
                    if ($agrupar_clases_sensor == false)
                    {
                        $info_agregacion_sensores["valores"] = array($valor);
                        $info_agregacion_sensores["ids_sensores"] = array($id_sensor);
                        $info_agregacion_sensores["nombres_sensores"] = array($nombre_sensor);
                    }
                    else
                    {
                        $info_agregacion_sensores["clases"] = array(
                            $clase_sensor => array(
                                "valores" => array($valor),
                                "ids_sensores" => array($id_sensor),
                                "nombres_sensores" => array($nombre_sensor),
                            )
                        );
                    }
                    array_push($info_agregaciones_sensores, $info_agregacion_sensores);
                }
                else
                {
                    // Se busca el valor del sensor con la misma fecha de la agregación correspondiente
                    // - Si se encuentra la agregación se añade el valor
                    // - Si no se encuentra se añade una nueva agregación en la posición correspondiente
                    $timestamp_info_agregacion_sensores_utc = $info_agregaciones_sensores[$indice_info_agregaciones_sensores]["timestamp_fecha_agregacion_utc"];
                    $fin_info_agregaciones_sensores_alcanzado = false;
                    while ($timestamp_info_agregacion_sensores_utc < $timestamp_fecha_hora_valor_utc)
                    {
                        $indice_info_agregaciones_sensores += 1;
                        if ($indice_info_agregaciones_sensores == count($info_agregaciones_sensores))
                        {
                            $fin_info_agregaciones_sensores_alcanzado = true;
                            break;
                        }
                        $timestamp_info_agregacion_sensores_utc = $info_agregaciones_sensores[$indice_info_agregaciones_sensores]["timestamp_fecha_agregacion_utc"];
                    }
                    if ($timestamp_info_agregacion_sensores_utc == $timestamp_fecha_hora_valor_utc)
                    {
                        if ($agrupar_clases_sensor == false)
                        {
                            array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["valores"], $valor);
                            array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["ids_sensores"], $id_sensor);
                            array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["nombres_sensores"], $nombre_sensor);
                        }
                        else
                        {
                            $clases_agregacion_sensores = $info_agregaciones_sensores[$indice_info_agregaciones_sensores]["clases"];
                            if (array_key_exists($clase_sensor, $clases_agregacion_sensores) == true)
                            {
                                array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["clases"][$clase_sensor]["valores"], $valor);
                                array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["clases"][$clase_sensor]["ids_sensores"], $id_sensor);
                                array_push($info_agregaciones_sensores[$indice_info_agregaciones_sensores]["clases"][$clase_sensor]["nombres_sensores"], $nombre_sensor);
                            }
                            else
                            {
                                $info_agregaciones_sensores[$indice_info_agregaciones_sensores]["clases"][$clase_sensor] = array(
                                    "valores" => array($valor),
                                    "ids_sensores" => array($id_sensor),
                                    "nombres_sensores" => array($nombre_sensor),
                                );
                            }
                        }
                    }
                    else
                    {
                        $info_agregacion_sensores = array(
                            "cadena_fecha_hora_agregacion_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                            "timestamp_fecha_agregacion_utc" => $timestamp_fecha_hora_valor_utc
                        );
                        if ($agrupar_clases_sensor == false)
                        {
                            $info_agregacion_sensores["valores"] = array($valor);
                            $info_agregacion_sensores["ids_sensores"] = array($id_sensor);
                            $info_agregacion_sensores["nombres_sensores"] = array($nombre_sensor);
                        }
                        else
                        {
                            $info_agregacion_sensores["clases"] = array(
                                $clase_sensor => array(
                                    "valores" => array($valor),
                                    "ids_sensores" => array($id_sensor),
                                    "nombres_sensores" => array($nombre_sensor),
                                )
                            );
                        }
                        array_splice($info_agregaciones_sensores, $indice_info_agregaciones_sensores, 0, array($info_agregacion_sensores));
                    }
                }

                // Contador de valores del sensor
                $numero_valores_sensor += 1;
            }
            if ($numero_valores_sensor > 0)
            {
                if ($primer_sensor_valores == true)
                {
                    $primer_sensor_valores = false;
                }
                if ($agrupar_clases_sensor == false)
                {
                    $numeros_sensores_valores += 1;
                }
                else
                {
                    if (array_key_exists($clase_sensor, $numeros_sensores_valores) == true)
                    {
                        $numeros_sensores_valores[$clase_sensor] += 1;
                    }
                    else
                    {
                        $numeros_sensores_valores[$clase_sensor] = 1;
                    }
                }
            }
        }

        $res = array(
            "info_agregaciones_sensores" => $info_agregaciones_sensores,
            "numeros_sensores_valores" => $numeros_sensores_valores);
        return ($res);
    }


    // Devuelve los tooltips de agregación de sensores
    function dame_tooltips_agregacion_sensores(
        $nombre_sensor,
        $cadena_fecha_hora_base_datos_utc,
        $valor,
        $suma_valores,
        $unidad_medida,
        $nombre_sensor_agregacion,
        $numero_sensores_sin_valor)
    {
        $idiomas = new Idiomas();

        $tooltip_valor = "";
        $tooltip_suma_valores = "";

        // Fecha de valores
        $fecha_valores_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
        $fecha_valores_local = dame_fecha_hora_local($fecha_valores_utc);
        $cadena_fecha_valores_local_local = convierte_fecha_a_cadena($fecha_valores_local, $_SESSION["formato_fecha_hora_local_sin_segundos"]);

        // Valores
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }
        $cadena_valor = formatea_numero($valor, 2).$cadena_unidad_medida." (".$cadena_fecha_valores_local_local.")";
        $cadena_suma_valores = formatea_numero($suma_valores, 2).$cadena_unidad_medida." (".$cadena_fecha_valores_local_local.")";

        // Tooltips
        if ($nombre_sensor !== NULL)
        {
            $tooltip_valor .= "[".$nombre_sensor."]"."<br/>";
            $tooltip_suma_valores .= "[".$nombre_sensor."]"."<br/>";
        }
        $tooltip_valor .= $cadena_valor;
        $tooltip_suma_valores .= $cadena_suma_valores;

        // Nombre de sensor de la agregación (p.e. máximo o mínimo)
        if ($nombre_sensor_agregacion !== NULL)
        {
            $cadena_sensor_agregacion = "<br/>"."(".$idiomas->_("Sensor").": ".$nombre_sensor_agregacion.")";
            $tooltip_valor .= $cadena_sensor_agregacion;
            $tooltip_suma_valores .= $cadena_sensor_agregacion;
        }

        // Número de sensores de la agregación
        if ($numero_sensores_sin_valor > 0)
        {
            $cadena_sensores_sin_valor = "<br/>"."(".$idiomas->_("número de sensores sin valor").": ".$numero_sensores_sin_valor.")";
            $tooltip_valor .= $cadena_sensores_sin_valor;
            $tooltip_suma_valores .= $cadena_sensores_sin_valor;
        }

        // Se devuelven los tooltips
        return (array(
            "tooltip_valor" => $tooltip_valor,
            "tooltip_suma_valores" => $tooltip_suma_valores));
    }


    // Devuelve las cadenas de inicio y fin de nombres de agregaciones
    function dame_cadenas_inicio_fin_nombre_agregacion()
    {
        $idiomas = new Idiomas();

        $cadena_inicio = $idiomas->_("Agregación").": ";
        $cadena_fin = "";
        $res = array(
            "cadena_inicio" => $cadena_inicio,
            "cadena_fin" => $cadena_fin);
        return ($res);
    }


    //
    // Funciones de campos "especiales"
    //


    // Devuelve el tipo de valores de un campo según el informe
    function dame_tipo_valores_campo_clase_sensor_informe($clase_sensor, $campo, $tipo_informe)
    {
        $tipo_valores_campo = NULL;
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        switch ($campo)
                        {
                            case CAMPO_SOBREPOTENCIA:
                            {
                                $tipo_valores_campo = TIPO_VALORES_SENSOR_INCREMENTALES;
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }
        if ($tipo_valores_campo === NULL)
        {
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        }
        return ($tipo_valores_campo);
    }


    // Modifica el valor del campo según el informe
    function modifica_valor_campo_clase_sensor_informe($clase_sensor, $campo, $valor, $tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        switch ($campo)
                        {
                            case CAMPO_SOBREPOTENCIA:
                            {
                                if ($valor < 0)
                                {
                                    $valor = 0;
                                }
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }
        return ($valor);
    }


    //
    // Funciones de intervalos
    //


    // Devuelve el intervalo de fecha (objeto) correspondiente al intervalo de valores especificado
    // (para sumar o restar a la fecha si las fechas mínima y máxima son iguales)
    function dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $intervalo_fecha = new DateInterval("PT1S");
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            {
                $intervalo_fecha = new DateInterval("PT15M");
                break;
            }
            case INTERVALO_VALORES_HORA:
            {
                $intervalo_fecha = new DateInterval("PT1H");
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $intervalo_fecha = new DateInterval("P1D");
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            {
                $intervalo_fecha = new DateInterval("P1W");
                break;
            }
            case INTERVALO_VALORES_MES:
            {
                $intervalo_fecha = new DateInterval("P1M");
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores desconocido: '".$intervalo_valores."'");
            }
        }
        return ($intervalo_fecha);
    }


    //
    // Funciones de descripciones
    //


    function dame_descripcion_sensor_informe($id_sensor)
    {
        // Descripción del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $descripcion_sensor = $fila_sensor["descripcion"];
        if ($descripcion_sensor != "")
        {
            $descripcion_sensor = "<i class='icon-asterisk color-gris'></i> ".$descripcion_sensor;
        }
        return ($descripcion_sensor);
    }


    function dame_descripciones_sensores_informe($ids_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $descripciones_sensores = "";
        $cadena_ids_consulta = dame_cadena_ids_consulta($ids_sensores);
        $consulta_sensores = "
            SELECT *
            FROM sensores
            WHERE
                id IN (".$bd_red->_($cadena_ids_consulta).")
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $nombre_sensor = $fila_sensor["nombre"];
            $descripcion_sensor = $fila_sensor["descripcion"];
            if ($descripcion_sensor != "")
            {
                if ($descripciones_sensores != "")
                {
                    $descripciones_sensores .= "<br/>";
                }
                $descripciones_sensores .= "<i class='icon-asterisk color-gris'></i> ".$nombre_sensor.": ".$descripcion_sensor;
            }
        }
        return ($descripciones_sensores);
    }


    function dame_descripcion_destino_accion_informe($destino, $id_destino)
    {
        // Descripción del destino
        switch ($destino)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $fila_destino = dame_fila_actuador($id_destino);
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $fila_destino = dame_fila_grupo_actuadores($id_destino);
                break;
            }
        }
        $descripcion_destino = $fila_destino["descripcion"];
        if ($descripcion_destino != "")
        {
            $descripcion_destino = "<i class='icon-asterisk color-gris'></i> ".$descripcion_destino;
        }
        return ($descripcion_destino);
    }


    //
    // Funciones de informes de perfil horario
    //


    function dame_datos_grafica_valores_mapas_calor_valores_perfil_horario(
        $id_sensor,
        $campo,
        $descripcion_campo,
        $funcion_conversion_valor_campo,
        $numero_decimales_valores,
        $unidad_medida,
        $cadena_fecha_inicio_perfil_horario_local_local,
        $cadena_fecha_fin_perfil_horario_local_local,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $milisegundos_desfase_zonas_horarias_cliente_local)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_inicio_perfil_horario_local_local.", 00:00:00", $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_fin_perfil_horario_local_local.", 23:59:59", $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_perfil_horario_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_perfil_horario_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_perfil_horario_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_perfil_horario_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se recuperan las filas de valores del sensor
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_perfil_horario_base_datos_utc,
            $cadena_fecha_hora_fin_perfil_horario_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            NULL);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Se recorren y guardan las filas de valores del sensor
        $filas_valores_sensor = array();
        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            array_push($filas_valores_sensor, $fila_valores_sensor);
        }

        // Se guardan los valores del mapa de calor semanal
        $valores_mapa_calor_valores_perfil_horario_semanales = new ValoresMapaCalor(TIPO_MAPA_CALOR_SEMANAL);
        foreach ($filas_valores_sensor as $fila_valores_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $valor = (float) $fila_valores_sensor[$campo];

            // Se convierte el valor (si es necesario)
            if ($funcion_conversion_valor_campo !== NULL)
            {
                $valor = $funcion_conversion_valor_campo($valor);
            }

            // Datos para el mapa de calor de valores semanales
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            $valores_mapa_calor_valores_perfil_horario_semanales->anyade_valor_fecha_hora($fecha_hora_local, $valor);
        }

        // Se calcula la desviación estándar de cada uno de los valores del mapa de calor de valores semanales
        $valores_horas = $valores_mapa_calor_valores_perfil_horario_semanales->dame_valores_horas();
        foreach ($valores_horas as $indice => $valores_hora)
        {
            // Media de valores y desviación estándar de la hora de la semana
            $numero_valores_hora = $valores_hora["ocurrencias"];
            $media_valores_hora = $valores_hora["suma_valores_hora"] / $valores_hora["ocurrencias"];
            $valores_hora["media_valores"] = $media_valores_hora;
            $suma_desviacion_estandar_hora = 0;
            foreach ($valores_hora["valores_hora"] as $valor_hora)
            {
                $suma_desviacion_estandar_hora += ($valor_hora - $media_valores_hora) * ($valor_hora - $media_valores_hora);
            }
            $desviacion_estandar_hora = sqrt($suma_desviacion_estandar_hora / $numero_valores_hora);
            $valores_hora["desviacion_estandar"] = $desviacion_estandar_hora;
            $valores_horas[$indice] = $valores_hora;
        }
        $valores_mapa_calor_valores_perfil_horario_semanales->pon_valores_horas($valores_horas);

        // Variables de gráfica de valores y mapas de calor correspondientes (de valores utilizados para el cálculo del perfil horario)
        $datos_grafica_valores_perfil_horario_semanales = new VectorDatos();
        $datos_grafica_valores_perfil_horario = new VectorDatos();
        $datos_banda_valores_perfil_horario_semanales = new VectorDatos();
        $min_valor_perfil_horario = INF;
        $max_valor_perfil_horario = -INF;
        $valores_mapa_calor_valores_perfil_horario = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);

        // Segundos máximos entre consumos (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_graficas = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren los valores del sensor
        $timestamp_fecha_hora_valor_perfil_horario_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica_valores_perfil_horario = 0;
        foreach ($filas_valores_sensor as $fila_valores_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $valor = (float) $fila_valores_sensor[$campo];

            // Se convierte el valor (si es necesario)
            if ($funcion_conversion_valor_campo !== NULL)
            {
                $valor = $funcion_conversion_valor_campo($valor);
            }

            // Valores máximo y mínimo
            if ($valor > $max_valor_perfil_horario)
            {
                $max_valor_perfil_horario = $valor;
            }
            if ($valor < $min_valor_perfil_horario)
            {
                $min_valor_perfil_horario = $valor;
            }

            // Se recupera la media de valor semanal para la hora especificada
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            $numero_dia = $fecha_hora_local->format('w');
            if ($numero_dia == 0)
            {
                $numero_dia = 7;
            }
            $hora_dia = $fecha_hora_local->format('H');
            $numero_hora = ($numero_dia - 1) * 24 + $hora_dia;
            $valores_hora_semana_valor = $valores_mapa_calor_valores_perfil_horario_semanales->valores_horas[$numero_hora];
            $valor_semanal = $valores_hora_semana_valor["media_valores"];
            $desviacion_estandar_valor_semanal = $valores_hora_semana_valor["desviacion_estandar"];
            if ($valor_semanal > $max_valor_perfil_horario)
            {
                $max_valor_perfil_horario = $valor_semanal;
            }
            if ($valor_semanal < $min_valor_perfil_horario)
            {
                $min_valor_perfil_horario = $valor_semanal;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_perfil_horario_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_perfil_horario_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica_valores_perfil_horario > 1) &&
                ($segundos_maximos_entre_valores_graficas !== NULL) && ($timestamp_fecha_hora_valor_perfil_horario_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_perfil_horario_utc - $timestamp_fecha_hora_valor_perfil_horario_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_graficas)
                {
                    $numero_puntos_seguidos_grafica_valores_perfil_horario = 0;
                    $datos_grafica_valores_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_perfil_horario_anterior_utc + 1, NULL);
                    $datos_grafica_valores_perfil_horario_semanales->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_perfil_horario_anterior_utc + 1, NULL);
                    $datos_banda_valores_perfil_horario_semanales->anyade_tupla_pareja_datos(NULL, NULL);
                }
            }
            $timestamp_fecha_hora_valor_perfil_horario_anterior_utc = $timestamp_fecha_hora_valor_perfil_horario_utc;
            $numero_puntos_seguidos_grafica_valores_perfil_horario += 1;

            // Tooltips de valores
            $tooltip_valor_semanal = $idiomas->_("Media semanal").": ".formatea_numero($valor_semanal, 2)." ".$unidad_medida;
            $nombre_dia_semana = strtolower(dame_nombre_dia_semana($numero_dia));
            $tooltip_valor_semanal .= " (".$nombre_dia_semana;
            if ($intervalo_valores == INTERVALO_VALORES_HORA)
            {
                $tooltip_valor_semanal .= ", ".$hora_dia.":00";
            }
            $tooltip_valor_semanal .= ")<br/>";
            $cadena_desviacion_estandar_valor_semanal = formatea_numero($desviacion_estandar_valor_semanal, $numero_decimales_valores);
            $tooltip_valor_semanal .= $idiomas->_("Desviación estándar").": ".$cadena_desviacion_estandar_valor_semanal." ".$unidad_medida;
            $tooltip_valor = $descripcion_campo.": ".formatea_numero($valor, 2)." ".$unidad_medida;
            $tooltip_valor .= " (".$nombre_dia_semana." - ";
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_HORA:
                {
                    $cadena_fecha_hora_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $tooltip_valor .= $cadena_fecha_hora_local_local.")";
                    break;
                }
                case INTERVALO_VALORES_DIA:
                {
                    $cadena_fecha_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                    $tooltip_valor .= $cadena_fecha_local_local.")";
                    break;
                }
            }

            // Se añaden los valores (con los tooltips personalizados)
            $datos_grafica_valores_perfil_horario_semanales->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valor_perfil_horario_utc,
                $valor_semanal,
                $tooltip_valor_semanal);
            $datos_grafica_valores_perfil_horario->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valor_perfil_horario_utc,
                $valor,
                $tooltip_valor);
            $datos_banda_valores_perfil_horario_semanales->anyade_tupla_pareja_datos(
                $valor_semanal + $desviacion_estandar_valor_semanal,
                $valor_semanal - $desviacion_estandar_valor_semanal);

            // Datos para el mapa de calor de valores
            $valores_mapa_calor_valores_perfil_horario->anyade_valor_fecha_hora($fecha_hora_local, $valor);
        }

        // Se devuelven los datos de la gráfica y los mapas de calor
        $datos_grafica_valores_mapa_calor = array(
            "datos_grafica_valores_perfil_horario_semanales" => $datos_grafica_valores_perfil_horario_semanales,
            "datos_grafica_valores_perfil_horario" => $datos_grafica_valores_perfil_horario,
            "datos_banda_valores_perfil_horario_semanales" => $datos_banda_valores_perfil_horario_semanales,
            "min_valor_perfil_horario" => $min_valor_perfil_horario,
            "max_valor_perfil_horario" => $max_valor_perfil_horario,
            "valores_mapa_calor_valores_perfil_horario_semanales" => $valores_mapa_calor_valores_perfil_horario_semanales,
            "valores_mapa_calor_valores_perfil_horario" => $valores_mapa_calor_valores_perfil_horario
        );
        return ($datos_grafica_valores_mapa_calor);
    }
?>
