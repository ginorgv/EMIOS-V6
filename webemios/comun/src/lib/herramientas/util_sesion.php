<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sesion.php');


    // Devuelve la información local (de la sesión)
    function dame_informacion_local()
    {
        // Formatos de fecha local (JavaScript)
        $formato_fecha_local = $_SESSION["formato_fecha_local"];
        switch ($formato_fecha_local)
        {
            case FORMATO_FECHA_LOCAL_DIA_MES_ANYO:
            {
                $formato_fecha_local_javascript = FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JAVASCRIPT;
                break;
            }
            case FORMATO_FECHA_LOCAL_MES_DIA_ANYO:
            {
                $formato_fecha_local_javascript = FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JAVASCRIPT;
                break;
            }
            case FORMATO_FECHA_LOCAL_ANYO_MES_DIA:
            {
                $formato_fecha_local_javascript = FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JAVASCRIPT;
                break;
            }
        }
        $formato_dia_anyo_local = $_SESSION["formato_dia_anyo_local"];
        switch ($formato_dia_anyo_local)
        {
            case FORMATO_DIA_ANYO_LOCAL_DIA_MES:
            {
                $formato_dia_anyo_local_javascript = FORMATO_DIA_ANYO_LOCAL_DIA_MES_JAVASCRIPT;
                break;
            }
            case FORMATO_DIA_ANYO_LOCAL_MES_DIA:
            {
                $formato_dia_anyo_local_javascript = FORMATO_DIA_ANYO_LOCAL_MES_DIA_JAVASCRIPT;
                break;
            }
        }

        // Formateado de números
        $separador_miles = $_SESSION["separador_miles"];
        $punto_decimal = $_SESSION["punto_decimal"];

        // Información local
        $informacion_local = array(
            "formato_fecha_local" => $formato_fecha_local_javascript,
            "formato_dia_anyo_local" => $formato_dia_anyo_local_javascript,
            "separador_miles" => $separador_miles,
            "punto_decimal" => $punto_decimal);

        // Se añade la informacion 'extra' local
        $informacion_extra_local = dame_informacion_extra_local();
        $informacion_local = array_merge($informacion_local, $informacion_extra_local);

        // Se devuelve la información local
        return ($informacion_local);
    }


    // Devuelve la información de preferencias (de la sesión)
    function dame_informacion_preferencias()
    {
        // Idioma actual
        if (array_key_exists("idioma", $_SESSION) == true)
        {
            $idioma = $_SESSION["idioma"];
        }
        else
        {
            $idioma = "";
        }

        // Informacion de preferencias
        $informacion_preferencias = array(
            "ficheros_web_concatenados" => FICHEROS_WEB_CONCATENADOS,
            "idioma" => $idioma,
            "color_tema_oscuro" => $_SESSION["colores"]["color_tema_oscuro"],
            "color_tema_intermedio" => $_SESSION["colores"]["color_tema_intermedio"],
            "color_tema_claro" => $_SESSION["colores"]["color_tema_claro"],
            "color_tema_fondo" => $_SESSION["colores"]["color_tema_fondo"]);

        // Añade la informacion 'extra' de preferencias actuales
        $informacion_extra_preferencias_actuales = dame_informacion_extra_preferencias_actuales();
        $informacion_preferencias = array_merge($informacion_preferencias, $informacion_extra_preferencias_actuales);

        // Se devuelve la información de preferencias
        return ($informacion_preferencias);
    }
?>