<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_ANOTACION_EQUIPO_INSTALACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_instalacion = $_POST["id_instalacion"];
    $id_equipo = $_POST["id_equipo"];
    $id_anotacion = $_POST["id_anotacion"];
    if ($id_anotacion === NULL)
    {
        $id_anotacion = ID_NINGUNO;
    }

    // Añadir o modificar anotación
    $anyadir_anotacion = ($id_anotacion == ID_NINGUNO);
    if ($anyadir_anotacion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_anotacion_equipo_instalacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("anotación");
    $error = rellena_contenido_ventana_anyadir_modificar_anotacion(
        $anyadir_anotacion,
        $id_instalacion,
        $id_equipo,
        $id_anotacion,
        $contenido);
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar anotacion
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar anotación
	function rellena_contenido_ventana_anyadir_modificar_anotacion(
        $anyadir_anotacion,
        $id_instalacion,
        $id_equipo,
        $id_anotacion,
        &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la anotación, se recupera la información actual de la base de datos
		if ($anyadir_anotacion == false)
		{
			$fila_anotacion_equipo_instalacion = dame_fila_anotacion_equipo_instalacion($id_anotacion);

            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_base_datos_utc = $fila_anotacion_equipo_instalacion["hora"];
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $fecha_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $hora_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $texto = $fila_anotacion_equipo_instalacion["texto"];
            $foto = $fila_anotacion_equipo_instalacion["foto"];
		}

        // Contenido de la ventana

        // Contenido de pestaña principal
        $mostrar_identificador = false;
        if ($anyadir_anotacion == false)
        {
            $mostrar_identificador = true;
        }
        if ($mostrar_identificador == true)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador").": "."</span><br/>
                        <input type='text' id='id_widget'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$id_anotacion."' disabled>
                    </div>
                </div>";
        }

        // Fecha y hora
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha").": "."</span><br/>";
        $contenido .= "
                    <input size='10' type='text' id='fecha_anotacion_equipo_instalacion' class='selector-fecha datepicker'
                        readonly='readonly'";
        if ($anyadir_anotacion == false)
        {
            $contenido .= " value='".$fecha_local."'";
        }
        else
        {
            $contenido .= " value='".date($_SESSION["formato_fecha_local"])."'";
        }
        $contenido .= ">";

        $contenido .= "
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_anotacion_equipo_instalacion' class='selector-hora timepicker' readonly='readonly'";
        if ($anyadir_anotacion == false)
        {
            $contenido .= " value='".$hora_local."'";
        }
        $contenido .= ">
                    </span>
				</div>
			</div>";

        // Texto
        $numero_caracteres_actuales = dame_numero_caracteres($texto);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TEXTO_ANOTACION_EQUIPO_INSTALACION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Texto').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='texto_anotacion_equipo_instalacion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($texto, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Foto
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Foto").": "."</span><br/>
					<select id='foto_anotacion_equipo_instalacion' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($foto);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_foto_anotacion_equipo_instalacion'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de foto").": "."</span><br/>
                    <input type='file' id='fichero_foto_anotacion_equipo_instalacion_file'>
                    <input type='text' id='fichero_foto_anotacion_equipo_instalacion_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_anotacion_equipo_instalacion_seleccionar_fichero_foto' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_anotacion == false) && ($foto == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO;
            $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $id_instalacion,
                $id_equipo,
                $id_anotacion));
            $nombre_equipo = dame_nombre_equipo_instalacion($id_equipo);
            $nombre_ventana = htmlspecialchars($nombre_equipo, ENT_QUOTES);
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-camera color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_anotacion_equipo_instalacion"
                anyadir_anotacion="'.$anyadir_anotacion.'"
                id_instalacion="'.$id_instalacion.'"
                id_equipo="'.$id_equipo.'"
                id_anotacion="'.$id_anotacion.'"
                foto="'.$foto.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
