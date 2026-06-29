<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_EQUIPO_INSTALACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_instalacion = $_POST["id_instalacion"];
    $id_equipo = $_POST["id_equipo"];
    if ($id_equipo === NULL)
    {
        $id_equipo = ID_NINGUNO;
    }

    // Añadir o modificar equipo
    $anyadir_equipo = ($id_equipo == ID_NINGUNO);
    if ($anyadir_equipo == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_equipo_instalacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("equipo");
    $error = rellena_contenido_ventana_anyadir_modificar_equipo($id_instalacion, $anyadir_equipo, $id_equipo, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar equipo
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar equipo
	function rellena_contenido_ventana_anyadir_modificar_equipo($id_instalacion, $anyadir_equipo, $id_equipo, &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar el equipo, se recupera la información actual de la base de datos
		if ($anyadir_equipo == false)
		{
            $fila_equipo_instalacion = dame_fila_equipo_instalacion($id_equipo);

            // Principal
            $nombre = $fila_equipo_instalacion['nombre'];
            $descripcion = $fila_equipo_instalacion['descripcion'];
            $id_equipo_padre = $fila_equipo_instalacion['equipo_padre'];

            // Sensores y actuadores
            $cadena_ids_sensores = $fila_equipo_instalacion['sensores'];
            $cadena_ids_actuadores = $fila_equipo_instalacion['actuadores'];
            $ids_sensores = array();
            if ($cadena_ids_sensores != "")
            {
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
            }
            $ids_actuadores = array();
            if ($cadena_ids_actuadores != "")
            {
                $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_actuadores);
            }

            // Estado (y observaciones)
            $estado = $fila_equipo_instalacion['estado'];
            $observaciones = $fila_equipo_instalacion['observaciones'];

            // Icono de imagen (mapa)
            $icono_imagen = $fila_equipo_instalacion['icono_imagen'];

            // Posición en imagen (mapa)
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                $id_equipo,
                ORIGEN_MAPA_INSTALACION,
                $id_instalacion);
            if ($info_posicion_mapa === NULL)
            {
                $fila_instalacion = dame_fila_instalacion($id_instalacion);
                $mostrar_en_imagen = VALOR_NO;
                $latitud_imagen = $fila_instalacion["latitud_imagen_defecto"];
                $longitud_imagen = $fila_instalacion["longitud_imagen_defecto"];
                $zoom_imagen = $fila_instalacion["zoom_imagen_defecto"];
            }
            else
            {
                $mostrar_en_imagen = VALOR_SI;
                $latitud_imagen = $info_posicion_mapa["latitud"];
                $longitud_imagen = $info_posicion_mapa["longitud"];
                $zoom_imagen = $info_posicion_mapa["zoom"];
            }
		}
        else
        {
            // Principal
            $nombre = "";
            $descripcion = "";
            $id_equipo_padre = ID_NINGUNO;

            // Sensores y actuadores
            $ids_sensores = array();
            $ids_actuadores = array();

            // Estado (y observaciones)
            $estado = ESTADO_EQUIPO_INSTALACION_OK;
            $observaciones = "";

            // Icono de imagen (mapa)
            $icono_imagen = "Equipo";

            // Se recupera la información de la imagen (mapa) de la instalación
            $fila_instalacion = dame_fila_instalacion($id_instalacion);
            $mostrar_en_imagen = VALOR_NO;
            $latitud_imagen = $fila_instalacion["latitud_imagen_defecto"];
            $longitud_imagen = $fila_instalacion["longitud_imagen_defecto"];
            $zoom_imagen = $fila_instalacion["zoom_imagen_defecto"];
        }

        // Se recupera información de la instalación
        $fila_instalacion = dame_fila_instalacion($id_instalacion);
        $id_localizacion = $fila_instalacion["localizacion"];
        $imagen_instalacion = $fila_instalacion["imagen"];

        // Se muestran las siguientes pestañas:
        // - Principal, sensores y actuadores, estado y observaciones y posición en imagen (mapa)
        $contenido = "
            <div id='tabs-administracion-equipo-instalacion' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-sensores-actuadores' id='titulo-tab-sensores-actuadores'>".$idiomas->_("Sensores y actuadores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-estado' id='titulo-tab-estado'>".$idiomas->_("Estado")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-imagen' id='titulo-tab-posicion-imagen'>".$idiomas->_("Posición en imagen")."</a></li>
                </ul>
                <div id='tabs-content-administracion-equipo-instalacion' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_equipo_instalacion'
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
                    <textarea id='descripcion_equipo_instalacion'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Se añade la lista de equipos padres
		$contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Equipo padre").": "."</span><br/>
                    <select id='id_equipo_padre_equipo_instalacion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_equipos_instalacion_padres($id_instalacion, $id_equipo, $id_equipo_padre);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de sensores y actuadores
        $contenido .= "
                    <div class='tab-pane' id='tab-sensores-actuadores'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_equipo_instalacion_no_visible' hidden></div>
                    <select id='ids_sensores_equipo_instalacion'
                        name='ids_sensores_equipo_instalacion'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_EQUIPO_INSTALACION."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_nodos_localizacion_sin_equipo_instalacion($id_localizacion, TIPO_NODO_SENSOR, $ids_sensores);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Actuadores").": "."</span><br/>
                    <div id='select_actuadores_equipo_instalacion_no_visible' hidden></div>
                    <select id='ids_actuadores_equipo_instalacion'
                        name='ids_actuadores_equipo_instalacion'
                        max_selected='".MAX_ACTUADORES_SELECCIONADOS_LISTA_ACTUADORES_EQUIPO_INSTALACION."' multiple='multiple'
                        class='select-administracion' hidden>";
        $contenido .= dame_lista_nodos_localizacion_sin_equipo_instalacion($id_localizacion, TIPO_NODO_ACTUADOR, $ids_actuadores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de estado (y de observaciones)
        $contenido .= "
                    <div class='tab-pane' id='tab-estado'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Estado").": "."</span><br/>
                    <select id='estado_equipo_instalacion' class='select-administracion'>";
        $contenido .= dame_lista_estados_equipo_instalacion($estado);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $numero_caracteres_actuales = dame_numero_caracteres($observaciones);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_OBSERVACIONES;
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Observaciones').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='observaciones_equipo_instalacion'
						class='TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($observaciones, ENT_QUOTES)."</textarea>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Posición en la imagen (mapa)
        $contenido .= "
                    <div class='tab-pane' id='tab-posicion-imagen'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar en imagen").": "."</span><br/>
					<select id='mostrar_en_mapa' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_en_imagen);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_icono_imagen'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono de imagen").": "."</span><br/>
                    <select id='icono_imagen' class='select-administracion'>";
        $contenido .= dame_lista_iconos_mapa_equipo_instalacion($icono_imagen);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $parametros_origen_mapa = array(
            "modulo" => MODULO_LOCALIZACIONES,
            "tipo_elemento_mapa" => TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION);
		$contenido .= dame_localizador_mapa(
            "",
            ORIGEN_MAPA_POSICION,
            $parametros_origen_mapa,
            $latitud_imagen,
            $longitud_imagen,
            $zoom_imagen);

        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) específicos de la red en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_equipo_instalacion"
                anyadir_equipo="'.$anyadir_equipo.'"
                id_instalacion="'.$id_instalacion.'"
                imagen_instalacion="'.$imagen_instalacion.'"
                id_equipo="'.$id_equipo.'"
                id_equipo_padre="'.$id_equipo_padre.'"
                hidden>
            </div>';

        return ("OK");
	}
?>