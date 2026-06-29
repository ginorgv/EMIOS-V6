<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_energia_reactiva($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES:
            {
                $titulo_informe = "Informe de simulación de batería de condensadores";
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


    // Parámetros del fichero del informe de simulación de batería de condensadores
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_bateria_condensadores($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $diferencia_capacidad = $parametros_informe["diferencia_capacidad"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Diferencia de capacidad").":</b></td><td class='contenido-parametro-informe-fichero'>".$diferencia_capacidad." ".$idiomas->_("kVAr")."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$hora_fin."</div>";
        $html .= "<div id='diferencia_capacidad_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$diferencia_capacidad."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_simulador_bateria_condensadores' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
