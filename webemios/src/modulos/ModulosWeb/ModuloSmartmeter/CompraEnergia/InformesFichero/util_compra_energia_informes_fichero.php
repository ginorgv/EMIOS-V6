<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_compra_energia($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA:
            {
                $titulo_informe = "Informe de previsión de compra de energía";
                break;
            }
            case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            {
                $titulo_informe = "Informe de desvíos de compra de energía";
                break;
            }
            case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            {
                $titulo_informe = "Informe de desvíos ponderados de compra de energía";
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


    // Parámetros del fichero del informe de previsión de compra de energía
    function dame_html_parametros_tipo_informe_fichero_smartmeter_prevision_compra_energia($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $fecha_inicio_perfil_horario = $parametros_informe["fecha_inicio_perfil_horario"];
        $fecha_fin_perfil_horario = $parametros_informe["fecha_fin_perfil_horario"];
        $tipo_perfil_horario = $parametros_informe["tipo_perfil_horario"];
        $cadena_agrupaciones_dias_semana = $parametros_informe["agrupaciones_dias_semana"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio_perfil_horario."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin_perfil_horario."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de perfil horario").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_perfil_horario($tipo_perfil_horario)."</td></tr>";
        $html .= dame_html_parametro_agrupaciones_dias_semana_informe_fichero($idiomas->_("Agrupaciones de días de la semana"), $cadena_agrupaciones_dias_semana);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_prevision_compra_energia' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$hora_fin."</div>";
        $html .= "<div id='fecha_inicio_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$fecha_inicio_perfil_horario."</div>";
        $html .= "<div id='fecha_fin_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$fecha_fin_perfil_horario."</div>";
        $html .= "<div id='tipo_perfil_horario_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$tipo_perfil_horario."</div>";
        $html .= "<div id='agrupaciones_dias_semana_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$cadena_agrupaciones_dias_semana."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_prevision_compra_energia' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de desvíos de compra de energía
    function dame_html_parametros_tipo_informe_fichero_smartmeter_desvios_compra_energia($parametros_informe)
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_desvios_compra_energia' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$hora_fin."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_desvios_compra_energia' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de desvíos ponderados de compra de energía
    function dame_html_parametros_tipo_informe_fichero_smartmeter_desvios_ponderados_compra_energia($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_sensor_hijo = $parametros_informe["id_sensor_hijo"];
        $nombre_sensor_hijo = dame_nombre_sensor($id_sensor_hijo);
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor hijo").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor_hijo, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_sensor_hijo_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$id_sensor_hijo."</div>";
        $html .= "<div id='nombre_sensor_hijo_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".htmlspecialchars($nombre_sensor_hijo, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$hora_fin."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_desvios_ponderados_compra_energia' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
