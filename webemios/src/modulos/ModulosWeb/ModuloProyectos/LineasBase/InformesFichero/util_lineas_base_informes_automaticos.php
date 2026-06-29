<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_SIMULADOR_LINEA_BASE_COMENTARIOS", 1);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de simulación de línea base
    function dame_html_parametros_tipo_informe_automatico_proyectos_simulador_linea_base($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_linea_base = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
        $comentarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_SIMULADOR_LINEA_BASE_COMENTARIOS];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Línea base").": ".dame_nombre_linea_base($id_linea_base)."</li>";
        $html .= "<li>".$idiomas->_("Comentarios").": ".dame_descripcion_comentarios($comentarios)."</li>";
        $html .= "</ul>";

        return ($html);
    }
?>
