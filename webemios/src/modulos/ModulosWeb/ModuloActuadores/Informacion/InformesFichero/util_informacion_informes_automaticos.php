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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CLASE_ACTUADOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ORIGEN_ACCIONES", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CLASE_SENSOR", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CAMPO", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_COMENTARIOS", 8);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_EXCLUSION_FECHAS", 10);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_INCLUSION_FECHAS", 11);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de acciones enviadas
    function dame_html_parametros_tipo_informe_automatico_actuadores_informacion_acciones_enviadas($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CLASE_ACTUADOR];
        $destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
        $id_destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $nombre_destino_accion = dame_nombre_actuador($id_destino_accion);
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $nombre_destino_accion = dame_nombre_grupo_actuadores($id_destino_accion);
                break;
            }
        }
        $origen_acciones = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ORIGEN_ACCIONES];
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CLASE_SENSOR];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR];
        if ($id_sensor != ID_NINGUNO)
        {
            $nombre_sensor = dame_nombre_sensor($id_sensor);
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_CAMPO]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];
            $intervalo_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_INTERVALO_VALORES];
        }
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_COMENTARIOS];
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_INCLUSION_FECHAS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Clase de actuador").": ".NodoActuador::dame_descripcion_clase_actuador($clase_actuador)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de destino").": ".dame_descripcion_destino_accion($destino_accion)."</li>";
        $html .= "<li>".$idiomas->_("Destino").": ".$nombre_destino_accion."</li>";
        $html .= "<li>".$idiomas->_("Origen").": ".dame_descripcion_origen_acciones($origen_acciones)."</li>";
        if ($id_sensor != ID_NINGUNO)
        {
            $html .= "<li>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
            $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
            $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
                $html .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
            }
            $html .= "</li>";
            $html .= "<li>".$idiomas->_("Intervalo de valores de sensor").": ".dame_descripcion_intervalo_valores($intervalo_valores)."</li>";
        }
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</ul>";

        return ($html);
    }
?>
