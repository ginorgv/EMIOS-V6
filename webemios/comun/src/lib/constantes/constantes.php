<?php
    // Evita la doble inclusión
    if (defined('CONSTANTES_COMUN_YA_INCLUIDO')) { return; }
    define('CONSTANTES_COMUN_YA_INCLUIDO', true);

    // Versión del módulo común WEB
    define("VERSION_COMUN_WEB", "6.0.0.0");

    // Ficheros WEB concatenados
    define("FICHEROS_WEB_CONCATENADOS", false);

    // Web de EnergyMinus
    define("WEB_ENERGY_MINUS", "www.energy-minus.es");

    // Número máximo de intentos de login (por sesión)
    define("NUMERO_MAXIMO_INTENTOS_LOGIN_SESION", 5);
    define("NUMERO_MINIMO_SEGUNDOS_ESPERA_MAXIMO_INTENTOS_LOGIN_SESION", 60);

    // Longitud del identificador de sesión
    define("LONGITUD_ID_SESION", 8);

    // Zonas horarias
    define("ZONA_HORARIA_UTC", "UTC");

    // Tipos de formatos 'permitidos' de fecha locales
    define("TIPO_FORMATO_FECHA_LOCAL_DIA_MES_ANYO", "dia_mes_anyo");
    define("TIPO_FORMATO_FECHA_LOCAL_MES_DIA_ANYO", "mes_dia_anyo");
    define("TIPO_FORMATO_FECHA_LOCAL_ANYO_MES_DIA", "anyo_mes_dia");

    // Formatos de fechas locales
    define("FORMATO_FECHA_LOCAL_DIA_MES_ANYO", "d/m/Y");
    define("FORMATO_FECHA_LOCAL_MES_DIA_ANYO", "m/d/Y");
    define("FORMATO_FECHA_LOCAL_ANYO_MES_DIA", "Y/m/d");

    // Formatos de fechas locales (javascript)
    define("FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JAVASCRIPT", "dd/mm/yyyy");
    define("FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JAVASCRIPT", "mm/dd/yyyy");
    define("FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JAVASCRIPT", "yyyy/mm/dd");

    // Formatos de dias de año
    define("FORMATO_DIA_ANYO_LOCAL_DIA_MES", "d/m");
    define("FORMATO_DIA_ANYO_LOCAL_MES_DIA", "m/d");

    // Formatos 'permitidos' de dias de año
    define("FORMATO_DIA_ANYO_LOCAL_DIA_MES_JAVASCRIPT", "dd/mm");
    define("FORMATO_DIA_ANYO_LOCAL_MES_DIA_JAVASCRIPT", "mm/dd");

    // Tipo de formato de fecha local por defecto
    define("TIPO_FORMATO_FECHA_LOCAL_DEFECTO", TIPO_FORMATO_FECHA_LOCAL_DIA_MES_ANYO);

    // Formatos de hora
    define("FORMATO_HORA", "H:i:s");
    define("FORMATO_HORA_SIN_SEGUNDOS", "H:i");

    // Formateado de números por defecto
    define("SEPARADOR_MILES_DEFECTO", ",");
    define("PUNTO_DECIMAL_DEFECTO",  ".");
?>
