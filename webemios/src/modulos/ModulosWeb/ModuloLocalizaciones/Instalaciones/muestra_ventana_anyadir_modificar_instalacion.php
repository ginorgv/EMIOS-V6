<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_INSTALACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_instalacion = $_POST["id_instalacion"];
    if ($id_instalacion === NULL)
    {
        $id_instalacion = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar instalación
    $anyadir_instalacion = (($id_instalacion == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_instalacion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_instalacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("instalación");
    if (($anyadir_instalacion == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_instalacion($anyadir_instalacion, $id_instalacion, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar instalación
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar instalación
	function rellena_contenido_ventana_anyadir_modificar_instalacion($anyadir_instalacion, $id_instalacion, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la instalación (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_instalacion != ID_NINGUNO)
		{
            $fila_instalacion = dame_fila_instalacion($id_instalacion);

            // Principal
			$nombre = $fila_instalacion["nombre"];
            $descripcion = $fila_instalacion["descripcion"];
            $id_localizacion = $fila_instalacion["localizacion"];

            // Opciones de imagen (mapa)
            $imagen = $fila_instalacion["imagen"];
            $nombre_imagen = $fila_instalacion["nombre_imagen"];
            $factor_reduccion_imagen = $fila_instalacion["factor_reduccion_imagen"];

            // Imagen (mapa) (posición y zoom por defecto)
            $latitud_imagen_defecto = $fila_instalacion["latitud_imagen_defecto"];
            $longitud_imagen_defecto = $fila_instalacion["longitud_imagen_defecto"];
            $zoom_imagen_defecto = $fila_instalacion["zoom_imagen_defecto"];

            // Posición en mapa
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_INSTALACION,
                $id_instalacion,
                ORIGEN_MAPA_LOCALIZACION,
                $id_localizacion);
            if ($info_posicion_mapa === NULL)
            {
                $mostrar_en_mapa = VALOR_NO;
                $latitud_mapa = 0.0;
                $longitud_mapa = 0.0;
                $zoom_mapa = ZOOM_MAPA_DEFECTO;
            }
            else
            {
                $mostrar_en_mapa = VALOR_SI;
                $latitud_mapa = $info_posicion_mapa["latitud"];
                $longitud_mapa = $info_posicion_mapa["longitud"];
                $zoom_mapa = $info_posicion_mapa["zoom"];
            }
		}
        else
        {
            // Principal
            $nombre = "";
            $descripcion = "";
            $id_localizacion = ID_NINGUNO;

            // Opciones de imagen (mapa)
            $imagen = VALOR_NO;
            $nombre_imagen = "";
            $factor_reduccion_imagen = 1;

            // Imagen (mapa) (posición y zoom por defecto)
            $latitud_imagen_defecto = 0.0;
            $longitud_imagen_defecto = 0.0;
            $zoom_imagen_defecto = ZOOM_MAPA_DEFECTO;

            // Posición en mapa
            $mostrar_en_mapa = VALOR_NO;
            $latitud_mapa = 0.0;
            $longitud_mapa = 0.0;
            $zoom_mapa = ZOOM_MAPA_DEFECTO;
        }

        // Se muestran las siguientes pestañas:
        // - Principal, opciones de imagen (mapa), imagen (mapa) y posición en mapa
        $contenido = "
            <div id='tabs-administracion-instalacion' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-opciones-imagen' id='titulo-tab-opciones-imagen'>".$idiomas->_("Opciones de imagen")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-imagen' id='titulo-tab-imagen'>".$idiomas->_("Imagen")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-mapa' id='titulo-tab-posicion-mapa'>".$idiomas->_("Posición en mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-instalacion' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_instalacion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // Descripción
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='descripcion_instalacion'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_instalacion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de opciones de imagen (mapa)
        $contenido .= "
                    <div class='tab-pane' id='tab-opciones-imagen'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Imagen").": "."</span><br/>
					<select id='imagen_instalacion' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($imagen);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_imagen_instalacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de imagen").": "."</span><br/>
					<input type='text' id='nombre_imagen_instalacion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$nombre_imagen."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_imagen_instalacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen").": "."</span><br/>
                    <input type='file' id='fichero_imagen_instalacion_file'>
                    <input type='text' id='fichero_imagen_instalacion_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_instalacion_seleccionar_fichero_imagen' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_instalacion == false) && ($imagen == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_INSTALACION_IMAGEN;
            $id_origen = $id_instalacion;
            $nombre_ventana = htmlspecialchars($nombre, ENT_QUOTES);
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>

            <div class='row-fluid' id='control_factor_reduccion_imagen_instalacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Factor de reducción de imagen").": "."</span><br/>
					<input type='text' id='factor_reduccion_imagen_instalacion'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$factor_reduccion_imagen."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Imagen (mapa)
        $contenido .= "
                    <div class='tab-pane' id='tab-imagen'>";
        $contenido .= dame_localizador_mapa(
            "_defecto",
            ORIGEN_MAPA_INSTALACION,
            $id_instalacion,
            $latitud_imagen_defecto,
            $longitud_imagen_defecto,
            $zoom_imagen_defecto);
        $contenido .= "
                    </div>";

        // Posición en el mapa
        // (la imagen del localizador del mapa cambiará 'dinámicamente' dependiendo de la localización seleccionada)
        $contenido .= "
                    <div class='tab-pane' id='tab-posicion-mapa'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar en mapa").": "."</span><br/>
					<select id='mostrar_en_mapa' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_en_mapa);
		$contenido .= "
					</select>
				</div>
			</div>";

        $parametros_origen_mapa = array(
            "modulo" => MODULO_LOCALIZACIONES,
            "tipo_elemento_mapa" => TIPO_ELEMENTO_MAPA_INSTALACION,
            "id_instalacion" => $id_instalacion);
		$contenido .= dame_localizador_mapa(
            "",
            ORIGEN_MAPA_POSICION,
            $parametros_origen_mapa,
            $latitud_mapa,
            $longitud_mapa,
            $zoom_mapa);

        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) específicos de la red en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_instalacion"
                anyadir_instalacion="'.$anyadir_instalacion.'"
                id_instalacion="'.$id_instalacion.'"
                id_localizacion="'.$id_localizacion.'"
                imagen="'.$imagen.'"
                factor_reduccion_imagen="'.$factor_reduccion_imagen.'"
                latitud_mapa="'.$latitud_mapa.'"
                longitud_mapa="'.$longitud_mapa.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
