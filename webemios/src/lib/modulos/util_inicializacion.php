<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_inicializacion.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    // Convierte rutas de Linux a Windows cuando se ejecuta en Windows
    function dame_ruta_local($ruta_linux)
    {
        // Si no es Windows, devolver la ruta original
        if (PHP_OS_FAMILY !== 'Windows') {
            return $ruta_linux;
        }
        
        // Convertir /opt/energyminus/ -> C:\opt\energyminus\
        // Eliminar barra inicial y reemplazar / por \
        $ruta_linux = ltrim($ruta_linux, '/');
        $ruta_linux = str_replace('/', '\\', $ruta_linux);
        
        // Prevenir doble backslash si la ruta empieza con opt
        if (strpos($ruta_linux, '\\') === 0) {
            $ruta_linux = substr($ruta_linux, 1);
        }
        
        return 'C:\\' . $ruta_linux;
    }


    // Devuelve las entradas del fichero de inicio
    // Soporta variables de entorno para despliegue en Railway/Cloud
    function dame_entradas_ini()
	{
        // Intentar cargar config.ini (puede no existir en cloud)
        $entradas_ini = array();
        $ruta_ini = $_SESSION["directorio"].RUTA_FICHERO_INI;
        if (file_exists($ruta_ini)) {
            $entradas_ini_fichero = parse_ini_file($ruta_ini);
            if ($entradas_ini_fichero !== false) {
                $entradas_ini = $entradas_ini_fichero;
            }
        }

        // Sobrescribir con variables de entorno (Railway/Cloud)
        // Railway MySQL plugin proporciona: MYSQL_URL (mysql://user:pass@host:port/db)
        // o individualmente: MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD
        $mysql_url = getenv('MYSQL_URL');
        $mysql_host = getenv('MYSQL_HOST') ?: 'localhost';
        $mysql_port = getenv('MYSQL_PORT') ?: '3306';
        $mysql_db   = getenv('MYSQL_DATABASE') ?: 'emios';
        $mysql_user = getenv('MYSQL_USER') ?: 'emios_user';
        $mysql_pass = getenv('MYSQL_PASSWORD') ?: '';

        // Parsear MYSQL_URL si está disponible (formato: mysql://user:pass@host:port/db)
        if ($mysql_url) {
            $parts = parse_url($mysql_url);
            if ($parts !== false && isset($parts['scheme']) && $parts['scheme'] === 'mysql') {
                $mysql_host = $parts['host'] ?? $mysql_host;
                $mysql_port = isset($parts['port']) ? (string)$parts['port'] : $mysql_port;
                $mysql_user = $parts['user'] ?? $mysql_user;
                $mysql_pass = $parts['pass'] ?? $mysql_pass;
                $mysql_db   = isset($parts['path']) ? ltrim($parts['path'], '/') : $mysql_db;
            }
        }

        $entradas_ini["ip_base_datos_red"] = $mysql_host;
        $entradas_ini["puerto_base_datos_red"] = $mysql_port;
        $entradas_ini["nombre_base_datos_red"] = $mysql_db;
        $entradas_ini["usuario_base_datos_red"] = $mysql_user;
        $entradas_ini["contrasenya_base_datos_red"] = $mysql_pass;

        $entradas_ini["ip_base_datos_datos"] = $mysql_host;
        $entradas_ini["puerto_base_datos_datos"] = $mysql_port;
        $entradas_ini["nombre_base_datos_datos"] = $mysql_db;
        $entradas_ini["usuario_base_datos_datos"] = $mysql_user;
        $entradas_ini["contrasenya_base_datos_datos"] = $mysql_pass;

        // Intentar cargar servidor_emios.ini (puede no existir en cloud)
        $ip_externa = 'localhost';
        $ruta_ini_servidor = null;
        
        if (isset($entradas_ini["ruta_fichero_ini_servidor_emios"])) {
            $ruta_base_servidor_emios = dame_ruta_local($entradas_ini["ruta_fichero_ini_servidor_emios"]);
            $ruta_ini_servidor = $ruta_base_servidor_emios . NOMBRE_FICHERO_INI_SERVIDOR_EMIOS;
        }
        
        $entradas_ini_servidor_emios = false;
        
        if ($ruta_ini_servidor && file_exists($ruta_ini_servidor)) {
            $entradas_ini_servidor_emios = parse_ini_file($ruta_ini_servidor);
        }
        
        // Fallback: buscar en tmp/ (Windows o cloud)
        if ($entradas_ini_servidor_emios == false) {
            $ruta_tmp = $_SESSION["directorio"] . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . NOMBRE_FICHERO_INI_SERVIDOR_EMIOS;
            if (file_exists($ruta_tmp)) {
                $entradas_ini_servidor_emios = parse_ini_file($ruta_tmp);
            }
        }
        
        if ($entradas_ini_servidor_emios !== false && isset($entradas_ini_servidor_emios["ip_externa"])) {
            $ip_externa = $entradas_ini_servidor_emios["ip_externa"];
        }
        
        // Sobrescribir con variable de entorno
        if (getenv('IP_EXTERNA')) {
            $ip_externa = getenv('IP_EXTERNA');
        }

        // Convertir rutas adicionales (solo Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            if (isset($entradas_ini["ruta_procesado_emios"])) {
                $entradas_ini["ruta_procesado_emios"] = dame_ruta_local($entradas_ini["ruta_procesado_emios"]);
            }
            if (isset($entradas_ini["ruta_servicios_emios"])) {
                $entradas_ini["ruta_servicios_emios"] = dame_ruta_local($entradas_ini["ruta_servicios_emios"]);
            }
        }

        // Valores por defecto para cloud si no existen rutas
        if (!isset($entradas_ini["ruta_procesado_emios"])) {
            $entradas_ini["ruta_procesado_emios"] = "/opt/energyminus/procesado/";
        }
        if (!isset($entradas_ini["ruta_servicios_emios"])) {
            $entradas_ini["ruta_servicios_emios"] = "/opt/energyminus/servicios/";
        }

        // IP externa del servidor EMIOS
        $entradas_ini["ip_servidor_emios"] = $ip_externa;
        $entradas_ini["web_emios"] = $ip_externa;

        return ($entradas_ini);
    }


    // Devuelve el valor de la entrada especificada del fichero de inicio
    function dame_valor_entrada_ini($clave)
	{
        $entradas_ini = dame_entradas_ini();
        if (array_key_exists($clave, $entradas_ini))
        {
            return ($entradas_ini[$clave]);
        }
        else
        {
            return (NULL);
        }
    }


    // Establece todas las preferencias por defecto (según la URL)
    function establece_todas_preferencias_defecto()
    {
        establece_preferencias_defecto(true, true, true, true);
    }


    // Establece las preferencias por defecto (según la URL)
    function establece_preferencias_defecto(
        $logo_defecto,
        $titulo_web_defecto,
        $tema_defecto,
        $paleta_colores_graficas_defecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la URL utilizada para entrar en la Web
        $url = $_SERVER['HTTP_HOST'];

        // Se recuperan las preferencias asociadas a esta URL (si las hay)
        $consulta_preferencias = "
            SELECT *
            FROM preferencias
            WHERE
                (url = '".$bd_red->_($url)."')";
        $res_preferencias = $bd_red->ejecuta_consulta($consulta_preferencias);
        if ($res_preferencias == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_preferencias."'");
        }
        if ($res_preferencias->dame_numero_filas() == 0)
        {
            $consulta_preferencias = "
                SELECT *
                FROM preferencias
                WHERE
                    (url = '*')";
            $res_preferencias = $bd_red->ejecuta_consulta($consulta_preferencias);
            if ($res_preferencias == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_preferencias."'");
            }
        }
        if ($res_preferencias->dame_numero_filas() > 0)
        {
            // Información de preferencias
            $fila_preferencias = $res_preferencias->dame_siguiente_fila();
            $id_preferencias = $fila_preferencias["id"];
            $logo_personalizado = $fila_preferencias["logo_personalizado"];
            $url_logo = $fila_preferencias["url_logo"];
            $titulo_web = $fila_preferencias["titulo_web"];
            $tema = $fila_preferencias["tema"];
            $paleta_colores_graficas = $fila_preferencias["paleta_colores_graficas"];

            // Logo (y URL del logo)
            if ($logo_defecto == true)
            {
                switch ($logo_personalizado)
                {
                    case VALOR_SI:
                    {
                        $info_imagen_logo = carga_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, $id_preferencias, null);
                        $info_imagen_logo_pdf = carga_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, $id_preferencias, null);
                        $_SESSION["ruta_logo"] = $info_imagen_logo["ruta_fichero_imagen"];
                        $_SESSION["ruta_logo_pdf"] = $info_imagen_logo_pdf["ruta_fichero_imagen"];
                        $_SESSION["url_logo"] = $url_logo;
                        break;
                    }
                    case VALOR_NO:
                    {
                        unset($_SESSION["ruta_logo"]);
                        unset($_SESSION["ruta_logo_pdf"]);
                        unset($_SESSION["url_logo"]);
                        break;
                    }
                }
            }

            // Título de la web
            if ($titulo_web_defecto == true)
            {
                $_SESSION["titulo_web"] = $titulo_web;
            }

            // Tema de colores
            if ($tema_defecto == true)
            {
                $_SESSION["tema"] = $tema;
            }

            // Paleta de colores de las gráficas
            if ($paleta_colores_graficas_defecto == true)
            {
                $_SESSION["paleta_colores_graficas"] = $paleta_colores_graficas;
            }
        }
        else
        {
            if ($logo_defecto == true)
            {
                unset($_SESSION["ruta_logo"]);
                unset($_SESSION["url_logo"]);
                unset($_SESSION["ruta_logo_pdf"]);
            }
            if ($titulo_web_defecto == true)
            {
                $_SESSION["titulo_web"] = TITULO_WEB;
            }
            if ($tema_defecto == true)
            {
                $_SESSION["tema"] = TEMA_DEFECTO;
            }
            if ($paleta_colores_graficas_defecto == true)
            {
                $_SESSION["paleta_colores_graficas"] = PALETA_COLORES_GRAFICAS_DEFECTO;
            }
        }
    }


    // Establece los parametros locales por defecto
    function establece_parametros_locales_defecto()
    {
        // Formatos de fecha local
        establece_formatos_fecha_local(TIPO_FORMATO_FECHA_LOCAL_DIA_MES_ANYO);

        // Formato de números
        $_SESSION["separador_miles"] = SEPARADOR_MILES_DEFECTO;
        $_SESSION["punto_decimal"] = PUNTO_DECIMAL_DEFECTO;

        // Unidades
        $_SESSION["moneda"] = MONEDA_DEFECTO;
        $_SESSION["unidad_medida_temperatura"] = UNIDAD_MEDIDA_TEMPERATURA_DEFECTO;
        $_SESSION["unidad_medida_velocidad"] = UNIDAD_MEDIDA_VELOCIDAD_DEFECTO;

        // Países de tarifas
        $_SESSION["pais_tarifas_electricas"] = PAIS_TARIFAS_ELECTRICAS_DEFECTO;
        $_SESSION["pais_tarifas_gas"] = PAIS_TARIFAS_GAS_DEFECTO;
        $_SESSION["pais_tarifas_agua"] = PAIS_TARIFAS_AGUA_DEFECTO;

        // Medición por defecto
        $_SESSION["medicion_defecto"] = MEDICION_DEFECTO;
    }


    // Carga los colores del tema actual
    function carga_colores_tema_actual()
    {
        switch ($_SESSION["tema"])
        {
            case TEMA_DEFECTO:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_DEFECTO_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_DEFECTO_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_DEFECTO_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_DEFECTO_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_DEFECTO_FONDO_CLARO;
                break;
            }
            case TEMA_NARANJA:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_NARANJA_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_NARANJA_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_NARANJA_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_NARANJA_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_NARANJA_FONDO_CLARO;
                break;
            }
            case TEMA_ROJO:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_ROJO_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_ROJO_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_ROJO_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_ROJO_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_ROJO_FONDO_CLARO;
                break;
            }
            case TEMA_TURQUESA:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_TURQUESA_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_TURQUESA_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_TURQUESA_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_TURQUESA_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_TURQUESA_FONDO_CLARO;
                break;
            }
            case TEMA_AZUL_CLARO:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_AZUL_CLARO_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_AZUL_CLARO_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_AZUL_CLARO_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_AZUL_CLARO_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_AZUL_CLARO_FONDO_CLARO;
                break;
            }
            case TEMA_AZUL:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_AZUL_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_AZUL_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_AZUL_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_AZUL_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_AZUL_FONDO_CLARO;
                break;
            }
            case TEMA_MORADO:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_MORADO_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_MORADO_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_MORADO_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_MORADO_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_MORADO_FONDO_CLARO;
                break;
            }
            case TEMA_MAGENTA:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_MAGENTA_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_MAGENTA_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_MAGENTA_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_MAGENTA_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_MAGENTA_FONDO_CLARO;
                break;
            }
            case TEMA_VERDE:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_VERDE_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_VERDE_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_VERDE_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_VERDE_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_VERDE_FONDO_CLARO;
                break;
            }
            case TEMA_MARRON:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_MARRON_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_MARRON_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_MARRON_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_MARRON_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_MARRON_FONDO_CLARO;
                break;
            }
            case TEMA_GRIS:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_GRIS_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_GRIS_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_GRIS_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_GRIS_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_GRIS_FONDO_CLARO;
                break;
            }
            case TEMA_NEGRO:
            {
                $_SESSION["colores"]["color_tema_oscuro"] = COLOR_TEMA_NEGRO_OSCURO;
                $_SESSION["colores"]["color_tema_intermedio"] = COLOR_TEMA_NEGRO_INTERMEDIO;
                $_SESSION["colores"]["color_tema_claro"] = COLOR_TEMA_NEGRO_CLARO;
                $_SESSION["colores"]["color_tema_fondo"] = COLOR_TEMA_NEGRO_FONDO;
                $_SESSION["colores"]["color_tema_fondo_claro"] = COLOR_TEMA_NEGRO_FONDO_CLARO;
                break;
            }
            default:
            {
                throw new Exception("Tema desconocido: '".$_SESSION["tema"]);
            }
        }
    }


    // Realiza acciones antes de la carga de la página inicial
    function realiza_acciones_extra_antes_carga_pagina_inicial()
    {
        if ($_SESSION["id_red"] == ID_NINGUNO)
        {
            // Se establecen las preferencias y los parámetros locales por defecto
            establece_todas_preferencias_defecto();
            establece_parametros_locales_defecto();
        }
        else
        {
            // Se establecen las preferencias y los parámetros locales de la red
            establece_preferencias_red($_SESSION["id_red"], NULL);
            establece_parametros_locales_red($_SESSION["id_red"], NULL);
        }
    }
?>
