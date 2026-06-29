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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_TIPO_MAPA_CALOR", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_EXCLUSION_FECHAS", 7);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_CLASE_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_INTERVALO_VALORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_FECHA_INICIO_PERFIL_HORARIO", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_FECHA_FIN_PERFIL_HORARIO", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_PERFIL_HORARIO", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_AGRUPACIONES_DIAS_SEMANA", 8);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_EXCLUSION_FECHAS", 10);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_MAPA_CALOR", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_CLASES_SENSORES", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_CAMPOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_UNIFICAR_ESCALAS", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_INTERVALO_VALORES", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_TIPO_MAPA_CALOR", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_HORARIO_SEMANAL", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_EXCLUSION_FECHAS", 8);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_INCLUSION_FECHAS", 9);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_CLASES_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_CAMPOS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_AGREGACION", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_INCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_CLASES_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_CAMPOS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_INTERVALO_VALORES", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_AGREGACION", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_HORARIO_SEMANAL", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_EXCLUSION_FECHAS", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_INCLUSION_FECHAS", 8);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de comparación de periodos
    function dame_html_parametros_tipo_informe_automatico_sensores_comparacion_periodos($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_TIPO_MAPA_CALOR];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_EXCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo)."</li>";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de comparación con perfil horario
    function dame_html_parametros_tipo_informe_automatico_sensores_comparacion_perfil_horario($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_MAPA_CALOR];
        $cadena_fecha_inicio_perfil_horario = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_FECHA_INICIO_PERFIL_HORARIO];
        $cadena_fecha_fin_perfil_horario = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_FECHA_FIN_PERFIL_HORARIO];
        $tipo_perfil_horario = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_PERFIL_HORARIO];
        $cadena_agrupaciones_dias_semana = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_AGRUPACIONES_DIAS_SEMANA];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_EXCLUSION_FECHAS];

        $cadena_fecha_inicio_perfil_horario_local = convierte_formato_fecha($cadena_fecha_inicio_perfil_horario, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_perfil_horario_local = convierte_formato_fecha($cadena_fecha_fin_perfil_horario, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo)."</li>";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= "<li>".$idiomas->_("Fecha de inicio de perfil horario").": ".$cadena_fecha_inicio_perfil_horario_local."</li>";
        $html .= "<li>".$idiomas->_("Fecha de fin de perfil horario").": ".$cadena_fecha_fin_perfil_horario_local."</li>";
        $html .= "<li>".$idiomas->_("Tipo de perfil horario").": ".dame_descripcion_tipo_perfil_horario($tipo_perfil_horario)."</li>";
        $html .= dame_html_parametro_agrupaciones_dias_semana_informe_automatico($idiomas->_("Agrupaciones de días de la semana"), $cadena_agrupaciones_dias_semana);
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de comparación de campos iguales
    function dame_html_parametros_tipo_informe_automatico_sensores_comparacion_campos_iguales($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_CLASE_SENSOR];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES]);
        $nombre_sensores = dame_nombres_sensores($ids_sensores);
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_MAPA_CALOR];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Sensor principal").": ".$nombre_sensores[0]."</li>";
        $html .= "<li>".$idiomas->_("Sensores secundarios").":";
        $lista_nombres_sensores_secundarios = "<ul>";
        $nombres_sensor_principal = true;
        foreach ($nombre_sensores AS $nombre_sensor)
        {
            // Se descarta el primer valor, que se corresponde con el del sensor principal
            if ($nombres_sensor_principal == true)
            {
                $nombres_sensor_principal = false;
            }
            else
            {
                $lista_nombres_sensores_secundarios .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
            }
        }
        $lista_nombres_sensores_secundarios .= "</ul>";
        $html .= $lista_nombres_sensores_secundarios;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo)."</li>";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de comparación de campos diferentes
    function dame_html_parametros_tipo_informe_automatico_sensores_comparacion_campos_diferentes($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO];
        $clases_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_CLASES_SENSORES]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_CAMPOS]);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_INTERVALO_VALORES];
        $unificar_escalas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_UNIFICAR_ESCALAS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Sensores").":";
        $lista_sensores = "<ul>";
        for ($i = 0; $i < count($clases_sensores); $i++)
        {
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $campos_parametros_extra[$i]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];

            $lista_sensores .= "<li>".$idiomas->_("Sensor")." ".($i + 1).":";
            $lista_sensores .= "<ul>".$idiomas->_("Clase").": ".NodoSensor::dame_descripcion_clase_sensor($clases_sensores[$i])."</ul>";
            $lista_sensores .= "<ul>".$idiomas->_("Nombre").": ".$nombres_sensores[$i]."</ul>";
            $lista_sensores .= "<ul>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clases_sensores[$i], $campo);
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clases_sensores[$i], $campo));
                $lista_sensores .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
            }
            $lista_sensores .= "</ul>";
            $lista_sensores .= "</li>";
        }
        $lista_sensores .= "</ul>";
        $html .= $lista_sensores;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Unificar escalas").": ".dame_descripcion_valores_si_no($unificar_escalas)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de análisis comparativo
    function dame_html_parametros_tipo_informe_automatico_sensores_analisis_comparativo($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_CLASE_SENSOR];
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_CAMPO]);
        $campo = $campo_parametros_extra[0];
        $parametros_extra_campo = $campo_parametros_extra[1];
        $ids_sensores_agregados = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS]);
        $nombres_sensores_agregados = dame_nombres_sensores($ids_sensores_agregados);
        $id_sensor_destacado = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO];
        $nombre_sensor_destacado = dame_nombre_sensor($id_sensor_destacado);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_INTERVALO_VALORES];
        $tipo_mapa_calor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_TIPO_MAPA_CALOR];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
            $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
        }
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Sensores agregados").":";
        $lista_nombres_sensores_agregados = "<ul>";
        foreach ($nombres_sensores_agregados AS $nombre_sensor_agregado)
        {
            $lista_nombres_sensores_agregados .= "<li>".htmlspecialchars($nombre_sensor_agregado, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_agregados .= "</ul>";
        $html .= $lista_nombres_sensores_agregados;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Sensor destacado").": ".$nombre_sensor_destacado."</li>";
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de mapa de calor").": ".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de valores generales
    function dame_html_parametros_tipo_informe_automatico_sensores_valores_generales($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_ID_RATIO];
        $clases_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_CLASES_SENSOR]);
        $campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_CAMPOS]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_INTERVALO_VALORES];
        $agregacion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_AGREGACION];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Clases de sensor").":";
        $lista_clases_sensor = "<ul>";
        $numero_clase_sensor = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];

            $lista_clases_sensor .= "<li>".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
            $numero_clase_sensor += 1;
            if ($numero_clase_sensor == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_clases_sensor .= "<li>...</li>";
                break;
            }
        }
        $lista_clases_sensor .= "</ul>";
        $html .= $lista_clases_sensor;
        $html .= "<li>".$idiomas->_("Campos").":";
        $lista_campos = "<ul>";
        $numero_campo = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $campos_parametros_extra[$i]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];

            $lista_campos .= "<li>".dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
                $lista_campos .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
            }
            $lista_campos .= "</li>";
            $numero_campo += 1;
            if ($numero_campo == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_campos .= "<li>...</li>";
                break;
            }
        }
        $lista_campos .= "</ul>";
        $html .= $lista_campos;
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
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }


    // Parámetros del informe de incrementos totales
    function dame_html_parametros_tipo_informe_automatico_sensores_incrementos_totales($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_ID_RATIO];
        $clases_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_CLASES_SENSOR]);
        $campos_parametros_extra = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_CAMPOS]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_INTERVALO_VALORES];
        $agregacion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_AGREGACION];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_INCLUSION_FECHAS];

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
        $html .= "<li>".$idiomas->_("Clases de sensor").":";
        $lista_clases_sensor = "<ul>";
        $numero_clase_sensor = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];

            $lista_clases_sensor .= "<li>".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
            $numero_clase_sensor += 1;
            if ($numero_clase_sensor == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_clases_sensor .= "<li>...</li>";
                break;
            }
        }
        $lista_clases_sensor .= "</ul>";
        $html .= $lista_clases_sensor;
        $html .= "<li>".$idiomas->_("Campos").":";
        $lista_campos = "<ul>";
        $numero_campo = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $campos_parametros_extra[$i]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];

            $lista_campos .= "<li>".dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
                $lista_campos .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
            }
            $lista_campos .= "</li>";
            $numero_campo += 1;
            if ($numero_campo == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
            {
                $lista_campos .= "<li>...</li>";
                break;
            }
        }
        $lista_campos .= "</ul>";
        $html .= $lista_campos;
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
        $html .= "<li>".$idiomas->_("Intervalo de valores").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        $html .= "<li>".$idiomas->_("Agregación").": ".dame_descripcion_agregacion($agregacion)."</li>";
        $html .= "</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
?>
