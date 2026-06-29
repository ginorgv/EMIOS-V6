<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/util_informes_facturas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_informes_facturas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/gas/Espanya/util_informes_facturas_gas_Espanya.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_RATIO", 0);
	define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_VALOR", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_AGRUPACION_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_HORARIO_SEMANAL", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_EXCLUSION_FECHAS", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR_INCLUSION_FECHAS", 9);

    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_MEDICION", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_CONCEPTO_FACTURA", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_PERIODO_TIEMPO", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_UTILIZAR_COLORES_FONDO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_COLORES_FONDO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_COSTE_FACTURA_SENSOR_ICONO", 10);


    // Devuelve los datos de un widget de tipo 'Gráfica de consumos y costes por tramo de un sensor'
    function dame_datos_widget_grafica_consumos_costes_tramos_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $valor = $parametros_tipo["valor"];
        $agrupacion_valores = $parametros_tipo["agrupacion_valores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se obtiene el nombre del sensor
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de consumos y costes por tramo
        $parametros_consumos_costes = array(
            "id_ratio" => $id_ratio,
            "id_sensor" => $id_sensor,
            "nombre_sensor" => $nombre_sensor,
            "valor" => $valor,
            "agrupacion_valores" => $agrupacion_valores,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "horario_semanal" => NULL,
            "exclusion_fechas" => NULL,
            "inclusion_fechas" => NULL,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas),
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "mostrar_tablas_tramos" => false);
        $res_consumos_costes = dame_consumos_costes_sensor_tramos_electricidad($parametros_consumos_costes);
        if ($res_consumos_costes["res"] == "OK")
        {
            // Cadenas con las horas de los valores inicial y final
            switch ($agrupacion_valores)
            {
                case AGRUPACION_VALORES_HORA:
                {
                    if (($res_consumos_costes["min_hora"] !== NULL) && ($res_consumos_costes["max_hora"] !== NULL))
                    {
                        $cadena_fecha_hora_valor_inicial_local_local = convierte_formato_fecha($res_consumos_costes["min_hora"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                        $cadena_fecha_hora_valor_final_local_local = convierte_formato_fecha($res_consumos_costes["max_hora"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                    }
                    break;
                }
                case AGRUPACION_VALORES_FECHA:
                case AGRUPACION_VALORES_DIA_SEMANA:
                {
                    if (($res_consumos_costes["min_fecha"] !== NULL) && ($res_consumos_costes["max_fecha"] !== NULL))
                    {
                        $cadena_fecha_hora_valor_inicial_local_local = convierte_formato_fecha($res_consumos_costes["min_fecha"], FORMATO_FECHA_JQPLOT, $_SESSION["formato_fecha_local"]);
                        $cadena_fecha_hora_valor_final_local_local = convierte_formato_fecha($res_consumos_costes["max_fecha"], FORMATO_FECHA_JQPLOT, $_SESSION["formato_fecha_local"]);
                    }
                    break;
                }
            }

            // Cadena de horas de valores
            $html_cadena_horas_valores = "";
            $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_inicial_local_local."),</span>";
            $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_final_local_local.")</span>";

            // Parámetros 'extra' para el dibujado del widget
            $res_consumos_costes["valor"] = $valor;
            $res_consumos_costes["agrupacion_valores"] = $agrupacion_valores;
            $res_consumos_costes["html_cadena_horas_valores"] = $html_cadena_horas_valores;
        }

        // Datos del widget
        $datos_widget_grafica_consumos_costes_tramos = $res_consumos_costes;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_consumos_costes_tramos);
    }


    // Devuelve los datos de un widget de tipo 'Coste de factura eléctrica de un sensor'
    function dame_datos_widget_coste_factura_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $medicion = $parametros_tipo["medicion"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $concepto_factura = $parametros_tipo["concepto_factura"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $icono = $parametros_tipo["icono"];

        // Se obtiene el nombre del sensor
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Se ajustan las horas de inicio y de fin a la hora actual en punto (para poder reaprovechar las facturas de varios widgets con mismos parámetros)
        // (si no la fecha de fin podía diferir en los minutos y/o segundos)
        $fecha_hora_inicio_local->setTime($fecha_hora_inicio_local->format("H"), 0, 0);
        $fecha_hora_fin_local->setTime($fecha_hora_fin_local->format("H"), 0, 0);

        // Conversión a formato local
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recupera el coste del concepto de la factura
        $parametros_coste_concepto = array(
            "id_ratio" => ID_NINGUNO,
            "id_sensor" => $id_sensor,
            "nombre_sensor" => $nombre_sensor,
            "concepto_factura" => $concepto_factura,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
        $res_coste_concepto = dame_coste_concepto_simulacion_factura_sensor($medicion, $parametros_coste_concepto);
        if ($res_coste_concepto["res"] == "OK")
        {
            // Si hay coste de concepto
            if (array_key_exists("coste_concepto_factura", $res_coste_concepto) == true)
            {
                // Coste de concepto
                $coste_concepto_factura = $res_coste_concepto["coste_concepto_factura"];
                $unidad_medida_coste = $res_coste_concepto["unidad_medida_coste"];

                // Se recupera la cadena HTML con el coste especificado
                $html_cadena_coste = dame_html_cadena_valor_unidad_widget(
                    $coste_concepto_factura,
                    $unidad_medida_coste,
                    2);

                // Colores de fondo del widget
                $indice_color_fondo = dame_indice_color_fondo_widget(
                    $coste_concepto_factura,
                    $utilizar_colores_fondo,
                    $valor_limite_colores_fondo_1,
                    $valor_limite_colores_fondo_2);

                // Cadenas de fechas
                $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $cadena_fecha_hora_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_inicio_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_fin_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $res_coste_concepto["html_cadena_coste"] = $html_cadena_coste;
                $res_coste_concepto["html_cadena_horas_valores"] = $html_cadena_horas_valores;
                $res_coste_concepto["indice_color_fondo"] = $indice_color_fondo;
                $res_coste_concepto["colores_fondo"] = $colores_fondo;
            }
        }

        // Datos del widget
        $datos_widget_coste_factura = $res_coste_concepto;

        // Icono
        $datos_widget_coste_factura["icono"] = $icono;

        // Se devuelven los datos del widget
        return ($datos_widget_coste_factura);
    }


    //
    // Funciones auxiliares para obtener los datos de los widgets
    //


    // Devuelve el código HTML con la cadena de un valor y su unidad especificada
    function dame_html_cadena_valor_unidad_widget(
        $valor,
        $unidad,
        $numero_decimales,
        $numero_columnas_fila_widget_clase_contenido_widget)
    {
        // Nota: Altura del valor y la unidad dependiente de la configuración de la cuadrícula
        $clase_tamanyo_fuente_texto_grande_widget = "tamanyo-fuente-texto-grande-widget-valor-digital-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
        $clase_css_texto_grande = "texto-grande-widget-valor-digital-sensor ".$clase_tamanyo_fuente_texto_grande_widget;
        $clase_css_texto_pequenyo = "texto-pequenyo-widget-valor-digital-sensor";

        $cadena_valor = formatea_numero($valor, 2);
        $codigo_html = "
            <div class='".$clase_css_texto_grande."'>".$cadena_valor."
                <span class='".$clase_css_texto_pequenyo."'>".$unidad."</span>
            </div>";
        return ($codigo_html);
    }


    // Devuelve el coste del concepto de una simulación de factura
    function dame_coste_concepto_simulacion_factura_sensor($medicion, $parametros_coste_concepto)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $res_coste_concepto = dame_coste_concepto_simulacion_factura_sensor_electricidad_Espanya($parametros_coste_concepto);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $res_coste_concepto = dame_coste_concepto_simulacion_factura_sensor_gas_Espanya($parametros_coste_concepto);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $res_coste_concepto = dame_coste_concepto_simulacion_factura_sensor_agua_Espanya($parametros_coste_concepto);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($res_coste_concepto);
    }
?>

