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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_RED_PARCIAL, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_red = $_POST["id_red"];

    // Título
    $titulo = $idiomas->_("Modificar")." ".$idiomas->_("red");

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_modificar_red_parcial">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_modificar_red_parcial($id_red, $contenido);
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
	// Funcion para mostrar el contenido de la ventana de modificar red parcial
	//


	// Función que rellena el contenido de la ventana de modificar red parcial
	function rellena_contenido_ventana_modificar_red_parcial($id_red, &$contenido)
	{
		$idiomas = new Idiomas();

		// Se recupera la información actual de la base de datos
        $fila_red = dame_fila_red($id_red);

        // Nombre
        $nombre = $fila_red["nombre"];

        // Preferencias
        $logo_personalizado = $fila_red["logo_personalizado"];
        $nombre_logo = $fila_red["nombre_logo"];
        $url_logo = $fila_red["url_logo"];
        $titulo_web = $fila_red["titulo_web"];
        $tema = $fila_red["tema"];
        $paleta_colores_graficas = $fila_red["paleta_colores_graficas"];
        $periodo_completo_informes_defecto = $fila_red["periodo_completo_informes_defecto"];

        // Opciones de mapa
        $tipo_mapa = $fila_red["tipo_mapa"];
        $nombre_mapa = $fila_red["nombre_mapa"];
        $factor_reduccion_imagen_mapa_local = $fila_red["factor_reduccion_imagen_mapa_local"];
        $etiquetas_mapa = $fila_red["etiquetas_mapa"];

        // Mapa (posición y zoom por defecto)
        $latitud_mapa_defecto = $fila_red["latitud_mapa_defecto"];
        $longitud_mapa_defecto = $fila_red["longitud_mapa_defecto"];
        $zoom_mapa_defecto = $fila_red["zoom_mapa_defecto"];

        // Se muestran las siguientes pestañas:
        // - Preferencias, opciones de mapa y mapa
        $contenido = "
            <div id='tabs-administracion-red' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-preferencias' id='titulo-tab-preferencias'>".$idiomas->_("Preferencias")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-opciones-mapa' id='titulo-tab-opciones-mapa'>".$idiomas->_("Opciones de mapa")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-mapa' id='titulo-tab-mapa'>".$idiomas->_("Mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-red' class='tab-content'>";

        // Contenido de pestaña de preferencias
        $contenido .= "
                    <div class='tab-pane active' id='tab-preferencias'>";
        $contenido .= dame_controles_red_pestanya_preferencias(
            false,
            $id_red,
            $nombre,
            $logo_personalizado,
            $nombre_logo,
            $url_logo,
            $titulo_web,
            $tema,
            $paleta_colores_graficas,
            $periodo_completo_informes_defecto);
        $contenido .= "
                    </div>";

        // Contenido de pestaña de opciones de mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-opciones-mapa'>";
        $contenido .= dame_controles_red_pestanya_opciones_mapa(
            false,
            $id_red,
            $nombre,
            $tipo_mapa,
            $nombre_mapa,
            $factor_reduccion_imagen_mapa_local,
            $etiquetas_mapa);
        $contenido .= "
                    </div>";

        // Mapa (posición y zoom por defecto)
        $contenido .= "
                    <div class='tab-pane' id='tab-mapa'>";
		$contenido .= dame_localizador_mapa(
            "_defecto",
            ORIGEN_MAPA_RED,
            $id_red,
            $latitud_mapa_defecto,
            $longitud_mapa_defecto,
            $zoom_mapa_defecto);
        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) específicos de la red en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_red_parcial"
                id_red="'.$id_red.'"
                logo_personalizado="'.$logo_personalizado.'"
                tipo_mapa="'.$tipo_mapa.'"
                nombre_mapa="'.$nombre_mapa.'"
                factor_reduccion_imagen_mapa_local="'.$factor_reduccion_imagen_mapa_local.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
