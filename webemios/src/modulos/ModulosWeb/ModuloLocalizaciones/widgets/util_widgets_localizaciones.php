<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/widgets/util_widgets_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_RATIO", 0);
	define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_LOCALIZACION", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_PERIODO_TIEMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_INICIAR_COMIENZO_PERIODO_TIEMPO", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_FECHA_INICIO_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ICONO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_INCLUSION_FECHAS", 8);


    // Devuelve los datos de un widget de tipo 'Valor de un ratio'
    function dame_datos_widget_valor_ratio(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $id_localizacion = $parametros_tipo["id_localizacion"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $icono = $parametros_tipo["icono"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se comprueba si la localización es visible por el usuario actual
        if ($id_localizacion != ID_NINGUNO)
        {
            $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
            if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == false)
            {
                throw new Exception("Localización no visible por el usuario actual (id: '".$id_localizacion."')");
            }
        }

        // Se recuperan el valor y la unidad
        $valor_unidad_ratio = dame_valor_unidad_ratio_widget(
            $id_ratio,
            $id_localizacion,
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);
        if ($valor_unidad_ratio === NULL)
        {
            $datos_widget_valor_ratio = array("sin_valores" => true);
        }
        else
        {
            $cadena_valor_ratio = $valor_unidad_ratio["cadena_valor"];
            $unidad_ratio = $valor_unidad_ratio["unidad"];
            $html_cadena_hora_valores = $valor_unidad_ratio["html_cadena_hora_valores"];

            // Nota: Altura del valor y la unidad dependiente de la configuración de la cuadrícula
            $clase_tamanyo_fuente_texto_grande_widget = "tamanyo-fuente-texto-grande-widget-valor-digital-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
            $clase_css_texto_grande = "texto-grande-widget-valor-digital-sensor ".$clase_tamanyo_fuente_texto_grande_widget;
            $clase_css_texto_pequenyo = "texto-pequenyo-widget-valor-digital-sensor";

            // HTML de cadena de valor del ratio
            $html_cadena_valores = "<div class='".$clase_css_texto_grande."'>".$cadena_valor_ratio;
            if ($unidad_ratio != "")
            {
                $html_cadena_valores .= " "."<span class='".$clase_css_texto_pequenyo."'>".$unidad_ratio."</span></div>";
            }

            // Datos del widget
            $datos_widget_valor_ratio = array(
                "sin_valores" => false,
                "html_cadena_valores" => $html_cadena_valores,
                "html_cadena_hora_valores" => $html_cadena_hora_valores);
        }

        // Icono
        $datos_widget_valor_ratio["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_valor_ratio["res"] = "OK";
        return ($datos_widget_valor_ratio);
    }


    //
    // Funciones auxiliares para obtener los datos de los widgets
    //


    // Devuelve el valor y la unidad del ratio especificado (de la localización especificada)
    function dame_valor_unidad_ratio_widget(
        $id_ratio,
        $id_localizacion,
        $periodo_tiempo,
        $iniciar_comienzo_periodo_tiempo,
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la fila del ratio
        $fila_ratio = dame_fila_ratio($id_ratio);

        // Se recuperan el valor y sensor del ratio de la localización
        if ($id_localizacion != ID_NINGUNO)
        {
            $consulta_ratio_localizacion = "
                SELECT
                    valor,
                    sensor
                FROM ratios_localizaciones
                WHERE
                    (localizacion = '".$bd_red->_($id_localizacion)."')
                    AND (ratio = '".$bd_red->_($id_ratio)."')";
            $res_ratio_localizacion = $bd_red->ejecuta_consulta($consulta_ratio_localizacion);
            if ($res_ratio_localizacion == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_ratio_localizacion."'");
            }
            if ($res_ratio_localizacion->dame_numero_filas() > 0)
            {
                $fila_ratio_localizacion = $res_ratio_localizacion->dame_siguiente_fila();
                $valor_ratio = $fila_ratio_localizacion["valor"];
                $id_sensor_ratio = $fila_ratio_localizacion["sensor"];
            }
            else
            {
                $valor_ratio = $fila_ratio["valor_defecto"];
                $id_sensor_ratio = $fila_ratio["sensor_defecto"];
            }
        }
        else
        {
            $valor_ratio = $fila_ratio["valor_defecto"];
            $id_sensor_ratio = $fila_ratio["sensor_defecto"];
        }

        // Se recupera el último valor del ratio (o el acumulado si es variable y campo incremento) según el tipo de ratio
        switch ($fila_ratio["tipo"])
        {
            case TIPO_RATIO_FIJO:
            {
                if ($valor_ratio === NULL)
                {
                    return (NULL);
                }
                $html_cadena_hora_valores = NULL;
                break;
            }
            case TIPO_RATIO_VARIABLE:
            {
                if ($id_sensor_ratio == ID_NINGUNO)
                {
                    return (NULL);
                }
                switch ($fila_ratio["campo_sensor"])
                {
                    case CAMPO_VALOR:
                    {
                        $fila_sensor_ratio = dame_fila_sensor($id_sensor_ratio);
                        $cadena_fecha_hora_ultimos_valores_clase_horas_base_datos_utc = $fila_sensor_ratio["hora_ultimos_valores_clase_horas"];
                        $ultimos_valores_clase_horas = $fila_sensor_ratio["ultimos_valores_clase_horas"];
                        if ($cadena_fecha_hora_ultimos_valores_clase_horas_base_datos_utc === NULL)
                        {
                            return (NULL);
                        }
                        $valor_ratio = dame_valor_numerico_sensor_widget(
                            ID_NINGUNO,
                            $id_sensor_ratio,
                            $cadena_fecha_hora_ultimos_valores_clase_horas_base_datos_utc,
                            $ultimos_valores_clase_horas,
                            $fila_ratio["clase_sensor"],
                            NULL,
                            GRANULARIDAD_HORARIA,
                            $fila_ratio["campo_sensor"]);

                        // Hora de últimos valores
                        $zona_horaria_local = dame_zona_horaria_local();
                        $cadena_fecha_hora_ultimos_valores_clase_horas_local_utc = convierte_formato_fecha($cadena_fecha_hora_ultimos_valores_clase_horas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                        $cadena_fecha_hora_ultimos_valores_clase_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimos_valores_clase_horas_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria_local);

                        // HTML de cadena de hora de valores
                        $html_cadena_hora_valores = "(".$cadena_fecha_hora_ultimos_valores_clase_horas_local_local.")";
                        break;
                    }
                    case CAMPO_INCREMENTO:
                    {
                        // Fechas de inicio y fin
                        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
                            $periodo_tiempo,
                            $iniciar_comienzo_periodo_tiempo,
                            $cadena_fecha_inicio_periodo_tiempo_base_datos_local);
                        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
                        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

                        // Conversión a UTC
                        $zona_horaria = dame_zona_horaria_local();
                        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
                        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
                        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
                        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

                        // HTML de cadena de hora de valores
                        $html_cadena_hora_valores = "";
                        $html_cadena_hora_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_inicio_local_local."),</span>";
                        $html_cadena_hora_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_fin_local_local.")</span>";

                        // Se recupera la información del valor medio / acumulado del campo del sensor
                        $res_valor_medio_acumulado = dame_valor_medio_acumulado_campo_sensor_widget(
                            ID_NINGUNO,
                            $fila_ratio["clase_sensor"],
                            $id_sensor_ratio,
                            $fila_ratio["campo_sensor"],
                            $cadena_fecha_hora_inicio_base_datos_utc,
                            $cadena_fecha_hora_fin_base_datos_utc,
                            $horario_semanal,
                            $exclusion_fechas,
                            $inclusion_fechas,
                            NULL);
                        if ($res_valor_medio_acumulado["sin_valores"] == true)
                        {
                            return (NULL);
                        }
                        $valor_ratio = $res_valor_medio_acumulado["valor_medio_acumulado"];
                        break;
                    }
                }
                break;
            }
        }

        // Se devuelve el valor actual, la unidad y la hora de valores
        $cadena_valor_ratio = formatea_numero($valor_ratio, 2);
        $unidad_medida_ratio = $fila_ratio["unidad_medida"];
        $valor_unidad = array(
            "cadena_valor" => $cadena_valor_ratio,
            "unidad" => $unidad_medida_ratio,
            "html_cadena_hora_valores" => $html_cadena_hora_valores);
        return ($valor_unidad);
    }
?>
