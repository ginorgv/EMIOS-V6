<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO", 0);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de información de proyecto
    function dame_html_parametros_tipo_informe_automatico_proyectos_informacion_proyecto($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Proyecto").": ".dame_nombre_proyecto($id_proyecto)."</li>";
        $html .= "</ul>";

        return ($html);
    }
?>
