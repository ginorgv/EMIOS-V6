<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_MEDICION", 0);
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_AGREGACION", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_COMENTARIOS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_MEDICION", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_INCLUSION_FECHAS", 6);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_HORARIO_SEMANAL", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_EXCLUSION_FECHAS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_INCLUSION_FECHAS", 4);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_GRANULARIDAD", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_HORARIO_SEMANAL", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_EXCLUSION_FECHAS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_INCLUSION_FECHAS", 4);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_HORARIO_SEMANAL", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_EXCLUSION_FECHAS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_INCLUSION_FECHAS", 3);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CORTES_TENSION_ID_SENSOR", 0);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_HORARIO_SEMANAL", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_EXCLUSION_FECHAS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_INCLUSION_FECHAS", 3);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_MEDICION", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_EXCLUSION_FECHAS", 5);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de consumos y costes generales
    function dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_generales($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $medicion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_MEDICION];
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_INTERVALO_VALORES];
        $agregacion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_AGREGACION];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Medición").": ".dame_descripcion_medicion($medicion)."</li>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Sensores").":";
        $lista_nombres_sensores = "<ul>";
        $numero_sensor = 0;
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
            $numero_sensor += 1;
            if ($numero_sensor == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_nombres_sensores .= "<li>...</li>";
                break;
            }
        }
        $lista_nombres_sensores .= "</ul>";
        $html .= $lista_nombres_sensores;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Agregación").": ".dame_descripcion_agregacion($agregacion)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de consumos y costes totales
    function dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_totales($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $medicion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_MEDICION];
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_ID_RATIO];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_INTERVALO_VALORES];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Medición").": ".dame_descripcion_medicion($medicion)."</li>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Sensores").":";
        $lista_nombres_sensores = "<ul>";
        $numero_sensor = 0;
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
            $numero_sensor += 1;
            if ($numero_sensor == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_nombres_sensores .= "<li>...</li>";
                break;
            }
        }
        $lista_nombres_sensores .= "</ul>";
        $html .= $lista_nombres_sensores;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de consumos y costes por tramo
    function dame_html_parametros_tipo_informe_automatico_smartmeter_consumos_costes_tramos($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de excesos de potencia
    function dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_potencia($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $granularidad = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_GRANULARIDAD];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Granularidad").": ".dame_descripcion_granularidad($granularidad)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de excesos de energía reactiva
    function dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_energia_reactiva($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_INCLUSION_FECHAS];

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


    // Parámetros del informe de excesos de cortes de tensión
    function dame_html_parametros_tipo_informe_automatico_smartmeter_cortes_tension($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[0];
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de excesos de excesos de caudal
    function dame_html_parametros_tipo_informe_automatico_smartmeter_excesos_caudal($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_INCLUSION_FECHAS];

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


    // Parámetros del informe de comparación de periodos
    function dame_html_parametros_tipo_informe_automatico_smartmeter_comparacion_periodos($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $medicion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_MEDICION];
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_INTERVALO_VALORES];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_EXCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Medición").": ".dame_descripcion_medicion($medicion)."</li>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
?>
