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
    function dame_html_cabecera_informe_fichero_smartmeter_caudales($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO:
            {
                $titulo_informe = "Informe de optimización de caudales automático";
                break;
            }
            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL:
            {
                $titulo_informe = "Informe de optimización de caudales manual";
                break;
            }
            case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO:
            {
                $titulo_informe = "Informe de simulación de caudales automático";
                break;
            }
            case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL:
            {
                $titulo_informe = "Informe de simulación de caudales manual";
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


    // Parámetros del fichero del informe de optimización de caudales automático
    function dame_html_parametros_tipo_informe_fichero_smartmeter_optimizador_caudales_automatico($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_GAS);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $diferencia_caudal = $parametros_informe["diferencia_caudal"];
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
        if ($diferencia_caudal != 0)
        {
            // Selección de país
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $nombre_parametro_diferencia_caudal = $idiomas->_("Diferencia de caudal diario");
                    $sufijo_nombre_parametros_horario_semanal_fechas = $idiomas->_("diferencia de caudal diario");
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".htmlspecialchars($nombre_parametro_diferencia_caudal).":</b></td><td class='contenido-parametro-informe-fichero'>".$diferencia_caudal." ".$idiomas->_("kWh")."</td></tr>";
            $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_inclusion_fechas);
        }
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$fecha_fin."</div>";
        $html .= "<div id='diferencia_caudal_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$diferencia_caudal."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de optimización de caudales manual
    function dame_html_parametros_tipo_informe_fichero_smartmeter_optimizador_caudales_manual($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_GAS);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $ruta_fichero_caudales_maximos = $parametros_informe["ruta_fichero_caudales_maximos"];
        $nombre_fichero_caudales_maximos = $parametros_informe["nombre_fichero_caudales_maximos"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fichero de caudales máximos").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_fichero_caudales_maximos, ENT_QUOTES)."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='ruta_fichero_caudales_maximos_smartmeter_informe_fichero_optimizador_caudales_automatico' hidden>".htmlspecialchars($ruta_fichero_caudales_maximos, ENT_QUOTES)."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de simulación de caudales automático
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_caudales_automatico($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_GAS);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $caudal = $parametros_informe["caudal"];
        $diferencia_caudal = $parametros_informe["diferencia_caudal"];
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
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Caudal diario").":</b></td><td class='contenido-parametro-informe-fichero'>".formatea_numero($caudal, NUMERO_DECIMALES_CAUDALES_SELECCIONADOS)." ".$idiomas->_("kWh")."</td></tr>";
        if ($diferencia_caudal != 0)
        {
            // Selección de país
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $nombre_parametro_diferencia_caudal = $idiomas->_("Diferencia de caudal diario");
                    $sufijo_nombre_parametros_horario_semanal_fechas = $idiomas->_("diferencia de caudal diario");
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".htmlspecialchars($nombre_parametro_diferencia_caudal).":</b></td><td class='contenido-parametro-informe-fichero'>".$diferencia_caudal." ".$idiomas->_("kWh")."</td></tr>";
            $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas")."<br/>(".$sufijo_nombre_parametros_horario_semanal_fechas.")", $cadena_inclusion_fechas);
        }
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$id_tarifa."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$fecha_fin."</div>";
        $html .= "<div id='caudal_manual_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$caudal."</div>";
        $html .= "<div id='diferencia_caudal_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$diferencia_caudal."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_simulador_caudales_automatico' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }


    // Parámetros del fichero del informe de simulación de caudales manual
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_caudales_manual($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_tarifa = $parametros_informe["id_tarifa"];
        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_GAS);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        $ruta_fichero_caudales_maximos = $parametros_informe["ruta_fichero_caudales_maximos"];
        $caudal = $parametros_informe["caudal"];
        $nombre_fichero_caudales_maximos = $parametros_informe["nombre_fichero_caudales_maximos"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Caudal diario").":</b></td><td class='contenido-parametro-informe-fichero'>".formatea_numero($caudal, NUMERO_DECIMALES_CAUDALES_SELECCIONADOS)." ".$idiomas->_("kWh")."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fichero de caudales máximos").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_fichero_caudales_maximos, ENT_QUOTES)."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_caudales_manual' hidden>".$id_tarifa."</div>";
        $html .= "<div id='ruta_fichero_caudales_maximos_smartmeter_informe_fichero_simulador_caudales_manual' hidden>".htmlspecialchars($ruta_fichero_caudales_maximos, ENT_QUOTES)."</div>";
        $html .= "<div id='caudal_manual_smartmeter_informe_fichero_simulador_caudales_manual' hidden>".$caudal."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
