<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_proyectos_informacion($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $titulo_informe = "Informe de información de proyecto";
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


    // Parámetros del fichero del informe de información de proyecto
    function dame_html_parametros_tipo_informe_fichero_proyectos_informacion_proyecto($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_proyecto = $parametros_informe["id_proyecto"];
        $nombre_proyecto = dame_nombre_proyecto($id_proyecto);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Proyecto").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_proyecto, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_proyecto_proyectos_informe_fichero_informacion_proyecto' hidden>".$id_proyecto."</div>";
        $html .= "<div id='fecha_inicio_proyectos_informe_fichero_informacion_proyecto' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_proyectos_informe_fichero_informacion_proyecto' hidden>".$fecha_fin."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
