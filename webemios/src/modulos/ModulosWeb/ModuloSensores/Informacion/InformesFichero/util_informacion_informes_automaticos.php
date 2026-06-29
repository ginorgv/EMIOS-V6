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
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_CAMPO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_INTERVALO_VALORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_TIPO_MAPA_CALOR", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_COMENTARIOS", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_HORARIO_SEMANAL", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_EXCLUSION_FECHAS", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_INCLUSION_FECHAS", 7);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_INTERVALO_VALORES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_TIPO_MAPA_CALOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_COMENTARIOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_INCLUSION_FECHAS", 6);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_INTERVALO_VALORES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_TIPO_MAPA_CALOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_COMENTARIOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_INCLUSION_FECHAS", 6);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_INTERVALO_VALORES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_TIPO_MAPA_CALOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_COMENTARIOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_INCLUSION_FECHAS", 6);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_COMENTARIOS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_INTERVALO_VALORES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_TIPO_MAPA_CALOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_COMENTARIOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_HORARIO_SEMANAL", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_EXCLUSION_FECHAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_INCLUSION_FECHAS", 6);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_ID_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_CAMPO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_INTERVALO_VALORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_TIPO_MAPA_CALOR", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_COMENTARIOS", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_HORARIO_SEMANAL", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_EXCLUSION_FECHAS", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_INCLUSION_FECHAS", 7);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_COMENTARIOS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_COMENTARIOS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_COMENTARIOS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_INCLUSION_FECHAS", 8);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de temperatura
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_temperatura($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de humedad
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_humedad($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de luz interior
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_luz_interior($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de viento
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_viento($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_VIENTO_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de energía
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_energia($tipo, $cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_CAMPO];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_INCLUSION_FECHAS];

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
        switch ($tipo)
        {
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_REACTIVA;
                break;
            }
        }
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo)."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de cortes de tensión
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_cortes_tension($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de compra de energía
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_compra_energia($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_CAMPO];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, $campo)."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de gas
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_gas($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_CAMPO];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, $campo)."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de agua
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_agua($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_CAMPO];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, $campo)."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de información genérica
    function dame_html_parametros_tipo_informe_automatico_sensores_informacion_generica($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_CAMPO];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_TIPO_MAPA_CALOR];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, $campo)."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
?>
