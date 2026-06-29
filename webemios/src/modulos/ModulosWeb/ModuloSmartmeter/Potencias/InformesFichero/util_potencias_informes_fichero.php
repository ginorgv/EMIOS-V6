<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_potencias($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO:
            {
                $titulo_informe = "Informe de optimización de potencias automático";
                break;
            }
            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL:
            {
                $titulo_informe = "Informe de optimización de potencias manual";
                break;
            }
            case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO:
            {
                $titulo_informe = "Informe de simulación de potencias automático";
                break;
            }
            case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL:
            {
                $titulo_informe = "Informe de simulación de potencias manual";
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


    // Parámetros del fichero del informe de optimización de potencias automático
    function dame_html_parametros_tipo_informe_fichero_smartmeter_optimizador_potencias_automatico($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $granularidad = $parametros_informe["granularidad"];
        $rango_potencias = $parametros_informe["rango_potencias"];
        $diferencia_potencia = $parametros_informe["diferencia_potencia"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Granularidad").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_granularidad($granularidad)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Rango de potencias").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_rango_potencias($rango_potencias)."</td></tr>";
        if ($diferencia_potencia != 0)
        {
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Diferencia de potencia").":</b></td><td class='contenido-parametro-informe-fichero'>".$diferencia_potencia." ".$idiomas->_("kW")."</td></tr>";
            $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal")." (".$idiomas->_("diferencia de potencia").")", $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas")." (".$idiomas->_("diferencia de potencia").")", $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas")." (".$idiomas->_("diferencia de potencia").")", $cadena_inclusion_fechas);
        }
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$fecha_fin."</div>";
        $html .= "<div id='granularidad_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$granularidad."</div>";
        $html .= "<div id='rango_potencias_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$rango_potencias."</div>";
        $html .= "<div id='diferencia_potencia_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$diferencia_potencia."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de optimización de potencias manual
    function dame_html_parametros_tipo_informe_fichero_smartmeter_optimizador_potencias_manual($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $ruta_fichero_potencias_maximas = $parametros_informe["ruta_fichero_potencias_maximas"];
        $nombre_fichero_potencias_maximas = $parametros_informe["nombre_fichero_potencias_maximas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fichero de potencias máximas").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_fichero_potencias_maximas, ENT_QUOTES)."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='ruta_fichero_potencias_maximas_smartmeter_informe_fichero_optimizador_potencias_automatico' hidden>".htmlspecialchars($ruta_fichero_potencias_maximas, ENT_QUOTES)."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de simulación de potencias automático
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_potencias_automatico($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $granularidad = $parametros_informe["granularidad"];
        $rango_potencias = $parametros_informe["rango_potencias"];
        $diferencia_potencia = $parametros_informe["diferencia_potencia"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];
        $potencias_tramos = explode(",", $parametros_informe["potencias_tramos"]);

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Granularidad").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_granularidad($granularidad)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Rango de potencias").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_rango_potencias($rango_potencias)."</td></tr>";
        if ($diferencia_potencia != 0)
        {
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Diferencia de potencia").":</b></td><td class='contenido-parametro-informe-fichero'>".$diferencia_potencia." ".$idiomas->_("kW")."</td></tr>";
            $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal")." (".$idiomas->_("diferencia de potencia").")", $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas")." (".$idiomas->_("diferencia de potencia").")", $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas")." (".$idiomas->_("diferencia de potencia").")", $cadena_inclusion_fechas);
        }
        $numero_potencia_tramo = 0;
        foreach ($potencias_tramos AS $potencia_tramo)
        {
            $texto_potencia_tramo = "";
            if ($numero_potencia_tramo == 0)
            {
                $texto_potencia_tramo = $idiomas->_("Potencias").":";
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_potencia_tramo."</b></td><td class='contenido-parametro-informe-fichero'>• ".formatea_numero($potencia_tramo, NUMERO_DECIMALES_POTENCIAS_SELECCIONADAS, true)." ".$idiomas->_("kWh")."</td></tr>";
            $numero_potencia_tramo++;
        }
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$fecha_fin."</div>";
        $html .= "<div id='granularidad_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$granularidad."</div>";
        $html .= "<div id='rango_potencias_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$rango_potencias."</div>";
        $html .= "<div id='diferencia_potencia_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$diferencia_potencia."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>".$cadena_inclusion_fechas."</div>";
        $lista_potencias_tramos_oculta = "<ul id='potencias_tramos_smartmeter_informe_fichero_simulador_potencias_automatico' hidden>";
        foreach ($potencias_tramos AS $potencia_tramo)
        {
            $lista_potencias_tramos_oculta .= "<li>".$potencia_tramo."</li>";
        }
        $lista_potencias_tramos_oculta .= "</ul>";
        $html .= $lista_potencias_tramos_oculta;
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de simulación de potencias manual
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_potencias_manual($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $ruta_fichero_potencias_maximas = $parametros_informe["ruta_fichero_potencias_maximas"];
        $potencias_tramos = explode(",", $parametros_informe["potencias_tramos"]);
        $nombre_fichero_potencias_maximas = $parametros_informe["nombre_fichero_potencias_maximas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fichero de potencias máximas").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_fichero_potencias_maximas, ENT_QUOTES)."</td></tr>";
        $numero_potencia_tramo = 0;
        foreach ($potencias_tramos AS $potencia_tramo)
        {
            $texto_potencia_tramo = "";
            if ($numero_potencia_tramo == 0)
            {
                $texto_potencia_tramo = $idiomas->_("Potencias").":";
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_potencia_tramo."</b></td><td class='contenido-parametro-informe-fichero'>• ".formatea_numero($potencia_tramo, NUMERO_DECIMALES_POTENCIAS_SELECCIONADAS, true)." ".$idiomas->_("kWh")."</td></tr>";
            $numero_potencia_tramo++;
        }
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_potencias_manual' hidden>".$id_tarifa."</div>";
        $html .= "<div id='ruta_fichero_potencias_maximas_smartmeter_informe_fichero_simulador_potencias_manual' hidden>".htmlspecialchars($ruta_fichero_potencias_maximas, ENT_QUOTES)."</div>";
        $lista_potencias_tramos_oculta = "<ul id='potencias_tramos_smartmeter_informe_fichero_simulador_potencias_manual' hidden>";
        foreach ($potencias_tramos AS $potencia_tramo)
        {
            $lista_potencias_tramos_oculta .= "<li>".$potencia_tramo."</li>";
        }
        $lista_potencias_tramos_oculta .= "</ul>";
        $html .= $lista_potencias_tramos_oculta;
        $html .= "</div>";

        return ($html);
    }
?>
