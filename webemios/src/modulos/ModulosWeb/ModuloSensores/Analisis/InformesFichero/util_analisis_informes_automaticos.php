<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_HORARIO_SEMANAL", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_EXCLUSION_FECHAS", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_INCLUSION_FECHAS", 7);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_HORARIO_SEMANAL", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_EXCLUSION_FECHAS", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_INCLUSION_FECHAS", 7);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_INCLUSION_FECHAS", 6);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de análisis horario
    function dame_html_parametros_tipo_informe_automatico_sensores_analisis_horario($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_TIPO_MAPA_CALOR];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de análisis diario
    function dame_html_parametros_tipo_informe_automatico_sensores_analisis_diario($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_TIPO_MAPA_CALOR];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de análisis de comportamiento
    function dame_html_parametros_tipo_informe_automatico_sensores_analisis_comportamiento($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_CLASE_SENSOR];
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Sensores").":";
        $lista_nombres_sensores = "<ul>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores .= "</ul>";
        $html .= $lista_nombres_sensores;
        $html .= "</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }

