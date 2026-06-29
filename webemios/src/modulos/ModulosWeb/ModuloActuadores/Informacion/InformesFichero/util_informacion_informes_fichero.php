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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_actuadores_informacion($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $titulo_informe = "Informe de acciones enviadas";
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


    // Parámetros del fichero del informe de acciones enviadas
    function dame_html_parametros_tipo_informe_fichero_actuadores_informacion_acciones_enviadas($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $clase_actuador = $parametros_informe["clase_actuador"];
        $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
        $destino_accion = $parametros_informe["destino_accion"];
        $id_destino_accion = $parametros_informe["id_destino_accion"];
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
        $origen_acciones = $parametros_informe["origen_acciones"];
        $clase_sensor = $parametros_informe["clase_sensor"];
        $id_sensor = $parametros_informe["id_sensor"];
        if ($id_sensor != ID_NINGUNO)
        {
            $nombre_sensor = dame_nombre_sensor($id_sensor);
            $campo = $parametros_informe["campo"];
            $nombre_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
            $parametros_extra_campo = $parametros_informe["parametros_extra_campo"];
            $intervalo_valores = $parametros_informe["intervalo_valores"];
        }
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $comentarios = $parametros_informe["comentarios"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Clase de actuador").":</b></td><td class='contenido-parametro-informe-fichero'>".NodoActuador::dame_descripcion_clase_actuador($clase_actuador)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de destino").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_destino_accion($destino_accion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Destino").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_destino_accion, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Origen").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_origen_acciones($origen_acciones, ENT_QUOTES)."</td></tr>";
        if ($id_sensor != ID_NINGUNO)
        {
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
            $html .= " [".strtolower(NodoSensor::dame_descripcion_clase_sensor($clase_sensor));
            $campos_clase = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
            if (count($campos_clase) > 1)
            {
                $html .= " - ".strtolower($nombre_campo);
            }
            $html .= "]";
            if ($parametros_extra_campo != "")
            {
                $descripcion_parametros_extra_campo = dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo);
                $html .= " (".strtolower($descripcion_parametros_extra_campo).": ".$parametros_extra_campo.")";
            }
            $html .= "</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        if ($id_sensor != ID_NINGUNO)
        {
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Intervalo de valores de sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_intervalo_valores($intervalo_valores)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='clase_actuador_actuadores_informe_fichero_acciones_enviadas' hidden>".$clase_actuador."</div>";
        $html .= "<div id='nombre_clase_actuador_actuadores_informe_fichero_acciones_enviadas' hidden>".htmlspecialchars($nombre_clase_actuador, ENT_QUOTES)."</div>";
        $html .= "<div id='destino_accion_actuadores_informe_fichero_acciones_enviadas' hidden>".$destino_accion."</div>";
        $html .= "<div id='id_destino_accion_actuadores_informe_fichero_acciones_enviadas' hidden>".$id_destino_accion."</div>";
        $html .= "<div id='nombre_destino_accion_actuadores_informe_fichero_acciones_enviadas' hidden>".htmlspecialchars($nombre_destino_accion, ENT_QUOTES)."</div>";
        $html .= "<div id='origen_acciones_actuadores_informe_fichero_acciones_enviadas' hidden>".$origen_acciones."</div>";
        $html .= "<div id='clase_sensor_actuadores_informe_fichero_acciones_enviadas' hidden>".$clase_sensor."</div>";
        $html .= "<div id='id_sensor_actuadores_informe_fichero_acciones_enviadas' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_actuadores_informe_fichero_acciones_enviadas' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='campo_actuadores_informe_fichero_acciones_enviadas' hidden>".$campo."</div>";
        $html .= "<div id='nombre_campo_actuadores_informe_fichero_acciones_enviadas' hidden>".htmlspecialchars($nombre_campo, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_actuadores_informe_fichero_acciones_enviadas' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_actuadores_informe_fichero_acciones_enviadas' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_actuadores_informe_fichero_acciones_enviadas' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_actuadores_informe_fichero_acciones_enviadas' hidden>".$hora_fin."</div>";
        $html .= "<div id='intervalo_valores_actuadores_informe_fichero_acciones_enviadas' hidden>".$intervalo_valores."</div>";
        $html .= "<div id='comentarios_actuadores_informe_fichero_acciones_enviadas' hidden>".$comentarios."</div>";
        $html .= "<div id='horario_semanal_actuadores_informe_fichero_acciones_enviadas' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_actuadores_informe_fichero_acciones_enviadas' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_actuadores_informe_fichero_acciones_enviadas' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
