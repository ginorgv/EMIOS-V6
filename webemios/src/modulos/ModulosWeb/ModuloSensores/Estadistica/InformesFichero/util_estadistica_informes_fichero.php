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
    function dame_html_cabecera_informe_fichero_sensores_estadistica($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_HISTOGRAMA:
            {
                $titulo_informe = "Informe de histograma de valores";
                break;
            }
            case TIPO_INFORME_SENSORES_CORRELACION:
            {
                $titulo_informe = "Informe de correlación de valores";
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


    // Parámetros del fichero del informe de histograma
    function dame_html_parametros_tipo_informe_fichero_sensores_histograma($parametros_informe)
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
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $detalle = $parametros_informe["detalle"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)." [".NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Detalle").":</b></td><td class='contenido-parametro-informe-fichero'>".$cadena_detalle."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_histograma' hidden>".$id_ratio."</div>";
        $html .= "<div id='clase_sensor_sensores_informe_fichero_histograma' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_histograma' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_histograma' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_histograma' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_histograma' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_histograma' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_histograma' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_histograma' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_histograma' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='detalle_sensores_informe_fichero_histograma' hidden>".$detalle."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_histograma' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_histograma' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_histograma' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de correlación
    function dame_html_parametros_tipo_informe_fichero_sensores_correlacion($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $clases_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["clases_sensores_independientes"]);
        $ids_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_sensores_independientes"]);
        $nombres_sensores_independientes = dame_nombres_sensores($ids_sensores_independientes);
        $campos_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["campos_independientes"]);
        $parametros_extra_campos_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["parametros_extra_campos_independientes"]);
        $clase_sensor_dependiente = $parametros_informe["clase_sensor_dependiente"];
        $id_sensor_dependiente = $parametros_informe["id_sensor_dependiente"];
        $nombre_sensor_dependiente = dame_nombre_sensor($id_sensor_dependiente);
        $campo_dependiente = $parametros_informe["campo_dependiente"];
        $parametros_extra_campo_dependiente = $parametros_informe["parametros_extra_campo_dependiente"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $funcion_correlacion = $parametros_informe["funcion_correlacion"];
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
        $primer_sensor_independiente = true;
        $numero_sensor_independiente = 0;
        foreach ($nombres_sensores_independientes as $nombre_sensor_independiente)
        {
            $texto_sensor = "";
            if ($primer_sensor_independiente == true)
            {
                $texto_sensor = $idiomas->_("Sensores independientes").":";
                $primer_sensor_independiente = false;
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor_independiente, ENT_QUOTES);
            $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clases_sensores_independientes[$numero_sensor_independiente]));
            $campos_clase_independiente = dame_todos_campos_clase_sensor_parametros_extra($clases_sensores_independientes[$numero_sensor_independiente]);
            if (count($campos_clase_independiente) > 1)
            {
                $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor(
                    $clases_sensores_independientes[$numero_sensor_independiente],
                    $campos_independientes[$numero_sensor_independiente]));
            }
            $html .= "]";
            if ($parametros_extra_campos_independientes[$numero_sensor_independiente] != "")
            {
                $descripcion_parametros_extra_campo_independiente = dame_descripcion_parametros_extra_campo_clase_sensor($clases_sensores_independientes[$numero_sensor_independiente], $campos_independientes[$numero_sensor_independiente]);
                $html .= " (".strtolower($descripcion_parametros_extra_campo_independiente).": ".$parametros_extra_campos_independientes[$numero_sensor_independiente].")";
            }
            $html .= "</td></tr>";
            $numero_sensor_independiente++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor dependiente").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor_dependiente, ENT_QUOTES);
        $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor_dependiente));
        $campos_clase_dependiente = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor_dependiente);
        if (count($campos_clase_dependiente) > 1)
        {
            $html .= " - ".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor_dependiente, $campo_dependiente));
        }
        $html .= "]";
        if ($parametros_extra_campo_dependiente != "")
        {
            $descripcion_parametros_extra_campo_dependiente = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor_dependiente, $campo_dependiente);
            $html .= " (".strtolower($descripcion_parametros_extra_campo_dependiente).": ".$parametros_extra_campo_dependiente.")";
        }
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        if ($funcion_correlacion == "")
        {
            $descripcion_funcion_correlacion = dame_descripcion_funcion_correlacion(FUNCION_CORRELACION_AUTOMATICA);
        }
        else
        {
            $descripcion_funcion_correlacion = dame_descripcion_funcion_correlacion($funcion_correlacion);
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Función de correlación").":</b></td><td class='contenido-parametro-informe-fichero'>".$descripcion_funcion_correlacion."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_correlacion' hidden>".$id_ratio."</div>";
        $lista_clases_independientes_oculta = "<ul id='clases_sensores_independientes_sensores_informe_fichero_correlacion' hidden>";
        foreach ($clases_sensores_independientes AS $clase_sensor_independiente)
        {
            $lista_clases_independientes_oculta .= "<li>".$clase_sensor_independiente."</li>";
        }
        $lista_clases_independientes_oculta .= "</ul>";
        $html .= $lista_clases_independientes_oculta;
        $lista_ids_sensores_independientes_oculta = "<ul id='ids_sensores_independientes_sensores_informe_fichero_correlacion' hidden>";
        foreach ($ids_sensores_independientes AS $id_sensor_independiente)
        {
            $lista_ids_sensores_independientes_oculta .= "<li>".$id_sensor_independiente."</li>";
        }
        $lista_ids_sensores_independientes_oculta .= "</ul>";
        $html .= $lista_ids_sensores_independientes_oculta;
        $lista_nombres_sensores_independientes_oculta = "<ul id='nombres_sensores_independientes_sensores_informe_fichero_correlacion' hidden>";
        foreach ($nombres_sensores_independientes AS $nombre_sensor_independiente)
        {
            $lista_nombres_sensores_independientes_oculta .= "<li>".htmlspecialchars($nombre_sensor_independiente, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_independientes_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_independientes_oculta;
        $lista_campos_independientes_oculta = "<ul id='campos_independientes_sensores_informe_fichero_correlacion' hidden>";
        foreach ($campos_independientes AS $campo_independiente)
        {
            $lista_campos_independientes_oculta .= "<li>".$campo_independiente."</li>";
        }
        $lista_campos_independientes_oculta .= "</ul>";
        $html .= $lista_campos_independientes_oculta;
        $lista_parametros_extra_campos_independientes_oculta = "<ul id='parametros_extra_campos_independientes_sensores_informe_fichero_correlacion' hidden>";
        foreach ($parametros_extra_campos_independientes AS $parametros_extra_campo_independiente)
        {
            $lista_parametros_extra_campos_independientes_oculta .= "<li>".$parametros_extra_campo_independiente."</li>";
        }
        $lista_parametros_extra_campos_independientes_oculta .= "</ul>";
        $html .= $lista_parametros_extra_campos_independientes_oculta;
        $html .= "<div id='clase_sensor_dependiente_sensores_informe_fichero_correlacion' hidden>".$clase_sensor_dependiente."</div>";
        $html .= "<div id='id_sensor_dependiente_sensores_informe_fichero_correlacion' hidden>".$id_sensor_dependiente."</div>";
        $html .= "<div id='nombre_sensor_dependiente_sensores_informe_fichero_correlacion' hidden>".htmlspecialchars($nombre_sensor_dependiente, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_dependiente_sensores_informe_fichero_correlacion' hidden>".$campo_dependiente."</div>";
        $html .= "<div id='parametros_extra_campo_dependiente_sensores_informe_fichero_correlacion' hidden>".$parametros_extra_campo_dependiente."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_correlacion' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_correlacion' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_correlacion' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_correlacion' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_correlacion' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='funcion_correlacion_sensores_informe_fichero_correlacion' hidden>".$funcion_correlacion."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_correlacion' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_correlacion' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_correlacion' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
