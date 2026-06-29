<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_parametros.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    //
    // Funciones que devuelven código HTML para la generación de los ficheros de los informes
    //


    // Devuelve el código HTML para la cabecera de una página de un informe
    function dame_html_cabecera_informe_fichero_personal_plantillas_informes($tipo_informe, $titulo_informe)
    {
        switch ($tipo_informe)
        {
            case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
            {
                if ($titulo_informe === NULL)
                {
                    $titulo_informe = "Informe de plantilla de informe";
                    $traducir_titulo_informe = true;
                }
                else
                {
                    $traducir_titulo_informe = false;
                }
                break;
            }
            default:
            {
                $titulo_informe = "Informe desconocido";
                break;
            }
        }
        $html = dame_html_cabecera_informe_fichero($titulo_informe, $traducir_titulo_informe);
        return ($html);
    }


    // Parámetros del fichero del informe de plantilla de informe
    function dame_html_parametros_tipo_informe_fichero_personal_informe_plantilla_informe($parametros_informe)
    {
        $idiomas = new Idiomas();

        // Parámetros del informe
        $id_plantilla_informe = $parametros_informe["id_plantilla_informe"];
        $nombre_plantilla_informe = dame_nombre_plantilla_informe($id_plantilla_informe);
        $ids_parametros = array();
        $valores_parametros = array();
        $ids_elementos_portada = array();
        $ids_elementos_titulo = array();
        $ids_elementos_texto = array();
        $ids_elementos_notas = array();
        $ids_elementos_imagen = array();
        if ($parametros_informe["ids_parametros"] != "")
        {
            $ids_parametros = explode(",", $parametros_informe["ids_parametros"]);
        }
        if ($parametros_informe["valores_parametros"] != "")
        {
            $valores_parametros = explode(",", $parametros_informe["valores_parametros"]);
        }
        if ($parametros_informe["ids_elementos_portada"] != "")
        {
            $ids_elementos_portada = explode(",", $parametros_informe["ids_elementos_portada"]);
        }
        if ($parametros_informe["ids_elementos_titulo"] != "")
        {
            $ids_elementos_titulo = explode(",", $parametros_informe["ids_elementos_titulo"]);
        }
        if ($parametros_informe["ids_elementos_texto"] != "")
        {
            $ids_elementos_texto = explode(",", $parametros_informe["ids_elementos_texto"]);
        }
        if ($parametros_informe["ids_elementos_notas"] != "")
        {
            $ids_elementos_notas = explode(",", $parametros_informe["ids_elementos_notas"]);
        }
        if ($parametros_informe["ids_elementos_imagen"] != "")
        {
            $ids_elementos_imagen = explode(",", $parametros_informe["ids_elementos_imagen"]);
        }
        $fecha_inicio = $parametros_informe["fecha_inicio"];
        $hora_inicio = $parametros_informe["hora_inicio"];
        $fecha_fin = $parametros_informe["fecha_fin"];
        $hora_fin = $parametros_informe["hora_fin"];
        $cadena_horario_semanal = $parametros_informe["horario_semanal"];
        $cadena_exclusion_fechas = $parametros_informe["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros_informe["inclusion_fechas"];
        $ruta_fichero_parametros_tipo_json = $parametros_informe["ruta_fichero_parametros_tipo_json"];
        if ($ruta_fichero_parametros_tipo_json != "")
        {
            // Nota desarrollo: Establecer aquí los parámetros de tipo json a utilizar para desarrollo
            // (las rutas deben ser relativas siempre)
            /*$cadena_parametros_tipo_json = '{
                "subtitulo_elemento_portada_XXX": "st1",
                "titulo_elemento_titulo_XXX": "t1",
                "texto_elemento_texto_XXX": "t1",
                "ruta_fichero_imagen_elemento_imagen_XXX": "./rsc/ficheros/tmp/_pruebas/XXX.XXX"
            }';*/
            $cadena_parametros_tipo_json = file_get_contents($ruta_fichero_parametros_tipo_json);
        }

        // Tabla con los parámetros del informe
        $html = "<div>";
        $html .= "<div class='titulo-informe-fichero'>".$idiomas->_("Parámetros")."</div>";
        $html .= "<table class='tabla-parametros-informe-fichero'>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Plantilla de informe").":</b></td><td class='contenido-parametro-informe-fichero'>".htmlspecialchars($nombre_plantilla_informe, ENT_QUOTES)."</td></tr>";
        $tipo_plantilla_informe = dame_tipo_plantilla_informe($id_plantilla_informe);
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            if (count($ids_parametros) > 0)
            {
                $numero_parametro = 0;
                for ($i = 0; $i < count($ids_parametros); $i++)
                {
                    $texto_parametro = "";
                    if ($numero_parametro == 0)
                    {
                        if (count($ids_parametros) == 1)
                        {
                            $texto_parametro = $idiomas->_("Parámetro").":";
                        }
                        else
                        {
                            $texto_parametro = $idiomas->_("Parámetros").":";
                        }
                    }
                    $id_parametro = $ids_parametros[$i];
                    $valor_parametro = $valores_parametros[$i];
                    $fila_parametro = dame_fila_parametro_plantilla_informe($id_parametro);
                    $nombre_parametro = $fila_parametro["nombre"];
                    $nombre_valor_parametro = dame_nombre_valor_parametro_plantilla_informe($fila_parametro, $valor_parametro);
                    $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$texto_parametro."</b></td><td class='contenido-parametro-informe-fichero'>• ".$nombre_parametro.": ".$nombre_valor_parametro."</td></tr>";
                    $numero_parametro++;
                }
            }
        }

        // Horario semanal, exclusión e inclusión de fechas
        $tipo_seleccion_horario_semanal_fechas_plantilla_informe = dame_tipo_seleccion_horario_semanal_fechas_plantilla_informe($id_plantilla_informe);
        if ($tipo_seleccion_horario_semanal_fechas_plantilla_informe == TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE)
        {
            $html .= dame_html_parametro_horario_semanal_informe_fichero($idiomas->_("Horario semanal"), $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_fichero($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        }

        // Nota: No se muestra la tabla con los subtítulo de portadas, títulos, textos, notas e imágenes del informe
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_inicio.", ".$hora_inicio."</td></tr>";
        $html .= "<tr><td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":</b></td><td class='contenido-parametro-informe-fichero'>".$fecha_fin.", ".$hora_fin."</td></tr>";
        $html .= "</table>";

        // Se guardan los parámetros del informe ocultos (para poder recuperarlo desde JavaScript)
        $html .= "<div id='id_plantilla_informe_personal_informe_fichero_informe_plantilla_informe' hidden>".$id_plantilla_informe."</div>";
        $lista_ids_parametros_oculta = "<ul id='ids_parametros_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_parametros AS $id_parametro)
        {
            $lista_ids_parametros_oculta .= "<li>".$id_parametro."</li>";
        }
        $lista_ids_parametros_oculta .= "</ul>";
        $html .= $lista_ids_parametros_oculta;
        $lista_valores_parametros_oculta = "<ul id='valores_parametros_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($valores_parametros AS $valor_parametro)
        {
            $lista_valores_parametros_oculta .= "<li>".$valor_parametro."</li>";
        }
        $lista_valores_parametros_oculta .= "</ul>";
        $html .= $lista_valores_parametros_oculta;
        $lista_ids_elementos_portada_oculta = "<ul id='ids_elementos_portada_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_elementos_portada AS $id_elemento_portada)
        {
            $lista_ids_elementos_portada_oculta .= "<li>".$id_elemento_portada."</li>";
        }
        $lista_ids_elementos_portada_oculta .= "</ul>";
        $html .= $lista_ids_elementos_portada_oculta;
        $lista_ids_elementos_titulo_oculta = "<ul id='ids_elementos_titulo_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_elementos_titulo AS $id_elemento_titulo)
        {
            $lista_ids_elementos_titulo_oculta .= "<li>".$id_elemento_titulo."</li>";
        }
        $lista_ids_elementos_titulo_oculta .= "</ul>";
        $html .= $lista_ids_elementos_titulo_oculta;
        $lista_ids_elementos_texto_oculta = "<ul id='ids_elementos_texto_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_elementos_texto AS $id_elemento_texto)
        {
            $lista_ids_elementos_texto_oculta .= "<li>".$id_elemento_texto."</li>";
        }
        $lista_ids_elementos_texto_oculta .= "</ul>";
        $html .= $lista_ids_elementos_texto_oculta;
        $lista_ids_elementos_notas_oculta = "<ul id='ids_elementos_notas_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_elementos_notas AS $id_elemento_notas)
        {
            $lista_ids_elementos_notas_oculta .= "<li>".$id_elemento_notas."</li>";
        }
        $lista_ids_elementos_notas_oculta .= "</ul>";
        $html .= $lista_ids_elementos_notas_oculta;
        $lista_ids_elementos_imagen_oculta = "<ul id='ids_elementos_imagen_personal_informe_fichero_informe_plantilla_informe' hidden>";
        foreach ($ids_elementos_imagen AS $id_elemento_imagen)
        {
            $lista_ids_elementos_imagen_oculta .= "<li>".$id_elemento_imagen."</li>";
        }
        $lista_ids_elementos_imagen_oculta .= "</ul>";
        $html .= $lista_ids_elementos_imagen_oculta;
        $html .= "<div id='parametros_tipo_json_personal_informe_fichero_informe_plantilla_informe' hidden>".htmlspecialchars($cadena_parametros_tipo_json, ENT_QUOTES)."</div>";
        $html .= "<div id='fecha_inicio_personal_informe_fichero_informe_plantilla_informe' hidden>".$fecha_inicio."</div>";
        $html .= "<div id='hora_inicio_personal_informe_fichero_informe_plantilla_informe' hidden>".$hora_inicio."</div>";
        $html .= "<div id='fecha_fin_personal_informe_fichero_informe_plantilla_informe' hidden>".$fecha_fin."</div>";
        $html .= "<div id='hora_fin_personal_informe_fichero_informe_plantilla_informe' hidden>".$hora_fin."</div>";
        $html .= "<div id='horario_semanal_personal_informe_fichero_informe_plantilla_informe' hidden>".$cadena_horario_semanal."</div>";
        $html .= "<div id='exclusion_fechas_personal_informe_fichero_informe_plantilla_informe' hidden>".$cadena_exclusion_fechas."</div>";
        $html .= "<div id='inclusion_fechas_personal_informe_fichero_informe_plantilla_informe' hidden>".$cadena_inclusion_fechas."</div>";
        $html .= "</div>";

        return ($html);
    }
?>
