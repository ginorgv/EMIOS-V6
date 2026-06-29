<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_elementos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');


    //
    // Funciones de tratamiento de imágenes
    //


    function crea_imagen_satelites($rutas_imagenes_satelite, $ruta_imagen_satelites)
	{
        $numero_imagenes_satelite = count($rutas_imagenes_satelite);
        if ($numero_imagenes_satelite == 0)
        {
            return;
        }

        if (file_exists($rutas_imagenes_satelite[0]) == false)
        {
            throw new Exception("No existe la imagen: '".$rutas_imagenes_satelite[0]."'");
        }

        $tamanyo_satelite = getimagesize($rutas_imagenes_satelite[0]);
        $anchura_satelite = $tamanyo_satelite[0];
        $altura_satelite = $tamanyo_satelite[1];

        $anchura_imagen = $numero_imagenes_satelite * $anchura_satelite;
        $altura_imagen = $altura_satelite;

        $imagen = imagecreatetruecolor($anchura_imagen, $altura_imagen);
        imagesavealpha($imagen, true);
        imagealphablending($imagen, false);
        $color_transparente = imagecolorallocatealpha($imagen, 255, 255, 255, 127);
        imagefill($imagen, 0, 0, $color_transparente);

        // Se recorren los satelites y se van añadiendo a la imagen de los satelites
        $offset_x_satelite = 0;
        for($i = 0; $i < $numero_imagenes_satelite; $i++)
        {
            if (file_exists($rutas_imagenes_satelite[$i]) == false)
            {
                throw new Exception("No existe la imagen: '".$rutas_imagenes_satelite[$i]."'");
            }
            $satelite = imagecreatefrompng($rutas_imagenes_satelite[$i]);

            imageAlphaBlending($satelite, true);
            imageSaveAlpha($satelite, true);
            imagecopy($imagen, $satelite, $offset_x_satelite, 0, 0, 0, $anchura_satelite, $altura_satelite);

            $offset_x_satelite += $anchura_satelite;
        }

        imagepng($imagen, $ruta_imagen_satelites);
        imagedestroy($imagen);
    }


    function crea_imagen_texto($texto_imagen, $ruta_imagen_texto)
	{
        // Fuente para las imágenes de textos (Arial)
        $fuente = $_SESSION["directorio"].'/rsc/fuentes/arial.ttf';

        // Ajustes para el centrado y tamaño correcto de la imagen
        $tamanyo_fuente = 11 + ($_SESSION["tamanyo_letra"] - TAMANYO_LETRA_DEFECTO);
        $margen_imagen = 2;
        $margen_imagen_izquierda_adicional = 2;
        $margen_imagen_inferior_adicional = 2;

        // Tamaño de la imagen del texto
        $rectangulo_imagen_texto = imagettfbbox($tamanyo_fuente, 0, $fuente, $texto_imagen);
        $anchura_imagen_texto = $rectangulo_imagen_texto[2] - $rectangulo_imagen_texto[0];
        $altura_imagen_texto = $rectangulo_imagen_texto[3] - $rectangulo_imagen_texto[5];

        // Se crea la imagen para el texto (con los márgenes correspondientes)
        $imagen = imagecreatetruecolor(
            $margen_imagen_izquierda_adicional + $anchura_imagen_texto + ($margen_imagen * 2),
            $altura_imagen_texto + ($margen_imagen * 2) + $margen_imagen_inferior_adicional);
        $color_blanco = imagecolorallocate($imagen, 255, 255, 255);
        $color_negro = imagecolorallocate($imagen, 0, 0, 0);

        // Se crea la imagen y se convierte a 'png'
        // Nota: Se añade la mitad del margen para el centrado vertical
        imagefill($imagen, 0, 0, $color_blanco);
        imagettftext(
            $imagen,
            $tamanyo_fuente, 0,
            $margen_imagen_izquierda_adicional + $margen_imagen, $tamanyo_fuente + ($margen_imagen * 2),
            $color_negro, $fuente, $texto_imagen);
        imagepng($imagen, $ruta_imagen_texto);
        imagedestroy($imagen);
    }


	function crea_imagen_base_satelites(
        $ruta_imagen_base,
        $ruta_imagen_satelites,
        $ruta_imagen_destino,
        $posicion_imagen_satelites = "ARRIBA")
	{
        if (file_exists($ruta_imagen_base) == false)
        {
            throw new Exception("No existe la imagen: '".$ruta_imagen_base."'");
        }
        if (file_exists($ruta_imagen_satelites) == false)
        {
            throw new Exception("No existe la imagen: '".$ruta_imagen_satelites."'");
        }

		$base = imagecreatefrompng($ruta_imagen_base);
        $satelites = imagecreatefrompng($ruta_imagen_satelites);

        imageAlphaBlending($base, true);
        imageSaveAlpha($base, true);

        imageAlphaBlending($satelites, true);
        imageSaveAlpha($satelites, true);

        $tamanyo_base = getimagesize($ruta_imagen_base);
        $anchura_base = $tamanyo_base[0];
        $altura_base = $tamanyo_base[1];

        $tamanyo_satelites = getimagesize($ruta_imagen_satelites);
        $anchura_satelites = $tamanyo_satelites[0];
        $altura_satelites = $tamanyo_satelites[1];

        $anchura_imagen = $anchura_base > $anchura_satelites? $anchura_base: $anchura_satelites;
        $altura_imagen = $altura_base + $altura_satelites;

        $imagen = imagecreatetruecolor($anchura_imagen, $altura_imagen);
        imagesavealpha($imagen, true);
        imagealphablending($imagen, false);
        $color_transparente = imagecolorallocatealpha($imagen, 255, 255, 255, 127);
        imagefill($imagen, 0, 0, $color_transparente);

        // Se crea la imagen con la base y los satelites para que queden centrados los satelites encima de la imagen base
        if ($anchura_base > $anchura_satelites)
        {
            $offset_x_satelites = ($anchura_base - $anchura_satelites) / 2;
            if ($posicion_imagen_satelites == "ARRIBA")
            {
                imagecopy($imagen, $satelites, $offset_x_satelites, 0, 0, 0, $anchura_satelites, $altura_satelites);
                imagecopy($imagen, $base, 0, $altura_satelites, 0, 0, $anchura_base, $altura_base);
            }
            else
            {
                imagecopy($imagen, $base, 0, 0, 0, 0, $anchura_base, $altura_base);
                imagecopy($imagen, $satelites, $offset_x_satelites, $altura_base, 0, 0, $anchura_satelites, $altura_satelites);
            }
        }
        else
        {
            $offset_x_base = ($anchura_satelites - $anchura_base) / 2;
            if ($posicion_imagen_satelites == "ARRIBA")
            {
                imagecopy($imagen, $satelites, 0, 0, 0, 0, $anchura_satelites, $altura_satelites);
                imagecopy($imagen, $base, $offset_x_base, $altura_satelites, 0, 0, $anchura_base, $altura_base);
            }
            else
            {
                imagecopy($imagen, $base, $offset_x_base, 0, 0, 0, $anchura_base, $altura_base);
                imagecopy($imagen, $satelites, 0, $altura_base, 0, 0, $anchura_satelites, $altura_satelites);
            }
        }

        imagepng($imagen, $ruta_imagen_destino);
        imagedestroy($imagen);
	}


    function redimensiona_imagen($ruta_imagen, $factor)
	{
        if (file_exists($ruta_imagen) == false)
        {
            throw new Exception("No existe la imagen: '".$ruta_imagen."'");
        }

        $tamanyo_imagen = getimagesize($ruta_imagen);
        $anchura_imagen = $tamanyo_imagen[0] * $factor;
        $altura_imagen = $tamanyo_imagen[1] * $factor;

        $imagen = imagecreatetruecolor($anchura_imagen, $altura_imagen);
        imagesavealpha($imagen, true);
        imagealphablending($imagen, false);
        $color_transparente = imagecolorallocatealpha($imagen, 255, 255, 255, 127);
        imagefill($imagen, 0, 0, $color_transparente);

        // Se carga la imagen original y se copia a la imagen destino redimensionada
        $imagen_original = imagecreatefrompng($ruta_imagen);
        imageAlphaBlending($imagen_original, true);
        imageSaveAlpha($imagen_original, true);
        imagecopyresampled($imagen, $imagen_original, 0, 0, 0, 0, $anchura_imagen, $altura_imagen, $anchura_imagen / $factor, $altura_imagen / $factor);

        imagepng($imagen, $ruta_imagen);
        imagedestroy($imagen);
    }


    //
    // Funciones de imágenes
    //


    function comprueba_imagen_correcta(
        $origen,
        $fichero_imagen,
        &$tipo_imagen,
        &$anchura_imagen,
        &$altura_imagen,
        &$msg_error)
    {
        $idiomas = new Idiomas();

        // Nombre de fichero y ruta de fichero (en el lado del cliente)
        $nombre_fichero = $fichero_imagen["name"];
        $ruta_fichero_cliente = $fichero_imagen["tmp_name"];

        // Comprobaciones de imagen correcta:
        // - Comprobación de imagen cargada correctamente
        // - Comprobación de imagen válida
        // - Comprobación de tipo de imagen
        // - Comprobación de dimensiones de imagen según el origen
        $imagen_correcta = true;

        // Comprobación de imagen cargada correctamente
        if ($imagen_correcta == true)
        {
            if ($ruta_fichero_cliente == "")
            {
                $imagen_correcta = false;
                $msg_error = $idiomas->_("No se ha podido cargar el fichero de imagen")."\n".
                    "(".$nombre_fichero.")";
            }
        }

        // Comprobación de imagen válida
        if ($imagen_correcta == true)
        {
            // Se recupera información de la imagen
            $info_imagen = getimagesize($ruta_fichero_cliente);
            if ($info_imagen == false)
            {
                $imagen_correcta = false;
                $msg_error = $idiomas->_("El fichero seleccionado no es una imagen válida")."\n".
                    "(".$nombre_fichero.")";
            }
            else
            {
                $tipo_imagen = $info_imagen["mime"];
                $anchura_imagen = $info_imagen["0"];
                $altura_imagen = $info_imagen["1"];
            }
        }

        // Comprobación de tipo de imagen
        if ($imagen_correcta == true)
        {
            switch ($tipo_imagen)
            {
                case "image/gif":
                case "image/jpeg":
                case "image/png":
                {
                    break;
                }
                default:
                {
                    $imagen_correcta = false;
                    $msg_error = $idiomas->_("El tipo de imagen no está permitido")."\n".
                        "(".$nombre_fichero.")";
                }
            }
        }

        // Comprobación de dimensiones de la imagen según el origen
        if ($imagen_correcta == true)
        {
            $dimensiones_correctas = dame_dimensiones_correctas_imagen(
                $origen,
                $anchura_imagen,
                $altura_imagen,
                $msg_error);
            if ($dimensiones_correctas == false)
            {
                $imagen_correcta = false;
                $msg_error = $idiomas->_("Las dimensiones de la imagen son incorrectas")."\n".
                    "(".$nombre_fichero.")"."\n".
                    "(".$msg_error.")";
            }
        }

        // Se devuelve si la imagen es correcta
        return ($imagen_correcta);
    }


    //
    // Funciones de imágenes de servidor
    //


    function guarda_imagen_servidor_datos_imagen($nombre_fichero, $datos_imagen, $codificacion_imagen)
    {
        // Nombre de fichero
        // (http://php.net/manual/es/function.rawurldecode.php)
        $nombre_fichero = rawurldecode($nombre_fichero);

        // Contenido de la imagen
        switch ($codificacion_imagen)
        {
            case CODIFICACION_BASE_64:
            {
                $datos_imagen = base64_decode($datos_imagen);
                break;
            }
        }

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        // Se guarda el fichero de imagen en el servidor
        $ruta_fichero_imagen_absoluta = $directorio_absoluto_ficheros_temporales_usuario.'/'.$nombre_fichero;
        $fichero = fopen($ruta_fichero_imagen_absoluta, 'w');
        if ($fichero == false)
        {
            throw new Exception("No se ha podido abrir el fichero de imagen: '".$ruta_fichero_imagen_absoluta."'");
        }
        else
        {
            if (fwrite($fichero, $datos_imagen) == true)
            {
                // La ruta del fichero de imagen es relativa al directorio local '.' (el directorio '.' es el directorio raiz en JavaScript)
                $ruta_fichero_imagen_relativa = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero;
            }
            else
            {
                throw new Exception("No se podido escribir el fichero de imagen: '".$ruta_fichero_imagen_absoluta."'");
            }
            fclose($fichero);
        }

        // Se devuelve la ruta del fichero de imagen
        return ($ruta_fichero_imagen_relativa);
    }


    function guarda_imagen_servidor_fichero_imagen($fichero_imagen)
    {
        // Nombre de fichero y ruta de fichero (en el lado del cliente)
        $nombre_fichero = $fichero_imagen["name"];
        $ruta_fichero_cliente = $fichero_imagen["tmp_name"];

        // Se leen los datos de la imagen
        $datos_imagen = file_get_contents($ruta_fichero_cliente);

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        // Se guarda el fichero de imagen en el servidor
        $ruta_fichero_imagen_absoluta = $directorio_absoluto_ficheros_temporales_usuario.'/'.$nombre_fichero;
        $fichero = fopen($ruta_fichero_imagen_absoluta, 'w');
        if ($fichero == false)
        {
            throw new Exception("No se ha podido abrir el fichero de imagen: '".$ruta_fichero_imagen_absoluta."'");
        }
        else
        {
            if (fwrite($fichero, $datos_imagen) == true)
            {
                // La ruta del fichero de imagen es relativa al directorio local '.' (el directorio '.' es el directorio raiz en JavaScript)
                $ruta_fichero_imagen_relativa = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero;
            }
            else
            {
                throw new Exception("No se podido escribir el fichero de imagen: '".$ruta_fichero_imagen_absoluta."'");
            }
            fclose($fichero);
        }

        // Se devuelve la ruta del fichero de imagen
        return ($ruta_fichero_imagen_relativa);
    }


    //
    // Funciones de imágenes de base de datos
    //


    function guarda_imagen_base_datos_fichero_imagen($origen, $id_origen, $fichero_imagen)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Nombre de fichero y ruta de fichero (en el lado del cliente)
        $nombre_fichero = $fichero_imagen["name"];
        $ruta_fichero_cliente = $fichero_imagen["tmp_name"];

        // Comprobaciones antes de guardar la imagen:
        // - Comprobación de permisos según el origen
        // - Comprobación de imagen correcta
        $guardar_imagen = true;

        // Comprobación de permisos según el origen
        if ($guardar_imagen == true)
        {
            $id_origen_visible_usuario_actual = dame_id_origen_imagen_visible_usuario_actual($origen, $id_origen);
            if ($id_origen_visible_usuario_actual == false)
            {
                $guardar_imagen = false;

                $res = "ERROR";
                $msg = $idiomas->_("El usuario no tiene permisos para guardar la imagen");
            }
        }

        // Comprobación de imagen correcta
        if ($guardar_imagen == true)
        {
            $tipo_imagen = "";
            $anchura_imagen = 0;
            $altura_imagen = 0;
            $msg_error = "";
            $imagen_correcta = comprueba_imagen_correcta(
                $origen,
                $fichero_imagen,
                $tipo_imagen,
                $anchura_imagen,
                $altura_imagen,
                $msg_error);
            if ($imagen_correcta == false)
            {
                $guardar_imagen = false;

                $res = "ERROR";
                $msg = $msg_error;
            }
        }

        // Se guarda la imagen
        if ($guardar_imagen == true)
        {
            // Se elimina la imagen (por si ya existía)
            elimina_imagen_base_datos($origen, $id_origen);

            // Se leen los datos de la imagen
            $datos_imagen = file_get_contents($ruta_fichero_cliente);

            // Identificador de red de la imagen
            switch ($origen)
            {
                case ORIGEN_IMAGEN_RED_LOGO:
                case ORIGEN_IMAGEN_RED_LOGO_PDF:
                case ORIGEN_IMAGEN_RED_MAPA:
                {
                    $id_red_imagen = $id_origen;
                    break;
                }
                case ORIGEN_IMAGEN_PREFERENCIAS_LOGO:
                case ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF:
                {
                    $id_red_imagen = ID_NINGUNO;
                    break;
                }
                default:
                {
                    $id_red_imagen = $_SESSION["id_red"];
                    break;
                }
            }

            // Se añade la imagen
            $operacion_insercion = "
                INSERT INTO imagenes (
                    red,
                    origen,
                    id_origen,
                    tipo,
                    anchura,
                    altura,
                    datos
                ) VALUES (
                    '".$bd_red->_($id_red_imagen)."',
                    '".$bd_red->_($origen)."',
                    '".$bd_red->_($id_origen)."',
                    '".$bd_red->_($tipo_imagen)."',
                    '".$bd_red->_($anchura_imagen)."',
                    '".$bd_red->_($altura_imagen)."',
                    '".$bd_red->_($datos_imagen)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la imagen añadida
                $id_imagen = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_imagen = dame_fila_imagen($id_imagen);

                // Se añade la acción de usuario
                anyade_accion_usuario_guardar_imagen_base_datos($fila_imagen, $nombre_fichero);

                $res = "OK";
                $msg = "";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => $res,
            "msg" => $msg);
        return ($resultado);
    }


    function duplica_imagen_base_datos($origen, $id_origen_anterior, $id_origen)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Comprobaciones antes de guardar la imagen:
        // - Comprobación de permisos según los orígenes
        $duplicar_imagen = true;

        // Comprobación de permisos según el origen
        if ($duplicar_imagen == true)
        {
            $id_origen_anterior_visible_usuario_actual = dame_id_origen_imagen_visible_usuario_actual($origen, $id_origen_anterior);
            $id_origen_visible_usuario_actual = dame_id_origen_imagen_visible_usuario_actual($origen, $id_origen);
            if (($id_origen_anterior_visible_usuario_actual == false) || ($id_origen_visible_usuario_actual == false))
            {
                $duplicar_imagen = false;

                $res = "ERROR";
                $msg = $idiomas->_("El usuario no tiene permisos para duplicar la imagen");
            }
        }

        // Se duplica la imagen
        if ($duplicar_imagen == true)
        {
            // Se recupera la imagen de la base de datos
            $consulta_imagen = "
                SELECT *
                FROM imagenes
                WHERE
                    (origen = '".$bd_red->_($origen)."')
                    AND (id_origen = '".$bd_red->_($id_origen_anterior)."')";
            $res_imagen = $bd_red->ejecuta_consulta($consulta_imagen);
            if (($res_imagen == false) || ($res_imagen->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_imagen."'");
            }
            $fila_imagen = $res_imagen->dame_siguiente_fila();
            $tipo_imagen = $fila_imagen["tipo"];
            $anchura_imagen = $fila_imagen["anchura"];
            $altura_imagen = $fila_imagen["altura"];
            $datos_imagen = $fila_imagen["datos"];
            $id_red_imagen = $fila_imagen["red"];

            // Se añade la imagen en base de datos
            $operacion_insercion = "
                INSERT INTO imagenes (
                    origen,
                    id_origen,
                    tipo,
                    anchura,
                    altura,
                    datos,
                    red
                ) VALUES (
                    '".$bd_red->_($origen)."',
                    '".$bd_red->_($id_origen)."',
                    '".$bd_red->_($tipo_imagen)."',
                    '".$bd_red->_($anchura_imagen)."',
                    '".$bd_red->_($altura_imagen)."',
                    '".$bd_red->_($datos_imagen)."',
                    '".$bd_red->_($id_red_imagen)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la imagen añadida
                $id_imagen = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_imagen = dame_fila_imagen($id_imagen);

                // Se añade la acción de usuario
                anyade_accion_usuario_duplicar_imagen_base_datos($fila_imagen, $id_origen_anterior);

                $res = "OK";
                $msg = "";
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => $res,
            "msg" => $msg);
        return ($resultado);
    }


    function dame_id_origen_imagen_visible_usuario_actual($origen, $id_origen)
    {
        $id_origen_imagen_visible_usuario_actual = false;
        switch ($origen)
        {
            case ORIGEN_IMAGEN_RED_LOGO:
            case ORIGEN_IMAGEN_RED_LOGO_PDF:
            case ORIGEN_IMAGEN_RED_MAPA:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    {
                        $ids_redes_usuario_actual = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                        if (in_array($id_origen, $ids_redes_usuario_actual) == true)
                        {
                            $id_origen_imagen_visible_usuario_actual = true;
                        }
                        break;
                    }
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO:
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (PlantillaInforme::dame_administracion_plantillas_informes() == true)
                        {
                            $ids_plantillas_informes_usuario_actual = dame_ids_plantillas_informes_usuario_actual();
                            if (in_array($id_origen, $ids_plantillas_informes_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        // Nota: Se permite cualquier origen porque puede ser un duplicado de una plantilla de informe
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (PlantillaInforme::dame_administracion_plantillas_informes() == true)
                        {
                            $ids_plantillas_informes_usuario_actual = dame_ids_plantillas_informes_usuario_actual();
                            $ids_origen = explode(SEPARADOR_PARAMETROS_SIMPLES, $id_origen);
                            $id_plantilla_informe = $ids_origen[0];
                            if (in_array($id_plantilla_informe, $ids_plantillas_informes_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        // Nota: Se permite cualquier origen porque puede ser un duplicado de una plantilla de informe
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (PlantillaInforme::dame_administracion_plantillas_informes() == true)
                        {
                            $ids_informes_automaticos_usuario_actual = dame_ids_informes_automaticos_usuario_actual();
                            $ids_origen = explode(SEPARADOR_PARAMETROS_SIMPLES, $id_origen);
                            $id_informe_automatico = $ids_origen[0];
                            if (in_array($id_informe_automatico, $ids_informes_automaticos_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        // Nota: Se permite cualquier origen porque puede ser un duplicado de un usuario
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (dame_administracion_widgets() == true)
                        {
                            $ids_pestanyas_widgets_usuario_actual = dame_ids_pestanyas_widgets_usuario_actual();
                            if (in_array($id_origen, $ids_pestanyas_widgets_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        // Nota: Se permite cualquier origen porque puede ser un duplicado de una pestaña de widgets
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_WIDGET_IMAGEN:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (dame_administracion_widgets() == true)
                        {
                            $ids_pestanyas_widgets_usuario_actual = dame_ids_pestanyas_widgets_usuario_actual();
                            $ids_origen = explode(SEPARADOR_PARAMETROS_SIMPLES, $id_origen);
                            $id_pestanya_widgets = $ids_origen[0];
                            if (in_array($id_pestanya_widgets, $ids_pestanyas_widgets_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        // Nota: Se permite cualquier origen porque puede ser un duplicado de un widget
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_LOCALIZACION_MAPA:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    {
                        if (Localizacion::dame_administracion_localizaciones() == true)
                        {
                            $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
                            if (in_array($id_origen, $ids_localizaciones_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_INSTALACION_IMAGEN:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    {
                        if (Instalacion::dame_administracion_instalaciones() == true)
                        {
                            $ids_instalaciones_usuario_actual = Instalacion::dame_ids_instalaciones_usuario_actual();
                            if (in_array($id_origen, $ids_instalaciones_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            case ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO:
            {
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    {
                        if (Instalacion::dame_administracion_instalaciones() == true)
                        {
                            $ids_instalaciones_usuario_actual = Instalacion::dame_ids_instalaciones_usuario_actual();
                            $ids_origen = explode(SEPARADOR_PARAMETROS_SIMPLES, $id_origen);
                            $id_instalacion = $ids_origen[0];
                            if (in_array($id_instalacion, $ids_instalaciones_usuario_actual) == true)
                            {
                                $id_origen_imagen_visible_usuario_actual = true;
                            }
                        }
                        break;
                    }
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_origen_imagen_visible_usuario_actual = true;
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Origen de imagen desconocido: '".$origen."'");
            }
        }
        return ($id_origen_imagen_visible_usuario_actual);
    }


    function carga_imagen_base_datos($origen, $id_origen, $nombre_fichero_imagen_sin_extension)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la imagen de la base de datos
        $consulta_imagen = "
            SELECT *
            FROM imagenes
            WHERE
                (origen = '".$bd_red->_($origen)."')
                AND (id_origen = '".$bd_red->_($id_origen)."')";
        $res_imagen = $bd_red->ejecuta_consulta($consulta_imagen);
        if (($res_imagen == false) || ($res_imagen->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_imagen."'");
        }
        $fila_imagen = $res_imagen->dame_siguiente_fila();
        $id_imagen = $fila_imagen["id"];
        $tipo_imagen = $fila_imagen["tipo"];
        $anchura_imagen = $fila_imagen["anchura"];
        $altura_imagen = $fila_imagen["altura"];
        $datos_imagen = $fila_imagen["datos"];

        // Comprobación de tipo de imagen
        switch ($tipo_imagen)
        {
            case "image/gif":
            {
                $extension_imagen = "gif";
                break;
            }
            case "image/jpeg":
            {
                $extension_imagen = "jpg";
                break;
            }
            case "image/png":
            {
                $extension_imagen = "png";
                break;
            }
            default:
            {
                throw new Exception("El tipo de imagen es incorrecto: '".$tipo_imagen."'");
            }
        }

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        // Nombre del fichero de imagen
        if ($nombre_fichero_imagen_sin_extension === NULL)
        {
            $nombre_fichero_imagen_sin_extension = "_imagen_".$id_imagen;
        }
        else
        {
            $nombre_fichero_imagen_sin_extension = rawurldecode($nombre_fichero_imagen_sin_extension);
            $nombre_fichero_imagen_sin_extension = convierte_ascii_estandar($nombre_fichero_imagen_sin_extension);
            $nombre_fichero_imagen_sin_extension = reemplaza_caracteres_no_alfanumericos($nombre_fichero_imagen_sin_extension, "_");
        }
        $nombre_fichero_imagen = $nombre_fichero_imagen_sin_extension.".".$extension_imagen;

        // Se guarda la imagen en el servidor
        $ruta_fichero_imagen_absoluta = $directorio_absoluto_ficheros_temporales_usuario."/".$nombre_fichero_imagen;
        $resultado_escritura_fichero_imagen = file_put_contents($ruta_fichero_imagen_absoluta, $datos_imagen);
        if ($resultado_escritura_fichero_imagen === false)
        {
            throw new Exception("No se ha podido escribir el fichero de imagen (ruta: '".$ruta_fichero_imagen_absoluta."'");
        }
        else
        {
            // La ruta del fichero de imagen es relativa al directorio local '.' (el directorio '.' es el directorio raiz en JavaScript)
            $ruta_fichero_imagen_relativa = $directorio_relativo_ficheros_temporales_usuario."/".$nombre_fichero_imagen;
        }

        // Se devuelve información de la imagen
        $info_imagen = array(
            "ruta_fichero_imagen" => $ruta_fichero_imagen_relativa,
            "anchura_imagen" => $anchura_imagen,
            "altura_imagen" => $altura_imagen);
        return ($info_imagen);
    }


    function elimina_imagen_base_datos($origen, $id_origen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado = "
            DELETE
            FROM imagenes
            WHERE
                (origen = '".$bd_red->_($origen)."')
                AND (id_origen = '".$bd_red->_($id_origen)."')";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    // Añade la acción de usuario de guardado de imagen en base de datos
    function anyade_accion_usuario_guardar_imagen_base_datos($fila, $nombre_fichero)
    {
        // Las siguientes imágenes no se guardan (aún) en las acciones de usuario
        switch ($fila["origen"])
        {
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO:
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF:
            case ORIGEN_IMAGEN_PLANTILLA_INFORMES_LOGO_PDF:
            case ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            case ORIGEN_IMAGEN_WIDGET_IMAGEN:
            {
                return;
            }
        }

        // Nombres de parámetros
        $nombre_origen = dame_nombre_origen_imagen($fila["origen"], $fila["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_GUARDAR_IMAGEN_BASE_DATOS;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_IMAGEN] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_FICHERO] = $nombre_fichero;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_IMAGEN] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ANCHURA_IMAGEN] = $fila["anchura"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALTURA_IMAGEN] = $fila["altura"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de duplicado de imagen en base de datos
    function anyade_accion_usuario_duplicar_imagen_base_datos($fila, $id_origen_anterior)
    {
        // Las siguientes imágenes no se guardan (aún) en las acciones de usuario
        switch ($fila["origen"])
        {
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO:
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF:
            case ORIGEN_IMAGEN_PLANTILLA_INFORMES_LOGO_PDF:
            case ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            case ORIGEN_IMAGEN_WIDGET_IMAGEN:
            {
                return;
            }
        }

        // Nombres de parámetros
        $nombre_origen_anterior = dame_nombre_origen_imagen($fila["origen"], $id_origen_anterior);
        $nombre_origen = dame_nombre_origen_imagen($fila["origen"], $fila["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_DUPLICAR_IMAGEN_BASE_DATOS;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_IMAGEN] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN_ANTERIOR] = $nombre_origen_anterior;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_IMAGEN] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ANCHURA_IMAGEN] = $fila["anchura"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALTURA_IMAGEN] = $fila["altura"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    //
    // Funciones auxiliares
    //


    function dame_fila_imagen($id_imagen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_imagen = "
            SELECT *
            FROM imagenes
            WHERE
                id = '".$bd_red->_($id_imagen)."'";
        $res_imagen = $bd_red->ejecuta_consulta($consulta_imagen);
        if (($res_imagen == false) || ($res_imagen->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_imagen."'");
        }
        $fila_imagen = $res_imagen->dame_siguiente_fila();
        return ($fila_imagen);
    }


    function dame_dimensiones_correctas_imagen(
        $origen,
        $anchura_imagen,
        $altura_imagen,
        &$msg_error)
    {
        $idiomas = new Idiomas();

        // Anchura y altura según el origen
        $anchura_exacta_imagen = NULL;
        $anchura_maxima_imagen = NULL;
        $altura_exacta_imagen = NULL;
        $altura_maxima_imagen = NULL;
        switch ($origen)
        {
            case ORIGEN_IMAGEN_RED_LOGO:
            case ORIGEN_IMAGEN_RED_LOGO_PDF:
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO:
            case ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF:
            case ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_LOGO;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_LOGO;
                break;
            }
            case ORIGEN_IMAGEN_RED_MAPA:
            case ORIGEN_IMAGEN_LOCALIZACION_MAPA:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_MAPA;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_MAPA;
                break;
            }
            case ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            case ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_PLANTILLA_INFORME;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_PLANTILLA_INFORME;
                break;
            }
            case ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_FONDO;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_FONDO;
                break;
            }
            case ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_WIDGET;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_WIDGET;
                break;
            }
            case ORIGEN_IMAGEN_INSTALACION_IMAGEN:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_INSTALACION;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_INSTALACION;
                break;
            }
            case ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO:
            {
                $anchura_maxima_imagen = ANCHURA_MAXIMA_IMAGEN_FOTO;
                $altura_maxima_imagen = ALTURA_MAXIMA_IMAGEN_FOTO;
                break;
            }
        }

        // Comprobación de dimensiones y mensaje de error
        $dimensiones_correctas = true;
        if (($anchura_exacta_imagen !== NULL) && ($anchura_imagen != $anchura_exacta_imagen))
        {
            $dimensiones_correctas = false;
            if ($msg_error != "")
            {
                $msg_error .= ", ";
            }
            $msg_error .= $idiomas->_("la anchura de la imagen debe ser")." ".$anchura_exacta_imagen." (".$anchura_imagen.")";
        }
        if (($anchura_maxima_imagen !== NULL) && ($anchura_imagen > $anchura_maxima_imagen))
        {
            $dimensiones_correctas = false;
            if ($msg_error != "")
            {
                $msg_error .= ", ";
            }
            $msg_error .= $idiomas->_("la anchura máxima de la imagen es")." ".$anchura_maxima_imagen." (".$anchura_imagen.")";
        }
        if (($altura_exacta_imagen !== NULL) && ($altura_imagen != $altura_exacta_imagen))
        {
            $dimensiones_correctas = false;
            if ($msg_error != "")
            {
                $msg_error .= ", ";
            }
            $msg_error .= $idiomas->_("la altura de la imagen debe ser")." ".$altura_exacta_imagen." (".$altura_imagen.")";
        }
        if (($altura_maxima_imagen !== NULL) && ($altura_imagen > $altura_maxima_imagen))
        {
            $dimensiones_correctas = false;
            if ($msg_error != "")
            {
                $msg_error .= ", ";
            }
            $msg_error .= $idiomas->_("la altura máxima de la imagen es")." ".$altura_maxima_imagen." (".$altura_imagen.")";
        }
        return ($dimensiones_correctas);
    }


    // Devuelve la descripción del origen de la imagen
    function dame_descripcion_origen_imagen($origen_imagen)
    {
        switch ($origen_imagen)
        {
            case ORIGEN_IMAGEN_RED_LOGO:
            {
                $descripcion = "Logo de red";
                break;
            }
            case ORIGEN_IMAGEN_RED_LOGO_PDF:
            {
                $descripcion = "Logo PDF de red";
                break;
            }
            case ORIGEN_IMAGEN_RED_MAPA:
            {
                $descripcion = "Mapa de red";
                break;
            }
            case ORIGEN_IMAGEN_LOCALIZACION_MAPA:
            {
                $descripcion = "Mapa de localización";
                break;
            }
            case ORIGEN_IMAGEN_INSTALACION_IMAGEN:
            {
                $descripcion = "Imagen de instalación";
                break;
            }
            case ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO:
            {
                $descripcion = "Foto de anotación de equipo de instalación";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve el nombre del origen de la imagen
    function dame_nombre_origen_imagen($origen_imagen, $id_origen_imagen)
    {
        $idiomas = new Idiomas();

        switch ($origen_imagen)
        {
            case ORIGEN_IMAGEN_RED_LOGO:
            case ORIGEN_IMAGEN_RED_LOGO_PDF:
            case ORIGEN_IMAGEN_RED_MAPA:
            {
                $nombre_origen = dame_nombre_red($id_origen_imagen);
                break;
            }
            case ORIGEN_IMAGEN_LOCALIZACION_MAPA:
            {
                $nombre_origen = dame_nombre_localizacion($id_origen_imagen);
                break;
            }
            case ORIGEN_IMAGEN_INSTALACION_IMAGEN:
            {
                $nombre_origen = dame_nombre_instalacion($id_origen_imagen);
                break;
            }
            case ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO:
            {
                $ids_origen_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $id_origen_imagen);
                $id_instalacion = $ids_origen_imagen[0];
                $id_equipo = $ids_origen_imagen[1];
                $nombre_instalacion = dame_nombre_instalacion($id_instalacion);
                $nombre_equipo = dame_nombre_equipo_instalacion($id_equipo);
                $nombre_origen = $nombre_equipo." (".$nombre_instalacion.")";
                break;
            }
            default:
            {
                $nombre_origen = $idiomas->_("Desconocido");
                break;
            }
        }
        return ($nombre_origen);
    }


    //
    // Funciones de colores
    //


    // Devuelve el rgb de un color hexadecimal
    function dame_rgb_color_hexadecimal($color_hexadecimal)
    {
        $color_hexadecimal = str_replace("#", "", $color_hexadecimal);
        $r = hexdec(substr($color_hexadecimal, 0, 2));
        $g = hexdec(substr($color_hexadecimal, 0, 2));
        $b = hexdec(substr($color_hexadecimal, 0, 2));
        $rgb = array($r, $g, $b);
        return ($rgb);
    }


    // Convierte un color de hexadecimal a RGB (con transparencia si existe)
    function convierte_color_hexadecimal_rgb($color_hexadecimal, $transparencia)
    {
        $rgb = dame_rgb_color_hexadecimal($color_hexadecimal);
        if ($transparencia === NULL)
        {
            $color_rgb = "rgb(".
                $rgb[0].",".
                $rgb[1].",".
                $rgb[2].")";
            return ($color_rgb);
        }
        else {
            $color_rgba = "rgba(".
                $rgb[0].",".
                $rgb[1].",".
                $rgb[2].",".
                (1 - $transparencia).")";
            return ($color_rgba);
        }
    }
?>
