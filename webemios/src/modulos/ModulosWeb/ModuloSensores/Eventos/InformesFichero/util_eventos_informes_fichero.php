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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve la cabecera de una página de un fichero de un informe
    function dame_html_cabecera_informe_fichero_sensores_eventos($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $titulo_informe = "Informe de activaciones de eventos";
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


    // Parámetros del fichero del informe de activaciones de eventos
    function dame_html_parametros_tipo_informe_fichero_sensores_activaciones_eventos($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $clase_sensor = $parametros_informe["clase_sensor"];
        $origen_evento = $parametros_informe["origen_evento"];
        $id_origen_evento = $parametros_informe["id_origen_evento"];
        switch ($origen_evento)
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                $nombre_origen_evento = dame_nombre_sensor($id_origen_evento);
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $nombre_origen_evento = dame_nombre_grupo_sensores($id_origen_evento);
                break;
            }
        }
        $granularidad_evento = $parametros_informe["granularidad_evento"];
        $ids_eventos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_informe["ids_eventos"]);
        $nombres_eventos = dame_nombres_eventos($ids_eventos);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $campo = $parametros_informe["campo"];
        $nombre_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Clase").":</b></td><td class='contenido-parametro-informe-fichero'>".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de origen").":</b></td><td class='contenido-parametro-informe-fichero'>".Evento::dame_descripcion_origen_evento($origen_evento)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Origen").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_origen_evento, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Granularidad").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_granularidad($granularidad_evento)."</td></tr>";
        $numero_evento = 0;
        foreach ($nombres_eventos as $nombre_evento)
        {
            $texto_evento = "";
            if ($numero_evento == 0)
            {
                if (count($nombres_eventos) == 1)
                {
                    $texto_evento = $idiomas->_("Evento").":";
                }
                else
                {
                    $texto_evento = $idiomas->_("Eventos").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_evento."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_evento, ENT_QUOTES)."</td></tr>";
            $numero_evento++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Campo").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_campo, ENT_QUOTES)."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='clase_sensor_sensores_informe_fichero_activaciones_eventos' hidden>".$clase_sensor."</div>";
        $html .= "<div id='origen_evento_sensores_informe_fichero_activaciones_eventos' hidden>".$origen_evento."</div>";
        $html .= "<div id='id_origen_evento_sensores_informe_fichero_activaciones_eventos' hidden>".$id_origen_evento."</div>";
        $html .= "<div id='nombre_origen_evento_sensores_informe_fichero_activaciones_eventos' hidden>".htmlspecialchars($nombre_origen_evento, ENT_QUOTES)."</div>";
        $html .= "<div id='granularidad_evento_sensores_informe_fichero_activaciones_eventos' hidden>".$granularidad_evento."</div>";
        $lista_ids_eventos_oculta = "<ul id='ids_eventos_sensores_informe_fichero_activaciones_eventos' hidden>";
        foreach ($ids_eventos AS $id_evento)
        {
            $lista_ids_eventos_oculta .= "<li>".$id_evento."</li>";
        }
        $lista_ids_eventos_oculta .= "</ul>";
        $html .= $lista_ids_eventos_oculta;
        $lista_nombres_eventos_oculta = "<ul id='nombres_eventos_sensores_informe_fichero_activaciones_eventos' hidden>";
        foreach ($nombres_eventos AS $nombre_evento)
        {
            $lista_nombres_eventos_oculta .= "<li>".htmlspecialchars($nombre_evento, ENT_QUOTES)."</li>";
        }
        $lista_nombres_eventos_oculta .= "</ul>";
        $html .= $lista_nombres_eventos_oculta;
        $html .= "<div id='fecha_inicio_sensores_informe_fichero_activaciones_eventos' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_sensores_informe_fichero_activaciones_eventos' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_sensores_informe_fichero_activaciones_eventos' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_sensores_informe_fichero_activaciones_eventos' hidden>".$hora_fin."</div>";
        $html .= "<div id='campo_sensores_informe_fichero_activaciones_eventos' hidden>".$campo."</div>";
        $html .= "<div id='nombre_campo_sensores_informe_fichero_activaciones_eventos' hidden>".htmlspecialchars($nombre_campo, ENT_QUOTES)."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
