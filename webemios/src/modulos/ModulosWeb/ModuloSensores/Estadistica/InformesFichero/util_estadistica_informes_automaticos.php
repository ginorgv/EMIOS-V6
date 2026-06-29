<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_DETALLE", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CLASES_SENSORES_INDEPENDIENTES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CAMPOS_INDEPENDIENTES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CLASE_SENSOR_DEPENDIENTE", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CAMPO_DEPENDIENTE", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_FUNCION_CORRELACION", 8);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_EXCLUSION_FECHAS", 10);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_INCLUSION_FECHAS", 11);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de histograma
    function dame_html_parametros_tipo_informe_automatico_sensores_histograma($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_INTERVALO_VALORES];
        $detalle = $parametros_tipo[5];
        $cadena_detalle = "";
        switch ($detalle)
        {
            case DETALLE_MINIMO:
            {
                $cadena_detalle = $idiomas->_("Mínimo");
                break;
            }
            case DETALLE_MEDIO:
            {
                $cadena_detalle = $idiomas->_("Medio");
                break;
            }
            case DETALLE_MAXIMO:
            {
                $cadena_detalle = $idiomas->_("Máximo");
                break;
            }
            default:
            {
                $cadena_detalle = $idiomas->_("Desconocido");
                break;
            }
        }
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Detalle").": ".$cadena_detalle."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de correlación
    function dame_html_parametros_tipo_informe_automatico_sensores_correlacion($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_RATIO];
        $clases_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CLASES_SENSORES_INDEPENDIENTES]);
        $ids_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES]);
        $nombres_sensores_independientes = dame_nombres_sensores($ids_sensores_independientes);
        $campos_parametros_extra_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CAMPOS_INDEPENDIENTES]);
        $clase_sensor_dependiente = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CLASE_SENSOR_DEPENDIENTE];
        $id_sensor_dependiente = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE];
        $nombre_sensor_dependiente = dame_nombre_sensor($id_sensor_dependiente);
        $campo_parametros_extra_dependiente = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_CAMPO_DEPENDIENTE]);
        $campo_dependiente = $campo_parametros_extra_dependiente[0];
        $parametros_extra_campo_dependiente = $campo_parametros_extra_dependiente[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_INTERVALO_VALORES];
        $funcion_correlacion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_FUNCION_CORRELACION];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Sensores independientes").":";
        $lista_sensores_independientes = "<ul>";
        for ($i = 0; $i < count($clases_sensores_independientes); $i++)
        {
            $clase_sensor_independiente = $clases_sensores_independientes[$i];
            $campo_parametros_extra_independiente = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $campos_parametros_extra_independientes[$i]);
            $campo_independiente = $campo_parametros_extra_independiente[0];
            $parametros_extra_campo_independiente = $campo_parametros_extra_independiente[1];

            $lista_sensores_independientes .= "<li>".$idiomas->_("Sensor")." ".($i + 1).":";
            $lista_sensores_independientes .= "<ul>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor_independiente)."</ul>";
            $lista_sensores_independientes .= "<ul>".$idiomas->_("Nombre").": ".$nombres_sensores_independientes[$i]."</ul>";
            $lista_sensores_independientes .= "<ul>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor_independiente, $campo_independiente);
            if ($parametros_extra_campo_independiente != "")
            {
                $descripcion_parametros_extra_campo_independiente = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clases_sensores_independientes[$i], $campo_independiente));
                $lista_sensores_independientes .= " (".$descripcion_parametros_extra_campo_independiente.": ".$parametros_extra_campo_independiente.")";
            }
            $lista_sensores_independientes .= "</ul>";
            $lista_sensores_independientes .= "</li>";
        }
        $lista_sensores_independientes .= "</ul>";
        $html .= $lista_sensores_independientes;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Sensor dependiente").": ";
        $html .= "<ul>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor_dependiente)."</ul>";
        $html .= "<ul>".$idiomas->_("Sensor").": ".$nombre_sensor_dependiente."</ul>";
        $html .= "<ul>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor_dependiente, $campo_dependiente);
        if ($parametros_extra_campo_dependiente != "")
        {
            $descripcion_parametros_extra_campo_dependiente = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor_dependiente, $campo_dependiente));
            $html .= " (".$descripcion_parametros_extra_campo_dependiente.": ".$parametros_extra_campo_dependiente.")";
        }
        $html .= "</ul>";
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Función de correlación").": ".dame_descripcion_funcion_correlacion($funcion_correlacion)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
