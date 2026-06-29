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


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_sensores_analisis($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $titulo_informe = "Informe de análisis de comportamiento";
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $titulo_informe = "Informe de análisis diario";
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $titulo_informe = "Informe de análisis horario";
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


    // Parámetros del fichero del informe de análisis horario
    function dame_html_parametros_tipo_informe_fichero_sensores_analisis_horario($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_analisis_horario' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_analisis_horario' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_analisis_horario' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_analisis_horario' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_analisis_horario' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_analisis_horario' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_analisis_horario' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_analisis_horario' hidden>".$fecha_fin."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_analisis_horario' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_analisis_horario' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_analisis_horario' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_analisis_horario' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de análisis diario
    function dame_html_parametros_tipo_informe_fichero_sensores_analisis_diario($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_analisis_diario' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_analisis_diario' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_analisis_diario' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_analisis_diario' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_analisis_diario' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_analisis_diario' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_analisis_diario' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_analisis_diario' hidden>".$fecha_fin."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_analisis_diario' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_analisis_diario' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_analisis_diario' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_analisis_diario' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de análisis de comportamiento
    function dame_html_parametros_tipo_informe_fichero_sensores_analisis_comportamiento($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $campo = $parametros_informe["campo"];
        $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
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
            $html .= " - ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
        }
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $html .= "</td></tr>";
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_analisis_comportamiento' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_analisis_comportamiento' hidden>".$clase_sensor."</div>";
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_sensores_informe_fichero_analisis_comportamiento' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_sensores_informe_fichero_analisis_comportamiento' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='campo_sensores_informe_fichero_analisis_comportamiento' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_analisis_comportamiento' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_analisis_comportamiento' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_analisis_comportamiento' hidden>".$fecha_fin."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_analisis_comportamiento' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_analisis_comportamiento' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_analisis_comportamiento' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
