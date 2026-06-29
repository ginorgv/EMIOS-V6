<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/CuadriculaWidgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/widgets/util_widgets_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/widgets/util_widgets_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/widgets/util_widgets_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/widgets/util_widgets_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/widgets/util_widgets_smartmeter.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
    define("INDICE_PARAMETRO_TIPO_WIDGET_IMAGEN_ALTURA_MAXIMA", 0);


    // Devuelve los datos de un widget
	function dame_datos_widget(
        $id_widget,
        $fila_widget,
        $fila_pestanya_widgets,
        $numero_columnas_fila_widget,
        $numero_columnas_widget,
        $minutos_desfase_utc)
	{
        try
        {
            // Parámetros de apariencia de widgets de la pestaña de widgets
            $parametros_apariencia_widgets = dame_nombres_valores_parametros_apariencia_widgets_pestanya_widgets(
                $fila_pestanya_widgets["parametros_apariencia_widgets"]);

            // Número de columnas de fila de widget para la clase del contenido del widget
            $numero_columnas_fila_widget_clase_contenido_widget = (int) ceil($numero_columnas_fila_widget / $numero_columnas_widget);
            if ($numero_columnas_fila_widget_clase_contenido_widget > NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS)
            {
                $numero_columnas_fila_widget_clase_contenido_widget = NUMERO_MAXIMO_COLUMNAS_FILAS_WIDGETS;
            }

            // Tipo y parámetros de tipo
            $tipo_widget = $fila_widget["tipo"];
            $cadena_parametros_tipo = $fila_widget["parametros_tipo"];

            // Se recuperan los parámetros del tipo de widget
            $parametros_tipo_widget = dame_nombres_valores_parametros_tipo_widget($tipo_widget, $cadena_parametros_tipo);

            // Tipo de widget
            switch ($tipo_widget)
            {
                // "Generales" (sin módulo asociado)
                case TIPO_WIDGET_IMAGEN:
                {
                    break;
                }
                // Módulo Localizaciones
                case TIPO_WIDGET_VALOR_RATIO:
                {
                    $datos_widget = dame_datos_widget_valor_ratio(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                // Módulo Sensores
                case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                {
                    $datos_widget = dame_datos_widget_valor_digital_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                {
                    $datos_widget = dame_datos_widget_valor_digital_medio_acumulado_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                {
                    $datos_widget = dame_datos_widget_valor_analogico_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                {
                    $datos_widget = dame_datos_widget_valor_analogico_medio_acumulado_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                {
                    $datos_widget = dame_datos_widget_grafica_valores_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                {
                    $datos_widget = dame_datos_widget_mapa_calor_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                {
                    $datos_widget = dame_datos_widget_grafica_comparacion_periodos_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                {
                    $datos_widget = dame_datos_widget_evolucion_valores_comparacion_periodos_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                {
                    $datos_widget = dame_datos_widget_grafica_comparacion_campos_iguales_sensores(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                {
                    $datos_widget = dame_datos_widget_grafica_comparacion_campos_diferentes_sensores(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                {
                    $datos_widget = dame_datos_widget_grafica_valores_generales_sensores(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                {
                    $datos_widget = dame_datos_widget_valor_agregado_valores_generales_sensores(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                {
                    $datos_widget = dame_datos_widget_grafica_incrementos_totales_sensores(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                // Módulo Actuadores
                case TIPO_WIDGET_INFORMACION_ACTUADOR:
                case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                {
                    $datos_widget = dame_datos_widget_informacion_actuador_grupo_actuadores(
                        $tipo_widget,
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                // Módulo Smartmeter
                case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                {
                    $datos_widget = dame_datos_widget_grafica_consumos_costes_tramos_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
                {
                    $datos_widget = dame_datos_widget_coste_factura_sensor(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                // Módulo Proyectos
                case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
                {
                    $datos_widget = dame_datos_widget_simulador_linea_base(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                case TIPO_WIDGET_INFORMACION_PROYECTO:
                {
                    $datos_widget = dame_datos_widget_informacion_proyecto(
                        $id_widget,
                        $parametros_tipo_widget,
                        $numero_columnas_fila_widget_clase_contenido_widget,
                        $minutos_desfase_utc);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de widget desconocido: '".$tipo_widget."'");
                }
            }
        }
        catch (Exception $excepcion)
        {
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $excepcion);

            // Mensaje de error
            $codigo_excepcion = $excepcion->getCode();
            switch ($codigo_excepcion)
            {
                case CODIGO_EXCEPCION_NUMERO_MAXIMO_FILAS_CONSULTA_SUPERADO_MYSQL:
                {
                    $mensaje_error_excepcion = "Número máximo de valores superado (modifique los parámetros)";
                    $cadena_numero_maximo_valores = "número máximo de valores";
                    if ($_SESSION["idioma"] !== NULL)
                    {
                        $idiomas = new Idiomas();
                        $mensaje_error_excepcion = $idiomas->_($mensaje_error_excepcion);
                        $cadena_numero_maximo_valores = $idiomas->_($cadena_numero_maximo_valores);
                    }
                    $numero_maximo_valores = formatea_numero(NUMERO_MAXIMO_FILAS_CONSULTA_MYSQL, 0);
                    $mensaje_error_excepcion .= "<br/>(".$cadena_numero_maximo_valores.": ".$numero_maximo_valores.")";
                    break;
                }
                default:
                {
                    $mensaje_error_excepcion = "";
                    break;
                }
            }

            // Se devuelve error
            $datos_widget = array(
                "res" => "ERROR",
                "msg" => $mensaje_error_excepcion);
        }

        // Parámetros de apariencia de widgets
        $datos_widget["parametros_apariencia_widgets"] = $parametros_apariencia_widgets;

        // Se devuelven los datos del widget
        return ($datos_widget);
	}


    //
    // Funciones para eliminar widgets automáticamente
    //


    // Devuelve los identificadores de las localizaciones del widget
    function dame_ids_localizaciones_widget($tipo, $cadena_parametros_tipo)
    {
        $ids_localizaciones = array();

        // Se recuperan los índices de los identificadores de las localizaciones de los parámetros de tipo
        $indices_parametros_tipo_ids_localizaciones = array();
        switch ($tipo)
        {
            // Widgets del módulo Localizaciones
            case TIPO_WIDGET_VALOR_RATIO:
            {
                array_push($indices_parametros_tipo_ids_localizaciones, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_LOCALIZACION);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de localizaciones de los parámetros de tipo
        if (count($ids_localizaciones) == 0)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            foreach ($indices_parametros_tipo_ids_localizaciones as $indice_parametros_tipo_ids_localizaciones)
            {
                $ids_localizaciones_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_localizaciones]);
                $ids_localizaciones = array_merge($ids_localizaciones, $ids_localizaciones_parametro_tipo);
            }
        }

        // Se devuelven los identificadores de localizaciones
        return ($ids_localizaciones);
    }


    // Devuelve los identificadores de los ratios del widget
    function dame_ids_ratios_widget($tipo, $cadena_parametros_tipo)
    {
        $ids_ratios = array();

        // Se recuperan los índices de los identificadores de los ratios de los parámetros de tipo
        $indices_parametros_tipo_ids_ratios = array();
        switch ($tipo)
        {
            // Widgets del módulo Localizaciones
            case TIPO_WIDGET_VALOR_RATIO:
            {
                array_push($indices_parametros_tipo_ids_ratios, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_RATIO);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de ratios de los parámetros de tipo
        if (count($ids_ratios) == 0)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            foreach ($indices_parametros_tipo_ids_ratios as $indice_parametros_tipo_ids_ratios)
            {
                $ids_ratios_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_ratios]);
                $ids_ratios = array_merge($ids_ratios, $ids_ratios_parametro_tipo);
            }
        }

        // Se devuelven los identificadores de ratios
        return ($ids_ratios);
    }


    // Devuelve el índice del identificador de ratio del widget
    function dame_indice_id_ratio_widget($tipo)
    {
        // Se recupera el índice del identificador del ratio
        $indice_id_ratio = ID_NINGUNO;
        switch ($tipo)
        {
            // Widgets del módulo Sensores
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_ID_RATIO;
                break;
            }
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
            {
                $indice_id_ratio = INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_RATIO;
                break;
            }
            default:
            {
                break;
            }
        }

        // Se devuelve el índice del identificador del ratio
        return ($indice_id_ratio);
    }


    // Devuelve los identificadores de los sensores del widget
    function dame_ids_sensores_widget($tipo, $cadena_parametros_tipo)
    {
        $ids_sensores = array();

        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        // (o los sensores según el tipo de widget)
        $indices_parametros_tipo_ids_sensores = array();
        switch ($tipo)
        {
            // Widgets del módulo Sensores
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_IDS_SENSORES);
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_IDS_SENSORES);
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_IDS_SENSORES);
                break;
            }
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_IDS_SENSORES);
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_IDS_SENSORES);
                break;
            }
            // Widgets del módulo SmartMeter
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_SENSOR);
                break;
            }
            case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_ID_SENSOR);
                break;
            }
            // Widgets del módulo Proyectos
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $id_linea_base = $parametros_tipo[INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                $id_sensor_linea_base = dame_id_sensor_linea_base($id_linea_base);
                $ids_sensores = array($id_sensor_linea_base);
                break;
            }
            case TIPO_WIDGET_INFORMACION_PROYECTO:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ID_PROYECTO];
                $id_sensor_proyecto = dame_id_sensor_proyecto($id_proyecto);
                $ids_sensores = array($id_sensor_proyecto);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de sensores de los parámetros de tipo (si no se han recuperado ya)
        if (count($ids_sensores) == 0)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            foreach ($indices_parametros_tipo_ids_sensores as $indice_parametros_tipo_ids_sensores)
            {
                $ids_sensores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_sensores]);
                $ids_sensores = array_merge($ids_sensores, $ids_sensores_parametro_tipo);
            }
        }

        // Se devuelven los identificadores de sensores
        return ($ids_sensores);
    }


    // Devuelve los identificadores de los actuadores del widget
    function dame_ids_actuadores_widget($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los actuadores de los parámetros de tipo
        $indices_parametros_tipo_ids_actuadores = array();
        switch ($tipo)
        {
            // Widgets del módulo Actuadores
            case TIPO_WIDGET_INFORMACION_ACTUADOR:
            {
                array_push($indices_parametros_tipo_ids_actuadores, INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_ID_ACTUADOR);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de actuadores de los parámetros de tipo
        $ids_actuadores = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_actuadores as $indice_parametros_tipo_ids_actuadores)
        {
            $ids_actuadores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_actuadores]);
            $ids_actuadores = array_merge($ids_actuadores, $ids_actuadores_parametro_tipo);
        }
        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores del widget
    function dame_ids_grupos_actuadores_widget($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los grupos actuadores de los parámetros de tipo
        $indices_parametros_tipo_ids_grupos_actuadores = array();
        switch ($tipo)
        {
            // Widgets del módulo Actuadores
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
            {
                array_push($indices_parametros_tipo_ids_grupos_actuadores, INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_ID_GRUPO_ACTUADORES);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de grupos de actuadores de los parámetros de tipo
        $ids_grupos_actuadores = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_grupos_actuadores as $indice_parametros_tipo_ids_grupos_actuadores)
        {
            $ids_grupos_actuadores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_grupos_actuadores]);
            $ids_grupos_actuadores = array_merge($ids_grupos_actuadores, $ids_grupos_actuadores_parametro_tipo);
        }
        return ($ids_grupos_actuadores);
    }


    // Devuelve los identificadores de las líneas base del widget
    function dame_ids_lineas_base_widget($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de las líneas base de los parámetros de tipo
        $indices_parametros_tipo_ids_lineas_base = array();
        switch ($tipo)
        {
            // Widgets del módulo Proyectos
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
            {
                array_push($indices_parametros_tipo_ids_lineas_base, INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_ID_LINEA_BASE);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de líneas base de los parámetros de tipo
        $ids_lineas_base = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_lineas_base as $indice_parametros_tipo_ids_lineas_base)
        {
            $ids_lineas_base_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_lineas_base]);
            $ids_lineas_base = array_merge($ids_lineas_base, $ids_lineas_base_parametro_tipo);
        }
        return ($ids_lineas_base);
    }


    // Devuelve los identificadores de los proyectos del widget
    function dame_ids_proyectos_widget($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los proyectos de los parámetros de tipo
        $indices_parametros_tipo_ids_proyectos = array();
        switch ($tipo)
        {
            // Widgets del módulo Proyectos
            case TIPO_WIDGET_INFORMACION_PROYECTO:
            {
                array_push($indices_parametros_tipo_ids_proyectos, INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ID_PROYECTO);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de proyectos de los parámetros de tipo
        $ids_proyectos = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_proyectos as $indice_parametros_tipo_ids_proyectos)
        {
            $ids_proyectos_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_proyectos]);
            $ids_proyectos = array_merge($ids_proyectos, $ids_proyectos_parametro_tipo);
        }
        return ($ids_proyectos);
    }


    // Elimina los widgets no visibles de un usuario (con perfil estándar)
    function elimina_widgets_no_visibles_usuario(
        $id_usuario,
        $perfil,
        $id_red,
        $parametros_usuario)
    {
        // Se eliminan los widgets que el usuario ya no puede visualizar:
        // 1. No tiene el módulo correspondiente.
        // 2. Si es el módulo Sensores: Eliminar el widget si el usuario ya no tiene permisos para visualizar el sensor correspondiente
        // (Nota: Se comprueban los permisos del sensor o actuador tanto en el módulo Sensores y Actuadores como en el módulo Localizaciones)

        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red);
        $secciones_usuario = dame_secciones_usuario($id_usuario, $id_red);

        // Parámetros del usuario
        $parametros_modulo_localizaciones = $parametros_usuario["parametros_modulo_localizaciones"];
        $parametros_modulo_sensores = $parametros_usuario["parametros_modulo_sensores"];
        $parametros_modulo_actuadores = $parametros_usuario["parametros_modulo_actuadores"];

        // Identificadores de elementos visibles por el usuario
        if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
        {
            $permiso_todas_localizaciones = $parametros_modulo_localizaciones["permiso_todas_localizaciones"];
            if ($permiso_todas_localizaciones == true)
            {
                $ids_localizaciones = dame_ids_localizaciones();
            }
            else
            {
                $ids_localizaciones = $parametros_modulo_localizaciones["ids_localizaciones"];
            }
            $ids_sensores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_actuadores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
            $ids_grupos_actuadores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
        }
        else
        {
            $ids_sensores_visibles_localizaciones = array();
            $ids_actuadores_visibles_localizaciones = array();
            $ids_grupos_actuadores_visibles_localizaciones = array();
        }
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
        $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];

        // Si se tiene el módulo Localizaciones
        if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los widgets del usuario que muestran información de localizaciones
            // 2. Se recuperan los identificadores de localizaciones del widget
            // 3. Si alguna localizacion no es visible por el usuario, se elimina el widget
            $consulta_widgets = "
                SELECT *
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')";
            $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
            if ($res_widgets == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_widgets."'");
            }

            $ids_widgets_pendientes_borrado = array();
            while ($fila_widget = $res_widgets->dame_siguiente_fila())
            {
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_VALOR_RATIO:
                    {
                        $ids_localizaciones_widget = dame_ids_localizaciones_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_localizaciones_widget as $id_localizacion_widget)
                        {
                            $localizacion_visible_usuario = false;
                            if ($localizacion_visible_usuario == false)
                            {
                                if (($permiso_todas_localizaciones == true) ||
                                    (in_array($id_localizacion_widget, $ids_localizaciones) == true))
                                {
                                    $localizacion_visible_usuario = true;
                                }
                            }
                            if ($localizacion_visible_usuario == false)
                            {
                                array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            // Se borran los widgets pendientes de borrado
            if (count($ids_widgets_pendientes_borrado) > 0)
            {
                $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
                $operacion_borrado_widgets_pendientes = "
                    DELETE
                    FROM widgets
                    WHERE
                        id IN (".$cadena_ids_widgets_pendientes_borrado.")";
                $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
                if ($res_borrado_widgets_pendientes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
                }
            }
        }

        // Se eliminan los widgets de la sección ratios del módulo Localizaciones (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_LOCALIZACIONES]) > 0) && (in_array(SECCION_LOCALIZACIONES_RATIOS, $secciones_usuario[MODULO_LOCALIZACIONES]) == false)))
        {
            $operacion_borrado_widgets_localizaciones = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_VALOR_RATIO."'))";
            $res_borrado_widgets_localizaciones = $bd_red->ejecuta_operacion($operacion_borrado_widgets_localizaciones);
            if ($res_borrado_widgets_localizaciones == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_localizaciones."'");
            }
        }

        // Si se tiene el módulo Sensores
        if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los widgets del usuario que muestran información de sensores
            // 2. Se recuperan los identificadores de sensores del widget
            // 3. Si algún sensor no es visible por el usuario, se elimina el widget
            $consulta_widgets = "
                SELECT *
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')";
            $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
            if ($res_widgets == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_widgets."'");
            }

            $ids_widgets_pendientes_borrado = array();
            while ($fila_widget = $res_widgets->dame_siguiente_fila())
            {
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                    case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
                    {
                        $ids_sensores_widget = dame_ids_sensores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_sensores_widget as $id_sensor_widget)
                        {
                            if (($id_sensor_widget == "") || ($id_sensor_widget == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_widget, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_widget, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            // Se borran los widgets pendientes de borrado
            if (count($ids_widgets_pendientes_borrado) > 0)
            {
                $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
                $operacion_borrado_widgets_pendientes = "
                    DELETE
                    FROM widgets
                    WHERE
                        id IN (".$cadena_ids_widgets_pendientes_borrado.")";
                $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
                if ($res_borrado_widgets_pendientes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
                }
            }
        }

        // Se eliminan los widgets de la sección información del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_widgets_sensores = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_VALOR_DIGITAL_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_VALOR_ANALOGICO_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_GRAFICA_VALORES_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_MAPA_CALOR_SENSOR."'))";
            $res_borrado_widgets_sensores = $bd_red->ejecuta_operacion($operacion_borrado_widgets_sensores);
            if ($res_borrado_widgets_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_sensores."'");
            }
        }

        // Se eliminan los widgets de la sección comparación del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_widgets_sensores = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR."')
                        OR (tipo = '".TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES."')
                        OR (tipo = '".TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES."')
                        OR (tipo = '".TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES."')
                        OR (tipo = '".TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES."')
                        OR (tipo = '".TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES."'))";
            $res_borrado_widgets_sensores = $bd_red->ejecuta_operacion($operacion_borrado_widgets_sensores);
            if ($res_borrado_widgets_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_sensores."'");
            }
        }

        // Si se tiene el módulo Actuadores
        if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los widgets del usuario que muestran información de actuadores o grupos de actuadores
            // 2. Se recuperan los identificadores de actuadores o grupos de actuadores del widget
            // 3. Si algún actuador o grupo de actuadores no es visible por el usuario, se elimina el widget
            $consulta_widgets = "
                SELECT *
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')";
            $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
            if ($res_widgets == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_widgets."'");
            }

            $ids_widgets_pendientes_borrado = array();
            while ($fila_widget = $res_widgets->dame_siguiente_fila())
            {
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_INFORMACION_ACTUADOR:
                    {
                        $ids_actuadores_widget = dame_ids_actuadores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_actuadores_widget as $id_actuador_widget)
                        {
                            if (($id_actuador_widget == "") || ($id_actuador_widget == ID_NINGUNO))
                            {
                                continue;
                            }
                            $actuador_visible_usuario = false;
                            if ($actuador_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (dame_actuador_actuadores_grupos($id_actuador_widget, $ids_actuadores, $ids_grupos_actuadores) == true))
                                {
                                    $actuador_visible_usuario = true;
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_actuador_widget, $ids_actuadores_visibles_localizaciones) == true)
                                    {
                                        $actuador_visible_usuario = true;
                                    }
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                    {
                        $ids_grupos_actuadores_widget = dame_ids_grupos_actuadores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_grupos_actuadores_widget as $id_grupo_actuadores_widget)
                        {
                            $grupo_actuadores_visible_usuario = false;
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (in_array($id_grupo_actuadores_widget, $ids_grupos_actuadores) == true))
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_grupo_actuadores_widget, $ids_grupos_actuadores_visibles_localizaciones) == true)
                                    {
                                        $grupo_actuadores_visible_usuario = true;
                                    }
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            // Se borran los widgets pendientes de borrado
            if (count($ids_widgets_pendientes_borrado) > 0)
            {
                $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
                $operacion_borrado_widgets_pendientes = "
                    DELETE
                    FROM widgets
                    WHERE
                        id IN (".$cadena_ids_widgets_pendientes_borrado.")";
                $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
                if ($res_borrado_widgets_pendientes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
                }
            }
        }

        // Se eliminan los widgets de la sección información del módulo Actuadores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
        {
            $operacion_borrado_widgets_actuadores = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_INFORMACION_ACTUADOR."')
                        OR (tipo = '".TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES."'))";
            $res_borrado_widgets_actuadores = $bd_red->ejecuta_operacion($operacion_borrado_widgets_actuadores);
            if ($res_borrado_widgets_actuadores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_actuadores."'");
            }
        }

        // Se eliminan los widgets de la sección consumos y costes del módulo Smartmeter (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_widgets_smartmeter = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR."'))";
            $res_borrado_widgets_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_widgets_smartmeter);
            if ($res_borrado_widgets_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_smartmeter."'");
            }
        }

        // Se eliminan los widgets de la sección facturas eléctricas del módulo Smartmeter (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_widgets_smartmeter = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_COSTE_FACTURA_SENSOR."'))";
            $res_borrado_widgets_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_widgets_smartmeter);
            if ($res_borrado_widgets_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_smartmeter."'");
            }
        }

        // Si se tiene el módulo Proyectos
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los widgets del usuario que muestran información de líneas base o de proyectos
            // 2. Se recuperan los identificadores de sensores del widget
            // 3. Si algún sensor no es visible por el usuario, se elimina el widget
            $consulta_widgets = "
                SELECT *
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')";
            $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
            if ($res_widgets == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_widgets."'");
            }

            $ids_widgets_pendientes_borrado = array();
            while ($fila_widget = $res_widgets->dame_siguiente_fila())
            {
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
                    case TIPO_WIDGET_INFORMACION_PROYECTO:
                    {
                        $ids_sensores_widget = dame_ids_sensores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_sensores_widget as $id_sensor_widget)
                        {
                            if (($id_sensor_widget == "") || ($id_sensor_widget == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_widget, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_widget, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            // Se borran los widgets pendientes de borrado
            if (count($ids_widgets_pendientes_borrado) > 0)
            {
                $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
                $operacion_borrado_widgets_pendientes = "
                    DELETE
                    FROM widgets
                    WHERE
                        id IN (".$cadena_ids_widgets_pendientes_borrado.")";
                $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
                if ($res_borrado_widgets_pendientes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
                }
            }
        }

        // Se eliminan los widgets de la sección líneas base del módulo Proyectos (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_widgets_lineas_base = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_SIMULADOR_LINEA_BASE."'))";
            $res_borrado_widgets_lineas_base = $bd_red->ejecuta_operacion($operacion_borrado_widgets_lineas_base);
            if ($res_borrado_widgets_lineas_base == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_lineas_base."'");
            }
        }

        // Se eliminan los widgets de la sección información del módulo Proyectos (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_widgets_proyectos = "
                DELETE
                FROM widgets
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_WIDGET_INFORMACION_PROYECTO."'))";
            $res_borrado_widgets_proyectos = $bd_red->ejecuta_operacion($operacion_borrado_widgets_proyectos);
            if ($res_borrado_widgets_proyectos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_proyectos."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar una localización
    function elimina_widgets_localizacion_eliminada($id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de localizaciones
        // 2. Se recuperan los identificadores de localizaciones del widget
        // 3. Si la localización es alguna localización del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_VALOR_RATIO:
                {
                    $ids_localizaciones_widget = dame_ids_localizaciones_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_localizacion, $ids_localizaciones_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar un ratio
    function elimina_widgets_ratio_eliminado($id_ratio)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de ratios
        // 2. Se recuperan los identificadores de ratios del widget
        // 3. Si el ratio es algún ratio del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_VALOR_RATIO:
                {
                    $ids_ratios_widget = dame_ids_ratios_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_ratio, $ids_ratios_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Modifica los widgets correspondientes al eliminar un ratio (lo establece a ninguno)
    function modifica_widgets_ratio_eliminado($id_ratio)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que utilicen el ratio para mostrar información
        // 2. Se recuperan los identificadores de ratios del widget
        // 3. Si el ratio es algún ratio del widget, se establece a ninguno
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            $id_widget = $fila_widget["id"];
            $tipo_widget = $fila_widget["tipo"];
            $cadena_parametros_tipo_widget = $fila_widget["parametros_tipo"];
            switch ($tipo_widget)
            {
                case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                {
                    $indice_id_ratio_widget = dame_indice_id_ratio_widget($tipo_widget);
                    $parametros_tipo_widget = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_widget);
                    $id_ratio_widget = $parametros_tipo_widget[$indice_id_ratio_widget];
                    if ($id_ratio_widget == $id_ratio)
                    {
                        $parametros_tipo_widget[$indice_id_ratio_widget] = ID_NINGUNO;
                        $cadena_parametros_tipo_widget_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_widget);
                        $operacion_modificacion_widgets = "
                            UPDATE widgets
                            SET
                                parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_widget_modificada)."'
                            WHERE
                                id = '".$bd_red->_($id_widget)."'";
                        $res_modificacion_widgets = $bd_red->ejecuta_operacion($operacion_modificacion_widgets);
                        if ($res_modificacion_widgets == false)
                        {
                            throw new Exception("Error en la operación: '".$operacion_modificacion_widgets."'");
                        }
                    }
                    break;
                }
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar un sensor
    function elimina_widgets_sensor_eliminado($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de sensores
        // 2. Se recuperan los identificadores de sensores del widget
        // 3. Si el sensor es algún sensor del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
                {
                    $ids_sensores_widget = dame_ids_sensores_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_sensor, $ids_sensores_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar un actuador
    function elimina_widgets_actuador_eliminado($id_actuador)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de actuadores
        // 2. Se recuperan los identificadores de actuadores del widget
        // 3. Si el actuador es algún actuador del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_INFORMACION_ACTUADOR:
                {
                    $ids_actuadores_widget = dame_ids_actuadores_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_actuador, $ids_actuadores_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar un grupo de actuadores
    function elimina_widgets_grupo_actuadores_eliminado($id_grupo_actuadores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de grupos de actuadores
        // 2. Se recuperan los identificadores de grupos de actuadores del widget
        // 3. Si el grupo de actuadores es algún grupo de actuadores del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                {
                    $ids_grupos_actuadores_widget = dame_ids_grupos_actuadores_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar una línea base
    function elimina_widgets_linea_base_eliminada($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de líneas base
        // 2. Se recuperan los identificadores de líneas base del widget
        // 3. Si la línea base es alguna línea base del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
                {
                    $ids_lineas_base_widget = dame_ids_lineas_base_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_linea_base, $ids_lineas_base_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    // Elimina los widgets correspondientes al eliminar un proyecto
    function elimina_widgets_proyecto_eliminado($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los widgets que muestran información de proyectos
        // 2. Se recuperan los identificadores de proyectos del widget
        // 3. Si el proyecto es algún proyecto del widget, se elimina el widget
        $consulta_widgets = "
            SELECT *
            FROM widgets
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }

        $ids_widgets_pendientes_borrado = array();
        while ($fila_widget = $res_widgets->dame_siguiente_fila())
        {
            switch ($fila_widget["tipo"])
            {
                case TIPO_WIDGET_INFORMACION_PROYECTO:
                {
                    $ids_proyectos_widget = dame_ids_proyectos_widget(
                        $fila_widget["tipo"],
                        $fila_widget["parametros_tipo"]);
                    if (in_array($id_proyecto, $ids_proyectos_widget) == true)
                    {
                        array_push($ids_widgets_pendientes_borrado, $fila_widget["id"]);
                        break;
                    }
                    break;
                }
            }
        }
        if (count($ids_widgets_pendientes_borrado) > 0)
        {
            $cadena_ids_widgets_pendientes_borrado = dame_cadena_ids_consulta($ids_widgets_pendientes_borrado);
            $operacion_borrado_widgets_pendientes = "
                DELETE
                FROM widgets
                WHERE
                    id IN (".$cadena_ids_widgets_pendientes_borrado.")";
            $res_borrado_widgets_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_widgets_pendientes);
            if ($res_borrado_widgets_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_widgets_pendientes."'");
            }
        }
    }


    //
    // Funciones de tipos de widget
    //


    function dame_nombres_valores_parametros_tipo_widget($tipo_widget, $cadena_parametros_tipo_widget)
    {
        // Se recuperan los parámetros de tipo del widget
        $parametros_tipo_widget = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_widget);
        $nombres_valores_parametros_tipo_widget = array();
        switch ($tipo_widget)
        {
            // Widgets "generales" (sin módulo asociado)
            case TIPO_WIDGET_IMAGEN:
            {
                $nombres_valores_parametros_tipo_widget["altura_maxima"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_IMAGEN_ALTURA_MAXIMA];
                break;
            }
            // Widgets de localizaciones (Ratios)
            case TIPO_WIDGET_VALOR_RATIO:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["id_localizacion"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ID_LOCALIZACION];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_ICONO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_RATIO_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            // Widget de sensores (Información)
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_CLASE_SENSOR];
                $nombres_valores_parametros_tipo_widget["granularidad_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_GRANULARIDAD_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ICONO];
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ICONO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["tipo_grafico"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_TIPO_GRAFICO];
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_CLASE_SENSOR];
                $nombres_valores_parametros_tipo_widget["granularidad_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_GRANULARIDAD_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["valor_minimo_indicador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_MINIMO_INDICADOR];
                $nombres_valores_parametros_tipo_widget["valor_maximo_indicador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_MAXIMO_INDICADOR];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["valor_digital"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_DIGITAL];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ICONO];
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["tipo_grafico"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_TIPO_GRAFICO];
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["valor_minimo_indicador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_MINIMO_INDICADOR];
                $nombres_valores_parametros_tipo_widget["valor_maximo_indicador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_MAXIMO_INDICADOR];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["valor_digital"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_DIGITAL];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ICONO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INTERVALO_VALORES];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["colores_mapa_calor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_COLORES_MAPA_CALOR];
                $nombres_valores_parametros_tipo_widget["tipo_mapa_calor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_TIPO_MAPA_CALOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            // Widgets de sensores (Comparación)
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_INTERVALO_VALORES];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_EXCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_INTERVALO_VALORES];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["tipo_valores_limite_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_TIPO_VALORES_LIMITE_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ICONO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_EXCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clase_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_CLASE_SENSOR];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_CAMPO]);
                $nombres_valores_parametros_tipo_widget["campo"] = $campo_parametros_extra[0];
                $nombres_valores_parametros_tipo_widget["parametros_extra_campo"] = $campo_parametros_extra[1];
                $nombres_valores_parametros_tipo_widget["ids_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_IDS_SENSORES]);
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INTERVALO_VALORES];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clases_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_CLASES_SENSORES]);
                $cadenas_campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_CAMPOS]);
                $campos = array();
                $parametros_extra_campos = array();
                foreach ($cadenas_campos_parametros_extra as $cadena_campo_parametros_extra)
                {
                    $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $cadena_campo_parametros_extra);
                    $campo = $campo_parametros_extra[0];
                    $parametros_extra_campo = $campo_parametros_extra[1];
                    array_push($campos, $campo);
                    array_push($parametros_extra_campos, $parametros_extra_campo);
                }
                $nombres_valores_parametros_tipo_widget["campos"] = $campos;
                $nombres_valores_parametros_tipo_widget["parametros_extra_campos"] = $parametros_extra_campos;
                $nombres_valores_parametros_tipo_widget["ids_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_IDS_SENSORES]);
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INTERVALO_VALORES];
                $nombres_valores_parametros_tipo_widget["unificar_escalas"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_UNIFICAR_ESCALAS];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clases_sensor"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_CLASES_SENSOR]);
                $cadenas_campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_CAMPOS]);
                $campos = array();
                $parametros_extra_campos = array();
                foreach ($cadenas_campos_parametros_extra as $cadena_campo_parametros_extra)
                {
                    $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $cadena_campo_parametros_extra);
                    $campo = $campo_parametros_extra[0];
                    $parametros_extra_campo = $campo_parametros_extra[1];
                    array_push($campos, $campo);
                    array_push($parametros_extra_campos, $parametros_extra_campo);
                }
                $nombres_valores_parametros_tipo_widget["campos"] = $campos;
                $nombres_valores_parametros_tipo_widget["parametros_extra_campos"] = $parametros_extra_campos;
                $nombres_valores_parametros_tipo_widget["ids_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_IDS_SENSORES]);
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INTERVALO_VALORES];
                $nombres_valores_parametros_tipo_widget["agregacion"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_AGREGACION];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clases_sensor"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_CLASES_SENSOR]);
                $cadenas_campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_CAMPOS]);
                $campos = array();
                $parametros_extra_campos = array();
                foreach ($cadenas_campos_parametros_extra as $cadena_campo_parametros_extra)
                {
                    $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $cadena_campo_parametros_extra);
                    $campo = $campo_parametros_extra[0];
                    $parametros_extra_campo = $campo_parametros_extra[1];
                    array_push($campos, $campo);
                    array_push($parametros_extra_campos, $parametros_extra_campo);
                }
                $nombres_valores_parametros_tipo_widget["campos"] = $campos;
                $nombres_valores_parametros_tipo_widget["parametros_extra_campos"] = $parametros_extra_campos;
                $nombres_valores_parametros_tipo_widget["ids_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_IDS_SENSORES]);
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INTERVALO_VALORES];
                $nombres_valores_parametros_tipo_widget["agregacion"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_AGREGACION];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_ICONO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                $nombres_valores_parametros_tipo_widget["tipo_grafica"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_TIPO_GRAFICA];
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["clases_sensor"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_CLASES_SENSOR]);
                $cadenas_campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_CAMPOS]);
                $campos = array();
                $parametros_extra_campos = array();
                foreach ($cadenas_campos_parametros_extra as $cadena_campo_parametros_extra)
                {
                    $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $cadena_campo_parametros_extra);
                    $campo = $campo_parametros_extra[0];
                    $parametros_extra_campo = $campo_parametros_extra[1];
                    array_push($campos, $campo);
                    array_push($parametros_extra_campos, $parametros_extra_campo);
                }
                $nombres_valores_parametros_tipo_widget["campos"] = $campos;
                $nombres_valores_parametros_tipo_widget["parametros_extra_campos"] = $parametros_extra_campos;
                $nombres_valores_parametros_tipo_widget["ids_sensores"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_IDS_SENSORES]);
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["intervalo_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INTERVALO_VALORES];
                $nombres_valores_parametros_tipo_widget["agregacion"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_AGREGACION];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            // Widgets de actuadores (Información)
            case TIPO_WIDGET_INFORMACION_ACTUADOR:
            {
                $nombres_valores_parametros_tipo_widget["clase_actuador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_CLASE_ACTUADOR];
                $nombres_valores_parametros_tipo_widget["id_actuador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_ID_ACTUADOR];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_ICONO];
                break;
            }
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
            {
                $nombres_valores_parametros_tipo_widget["clase_actuador"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_CLASE_ACTUADOR];
                $nombres_valores_parametros_tipo_widget["id_grupo_actuadores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_ID_GRUPO_ACTUADORES];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_ICONO];
                break;
            }
            // Widgets de SmartMeter (Consumos y costes)
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["id_ratio"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_RATIO];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["valor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_VALOR];
                $nombres_valores_parametros_tipo_widget["agrupacion_valores"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_AGRUPACION_VALORES];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $cadena_horario_semanal = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_INCLUSION_FECHAS];
                $nombres_valores_parametros_tipo_widget["horario_semanal"] = dame_horario_semanal($cadena_horario_semanal);
                $nombres_valores_parametros_tipo_widget["exclusion_fechas"] = dame_fechas($cadena_exclusion_fechas);
                $nombres_valores_parametros_tipo_widget["inclusion_fechas"] = dame_fechas($cadena_inclusion_fechas);
                break;
            }
            // Widgets de SmartMeter (Facturas)
            case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
            {
                $nombres_valores_parametros_tipo_widget["medicion"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_MEDICION];
                $nombres_valores_parametros_tipo_widget["id_sensor"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_ID_SENSOR];
                $nombres_valores_parametros_tipo_widget["concepto_factura"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_CONCEPTO_FACTURA];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["utilizar_colores_fondo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_UTILIZAR_COLORES_FONDO];
                $nombres_valores_parametros_tipo_widget["colores_fondo"] = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_COLORES_FONDO]);
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_1"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_VALOR_LIMITE_COLORES_FONDO_1];
                $nombres_valores_parametros_tipo_widget["valor_limite_colores_fondo_2"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_VALOR_LIMITE_COLORES_FONDO_2];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_ICONO];
                break;
            }
            // Widgets de proyectos (Líneas base)
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
            {
                $nombres_valores_parametros_tipo_widget["id_linea_base"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_FECHA_INICIO_PERIODO_TIEMPO];
                break;
            }
            // Widgets de proyectos (Información)
            case TIPO_WIDGET_INFORMACION_PROYECTO:
            {
                $nombres_valores_parametros_tipo_widget["id_proyecto"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ID_PROYECTO];
                $nombres_valores_parametros_tipo_widget["periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["iniciar_comienzo_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_INICIAR_COMIENZO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["fecha_inicio_periodo_tiempo"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_FECHA_INICIO_PERIODO_TIEMPO];
                $nombres_valores_parametros_tipo_widget["icono"] = $parametros_tipo_widget[INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ICONO];
                break;
            }
            default:
            {
                throw new Exception("Tipo de widget desconocido: '".$tipo_widget."'");
            }
        }
        return ($nombres_valores_parametros_tipo_widget);
    }


    //
    // Funciones de obtención de información de widgets
    //


    // Devuelve la fila del widget
    function dame_fila_widget($id_widget)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_widget = "
            SELECT *
            FROM widgets
            WHERE
                id = '".$bd_red->_($id_widget)."'";
        $res_widget = $bd_red->ejecuta_consulta($consulta_widget);
        if (($res_widget == false) || ($res_widget->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_widget."'");
        }
        $fila_widget = $res_widget->dame_siguiente_fila();
        return ($fila_widget);
    }



    //
    // Funciones auxiliares
    //


    // Devuelve si el usuario actual tiene permiso de administración de widgets
    function dame_administracion_widgets()
    {
        $administracion_widgets = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_personal"]["administracion_widgets"] == VALOR_SI);
        return ($administracion_widgets);
    }


    // Devuelve las fechas de inicio y fin según el periodo de tiempo del widget correspondiente
    function dame_fechas_inicio_fin_periodo_tiempo_widget(
        $periodo_tiempo,
        $iniciar_comienzo_periodo_tiempo,
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
        $clase_sensor="")
    {
        $fecha_hora_fin_local = dame_fecha_hora_actual_local();
        $fecha_hora_inicio_local = clone $fecha_hora_fin_local;
        $hora_inicio = 0; 
        // Si el sensor es de gas, formazos que widgets empiecen a las 06:00 
        if($clase_sensor == CLASE_SENSOR_GAS)
        {
            $hora_inicio = 6; 
        }
        switch ($periodo_tiempo)
        {
            case PERIODO_TIEMPO_FECHA_INICIO:
            {
                $zona_horaria = dame_zona_horaria_local();
                $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_inicio_periodo_tiempo_base_datos_local, FORMATO_FECHA_BASE_DATOS, $zona_horaria);
                break;
            }
            default:
            {
                if ($iniciar_comienzo_periodo_tiempo == true)
                {
                    switch ($periodo_tiempo)
                    {
                        case PERIODO_TIEMPO_HORA:
                        {
                            $fecha_hora_inicio_local->setTime($fecha_hora_inicio_local->format("H"), 0, 0);
                            break;
                        }
                        case PERIODO_TIEMPO_DIA:
                        {
                            $fecha_hora_inicio_local->setTime($hora_inicio, 0, 0);
                            break;
                        }
                        case PERIODO_TIEMPO_SEMANA:
                        {
                            $fecha_hora_inicio_local->setTime($hora_inicio, 0, 0);
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
                            $fecha_hora_inicio_local->setTime($hora_inicio, 0, 0);
                            $fecha_hora_inicio_local->setDate($fecha_hora_inicio_local->format("Y"), $fecha_hora_inicio_local->format("m"), 1);
                            break;
                        }
                        case PERIODO_TIEMPO_ANYO:
                        {
                            $fecha_hora_inicio_local->setTime($hora_inicio, 0, 0);
                            $fecha_hora_inicio_local->setDate($fecha_hora_inicio_local->format("Y"), 1, 1);
                            break;
                        }
                        default:
                        {
                            throw new Exception("Periodo desconocido");
                        }
                    }
                }
                else
                {
                    switch ($periodo_tiempo)
                    {
                        case PERIODO_TIEMPO_HORA:
                        {
                            $cadena_periodo = "PT1H";
                            break;
                        }
                        case PERIODO_TIEMPO_DIA:
                        {
                            $cadena_periodo = "P1D";
                            break;
                        }
                        case PERIODO_TIEMPO_SEMANA:
                        {
                            $cadena_periodo = "P7D";
                            break;
                        }
                        case PERIODO_TIEMPO_MES:
                        {
                            $cadena_periodo = "P1M";
                            break;
                        }
                        case PERIODO_TIEMPO_ANYO:
                        {
                            $cadena_periodo = "P1Y";
                            break;
                        }
                        default:
                        {
                            throw new Exception("Periodo desconocido");
                        }
                    }
                    $periodo_tiempo = new DateInterval($cadena_periodo);
                    $fecha_hora_inicio_local->sub($periodo_tiempo);
                }
                break;
            }
        }

        $fechas_horas_inicio_fin = array(
            "fecha_hora_inicio_local" => $fecha_hora_inicio_local,
            "fecha_hora_fin_local" => $fecha_hora_fin_local);
        return ($fechas_horas_inicio_fin);
    }


    // Devuelve la información de periodos (fechas de inicio de periodos y días de periodos) según el periodo de tiempo del widget correspondiente
    function dame_info_periodos_periodo_tiempo_widget($periodo_tiempo, $iniciar_comienzo_periodo_tiempo, $clase_sensor = "")
    {
        $fecha_hora_inicio_periodo_posterior_local = dame_fecha_hora_actual_local();
        switch ($periodo_tiempo)
        {
            case PERIODO_TIEMPO_HORA:
            {
                $cadena_periodo = "PT1H";
                break;
            }
            case PERIODO_TIEMPO_DIA:
            {
                $cadena_periodo = "P1D";
                break;
            }
            case PERIODO_TIEMPO_SEMANA:
            {
                $cadena_periodo = "P7D";
                break;
            }
            case PERIODO_TIEMPO_MES:
            {
                $cadena_periodo = "P28D";
                break;
            }
            case PERIODO_TIEMPO_ANYO:
            {
                $cadena_periodo = "P1Y";
                break;
            }
            default:
            {
                throw new Exception("Periodo desconocido");
            }
        }
        
        $hora_inicio = 0; 
        // Si el sensor es de gas, formazos que widgets empiecen a las 06:00 
        if($clase_sensor == CLASE_SENSOR_GAS)
        {
            $hora_inicio = 6; 
        }
        
        if ($iniciar_comienzo_periodo_tiempo == true)
        {
            switch ($periodo_tiempo)
            {
                case PERIODO_TIEMPO_HORA:
                {
                    $fecha_hora_inicio_periodo_posterior_local->setTime($fecha_hora_inicio_periodo_posterior_local->format("H"), 0, 0);
                    break;
                }
                case PERIODO_TIEMPO_DIA:
                {
                    $fecha_hora_inicio_periodo_posterior_local->setTime($hora_inicio, 0, 0);
                    break;
                }
                case PERIODO_TIEMPO_SEMANA:
                {
                    $fecha_hora_inicio_periodo_posterior_local->setTime($hora_inicio, 0, 0);
                    $numero_dia_semana = $fecha_hora_inicio_periodo_posterior_local->format('w');
                    if ($numero_dia_semana == 0)
                    {
                        $numero_dia_semana = 7;
                    }
                    date_modify($fecha_hora_inicio_periodo_posterior_local, '-'.($numero_dia_semana - 1).' day');
                    break;
                }
                case PERIODO_TIEMPO_MES:
                {
                    $fecha_hora_inicio_periodo_posterior_local->setTime($hora_inicio, 0, 0);
                    $fecha_hora_inicio_periodo_posterior_local->setDate(
                        $fecha_hora_inicio_periodo_posterior_local->format("Y"),
                        $fecha_hora_inicio_periodo_posterior_local->format("m"),
                        1);
                    break;
                }
                case PERIODO_TIEMPO_ANYO:
                {
                    $fecha_hora_inicio_periodo_posterior_local->setTime($hora_inicio, 0, 0);
                    $fecha_hora_inicio_periodo_posterior_local->setDate(
                        $fecha_hora_inicio_periodo_posterior_local->format("Y"),
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
        else
        {
            switch ($periodo_tiempo)
            {
                case PERIODO_TIEMPO_MES:
                {
                    $fecha_hora_inicio_periodo_posterior_local->sub(new DateInterval("P1M"));
                    break;
                }
                default:
                {
                    $fecha_hora_inicio_periodo_posterior_local->sub(new DateInterval($cadena_periodo));
                    break;
                }
            }
        }
        $fecha_hora_inicio_periodo_anterior_local = clone $fecha_hora_inicio_periodo_posterior_local;
        $fecha_hora_inicio_periodo_anterior_local->sub(new DateInterval($cadena_periodo));
        $periodo = $fecha_hora_inicio_periodo_posterior_local->diff($fecha_hora_inicio_periodo_anterior_local);

        // http://php.net/manual/es/class.dateinterval.php
        $numero_dias_periodo = $periodo->days;
        if (($numero_dias_periodo === -99999) || ($numero_dias_periodo === false))
        {
            $numero_dias_periodo = ($periodo->y * 365) + ($periodo->m * 30) + $periodo->d;
        }

        $info_periodos = array(
            "fecha_hora_inicio_periodo_anterior_local" => $fecha_hora_inicio_periodo_anterior_local,
            "fecha_hora_inicio_periodo_posterior_local" => $fecha_hora_inicio_periodo_posterior_local,
            "numero_dias_periodo" => $numero_dias_periodo);
        return ($info_periodos);
    }


    // Devuelve las filas de los widgets visibles por el usuario (con perfil estándar)
    function dame_filas_widgets_visibles_usuario(
        $id_usuario,
        $perfil,
        $filas_widgets)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $_SESSION["id_red"]);
        $secciones_usuario = dame_secciones_usuario($id_usuario, $_SESSION["id_red"]);

        // Se recuperan los parámetros de los módulos del usuario:
        // - Localizaciones
        // - Sensores
        // - Actuadores
        $parametros_modulo_localizaciones = NULL;
        $parametros_modulo_sensores = NULL;
        $parametros_modulo_actuadores = NULL;
        $consulta_parametros_modulos_usuario = "
            SELECT
                modulo,
                parametros
            FROM modulos_usuarios
            WHERE
                (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulos_usuario = $bd_red->ejecuta_consulta($consulta_parametros_modulos_usuario);
        if ($res_parametros_modulos_usuario == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulos_usuario."'");
        }
        while ($fila_parametros_modulo_usuario = $res_parametros_modulos_usuario->dame_siguiente_fila())
        {
            $modulo = $fila_parametros_modulo_usuario["modulo"];
            $cadena_parametros = $fila_parametros_modulo_usuario["parametros"];

            switch ($modulo)
            {
                case MODULO_LOCALIZACIONES:
                {
                    $parametros_modulo_localizaciones = dame_parametros_modulo_localizaciones_usuario($cadena_parametros);
                    break;
                }
                case MODULO_SENSORES:
                {
                    $parametros_modulo_sensores = dame_parametros_modulo_sensores_usuario($cadena_parametros);
                    break;
                }
                case MODULO_ACTUADORES:
                {
                    $parametros_modulo_actuadores = dame_parametros_modulo_actuadores_usuario($cadena_parametros);
                    break;
                }
            }
        }
        if (($parametros_modulo_localizaciones === NULL) ||
            ($parametros_modulo_sensores === NULL) ||
            ($parametros_modulo_actuadores === NULL))
        {
            throw new Exception("Error al recuperar los parámetros de módulos del usuario: '".$id_usuario."'");
        }

        // Identificadores de elementos visibles por el usuario
        if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
        {
            $permiso_todas_localizaciones = $parametros_modulo_localizaciones["permiso_todas_localizaciones"];
            if ($permiso_todas_localizaciones == true)
            {
                $ids_localizaciones = dame_ids_localizaciones();
            }
            else
            {
                $ids_localizaciones = $parametros_modulo_localizaciones["ids_localizaciones"];
            }
            $ids_sensores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_actuadores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
            $ids_grupos_actuadores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
        }
        else
        {
            $ids_sensores_visibles_localizaciones = array();
            $ids_actuadores_visibles_localizaciones = array();
            $ids_grupos_actuadores_visibles_localizaciones = array();
        }
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
        $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];

        // Filas de widgets visibles por el usuario
        $filas_widgets_visibles_usuario = array();

        // Se recorren las filas de los widgets
        foreach ($filas_widgets as $fila_widget)
        {
            // Flag de sensor visible por el usuario
            $widget_visible_usuario = true;

            // Se comprueba la sección ratios del módulo Localizaciones (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_LOCALIZACIONES]) > 0) && (in_array(SECCION_LOCALIZACIONES_RATIOS, $secciones_usuario[MODULO_LOCALIZACIONES]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_VALOR_RATIO:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Comprobación de elementos visibles del módulo Localizaciones
            if ($widget_visible_usuario == true)
            {
                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_VALOR_RATIO:
                        {
                            $ids_localizaciones_widget = dame_ids_localizaciones_widget(
                                $fila_widget["tipo"],
                                $fila_widget["parametros_tipo"]);
                            foreach ($ids_localizaciones_widget as $id_localizacion_widget)
                            {
                                $localizacion_visible_usuario = false;
                                if ($localizacion_visible_usuario == false)
                                {
                                    if (($permiso_todas_localizaciones == true) ||
                                        (in_array($id_localizacion_widget, $ids_localizaciones) == true))
                                    {
                                        $localizacion_visible_usuario = true;
                                    }
                                }
                                if ($localizacion_visible_usuario == false)
                                {
                                    $widget_visible_usuario = false;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección información del módulo Sensores (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                        case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                        case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                        case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                        case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                        case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección comparación del módulo Sensores (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                        case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                        case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Comprobación de elementos visibles del módulo Sensores (se incluyen los del módulo SmartMeter)
            if ($widget_visible_usuario == true)
            {
                if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                        case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                        case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                        case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                        case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                        case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                        case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                        case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                        case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                        case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                        case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
                        {
                            $ids_sensores_widget = dame_ids_sensores_widget(
                                $fila_widget["tipo"],
                                $fila_widget["parametros_tipo"]);
                            foreach ($ids_sensores_widget as $id_sensor_widget)
                            {
                                if (($id_sensor_widget == "") || ($id_sensor_widget == ID_NINGUNO))
                                {
                                    continue;
                                }
                                $sensor_visible_usuario = false;
                                if ($sensor_visible_usuario == false)
                                {
                                    if (($permiso_todos_sensores == true) ||
                                        (dame_sensor_sensores_grupos($id_sensor_widget, $ids_sensores, $ids_grupos_sensores) == true))
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                                if ($sensor_visible_usuario == false)
                                {
                                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                    {
                                        if (in_array($id_sensor_widget, $ids_sensores_visibles_localizaciones) == true)
                                        {
                                            $sensor_visible_usuario = true;
                                        }
                                    }
                                }
                                if ($sensor_visible_usuario == false)
                                {
                                    $widget_visible_usuario = false;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección información del módulo Actuadores (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_INFORMACION_ACTUADOR:
                        case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Comprobación de elementos visibles del módulo Actuadores
            if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
            {
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_INFORMACION_ACTUADOR:
                    {
                        $ids_actuadores_widget = dame_ids_actuadores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_actuadores_widget as $id_actuador_widget)
                        {
                            if (($id_actuador_widget == "") || ($id_actuador_widget == ID_NINGUNO))
                            {
                                continue;
                            }
                            $actuador_visible_usuario = false;
                            if ($actuador_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (dame_actuador_actuadores_grupos($id_actuador_widget, $ids_actuadores, $ids_grupos_actuadores) == true))
                                {
                                    $actuador_visible_usuario = true;
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_actuador_widget, $ids_actuadores_visibles_localizaciones) == true)
                                    {
                                        $actuador_visible_usuario = true;
                                    }
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                $widget_visible_usuario = false;
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                    {
                        $ids_grupos_actuadores_widget = dame_ids_grupos_actuadores_widget(
                            $fila_widget["tipo"],
                            $fila_widget["parametros_tipo"]);
                        foreach ($ids_grupos_actuadores_widget as $id_grupo_actuadores_widget)
                        {
                            $grupo_actuadores_visible_usuario = false;
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (in_array($id_grupo_actuadores_widget, $ids_grupos_actuadores) == true))
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_grupo_actuadores_widget, $ids_grupos_actuadores_visibles_localizaciones) == true)
                                    {
                                        $grupo_actuadores_visible_usuario = true;
                                    }
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                $widget_visible_usuario = false;
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            // Se comprueba la sección consumos y costes del módulo Smartmeter (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección facturas del módulo Smartmeter (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección líneas base del módulo Proyectos (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Se comprueba la sección información del módulo Proyectos (si no se tiene el módulo o la sección)
            if ($widget_visible_usuario == true)
            {
                if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
                    ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == false)))
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_INFORMACION_PROYECTO:
                        {
                            $widget_visible_usuario = false;
                            break;
                        }
                    }
                }
            }

            // Comprobación de elementos visibles del módulo Proyectos
            if ($widget_visible_usuario == true)
            {
                if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
                {
                    switch ($fila_widget["tipo"])
                    {
                        case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
                        case TIPO_WIDGET_INFORMACION_PROYECTO:
                        {
                            $ids_sensores_widget = dame_ids_sensores_widget(
                                $fila_widget["tipo"],
                                $fila_widget["parametros_tipo"]);
                            foreach ($ids_sensores_widget as $id_sensor_widget)
                            {
                                if (($id_sensor_widget == "") || ($id_sensor_widget == ID_NINGUNO))
                                {
                                    continue;
                                }
                                $sensor_visible_usuario = false;
                                if ($sensor_visible_usuario == false)
                                {
                                    if (($permiso_todos_sensores == true) ||
                                        (dame_sensor_sensores_grupos($id_sensor_widget, $ids_sensores, $ids_grupos_sensores) == true))
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                                if ($sensor_visible_usuario == false)
                                {
                                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                    {
                                        if (in_array($id_sensor_widget, $ids_sensores_visibles_localizaciones) == true)
                                        {
                                            $sensor_visible_usuario = true;
                                        }
                                    }
                                }
                                if ($sensor_visible_usuario == false)
                                {
                                    $widget_visible_usuario = false;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // Se añade la fila de widget si es visible por el usuario
            if ($widget_visible_usuario == true)
            {
                array_push($filas_widgets_visibles_usuario, $fila_widget);
            }
        }

        // Se devuelven las filas de los widgets visibles por el usuario
        return ($filas_widgets_visibles_usuario);
    }


    // Devuelve el índice de color de fondo de un widget
    function dame_indice_color_fondo_widget(
        $valor,
        $utilizar_colores_fondo,
        $valor_limite_colores_fondo_1,
        $valor_limite_colores_fondo_2)
    {
        $sin_color_fondo = (($utilizar_colores_fondo == VALOR_NO) || ($valor === NULL));
        if ($sin_color_fondo == true)
        {
            $indice_color_fondo = ID_NINGUNO;
        }
        else
        {
            if ($valor !== NULL)
            {
                if ($valor < $valor_limite_colores_fondo_1)
                {
                    $indice_color_fondo = 0;
                }
                else
                {
                    if (($valor >= $valor_limite_colores_fondo_1) && ($valor < $valor_limite_colores_fondo_2))
                    {
                        $indice_color_fondo = 1;
                    }
                    else
                    {
                        if ($valor >= $valor_limite_colores_fondo_2)
                        {
                            $indice_color_fondo = 2;
                        }
                    }
                }
            }
        }
        return ($indice_color_fondo);
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de los widgets del usuario actual
    function dame_ids_widgets_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de widgets
        $consulta_widgets = "
            SELECT id
            FROM widgets
            WHERE
                (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
        if ($res_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_widgets."'");
        }
        $ids_widgets = array();
        while ($fila_widgets = $res_widgets->dame_siguiente_fila())
        {
            $id_widgets = $fila_widgets['id'];
            array_push($ids_widgets, $id_widgets);
        }
        return ($ids_widgets);
    }
?>
