<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PREFERENCIAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_preferencias = $_POST["id_preferencias"];
    if ($id_preferencias === NULL)
    {
        $id_preferencias = ID_NINGUNO;
    }

    // Añadir o modificar preferencias
    $anyadir_preferencias = ($id_preferencias == ID_NINGUNO);
    if ($anyadir_preferencias == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_administracion_anyadir_modificar_preferencias">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("preferencias");
    $error = rellena_contenido_ventana_anyadir_modificar_preferencias($anyadir_preferencias, $id_preferencias, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar preferencias
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar preferencias
	function rellena_contenido_ventana_anyadir_modificar_preferencias($anyadir_preferencias, $id_preferencias, &$contenido)
	{
		$idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

		// Si hay que modificar las preferencias, se recupera la información actual de la base de datos
		if ($anyadir_preferencias == false)
		{
			$consulta = "
				SELECT *
				FROM preferencias
				WHERE
					id = '".$bd_red->_($id_preferencias)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
			if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }

			$fila = $res->dame_siguiente_fila();
			$url = $fila["url"];
            $logo_personalizado = $fila["logo_personalizado"];
            $nombre_logo = $fila["nombre_logo"];
            $url_logo = $fila["url_logo"];
            $titulo_web = $fila["titulo_web"];
            $tema = $fila["tema"];
            $paleta_colores_graficas = $fila["paleta_colores_graficas"];
		}
        else
        {
            $url = "";
            $logo_personalizado = VALOR_NO;
            $nombre_logo = "";
            $url_logo = "";
            $titulo_web = "";
            $tema = TEMA_DEFECTO;
            $paleta_colores_graficas = PALETA_COLORES_GRAFICAS_DEFECTO;
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('URL').": "."</span><br/>
					<input type='text' id='url_preferencias'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($url, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Logo personalizado").": "."</span><br/>
					<select id='logo_personalizado_preferencias' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($logo_personalizado);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_logo_preferencias'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de logo").": "."</span><br/>
					<input type='text' id='nombre_logo_preferencias'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_logo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_logo_preferencias'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de logo").": "."</span><br/>
                    <input type='file' id='fichero_logo_preferencias_file'>
                    <input type='text' id='fichero_logo_preferencias_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_preferencias_seleccionar_fichero_logo' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_preferencias == false) && ($logo_personalizado == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_PREFERENCIAS_LOGO;
            $id_origen = $id_preferencias;
            $nombre_ventana = $idiomas->_("Logo");
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_logo_pdf_preferencias'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de logo PDF").": "."</span><br/>
                    <input type='file' id='fichero_logo_pdf_preferencias_file'>
                    <input type='text' id='fichero_logo_pdf_preferencias_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_preferencias_seleccionar_fichero_logo_pdf' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_preferencias == false) && ($logo_personalizado == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF;
            $id_origen = $id_preferencias;
            $nombre_ventana = $idiomas->_("Logo PDF");
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>

            <div class='row-fluid' id='control_url_logo_preferencias'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('URL de logo').": "."</span><br/>
					<input type='text' id='url_logo_preferencias'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($url_logo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Título de Web').": "."</span><br/>
					<input type='text' id='titulo_web_preferencias'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo_web, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tema").": "."</span><br/>
					<select id='tema_preferencias' class='select-administracion'>";
        $contenido .= dame_lista_temas($tema);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de gráficas").": "."</span><br/>
					<select id='paleta_colores_graficas_preferencias' class='select-administracion'>";
        $contenido .= dame_lista_paleta_colores_graficas($paleta_colores_graficas);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Se añaden los parámetros (no visibles) específicos de la red en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_preferencias"
                anyadir_preferencias="'.$anyadir_preferencias.'"
                id_preferencias="'.$id_preferencias.'"
                logo_personalizado="'.$logo_personalizado.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
