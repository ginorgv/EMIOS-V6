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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_autoconsumo($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO:
            {
                $titulo_informe = "Informe de simulación de autoconsumo";
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


    // Parámetros del fichero del informe de simulación de autoconsumo
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_autoconsumo($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Se recuperan las características de las tarifas
        $medicion = $parametros_informe["medicion"];
        $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

        // Parámetros del informe
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_sensor_generacion = $parametros_informe["id_sensor_generacion"];
        $nombre_sensor_generacion = dame_nombre_sensor($id_sensor_generacion);
        if ($caracteristicas_tarifas["curva_coste"] == true)
        {
            $id_tarifa = $parametros_informe["id_tarifa"];
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        }
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $tipo_autoconsumo = $parametros_informe["tipo_autoconsumo"];
        $capacidad_acumulacion = $parametros_informe["capacidad_acumulacion"];
        $factor_multiplicacion_generacion = $parametros_informe["factor_multiplicacion_generacion"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor de generación").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor_generacion, ENT_QUOTES)."</td></tr>";
        if ($caracteristicas_tarifas["curva_coste"] == true)
        {
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").": "."</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo de autoconsumo").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_tipo_autoconsumo($tipo_autoconsumo)."</td></tr>";
        if ($tipo_autoconsumo == TIPO_AUTOCONSUMO_CON_ACUMULACION)
        {
            $clase_sensor = dame_clase_sensor_medicion($medicion);
            $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Capacidad de acumulación").":</b></td><td class='contenido-parametro-informe-fichero'>".$capacidad_acumulacion." ".$unidad_medida_consumo."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Factor de multiplicación de generación").":</b></td><td class='contenido-parametro-informe-fichero'>".$factor_multiplicacion_generacion."</td></tr>";
        $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$medicion."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_sensor_generacion_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$id_sensor_generacion."</div>";
        $html .= "<div id='nombre_sensor_generacion_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".htmlspecialchars($nombre_sensor_generacion, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$id_tarifa."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$hora_fin."</div>";
        $html .= "<div id='tipo_autoconsumo_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$tipo_autoconsumo."</div>";
        $html .= "<div id='capacidad_acumulacion_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$capacidad_acumulacion."</div>";
        $html .= "<div id='factor_multiplicacion_generacion_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$factor_multiplicacion_generacion."</div>";
        $html .= "<div id='horario_semanal_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_smartmeter_informe_fichero_simulador_autoconsumo' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
