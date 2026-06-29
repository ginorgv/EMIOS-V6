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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_LOCALIZACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_localizacion = $_POST["id_localizacion"];
    if ($id_localizacion === NULL)
    {
        $id_localizacion = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar localización
    $anyadir_localizacion = (($id_localizacion == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_localizacion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_localizacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("localización");
    if (($anyadir_localizacion == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_localizacion($anyadir_localizacion, $id_localizacion, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar localización
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar localización
	function rellena_contenido_ventana_anyadir_modificar_localizacion($anyadir_localizacion, $id_localizacion, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la localización (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_localizacion != ID_NINGUNO)
		{
            $fila_localizacion = dame_fila_localizacion($id_localizacion);

            // Principal
			$nombre = $fila_localizacion["nombre"];
            $descripcion = $fila_localizacion["descripcion"];

            // Opciones de mapa
            $mapa_personalizado = $fila_localizacion["mapa_personalizado"];
            $tipo_mapa = $fila_localizacion["tipo_mapa"];
            $nombre_mapa = $fila_localizacion["nombre_mapa"];
            $factor_reduccion_imagen_mapa_local = $fila_localizacion["factor_reduccion_imagen_mapa_local"];
            $etiquetas_mapa = $fila_localizacion["etiquetas_mapa"];

            // Mapa (posición y zoom por defecto)
            $latitud_mapa_defecto = $fila_localizacion["latitud_mapa_defecto"];
            $longitud_mapa_defecto = $fila_localizacion["longitud_mapa_defecto"];
            $zoom_mapa_defecto = $fila_localizacion["zoom_mapa_defecto"];

            // Posición en mapa
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_LOCALIZACION,
                $id_localizacion,
                ORIGEN_MAPA_RED,
                $_SESSION["id_red"]);
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

            // Opciones de mapa
            $mapa_personalizado = VALOR_NO;
            $tipo_mapa = TIPO_MAPA_INTERNET;
            $nombre_mapa = "";
            $factor_reduccion_imagen_mapa_local = 1;
            $etiquetas_mapa = VALOR_SI;

            // Mapa (posición y zoom por defecto)
            $latitud_mapa_defecto = 0.0;
            $longitud_mapa_defecto = 0.0;
            $zoom_mapa_defecto = ZOOM_MAPA_DEFECTO;

            // Se recupera la información del mapa de la red
            $fila_red = dame_fila_red($_SESSION["id_red"]);
            $mostrar_en_mapa = VALOR_NO;
            $latitud_mapa = $fila_red["latitud_mapa_defecto"];
            $longitud_mapa = $fila_red["longitud_mapa_defecto"];
            $zoom_mapa = $fila_red["zoom_mapa_defecto"];
        }

        // Se muestran las siguientes pestañas:
        // - Principal, ratios, opciones de mapa, mapa y posición en mapa
        $contenido = "
            <div id='tabs-administracion-localizacion' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-valores-sensores-ratios' id='titulo-tab-valores-sensores-ratios'>".$idiomas->_("Valores y sensores de ratios")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-opciones-mapa' id='titulo-tab-opciones-mapa'>".$idiomas->_("Opciones de mapa")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-mapa' id='titulo-tab-mapa'>".$idiomas->_("Mapa")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-mapa' id='titulo-tab-posicion-mapa'>".$idiomas->_("Posición en mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-localizacion' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_localizacion'
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
                    <textarea id='descripcion_localizacion'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de valores y sensores de ratios
        $contenido .= "
                    <div class='tab-pane' id='tab-valores-sensores-ratios'>";
		$contenido .= dame_controles_valores_sensores_ratios_localizacion($id_localizacion);
        $contenido .= "
                    </div>";

        // Contenido de pestaña de opciones de mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-opciones-mapa'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mapa personalizado").": "."</span><br/>
					<select id='mapa_personalizado_localizacion' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mapa_personalizado);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_tipo_mapa_localizacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa").": "."</span><br/>
					<select id='tipo_mapa_localizacion' class='select-administracion'>";
        $contenido .= dame_lista_tipos_mapa($tipo_mapa);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_mapa_localizacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de mapa").": "."</span><br/>
					<input type='text' id='nombre_mapa_localizacion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$nombre_mapa."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_imagen_mapa_localizacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen de mapa").": "."</span><br/>
                    <input type='file' id='fichero_imagen_mapa_localizacion_file'>
                    <input type='text' id='fichero_imagen_mapa_localizacion_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_localizacion_seleccionar_fichero_imagen_mapa' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if ($anyadir_localizacion == false)
        {
            $origen = ORIGEN_IMAGEN_LOCALIZACION_MAPA;
            $id_origen = $id_localizacion;
            $nombre_ventana = htmlspecialchars($nombre, ENT_QUOTES)." (".$idiomas->_("mapa").")";
            $contenido .= "
                <button id='boton_mostrar_imagen_mapa_localizacion' class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'";
            if (($mapa_personalizado == VALOR_NO) || ($tipo_mapa == TIPO_MAPA_INTERNET))
            {
                $contenido .= "style='display: none;'";
            }
            $contenido .= "><i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>

            <div class='row-fluid' id='control_factor_reduccion_imagen_mapa_local_localizacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Factor de reducción de imagen de mapa").": "."</span><br/>
					<input type='text' id='factor_reduccion_imagen_mapa_local_localizacion'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$factor_reduccion_imagen_mapa_local."'>
				</div>
			</div>

            <div class='row-fluid' id='control_etiquetas_mapa_localizacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar etiquetas").": "."</span><br/>
					<select id='etiquetas_mapa_localizacion' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($etiquetas_mapa);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-mapa'>";
        $contenido .= dame_localizador_mapa(
            "_defecto",
            ORIGEN_MAPA_LOCALIZACION,
            $id_localizacion,
            $latitud_mapa_defecto,
            $longitud_mapa_defecto,
            $zoom_mapa_defecto);
        $contenido .= "
                    </div>";

        // Posición en el mapa
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
            "tipo_elemento_mapa" => TIPO_ELEMENTO_MAPA_LOCALIZACION);
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
            <div id="parametros_ventana_anyadir_modificar_localizacion"
                anyadir_localizacion="'.$anyadir_localizacion.'"
                id_localizacion="'.$id_localizacion.'"
                mapa_personalizado="'.$mapa_personalizado.'"
                tipo_mapa="'.$tipo_mapa.'"
                nombre_mapa="'.$nombre_mapa.'"
                factor_reduccion_imagen_mapa_local="'.$factor_reduccion_imagen_mapa_local.'"
                latitud_mapa="'.$latitud_mapa.'"
                longitud_mapa="'.$longitud_mapa.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
