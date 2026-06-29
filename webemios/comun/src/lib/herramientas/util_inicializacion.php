<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/log/log.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Establece los formatos de fecha (en la sesión)
    function establece_formatos_fecha_local($tipo_formato_fecha_local)
    {
        switch ($tipo_formato_fecha_local)
        {
            case TIPO_FORMATO_FECHA_LOCAL_DIA_MES_ANYO:
            {
                $formato_fecha_local = FORMATO_FECHA_LOCAL_DIA_MES_ANYO;
                break;
            }
            case TIPO_FORMATO_FECHA_LOCAL_MES_DIA_ANYO:
            {
                $formato_fecha_local = FORMATO_FECHA_LOCAL_MES_DIA_ANYO;
                break;
            }
            case TIPO_FORMATO_FECHA_LOCAL_ANYO_MES_DIA:
            {
                $formato_fecha_local = FORMATO_FECHA_LOCAL_ANYO_MES_DIA;
                break;
            }
        }
        $_SESSION["formato_fecha_local"] = $formato_fecha_local;
        $_SESSION["formato_fecha_hora_local"] = $formato_fecha_local.", ".FORMATO_HORA;
        $_SESSION["formato_fecha_hora_local_sin_segundos"] = $formato_fecha_local.", ".FORMATO_HORA_SIN_SEGUNDOS;

        switch ($formato_fecha_local)
        {
            case FORMATO_FECHA_LOCAL_DIA_MES_ANYO:
            {
                $formato_dia_anyo_local = FORMATO_DIA_ANYO_LOCAL_DIA_MES;
                break;
            }
            case FORMATO_FECHA_LOCAL_MES_DIA_ANYO:
            case FORMATO_FECHA_LOCAL_ANYO_MES_DIA:
            {
                $formato_dia_anyo_local = FORMATO_DIA_ANYO_LOCAL_MES_DIA;
                break;
            }
        }
        $_SESSION["formato_dia_anyo_local"] = $formato_dia_anyo_local;
    }
?>