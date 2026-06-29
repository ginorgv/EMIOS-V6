<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_consumos_costes($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $titulo_informe = "Informe de consumos y costes generales";
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $titulo_informe = "Informe de consumos y costes totales";
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
            {
                $titulo_informe = "Informe de consumos y costes por tramo";
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
            {
                $titulo_informe = "Informe de excesos de potencia";
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
            {
                $titulo_informe = "Informe de excesos de energía reactiva";
                break;
            }
            case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
            {
                $titulo_informe = "Informe de cortes de tensión";
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
            {
                $titulo_informe = "Informe de excesos de caudal";
                break;
            }
            case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $titulo_informe = "Informe de comparación de periodos";
                break;
            }
            case TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $titulo_informe = "Informe de simulacion de tarifas";
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


    // Parámetros del fichero del informe de consumos y costes generales
    function dame_html_parametros_tipo_informe_fichero_smartmeter_consumos_costes_generales($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $medicion = $parametros_informe["medicion"];
        $id_ratio = $parametros_informe["id_ratio"];
        $ids_sensores = explode(",", $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $agregacion = $parametros_informe["agregacion"];
        $comentarios = $parametros_informe["comentarios"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
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
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
            $numero_sensor++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Agregación").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_agregacion($agregacion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$medicion."</div>";
        $html .= "<div id='id_ratio_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$id_ratio."</div>";
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_smartmeter_informe_fichero_consumos_costes_generales' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_smartmeter_informe_fichero_consumos_costes_generales' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='agregacion_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$agregacion."</div>";
        $html .= "<div id='comentarios_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_consumos_costes_generales' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de consumos y costes totales
    function dame_html_parametros_tipo_informe_fichero_smartmeter_consumos_costes_totales($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $medicion = $parametros_informe["medicion"];
        $id_ratio = $parametros_informe["id_ratio"];
        $ids_sensores = explode(",", $parametros_informe["ids_sensores"]);
        $nombres_sensores = dame_nombres_sensores($ids_sensores);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
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
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
            $numero_sensor++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$medicion."</div>";
        $html .= "<div id='id_ratio_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$id_ratio."</div>";
        $lista_ids_sensores_oculta = "<ul id='ids_sensores_smartmeter_informe_fichero_consumos_costes_totales' hidden>";
        foreach ($ids_sensores AS $id_sensor)
        {
            $lista_ids_sensores_oculta .= "<li>".$id_sensor."</li>";
        }
        $lista_ids_sensores_oculta .= "</ul>";
        $html .= $lista_ids_sensores_oculta;
        $lista_nombres_sensores_oculta = "<ul id='nombres_sensores_smartmeter_informe_fichero_consumos_costes_totales' hidden>";
        foreach ($nombres_sensores AS $nombre_sensor)
        {
            $lista_nombres_sensores_oculta .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_oculta;
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_consumos_costes_totales' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de consumos y costes por tramo
    function dame_html_parametros_tipo_informe_fichero_smartmeter_consumos_costes_tramos($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$fecha_fin."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_consumos_costes_tramos' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de excesos de potencia
    function dame_html_parametros_tipo_informe_fichero_smartmeter_excesos_potencia($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $granularidad = $parametros_informe["granularidad"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Granularidad").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_granularidad($granularidad)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_excesos_potencia' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_excesos_potencia' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_excesos_potencia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_excesos_potencia' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_excesos_potencia' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_excesos_potencia' hidden>".$hora_fin."</div>";
        $html .= "<div id='granularidad_smartmeter_informe_fichero_excesos_potencia' hidden>".$granularidad."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_excesos_potencia' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_excesos_potencia' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_excesos_potencia' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de excesos de energía reactiva
    function dame_html_parametros_tipo_informe_fichero_smartmeter_excesos_energia_reactiva($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$hora_fin."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_excesos_energia_reactiva' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de cortes de tensión
    function dame_html_parametros_tipo_informe_fichero_smartmeter_cortes_tension($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_cortes_tension' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_cortes_tension' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_cortes_tension' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_cortes_tension' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_cortes_tension' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_cortes_tension' hidden>".$hora_fin."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de excesos de caudal
    function dame_html_parametros_tipo_informe_fichero_smartmeter_excesos_caudal($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_excesos_caudal' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_excesos_caudal' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_excesos_caudal' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_excesos_caudal' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_excesos_caudal' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_excesos_caudal' hidden>".$hora_fin."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_excesos_caudal' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_excesos_caudal' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_excesos_caudal' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de comparación de periodos
    function dame_html_parametros_tipo_informe_fichero_smartmeter_comparacion_periodos($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $medicion = $parametros_informe["medicion"];
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio_periodo_anterior = $parametros_informe["fecha_inicio_periodo_anterior"];
        $fecha_inicio_periodo_posterior = $parametros_informe["fecha_inicio_periodo_posterior"];
        $numero_dias_periodo = $parametros_informe["numero_dias_periodo"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio del periodo anterior").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_periodo_anterior."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio del periodo posterior").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_periodo_posterior."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Duración del periodo")." (".$idiomas->_("días").")".":</b></td><td class='contenido-parametro-informe-fichero'>".$numero_dias_periodo."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_comparacion_periodos' hidden>".$medicion."</div>";
        $html .= "<div id='id_ratio_smartmeter_informe_fichero_comparacion_periodos' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_comparacion_periodos' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_comparacion_periodos' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_periodo_anterior_smartmeter_informe_fichero_comparacion_periodos' hidden>".$fecha_inicio_periodo_anterior."</div>";
        $html .= "<div id='fecha_inicio_periodo_posterior_smartmeter_informe_fichero_comparacion_periodos' hidden>".$fecha_inicio_periodo_posterior."</div>";
        $html .= "<div id='numero_dias_periodo_smartmeter_informe_fichero_comparacion_periodos' hidden>".$numero_dias_periodo."</div>";
        $html .= "<div id='intervalo_valores_smartmeter_informe_fichero_comparacion_periodos' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_comparacion_periodos' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_comparacion_periodos' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de simulación de tarifas
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_tarifas($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $medicion = $parametros_informe["medicion"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $ids_tarifas = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_tarifas"]);
        $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
        $nombres_tarifas = dame_nombres_tarifas($tabla_tarifas, $ids_tarifas);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $numero_tarifa = 0;
        foreach ($nombres_tarifas as $nombre_tarifa)
        {
            $texto_tarifa = "";
            if ($numero_tarifa == 0)
            {
                if (count($nombres_tarifas) == 1)
                {
                    $texto_tarifa = $idiomas->_("Tarifa").":";
                }
                else
                {
                    $texto_tarifa = $idiomas->_("Tarifas").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_tarifa."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
            $numero_tarifa++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_simulador_tarifas' hidden>".$medicion."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_tarifas' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_tarifas' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $lista_ids_tarifas_oculta = "<ul id='ids_tarifas_smartmeter_informe_fichero_simulador_tarifas' hidden>";
        foreach ($ids_tarifas AS $id_tarifa)
        {
            $lista_ids_tarifas_oculta .= "<li>".$id_tarifa."</li>";
        }
        $lista_ids_tarifas_oculta .= "</ul>";
        $html .= $lista_ids_tarifas_oculta;
        $lista_nombres_tarifas_oculta = "<ul id='nombres_tarifas_smartmeter_informe_fichero_simulador_tarifas' hidden>";
        foreach ($nombres_tarifas AS $nombre_tarifa)
        {
            $lista_nombres_tarifas_oculta .= "<li>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</li>";
        }
        $lista_nombres_tarifas_oculta .= "</ul>";
        $html .= $lista_nombres_tarifas_oculta;
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_tarifas' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_simulador_tarifas' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_tarifas' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_simulador_tarifas' hidden>".$hora_fin."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
