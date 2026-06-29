<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_facturas($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $titulo_informe = "Simulación de factura";
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


    // Parámetros del informe de simulación de factura
    function dame_html_parametros_tipo_informe_fichero_smartmeter_simulador_factura($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe automático
        $medicion = $parametros_informe["medicion"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_informe["id_tarifa"];
        if ($id_tarifa == ID_NINGUNO)
        {
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);
            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $nombre_tarifa = $idiomas->_("Tarifa vigente según fechas");
            }
            else
            {
                $nombre_tarifa = $idiomas->_("Actual");
            }
        }
        else
        {
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        }
        $cadena_ids_sensores_reparto_costes = $parametros_informe["ids_sensores_reparto_costes"];
        if ($cadena_ids_sensores_reparto_costes == "")
        {
            $ids_sensores_reparto_costes = array();
        }
        else
        {
            $ids_sensores_reparto_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_reparto_costes);
        }
        $nombres_sensores_reparto_costes = dame_nombres_sensores($ids_sensores_reparto_costes);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        if ($cadena_exclusion_fechas === NULL)
        {
            $cadena_exclusion_fechas = "";
        }

        // Tabla con los parámetros del informe fichero
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tarifa").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_tarifa, ENT_QUOTES)."</td></tr>";
        $numero_sensor_reparto_costes = 0;
        foreach ($nombres_sensores_reparto_costes as $nombre_sensor_reparto_costes)
        {
            $texto_sensor = "";
            if ($numero_sensor_reparto_costes == 0)
            {
                if (count($nombres_sensores_reparto_costes) == 1)
                {
                    $texto_sensor = $idiomas->_("Sensor de reparto de costes").":";
                }
                else
                {
                    $texto_sensor = $idiomas->_("Sensores de reparto de costes").":";
                }
            }
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_sensor."</b></td><td class='contenido-parametro-informe-fichero'>• ".htmlspecialchars($nombre_sensor_reparto_costes, ENT_QUOTES)."</td></tr>";
            $numero_sensor_reparto_costes++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_simulador_factura' hidden>".$medicion."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_simulador_factura' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_simulador_factura' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $html .= "<div id='id_tarifa_smartmeter_informe_fichero_simulador_factura' hidden>".$id_tarifa."</div>";
        $lista_ids_sensores_reparto_costes_oculta = "<ul id='ids_sensores_reparto_costes_smartmeter_informe_fichero_simulador_factura' hidden>";
        foreach ($ids_sensores_reparto_costes AS $id_sensor_reparto_costes)
        {
            $lista_ids_sensores_reparto_costes_oculta .= "<li>".$id_sensor_reparto_costes."</li>";
        }
        $lista_ids_sensores_reparto_costes_oculta .= "</ul>";
        $html .= $lista_ids_sensores_reparto_costes_oculta;
        $lista_nombres_sensores_reparto_costes_oculta = "<ul id='nombres_sensores_reparto_costes_smartmeter_informe_fichero_simulador_factura' hidden>";
        foreach ($nombres_sensores_reparto_costes AS $nombre_sensor_reparto_costes)
        {
            $lista_nombres_sensores_reparto_costes_oculta .= "<li>".htmlspecialchars($nombre_sensor_reparto_costes, ENT_QUOTES)."</li>";
        }
        $lista_nombres_sensores_reparto_costes_oculta .= "</ul>";
        $html .= $lista_nombres_sensores_reparto_costes_oculta;
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_simulador_factura' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_simulador_factura' hidden>".$fecha_fin."</div>";
        $html .= "<div id='exclusion_fechas_smartmeter_informe_fichero_simulador_factura' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
