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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve la cabecera de una página de un fichero de un informe
    function dame_html_cabecera_informe_fichero_sensores_informacion($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
            {
                $titulo_informe = "Informe de temperatura";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
            {
                $titulo_informe = "Informe de humedad";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
            {
                $titulo_informe = "Informe de luz interior";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
            {
                $titulo_informe = "Informe de viento";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            {
                $titulo_informe = "Informe de energía activa";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            {
                $titulo_informe = "Informe de energía reactiva";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
            {
                $titulo_informe = "Informe de cortes de tensión";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
            {
                $titulo_informe = "Informe de compra de energía";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
            {
                $titulo_informe = "Informe de gas";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
            {
                $titulo_informe = "Informe de agua";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
            {
                $titulo_informe = "Informe de datos genéricos";
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


    // Parámetros del fichero del informe de temperatura
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_temperatura($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
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
        $comentarios = $parametros_informe["comentarios"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo))."]";
        if ($parametros_extra_campo != "")
        {
            $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
            $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
        }
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_temperatura' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_temperatura' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_temperatura' hidden>".$campo."</div>";
        $html .= "<div id='parametros_extra_campo_sensores_informe_fichero_informacion_temperatura' hidden>".$parametros_extra_campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_temperatura' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_temperatura' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_temperatura' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_temperatura' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_temperatura' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_temperatura' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_temperatura' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_temperatura' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_temperatura' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_temperatura' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de humedad
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_humedad($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_humedad' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_humedad' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_humedad' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_humedad' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_humedad' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_humedad' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_humedad' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_humedad' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_humedad' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_humedad' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_humedad' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_humedad' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de luz interior
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_luz_interior($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_luz_interior' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_luz_interior' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_luz_interior' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_luz_interior' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_luz_interior' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_luz_interior' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_luz_interior' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_luz_interior' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_luz_interior' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_luz_interior' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_luz_interior' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_luz_interior' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de viento
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_viento($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_viento' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_viento' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_viento' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_viento' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_viento' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_viento' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_viento' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_viento' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_viento' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_viento' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_viento' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_viento' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de energía
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_energia($tipo_informe, $parametros_informe)
    {
        $idiomas = new Idiomas();

        // clase de sensor y sufijo de controles de tipo de energía
        $clase_sensor = NULL;
        $tipo_energia = NULL;
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
                $tipo_energia = "activa";
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_REACTIVA;
                $tipo_energia = "reactiva";
                break;
            }
        }

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo))."]";
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_energia_".$tipo_energia."' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de cortes de tensión
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_cortes_tension($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_cortes_tension' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_cortes_tension' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_cortes_tension' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_cortes_tension' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_cortes_tension' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_cortes_tension' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_cortes_tension' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_cortes_tension' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_cortes_tension' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_cortes_tension' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_cortes_tension' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_cortes_tension' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_cortes_tension' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de compra de energía
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_compra_energia($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, $campo))."]";
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_compra_energia' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_compra_energia' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_compra_energia' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_compra_energia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_compra_energia' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_compra_energia' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_compra_energia' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_compra_energia' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_compra_energia' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_compra_energia' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_compra_energia' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_compra_energia' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_compra_energia' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_compra_energia' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de gas
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_gas($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, $campo))."]";
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_informacion_gas' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_gas' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_gas' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_gas' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_gas' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_gas' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_gas' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_gas' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_gas' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_gas' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_gas' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_gas' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_gas' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_gas' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_gas' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de agua
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_agua($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, $campo))."]";
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_informacion_agua' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_agua' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_agua' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_agua' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_agua' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_agua' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_agua' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_agua' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_agua' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_agua' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_agua' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_agua' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_agua' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_agua' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_agua' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de información generica
    function dame_html_parametros_tipo_informe_fichero_sensores_informacion_generica($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $campo = $parametros_informe["campo"];
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $intervalo_valores = $parametros_informe["intervalo_valores"];
        $tipo_mapa_calor = $parametros_informe["tipo_mapa_calor"];
        $comentarios = $parametros_informe["comentarios"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
        $html .= " [".strtolower(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, $campo))."]";
        $html .= "</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de mapa de calor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_mapa_calor($tipo_mapa_calor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_ratio_sensores_informe_fichero_informacion_generica' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_sensores_informe_fichero_informacion_generica' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_sensores_informe_fichero_informacion_generica' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_informacion_generica' hidden>".$campo."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_generica' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_informacion_generica' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_informacion_generica' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_informacion_generica' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_informacion_generica' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_sensores_informe_fichero_informacion_generica' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='tipo_mapa_calor_sensores_informe_fichero_informacion_generica' hidden>".$tipo_mapa_calor."</div>";
        $html .= "<div id='comentarios_sensores_informe_fichero_informacion_generica' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_sensores_informe_fichero_informacion_generica' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_sensores_informe_fichero_informacion_generica' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_sensores_informe_fichero_informacion_generica' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
