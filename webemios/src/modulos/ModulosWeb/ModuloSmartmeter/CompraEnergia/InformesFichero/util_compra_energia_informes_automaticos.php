<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_HORARIO_SEMANAL", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_EXCLUSION_FECHAS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_INCLUSION_FECHAS", 3);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR_HIJO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_HORARIO_SEMANAL", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_EXCLUSION_FECHAS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_INCLUSION_FECHAS", 4);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de desvíos de compra de energía
    function dame_html_parametros_tipo_informe_automatico_smartmeter_desvios_compra_energia($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de desvíos ponderados de compra de energía
    function dame_html_parametros_tipo_informe_automatico_smartmeter_desvios_ponderados_compra_energia($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_sensor_hijo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR_HIJO];
        $nombre_sensor_hijo = dame_nombre_sensor($id_sensor_hijo);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Sensor hijo").": ".$nombre_sensor_hijo."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
