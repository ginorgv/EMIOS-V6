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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_HIJA_LOCALIZACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_localizacion_padre = $_POST["id_localizacion_padre"];
    $id_localizacion_hija = $_POST["id_localizacion_hija"];
    $id_hija_localizacion = $_POST["id_hija_localizacion"];
    if ($id_hija_localizacion === NULL)
    {
        $id_hija_localizacion = ID_NINGUNO;
    }

    // Añadir o modificar hija
    $anyadir_hija = ($id_hija_localizacion == ID_NINGUNO);
    if ($anyadir_hija == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_localizaciones_anyadir_modificar_hija_localizacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("hija");
    $error = rellena_contenido_ventana_anyadir_modificar_hija_localizacion(
        $anyadir_hija,
        $id_hija_localizacion,
        $id_localizacion_padre,
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

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_hija_localizacion"
            anyadir_hija="'.$anyadir_hija.'"
            id_localizacion_padre="'.$id_localizacion_padre.'"
            id_localizacion_hija="'.$id_localizacion_hija.'"
            id_hija_localizacion="'.$id_hija_localizacion.'"
            hidden>
        </div>';

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar hija
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar hija de localización
	function rellena_contenido_ventana_anyadir_modificar_hija_localizacion(
        $anyadir_hija,
        $id_hija_localizacion,
        $id_localizacion_padre,
        &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la hija de localización, se recupera la información actual de la base de datos
		if ($anyadir_hija == false)
		{
            $fila_hija_localizacion = dame_fila_hija_localizacion($id_hija_localizacion);

            $id_localizacion_hija = $fila_hija_localizacion["localizacion_hija"];
		}

        // Se añade la lista de localizaciones
		$contenido .= $controles_hija .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
                    <select id='id_localizacion_hija' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones_hijas($id_localizacion_padre, $id_localizacion_hija);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}
?>
