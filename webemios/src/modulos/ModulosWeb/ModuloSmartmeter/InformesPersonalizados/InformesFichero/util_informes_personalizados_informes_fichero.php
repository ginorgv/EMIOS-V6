<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/util_informes_informes_personalizados.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados($tipo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
            {
                $titulo_informe = "Estudio general";
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


    // Parámetros del informe de estudio general
    function dame_html_parametros_tipo_informe_fichero_smartmeter_estudio_general($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe automático
        $medicion = $parametros_informe["medicion"];
        $id_ratio = $parametros_informe["id_ratio"];
        $id_sensor = $parametros_informe["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $apartados = explode(",", $parametros_informe["apartados"]);
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $ruta_fichero_parametros_tipo_json = $parametros_informe["ruta_fichero_parametros_tipo_json"];
        if ($ruta_fichero_parametros_tipo_json != "")
        {
            // Nota desarrollo: Establecer aquí los parámetros de tipo json a utilizar para desarrollo
            /*$cadena_parametros_tipo_json = '{
                "texto_introduccion": ""
            }';*/
            $cadena_parametros_tipo_json = file_get_contents($ruta_fichero_parametros_tipo_json);
        }

        // Tabla con los parámetros del informe fichero
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Medición").":</b></td><td class='contenido-parametro-informe-fichero'>".dame_descripcion_medicion($medicion)."</td></tr>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Ratio").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_ratio, ENT_QUOTES)."</td></tr>";
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Sensor").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</td></tr>";
        $numero_apartado = 0;
        foreach ($apartados AS $apartado)
        {
            $texto_apartados = "";
            if ($numero_apartado == 0)
            {
                $texto_apartados = $idiomas->_("Apartados").":";
            }
            $nombre_apartado = dame_descripcion_apartado_estudio_general($medicion, $apartado);
            $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_apartados."</b></td><td class='contenido-parametro-informe-fichero'>• ".$nombre_apartado."</td></tr>";
            $numero_apartado++;
        }
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='medicion_smartmeter_informe_fichero_estudio_general' hidden>".$medicion."</div>";
        $html .= "<div id='id_ratio_smartmeter_informe_fichero_estudio_general' hidden>".$id_ratio."</div>";
        $html .= "<div id='id_sensor_smartmeter_informe_fichero_estudio_general' hidden>".$id_sensor."</div>";
        $html .= "<div id='nombre_sensor_smartmeter_informe_fichero_estudio_general' hidden>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</div>";
        $lista_apartados_oculta = "<ul id='apartados_smartmeter_informe_fichero_estudio_general' hidden>";
        foreach ($apartados AS $apartado)
        {
            $lista_apartados_oculta .= "<li>".$apartado."</li>";
        }
        $lista_apartados_oculta .= "</ul>";
        $html .= $lista_apartados_oculta;
        $html .= "<div id='parametros_tipo_json_smartmeter_informe_fichero_estudio_general' hidden>".htmlspecialchars($cadena_parametros_tipo_json, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_smartmeter_informe_fichero_estudio_general' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='fecha_fin_smartmeter_informe_fichero_estudio_general' hidden>".$fecha_fin."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
