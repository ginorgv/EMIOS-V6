<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones de información de comparación
    //


    // Devuelve la información de comparación de periodos de un sensor
    function dame_comparacion_valores_sensor_periodos($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_inicio_anterior_local_local = $parametros["fecha_inicio_anterior"];
        $cadena_fecha_inicio_posterior_local_local = $parametros["fecha_inicio_posterior"];
        $cadena_fecha_hora_inicio_anterior_local_local = $parametros["fecha_hora_inicio_anterior"];
        $cadena_fecha_hora_inicio_posterior_local_local = $parametros["fecha_hora_inicio_posterior"];
        $numero_dias_periodo = $parametros["numero_dias_periodo"];
        $numero_dias_periodo_anterior = $parametros["numero_dias_periodo_anterior"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
        }

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Se establece la hora de inicio de los periodos (si es necesario)
        if (($cadena_fecha_hora_inicio_anterior_local_local === NULL) &&
            ($cadena_fecha_hora_inicio_posterior_local_local === NULL))
        {
            $hora_inicio_periodos = "00:00:00";
            $cadena_fecha_hora_inicio_anterior_local_local = $cadena_fecha_inicio_anterior_local_local.", ".$hora_inicio_periodos;
            $cadena_fecha_hora_inicio_posterior_local_local = $cadena_fecha_inicio_posterior_local_local.", ".$hora_inicio_periodos;
        }
        else
        {
            $cadena_fecha_inicio_anterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_anterior_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_fecha_inicio_posterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
        }

        // Fechas iniciales de los periodos anterior y posterior
        $fecha_hora_inicio_anterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_anterior_local_local, $_SESSION["formato_fecha_hora_local"],  $zona_horaria);
        $fecha_hora_inicio_posterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_inicio_anterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_inicio_anterior_local, ZONA_HORARIA_UTC);
        $fecha_hora_inicio_posterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_inicio_posterior_local, ZONA_HORARIA_UTC);

        // Horas de separacion de periodos (diferencia entre las fechas iniciales)
        $duracion_periodo_posterior = new DateInterval("P".$numero_dias_periodo."D");
        if ($numero_dias_periodo_anterior === NULL)
        {
            $duracion_periodo_anterior = $duracion_periodo_posterior;
        }
        else
        {
            $duracion_periodo_anterior = new DateInterval("P".$numero_dias_periodo_anterior."D");
        }
        $separacion_periodos = $fecha_hora_inicio_anterior_utc->diff($fecha_hora_inicio_posterior_utc);
        $horas_separacion_periodos = new DateInterval('PT'.($separacion_periodos->days * 24 + $separacion_periodos->h).'H');

        // Se calculan las fechas finales de los periodos anterior y posterior
        $intervalo_segundo = new DateInterval("PT1S");
        $fecha_hora_fin_anterior_local = clone $fecha_hora_inicio_anterior_local;
        $fecha_hora_fin_anterior_local->add($duracion_periodo_anterior);
        $fecha_hora_fin_anterior_local->sub($intervalo_segundo);
        $fecha_hora_fin_anterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_fin_anterior_local, ZONA_HORARIA_UTC);
        $fecha_hora_fin_posterior_local = clone $fecha_hora_inicio_posterior_local;
        $fecha_hora_fin_posterior_local->add($duracion_periodo_posterior);
        $fecha_hora_fin_posterior_local->sub($intervalo_segundo);
        $fecha_hora_fin_posterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_fin_posterior_local, ZONA_HORARIA_UTC);

        // Conversión de fechas iniciales y finales a UTC
        $cadena_fecha_hora_inicio_anterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_inicio_posterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_anterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_fin_anterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_posterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_fin_posterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);

        // Comprobación de desfase entre días de la semana de las fechas iniciales de los periodos (si el intervalo es semana)
        $msg_aviso = "";
        $posible_calcular_evolucion_valores_diferencias = true;
        if ($intervalo_valores == INTERVALO_VALORES_SEMANA)
        {
            $dia_semana_fecha_hora_inicio_anterior_local = $fecha_hora_inicio_anterior_local->format("w");
            $dia_semana_fecha_hora_inicio_posterior_local = $fecha_hora_inicio_posterior_local->format("w");
            $desfase_dias_semana_fechas_horas_iniciales = $dia_semana_fecha_hora_inicio_anterior_local - $dia_semana_fecha_hora_inicio_posterior_local;
            if ($desfase_dias_semana_fechas_horas_iniciales != 0)
            {
                $posible_calcular_evolucion_valores_diferencias = false;
                $msg_aviso = $idiomas->_("No se pueden calcular los datos de evolución y diferencias de valores porque los periodos empiezan en diferentes días de la semana");
            }
        }

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor_informe(
            $clase_sensor,
            $campo,
            TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Flags para mostrar la gráfica de diferencias y el mapa de calor de diferencias
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $mostrar_grafica_diferencias = false;
                $mostrar_mapa_calor_diferencias = false;
                break;
            }
            default:
            {
                if ($posible_calcular_evolucion_valores_diferencias == true)
                {
                    $mostrar_grafica_diferencias = true;
                    $mostrar_mapa_calor_diferencias = true;
                }
                else
                {
                    $mostrar_grafica_diferencias = false;
                    $mostrar_mapa_calor_diferencias = false;
                }
                break;
            }
        }
        if ($tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)
        {
            $mostrar_mapa_calor_diferencias = false;
        }

        // Variables del informe
        $min_valor_anterior = INF;
        $max_valor_anterior = -INF;
        $min_valor_anterior_calculo_valores = INF;
        $max_valor_anterior_calculo_valores = -INF;
        $min_valor_posterior = INF;
        $max_valor_posterior = -INF;
        $min_valor_posterior_calculo_valores = INF;
        $max_valor_posterior_calculo_valores = -INF;
        $total_valor_anterior_calculo_valores = NULL;
        $total_valor_posterior_calculo_valores = NULL;
        $numero_valores_anterior_calculo_valores = 0;
        $numero_valores_posterior_calculo_valores = 0;

        // Nombres para las leyendas de las gráficas de valores
        $cadena_fecha_inicio_anterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_anterior_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_inicio_posterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_fin_anterior_local, $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_fin_posterior_local, $_SESSION["formato_fecha_local"]);
        $nombres_graficas_valores = new VectorDatos();
        $nombres_graficas_valores->anyade_etiqueta($idiomas->_("Periodo anterior")." (".$cadena_fecha_inicio_anterior_local_local." - ".$cadena_fecha_fin_anterior_local_local.")");
        $nombres_graficas_valores->anyade_etiqueta($idiomas->_("Periodo posterior")." (".$cadena_fecha_inicio_posterior_local_local." - ".$cadena_fecha_fin_posterior_local_local.")");
        $nombres_tooltips_graficas_valores = new VectorDatos();
        $nombres_tooltips_graficas_valores->anyade_etiqueta($idiomas->_("Periodo anterior"));
        $nombres_tooltips_graficas_valores->anyade_etiqueta($idiomas->_("Periodo posterior"));

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Información del ratio del periodo anterior
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor_anterior = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_anterior_base_datos_utc,
                $cadena_fecha_hora_fin_anterior_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                NULL);
        }

        // Valores del periodo anterior
        $consulta_periodo_anterior = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_anterior_base_datos_utc,
            $cadena_fecha_hora_fin_anterior_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            NULL,
            $parametros_extra_campo);
        $res_periodo_anterior = $bd_datos->ejecuta_consulta($consulta_periodo_anterior);
        if ($res_periodo_anterior == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodo_anterior."'");
        }
        $filas_periodo_anterior = array();
        $horario_verano_fecha_hora_inicial_periodo_anterior = NULL;
        $horario_verano_fecha_hora_inicial_adelantada_periodo_anterior = NULL;
        $claves_periodos_adelantados_valores_periodo_anterior = array();
        while ($fila_periodo_anterior = $res_periodo_anterior->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_anterior_base_datos_utc = $fila_periodo_anterior['fecha_hora'];
            $valor_anterior = $fila_periodo_anterior[$campo];
            if ($valor_anterior !== NULL)
            {
                $valor_anterior = (float) $valor_anterior;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor_anterior, $cadena_fecha_hora_anterior_base_datos_utc, $valor_anterior);
                }
            }
            if ($valor_anterior === NULL)
            {
                continue;
            }
            $fila_periodo_anterior[$campo] = $valor_anterior;

            // Se añade información de las fechas
            $fecha_hora_periodo_anterior_utc = convierte_cadena_a_fecha($cadena_fecha_hora_anterior_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_periodo_anterior_local = dame_fecha_hora_local($fecha_hora_periodo_anterior_utc);

            // Si el intervalo es día, semana o mes, establecer la hora a la primera hora del intervalo
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_periodo_anterior_local->modify('Monday this week');
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_periodo_anterior_local->modify('first day of this month');
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
            }

            // Fecha y hora adelantada (paras las gráficas y las diferencias)
            $fecha_hora_adelantada_utc = clone $fecha_hora_periodo_anterior_utc;
            $fecha_hora_adelantada_utc->add($horas_separacion_periodos);

            // Nota: Se ajusta la fecha y hora adelantada UTC según el horario de verano para que coincidan las fechas y horas locales
            // en ambos periodos
            $horario_verano_fecha_hora_periodo_anterior = dame_horario_verano_fecha_hora_utc($fecha_hora_periodo_anterior_utc, $zona_horaria);
            if ($horario_verano_fecha_hora_inicial_periodo_anterior === NULL)
            {
                $horario_verano_fecha_hora_inicial_periodo_anterior = $horario_verano_fecha_hora_periodo_anterior;
            }
            if (($horario_verano_fecha_hora_inicial_periodo_anterior == false) && ($horario_verano_fecha_hora_periodo_anterior == true))
            {
                $fecha_hora_adelantada_utc->add(new DateInterval('PT1H'));
            }
            else
            {
                if (($horario_verano_fecha_hora_inicial_periodo_anterior == true) && ($horario_verano_fecha_hora_periodo_anterior == false))
                {
                    $fecha_hora_adelantada_utc->sub(new DateInterval('PT1H'));
                }
            }
            $horario_verano_fecha_hora_adelantada_periodo_anterior = dame_horario_verano_fecha_hora_utc($fecha_hora_adelantada_utc, $zona_horaria);
            if ($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior === NULL)
            {
                $horario_verano_fecha_hora_inicial_adelantada_periodo_anterior = $horario_verano_fecha_hora_adelantada_periodo_anterior;
            }
            if (($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior == false) && ($horario_verano_fecha_hora_adelantada_periodo_anterior == true))
            {
                $fecha_hora_adelantada_utc->sub(new DateInterval('PT1H'));
            }
            else
            {
                if (($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior == true) && ($horario_verano_fecha_hora_adelantada_periodo_anterior == false))
                {
                    $fecha_hora_adelantada_utc->add(new DateInterval('PT1H'));
                }
            }
            $fecha_hora_adelantada_local = dame_fecha_hora_local($fecha_hora_adelantada_utc);
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_MES:
                {
                    // Si el intervalo de valores es mes hay que "redondear" la fecha hora adelantada para el cálculo correcto de las diferencias al día 1 del mes
                    $dia_mes_fecha_hora_adelantada_local = $fecha_hora_adelantada_local->format("d");
                    if ($dia_mes_fecha_hora_adelantada_local != 1)
                    {
                        $numero_dias_mes_fecha_hora_adelantada_local = cal_days_in_month(
                            CAL_GREGORIAN,
                            $fecha_hora_adelantada_local->format('m'),
                            $fecha_hora_adelantada_local->format('y'));
                        if ($dia_mes_fecha_hora_adelantada_local < ($numero_dias_mes_fecha_hora_adelantada_local / 2))
                        {
                            $fecha_hora_adelantada_local->setDate(
                                $fecha_hora_adelantada_local->format('Y'),
                                $fecha_hora_adelantada_local->format('m'),
                                1);
                        }
                        else
                        {
                            $fecha_hora_adelantada_local->modify('first day of next month');
                        }
                    }
                    $fecha_hora_adelantada_utc = dame_fecha_hora_utc($fecha_hora_adelantada_local);
                    break;
                }
            }
            $timestamp_fecha_hora_adelantada_utc = dame_timestamp_fecha_milisegundos($fecha_hora_adelantada_utc);

            // Se guarda la "clave" para determinar si hay valores en los mismos periodos (según el intervalo de valores)
            if (($mostrar_grafica_diferencias == true) || ($mostrar_mapa_calor_diferencias == true))
            {
                $clave_periodo_adelantado_periodo_anterior = dame_clave_periodo_comparacion_periodos($fecha_hora_adelantada_local, $intervalo_valores);
                array_push($claves_periodos_adelantados_valores_periodo_anterior, $clave_periodo_adelantado_periodo_anterior);
            }
            else
            {
                $clave_periodo_adelantado_periodo_anterior = NULL;
            }

            // Se añade la fila del periodo anterior
            $fila_periodo_anterior["fecha_hora_local"] = $fecha_hora_periodo_anterior_local;
            $fila_periodo_anterior["fecha_hora_adelantada_utc"] = $fecha_hora_adelantada_utc;
            $fila_periodo_anterior["fecha_hora_adelantada_local"] = $fecha_hora_adelantada_local;
            $fila_periodo_anterior["timestamp_fecha_hora_adelantada_utc"] = $timestamp_fecha_hora_adelantada_utc;
            $fila_periodo_anterior["clave_periodo_adelantado"] = $clave_periodo_adelantado_periodo_anterior;
            array_push($filas_periodo_anterior, $fila_periodo_anterior);
        }

        // Información del ratio del periodo posterior
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor_posterior = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_posterior_base_datos_utc,
                $cadena_fecha_hora_fin_posterior_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                NULL);
        }

        // Valores del periodo posterior
        $consulta_periodo_posterior = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_posterior_base_datos_utc,
            $cadena_fecha_hora_fin_posterior_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            NULL,
            $parametros_extra_campo);
        $res_periodo_posterior = $bd_datos->ejecuta_consulta($consulta_periodo_posterior);
        if ($res_periodo_posterior == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodo_posterior."'");
        }
        $filas_periodo_posterior = array();
        $claves_periodos_valores_periodo_posterior = array();
        while ($fila_periodo_posterior = $res_periodo_posterior->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_posterior_base_datos_utc = $fila_periodo_posterior['fecha_hora'];
            $valor_posterior = $fila_periodo_posterior[$campo];
            if ($valor_posterior !== NULL)
            {
                $valor_posterior = (float) $valor_posterior;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor_posterior, $cadena_fecha_hora_posterior_base_datos_utc, $valor_posterior);
                }
            }
            if ($valor_posterior === NULL)
            {
                continue;
            }
            $fila_periodo_posterior[$campo] = $valor_posterior;

            // Se añade información de las fechas
            $fecha_hora_periodo_posterior_utc = convierte_cadena_a_fecha($cadena_fecha_hora_posterior_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_periodo_posterior_local = dame_fecha_hora_local($fecha_hora_periodo_posterior_utc);

            // Si el intervalo es día, semana o mes, establecer la hora a la primera hora del intervalo
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_periodo_posterior_local->modify('Monday this week');
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_periodo_posterior_local->modify('first day of this month');
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
            }

            // Timestamp de la fecha
            $timestamp_fecha_hora_periodo_posterior_utc = dame_timestamp_fecha_milisegundos($fecha_hora_periodo_posterior_utc);

            // Se guarda la "clave" para determinar si hay valores en los mismos periodos (según el intervalo de valores)
            if (($mostrar_grafica_diferencias == true) ||($mostrar_mapa_calor_diferencias == true))
            {
                $clave_periodo_periodo_posterior = dame_clave_periodo_comparacion_periodos($fecha_hora_periodo_posterior_local, $intervalo_valores);
                array_push($claves_periodos_valores_periodo_posterior, $clave_periodo_periodo_posterior);
            }
            else
            {
                $clave_periodo_periodo_posterior = NULL;
            }

            // Se añade la fila del periodo posterior
            $fila_periodo_posterior["fecha_hora_utc"] = $fecha_hora_periodo_posterior_utc;
            $fila_periodo_posterior["fecha_hora_local"] = $fecha_hora_periodo_posterior_local;
            $fila_periodo_posterior["timestamp_fecha_hora_utc"] = $timestamp_fecha_hora_periodo_posterior_utc;
            $fila_periodo_posterior["clave_periodo"] = $clave_periodo_periodo_posterior;
            array_push($filas_periodo_posterior, $fila_periodo_posterior);
        }

        // Claves de periodos con valores en ambos periodos
        if ($posible_calcular_evolucion_valores_diferencias == true)
        {
            $claves_periodos_valores_ambos_periodos = array_intersect(
                $claves_periodos_adelantados_valores_periodo_anterior,
                $claves_periodos_valores_periodo_posterior);
        }
        else
        {
            $claves_periodos_valores_ambos_periodos = array();
        }

        // Formato de fechas locales (para los tooltips)
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            case INTERVALO_VALORES_DIA:
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_local"];
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
            }
        }

        // Se recorren los valores del periodo anterior
        $fecha_hora_inicio_valores_anterior_local = NULL;
        $fecha_hora_adelantada_local = NULL;
        $datos_periodo_anterior = new VectorDatos();
        $valores_periodo_anterior = array();
        $timestamp_fecha_hora_adelantada_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica_anterior = 0;
        foreach ($filas_periodo_anterior as $fila_periodo_anterior)
        {
            // Fecha y valor
            $fecha_hora_adelantada_utc = $fila_periodo_anterior["fecha_hora_adelantada_utc"];
            $fecha_hora_adelantada_local = $fila_periodo_anterior["fecha_hora_adelantada_local"];
            $valor_anterior = $fila_periodo_anterior[$campo];
            if ($valor_anterior > $max_valor_anterior)
            {
                $max_valor_anterior = $valor_anterior;
            }
            if ($valor_anterior < $min_valor_anterior)
            {
                $min_valor_anterior = $valor_anterior;
            }

            // Fecha de inicio y fin de valores
            if ($fecha_hora_inicio_valores_anterior_local === NULL)
            {
                $fecha_hora_inicio_valores_anterior_local = $fecha_hora_adelantada_local;
            }
            $fecha_hora_fin_valores_anterior_local = $fecha_hora_adelantada_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_adelantada_utc = $fila_periodo_anterior["timestamp_fecha_hora_adelantada_utc"];
            $timestamp_fecha_hora_adelantada_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica_anterior > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_adelantada_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_adelantada_utc - $timestamp_fecha_hora_adelantada_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica_anterior = 0;
                    $datos_periodo_anterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_adelantada_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_adelantada_anterior_utc = $timestamp_fecha_hora_adelantada_utc;
            $numero_puntos_seguidos_grafica_anterior += 1;

            // Se añade el valor (para la gráfica de valores)
            $datos_periodo_anterior->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_adelantada_utc,
                $valor_anterior,
                convierte_fecha_a_cadena($fila_periodo_anterior["fecha_hora_local"], $formato_fecha_hora_local));

            // Se añade el valor (para las gráficas de diferencias)
            array_push($valores_periodo_anterior, array(
                "fecha_hora_adelantada_utc" => $fecha_hora_adelantada_utc,
                "fecha_hora_adelantada_local" => $fecha_hora_adelantada_local,
                "valor" => $valor_anterior,
                "clave_periodo_adelantado" => $fila_periodo_anterior["clave_periodo_adelantado"]));

            // Valor anterior modificado
            $valor_anterior_modificado = modifica_valor_campo_clase_sensor_informe(
                $clase_sensor,
                $campo,
                $valor_anterior,
                TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);

            // Valores máximo y mínimo (con valores en ambos periodos)
            if (in_array($fila_periodo_anterior["clave_periodo_adelantado"], $claves_periodos_valores_ambos_periodos) == true)
            {
                if ($valor_anterior_modificado > $max_valor_anterior_calculo_valores)
                {
                    $max_valor_anterior_calculo_valores = $valor_anterior_modificado;
                }
                if ($valor_anterior_modificado < $min_valor_anterior_calculo_valores)
                {
                    $min_valor_anterior_calculo_valores = $valor_anterior_modificado;
                }
                $numero_valores_anterior_calculo_valores += 1;
            }

            // Valor total (sólo si la fecha es anterior a la fecha de fin del periodo posterior)
            if ($fecha_hora_adelantada_utc <= $fecha_hora_periodo_posterior_utc)
            {
                if ($total_valor_anterior_calculo_valores === NULL)
                {
                    $total_valor_anterior_calculo_valores = 0.0;
                }
                $total_valor_anterior_calculo_valores += $valor_anterior_modificado;
            }
        }

        // Si no hay fecha de inicio de valores es que no hay datos
        if ($fecha_hora_inicio_valores_anterior_local === NULL)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recorren los valores del periodo posterior
        $fecha_hora_inicio_valores_posterior_local = NULL;
        $fecha_hora_periodo_posterior_local = NULL;
        $datos_periodo_posterior = new VectorDatos();
        $valores_periodo_posterior = array();
        $timestamp_fecha_hora_periodo_posterior_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica_posterior = 0;
        foreach ($filas_periodo_posterior as $fila_periodo_posterior)
        {
            // Fecha y valor
            $fecha_hora_periodo_posterior_utc = $fila_periodo_posterior["fecha_hora_utc"];
            $fecha_hora_periodo_posterior_local = $fila_periodo_posterior["fecha_hora_local"];
            $valor_posterior = (float) $fila_periodo_posterior[$campo];
            if ($valor_posterior > $max_valor_posterior)
            {
                $max_valor_posterior = $valor_posterior;
            }
            if ($valor_posterior < $min_valor_posterior)
            {
                $min_valor_posterior = $valor_posterior;
            }

            // Fecha de inicio y fin de valores
            if ($fecha_hora_inicio_valores_posterior_local === NULL)
            {
                $fecha_hora_inicio_valores_posterior_local = $fecha_hora_periodo_posterior_local;
            }
            $fecha_hora_fin_valores_posterior_local = $fecha_hora_periodo_posterior_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_periodo_posterior_utc = $fila_periodo_posterior["timestamp_fecha_hora_utc"];
            $timestamp_fecha_hora_periodo_posterior_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica_posterior > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_periodo_posterior_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_periodo_posterior_utc - $timestamp_fecha_hora_periodo_posterior_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica_posterior = 0;
                    $datos_periodo_posterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_periodo_posterior_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_periodo_posterior_anterior_utc = $timestamp_fecha_hora_periodo_posterior_utc;
            $numero_puntos_seguidos_grafica_posterior += 1;

            // Se añade el valor (para la gráfica de valores)
            $datos_periodo_posterior->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_periodo_posterior_utc,
                $valor_posterior,
                convierte_fecha_a_cadena($fecha_hora_periodo_posterior_local, $formato_fecha_hora_local));

            // Se guarda el valor (para las gráficas de diferencias)
            array_push($valores_periodo_posterior, array(
                "fecha_hora_utc" => $fecha_hora_periodo_posterior_utc,
                "fecha_hora_local" => $fecha_hora_periodo_posterior_local,
                "valor" => $valor_posterior,
                "clave_periodo" => $fila_periodo_posterior["clave_periodo"]));

            // Valor posterior modificado
            $valor_posterior_modificado = modifica_valor_campo_clase_sensor_informe(
                $clase_sensor,
                $campo,
                $valor_posterior,
                TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);

            // Valores máximo y mínimo (con valores en ambos periodos)
            if (in_array($fila_periodo_posterior["clave_periodo"], $claves_periodos_valores_ambos_periodos) == true)
            {
                if ($valor_posterior_modificado > $max_valor_posterior_calculo_valores)
                {
                    $max_valor_posterior_calculo_valores = $valor_posterior_modificado;
                }
                if ($valor_posterior_modificado < $min_valor_posterior_calculo_valores)
                {
                    $min_valor_posterior_calculo_valores = $valor_posterior_modificado;
                }
                $numero_valores_posterior_calculo_valores += 1;
            }

            // Valor total
            if ($total_valor_posterior_calculo_valores === NULL)
            {
                $total_valor_posterior_calculo_valores = 0.0;
            }
            $total_valor_posterior_calculo_valores += $valor_posterior_modificado;
        }

        // Si no hay fecha de inicio de valores es que no hay datos
        if ($fecha_hora_inicio_valores_posterior_local === NULL)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Periodo de tiempo de valores del periodo posterior
        if ($fecha_hora_fin_valores_posterior_local !== NULL)
        {
            $fecha_hora_fin_valores_posterior_local_aux = clone $fecha_hora_fin_valores_posterior_local;
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_CUARTOHORA:
                {
                    $fecha_hora_fin_valores_posterior_local_aux->modify('+900 seconds');
                    break;
                }
                case INTERVALO_VALORES_HORA:
                {
                    $fecha_hora_fin_valores_posterior_local_aux->modify('+1 hour');
                    break;
                }
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_fin_valores_posterior_local_aux->modify('+1 day');
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_fin_valores_posterior_local_aux->modify('+1 week');
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_fin_valores_posterior_local_aux->modify('+1 month');
                    break;
                }
            }
            $periodo_tiempo_valores_posterior = $fecha_hora_inicio_valores_posterior_local->diff($fecha_hora_fin_valores_posterior_local_aux);
        }

        // Rango de fechas de los periodos
        $min_fecha_hora_valores_periodos_local = clone min(array($fecha_hora_inicio_valores_anterior_local, $fecha_hora_inicio_valores_posterior_local));
        $max_fecha_hora_valores_periodos_local = clone max(array($fecha_hora_fin_valores_anterior_local, $fecha_hora_fin_valores_posterior_local));

        // Se recupera el intervalo de fechas donde hay valores en los dos periodos
        $min_fecha_hora_valores_posterior_local = max(array($fecha_hora_inicio_valores_anterior_local, $fecha_hora_inicio_valores_posterior_local));
        $min_fecha_hora_valores_anterior_local = clone $min_fecha_hora_valores_posterior_local;
        $min_fecha_hora_valores_anterior_local->sub($horas_separacion_periodos);
        $max_fecha_hora_valores_posterior_local = min(array($fecha_hora_fin_valores_anterior_local, $fecha_hora_fin_valores_posterior_local));
        $max_fecha_hora_valores_anterior_local = clone $max_fecha_hora_valores_posterior_local;
        $max_fecha_hora_valores_anterior_local->sub($horas_separacion_periodos);

        // Se calculan los datos de las diferencias de valores (en el intervalo de fechas donde hay valores en los dos periodos)
        $valores_diferencias_periodos = array();
        $valores_diferencias_acumuladas_periodos = array();
        $min_diferencia = INF;
        $max_diferencia = -INF;
        $min_diferencia_acumulada = INF;
        $max_diferencia_acumulada = -INF;
        $indice_periodo_anterior = 0;
        $indice_periodo_posterior = 0;

        // Si hay que mostrar la gráfica de diferencias (sólo si hay valores)
        if ($mostrar_grafica_diferencias == true)
        {
            while (($indice_periodo_anterior < count($valores_periodo_anterior)) &&
                ($indice_periodo_posterior < count($valores_periodo_posterior)))
            {
                $clave_periodo_adelantado_periodo_anterior = $valores_periodo_anterior[$indice_periodo_anterior]["clave_periodo_adelantado"];
                $clave_periodo_periodo_posterior = $valores_periodo_posterior[$indice_periodo_posterior]["clave_periodo"];

                // Se añade el valor de diferencia de valores si es la misma clave de periodo (dependiendo del intervalo de valores)
                $anyadir_diferencia_valores_periodos = ($clave_periodo_adelantado_periodo_anterior == $clave_periodo_periodo_posterior);
                if ($anyadir_diferencia_valores_periodos == true)
                {
                    $fecha_hora_periodo_posterior_utc = $valores_periodo_posterior[$indice_periodo_posterior]["fecha_hora_utc"];
                    $valor_periodo_posterior = modifica_valor_campo_clase_sensor_informe(
                        $clase_sensor,
                        $campo,
                        $valores_periodo_posterior[$indice_periodo_posterior]["valor"],
                        TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);
                    $valor_periodo_anterior = modifica_valor_campo_clase_sensor_informe(
                        $clase_sensor,
                        $campo,
                        $valores_periodo_anterior[$indice_periodo_anterior]["valor"],
                        TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);

                    $diferencia_valores_periodos = $valor_periodo_posterior - $valor_periodo_anterior;
                    if ($diferencia_valores_periodos > $max_diferencia)
                    {
                        $max_diferencia = $diferencia_valores_periodos;
                    }
                    if ($diferencia_valores_periodos < $min_diferencia)
                    {
                        $min_diferencia = $diferencia_valores_periodos;
                    }
                    array_push($valores_diferencias_periodos, array(
                        "fecha_hora_utc" => $fecha_hora_periodo_posterior_utc,
                        "valor" => $diferencia_valores_periodos));

                    // Diferencias acumuladas
                    if ($campo_incremental == true)
                    {
                        $suma_diferencias_valores_periodos += $diferencia_valores_periodos;
                        if ($suma_diferencias_valores_periodos > $max_diferencia_acumulada)
                        {
                            $max_diferencia_acumulada = $suma_diferencias_valores_periodos;
                        }
                        if ($suma_diferencias_valores_periodos < $min_diferencia_acumulada)
                        {
                            $min_diferencia_acumulada = $suma_diferencias_valores_periodos;
                        }
                        array_push($valores_diferencias_acumuladas_periodos, array(
                            "fecha_hora_utc" => $fecha_hora_periodo_posterior_utc,
                            "suma_diferencias_valores" => $suma_diferencias_valores_periodos));
                    }

                    $indice_periodo_anterior += 1;
                    $indice_periodo_posterior += 1;
                }
                else
                {
                    if ($clave_periodo_adelantado_periodo_anterior < $clave_periodo_periodo_posterior)
                    {
                        $indice_periodo_anterior += 1;
                    }
                    else
                    {
                        $indice_periodo_posterior += 1;
                    }
                }
            }
        }

        // Nombres para la leyenda de la gráfica de diferencias
        $nombres_grafica_diferencias = new VectorDatos();
        $nombres_grafica_diferencias->anyade_etiqueta($idiomas->_("Periodo posterior")." - ".$idiomas->_("periodo anterior"));

        // Datos para las gráficas de diferencias (y diferencias acumuladas) y el mapa de calor de las diferencias (por día)
        $datos_diferencias_periodos = new VectorDatos();
        $datos_diferencias_acumuladas_periodos = new VectorDatos();
        $valores_mapa_calor_diferencias_periodos = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $timestamp_fecha_hora_diferencias_periodos_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        for ($i = 0; $i < count($valores_diferencias_periodos); $i++)
        {
            // Fecha y valor
            $fecha_hora_diferencias_periodos_utc = $valores_diferencias_periodos[$i]["fecha_hora_utc"];
            $valor_diferencias_periodos = $valores_diferencias_periodos[$i]["valor"];

            // Diferencias acumuladas
            if ($campo_incremental == true)
            {
                $valor_diferencias_acumuladas_periodos = $valores_diferencias_acumuladas_periodos[$i]["suma_diferencias_valores"];
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_diferencias_periodos_utc = dame_timestamp_fecha_milisegundos($fecha_hora_diferencias_periodos_utc);
            $timestamp_fecha_hora_diferencias_periodos_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_diferencias_periodos_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_diferencias_periodos_utc - $timestamp_fecha_hora_diferencias_periodos_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_diferencias_periodos->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_periodos_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_diferencias_acumuladas_periodos->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_periodos_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_diferencias_periodos_anterior_utc = $timestamp_fecha_hora_diferencias_periodos_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade la diferencia
            $datos_diferencias_periodos->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_periodos_utc, $valor_diferencias_periodos);

            // Diferencias acumuladas
            if ($campo_incremental == true)
            {
                $datos_diferencias_acumuladas_periodos->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_periodos_anterior_utc, $valor_diferencias_acumuladas_periodos);
            }

            // Mapa de calor de diferencias
            if ($mostrar_mapa_calor_diferencias == true)
            {
                $fecha_hora_diferencias_periodos_local = dame_fecha_hora_local($fecha_hora_diferencias_periodos_utc);
                $valores_mapa_calor_diferencias_periodos->anyade_valor_fecha_hora($fecha_hora_diferencias_periodos_local, $valor_diferencias_periodos);
            }
        }

        // Fechas mínima y máxima
        // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
        if ($min_fecha_hora_valores_periodos_local == $max_fecha_hora_valores_periodos_local)
        {
            $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
            $min_fecha_hora_valores_periodos_local->sub($intervalo_fecha);
            $max_fecha_hora_valores_periodos_local->add($intervalo_fecha);
        }
        $cadena_min_fecha_hora_jqplot_local = convierte_fecha_a_cadena($min_fecha_hora_valores_periodos_local, FORMATO_FECHA_HORA_JQPLOT);
        $cadena_max_fecha_hora_jqplot_local = convierte_fecha_a_cadena($max_fecha_hora_valores_periodos_local, FORMATO_FECHA_HORA_JQPLOT);

        // Valores mínimo y máximo
        $min_valor = min(array($min_valor_anterior, $min_valor_posterior));
        $max_valor = max(array($max_valor_anterior, $max_valor_posterior));

        // Gráficas
        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_periodo_anterior->dame_datos());
        $grafica_valores->anyade_dato($datos_periodo_posterior->dame_datos());

        $grafica_diferencias = new VectorDatos();
        $grafica_diferencias->anyade_dato($datos_diferencias_periodos->dame_datos());
        $grafica_diferencias_acumuladas = new VectorDatos();
        $grafica_diferencias_acumuladas->anyade_dato($datos_diferencias_acumuladas_periodos->dame_datos());

        // Tipo de líneas de valores y número de decimales de valores
        $tipo_lineas_valores = dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
            $intervalo_valores,
            $clase_sensor,
            $id_sensor,
            $campo);

        // Número de decimales de valores y unidad de medida
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida);
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Tabla de evolución de valores
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                $datos_tabla_evolucion_valores = NULL;
                break;
            }
            default:
            {
                // Diferencia de valores totales
                $texto_porcentaje_diferencia_valores_totales = "";
                if ($total_valor_posterior_calculo_valores == $total_valor_anterior_calculo_valores)
                {
                    $diferencia_valores_totales = 0;
                    $porcentaje_diferencia_valores_totales = 0;
                    $texto_diferencia_valores_totales_sin_unidad = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0";
                    $texto_diferencia_valores_totales = $texto_diferencia_valores_totales_sin_unidad.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_porcentaje_diferencia_valores_totales .= "(0 "."%".")";
                        $texto_diferencia_valores_totales .= " ".$texto_porcentaje_diferencia_valores_totales;
                    }
                }
                else
                {
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES)
                    {
                        $total_valor_anterior_calculo_valores /= $numero_valores_anterior_calculo_valores;
                        $total_valor_posterior_calculo_valores /= $numero_valores_posterior_calculo_valores;
                    }

                    $diferencia_valores_totales = abs($total_valor_posterior_calculo_valores - $total_valor_anterior_calculo_valores);
                    $cadena_diferencia_valores_totales = formatea_numero($diferencia_valores_totales, $numero_decimales_valores);
                    $porcentaje_diferencia_valores_totales = dame_porcentaje_valor_referencia($total_valor_posterior_calculo_valores, $total_valor_anterior_calculo_valores);
                    $cadena_porcentaje_diferencia_valores_totales = formatea_numero($porcentaje_diferencia_valores_totales, $numero_decimales_valores);

                    if ($total_valor_posterior_calculo_valores > $total_valor_anterior_calculo_valores)
                    {
                        $texto_diferencia_valores_totales_sin_unidad = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_valores_totales;
                        $texto_diferencia_valores_totales = $texto_diferencia_valores_totales_sin_unidad.$cadena_unidad_medida;
                        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $texto_porcentaje_diferencia_valores_totales .= "(+".$cadena_porcentaje_diferencia_valores_totales." "."%".")";
                            $texto_diferencia_valores_totales .= " ".$texto_porcentaje_diferencia_valores_totales;
                        }
                    }
                    else
                    {
                        $texto_diferencia_valores_totales_sin_unidad = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_valores_totales;
                        $texto_diferencia_valores_totales = $texto_diferencia_valores_totales_sin_unidad.$cadena_unidad_medida;
                        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $texto_porcentaje_diferencia_valores_totales = "(-".$cadena_porcentaje_diferencia_valores_totales." "."%".")";
                            $texto_diferencia_valores_totales .= " ".$texto_porcentaje_diferencia_valores_totales;
                        }
                        $diferencia_valores_totales *= -1;
                        $porcentaje_diferencia_valores_totales *= -1;
                    }
                }

                // Sólo si hay valores solapados en ambos periodos
                $hay_valores_solapados_periodos = (count($claves_periodos_valores_ambos_periodos) > 0);
                if ($hay_valores_solapados_periodos == true)
                {
                    // Diferencia de máximos de valores
                    if ($max_valor_posterior_calculo_valores == $max_valor_anterior_calculo_valores)
                    {
                        $texto_diferencia_valores_maximos = "<i class='icon-sort color-gris-claro'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                            "0".$cadena_unidad_medida;
                        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $texto_diferencia_valores_maximos .= " (0 "."%".")";
                        }
                    }
                    else
                    {
                        $diferencia_valores_maximos = abs($max_valor_posterior_calculo_valores - $max_valor_anterior_calculo_valores);
                        $cadena_diferencia_valores_maximos = formatea_numero($diferencia_valores_maximos, $numero_decimales_valores);
                        $porcentaje_diferencia_valores_maximos = dame_porcentaje_valor_referencia($max_valor_posterior_calculo_valores, $max_valor_anterior_calculo_valores);
                        $cadena_porcentaje_diferencia_valores_maximos = formatea_numero($porcentaje_diferencia_valores_maximos, $numero_decimales_valores);

                        if ($max_valor_posterior_calculo_valores > $max_valor_anterior_calculo_valores)
                        {
                            $texto_diferencia_valores_maximos = "<i class='icon-caret-up color-rojo'>".
                                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                                $cadena_diferencia_valores_maximos.$cadena_unidad_medida;
                            if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                            {
                                $texto_diferencia_valores_maximos .= " (+".$cadena_porcentaje_diferencia_valores_maximos." "."%".")";
                            }
                        }
                        else
                        {
                            $texto_diferencia_valores_maximos = "<i class='icon-caret-down color-verde'>".
                                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                                $cadena_diferencia_valores_maximos.$cadena_unidad_medida;
                            if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                            {
                                $texto_diferencia_valores_maximos .= " (-".$cadena_porcentaje_diferencia_valores_maximos." "."%".")";
                            }
                            $diferencia_valores_maximos *= -1;
                            $porcentaje_diferencia_valores_maximos *= -1;
                        }
                    }

                    // Diferencia de mínimos de valores
                    if ($min_valor_posterior_calculo_valores == $min_valor_anterior_calculo_valores)
                    {
                        $texto_diferencia_valores_minimos = "<i class='icon-sort color-gris-claro'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                            "0".$cadena_unidad_medida;
                        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $texto_diferencia_valores_minimos .= " (0 "."%".")";
                        }
                    }
                    else
                    {
                        $diferencia_valores_minimos = abs($min_valor_posterior_calculo_valores - $min_valor_anterior_calculo_valores);
                        $cadena_diferencia_valores_minimos = formatea_numero($diferencia_valores_minimos, $numero_decimales_valores);
                        $porcentaje_diferencia_valores_minimos = dame_porcentaje_valor_referencia($min_valor_posterior_calculo_valores, $min_valor_anterior_calculo_valores);
                        $cadena_porcentaje_diferencia_valores_minimos = formatea_numero($porcentaje_diferencia_valores_minimos, $numero_decimales_valores);

                        if ($min_valor_posterior_calculo_valores > $min_valor_anterior_calculo_valores)
                        {
                            $texto_diferencia_valores_minimos = "<i class='icon-caret-up color-rojo'>".
                                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                                $cadena_diferencia_valores_minimos.$cadena_unidad_medida;
                            if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                            {
                                $texto_diferencia_valores_minimos .= " (+".$cadena_porcentaje_diferencia_valores_minimos." "."%".")";
                            }
                        }
                        else
                        {
                            $texto_diferencia_valores_minimos = "<i class='icon-caret-down color-verde'>".
                                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                                $cadena_diferencia_valores_minimos.$cadena_unidad_medida;
                            if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                            {
                                $texto_diferencia_valores_minimos .= " (-".$cadena_porcentaje_diferencia_valores_minimos." "."%".")";
                            }
                            $diferencia_valores_minimos *= -1;
                            $porcentaje_diferencia_valores_minimos *= -1;
                        }
                    }
                }
                else
                {
                    $texto_diferencia_valores_maximos = $idiomas->_("ND");
                    $texto_diferencia_valores_minimos = $idiomas->_("ND");
                }

                // Tabla de evolución de valores
                $params_tabla_evolucion_valores = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVOLUCION_VALORES,
                    "generar_valores_xml" => true
                );
                $titulo_tabla_evolucion_valores = $idiomas->_("Evolución de valores");
                $segundos_duracion_periodos = dame_segundos_intervalo_tiempo($duracion_periodo_posterior);
                $segundos_periodo_tiempo_valores_posterior = dame_segundos_intervalo_tiempo($periodo_tiempo_valores_posterior);
                if ($segundos_duracion_periodos > $segundos_periodo_tiempo_valores_posterior)
                {
                    $texto_periodo = dame_texto_periodo($segundos_periodo_tiempo_valores_posterior);
                    $titulo_tabla_evolucion_valores .= " (".$texto_periodo.")";
                }
                else
                {
                    $texto_periodo = "";
                }
                $tabla_evolucion_valores = new TablaDatos(
                    "tabla-evolucion-valores-comparacion-periodos",
                    $titulo_tabla_evolucion_valores,
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_evolucion_valores
                );
                switch ($tipo_valores_campo)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        $titulo_cabecera_total_valor = $idiomas->_("Media");
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        $titulo_cabecera_total_valor = $idiomas->_("Total");
                        break;
                    }
                }
                $cabecera_tabla_evolucion_valores = array(
                    $titulo_cabecera_total_valor,
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo")
                );
                $tabla_evolucion_valores->anyade_cabecera("", $cabecera_tabla_evolucion_valores);
                $fila_valores = array(
                    $texto_diferencia_valores_totales,
                    $texto_diferencia_valores_maximos,
                    $texto_diferencia_valores_minimos);
                $tabla_evolucion_valores->anyade_fila("", $fila_valores);
                $datos_tabla_evolucion_valores = $tabla_evolucion_valores->dame_tabla();
                break;
            }
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_diferencia == INF)
        {
            $min_diferencia = "ND";
        }
        if ($max_diferencia == -INF)
        {
            $max_diferencia = "ND";
        }
        if ($min_diferencia_acumulada == INF)
        {
            $min_diferencia_acumulada = "ND";
        }
        if ($max_diferencia_acumulada == -INF)
        {
            $max_diferencia_acumulada = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "msg_aviso" => $msg_aviso,
            "min_fecha" => $cadena_min_fecha_hora_jqplot_local,
            "max_fecha" => $cadena_max_fecha_hora_jqplot_local,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "etiquetas_valores" => $nombres_graficas_valores->dame_datos(),
            "etiquetas_tooltips_valores" => $nombres_tooltips_graficas_valores->dame_datos(),
            "tipo_lineas_valores" => $tipo_lineas_valores,
            "hay_valores_solapados_periodos" => $hay_valores_solapados_periodos,
            "tabla_evolucion_valores" => $datos_tabla_evolucion_valores,
            "texto_periodo" => $texto_periodo,
            "texto_diferencia_valores_totales_sin_unidad" => $texto_diferencia_valores_totales_sin_unidad,
            "texto_porcentaje_diferencia_valores_totales" => $texto_porcentaje_diferencia_valores_totales,
            "diferencia_valores_totales" => $diferencia_valores_totales,
            "porcentaje_diferencia_valores_totales" => $porcentaje_diferencia_valores_totales,
            "etiquetas_diferencias" => $nombres_grafica_diferencias->dame_datos(),
            "grafica_diferencias" => $grafica_diferencias->dame_datos(),
            "min_diferencia" => $min_diferencia,
            "max_diferencia" => $max_diferencia,
            "campo_incremental" => $campo_incremental,
            "grafica_diferencias_acumuladas" => $grafica_diferencias_acumuladas->dame_datos(),
            "min_diferencia_acumulada" => $min_diferencia_acumulada,
            "max_diferencia_acumulada" => $max_diferencia_acumulada,
            "dias_mapa_calor_diferencias" => $valores_mapa_calor_diferencias_periodos->dame_dias(),
            "datos_mapa_calor_diferencias" => $valores_mapa_calor_diferencias_periodos->dame_datos(),
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "cadena_fecha_inicio_anterior" => $cadena_fecha_inicio_anterior_local_local,
            "cadena_fecha_inicio_posterior" => $cadena_fecha_inicio_posterior_local_local,
            "fecha_hora_inicio_valores_posterior" => $fecha_hora_inicio_valores_posterior_local,
            "fecha_hora_fin_valores_posterior" => $fecha_hora_fin_valores_posterior_local);
        return ($resultado);
    }


    // Devuelve la información de comparación con perfil horario de un sensor
    function dame_comparacion_valores_sensor_perfil_horario($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $cadena_fecha_inicio_perfil_horario_local_local = $parametros["fecha_inicio_perfil_horario"];
        $cadena_fecha_fin_perfil_horario_local_local = $parametros["fecha_fin_perfil_horario"];
        $tipo_perfil_horario = $parametros["tipo_perfil_horario"];
        $agrupaciones_dias_semana = json_decode($parametros["agrupaciones_dias_semana"], true);
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Campo y parámetros extra
        $campo_parametros_extra = $campo;
        if ($parametros_extra_campo != "")
        {
            $campo_parametros_extra .= SEPARADOR_CAMPO_PARAMETROS_EXTRA.$parametros_extra_campo;
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_inicio_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_inicio_perfil_horario_local_local, $_SESSION["formato_fecha_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_fin_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_fin_perfil_horario_local_local, $_SESSION["formato_fecha_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_inicio_perfil_horario_funciones_utc = convierte_formato_fecha($cadena_fecha_inicio_perfil_horario_local_utc, $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);
        $cadena_fecha_fin_perfil_horario_funciones_utc = convierte_formato_fecha($cadena_fecha_fin_perfil_horario_local_utc, $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);

        // Parámetros de la función a llamar
        $cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana($agrupaciones_dias_semana);
        $cadena_horario_semanal = dame_cadena_horario_semanal($horario_semanal);
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_PERFIL_HORARIO,
                "id_red" => $_SESSION["id_red"],
                "clase_sensor" => $clase_sensor,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => $campo,
                "parametros_extra_campo" => $parametros_extra_campo,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "intervalo_valores" => $intervalo_valores,
                "fecha_inicio_perfil_horario" => $cadena_fecha_inicio_perfil_horario_funciones_utc,
                "fecha_fin_perfil_horario" => $cadena_fecha_fin_perfil_horario_funciones_utc,
                "tipo_perfil_horario" => $tipo_perfil_horario,
                "agrupaciones_dias_semana" => $cadena_agrupaciones_dias_semana,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "incluir_valores_reales" => VALOR_SI
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de valores reales y simulados
        $valores_reales_simulados_perfil_horario = $resultado_funcion_externa["valores_reales_simulados_perfil_horario"];
        $numero_valores_reales_simulados_perfil_horario = count($valores_reales_simulados_perfil_horario);

        // Si no hay datos no se hace nada
        if ($numero_valores_reales_simulados_perfil_horario == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se crea el resultado del informe

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Unidad de medida y número de decimales
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores de las gráficas (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_graficas = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Valores mínimos y máximos de la comparación con el perfil horario
        $min_valor = INF;
        $max_valor = -INF;
        $min_diferencia = INF;
        $max_diferencia = -INF;
        $min_diferencia_acumulada = INF;
        $max_diferencia_acumulada = -INF;

        // Gráficas de línea base
        $datos_valores_reales_perfil_horario = new VectorDatos();
        $datos_valores_simulados_perfil_horario = new VectorDatos();
        $datos_banda_valores_reales_perfil_horario = new VectorDatos();
        $datos_banda_valores_simulados_perfil_horario = new VectorDatos();
        $datos_diferencias_valores_reales_simulados_perfil_horario = new VectorDatos();
        $datos_diferencias_acumuladas_valores_reales_simulados_perfil_horario = new VectorDatos();
        $suma_diferencias_valores_reales_simulados_perfil_horario = 0;
        $valores_mapa_calor_diferencias_valores_reales_simulados_perfil_horario = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $timestamp_fecha_hora_valores_perfil_horario_anterior = NULL;
        $numero_puntos_seguidos_grafica = 0;
        for ($i = 0; $i < $numero_valores_reales_simulados_perfil_horario; $i++)
        {
            $valor_real_simulado_perfil_horario = $valores_reales_simulados_perfil_horario[$i];
            $cadena_fecha_valor_real_simulado_perfil_horario_funciones_utc = $valor_real_simulado_perfil_horario["fecha_hora_utc"];
            $cadena_fecha_valor_real_simulado_perfil_horario_funciones_local = $valor_real_simulado_perfil_horario["fecha_hora_local"];
            $valor_real_perfil_horario = $valor_real_simulado_perfil_horario["valor_real"];
            $valor_simulado_perfil_horario = $valor_real_simulado_perfil_horario["valor_simulado"];
            $error_estandar_valor_simulado = $valor_real_simulado_perfil_horario["error_estandar_linea_base"];

            if ($valor_real_perfil_horario > $max_valor)
            {
                $max_valor = $valor_real_perfil_horario;
            }
            if ($valor_simulado_perfil_horario > $max_valor)
            {
                $max_valor = $valor_simulado_perfil_horario;
            }
            if ($valor_real_perfil_horario < $min_valor)
            {
                $min_valor = $valor_real_perfil_horario;
            }
            if ($valor_simulado_perfil_horario < $min_valor)
            {
                $min_valor = $valor_simulado_perfil_horario;
            }

            $timestamp_fecha_hora_valores_perfil_horario = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_valor_real_simulado_perfil_horario_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valores_perfil_horario -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_graficas !== NULL) && ($timestamp_fecha_hora_valores_perfil_horario_anterior !== NULL))
            {
                $segundos_entre_valores_perfil_horario = ($timestamp_fecha_hora_valores_perfil_horario - $timestamp_fecha_hora_valores_perfil_horario_anterior) / 1000;
                if ($segundos_entre_valores_perfil_horario > $segundos_maximos_entre_valores_graficas)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_valores_reales_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario_anterior + 1, NULL);
                    $datos_valores_simulados_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario_anterior + 1, NULL);
                    $datos_banda_valores_simulados_perfil_horario->anyade_tupla_pareja_datos(NULL, NULL);
                    $datos_diferencias_valores_reales_simulados_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario_anterior + 1, NULL);
                    $datos_diferencias_acumuladas_valores_reales_simulados_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valores_perfil_horario_anterior = $timestamp_fecha_hora_valores_perfil_horario;
            $numero_puntos_seguidos_grafica += 1;

            // Sólo se añaden las horas si el intervalo es por hora en los tooltips
            if ($intervalo_valores == INTERVALO_VALORES_HORA)
            {
                $cadena_fecha_valor_valores_perfil_horario_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_perfil_horario_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            }
            else
            {
                $cadena_fecha_valor_valores_perfil_horario_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_perfil_horario_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_local"]);
            }

            // Datos de valores reales
            $tooltip_valor_real = $idiomas->_("Valor real").": ".formatea_numero($valor_real_perfil_horario, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_real .= " ".$unidad_medida;
            }
            $tooltip_valor_real .= " (".$cadena_fecha_valor_valores_perfil_horario_local_local.")";

            $datos_valores_reales_perfil_horario->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_perfil_horario,
                $valor_real_perfil_horario,
                $tooltip_valor_real);

            // Datos de valores simulados
            $tooltip_valor_simulado = $idiomas->_("Valor simulado").": ".formatea_numero($valor_simulado_perfil_horario, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_simulado .= " ".$unidad_medida;
            }
            $tooltip_valor_simulado .= " (".$cadena_fecha_valor_valores_perfil_horario_local_local.")"."<br/>";
            $cadena_error_estandar_valor_simulado = formatea_numero($error_estandar_valor_simulado, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
            $tooltip_valor_simulado .= $idiomas->_("Error estándar")." (".$idiomas->_("RMSE").")".": ".$cadena_error_estandar_valor_simulado."<br/>";

            $datos_valores_simulados_perfil_horario->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_perfil_horario,
                $valor_simulado_perfil_horario,
                $tooltip_valor_simulado);

            $datos_banda_valores_simulados_perfil_horario->anyade_tupla_pareja_datos(
                $valor_simulado_perfil_horario - $error_estandar_valor_simulado,
                $valor_simulado_perfil_horario + $error_estandar_valor_simulado);

            // Datos de diferencias
            $diferencia_valor_real_simulado_perfil_horario = $valor_real_perfil_horario - $valor_simulado_perfil_horario;
            if ($diferencia_valor_real_simulado_perfil_horario > $max_diferencia)
            {
                $max_diferencia = $diferencia_valor_real_simulado_perfil_horario;
            }
            if ($diferencia_valor_real_simulado_perfil_horario < $min_diferencia)
            {
                $min_diferencia = $diferencia_valor_real_simulado_perfil_horario;
            }
            $datos_diferencias_valores_reales_simulados_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario, $diferencia_valor_real_simulado_perfil_horario);

            // Datos de diferencias acumuladas
            if ($campo_incremental == true)
            {
                $suma_diferencias_valores_reales_simulados_perfil_horario += $diferencia_valor_real_simulado_perfil_horario;
                if ($suma_diferencias_valores_reales_simulados_perfil_horario > $max_diferencia_acumulada)
                {
                    $max_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_perfil_horario;
                }
                if ($suma_diferencias_valores_reales_simulados_perfil_horario < $min_diferencia_acumulada)
                {
                    $min_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_perfil_horario;
                }
                $datos_diferencias_acumuladas_valores_reales_simulados_perfil_horario->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_perfil_horario, $suma_diferencias_valores_reales_simulados_perfil_horario);
            }

            // Mapa de calor de diferencias
            if ($tipo_mapa_calor == TIPO_MAPA_CALOR_DIARIO)
            {
                $fecha_valor_real_simulado_perfil_horario_local = convierte_cadena_a_fecha($cadena_fecha_valor_real_simulado_perfil_horario_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $zona_horaria);
                $valor_diferencia_valor_real_simulado_perfil_horario = abs($valor_real_perfil_horario - $valor_simulado_perfil_horario);
                $valores_mapa_calor_diferencias_valores_reales_simulados_perfil_horario->anyade_valor_fecha_hora($fecha_valor_real_simulado_perfil_horario_local, $valor_diferencia_valor_real_simulado_perfil_horario);
            }
        }

        // Fechas de inicio y fin
        if ($numero_valores_reales_simulados_perfil_horario > 0)
        {
            $cadena_min_fecha_jqplot_local = convierte_formato_fecha($valores_reales_simulados_perfil_horario[0]["fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_jqplot_local = convierte_formato_fecha($valores_reales_simulados_perfil_horario[$numero_valores_reales_simulados_perfil_horario - 1]["fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_JQPLOT);

            // Fechas mínima y máxima
            // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
            if ($cadena_min_fecha_jqplot_local == $cadena_max_fecha_jqplot_local)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_local = convierte_cadena_a_fecha($cadena_min_fecha_jqplot_local, FORMATO_FECHA_HORA_JQPLOT, $zona_horaria);
                $max_fecha_local = convierte_cadena_a_fecha($cadena_max_fecha_jqplot_local, FORMATO_FECHA_HORA_JQPLOT, $zona_horaria);
                $min_fecha_local->sub($intervalo_fecha);
                $max_fecha_local->add($intervalo_fecha);

                $cadena_min_fecha_jqplot_local = convierte_fecha_a_cadena($min_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
                $cadena_max_fecha_jqplot_local = convierte_fecha_a_cadena($max_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
            }
        }

        // Gráfica de valores (valores utilizados para el cálculo del perfil horario)
        $datos_grafica_valores_mapa_calor = dame_datos_grafica_valores_mapas_calor_valores_perfil_horario(
            $id_sensor,
            $campo,
            $idiomas->_("Valor"),
            NULL,
            $numero_decimales_valores,
            $unidad_medida,
            $cadena_fecha_inicio_perfil_horario_local_local,
            $cadena_fecha_fin_perfil_horario_local_local,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $milisegundos_desfase_zonas_horarias_cliente_local);

        // Variables de gráfica de consumos
        $datos_grafica_valores_perfil_horario_semanales = $datos_grafica_valores_mapa_calor["datos_grafica_valores_perfil_horario_semanales"];
        $datos_grafica_valores_perfil_horario = $datos_grafica_valores_mapa_calor["datos_grafica_valores_perfil_horario"];
        $datos_banda_valores_perfil_horario_semanales = $datos_grafica_valores_mapa_calor["datos_banda_valores_perfil_horario_semanales"];
        $min_valor_perfil_horario = $datos_grafica_valores_mapa_calor["min_valor_perfil_horario"];
        $max_valor_perfil_horario = $datos_grafica_valores_mapa_calor["max_valor_perfil_horario"];

        // Gráfica de valores
        $etiquetas_grafica_valores = new VectorDatos();
        $etiquetas_grafica_valores->anyade_etiqueta($idiomas->_("Perfil horario"));
        $etiquetas_grafica_valores->anyade_etiqueta($idiomas->_("Valores"));
        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_valores_simulados_perfil_horario->dame_datos());
        $grafica_valores->anyade_dato($datos_valores_reales_perfil_horario->dame_datos());
        $bandas_valores = new VectorDatos();
        $bandas_valores->anyade_dato($datos_banda_valores_simulados_perfil_horario->dame_datos());
        $bandas_valores->anyade_dato($datos_banda_valores_reales_perfil_horario->dame_datos());

        // Gráficas de diferencias y de diferencias acumuladas
        $grafica_diferencias = new VectorDatos();
        $grafica_diferencias->anyade_dato($datos_diferencias_valores_reales_simulados_perfil_horario->dame_datos());
        $grafica_diferencias_acumuladas = new VectorDatos();
        $grafica_diferencias_acumuladas->anyade_dato($datos_diferencias_acumuladas_valores_reales_simulados_perfil_horario->dame_datos());

        // Gráfica de valores de perfil horario
        $etiquetas_grafica_valores_perfil_horario = new VectorDatos();
        $etiquetas_grafica_valores_perfil_horario->anyade_etiqueta($idiomas->_("Media semanal"));
        $etiquetas_grafica_valores_perfil_horario->anyade_etiqueta($idiomas->_("Valores"));
        $grafica_valores_perfil_horario = new VectorDatos();
        $grafica_valores_perfil_horario->anyade_dato($datos_grafica_valores_perfil_horario_semanales->dame_datos());
        $grafica_valores_perfil_horario->anyade_dato($datos_grafica_valores_perfil_horario->dame_datos());
        $bandas_valores_perfil_horario = new VectorDatos();
        $bandas_valores_perfil_horario->anyade_dato($datos_banda_valores_perfil_horario_semanales->dame_datos());
        $bandas_valores_perfil_horario->anyade_dato(array());

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_diferencia == INF)
        {
            $min_diferencia = "ND";
        }
        if ($max_diferencia == -INF)
        {
            $max_diferencia = "ND";
        }
        if ($min_diferencia_acumulada == INF)
        {
            $min_diferencia_acumulada = "ND";
        }
        if ($max_diferencia_acumulada == -INF)
        {
            $max_diferencia_acumulada = "ND";
        }
        if ($min_valor_perfil_horario == INF)
        {
            $min_valor_perfil_horario = "ND";
        }
        if ($max_valor_perfil_horario == -INF)
        {
            $max_valor_perfil_horario = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_fecha" => $cadena_min_fecha_jqplot_local,
            "max_fecha" => $cadena_max_fecha_jqplot_local,
            "etiquetas_grafica_valores" => $etiquetas_grafica_valores->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "bandas_valores" => $bandas_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "grafica_diferencias" => $grafica_diferencias->dame_datos(),
            "min_diferencia" => $min_diferencia,
            "max_diferencia" => $max_diferencia,
            "campo_incremental" => $campo_incremental,
            "grafica_diferencias_acumuladas" => $grafica_diferencias_acumuladas->dame_datos(),
            "min_diferencia_acumulada" => $min_diferencia_acumulada,
            "max_diferencia_acumulada" => $max_diferencia_acumulada,
            "dias_mapa_calor_diferencias" => $valores_mapa_calor_diferencias_valores_reales_simulados_perfil_horario->dame_dias(),
            "datos_mapa_calor_diferencias" => $valores_mapa_calor_diferencias_valores_reales_simulados_perfil_horario->dame_datos(),
            "etiquetas_grafica_valores_perfil_horario" => $etiquetas_grafica_valores_perfil_horario->dame_datos(),
            "grafica_valores_perfil_horario" => $grafica_valores_perfil_horario->dame_datos(),
            "bandas_valores_perfil_horario" => $bandas_valores_perfil_horario->dame_datos(),
            "min_valor_perfil_horario" => $min_valor_perfil_horario,
            "max_valor_perfil_horario" => $max_valor_perfil_horario,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida);
        return ($resultado);
    }


    // Devuelve la información de comparación de campos iguales de sensores
    function dame_comparacion_valores_campos_iguales_sensores($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Flag para mostrar la gráfica de diferencias
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $mostrar_grafica_diferencias = false;
                $mostrar_mapas_calor_diferencias = false;
                break;
            }
            default:
            {
                $mostrar_grafica_diferencias = true;
                $mostrar_mapas_calor_diferencias = true;
                break;
            }
        }
        if ($tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)
        {
            $mostrar_mapas_calor_diferencias = false;
        }

        // Variables
        $min_valor = INF;
        $max_valor = -INF;
        $min_valores = array();
        $max_valores = array();
        $totales_valores = array();
        $numeros_valores = array();

        // Se recorren los sensores
        $hay_datos = false;
        $datos_sensores = array();
        $valores_sensores = array();
        $min_fecha_valores_sensores_local = NULL;
        $max_fecha_valores_sensores_local = NULL;
        $nombres_grafica_valores = new VectorDatos();
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Id y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Si el sensor está repetido no se realiza la consulta del mismo otra vez
            if (array_key_exists($nombre_sensor, $valores_sensores) == true)
            {
                continue;
            }

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

            // Información del ratio (si aplica)
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

            // Se realiza la consulta de valores
            $consulta_valores_sensor = dame_consulta_valores_sensor(
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $parametros_extra_campo);
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Se recorren los valores del sensor
            $fecha_hora_inicio_valores_sensor_local = NULL;
            $datos_sensor = new VectorDatos();
            $valores_sensor = array();
            $min_valor_sensor = INF;
            $max_valor_sensor = -INF;
            $total_valores_sensor = NULL;
            $numero_valores_sensor = 0;
            $timestamp_fecha_hora_valor_sensor_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica = 0;
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
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

                // Valor máximo y mínimo y fecha
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                }
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);

                // Si el intervalo es día, semana o mes, establecer la hora a la primera hora del intervalo
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    {
                        $fecha_hora_local->setTime(0, 0, 0);
                        $fecha_hora_utc = dame_fecha_hora_utc($fecha_hora_local);
                        break;
                    }
                    case INTERVALO_VALORES_SEMANA:
                    {
                        $fecha_hora_local->modify('Monday this week');
                        $fecha_hora_local->setTime(0, 0, 0);
                        $fecha_hora_utc = dame_fecha_hora_utc($fecha_hora_local);
                        break;
                    }
                    case INTERVALO_VALORES_MES:
                    {
                        $fecha_hora_local->modify('first day of this month');
                        $fecha_hora_local->setTime(0, 0, 0);
                        $fecha_hora_utc = dame_fecha_hora_utc($fecha_hora_local);
                        break;
                    }
                }

                // Fecha de inicio de valores del sensor
                if ($fecha_hora_inicio_valores_sensor_local === NULL)
                {
                    $fecha_hora_inicio_valores_sensor_local = $fecha_hora_local;
                }

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                $timestamp_fecha_hora_valor_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_valor_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if (($numero_puntos_seguidos_grafica > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_sensor_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_valor_sensor_utc - $timestamp_fecha_hora_valor_sensor_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica = 0;
                        $datos_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_valor_sensor_anterior_utc = $timestamp_fecha_hora_valor_sensor_utc;
                $numero_puntos_seguidos_grafica += 1;

                // Se añade el valor
                $datos_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_utc, $valor);
                array_push($valores_sensor, array(
                    "timestamp_fecha_valor_sensor_utc" => $timestamp_fecha_hora_valor_sensor_utc,
                    "fecha_hora_local" => $fecha_hora_local,
                    "valor" => $valor));

                // Valores mínimos y máximos y total de valores
                if ($valor > $max_valor_sensor)
                {
                    $max_valor_sensor = $valor;
                }
                if ($valor < $min_valor_sensor)
                {
                    $min_valor_sensor = $valor;
                }
                if ($total_valores_sensor === NULL)
                {
                    $total_valores_sensor = 0.0;
                }
                $total_valores_sensor += $valor;
                $numero_valores_sensor += 1;
            }
            $fecha_hora_fin_valores_sensor_local = $fecha_hora_local;

            // Fechas y valores máximos y mínimos
            if ($datos_sensor->dame_numero_datos() > 0)
            {
                // Flag de existencia de datos
                $hay_datos = true;

                // Fechas mínima y máxim
                if (($min_fecha_valores_sensores_local === NULL) || ($fecha_hora_inicio_valores_sensor_local < $min_fecha_valores_sensores_local))
                {
                    $min_fecha_valores_sensores_local = clone $fecha_hora_inicio_valores_sensor_local;
                }
                if (($max_fecha_valores_sensores_local === NULL) || ($fecha_hora_fin_valores_sensor_local > $max_fecha_valores_sensores_local))
                {
                    $max_fecha_valores_sensores_local = clone $fecha_hora_fin_valores_sensor_local;
                }

                // Gráfica de valores
                $nombres_grafica_valores->anyade_etiqueta($nombre_sensor);
                array_push($datos_sensores, $datos_sensor);

                // Valores de sensor
                $valores_sensores[$nombre_sensor] = $valores_sensor;
                array_push($min_valores, $min_valor_sensor);
                array_push($max_valores, $max_valor_sensor);
                array_push($totales_valores, $total_valores_sensor);
                array_push($numeros_valores, $numero_valores_sensor);
            }
        }

        // Si no hay datos no se hace nada
        if ($hay_datos == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Si no hay datos del sensor principal no se hace nada
        $id_sensor_principal = $ids_sensores[0];
        $nombre_sensor_principal = $nombres_sensores[0];
        if (array_key_exists($nombre_sensor_principal, $valores_sensores) == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se calculan los datos de las diferencias de valores (en el intervalo de fechas donde hay valores en los dos sensores)
        $valores_diferencias_sensores_secundarios = array();
        $min_diferencia = INF;
        $max_diferencia = -INF;
        $nombres_grafica_diferencias = new VectorDatos();
        if (array_key_exists($nombre_sensor_principal, $valores_sensores) == true)
        {
            $valores_sensor_principal = $valores_sensores[$nombre_sensor_principal];
            for ($i = 1; $i < count($nombres_sensores); $i++)
            {
                $nombre_sensor_secundario = $nombres_sensores[$i];
                if (array_key_exists($nombre_sensor_secundario, $valores_sensores) == false)
                {
                    continue;
                }
                if ($nombre_sensor_secundario == $nombre_sensor_principal)
                {
                    continue;
                }
                $valores_sensor_secundario = $valores_sensores[$nombre_sensor_secundario];

                $valores_diferencias_sensor_secundario = array();
                $indice_sensor_principal = 0;
                $indice_sensor_secundario = 0;

                // Si hay que mostrar la gráfica de diferencias
                if ($mostrar_grafica_diferencias == true)
                {
                    while (($indice_sensor_principal < count($valores_sensor_principal)) &&
                        ($indice_sensor_secundario < count($valores_sensor_secundario)))
                    {
                        $timestamp_fecha_hora_valor_sensor_principal_utc = $valores_sensor_principal[$indice_sensor_principal]["timestamp_fecha_valor_sensor_utc"];
                        $fecha_hora_sensor_principal_local = $valores_sensor_principal[$indice_sensor_principal]["fecha_hora_local"];
                        $valor_sensor_principal = $valores_sensor_principal[$indice_sensor_principal]["valor"];

                        $timestamp_fecha_hora_valor_sensor_secundario_utc = $valores_sensor_secundario[$indice_sensor_secundario]["timestamp_fecha_valor_sensor_utc"];
                        $valor_sensor_secundario = $valores_sensor_secundario[$indice_sensor_secundario]["valor"];

                        if ($timestamp_fecha_hora_valor_sensor_principal_utc == $timestamp_fecha_hora_valor_sensor_secundario_utc)
                        {
                            $valor_sensor_principal_modificado = modifica_valor_campo_clase_sensor_informe(
                                $clase_sensor,
                                $campo,
                                $valor_sensor_principal,
                                TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES);
                            $valor_sensor_secundario_modificado = modifica_valor_campo_clase_sensor_informe(
                                $clase_sensor,
                                $campo,
                                $valor_sensor_secundario,
                                TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES);

                            $diferencia_valor_sensores_fecha = $valor_sensor_principal_modificado - $valor_sensor_secundario_modificado;
                            if ($diferencia_valor_sensores_fecha > $max_diferencia)
                            {
                                $max_diferencia = $diferencia_valor_sensores_fecha;
                            }
                            if ($diferencia_valor_sensores_fecha < $min_diferencia)
                            {
                                $min_diferencia = $diferencia_valor_sensores_fecha;
                            }
                            array_push($valores_diferencias_sensor_secundario, array(
                                "timestamp_fecha_valor_sensor_utc" => $timestamp_fecha_hora_valor_sensor_principal_utc,
                                "fecha_hora_local" => $fecha_hora_sensor_principal_local,
                                "diferencia_valor" => $diferencia_valor_sensores_fecha));

                            $indice_sensor_principal += 1;
                            $indice_sensor_secundario += 1;
                        }
                        else
                        {
                            if ($timestamp_fecha_hora_valor_sensor_principal_utc < $timestamp_fecha_hora_valor_sensor_secundario_utc)
                            {
                                $indice_sensor_principal += 1;
                            }
                            else
                            {
                                $indice_sensor_secundario += 1;
                            }
                        }
                    }

                    if (count($valores_diferencias_sensor_secundario) > 0)
                    {
                        array_push($valores_diferencias_sensores_secundarios, $valores_diferencias_sensor_secundario);
                        $nombres_grafica_diferencias->anyade_etiqueta($nombre_sensor_principal." - ".$nombre_sensor_secundario);
                    }
                }
            }
        }

        // Se formatean los datos de diferencias de valores para la gráfica de diferencias
        $datos_diferencias_sensores_secundarios = array();
        $timestamp_fecha_hora_diferencias_sensor_secundario_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($valores_diferencias_sensores_secundarios as $valores_diferencias_sensor_secundario)
        {
            $datos_diferencias_sensor_secundario = new VectorDatos();
            foreach ($valores_diferencias_sensor_secundario as $valor_diferencias_sensor_secundario)
            {
                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                $timestamp_fecha_hora_diferencias_sensor_secundario_utc = $valor_diferencias_sensor_secundario["timestamp_fecha_valor_sensor_utc"];
                if (($numero_puntos_seguidos_grafica > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_diferencias_sensor_secundario_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_diferencias_sensor_secundario_utc - $timestamp_fecha_hora_diferencias_sensor_secundario_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica = 0;
                        $datos_diferencias_sensor_secundario->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_sensor_secundario_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_diferencias_sensor_secundario_anterior_utc = $timestamp_fecha_hora_diferencias_sensor_secundario_utc;
                $numero_puntos_seguidos_grafica += 1;

                // Se añade la diferencia
                $valor_diferencias_sensor_secundario = $valor_diferencias_sensor_secundario["diferencia_valor"];
                $datos_diferencias_sensor_secundario->anyade_tupla_pareja_datos($timestamp_fecha_hora_diferencias_sensor_secundario_utc, $valor_diferencias_sensor_secundario);
            }
            array_push($datos_diferencias_sensores_secundarios, $datos_diferencias_sensor_secundario);
        }

        // Datos para los mapas de calor de diferencias de valores
        $dias_mapas_calor_diferencias_sensores_secundarios = array();
        $datos_mapas_calor_diferencias_sensores_secundarios = array();
        if ($mostrar_mapas_calor_diferencias == true)
        {
            foreach ($valores_diferencias_sensores_secundarios as $valores_diferencias_sensor_secundario)
            {
                $valores_mapa_calor_diferencias_sensor_secundario = new ValoresMapaCalor($tipo_mapa_calor);
                foreach ($valores_diferencias_sensor_secundario as $valor_diferencias_sensor_secundario)
                {
                    $fecha_hora_local = $valor_diferencias_sensor_secundario["fecha_hora_local"];
                    $valor_diferencias = $valor_diferencias_sensor_secundario["diferencia_valor"];
                    $valores_mapa_calor_diferencias_sensor_secundario->anyade_valor_fecha_hora($fecha_hora_local, $valor_diferencias);
                }
                array_push($dias_mapas_calor_diferencias_sensores_secundarios, $valores_mapa_calor_diferencias_sensor_secundario->dame_dias());
                array_push($datos_mapas_calor_diferencias_sensores_secundarios, $valores_mapa_calor_diferencias_sensor_secundario->dame_datos());
            }
        }

        // Fechas mínima y máxima
        // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
        if ($min_fecha_valores_sensores_local == $max_fecha_valores_sensores_local)
        {
            $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
            $min_fecha_valores_sensores_local->sub($intervalo_fecha);
            $max_fecha_valores_sensores_local->add($intervalo_fecha);
        }
        $cadena_min_fecha_jqplot_local = convierte_fecha_a_cadena($min_fecha_valores_sensores_local, FORMATO_FECHA_HORA_JQPLOT);
        $cadena_max_fecha_jqplot_local = convierte_fecha_a_cadena($max_fecha_valores_sensores_local, FORMATO_FECHA_HORA_JQPLOT);

        // Valores mínimo y máximo
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_diferencia == INF)
        {
            $min_diferencia = "ND";
        }
        if ($max_diferencia == -INF)
        {
            $max_diferencia = "ND";
        }

        // Gráficas de valores y diferencias
        $grafica_valores = new VectorDatos();
        foreach ($datos_sensores as $datos_sensor)
        {
            $grafica_valores->anyade_dato($datos_sensor->dame_datos());
        }
        $grafica_diferencias = new VectorDatos();
        if ($mostrar_grafica_diferencias == true)
        {
            foreach ($datos_diferencias_sensores_secundarios as $datos_diferencias_sensor_secundario)
            {
                $grafica_diferencias->anyade_dato($datos_diferencias_sensor_secundario->dame_datos());
            }
        }

        // Tipo de líneas de valores
        $tipo_lineas_valores = dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
            $intervalo_valores,
            $clase_sensor,
            $id_sensor_principal,
            $campo);

        // Número de decimales de valores y unidad de medida
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor_principal, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida);
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Textos de diferencias de valores
        $nombres_sensores_tabla_diferencias_valores = array();
        $textos_diferencias_valores_totales = array();
        $textos_diferencias_valores_maximos = array();
        $textos_diferencias_valores_minimos = array();

        // Valores y textos de diferencias de valores
        $total_valor_principal = $totales_valores[0];
        $numero_valores_principal = $numeros_valores[0];
        $max_valor_principal = $max_valores[0];
        $min_valor_principal = $min_valores[0];
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES)
        {
            $total_valor_principal /= $numero_valores_principal;
        }
        for ($i = 1; $i < count($nombres_sensores); $i++)
        {
            // Nombres de los sensores
            $nombre_sensor_secundario = $nombres_sensores[$i];
            if ($nombre_sensor_secundario == $nombre_sensor_principal)
            {
                continue;
            }

            // Si no hay valores del sensor secundario no se añade la fila
            if (array_key_exists($nombre_sensor_secundario, $valores_sensores) == false)
            {
                continue;
            }

            // Se añade el nombre de la fila de la tabla de diferencias de valores
            array_push($nombres_sensores_tabla_diferencias_valores,
                $nombre_sensor_principal." - ".$nombre_sensor_secundario);

            // Valores del sensor secundario
            $total_valor_secundario = $totales_valores[$i];
            $numero_valores_secundario = $numeros_valores[$i];
            $max_valor_secundario = $max_valores[$i];
            $min_valor_secundario = $min_valores[$i];

            // Diferencias de valores totales
            if ($total_valor_principal == $total_valor_secundario)
            {
                $texto_diferencia_valores_totales = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0".$cadena_unidad_medida;
                if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $texto_diferencia_valores_totales .= " (0 "."%".")";
                }
            }
            else
            {
                if ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES)
                {
                    $total_valor_secundario /= $numero_valores_secundario;
                }

                $diferencia_valores_totales = abs($total_valor_principal - $total_valor_secundario);
                $cadena_diferencia_valores_totales = formatea_numero($diferencia_valores_totales, $numero_decimales_valores);
                $porcentaje_diferencia_valores_totales = dame_porcentaje_valor_referencia($total_valor_principal, $total_valor_secundario);
                $cadena_porcentaje_diferencia_valores_totales = formatea_numero($porcentaje_diferencia_valores_totales, $numero_decimales_valores);

                if ($total_valor_principal > $total_valor_secundario)
                {
                    $texto_diferencia_valores_totales = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_valores_totales.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_totales .= " (+".$cadena_porcentaje_diferencia_valores_totales." "."%".")";
                    }
                }
                else
                {
                    $texto_diferencia_valores_totales = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_valores_totales.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_totales .= " (-".$cadena_porcentaje_diferencia_valores_totales." "."%".")";
                    }
                    $diferencia_valores_totales *= -1;
                    $porcentaje_diferencia_valores_totales *= -1;
                }
            }
            array_push($textos_diferencias_valores_totales, $texto_diferencia_valores_totales);

            // Diferencias de máximos de valores
            if ($max_valor_principal == $max_valor_secundario)
            {
                $texto_diferencia_valores_maximos = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0".$cadena_unidad_medida;
                if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $texto_diferencia_valores_maximos .= " (0 "."%".")";
                }
            }
            else
            {
                $diferencia_valores_maximos = abs($max_valor_principal - $max_valor_secundario);
                $cadena_diferencia_valores_maximos = formatea_numero($diferencia_valores_maximos, $numero_decimales_valores);
                $porcentaje_diferencia_valores_maximos = dame_porcentaje_valor_referencia($max_valor_principal, $max_valor_secundario);
                $cadena_porcentaje_diferencia_valores_maximos = formatea_numero($porcentaje_diferencia_valores_maximos, $numero_decimales_valores);

                if ($max_valor_principal > $max_valor_secundario)
                {
                    $texto_diferencia_valores_maximos = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_valores_maximos.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_maximos .= " (+".$cadena_porcentaje_diferencia_valores_maximos." "."%".")";
                    }
                }
                else
                {
                    $texto_diferencia_valores_maximos = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_valores_maximos.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_maximos .= " (-".$cadena_porcentaje_diferencia_valores_maximos." "."%".")";
                    }
                    $diferencia_valores_maximos *= -1;
                    $porcentaje_diferencia_valores_maximos *= -1;
                }
            }
            array_push($textos_diferencias_valores_maximos, $texto_diferencia_valores_maximos);

            // Diferencia de mínimos de valores
            if ($min_valor_principal == $min_valor_secundario)
            {
                $texto_diferencia_valores_minimos = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0".$cadena_unidad_medida;
                if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                {
                    $texto_diferencia_valores_minimos .= " (0 "."%".")";
                }
            }
            else
            {
                $diferencia_valores_minimos = abs($min_valor_principal - $min_valor_secundario);
                $cadena_diferencia_valores_minimos = formatea_numero($diferencia_valores_minimos, $numero_decimales_valores);
                $porcentaje_diferencia_valores_minimos = dame_porcentaje_valor_referencia($min_valor_principal, $min_valor_secundario);
                $cadena_porcentaje_diferencia_valores_minimos = formatea_numero($porcentaje_diferencia_valores_minimos, $numero_decimales_valores);

                if ($min_valor_principal > $min_valor_secundario)
                {
                    $texto_diferencia_valores_minimos = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_valores_minimos.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_minimos .= " (+".$cadena_porcentaje_diferencia_valores_minimos." "."%".")";
                    }
                }
                else
                {
                    $texto_diferencia_valores_minimos = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_valores_minimos.$cadena_unidad_medida;
                    if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                    {
                        $texto_diferencia_valores_minimos .= " (-".$cadena_porcentaje_diferencia_valores_minimos." "."%".")";
                    }
                    $diferencia_valores_minimos *= -1;
                    $porcentaje_diferencia_valores_minimos *= -1;
                }
            }
            array_push($textos_diferencias_valores_minimos, $texto_diferencia_valores_minimos);
        }

        // Tabla de diferencias de valores de valores
        if (count($nombres_sensores_tabla_diferencias_valores) == 0)
        {
            $datos_tabla_diferencias_valores = NULL;
        }
        else
        {
            $params_tabla_diferencias_valores = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_DIFERENCIAS_VALORES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_DIFERENCIAS_VALORES),
                "generar_valores_xml" => true
            );
            $titulo_tabla_diferencias_valores = $idiomas->_("Diferencias de valores");
            $tabla_diferencias_valores = new TablaDatos(
                "tabla-diferencias-valores-comparacion-campos-iguales",
                $titulo_tabla_diferencias_valores,
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_diferencias_valores
            );
            switch ($tipo_valores_campo)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $titulo_cabecera_total_valor = $idiomas->_("Media");
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $titulo_cabecera_total_valor = $idiomas->_("Total");
                    break;
                }
            }
            $cabecera_tabla_diferencias_valores = array(
                $idiomas->_("Sensores"),
                $titulo_cabecera_total_valor,
                $idiomas->_("Máximo"),
                $idiomas->_("Mínimo")
            );
            $tabla_diferencias_valores->anyade_cabecera("", $cabecera_tabla_diferencias_valores);
            for ($i = 0; $i < count($nombres_sensores) - 1; $i++)
            {
                $fila_valores = array(
                    $nombres_sensores_tabla_diferencias_valores[$i],
                    $textos_diferencias_valores_totales[$i],
                    $textos_diferencias_valores_maximos[$i],
                    $textos_diferencias_valores_minimos[$i]);
                $tabla_diferencias_valores->anyade_fila("", $fila_valores);
            }
            $datos_tabla_diferencias_valores = $tabla_diferencias_valores->dame_tabla();
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_diferencia == INF)
        {
            $min_diferencia = "ND";
        }
        if ($max_diferencia == -INF)
        {
            $max_diferencia = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_fecha" => $cadena_min_fecha_jqplot_local,
            "max_fecha" => $cadena_max_fecha_jqplot_local,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "etiquetas_valores" => $nombres_grafica_valores->dame_datos(),
            "tipo_lineas_valores" => $tipo_lineas_valores,
            "tabla_diferencias_valores" => $datos_tabla_diferencias_valores,
            "grafica_diferencias" => $grafica_diferencias->dame_datos(),
            "min_diferencia" => $min_diferencia,
            "max_diferencia" => $max_diferencia,
            "etiquetas_diferencias" => $nombres_grafica_diferencias->dame_datos(),
            "dias_mapas_calor_diferencias" => $dias_mapas_calor_diferencias_sensores_secundarios,
            "datos_mapas_calor_diferencias" => $datos_mapas_calor_diferencias_sensores_secundarios,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida);
        return ($resultado);
    }


    // Devuelve la información de comparación de campos diferentes de sensores
    function dame_comparacion_valores_campos_diferentes_sensores($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clases_sensores = $parametros["clases_sensores"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $campos = $parametros["campos"];
        $parametros_extra_campos = $parametros["parametros_extra_campos"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $unificar_escalas = $parametros["unificar_escalas"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Valores mínimos y máximos
        $min_valores = array();
        $max_valores = array();

        // Fechas mínima y máxima
        $min_fecha_valores_sensores_local = NULL;
        $max_fecha_valores_sensores_local = NULL;

        // Tipos de líneas de valores
        $tipos_lineas_valores = array();

        // Números de decimales de valores y unidades de medida
        $numeros_decimales_valores = array();
        $unidades_medida = array();

        // Se recorren los sensores
        $hay_datos = false;
        $datos_sensores = array();
        $nombres_grafica_valores = new VectorDatos();
        $nombres_grafica_valores_unidad = new VectorDatos();
        for ($i = 0; $i < count($nombres_sensores); $i++)
        {
            // Información del sensor
            $clase_sensor = $clases_sensores[$i];
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];
            $campo = $campos[$i];
            $parametros_extra_campo = $parametros_extra_campos[$i];

            // Si no hay sensor se ignora
            if ($id_sensor == ID_NINGUNO)
            {
                continue;
            }

            // Si la clase no tiene procesado de valores se utiliza el intervalo de tiempo real
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
            if ($caracteristicas_clase_sensor["procesado_valores"] == true)
            {
                $intervalo_valores_sensor = $intervalo_valores;
            }
            else
            {
                $intervalo_valores_sensor = INTERVALO_VALORES_TIEMPO_REAL;
            }

            // Si hay campos de sensores repetidos (mismo sensor y campo) se ignora
            $campo_repetido = false;
            for ($j = 0; $j < $i; $j++)
            {
                if (($clase_sensor == $clases_sensores[$j]) &&
                    ($nombre_sensor == $nombres_sensores[$j]) &&
                    ($campo == $campos[$j]) &&
                    ($parametros_extra_campo == $parametros_extra_campo[$j]))
                {
                    $campo_repetido = true;
                    break;
                }
            }
            if ($campo_repetido == true)
            {
                continue;
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
                    $intervalo_valores_sensor,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

            // Se realiza la consulta de valores de sensor
            $consulta_valores_sensor = dame_consulta_valores_sensor(
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores_sensor,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $parametros_extra_campo);
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Se recorren los valores del sensor
            $datos_sensor = new VectorDatos();
            $min_valor = (float) (INF);
            $max_valor = (float) (-INF);
            $fecha_hora_inicio_valores_sensor_local = NULL;
            $timestamp_fecha_hora_valor_sensor_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica = 0;
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
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

                // Valores mínimo y máximo y fecha
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                }
                $fecha_hora_valor_sensor_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_valor_sensor_local = dame_fecha_hora_local($fecha_hora_valor_sensor_utc);
                if ($fecha_hora_inicio_valores_sensor_local === NULL)
                {
                    $fecha_hora_inicio_valores_sensor_local = $fecha_hora_valor_sensor_local;
                }
                
                // Si el intervalo es día, semana o mes, forzamos que la hora de los valores se muestre en 00:00
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    {
                        $fecha_hora_valor_sensor_local->setTime(0, 0, 0);
                        $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_valor_sensor_local, FORMATO_FECHA_HORA_BASE_DATOS);
                        break;
                    }
                    case INTERVALO_VALORES_SEMANA:
                    {
                        $fecha_hora_valor_sensor_local->modify('Monday this week');
                        $fecha_hora_valor_sensor_local->setTime(0, 0, 0);
                        $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_valor_sensor_local, FORMATO_FECHA_HORA_BASE_DATOS);
                        break;
                    }
                    case INTERVALO_VALORES_MES:
                    {
                        $fecha_hora_valor_sensor_local->modify('first day of this month');
                        $fecha_hora_valor_sensor_local->setTime(0, 0, 0);
                        $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_valor_sensor_local, FORMATO_FECHA_HORA_BASE_DATOS);
                        break;
                    }
                }
                

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                $timestamp_fecha_hora_valor_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_valor_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if (($numero_puntos_seguidos_grafica > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_sensor_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_valor_sensor_utc - $timestamp_fecha_hora_valor_sensor_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica = 0;
                        $datos_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_valor_sensor_anterior_utc = $timestamp_fecha_hora_valor_sensor_utc;
                $numero_puntos_seguidos_grafica += 1;
                
                // Se añade el valor del sensor
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                        break;
                    }
                    default:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                        break;
                    }
                }
                $cadena_fecha_hora_valor_sensor_local_local = convierte_fecha_a_cadena($fecha_hora_valor_sensor_local, $formato_fecha_hora_local);
                $datos_sensor->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_valor_sensor_utc,
                    $valor,
                    $cadena_fecha_hora_valor_sensor_local_local);
            }
            $fecha_hora_fin_valores_sensor_local = $fecha_hora_valor_sensor_local;

            // Si el sensor tiene datos
            if ($datos_sensor->dame_numero_datos() > 0)
            {
                $hay_datos = true;

                // Fechas mínima y máxima
                if (($min_fecha_valores_sensores_local === NULL) || ($fecha_hora_inicio_valores_sensor_local < $min_fecha_valores_sensores_local))
                {
                    $min_fecha_valores_sensores_local = clone $fecha_hora_inicio_valores_sensor_local;
                }
                if (($max_fecha_valores_sensores_local === NULL) || ($fecha_hora_fin_valores_sensor_local > $max_fecha_valores_sensores_local))
                {
                    $max_fecha_valores_sensores_local = clone $fecha_hora_fin_valores_sensor_local;
                }

                // Datos del sensor
                array_push($datos_sensores, $datos_sensor);

                // Valores mínimo y máximo
                array_push($min_valores, $min_valor);
                array_push($max_valores, $max_valor);

                $etiqueta_sensor = $nombre_sensor." (".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo)).")";
                $etiqueta_sensor_unidad = $nombre_sensor." (".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
                $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                if ($aplicar_ratio == true)
                {
                    modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
                }
                if ($unidad_medida != "")
                {
                    $etiqueta_sensor_unidad .= " - ".$unidad_medida;
                }
                $etiqueta_sensor_unidad .= ")";
                $nombres_grafica_valores->anyade_etiqueta($etiqueta_sensor);
                $nombres_grafica_valores_unidad->anyade_etiqueta($etiqueta_sensor_unidad);

                // Tipo de líneas de valores
                $tipo_lineas_valores = dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
                    $intervalo_valores,
                    $clase_sensor,
                    $id_sensor,
                    $campo);
                array_push($tipos_lineas_valores, $tipo_lineas_valores);

                // Número de decimales de valores y unidad de medida
                $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
                array_push($numeros_decimales_valores, $numero_decimales_valores);
                array_push($unidades_medida, $unidad_medida);
            }
        }

        // Si no hay datos no se hace nada
        if ($hay_datos == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Fechas mínima y máxima
        // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
        if ($min_fecha_valores_sensores_local == $max_fecha_valores_sensores_local)
        {
            $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
            $min_fecha_valores_sensores_local->sub($intervalo_fecha);
            $max_fecha_valores_sensores_local->add($intervalo_fecha);
        }
        $cadena_min_fecha_jqplot_local = convierte_fecha_a_cadena($min_fecha_valores_sensores_local, FORMATO_FECHA_HORA_JQPLOT);
        $cadena_max_fecha_jqplot_local = convierte_fecha_a_cadena($max_fecha_valores_sensores_local, FORMATO_FECHA_HORA_JQPLOT);

        // Variables para dibujar las gráficas
        $grafica_valores = new VectorDatos();
        foreach ($datos_sensores as $datos_sensor)
        {
            $datos_datos_sensor = $datos_sensor->dame_datos();
            $grafica_valores->anyade_dato($datos_datos_sensor);
        }

        // Se establecen los máximos y los mínimos al máximo de los máximos y al mínimo de los mínimos
        // si son la misma unidad de medida (y se mostrarán con el mismo eje 'y' en la gráfica)
        if ($unificar_escalas == VALOR_SI)
        {
            for ($i = 0; $i < count($unidades_medida); $i++)
            {
                $unidad_medida_i = $unidades_medida[$i];
                $min_valor_i = $min_valores[$i];
                $max_valor_i = $max_valores[$i];
                for ($j = 0; $j <= count($unidades_medida); $j++)
                {
                    if ($j == $i)
                    {
                        continue;
                    }
                    $unidad_medida_j = $unidades_medida[$j];
                    if ($unidad_medida_j == $unidad_medida_i)
                    {
                        $min_valor_j = $min_valores[$j];
                        $max_valor_j = $max_valores[$j];
                        if ($min_valor_j < $min_valor_i)
                        {
                            $min_valores[$i] = $min_valor_j;
                        }
                        else
                        {
                            $min_valores[$j] = $min_valor_i;
                        }
                        if ($max_valor_j > $max_valor_i)
                        {
                            $max_valores[$i] = $max_valor_j;
                        }
                        else
                        {
                            $max_valores[$j] = $max_valor_i;
                        }
                    }
                }
            }
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_fecha" => $cadena_min_fecha_jqplot_local,
            "max_fecha" => $cadena_max_fecha_jqplot_local,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "min_valores" => $min_valores,
            "max_valores" => $max_valores,
            "etiquetas_valores" => $nombres_grafica_valores->dame_datos(),
            "etiquetas_valores_unidad" => $nombres_grafica_valores_unidad->dame_datos(),
            "tipos_lineas_valores" => $tipos_lineas_valores,
            "numeros_decimales_valores" => $numeros_decimales_valores,
            "unidades_medida" => $unidades_medida);
        return ($resultado);
    }


    // Devuelve la información de análisis comparativo de sensores
    function dame_analisis_comparativo_sensores($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $campo = $parametros["campo"];
        $ids_sensores_agregados = $parametros["ids_sensores_agregados"];
        $nombres_sensores_agregados = $parametros["nombres_sensores_agregados"];
        $id_sensor_destacado = $parametros["id_sensor_destacado"];
        $nombre_sensor_destacado = $parametros["nombre_sensor_destacado"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores_agregados as $id_sensor_agregado)
        {
            if (in_array($id_sensor_agregado, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor agregado no visible por el usuario actual (id: '".$id_sensor_agregado."')");
            }
        }
        if (in_array($id_sensor_destacado, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor destacado no visible por el usuario actual (id: '".$id_sensor_destacado."')");
        }

        // Flag de sensor destacado en sensores agregados
        $sensor_destacado_en_sensores_agregados = in_array($id_sensor_destacado, $ids_sensores_agregados);

        // Se recuperan las filas de valores de los sensores
        $ids_sensores = $ids_sensores_agregados;
        $nombres_sensores = $nombres_sensores_agregados;
        if ($id_sensor_destacado != ID_NINGUNO)
        {
            if ($sensor_destacado_en_sensores_agregados == false)
            {
                array_push($ids_sensores, $id_sensor_destacado);
                array_push($nombres_sensores, $nombre_sensor_destacado);
            }
        }
        $parametros["ids_sensores"] = $ids_sensores;
        $parametros["nombres_sensores"] = $nombres_sensores;
        $filas_valores_sensores = dame_filas_valores_sensores($parametros);

        // Se recupera si aplica el ratio
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables
        $numero_sensor = 0;
        $grafica_valores = new VectorDatos();
        $grafica_valores_acumulados = new VectorDatos();
        $grafica_pareto = new VectorDatos();
        $nombres_sensores_valores = new VectorDatos();
        $min_valores_sensores = INF;
        $max_valores_sensores = -INF;
        $max_sumas_valores_sensores = 0.0;

        // Descripción de campo, número de decimales de valores y unidades de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
        if ($id_sensor_destacado != ID_NINGUNO)
        {
            $id_sensor_unidad_medida = $id_sensor_destacado;
        }
        else
        {
            $id_sensor_unidad_medida = $ids_sensores_agregados[0];
        }
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor_unidad_medida, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida);
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Tabla de valores máximos y mínimos
        $titulo_columna_sensor = $idiomas->_("Sensor");
        switch ($tipo_valores_campo)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_PUNTUAL,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_PUNTUAL),
                    "generar_valores_xml" => true
                );
                $cabecera_tabla = array(
                    $titulo_columna_sensor,
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo"),
                    $idiomas->_("Media")
                );
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_INCREMENTAL,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_ANALISIS_COMPARATIVO_INCREMENTAL),
                    "generar_valores_xml" => true
                );
                $cabecera_tabla = array(
                    $titulo_columna_sensor,
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo"),
                    $idiomas->_("Media por hora"),
                    $idiomas->_("Total")
                );
                break;
            }
        }
        $titulo_tabla_valores_maximos_minimos = $idiomas->_("Valores máximos y mínimos");
        $tabla_valores_maximos_minimos = new TablaDatos(
            "tabla-valores-maximos-minimos-analisis-comparativo",
            $titulo_tabla_valores_maximos_minimos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_valores_maximos_minimos->anyade_cabecera("", $cabecera_tabla);

        // Horas en una fila (para los campos incrementales sin horas - p.e. grados día)
        $segundos_intervalo_valores = dame_segundos_intervalo_valores($intervalo_valores);
        if ($segundos_intervalo_valores === NULL)
        {
            $horas_fila = NULL;
        }
        else
        {
            $horas_fila = $segundos_intervalo_valores / 3600;
        }

        // Se recupera la información de agregaciones de sensores agregados
        $res_info_agregaciones = dame_info_agregaciones_sensores_campos(
            $id_ratio,
            $ids_sensores_agregados,
            $nombres_sensores_agregados,
            $clase_sensor,
            array($campo),
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $filas_valores_sensores);
        $info_agregaciones_sensores_campos = $res_info_agregaciones["info_agregaciones_sensores"];
        $numeros_sensores_valores_agregaciones_campos = $res_info_agregaciones["numeros_sensores_valores"];
        $info_agregaciones_sensores = $info_agregaciones_sensores_campos[$campo];
        $numero_sensores_valores_agregaciones = $numeros_sensores_valores_agregaciones_campos[$campo];

        // Si no hay datos no se hace nada
        if (count($info_agregaciones_sensores) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se simulan 3 sensores (media, máximo y mínimo)
        $filas_media = array();
        $filas_maximo = array();
        $filas_minimo = array();
        for ($i = 0; $i < count($info_agregaciones_sensores); $i++)
        {
            $info_agregacion_sensores = $info_agregaciones_sensores[$i];
            $cadena_fecha_hora_agregacion_base_datos_utc = $info_agregacion_sensores["cadena_fecha_hora_agregacion_base_datos_utc"];

            $valor_media = array_sum($info_agregacion_sensores["valores"]) / count($info_agregacion_sensores["valores"]);
            $numero_sensores_sin_valor = count($ids_sensores_agregados) - count($info_agregacion_sensores["ids_sensores"]);

            $valor_maximo = max($info_agregacion_sensores["valores"]);
            $indice_valor_maximo = array_search($valor_maximo, $info_agregacion_sensores["valores"]);
            $nombre_sensor_valor_maximo = $info_agregacion_sensores["nombres_sensores"][$indice_valor_maximo];

            $valor_minimo = min($info_agregacion_sensores["valores"]);
            $indice_valor_minimo = array_search($valor_minimo, $info_agregacion_sensores["valores"]);
            $nombre_sensor_valor_minimo = $info_agregacion_sensores["nombres_sensores"][$indice_valor_minimo];

            // Filas de valores
            $fila_media = array(
                "fecha_hora" => $cadena_fecha_hora_agregacion_base_datos_utc,
                $campo => $valor_media,
                "horas" => $horas_fila,
                "nombre_sensor" => NULL,
                "numero_sensores_sin_valor" => $numero_sensores_sin_valor);
            $fila_maximo = array(
                "fecha_hora" => $cadena_fecha_hora_agregacion_base_datos_utc,
                $campo => $valor_maximo,
                "horas" => $horas_fila,
                "nombre_sensor" => $nombre_sensor_valor_maximo,
                "numero_sensores_sin_valor" => $numero_sensores_sin_valor);
            $fila_minimo = array(
                "fecha_hora" => $cadena_fecha_hora_agregacion_base_datos_utc,
                $campo => $valor_minimo,
                "horas" => $horas_fila,
                "nombre_sensor" => $nombre_sensor_valor_minimo,
                "numero_sensores_sin_valor" => $numero_sensores_sin_valor);

            array_push($filas_media, $fila_media);
            array_push($filas_maximo, $fila_maximo);
            array_push($filas_minimo, $fila_minimo);
        }

        // Simulación de sensores (con la media, máximo y mínimo)
        $ids_sensores_simulados = array(
            NULL,
            NULL,
            NULL);
        $res_cadenas_inicio_fin = dame_cadenas_inicio_fin_nombre_agregacion();
        $cadena_inicio_nombre_agregacion = $res_cadenas_inicio_fin["cadena_inicio"];
        $cadena_fin_nombre_agregacion = $res_cadenas_inicio_fin["cadena_fin"];
        $nombres_sensores_simulados = array(
            $cadena_inicio_nombre_agregacion.$idiomas->_("Media").$cadena_fin_nombre_agregacion,
            $cadena_inicio_nombre_agregacion.$idiomas->_("Máximo").$cadena_fin_nombre_agregacion,
            $cadena_inicio_nombre_agregacion.$idiomas->_("Mínimo").$cadena_fin_nombre_agregacion);
        $indice_filas_media = 0;
        $indice_filas_maximo = 1;
        $indice_filas_minimo = 2;
        $filas_valores_sensores_simulados = array(
            $nombres_sensores_simulados[$indice_filas_media] => $filas_media,
            $nombres_sensores_simulados[$indice_filas_maximo] => $filas_maximo,
            $nombres_sensores_simulados[$indice_filas_minimo] => $filas_minimo);

        // Se añade el sensor principal (si existe)
        if ($id_sensor_destacado != ID_NINGUNO)
        {
            array_push($ids_sensores_simulados, $id_sensor_destacado);
            array_push($nombres_sensores_simulados, $nombre_sensor_destacado);
            $filas_valores_sensores_simulados[$nombre_sensor_destacado] = $filas_valores_sensores[$nombre_sensor_destacado];
        }

        // Se recorren los sensores "simulados" (para las gráficas de valores y valores acumulados)
        for ($i = 0; $i < count($ids_sensores_simulados); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores_simulados[$i];
            $nombre_sensor = $nombres_sensores_simulados[$i];

            // Se recupera la información del ratio (si aplica)
            if (($aplicar_ratio == true) && ($id_sensor == $id_sensor_destacado))
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

            // Mínimos y máximos
            $max_valor = (float) -INF;
            $cadena_fecha_hora_max_valor_base_datos_utc = "";
            $min_valor = (float) INF;
            $cadena_fecha_hora_min_valor_base_datos_utc = "";

            // Sumas y numero de valores
            $suma_horas = 0.0;
            $suma_valores = 0.0;
            $numero_ocurrencias_valores = 0;

            // Filas de valores del sensor (si no hay datos para el sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores_simulados[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

            // Datos para las gráficas
            $datos_sensor_valores = new VectorDatos();
            $datos_sensor_valores_acumulados = new VectorDatos();

            // Se recorren las filas de valores
            $timestamp_fecha_hora_valor_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica = 0;
            foreach ($filas_valores_sensor as $fila_valor_sensor)
            {
                // Fecha y valor
                // (Nota: Se aplica el ratio sólo si es el sensor destacado (ya se ha aplicado en las agregaciones) (si aplica))
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if (($aplicar_ratio == true) && ($id_sensor == $id_sensor_destacado))
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                if ($valor === NULL)
                {
                    continue;
                }

                // Se incrementan las horas totales
                // Nota: Si el sensor no es incremental y es tiempo real, no hay horas en la fila (p.e. grados día)
                if ($campo_incremental == true)
                {
                    if (array_key_exists("horas", $fila_valor_sensor) == true)
                    {
                        $suma_horas += $fila_valor_sensor["horas"];
                    }
                    else
                    {
                        if ($horas_fila !== NULL)
                        {
                            $suma_horas += $horas_fila;
                        }
                    }
                }

                // Sumade valores, máximos y mínimos
                $suma_valores += $valor;
                if ($valor > $max_valores_sensores)
                {
                    $max_valores_sensores = $valor;
                }
                if ($valor < $min_valores_sensores)
                {
                    $min_valores_sensores = $valor;
                }
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                    $cadena_fecha_hora_max_valor_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                    $cadena_fecha_hora_min_valor_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if (($numero_puntos_seguidos_grafica > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
                {
                    $segundos_entre_incrementos = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                    if ($segundos_entre_incrementos > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica = 0;
                        $datos_sensor_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                        if ($campo_incremental == true)
                        {
                            $datos_sensor_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                        }
                    }
                }
                $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
                $numero_puntos_seguidos_grafica += 1;

                // Se añaden los valores
                $tooltips_agregacion = dame_tooltips_agregacion_sensores(
                    $nombre_sensor,
                    $cadena_fecha_hora_base_datos_utc,
                    $valor,
                    $suma_valores,
                    $unidad_medida,
                    $fila_valor_sensor["nombre_sensor"],
                    $fila_valor_sensor["numero_sensores_sin_valor"]);
                $tooltip_valor = $tooltips_agregacion["tooltip_valor"];
                $tooltip_valor_acumulado = $tooltips_agregacion["tooltip_suma_valores"];

                $datos_sensor_valores->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_valor_utc,
                    $valor,
                    $tooltip_valor);
                if ($campo_incremental == true)
                {
                    $datos_sensor_valores_acumulados->anyade_tupla_pareja_datos_etiqueta(
                        $timestamp_fecha_hora_valor_utc,
                        $suma_valores,
                        $tooltip_valor_acumulado);
                }

                // Se incrementa el número de ocurrencias de valores
                $numero_ocurrencias_valores += 1;
            }

            // Si no hay valores se ignora el sensor
            if ($numero_ocurrencias_valores == 0)
            {
                continue;
            }

            // Máximo de suma de valores
            if ($suma_valores > $max_sumas_valores_sensores)
            {
                $max_sumas_valores_sensores = $suma_valores;
            }

            // Se guardan los datos de las gráficas
            $grafica_valores->anyade_dato($datos_sensor_valores->dame_datos());
            if ($campo_incremental == true)
            {
                $grafica_valores_acumulados->anyade_dato($datos_sensor_valores_acumulados->dame_datos());
            }

            // Formatos de fecha
            $formato_fecha_origen = FORMATO_FECHA_HORA_BASE_DATOS;
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                case INTERVALO_VALORES_SEMANA:
                case INTERVALO_VALORES_MES:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_local"];
                    break;
                }
                default:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_hora_local"];
                    break;
                }
            }

            // Datos para tabla de valores máximos y mínimos
            $nombre_sensor_tabla_valores_maximos_minimos = htmlspecialchars($nombre_sensor, ENT_QUOTES);
            $cadena_fecha_hora_max_valor_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_max_valor_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_max_valor_local_local = convierte_formato_fecha($cadena_fecha_hora_max_valor_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $cadena_fecha_hora_min_valor_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_min_valor_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_min_valor_local_local = convierte_formato_fecha($cadena_fecha_hora_min_valor_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $maximo_tabla_valores_maximos_minimos = formatea_numero($max_valor, $numero_decimales_valores).$cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")";
            $minimo_tabla_valores_maximos_minimos = formatea_numero($min_valor, $numero_decimales_valores).$cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")";
            switch ($tipo_valores_campo)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $media_valores = $suma_valores / $numero_ocurrencias_valores;
                    $media_tabla_valores_maximos_minimos = formatea_numero($media_valores, $numero_decimales_valores).$cadena_unidad_medida;
                    $fila_tabla_valores_maximos_minimos = array(
                        $nombre_sensor_tabla_valores_maximos_minimos,
                        $maximo_tabla_valores_maximos_minimos,
                        $minimo_tabla_valores_maximos_minimos,
                        $media_tabla_valores_maximos_minimos);
                    $params_fila_valores_maximos_minimos = array(
                        "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")",
                        "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")",
                        "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    if ($suma_horas == 0)
                    {
                        $media_horaria_tabla_valores_maximos_minimos = $idiomas->_("ND");
                    }
                    else
                    {
                        $media_valores_por_hora = $suma_valores / $suma_horas;
                        $media_horaria_tabla_valores_maximos_minimos = formatea_numero($media_valores_por_hora, $numero_decimales_valores).$cadena_unidad_medida;
                    }

                    $total_tabla_valores_maximos_minimos = formatea_numero($suma_valores, $numero_decimales_valores).$cadena_unidad_medida;
                    $fila_tabla_valores_maximos_minimos = array(
                        $nombre_sensor_tabla_valores_maximos_minimos,
                        $maximo_tabla_valores_maximos_minimos,
                        $minimo_tabla_valores_maximos_minimos,
                        $media_horaria_tabla_valores_maximos_minimos,
                        $total_tabla_valores_maximos_minimos);
                    $params_fila_valores_maximos_minimos = array(
                        "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")",
                        "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")",
                        "texto_eliminar_valor_xml_3" => $cadena_unidad_medida,
                        "texto_eliminar_valor_xml_4" => $cadena_unidad_medida);
                    break;
                }
            }
            $tabla_valores_maximos_minimos->anyade_fila("", $fila_tabla_valores_maximos_minimos, $params_fila_valores_maximos_minimos);

            // Nombres de sensores con valores
            $nombres_sensores_valores->anyade_etiqueta($nombre_sensor);

            // Número de sensores
            $numero_sensor++;
        }

        // Se añade el pie de tabla
        $numero_sensores_pie_tabla = $numero_sensores_valores_agregaciones." (".$idiomas->_("agregados").")";
        $tabla_valores_maximos_minimos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_pie_tabla);

        // Si no hay datos no se hace nada
        if ($numero_sensor == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recorren los sensores (para la gráfica de pareto)
        $info_valores_pareto = array();
        $max_valor_pareto = -INF;
        $suma_valores_pareto = 0;
        $numero_sensores_suma_valores_pareto = 0;
        $numero_valor_destacado_pareto = NULL;
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);

            $suma_valores = 0.0;
            $numero_ocurrencias_valores = 0;

            // Filas de valores del sensor (si no hay datos para el sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Se recorren las filas de valores
            foreach ($filas_valores_sensor as $fila)
            {
                if ($fila[$campo] === NULL)
                {
                    continue;
                }

                // Valores de la fila
                $valor = (float) $fila[$campo];
                $cadena_fecha_hora_base_datos_utc = $fila["fecha_hora"];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                $suma_valores += $valor;

                // Se incrementa el número de ocurrencias de valores
                $numero_ocurrencias_valores += 1;
            }

            // Si no hay valores se ignora el sensor
            if ($numero_ocurrencias_valores == 0)
            {
                continue;
            }

            // Valor de la gráfica
            $valor_pareto = $suma_valores;
            if ($campo_incremental == false)
            {
                $valor_pareto /= $numero_ocurrencias_valores;
            }
            array_push($info_valores_pareto, array(
                "nombre_sensor" => $nombre_sensor,
                "valor" => $valor_pareto));

            // Valor máximo y suma de valores
            if ($valor_pareto > $max_valor_pareto)
            {
                $max_valor_pareto = $valor_pareto;
            }
            if (($id_sensor != $id_sensor_destacado) ||
                ($sensor_destacado_en_sensores_agregados == true))
            {
                $suma_valores_pareto += $valor_pareto;
                $numero_sensores_suma_valores_pareto += 1;
            }
        }

        // Media de valores de pareto
        $media_valores_pareto = $suma_valores_pareto / $numero_sensores_suma_valores_pareto;

        // Ordenación de mayor a menor de los valores de pareto
        $valores_ordenacion_pareto = array();
        foreach ($info_valores_pareto as $clave => $fila)
        {
            $valores_ordenacion_pareto[$clave] = $fila["valor"];
        }
        array_multisort($valores_ordenacion_pareto, SORT_DESC, $info_valores_pareto);

        // Tabla de valores de pareto
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_PARETO_ANALISIS_COMPARATIVO,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_PARETO_ANALISIS_COMPARATIVO),
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Posición"),
            $idiomas->_("Sensor"),
           $idiomas->_("Valor")
        );
        $titulo_tabla_valores_pareto = $idiomas->_("Valores de Pareto");
        $tabla_valores_pareto = new TablaDatos(
            "tabla-valores-pareto-analisis-comparativo",
            $titulo_tabla_valores_pareto,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_valores_pareto->anyade_cabecera("", $cabecera_tabla);

        // Gráfica y etiquetas de pareto y número de valor destacado de pareto
        $nombres_sensores_pareto = new VectorDatos();
        for ($i = 0; $i < count($info_valores_pareto); $i++)
        {
            $info_valor_pareto = $info_valores_pareto[$i];
            $nombre_sensor_pareto = $info_valor_pareto["nombre_sensor"];
            $valor_pareto = $info_valor_pareto["valor"];

            $nombres_sensores_pareto->anyade_etiqueta($nombre_sensor_pareto);
            $grafica_pareto->anyade_dato($valor_pareto);

            if ($nombre_sensor_pareto == $nombre_sensor_destacado)
            {
                $numero_valor_destacado_pareto = $i;
            }

            // Datos para tabla de valores de pareto
            $posicion_tabla_pareto = $i + 1;
            $nombre_sensor_tabla_pareto = htmlspecialchars($nombre_sensor_pareto, ENT_QUOTES);
            $valor_tabla_pareto = formatea_numero($valor_pareto, $numero_decimales_valores).$cadena_unidad_medida;
            $fila_tabla_valores_pareto = array(
                $posicion_tabla_pareto,
                $nombre_sensor_tabla_pareto,
                $valor_tabla_pareto);
            $params_fila_valores_pareto = array("texto_eliminar_valores_xml" => $cadena_unidad_medida);
            $tabla_valores_pareto->anyade_fila("", $fila_tabla_valores_pareto, $params_fila_valores_pareto);
        }

        // Se añade el pie de tabla de valores de pareto
        $tabla_valores_pareto->anyade_pie($idiomas->_("Sensores").": ".count($info_valores_pareto));

        // Datos para el mapa de calor de diferencias de valores del sensor destacado con la media de valores
        $valores_mapa_calor_diferencias = new ValoresMapaCalor($tipo_mapa_calor);
        if (($tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO) && ($id_sensor_destacado != ID_NINGUNO))
        {
            $indice_fila_valores_sensor_destacado = 0;
            $filas_sensor_destacado = $filas_valores_sensores[$nombre_sensor_destacado];
            $numero_filas_sensor_destacado = count($filas_sensor_destacado);
            foreach ($filas_media as $fila_media)
            {
                $fecha_hora_media = $fila_media["fecha_hora"];
                $valor_media = $fila_media[$campo];

                $valor_sensor_destacado_encontrado = false;
                $fin_valores_sensor_destacado = false;
                $fecha_mayor_sensor_destacado = false;
                while (true)
                {
                    if ($indice_fila_valores_sensor_destacado > ($numero_filas_sensor_destacado - 1))
                    {
                        $fin_valores_sensor_destacado = true;
                        break;
                    }
                    $fecha_hora_sensor_destacado = $filas_sensor_destacado[$indice_fila_valores_sensor_destacado]["fecha_hora"];
                    $valor_sensor_destacado = $filas_sensor_destacado[$indice_fila_valores_sensor_destacado][$campo];
                    if ($fecha_hora_sensor_destacado == $fecha_hora_media)
                    {
                        $valor_sensor_destacado_encontrado = true;
                        break;
                    }
                    elseif ($fecha_hora_sensor_destacado > $fecha_hora_media)
                    {
                        $fecha_mayor_sensor_destacado = true;
                        break;
                    }
                    elseif ($fecha_hora_sensor_destacado < $fecha_hora_media)
                    {
                        $indice_fila_valores_sensor_destacado += 1;
                    }
                }
                if ($valor_sensor_destacado_encontrado == true)
                {
                    $cadena_fecha_hora_local = cambia_zona_horaria_cadena_fecha_hora($fecha_hora_media, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $fecha_hora_local = convierte_cadena_a_fecha($cadena_fecha_hora_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria);

                    $valor_diferencias = $valor_sensor_destacado - $valor_media;
                    $valores_mapa_calor_diferencias->anyade_valor_fecha_hora($fecha_hora_local, $valor_diferencias);
                }
                else
                {
                    if ($fin_valores_sensor_destacado == true)
                    {
                        break;
                    }
                    elseif ($fecha_mayor_sensor_destacado == true)
                    {
                        continue;
                    }
                }
            }
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($max_sumas_valores_sensores == -INF)
        {
            $max_sumas_valores_sensores = "ND";
        }
        if ($max_valor_pareto == -INF)
        {
            $max_valor_pareto = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "campo_incremental" => $campo_incremental,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "tabla_valores_maximos_minimos" => $tabla_valores_maximos_minimos->dame_tabla(),
            "grafica_pareto" => $grafica_pareto->dame_datos(),
            "tabla_valores_pareto" => $tabla_valores_pareto->dame_tabla(),
            "dias_mapa_calor_diferencias" => $valores_mapa_calor_diferencias->dame_dias(),
            "datos_mapa_calor_diferencias" => $valores_mapa_calor_diferencias->dame_datos(),
            "min_valores" => $min_valores_sensores,
            "max_valores" => $max_valores_sensores,
            "max_sumas_valores" => $max_sumas_valores_sensores,
            "max_valor_pareto" => $max_valor_pareto,
            "media_valores_pareto" => $media_valores_pareto,
            "numero_valor_destacado_pareto" => $numero_valor_destacado_pareto,
            "etiquetas_valores" => $nombres_sensores_valores->dame_datos(),
            "etiquetas_pareto" => $nombres_sensores_pareto->dame_datos(),
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_campo" => $descripcion_campo,
            "id_sensor_destacado" => $id_sensor_destacado,
            "nombre_sensor_destacado" => $nombre_sensor_destacado);
        return ($resultado);
    }


    // Devuelve la información de valores generales de sensores
    function dame_valores_generales_sensores($parametros)
    {
        $idiomas = new Idiomas();

        // Devuelve los ids y nombres de sensores agrupados por clase
        anyade_ids_nombres_sensores_agrupados_clase($parametros);

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clases_sensor = $parametros["clases_sensor"];
        $campos = $parametros["campos"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $agregacion = $parametros["agregacion"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Parámetros añadidos
        $ids_sensores_clases = $parametros["ids_sensores_clases"];

        // Comprobaciones de clases de sensor y campos compatibles
        $res_compatibles = comprueba_clases_sensor_campos_compatibles(
            $id_ratio,
            $clases_sensor,
            $ids_sensores_clases,
            $campos,
            $intervalo_valores,
            TIPO_INFORME_SENSORES_VALORES_GENERALES);
        if ($res_compatibles["res"] == "ERROR")
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $res_compatibles["msg"]);
            return ($resultado);
        }
        else
        {
            $aplicar_ratio = $res_compatibles["aplicar_ratio"];
            $numero_decimales_valores = $res_compatibles["numero_decimales_valores"];
            $unidad_medida = $res_compatibles["unidad_medida"];
            $tipo_valores_campo = $res_compatibles["tipo_valores_campo"];
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Se recuperan las filas de valores de los sensores
        $filas_valores_sensores = dame_filas_valores_sensores_clases($parametros);

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Agregación de valores (si la hay)
        if ($agregacion != AGREGACION_NINGUNA)
        {
            $info_sensores_clases_agregacion = dame_info_sensores_clases_agregacion(
                $id_ratio,
                $agregacion,
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
                $filas_valores_sensores);
            if ($info_sensores_clases_agregacion["hay_datos"] == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
            else
            {
                $ids_sensores = $info_sensores_clases_agregacion["ids_sensores"];
                $nombres_sensores = $info_sensores_clases_agregacion["nombres_sensores"];
                $filas_valores_sensores = $info_sensores_clases_agregacion["filas_valores_sensores"];
                $cadena_numero_sensores_valores_agregaciones = $info_sensores_clases_agregacion["cadena_numero_sensores_valores_agregaciones"];
            }
        }

        // Variables
        $numero_sensor = 0;
        $grafica_valores = new VectorDatos();
        $grafica_valores_acumulados = new VectorDatos();
        $nombres_sensores_valores = new VectorDatos();
        $min_valores_sensores = INF;
        $max_valores_sensores = -INF;
        $max_sumas_valores_sensores = 0.0;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Tabla de valores máximos y mínimos
        $titulo_columna_sensor = $idiomas->_("Sensor");
        switch ($tipo_valores_campo)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_PUNTUAL,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_PUNTUAL),
                    "generar_valores_xml" => true
                );
               $cabecera_tabla = array(
                    $titulo_columna_sensor,
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo"),
                    $idiomas->_("Media")
                );
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_INCREMENTAL,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_MAXIMOS_MINIMOS_VALORES_GENERALES_INCREMENTAL),
                    "generar_valores_xml" => true
                );
                $cabecera_tabla = array(
                    $titulo_columna_sensor,
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo"),
                    $idiomas->_("Media por hora"),
                    $idiomas->_("Total")
                );
                break;
            }
        }
        $titulo_tabla_valores_maximos_minimos = $idiomas->_("Valores máximos y mínimos");
        $tabla_valores_maximos_minimos = new TablaDatos(
            "tabla-valores-maximos-minimos-valores-generales",
            $titulo_tabla_valores_maximos_minimos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_valores_maximos_minimos->anyade_cabecera("", $cabecera_tabla);

        // Horas en una fila (para los campos incrementales sin horas - p.e. grados día)
        $segundos_intervalo_valores = dame_segundos_intervalo_valores($intervalo_valores);
        if ($segundos_intervalo_valores === NULL)
        {
            $horas_fila = NULL;
        }
        else
        {
            $horas_fila = $segundos_intervalo_valores / 3600;
        }

        // Flag de número máximo de sensores para dibujado de gráficas superado
        $limite_sensores_graficas_superado = false;

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Máximos y mínimos
            $max_valor = (float) -INF;
            $cadena_fecha_hora_max_valor_base_datos_utc = "";
            $min_valor = (float) INF;
            $cadena_fecha_hora_min_valor_base_datos_utc = "";

            // Sumas y número de valores
            $suma_horas = 0.0;
            $suma_valores = 0.0;
            $numero_ocurrencias_valores = 0;

            // Filas de valores del sensor (si no hay datos para el sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Campo (depende de la clase y de si es el valor agregado)
            if ($id_sensor < 0)
            {
                $campo = "valor_agregacion";
            }
            else
            {
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
            }

            // Se recupera la información del ratio (si aplica)
            if (($aplicar_ratio == true) && ($id_sensor > 0))
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

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

            // Número máximo de sensores para dibujado de gráficas
            if ($numero_sensor == NUMERO_MAXIMO_SENSORES_GRAFICAS_VALORES_GENERALES)
            {
                $limite_sensores_graficas_superado = true;

                // Se eliminan todos los datos de las gráficas (no se van a dibujar)
                $grafica_valores = new VectorDatos();
                $grafica_valores_acumulados = new VectorDatos();
            }
            if ($limite_sensores_graficas_superado == false)
            {
                $datos_sensor_valores = new VectorDatos();
                $datos_sensor_valores_acumulados = new VectorDatos();
            }

            // Se recorren las filas de valores
            $timestamp_fecha_hora_valor_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica = 0;
            foreach ($filas_valores_sensor as $fila_valor_sensor)
            {
                // Fecha y valor
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                if ($valor === NULL)
                {
                    continue;
                }

                // Se incrementan las horas totales
                // Nota: Si el sensor no es incremental y es tiempo real, no hay horas en la fila (p.e. grados día)
                if ($campo_incremental == true)
                {
                    if (array_key_exists("horas", $fila_valor_sensor) == true)
                    {
                        $suma_horas += $fila_valor_sensor["horas"];
                    }
                    else
                    {
                        if ($horas_fila !== NULL)
                        {
                            $suma_horas += $horas_fila;
                        }
                    }
                }

                // Suma de valores, máximos y mínimos
                if (($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL) || ($cadena_fecha_hora_base_datos_utc < $cadena_fecha_hora_inicio_valores_base_datos_utc))
                {
                    $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if (($cadena_fecha_hora_fin_valores_base_datos_utc === NULL) || ($cadena_fecha_hora_base_datos_utc > $cadena_fecha_hora_fin_valores_base_datos_utc))
                {
                    $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($valor > $max_valores_sensores)
                {
                    $max_valores_sensores = $valor;
                }
                if ($valor < $min_valores_sensores)
                {
                    $min_valores_sensores = $valor;
                }
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                    $cadena_fecha_hora_max_valor_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                    $cadena_fecha_hora_min_valor_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                $suma_valores += $valor;

                // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
                if ($limite_sensores_graficas_superado == false)
                {
                    // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                    $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                    if (($numero_puntos_seguidos_grafica > 1) &&
                        ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
                    {
                        $segundos_entre_incrementos = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                        if ($segundos_entre_incrementos > $segundos_maximos_entre_valores_grafica)
                        {
                            $numero_puntos_seguidos_grafica = 0;
                            $datos_sensor_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                            if ($campo_incremental == true)
                            {
                                $datos_sensor_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                            }
                        }
                    }
                    $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
                    $numero_puntos_seguidos_grafica += 1;

                    // Se añaden los valores
                    switch ($agregacion)
                    {
                        case AGREGACION_NINGUNA:
                        {
                            $datos_sensor_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $valor);
                            if ($campo_incremental == true)
                            {
                                $datos_sensor_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
                            }
                            break;
                        }
                        case AGREGACION_SUMA:
                        case AGREGACION_MEDIA:
                        case AGREGACION_SUMA_CLASES:
                        case AGREGACION_MEDIA_CLASES:
                        {
                            $tooltips_agregacion = dame_tooltips_agregacion_sensores(
                                NULL,
                                $cadena_fecha_hora_base_datos_utc,
                                $valor,
                                $suma_valores,
                                $unidad_medida,
                                NULL,
                                $fila_valor_sensor["numero_sensores_sin_valor"]);

                            $datos_sensor_valores->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_valor_utc,
                                $valor,
                                $tooltips_agregacion["tooltip_valor"]);
                            if ($campo_incremental == true)
                            {
                                $datos_sensor_valores_acumulados->anyade_tupla_pareja_datos_etiqueta(
                                    $timestamp_fecha_hora_valor_utc,
                                    $suma_valores,
                                    $tooltips_agregacion["tooltip_suma_valores"]);
                            }
                            break;
                        }
                    }
                }

                // Se incrementa el número de ocurrencias de valores
                $numero_ocurrencias_valores += 1;
            }

            // Si no hay valores se ignora el sensor
            if ($numero_ocurrencias_valores == 0)
            {
                continue;
            }

            // Máximo de suma de valores
            if ($suma_valores > $max_sumas_valores_sensores)
            {
                $max_sumas_valores_sensores = $suma_valores;
            }

            // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
            if ($limite_sensores_graficas_superado == false)
            {
                $grafica_valores->anyade_dato($datos_sensor_valores->dame_datos());
                if ($campo_incremental == true)
                {
                    $grafica_valores_acumulados->anyade_dato($datos_sensor_valores_acumulados->dame_datos());
                }
            }

            // Formatos de fecha
            $formato_fecha_origen = FORMATO_FECHA_HORA_BASE_DATOS;
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                case INTERVALO_VALORES_SEMANA:
                case INTERVALO_VALORES_MES:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_local"];
                    break;
                }
                default:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_hora_local"];
                    break;
                }
            }

            // Datos para tabla de valores máximos y mínimos
            $nombre_sensor_tabla_valores_maximos_minimos = htmlspecialchars($nombre_sensor, ENT_QUOTES);
            $cadena_fecha_hora_max_valor_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_max_valor_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_max_valor_local_local = convierte_formato_fecha($cadena_fecha_hora_max_valor_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $cadena_fecha_hora_min_valor_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_min_valor_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_min_valor_local_local = convierte_formato_fecha($cadena_fecha_hora_min_valor_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $maximo_tabla_valores_maximos_minimos = formatea_numero($max_valor, $numero_decimales_valores).$cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")";
            $minimo_tabla_valores_maximos_minimos = formatea_numero($min_valor, $numero_decimales_valores).$cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")";
            switch ($tipo_valores_campo)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $media_valores = $suma_valores / $numero_ocurrencias_valores;
                    $media_tabla_valores_maximos_minimos = formatea_numero($media_valores, $numero_decimales_valores).$cadena_unidad_medida;
                    $fila_tabla_valores_maximos_minimos = array(
                        $nombre_sensor_tabla_valores_maximos_minimos,
                        $maximo_tabla_valores_maximos_minimos,
                        $minimo_tabla_valores_maximos_minimos,
                        $media_tabla_valores_maximos_minimos);
                    $params_fila_valores_maximos_minimos = array(
                        "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")",
                        "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")",
                        "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    if ($suma_horas == 0)
                    {
                        $media_horaria_tabla_valores_maximos_minimos = $idiomas->_("ND");
                    }
                    else
                    {
                        $media_valores_por_hora = $suma_valores / $suma_horas;
                        $media_horaria_tabla_valores_maximos_minimos = formatea_numero($media_valores_por_hora, $numero_decimales_valores).$cadena_unidad_medida;
                    }

                    $total_tabla_valores_maximos_minimos = formatea_numero($suma_valores, $numero_decimales_valores).$cadena_unidad_medida;
                    $fila_tabla_valores_maximos_minimos = array(
                        $nombre_sensor_tabla_valores_maximos_minimos,
                        $maximo_tabla_valores_maximos_minimos,
                        $minimo_tabla_valores_maximos_minimos,
                        $media_horaria_tabla_valores_maximos_minimos,
                        $total_tabla_valores_maximos_minimos);
                    $params_fila_valores_maximos_minimos = array(
                        "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$cadena_fecha_hora_max_valor_local_local.")",
                        "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$cadena_fecha_hora_min_valor_local_local.")",
                        "texto_eliminar_valor_xml_3" => $cadena_unidad_medida,
                        "texto_eliminar_valor_xml_4" => $cadena_unidad_medida);
                    break;
                }
            }
            $tabla_valores_maximos_minimos->anyade_fila("", $fila_tabla_valores_maximos_minimos, $params_fila_valores_maximos_minimos);

            // Valor agregado (es el valor para el widget de valor agregado de valores generales)
            // Nota: Solo puede haber un valor porque la agregación tiene que ser suma o media en este widget
            switch ($tipo_valores_campo)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $valor_agregado = $media_valores;
                    $texto_valor_agregado_sin_unidad = formatea_numero($media_valores, $numero_decimales_valores);
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $valor_agregado = $suma_valores;
                    $texto_valor_agregado_sin_unidad = formatea_numero($suma_valores, $numero_decimales_valores);
                    break;
                }
            }

            // Nombres de sensores con valores
            $nombres_sensores_valores->anyade_etiqueta($nombre_sensor);

            // Número de sensores
            $numero_sensor++;
        }

        // Se añade el pie de tabla
        if ($agregacion == AGREGACION_NINGUNA)
        {
            $numero_sensores_pie_tabla = $numero_sensor;
        }
        else
        {
            $numero_sensores_pie_tabla = $cadena_numero_sensores_valores_agregaciones." (".$idiomas->_("agregados").")";
        }
        $tabla_valores_maximos_minimos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_pie_tabla);

        // Si no hay datos no se hace nada
        if ($numero_sensor == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Conversión de fechas
        $cadena_fecha_hora_inicio_valores_jqplot_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);
        $cadena_fecha_hora_fin_valores_jqplot_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($max_sumas_valores_sensores == -INF)
        {
            $max_sumas_valores_sensores = "ND";
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "limite_sensores_graficas_superado" => $limite_sensores_graficas_superado,
            "campo_incremental" => $campo_incremental,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "tabla_valores_maximos_minimos" => $tabla_valores_maximos_minimos->dame_tabla(),
            "min_valores" => $min_valores_sensores,
            "max_valores" => $max_valores_sensores,
            "max_sumas_valores" => $max_sumas_valores_sensores,
            "etiquetas_valores" => $nombres_sensores_valores->dame_datos(),
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "valor_agregado" => $valor_agregado,
            "texto_valor_agregado_sin_unidad" => $texto_valor_agregado_sin_unidad,
            "fecha_hora_inicio_valores" => $cadena_fecha_hora_inicio_valores_jqplot_utc,
            "fecha_hora_fin_valores" => $cadena_fecha_hora_fin_valores_jqplot_utc);
        return ($resultado);
    }


    // Devuelve la información de incrementos totales de sensores
    function dame_incrementos_totales_sensores($parametros)
    {
        $idiomas = new Idiomas();

        // Devuelve los ids y nombres de sensores agrupados por clase
        anyade_ids_nombres_sensores_agrupados_clase($parametros);

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clases_sensor = $parametros["clases_sensor"];
        $campos = $parametros["campos"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $agregacion = $parametros["agregacion"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Parámetros añadidos
        $ids_sensores_clases = $parametros["ids_sensores_clases"];

        // Comprobaciones de clases de sensor y campos compatibles
        $res_compatibles = comprueba_clases_sensor_campos_compatibles(
            $id_ratio,
            $clases_sensor,
            $ids_sensores_clases,
            $campos,
            $intervalo_valores);
        if ($res_compatibles["res"] == "ERROR")
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $res_compatibles["msg"]);
            return ($resultado);
        }
        else
        {
            $aplicar_ratio = $res_compatibles["aplicar_ratio"];
            $numero_decimales_valores = $res_compatibles["numero_decimales_valores"];
            $unidad_medida = $res_compatibles["unidad_medida"];
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Se recuperan las filas de valores de los sensores
        $filas_valores_sensores = dame_filas_valores_sensores_clases($parametros);

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Agregación de valores (si la hay)
        if ($agregacion != AGREGACION_NINGUNA)
        {
            $info_sensores_clases_agregacion = dame_info_sensores_clases_agregacion(
                $id_ratio,
                $agregacion,
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
                $filas_valores_sensores);
            if ($info_sensores_clases_agregacion["hay_datos"] == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
            else
            {
                $ids_sensores = $info_sensores_clases_agregacion["ids_sensores"];
                $nombres_sensores = $info_sensores_clases_agregacion["nombres_sensores"];
                $filas_valores_sensores = $info_sensores_clases_agregacion["filas_valores_sensores"];
                $cadena_numero_sensores_valores_agregaciones = $info_sensores_clases_agregacion["cadena_numero_sensores_valores_agregaciones"];
            }
        }

        // Variables
        $numero_sensor = 0;
        $grafica_incrementos_totales = new VectorDatos();
        $datos_porcentajes_incrementos = new VectorDatos();
        $grafica_porcentajes_incrementos = new VectorDatos();
        $nombres_sensores_incrementos = new VectorDatos();
        $max_incrementos_totales = 0.0;
        $total_incrementos = 0.0;
        $info_incrementos_totales = array();
        $datos_incrementos_sensores = array();
        $incrementos_totales_fechas_horas = array();
        $cadena_fecha_hora_inicio_incrementos_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_incrementos_base_datos_utc = NULL;
        $fechas_horas_datos_incrementos_sensores_local = array();

        // Flag de número máximo de sensores para dibujado de gráficas superado
        $limite_sensores_graficas_superado = false;

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Incremento total
            $incremento_total = NULL;

            // Filas de valores del sensor (si no hay datos para el sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Campo (depende de la clase y de si es el valor agregado)
            if ($id_sensor < 0)
            {
                $campo = "valor_agregacion";
            }
            else
            {
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
            }

            // Se recupera la información del ratio (si aplica)
            if (($aplicar_ratio == true) && ($id_sensor > 0))
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_local_local,
                    $cadena_fecha_hora_fin_local_local,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Se recorren las filas de valores
            foreach ($filas_valores_sensor as $fila_valor_sensor)
            {
                // Fecha y valor
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $incremento = $fila_valor_sensor[$campo];
                if ($incremento !== NULL)
                {
                    $incremento = (float) $incremento;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $incremento);
                    }
                }
                if ($incremento === NULL)
                {
                    continue;
                }

                // Suma de incrementos, máximos y mínimos
                if (($cadena_fecha_hora_inicio_incrementos_base_datos_utc === NULL) || ($cadena_fecha_hora_base_datos_utc < $cadena_fecha_hora_inicio_incrementos_base_datos_utc))
                {
                    $cadena_fecha_hora_inicio_incrementos_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if (($cadena_fecha_hora_fin_incrementos_base_datos_utc === NULL) || ($cadena_fecha_hora_base_datos_utc > $cadena_fecha_hora_fin_incrementos_base_datos_utc))
                {
                    $cadena_fecha_hora_fin_incrementos_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($incremento_total === NULL)
                {
                    $incremento_total = 0.0;
                }
                $incremento_total += $incremento;

                // Datos para la gráfica de incrementos (apilados) (sólo si no es tiempo real)
                if ($intervalo_valores != INTERVALO_VALORES_TIEMPO_REAL)
                {
                    // Conversión a hora local
                    $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $fecha_hora_local = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria);

                    // Se añade la cadena
                    if (in_array($fecha_hora_local, $fechas_horas_datos_incrementos_sensores_local) == false)
                    {
                        array_push($fechas_horas_datos_incrementos_sensores_local, $fecha_hora_local);
                    }

                    // Se añade la información de incremento del sensor
                    if (array_key_exists($id_sensor, $datos_incrementos_sensores) == false)
                    {
                        $datos_incrementos_sensores[$id_sensor] = array(
                            "fechas_horas" => array(),
                            "incrementos" => array());
                    }
                    array_push($datos_incrementos_sensores[$id_sensor]["fechas_horas"], $fecha_hora_local);
                    array_push($datos_incrementos_sensores[$id_sensor]["incrementos"], $incremento);

                    // Máximo de incrementos por fechas y horas (para los máximos de las gráficas)
                    // Nota: El índice de un vector no puede ser una fecha (por eso se utiliza la cadena)
                    if (array_key_exists($cadena_fecha_hora_base_datos_local, $incrementos_totales_fechas_horas) == false)
                    {
                        $incrementos_totales_fechas_horas[$cadena_fecha_hora_base_datos_local] = 0;
                    }
                    $incrementos_totales_fechas_horas[$cadena_fecha_hora_base_datos_local] += $incremento;
                }
            }
            if ($incremento_total !== NULL)
            {
                if ($incremento_total > $max_incrementos_totales)
                {
                    $max_incrementos_totales = $incremento_total;
                }

                // Gráficas
                $grafica_incrementos_totales->anyade_tupla_dato($incremento_total);
                $datos_porcentajes_incrementos->anyade_tupla_etiqueta_dato($nombre_sensor, $incremento_total);

                // Información de incrementos totales (para la tabla de incrementos)
                $info_incremento_total = array(
                    htmlspecialchars($nombre_sensor, ENT_QUOTES),
                    $incremento_total);
                array_push($info_incrementos_totales, $info_incremento_total);

                // Nombres de sensores
                $nombres_sensores_incrementos->anyade_etiqueta($nombre_sensor);

                // Total de incrementos y número de sensores
                $total_incrementos += $incremento_total;
                $numero_sensor++;
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_sensor == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se añaden los datos a la gráfica de porcentajes
        $grafica_porcentajes_incrementos->anyade_dato($datos_porcentajes_incrementos->dame_datos());

        // Tablas de incrementos (totales y porcentajes)
        $params_tabla_incrementos = array(
            "numero_columnas" =>  NUMERO_COLUMNAS_TABLA_INCREMENTOS_INCREMENTOS_TOTALES,
            "generar_valores_xml" => true
        );
        $titulo_tabla_incrementos = $idiomas->_("Incrementos");
        $tabla_incrementos = new TablaDatos(
            "tabla-incrementos-incrementos-totales",
            $titulo_tabla_incrementos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_incrementos
        );
        $cabecera_tabla_incrementos = array(
            $idiomas->_("Sensor"),
            $idiomas->_("Total"),
            $idiomas->_("Porcentaje")
        );
        $tabla_incrementos->anyade_cabecera("", $cabecera_tabla_incrementos);

        foreach ($info_incrementos_totales as $info_incremento_total)
        {
            $porcentaje_incremento = ($info_incremento_total[1] * 100) / $total_incrementos;
            $fila_tabla_incrementos_totales_porcentajes = array(
                $info_incremento_total[0],
                formatea_numero($info_incremento_total[1], $numero_decimales_valores)." ".$unidad_medida,
                formatea_numero($porcentaje_incremento, 2)." %");
            $params_fila_incrementos_totales_porcentajes = array("textos_eliminar_valores_xml" => array(
                $cadena_unidad_medida,
                " %"));
            $tabla_incrementos->anyade_fila("", $fila_tabla_incrementos_totales_porcentajes, $params_fila_incrementos_totales_porcentajes);
        }

        // Se añade el pie de tabla
        if ($agregacion == AGREGACION_NINGUNA)
        {
            $numero_sensores_pie_tabla = $numero_sensor;
        }
        else
        {
            $numero_sensores_pie_tabla = $cadena_numero_sensores_valores_agregaciones." (".$idiomas->_("agregados").")";
        }
        $tabla_incrementos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_pie_tabla);

        // Conversión de fechas
        $cadena_fecha_hora_inicio_incrementos_jqplot_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_incrementos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);
        $cadena_fecha_hora_fin_incrementos_jqplot_utc = convierte_formato_fecha($cadena_fecha_hora_fin_incrementos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);

        // Gráficas de incrementos e incrementos acumulados (apilados)
        $datos_grafica_incrementos = NULL;
        $datos_grafica_incrementos_acumulados = NULL;
        $max_incrementos = NULL;
        if ($numero_sensor > NUMERO_MAXIMO_SENSORES_GRAFICAS_INCREMENTOS_TOTALES)
        {
            $limite_sensores_graficas_superado = true;
        }
        else
        {
            if ($intervalo_valores != INTERVALO_VALORES_TIEMPO_REAL)
            {
                $grafica_incrementos = new VectorDatos();
                $grafica_incrementos_acumulados = new VectorDatos();
                sort($fechas_horas_datos_incrementos_sensores_local);
                foreach ($datos_incrementos_sensores as $id_sensor => $datos_incrementos_sensor)
                {
                    // Nota: Hay que recorrer todas las fechas (entre la fecha mínima y máxima de valores) para en aquellas fechas
                    // en las que no hay valores añadir un valor nulo (0.0)
                    $datos_incrementos_sensor_fecha_hora = new VectorDatos();
                    $datos_incrementos_acumulados_sensor_fecha_hora = new VectorDatos();
                    $indice_datos_incremento_sensor_fecha_hora = 0;
                    $incremento_acumulado_fecha_hora = 0;
                    foreach ($fechas_horas_datos_incrementos_sensores_local as $fecha_hora_datos_incrementos_sensores_local)
                    {
                        if ($datos_incrementos_sensor["fechas_horas"][$indice_datos_incremento_sensor_fecha_hora] == $fecha_hora_datos_incrementos_sensores_local)
                        {
                            $incremento_fecha_hora = $datos_incrementos_sensor["incrementos"][$indice_datos_incremento_sensor_fecha_hora];
                            $indice_datos_incremento_sensor_fecha_hora += 1;
                        }
                        else
                        {
                            $incremento_fecha_hora = 0.0;
                        }

                        // Se le suma la mitad del periodo de tiempo (para que se muestre correctamente en la gráfica de barras)
                        $numero_segundos_mitad_periodo_tiempo = NULL;
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_CUARTOHORA:
                            {
                                $numero_segundos_mitad_periodo_tiempo = 450;
                                break;
                            }
                            case INTERVALO_VALORES_HORA:
                            {
                                $numero_segundos_mitad_periodo_tiempo = 1800;
                                break;
                            }
                            case INTERVALO_VALORES_DIA:
                            {
                                $numero_segundos_mitad_periodo_tiempo = 43200;
                                break;
                            }
                            case INTERVALO_VALORES_SEMANA:
                            {
                                $numero_segundos_mitad_periodo_tiempo = 302400;
                                break;
                            }
                            case INTERVALO_VALORES_MES:
                            {
                                $mes = date_format($fecha_hora_datos_incrementos_sensores_local, "n");
                                $anyo = date_format($fecha_hora_datos_incrementos_sensores_local, "y");
                                $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anyo);
                                $numero_segundos_mitad_periodo_tiempo = $numero_dias_mes * 43200;
                                break;
                            }
                            default:
                            {
                                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
                            }
                        }
                        $duracion_mitad_periodo_tiempo = new DateInterval("PT".$numero_segundos_mitad_periodo_tiempo."S");
                        $fecha_hora_local = clone $fecha_hora_datos_incrementos_sensores_local;
                        $fecha_hora_local->add($duracion_mitad_periodo_tiempo);
                        $cadena_fecha_hora_jqplot_local = convierte_fecha_a_cadena($fecha_hora_local, FORMATO_FECHA_HORA_JQPLOT);
                        $cadena_fecha_hora_local_local = convierte_fecha_a_cadena($fecha_hora_datos_incrementos_sensores_local, $_SESSION["formato_fecha_hora_local"]);

                        // Se añaden los datos de las gráficas
                        $datos_incrementos_sensor_fecha_hora->anyade_tupla_etiqueta_dato_etiqueta(
                            $cadena_fecha_hora_jqplot_local,
                            $incremento_fecha_hora,
                            $cadena_fecha_hora_local_local);
                        $incremento_acumulado_fecha_hora += $incremento_fecha_hora;
                        $datos_incrementos_acumulados_sensor_fecha_hora->anyade_tupla_etiqueta_dato_etiqueta(
                            $cadena_fecha_hora_jqplot_local,
                            $incremento_acumulado_fecha_hora,
                            $cadena_fecha_hora_local_local);
                    }
                    $grafica_incrementos->anyade_dato($datos_incrementos_sensor_fecha_hora->dame_datos());
                    $grafica_incrementos_acumulados->anyade_dato($datos_incrementos_acumulados_sensor_fecha_hora->dame_datos());
                }
                $datos_grafica_incrementos = $grafica_incrementos->dame_datos();
                $datos_grafica_incrementos_acumulados = $grafica_incrementos_acumulados->dame_datos();

                // Máximo de incrementos por fechas y horas (para los máximos de las gráficas)
                $max_incrementos = max($incrementos_totales_fechas_horas);

                // Fechas mínima y máxima
                $min_fecha_hora_datos_incrementos_sensores_local = $fechas_horas_datos_incrementos_sensores_local[0];
                $max_fecha_hora_datos_incrementos_sensores_local = $fechas_horas_datos_incrementos_sensores_local[count($fechas_horas_datos_incrementos_sensores_local) - 1];

                // Nota: Para la gráfica se suma un intervalo de tiempo (si no no se muestra la última barra)
                $min_fecha_hora_datos_incrementos_sensores_grafica_local = clone $min_fecha_hora_datos_incrementos_sensores_local;
                $max_fecha_hora_datos_incrementos_sensores_grafica_local = clone $max_fecha_hora_datos_incrementos_sensores_local;
                $max_fecha_hora_datos_incrementos_sensores_grafica_local->add($duracion_mitad_periodo_tiempo);
                $max_fecha_hora_datos_incrementos_sensores_grafica_local->add($duracion_mitad_periodo_tiempo);

                // Nota: Si cambia el horario de verano o de invierno, se suma o resta 1 hora para que se muestren todas las fechas en la gráfica
                $horario_verano_min_fecha = dame_horario_verano_fecha_hora_utc($min_fecha_hora_datos_incrementos_sensores_grafica_local, $zona_horaria);
                $horario_verano_max_fecha = dame_horario_verano_fecha_hora_utc($max_fecha_hora_datos_incrementos_sensores_grafica_local, $zona_horaria);
                if (($horario_verano_min_fecha == true) && ($horario_verano_max_fecha == false))
                {
                    $max_fecha_hora_datos_incrementos_sensores_grafica_local->sub(new DateInterval("PT1H"));
                }
                if (($horario_verano_min_fecha == false) && ($horario_verano_max_fecha == true))
                {
                    $max_fecha_hora_datos_incrementos_sensores_grafica_local->add(new DateInterval("PT1H"));
                }
                $cadena_min_fecha_hora_datos_incrementos_sensores_grafica_local = convierte_fecha_a_cadena($min_fecha_hora_datos_incrementos_sensores_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
                $cadena_max_fecha_hora_datos_incrementos_sensores_grafica_local = convierte_fecha_a_cadena($max_fecha_hora_datos_incrementos_sensores_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
            }
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($max_incrementos_totales == -INF)
        {
            $max_incrementos_totales = "ND";
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "limite_sensores_graficas_superado" => $limite_sensores_graficas_superado,
            "grafica_incrementos_totales" => $grafica_incrementos_totales->dame_datos(),
            "grafica_porcentajes_incrementos" => $grafica_porcentajes_incrementos->dame_datos(),
            "grafica_incrementos" => $datos_grafica_incrementos,
            "grafica_incrementos_acumulados" => $datos_grafica_incrementos_acumulados,
            "tabla_incrementos" => $tabla_incrementos->dame_tabla(),
            "max_incrementos_totales" => $max_incrementos_totales,
            "max_incrementos" => $max_incrementos,
            "max_incrementos_acumulados" => $total_incrementos,
            "etiquetas_incrementos" => $nombres_sensores_incrementos->dame_datos(),
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "fecha_hora_inicio_incrementos" => $cadena_fecha_hora_inicio_incrementos_jqplot_utc,
            "fecha_hora_fin_incrementos" => $cadena_fecha_hora_fin_incrementos_jqplot_utc,
            "min_fecha_graficas_incrementos" => $cadena_min_fecha_hora_datos_incrementos_sensores_grafica_local,
            "max_fecha_graficas_incrementos" => $cadena_max_fecha_hora_datos_incrementos_sensores_grafica_local);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_sensores_comparacion_periodos()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_TABLA_EVOLUCION_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS_ACUMULADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_MAPA_CALOR_DIFERENCIAS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_comparacion_perfil_horario()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS_ACUMULADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES_PERFIL_HORARIO);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_MAPA_CALOR_DIFERENCIAS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_comparacion_campos_iguales()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TABLA_DIFERENCIAS_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_DIFERENCIAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_MAPAS_CALOR_DIFERENCIAS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_comparacion_campos_diferentes()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_GRAFICA_VALORES);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_analisis_comparativo()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_MAXIMOS_MINIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_PARETO);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_PARETO);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_MAPA_CALOR_DIFERENCIAS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_valores_generales()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_TABLA_VALORES_MAXIMOS_MINIMOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_incrementos_totales()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_PORCENTAJES_INCREMENTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_TABLA_INCREMENTOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_sensores_comparacion_periodos($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_TABLA_EVOLUCION_VALORES:
            {
                $descripcion = "Tabla de evolución de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS:
            {
                $descripcion = "Gráfica de diferencias";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS_ACUMULADAS:
            {
                $descripcion = "Gráfica de diferencias acumuladas";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_MAPA_CALOR_DIFERENCIAS:
            {
                $descripcion = "Mapa de calor de diferencias";
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


    function dame_descripcion_elemento_informe_sensores_comparacion_perfil_horario($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS:
            {
                $descripcion = "Gráfica de diferencias";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS_ACUMULADAS:
            {
                $descripcion = "Gráfica de diferencias acumuladas";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_VALORES_PERFIL_HORARIO:
            {
                $descripcion = "Gráfica de valores de perfil horario";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_MAPA_CALOR_DIFERENCIAS:
            {
                $descripcion = "Mapa de calor de diferencias";
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


    function dame_descripcion_elemento_informe_sensores_comparacion_campos_iguales($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TABLA_DIFERENCIAS_VALORES:
            {
                $descripcion = "Tabla de diferencias de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_DIFERENCIAS:
            {
                $descripcion = "Gráfica de diferencias";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_MAPAS_CALOR_DIFERENCIAS:
            {
                $descripcion = "Mapas de calor de diferencias";
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


    function dame_descripcion_elemento_informe_sensores_comparacion_campos_diferentes($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
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


    function dame_descripcion_elemento_informe_sensores_analisis_comparativo($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_VALORES_ACUMULADOS:
            {
                $descripcion = "Gráfica de valores acumulados";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_MAXIMOS_MINIMOS:
            {
                $descripcion = "Tabla de valores máximos y mínimos";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_GRAFICA_PARETO:
            {
                $descripcion = "Gráfica de Pareto";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_TABLA_VALORES_PARETO:
            {
                $descripcion = "Tabla de valores de Pareto";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_MAPA_CALOR_DIFERENCIAS:
            {
                $descripcion = "Mapa de calor de diferencias";
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


    function dame_descripcion_elemento_informe_sensores_valores_generales($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_GRAFICA_VALORES_ACUMULADOS:
            {
                $descripcion = "Gráfica de valores acumulados";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_VALORES_GENERALES_TABLA_VALORES_MAXIMOS_MINIMOS:
            {
                $descripcion = "Tabla de valores máximos y mínimos";
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


    function dame_descripcion_elemento_informe_sensores_incrementos_totales($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_TOTALES:
            {
                $descripcion = "Gráfica de incrementos totales";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_PORCENTAJES_INCREMENTOS:
            {
                $descripcion = "Gráfica de porcentajes de incrementos";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS:
            {
                $descripcion = "Gráfica de incrementos";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de incrementos acumulados";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_TABLA_INCREMENTOS:
            {
                $descripcion = "Tabla de incrementos";
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


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_sensores_comparacion_periodos($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-comparacion-periodos'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-comparacion-periodos' hidden>
                        <div class='grafica100' id='grafica-valores-comparacion-periodos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-evolucion-valores-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-diferencias-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-diferencias-acumuladas-comparacion-periodos'></div>
                        <div class='mapa-calor100' id='mapa-calor-diferencias-comparacion-periodos'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Comparación de periodos (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-periodos-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-periodos-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-valores-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-acumuladas-comparacion-periodos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Comparación de periodos (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-periodos-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_PERIODOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-periodos-2'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-diferencias-comparacion-periodos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_comparacion_perfil_horario($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-comparacion-perfil-horario'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-comparacion-perfil-horario' hidden>
                        <div class='grafica100' id='grafica-valores-comparacion-perfil-horario'></div>
                        <div class='grafica100' id='grafica-diferencias-comparacion-perfil-horario'></div>
                        <div class='grafica100' id='grafica-diferencias-acumuladas-comparacion-perfil-horario'></div>
                        <div class='grafica100' id='grafica-valores-perfil-horario-comparacion-perfil-horario'></div>
                        <div class='mapa-calor100' id='mapa-calor-diferencias-comparacion-perfil-horario'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Comparación de perfil horario (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-perfil-horario-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-perfil-horario-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-acumuladas-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-perfil-horario-comparacion-perfil-horario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Comparación de perfil horario (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-perfil-horario-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-perfil-horario-2'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-diferencias-comparacion-perfil-horario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_comparacion_campos_iguales($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-comparacion-campos-iguales'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-comparacion-campos-iguales' hidden>
                        <div class='grafica100' id='grafica-valores-comparacion-campos-iguales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-diferencias-valores-comparacion-campos-iguales'></div>
                        <div class='grafica100' id='grafica-diferencias-comparacion-campos-iguales'></div>";
                for ($i = 0; $i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; $i++)
                {
                    $numero_mapa_calor = $i + 1;
                    $html_informe .= "
                        <div class='mapa-calor100' id='mapa-calor-diferencias-comparacion-campos-iguales-".$numero_mapa_calor."'></div>";
                }
                $html_informe .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Comparación de campos iguales (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-campos-iguales-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-campos-iguales-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-comparacion-campos-iguales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-diferencias-valores-comparacion-campos-iguales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-comparacion-campos-iguales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Páginas 'Comparación de campos iguales (2 - ...)'
                for ($i = 0; $i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; $i++)
                {
                    $numero_pagina_mapa_calor = $i + 2;
                    $numero_mapa_calor = $i + 1;
                    $html_informe .= "
                        <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-campos-iguales-".$numero_pagina_mapa_calor."'>";
                    $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES);
                    $html_informe .= "
                            <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-campos-iguales-".$numero_pagina_mapa_calor."'></div>
                            <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-diferencias-comparacion-campos-iguales-".$numero_mapa_calor."'></div>
                            <div class='fin-pagina-informe-fichero'></div>
                        </div>";
                }
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_comparacion_campos_diferentes($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-comparacion-campos-diferentes'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-comparacion-campos-diferentes' hidden>
                        <div class='grafica100' id='grafica-valores-comparacion-campos-diferentes'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de comparación de campos diferentes
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-campos-diferentes'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-campos-diferentes'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-comparacion-campos-diferentes'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_analisis_comparativo($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-analisis-comparativo'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-analisis-comparativo' hidden>
                        <div class='grafica100' id='grafica-valores-analisis-comparativo'></div>
                        <div class='grafica100' id='grafica-valores-acumulados-analisis-comparativo'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-valores-maximos-minimos-analisis-comparativo'></div>
                        <div class='grafica100' id='grafica-pareto-analisis-comparativo'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-valores-pareto-analisis-comparativo'></div>
                        <div class='mapa-calor100' id='mapa-calor-diferencias-media-analisis-comparativo'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Análisis comparativo (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-analisis-comparativo-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-comparativo-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-analisis-comparativo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-acumulados-analisis-comparativo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-valores-maximos-minimos-analisis-comparativo'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis comparativo (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-analisis-comparativo-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-comparativo-2'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-pareto-analisis-comparativo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-valores-pareto-analisis-comparativo'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis comparativo (3)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-analisis-comparativo-3'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-comparativo-3'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-diferencias-media-analisis-comparativo'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_valores_generales($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-valores-generales'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-valores-generales' hidden>
                        <div class='grafica100' id='grafica-valores-valores-generales'></div>
                        <div class='grafica100' id='grafica-valores-acumulados-valores-generales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-valores-maximos-minimos-valores-generales'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de valores generales
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-valores-generales'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_VALORES_GENERALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-valores-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-valores-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-acumulados-valores-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-valores-maximos-minimos-valores-generales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_incrementos_totales($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-incrementos-totales'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-incrementos-totales' hidden>
                        <div class='grafica100' id='grafica-incrementos-totales-incrementos-totales'></div>
                        <div class='grafica100' id='grafica-porcentajes-incrementos-incrementos-totales'></div>
                        <div class='grafica100' id='grafica-incrementos-incrementos-totales'></div>
                        <div class='grafica100' id='grafica-incrementos-acumulados-incrementos-totales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-incrementos-incrementos-totales'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de incrementos totales
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-incrementos-totales'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_comparacion(TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-incrementos-totales-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-incrementos-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-incrementos-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-incrementos-acumulados-incrementos-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-incrementos-incrementos-totales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_periodos(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-periodos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-valores-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-comparacion-periodos'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-periodos'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-valores-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-comparacion-periodos'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-periodos'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-perfil-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-perfil-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-comparacion-perfil-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-perfil-horario-comparacion-perfil-horario'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-perfil-horario'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-comparacion-perfil-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-perfil-horario-comparacion-perfil-horario'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-perfil-horario'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-campos-iguales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-diferencias-valores-comparacion-campos-iguales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-campos-iguales'></div>";
                for ($i = 0; $i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; $i++)
                {
                    $numero_mapa_calor = $i + 1;
                    $html_elemento .= "
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-campos-iguales-".$numero_mapa_calor."'></div>";
                }
                $html_elemento .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-campos-iguales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-diferencias-valores-comparacion-campos-iguales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-comparacion-campos-iguales'></div>";
                for ($i = 0; $i < NUMERO_MAXIMO_SENSORES_SECUNDARIOS_COMPARACION_CAMPOS_IGUALES; $i++)
                {
                    $numero_mapa_calor = $i + 1;
                    $html_elemento .= "
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-comparacion-campos-iguales-".$numero_mapa_calor."'></div>";
                }
                $html_elemento .= "
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-campos-diferentes'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-comparacion-campos-diferentes'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_valores_generales(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-valores-generales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-valores-generales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-maximos-minimos-valores-generales'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-valores-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-valores-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-maximos-minimos-valores-generales'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_analisis_comparativo(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-agregados-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores agregados seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-comparativo'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-analisis-comparativo'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-maximos-minimos-analisis-comparativo'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-pareto-analisis-comparativo'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-pareto-analisis-comparativo'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-media-analisis-comparativo'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-agregados-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores agregados seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-comparativo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-analisis-comparativo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-maximos-minimos-analisis-comparativo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-pareto-analisis-comparativo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-pareto-analisis-comparativo'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-diferencias-media-analisis-comparativo'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_incrementos_totales(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-totales-incrementos-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-incrementos-incrementos-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-incrementos-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-acumulados-incrementos-totales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-incrementos-incrementos-totales'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-totales-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-incrementos-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-incrementos-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-incrementos-acumulados-incrementos-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-incrementos-incrementos-totales'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_periodos(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_comparacion_valores_sensor_periodos($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_perfil_horario(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["tipo_perfil_horario"] = $parametros_tipo_elemento["tipo_perfil_horario"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Fechas
        $fecha_inicio_perfil_horario_base_datos_local = $parametros_tipo_elemento["fecha_inicio_perfil_horario"];
        $fecha_fin_perfil_horario_base_datos_local = $parametros_tipo_elemento["fecha_fin_perfil_horario"];
        $parametros_informe["fecha_inicio_perfil_horario"] = convierte_formato_fecha(
            $fecha_inicio_perfil_horario_base_datos_local,
            FORMATO_FECHA_BASE_DATOS,
            $_SESSION["formato_fecha_local"]);
        $parametros_informe["fecha_fin_perfil_horario"] = convierte_formato_fecha(
            $fecha_fin_perfil_horario_base_datos_local,
            FORMATO_FECHA_BASE_DATOS,
            $_SESSION["formato_fecha_local"]);

        // Agrupaciones de días de la semana
        $cadena_agrupaciones_dias_semana = $parametros_tipo_elemento["agrupaciones_dias_semana"];
        $agrupaciones_dias_semana = dame_agrupaciones_dias_semana($cadena_agrupaciones_dias_semana);
        $parametros_informe["agrupaciones_dias_semana"] = json_encode($agrupaciones_dias_semana);

        $datos_elemento = dame_comparacion_valores_sensor_perfil_horario($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_campos_iguales(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Se juntan los ids de los sensores
        $id_sensor_principal = $parametros_tipo_elemento["id_sensor_principal"];
        $ids_sensores_secundarios = $parametros_tipo_elemento["ids_sensores_secundarios"];
        $ids_sensores = array($id_sensor_principal);
        $ids_sensores = array_merge($ids_sensores, $ids_sensores_secundarios);

        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_seleccionados = false;
        if (count($ids_sensores) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($ids_sensores as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["ids_sensores"] = $ids_sensores;
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_comparacion_valores_campos_iguales_sensores($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_comparacion_campos_diferentes(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_sensores"]) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_sensores"] as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clases_sensores"] = $parametros_tipo_elemento["clases_sensores"];
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["campos"] = $parametros_tipo_elemento["campos"];
        $parametros_informe["parametros_extra_campos"] = $parametros_tipo_elemento["parametros_extra_campos"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["unificar_escalas"] = $parametros_tipo_elemento["unificar_escalas"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_comparacion_valores_campos_diferentes_sensores($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_comparativo(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Sensores
        $ids_sensores_agregados = $parametros_tipo_elemento["ids_sensores_agregados"];
        $id_sensor_destacado = $parametros_tipo_elemento["id_sensor_destacado"];

        // Si no hay sensores agregados seleccionados, se devuelve sin sensores
        $hay_sensores_agregados_seleccionados = false;
        if (count($ids_sensores_agregados) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($ids_sensores_agregados as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_agregados_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_agregados_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_agregados_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["ids_sensores_agregados"] = $ids_sensores_agregados;
        $nombres_sensores_agregados = dame_nombres_sensores($ids_sensores_agregados);
        $parametros_informe["nombres_sensores_agregados"] = $nombres_sensores_agregados;
        $parametros_informe["id_sensor_destacado"] = $id_sensor_destacado;
        $nombre_sensor_destacado = dame_nombre_sensor($id_sensor_destacado);
        $parametros_informe["nombre_sensor_destacado"] = $nombre_sensor_destacado;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_analisis_comparativo_sensores($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_valores_generales(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_sensores"]) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_sensores"] as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clases_sensor"] = $parametros_tipo_elemento["clases_sensor"];
        $parametros_informe["campos"] = $parametros_tipo_elemento["campos"];
        $parametros_informe["parametros_extra_campos"] = $parametros_tipo_elemento["parametros_extra_campos"];
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["agregacion"] = $parametros_tipo_elemento["agregacion"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_valores_generales_sensores($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_incrementos_totales(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_sensores"]) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_sensores"] as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clases_sensor"] = $parametros_tipo_elemento["clases_sensor"];
        $parametros_informe["campos"] = $parametros_tipo_elemento["campos"];
        $parametros_informe["parametros_extra_campos"] = $parametros_tipo_elemento["parametros_extra_campos"];
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["agregacion"] = $parametros_tipo_elemento["agregacion"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_incrementos_totales_sensores($parametros_informe);
        return ($datos_elemento);
    }


    //
    // Funciones auxiliares de informes
    //


    function dame_descripcion_tipo_perfil_horario($tipo_perfil_horario)
    {
        switch ($tipo_perfil_horario)
        {
            case TIPO_PERFIL_HORARIO_SEMANAL:
            {
                $descripcion_perfil_horario = "Semanal";
                break;
            }
            case TIPO_PERFIL_HORARIO_DIARIO:
            {
                $descripcion_perfil_horario = "Diario";
                break;
            }
            case TIPO_PERFIL_HORARIO_CONFIGURABLE:
            {
                $descripcion_perfil_horario = "Configurable";
                break;
            }
            default:
            {
                $descripcion_perfil_horario = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_perfil_horario));
    }


    //
    // Funciones de valores generales de incrementos totales
    //


    // Comprobaciones de clases de sensor y campos compatibles
    function comprueba_clases_sensor_campos_compatibles(
        $id_ratio,
        $clases_sensor,
        $ids_sensores_clases,
        $campos,
        $intervalo_valores,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        // Comprobación de clases diferentes
        if (count(array_unique($clases_sensor)) < count($clases_sensor))
        {
            $mensaje_error = ($idiomas->_("Las clases de sensor deben ser diferentes"));
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
        }
        else
        {
            $clase_procesado_valores_anterior = NULL;
            $granularidad_cuartohoraria_anterior = NULL;
            $aplicar_ratio_anterior = NULL;
            $unidad_medida_anterior = NULL;
            $tipo_valores_campo_anterior = NULL;
            $clases_sensor_campos_correctos = true;
            for ($i = 0; $i < count($clases_sensor); $i++)
            {
                // Clase de sensor y campo
                $clase_sensor = $clases_sensor[$i];
                $campo = $campos[$i];

                // Si la clase es ninguna no se hace nada
                if ($clase_sensor == CLASE_NINGUNA)
                {
                    continue;
                }

                // Características de clase de sensor
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                if (($clase_procesado_valores_anterior !== NULL) && ($clase_procesado_valores != $clase_procesado_valores_anterior))
                {
                    $clases_sensor_campos_correctos = false;
                    break;
                }
                $clase_procesado_valores_anterior = $clase_procesado_valores;
                if ($intervalo_valores == INTERVALO_VALORES_CUARTOHORA)
                {
                    $granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
                    if (($granularidad_cuartohoraria_anterior !== NULL) && ($granularidad_cuartohoraria != $granularidad_cuartohoraria_anterior))
                    {
                        $clases_sensor_campos_correctos = false;
                        break;
                    }
                    $granularidad_cuartohoraria_anterior = $granularidad_cuartohoraria;
                }

                // Se recupera si aplica el ratio
                $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
                if ($aplicar_ratio == true)
                {
                    $info_ratio = dame_info_ratio($id_ratio);
                }
                if (($aplicar_ratio_anterior !== NULL) && ($aplicar_ratio != $aplicar_ratio_anterior))
                {
                    $clases_sensor_campos_correctos = false;
                    break;
                }
                $aplicar_ratio_anterior = $aplicar_ratio;

                // Número de decimales de valores y unidades de medida
                $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
                $ids_sensores_clase = $ids_sensores_clases[$clase_sensor];
                for ($j = 0; $j < count($ids_sensores_clase); $j++)
                {
                    $id_sensor = $ids_sensores_clase[$j];
                    $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                    if ($aplicar_ratio == true)
                    {
                        modifica_unidad_medida_ratio($info_ratio, $unidad_medida);
                    }
                    if (($unidad_medida_anterior !== NULL) && ($unidad_medida != $unidad_medida_anterior))
                    {
                        $clases_sensor_campos_correctos = false;
                        break;
                    }
                }
                if ($clases_sensor_campos_correctos == false)
                {
                    break;
                }
                $unidad_medida_anterior = $unidad_medida;

                // Tipo de valores de campo
                $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor_informe($clase_sensor, $campo, $tipo_informe);
                if (($$tipo_valores_campo_anterior !== NULL) && ($tipo_valores_campo != $$tipo_valores_campo_anterior))
                {
                    $clases_sensor_campos_correctos = false;
                    break;
                }
                $tipo_valores_campo_anterior = $tipo_valores_campo;
            }

            // Se devuelve si son compatibles y la información correspondiente
            if ($clases_sensor_campos_correctos == true)
            {
                $resultado = array(
                    "res" => "OK",
                    "aplicar_ratio" => $aplicar_ratio,
                    "numero_decimales_valores" => $numero_decimales_valores,
                    "unidad_medida" => $unidad_medida,
                    "tipo_valores_campo" => $tipo_valores_campo);
            }
            else
            {
                $mensaje_error = ($idiomas->_("Las clases de sensor y los campos no son compatibles"));
                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $mensaje_error);
            }
        }
        return ($resultado);
    }


    // Devuelve la información de sensores (por clases) con la agregación especificada
    function dame_info_sensores_clases_agregacion(
        $id_ratio,
        $agregacion,
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
        $filas_valores_sensores)
    {
        switch ($agregacion)
        {
            case AGREGACION_SUMA:
            case AGREGACION_MEDIA:
            {
                // Se recupera la información de agregaciones de sensores
                $res = dame_info_agregaciones_sensores_clases(
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
                    false);
                $info_agregaciones_sensores = $res["info_agregaciones_sensores"];
                $numero_sensores_valores_agregaciones = $res["numeros_sensores_valores"];
                $cadena_numero_sensores_valores_agregaciones = $numero_sensores_valores_agregaciones;

                // Si no hay datos no se hace nada
                if (count($info_agregaciones_sensores) == 0)
                {
                    $info_sensores_clase_agregacion = array("hay_datos" => false);
                    return ($info_sensores_clase_agregacion);
                }

                // Se simula un sensor con la agregación correspondiente
                $filas_valores_agregados = array();
                $horas_agregacion = (dame_segundos_intervalo_valores($intervalo_valores) / 3600);
                for ($i = 0; $i < count($info_agregaciones_sensores); $i++)
                {
                    $info_agregacion_sensores = $info_agregaciones_sensores[$i];
                    $cadena_fecha_hora_agregacion_base_datos_utc = $info_agregacion_sensores["cadena_fecha_hora_agregacion_base_datos_utc"];
                    switch ($agregacion)
                    {
                        case AGREGACION_SUMA:
                        {
                            $valor_agregacion = array_sum($info_agregacion_sensores["valores"]);
                            break;
                        }
                        case AGREGACION_MEDIA:
                        {
                            $valor_agregacion = array_sum($info_agregacion_sensores["valores"]) / count($info_agregacion_sensores["valores"]);
                            break;
                        }
                    }
                    $numero_sensores_sin_valor = count($ids_sensores) - count($info_agregacion_sensores["ids_sensores"]);

                    // Fila de valores (con los valores agregados del campo correspondiente)
                    $fila_valor_agregado = array(
                        "fecha_hora" => $cadena_fecha_hora_agregacion_base_datos_utc,
                        "valor_agregacion" => $valor_agregacion,
                        "horas" => $horas_agregacion,
                        "numero_sensores_sin_valor" => $numero_sensores_sin_valor);
                    array_push($filas_valores_agregados, $fila_valor_agregado);
                }

                // Nombre de agregación
                $res_cadenas_inicio_fin = dame_cadenas_inicio_fin_nombre_agregacion();
                $cadena_inicio_nombre_agregacion = $res_cadenas_inicio_fin["cadena_inicio"];
                $cadena_fin_nombre_agregacion = $res_cadenas_inicio_fin["cadena_fin"];
                $nombre_agregacion =
                    $cadena_inicio_nombre_agregacion.
                    dame_descripcion_agregacion($agregacion).
                    $cadena_fin_nombre_agregacion;

                // Simulación de un solo sensor (con los valores agregados)
                $ids_sensores = array(-1);
                $nombres_sensores = array($nombre_agregacion);
                $filas_valores_sensores = array($nombre_agregacion => $filas_valores_agregados);
                break;
            }
            case AGREGACION_SUMA_CLASES:
            case AGREGACION_MEDIA_CLASES:
            {
                // Agregación
                if ($agregacion == AGREGACION_SUMA_CLASES)
                {
                    $agregacion = AGREGACION_SUMA;
                }
                if ($agregacion == AGREGACION_MEDIA_CLASES)
                {
                    $agregacion = AGREGACION_MEDIA;
                }

                // Se recupera la información de agregaciones de sensores
                $res = dame_info_agregaciones_sensores_clases(
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
                    true);
                $info_agregaciones_sensores = $res["info_agregaciones_sensores"];
                $numero_sensores_valores_agregaciones = $res["numeros_sensores_valores"];
                $cadena_numero_sensores_valores_agregaciones = "";

                // Si no hay datos no se hace nada
                if (count($info_agregaciones_sensores) == 0)
                {
                    $info_sensores_clase_agregacion = array("hay_datos" => false);
                    return ($info_sensores_clase_agregacion);
                }

                // Se simulan sensores con la agregación correspondiente (uno por clase)
                $ids_sensores = array();
                $nombres_sensores = array();
                $filas_valores_sensores = array();
                $filas_valores_agregados_clases = array();
                $horas_agregacion = (dame_segundos_intervalo_valores($intervalo_valores) / 3600);
                for ($i = 0; $i < count($info_agregaciones_sensores); $i++)
                {
                    $info_agregacion_sensores = $info_agregaciones_sensores[$i];
                    $cadena_fecha_hora_agregacion_base_datos_utc = $info_agregacion_sensores["cadena_fecha_hora_agregacion_base_datos_utc"];
                    $info_agregacion_sensores_clases = $info_agregacion_sensores["clases"];
                    foreach ($clases_sensor as $clase_sensor)
                    {
                        if (array_key_exists($clase_sensor, $info_agregacion_sensores_clases) == false)
                        {
                            continue;
                        }
                        $info_agregacion_sensores = $info_agregacion_sensores_clases[$clase_sensor];
                        switch ($agregacion)
                        {
                            case AGREGACION_SUMA:
                            {
                                $valor_agregacion = array_sum($info_agregacion_sensores["valores"]);
                                break;
                            }
                            case AGREGACION_MEDIA:
                            {
                                $valor_agregacion = array_sum($info_agregacion_sensores["valores"]) / count($info_agregacion_sensores["valores"]);
                                break;
                            }
                        }
                        $numero_sensores_sin_valor = count($ids_sensores_clases[$clase_sensor]) - count($info_agregacion_sensores["ids_sensores"]);

                        // Fila de valores (con los valores agregados del campo correspondiente)
                        $fila_valor = array(
                            "fecha_hora" => $cadena_fecha_hora_agregacion_base_datos_utc,
                            "valor_agregacion" => $valor_agregacion,
                            "horas" => $horas_agregacion,
                            "numero_sensores_sin_valor" => $numero_sensores_sin_valor);
                        if (array_key_exists($clase_sensor, $filas_valores_agregados_clases) == false)
                        {
                            $filas_valores_agregados_clases[$clase_sensor] = array();
                        }
                        array_push($filas_valores_agregados_clases[$clase_sensor], $fila_valor);
                    }
                }

                // Nombres de agregaciones
                $id_sensor_agregacion = -1;
                foreach ($clases_sensor as $clase_sensor)
                {
                    if (array_key_exists($clase_sensor, $filas_valores_agregados_clases) == false)
                    {
                        continue;
                    }
                    $res_cadenas_inicio_fin = dame_cadenas_inicio_fin_nombre_agregacion();
                    $cadena_inicio_nombre_agregacion = $res_cadenas_inicio_fin["cadena_inicio"];
                    $cadena_fin_nombre_agregacion = $res_cadenas_inicio_fin["cadena_fin"];
                    $nombre_agregacion =
                        $cadena_inicio_nombre_agregacion.
                        dame_descripcion_agregacion($agregacion)." (".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor)).")".
                        $cadena_fin_nombre_agregacion;

                    // Se añade la información de la agregación por clase
                    array_push($ids_sensores, $id_sensor_agregacion);
                    array_push($nombres_sensores, $nombre_agregacion);
                    $filas_valores_sensores[$nombre_agregacion] = $filas_valores_agregados_clases[$clase_sensor];
                    $id_sensor_agregacion -= 1;

                    // Número de sensores agregados por clase
                    if ($cadena_numero_sensores_valores_agregaciones != "")
                    {
                        $cadena_numero_sensores_valores_agregaciones .= ", ";
                    }
                    $cadena_numero_sensores_valores_agregaciones .= $numero_sensores_valores_agregaciones[$clase_sensor];
                }
                break;
            }
            default:
            {
                throw new Exception("Agregación incorrecta: '".$agregacion."'");
            }
        }
        $info_sensores_clase_agregacion = array(
            "hay_datos" => true,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "filas_valores_sensores" => $filas_valores_sensores,
            "cadena_numero_sensores_valores_agregaciones" => $cadena_numero_sensores_valores_agregaciones);
        return ($info_sensores_clase_agregacion);
    }
?>
