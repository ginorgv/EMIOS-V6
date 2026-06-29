<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_parametros.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO", 5);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN", 6);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN", 7);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL", 8);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS", 9);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS", 10);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de plantilla de informe
    function dame_html_parametros_tipo_informe_automatico_personal_informe_plantilla_informe($id_informe_automatico, $cadena_parametros_tipo, $parametros_tipo_json)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_plantilla_informe = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
        $cadena_ids_parametros = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
        if ($cadena_ids_parametros == "")
        {
            $ids_parametros = array();
        }
        else
        {
            $ids_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros);
        }
        $cadena_valores_parametros = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
        if ($cadena_valores_parametros == "")
        {
            $valores_parametros = array();
        }
        else
        {
            $valores_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_valores_parametros);
        }
        $cadena_ids_elementos_portada = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA];
        if ($cadena_ids_elementos_portada == "")
        {
            $ids_elementos_portada = array();
        }
        else
        {
            $ids_elementos_portada = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_portada);
        }
        $cadena_ids_elementos_titulo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO];
        if ($cadena_ids_elementos_titulo == "")
        {
            $ids_elementos_titulo = array();
        }
        else
        {
            $ids_elementos_titulo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_titulo);
        }
        $cadena_ids_elementos_texto = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
        if ($cadena_ids_elementos_texto == "")
        {
            $ids_elementos_texto = array();
        }
        else
        {
            $ids_elementos_texto = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_texto);
        }
        $cadena_ids_elementos_imagen = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
        if ($cadena_ids_elementos_imagen == "")
        {
            $ids_elementos_imagen = array();
        }
        else
        {
            $ids_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen);
        }
        $cadena_imagenes_personalizadas_elementos_imagen = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
        if ($cadena_ids_elementos_imagen == "")
        {
            $imagenes_personalizadas_elementos_imagen = array();
        }
        else
        {
            $imagenes_personalizadas_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_imagenes_personalizadas_elementos_imagen);
        }
        $cadena_horario_semanal = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
        $cadena_exclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
        $cadena_inclusion_fechas = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];
        $parametros_json = json_decode_caracteres_especiales($parametros_tipo_json);

        // Información de plantilla de informe
        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $tipo_plantilla_informe = $fila_plantilla_informe["tipo"];
        $tipo_seleccion_horario_semanal_fechas_plantilla_informe = $fila_plantilla_informe["tipo_seleccion_horario_semanal_fechas"];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Plantilla de informe").": ".dame_nombre_plantilla_informe($id_plantilla_informe)."</li>";

        // Parámetros
        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_CONFIGURABLE)
        {
            // Subtítulos de portadas
            $html_subtitulos_portadas = "";
            if (count($ids_elementos_portada) > 0)
            {
                $nombre_parametros_tipo_elementos_portada = dame_nombre_parametros_tipo_elementos_plantilla_informe($id_plantilla_informe, TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA);
                $numero_portada = 0;
                $html_subtitulos_portadas .= "
                    <ul>";
                foreach ($ids_elementos_portada as $id_elemento_portada)
                {
                    $nombre_parametros_tipo_elemento_portada = $nombre_parametros_tipo_elementos_portada[$id_elemento_portada];
                    $titulo_elemento_portada = $nombre_parametros_tipo_elemento_portada["titulo"];
                    $subtitulo_elemento_portada = $parametros_json["subtitulo_elemento_portada_".$id_elemento_portada];
                    $html_subtitulos_portadas .= "
                        <li>".htmlspecialchars($titulo_elemento_portada, ENT_QUOTES).": ".htmlspecialchars($subtitulo_elemento_portada, ENT_QUOTES)."</li>";
                    $numero_portada += 1;
                    if ($numero_portada == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
                    {
                        $html_subtitulos_portadas .= "<li>...</li>";
                        break;
                    }
                }
                $html_subtitulos_portadas .= "
                    </ul>";
            }
            if ($html_subtitulos_portadas != "")
            {
                $html .= "<li>".$idiomas->_("Subtítulos de portadas").": ";
                $html .= $html_subtitulos_portadas;
                $html .= "</li>";
            }

            // Títulos
            $html_titulos = "";
            if (count($ids_elementos_titulo) > 0)
            {
                $nombre_parametros_tipo_elementos_titulo = dame_nombre_parametros_tipo_elementos_plantilla_informe($id_plantilla_informe, TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO);
                $numero_titulo = 0;
                $html_titulos .= "
                    <ul>";
                foreach ($ids_elementos_titulo as $id_elemento_titulo)
                {
                    $nombre_parametros_tipo_elemento_titulo = $nombre_parametros_tipo_elementos_titulo[$id_elemento_titulo];
                    $titulo_elemento_titulo = $nombre_parametros_tipo_elemento_titulo["titulo"];
                    $titulo_elemento_titulo_parametros_json = $parametros_json["titulo_elemento_titulo_".$id_elemento_titulo];
                    $html_titulos .= "
                        <li>".htmlspecialchars($titulo_elemento_titulo, ENT_QUOTES);
                    if ($titulo_elemento_titulo_parametros_json != $titulo_elemento_titulo)
                    {
                        $html_titulos .= ": ".htmlspecialchars($titulo_elemento_titulo_parametros_json, ENT_QUOTES);
                    }
                    $html_titulos .= "</li>";
                    $numero_titulo += 1;
                    if ($numero_titulo == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
                    {
                        $html_titulos .= "<li>...</li>";
                        break;
                    }
                }
                $html_titulos .= "
                    </ul>";
            }
            if ($html_titulos != "")
            {
                $html .= "<li>".$idiomas->_("Títulos").": ";
                $html .= $html_titulos;
                $html .= "</li>";
            }

            // Parámetros
            if (count($ids_parametros) > 0)
            {
                $html .= "<li>".$idiomas->_("Parámetros").":";
                $lista_info_parametros = "<ul>";
                $numero_parametro = 0;
                for ($i = 0; $i < count($ids_parametros); $i++)
                {
                    $id_parametro = $ids_parametros[$i];
                    $valor_parametro = $valores_parametros[$i];
                    $fila_parametro = dame_fila_parametro_plantilla_informe($id_parametro);
                    if($fila_parametro == NULL)
                    {
                    	$nombre_parametro = "Desconocido";
                    	$nombre_valor_parametro = "Desconocido";
                    }
                    else
                    {
                    	$nombre_parametro = $fila_parametro["nombre"];
                    	$nombre_valor_parametro = dame_nombre_valor_parametro_plantilla_informe($fila_parametro, $valor_parametro);
                    }
                    $lista_info_parametros .= "<li>".htmlspecialchars($nombre_parametro, ENT_QUOTES).": ".htmlspecialchars($nombre_valor_parametro, ENT_QUOTES)."</li>";
                    $numero_parametro += 1;
                    if ($numero_parametro == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
                    {
                        $lista_info_parametros .= "<li>...</li>";
                        break;
                    }
                }
                $lista_info_parametros .= "</ul>";
                $html .= $lista_info_parametros;
            }
        }

        // Textos
        $html_textos = "";
        if (count($ids_elementos_texto) > 0)
        {
            $nombre_parametros_tipo_elementos_texto = dame_nombre_parametros_tipo_elementos_plantilla_informe($id_plantilla_informe, TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO);
            $numero_texto = 0;
            $html_textos .= "
                <ul>";
            foreach ($ids_elementos_texto as $id_elemento_texto)
            {
                $nombre_parametros_tipo_elemento_texto = $nombre_parametros_tipo_elementos_texto[$id_elemento_texto];
                $titulo_elemento_texto = $nombre_parametros_tipo_elemento_texto["titulo"];
                $texto_elemento_texto = $parametros_json["texto_elemento_texto_".$id_elemento_texto];
                $html_textos .= "
                    <li>".htmlspecialchars($titulo_elemento_texto, ENT_QUOTES).":"."</li>";
                $html_textos .= "
                    <div class='contenedor-textarea-detalle-tabla-datos'>
                        <textarea class='area-entrada-texto-detalles-informe' rows='1' disabled>".htmlspecialchars($texto_elemento_texto, ENT_QUOTES)."</textarea>
                    </div>";
                $numero_texto += 1;
                if ($numero_texto == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
                {
                    $html_textos .= "<li>...</li>";
                    break;
                }
            }
            $html_textos .= "
                </ul>";
        }
        if ($html_textos != "")
        {
            $html .= "<li>".$idiomas->_("Textos").": ";
            $html .= $html_textos;
            $html .= "</li>";
        }

        // Imágenes
        $html_imagenes = "";
        if (count($ids_elementos_imagen) > 0)
        {
            $nombre_parametros_tipo_elementos_imagen = dame_nombre_parametros_tipo_elementos_plantilla_informe($id_plantilla_informe, TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN);
            $numero_imagen = 0;
            $html_imagenes .= "
                <ul>";
            foreach ($ids_elementos_imagen as $id_elemento_imagen)
            {
                $nombre_parametros_tipo_elemento_imagen = $nombre_parametros_tipo_elementos_imagen[$id_elemento_imagen];
                $titulo_elemento_imagen = $nombre_parametros_tipo_elemento_imagen["titulo"];
                $nombre_imagen_elemento_imagen = $nombre_parametros_tipo_elemento_imagen["nombre_imagen"];

                $indice_parametro_id_elemento_imagen = array_search($id_elemento_imagen, $ids_elementos_imagen);
                if (($indice_parametro_id_elemento_imagen === false) || ($indice_parametro_id_elemento_imagen === NULL))
                {
                    throw new Exception("Parámetro de elemento imagen no encontrado");
                }

                $html_imagenes .= "
                    <li>".htmlspecialchars($titulo_elemento_imagen, ENT_QUOTES).": ";
                $imagen_personalizada_elemento_imagen = $imagenes_personalizadas_elementos_imagen[$indice_parametro_id_elemento_imagen];
                if ($imagen_personalizada_elemento_imagen == VALOR_NO)
                {
                    $html_imagenes .= $idiomas->_("Imagen de elemento")." (".htmlspecialchars($nombre_imagen_elemento_imagen, ENT_QUOTES).")";
                }
                else
                {
                    $origen = ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN;
                    $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                        $id_informe_automatico,
                        $id_elemento_imagen));
                    $nombre_ventana = htmlspecialchars($titulo_elemento_imagen, ENT_QUOTES);
                    $html_imagenes .= $idiomas->_("Imagen personalizada")." "."<span class='clickable boton_mostrar_imagen_base_datos_ventana' ".
                        "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>"." (".$idiomas->_("ver imagen").")"."</span>";
                }
                $html_imagenes .= "</li>";
                $numero_imagen += 1;
                if ($numero_imagen == NUMERO_MAXIMO_ELEMENTOS_LISTA_PARAMETROS_INFORMES)
                {
                    $html_imagenes .= "<li>...</li>";
                    break;
                }
            }
            $html_imagenes .= "
                </ul>";
        }
        if ($html_imagenes != "")
        {
            $html .= "<li>".$idiomas->_("Imágenes").": ";
            $html .= $html_imagenes;
            $html .= "</li>";
        }

        // Horario semanal, exclusión e inclusión de fechas
        if ($tipo_seleccion_horario_semanal_fechas_plantilla_informe == TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE)
        {
            $html .= dame_html_parametro_horario_semanal_informe_automatico($idiomas->_("Horario semanal"), $cadena_horario_semanal);
            $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Exclusión de fechas"), $cadena_exclusion_fechas);
            $html .= dame_html_parametro_fechas_informe_automatico($idiomas->_("Inclusión de fechas"), $cadena_inclusion_fechas);
        }
        $html .= "</ul>";

        return ($html);
    }


    //
    // Funciones auxiliares
    //


    function dame_nombre_parametros_tipo_elementos_plantilla_informe($id_plantilla_informe, $tipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_elementos = "
            SELECT
                id,
                nombre,
                parametros_tipo,
                parametros_tipo_json
            FROM elementos_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".$bd_red->_($tipo)."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        $nombre_parametros_tipo_elementos = array();
        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $nombre_elemento = $fila_elemento["nombre"];
            $cadena_parametros_tipo = $fila_elemento["parametros_tipo"];
            $cadena_parametros_tipo_json = $fila_elemento["parametros_tipo_json"];

            $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                $tipo,
                $cadena_parametros_tipo,
                $cadena_parametros_tipo_json);
            $nombre_parametros_tipo_elementos[$id_elemento] = $parametros_tipo;
            $nombre_parametros_tipo_elementos[$id_elemento]["nombre"] = $nombre_elemento;
        }
        return ($nombre_parametros_tipo_elementos);
    }
?>
