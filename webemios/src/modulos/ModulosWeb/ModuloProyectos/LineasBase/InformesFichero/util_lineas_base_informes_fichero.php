<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_proyectos_lineas_base($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $titulo_informe = "Informe de simulación de línea base";
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


    // Parámetros del fichero del informe de simulación de línea bas
    function dame_html_parametros_tipo_informe_fichero_proyectos_simulador_linea_base($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_linea_base = $parametros_informe["id_linea_base"];
        $nombre_linea_base = dame_nombre_linea_base($id_linea_base);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $comentarios = $parametros_informe["comentarios"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Línea base").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_linea_base, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Comentarios").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_comentarios($comentarios)."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_linea_base_proyectos_informe_fichero_simulador_linea_base' hidden>".$id_linea_base."</div>";
        $html .= "<div id='comentarios_proyectos_informe_fichero_simulador_linea_base' hidden>".$comentarios."</div>";
        $html .= "<div id='fecha_inicio_proyectos_informe_fichero_simulador_linea_base' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_proyectos_informe_fichero_simulador_linea_base' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_proyectos_informe_fichero_simulador_linea_base' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_proyectos_informe_fichero_simulador_linea_base' hidden>".$hora_fin."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
