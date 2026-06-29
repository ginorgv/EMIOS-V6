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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/util_comparacion_informes_fichero.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_sensores_comparacion($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $titulo_informe = "Informe de comparación de valores de periodos";
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $titulo_informe = "Informe de comparación de valores con perfil horario";
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $titulo_informe = "Informe de comparación de valores de campos iguales";
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $titulo_informe = "Informe de comparación de valores de campos diferentes";
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $titulo_informe = "Informe de análisis comparativo";
                break;
            }
            case TIPO_INFORME_SENSORES_VALORES_GENERALES:
            {
                $titulo_informe = "Informe de valores generales";
                break;
            }
            case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $titulo_informe = "Informe de incrementos totales";
                break;
            }
            default:
            {
                $titulo_informe = "Informe desconocido";
                break;
            }
        }
        $html = dame_html_cabecera_informe_fichero($titulo_informe, true);
        return ($html);
    }


    // Parámetros del fichero del informe de comparación de periodos
    function dame_html_parametros_tipo_informe_fichero_sensores_comparacion_periodos($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $fecha_inicio_periodo_anterior = $parametros_informe["fecha_inicio_periodo_anterior"];
        $fecha_inicio_periodo_posterior = $parametros_informe["fecha_inicio_periodo_posterior"];
        $numero_dias_periodo = $parametros_informe["numero_dias_periodo"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor));
        $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos_clase) > 1)
        {
            $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
        }
        $html .= "]";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio del periodo anterior").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_periodo_anterior."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio del periodo posterior").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_periodo_posterior."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Duración del periodo")." (".$idiomas->_("días").")".":</b></td><td class='contenido-parametro-informe-fichero'>".$numero_dias_periodo."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_comparacion_periodos' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_comparacion_periodos' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_comparacion_periodos' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_comparacion_periodos' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_comparacion_periodos' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_comparacion_periodos' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_periodo_anterior_sensores_informe_fichero_comparacion_periodos' hidden>".$fecha_inicio_periodo_anterior."</div>";
        $html .= "<div id='fecha_inicio_periodo_posterior_sensores_informe_fichero_comparacion_periodos' hidden>".$fecha_inicio_periodo_posterior."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_comparacion_periodos' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='numero_dias_periodo_sensores_informe_fichero_comparacion_periodos' hidden>".$numero_dias_periodo."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_comparacion_periodos' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_comparacion_periodos' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_comparacion_periodos' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de comparación con perfil horario
    function dame_html_parametros_tipo_informe_fichero_sensores_comparacion_perfil_horario($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $clase_sensor = $parametros_informe["clase_sensor"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $fecha_inicio_perfil_horario = $parametros_informe["fecha_inicio_perfil_horario"];
        $fecha_fin_perfil_horario = $parametros_informe["fecha_fin_perfil_horario"];
        $tipo_perfil_horario = $parametros_informe["tipo_perfil_horario"];
        $cadena_agrupaciones_dias_semana = $parametros_informe["agrupaciones_dias_semana"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor));
        $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos_clase) > 1)
        {
            $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
        }
        $html .= "]";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_perfil_horario."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin_perfil_horario."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_perfil_horario($tipo_perfil_horario)."</td></tr>";
        $html .= dame_html_parametro_agrupaciones_dias_semana_informe_fichero($idiomas->_("Agrupaciones de días de la semana"), $cadena_agrupaciones_dias_semana);
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='clase_sensor_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_comparacion_perfil_horario' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='fecha_inicio_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$fecha_inicio_perfil_horario."</div>";
        $html .= "<div id='fecha_fin_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$fecha_fin_perfil_horario."</div>";
        $html .= "<div id='tipo_perfil_horario_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$tipo_perfil_horario."</div>";
        $html .= "<div id='agrupaciones_dias_semana_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$cadena_agrupaciones_dias_semana."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_comparacion_perfil_horario' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de comparación de campos iguales
    function dame_html_parametros_tipo_informe_fichero_sensores_comparacion_campos_iguales($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor principal").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombres_sensores[0], ENT_QUOTES);
        $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor));
        $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos_clase) > 1)
        {
            $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
        }
        $html .= "]";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $numero_sensor_secundario = 0;
        $nombres_sensores_secundarios = array_slice($nombres_sensores, 1, count($nombres_sensores));
        foreach ($nombres_sensores_secundarios as $nombre_sensor_secundario)
        {
            $texto_sensor = "";
            if ($numero_sensor_secundario == 0)
            {
                if (count($nombres_sensores_secundarios) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor secundario").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores secundarios").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor_secundario, ENT_QUOTES)."</td></tr>";
            $numero_sensor_secundario++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$clase_sensor."</div>";
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_sensores_informe_fichero_comparacion_campos_iguales' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_sensores_informe_fichero_comparacion_campos_iguales' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='campo_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_comparacion_campos_iguales' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de comparación de campos diferentes
    function dame_html_parametros_tipo_informe_fichero_sensores_comparacion_campos_diferentes($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clases_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["clases_sensores"]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["campos"]);
        $parametros_extra_campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["parametros_extra_campos"]);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $unificar_escalas = $parametros_informe["unificar_escalas"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $numero_sensor = 0;
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $texto_sensor = "";
            if ($numero_sensor == 0)
            {
                if (count($nombres_sensores) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor, ENT_QUOTES);
            $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clases_sensores[$numero_sensor]));
            $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clases_sensores[$numero_sensor]);
            if (count($campos_clase) > 1)
            {
                $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clases_sensores[$numero_sensor], $campos[$numero_sensor]));
            }
            $html .= "]";
            if ($parametros_extra_campos[$numero_sensor] != "")
            {
                $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clases_sensores[$numero_sensor], $campos[$numero_sensor]);
                $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campos[$numero_sensor].")";
            }
            $html .= "</td></tr>";
            $numero_sensor++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$id_ratio."</div>";
        $lista_clases_sensores_oculta = "<ul id='clases_sensores_sensores_informe_fichero_comparacion_campos_diferentes' hidden>";
        foreach ($clases_sensores AS $clase_sensor)
        {
            $lista_clases_sensores_oculta .= "<li>".$clase_sensor."</li>";
        }
        $lista_clases_sensores_oculta .= "</ul>";
        $html .= $lista_clases_sensores_oculta;
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_sensores_informe_fichero_comparacion_campos_diferentes' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_sensores_informe_fichero_comparacion_campos_diferentes' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $lista_campos_oculta = "<ul id='campos_informe_fichero_comparacion_campos_diferentes' hidden>";
        foreach ($campos AS $campo)
        {
            $lista_campos_oculta .= "<li>".$campo."</li>";
        }
        $lista_campos_oculta .= "</ul>";
        $html .= $lista_campos_oculta;
        $lista_parametros_extra_campos_oculta = "<ul id='parametros_extra_campos_informe_fichero_comparacion_campos_diferentes' hidden>";
        foreach ($parametros_extra_campos AS $parametros_extra_campo)
        {
            $lista_parametros_extra_campos_oculta .= "<li>".$parametros_extra_campo."</li>";
        }
        $lista_parametros_extra_campos_oculta .= "</ul>";
        $html .= $lista_parametros_extra_campos_oculta;
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='unificar_escalas_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$unificar_escalas."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_comparacion_campos_diferentes' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de análisis comparativo
    function dame_html_parametros_tipo_informe_fichero_sensores_analisis_comparativo($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $ids_sensores_agregados = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores_agregados"]);
        $nombres_sensores_agregados = dame_nombres_sensores($ids_sensores_agregados);
        $id_sensor_destacado = $parametros_informe["id_sensor_destacado"];
        $nombre_sensor_destacado = dame_nombre_sensor($id_sensor_destacado);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Campo").": "."</b></td><td class='contenido-parametro-informe-fichero'>".NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
        $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
        if (count($campos_clase) > 1)
        {
            $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
        }
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $html .= "</td></tr>";
        $numero_sensor_agregado = 0;
        foreach ($nombres_sensores_agregados as $nombre_sensor_agregado)
        {
            $texto_sensor = "";
            if ($numero_sensor_agregado == 0)
            {
                if (count($nombres_sensores_agregados) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor agregado").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores agregados").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor_agregado, ENT_QUOTES)."</td></tr>";
            $numero_sensor_agregado++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor destacado").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor_destacado, ENT_QUOTES);
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_analisis_comparativo' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_analisis_comparativo' hidden>".$clase_sensor."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_analisis_comparativo' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_analisis_comparativo' hidden>".$parametros_extra_campo."</div>";
        $lista_ids_sensores_agregados_oculta = "<ul id='ids_sensores_agregados_sensores_informe_fichero_analisis_comparativo' hidden>";
        foreach ($ids_sensores_agregados AS $id_sensor_agregado)
        {
            $lista_ids_sensores_agregados_oculta .= "<li>".$id_sensor_agregado."</li>";
        }
        $lista_ids_sensores_agregados_oculta .= "</ul>";
        $html .= $lista_ids_sensores_agregados_oculta;
        $lista_nombres_sensores_agregados_oculta = "<ul id='nombres_sensores_agregados_sensores_informe_fichero_analisis_comparativo' hidden>";
        foreach ($nombres_sensores_agregados AS $nombre_sensor_agregado)
        {
            $lista_nombres_sensores_agregados_oculta .= "<li>".htmlspecialchars($nombre_sensor_agregado, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_agregados_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_agregados_oculta;
        $html .= "<div id='id_sensor_destacado_sensores_informe_fichero_analisis_comparativo' hidden>".$id_sensor_destacado."</div>";
        $html .= "<div id='nombre_sensor_destacado_sensores_informe_fichero_analisis_comparativo' hidden>".htmlspecialchars($nombre_sensor_destacado, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_analisis_comparativo' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_analisis_comparativo' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_analisis_comparativo' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_analisis_comparativo' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_analisis_comparativo' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_analisis_comparativo' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_analisis_comparativo' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_analisis_comparativo' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_analisis_comparativo' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de valores generales
    function dame_html_parametros_tipo_informe_fichero_sensores_valores_generales($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clases_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["clases_sensor"]);
        $campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["campos"]);
        $parametros_extra_campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["parametros_extra_campos"]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $agregacion = $parametros_informe["agregacion"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $numero_campo = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];
            $campo = $campos[$i];
            $parametros_extra_campo = $parametros_extra_campos[$i];
            $texto_campo = "";
            if ($numero_campo == 0)
            {
                if (count($clases_sensor) == 1)
                {
                    $texto_campo = $idiomas->_("Campo").":";
                }
                else
                {
                    $texto_campo = $idiomas->_("Campos").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_campo."</b></td><td class='contenido-parametro-informe-fichero'>• ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
            if (count($campos_clase) > 1)
            {
                $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
            }
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
                $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
            }
            $html .= "</td></tr>";
            $numero_campo++;
        }
        $numero_sensor = 0;
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $texto_sensor = "";
            if ($numero_sensor == 0)
            {
                if (count($nombres_sensores) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
            $numero_sensor++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Agregación").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_agregacion($agregacion)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_valores_generales' hidden>".$id_ratio."</div>";
        $lista_clases_sensor_oculta = "<ul id='clases_sensor_sensores_informe_fichero_valores_generales' hidden>";
        foreach ($clases_sensor AS $clase_sensor)
        {
            $lista_clases_sensor_oculta .= "<li>".$clase_sensor."</li>";
        }
        $lista_clases_sensor_oculta .= "</ul>";
        $html .= $lista_clases_sensor_oculta;
        $lista_campos_oculta = "<ul id='campos_sensores_informe_fichero_valores_generales' hidden>";
        foreach ($campos AS $campo)
        {
            $lista_campos_oculta .= "<li>".$campo."</li>";
        }
        $lista_campos_oculta .= "</ul>";
        $html .= $lista_campos_oculta;
        $lista_parametros_extra_campos_oculta = "<ul id='parametros_extra_campos_sensores_informe_fichero_valores_generales' hidden>";
        foreach ($parametros_extra_campos AS $parametros_extra_campo)
        {
            $lista_parametros_extra_campos_oculta .= "<li>".$parametros_extra_campo."</li>";
        }
        $lista_parametros_extra_campos_oculta .= "</ul>";
        $html .= $lista_parametros_extra_campos_oculta;
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_sensores_informe_fichero_valores_generales' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_sensores_informe_fichero_valores_generales' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_valores_generales' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_valores_generales' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_valores_generales' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_valores_generales' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_valores_generales' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='agregacion_sensores_informe_fichero_valores_generales' hidden>".$agregacion."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_valores_generales' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_valores_generales' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_valores_generales' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de incrementos totales
    function dame_html_parametros_tipo_informe_fichero_sensores_incrementos_totales($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clases_sensor = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["clases_sensor"]);
        $campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["campos"]);
        $parametros_extra_campos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["parametros_extra_campos"]);
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $agregacion = $parametros_informe["agregacion"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $numero_campo = 0;
        for ($i = 0; $i < count($clases_sensor); $i++)
        {
            $clase_sensor = $clases_sensor[$i];
            $campo = $campos[$i];
            $parametros_extra_campo = $parametros_extra_campos[$i];
            $texto_campo = "";
            if ($numero_campo == 0)
            {
                if (count($clases_sensor) == 1)
                {
                    $texto_campo = $idiomas->_("Campo").":";
                }
                else
                {
                    $texto_campo = $idiomas->_("Campos").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_campo."</b></td><td class='contenido-parametro-informe-fichero'>• ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
            if (count($campos_clase) > 1)
            {
                $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo));
            }
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
                $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
            }
            $html .= "</td></tr>";
            $numero_campo++;
        }
        $numero_sensor = 0;
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $texto_sensor = "";
            if ($numero_sensor == 0)
            {
                if (count($nombres_sensores) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
            $numero_sensor++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Agregación").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_agregacion($agregacion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_incrementos_totales' hidden>".$id_ratio."</div>";
        $lista_clases_sensor_oculta = "<ul id='clases_sensor_sensores_informe_fichero_incrementos_totales' hidden>";
        foreach ($clases_sensor AS $clase_sensor)
        {
            $lista_clases_sensor_oculta .= "<li>".$clase_sensor."</li>";
        }
        $lista_clases_sensor_oculta .= "</ul>";
        $html .= $lista_clases_sensor_oculta;
        $lista_campos_oculta = "<ul id='campos_sensores_informe_fichero_incrementos_totales' hidden>";
        foreach ($campos AS $campo)
        {
            $lista_campos_oculta .= "<li>".$campo."</li>";
        }
        $lista_campos_oculta .= "</ul>";
        $html .= $lista_campos_oculta;
        $lista_parametros_extra_campos_oculta = "<ul id='parametros_extra_campos_sensores_informe_fichero_incrementos_totales' hidden>";
        foreach ($parametros_extra_campos AS $parametros_extra_campo)
        {
            $lista_parametros_extra_campos_oculta .= "<li>".$parametros_extra_campo."</li>";
        }
        $lista_parametros_extra_campos_oculta .= "</ul>";
        $html .= $lista_parametros_extra_campos_oculta;
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_sensores_informe_fichero_incrementos_totales' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_sensores_informe_fichero_incrementos_totales' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_incrementos_totales' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_incrementos_totales' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_incrementos_totales' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_incrementos_totales' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_incrementos_totales' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='agregacion_sensores_informe_fichero_incrementos_totales' hidden>".$agregacion."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_incrementos_totales' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_incrementos_totales' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_incrementos_totales' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
