<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');


    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Botones
    $pie .= '<button class="btn btn-success boton_sensores_correlacion_anyadir_linea_base">'.$idiomas->_("Añadir").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= $idiomas->_("Añadir línea base")." (".$idiomas->_("correlación").")";

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_linea_base_correlacion($contenido);
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar línea base desde correlación
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar línea base desde correlación
	function rellena_contenido_ventana_anyadir_linea_base_correlacion(&$contenido)
	{
		$idiomas = new Idiomas();

        $contenido = "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_linea_base'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value=''>
				</div>
			</div>";

        return ("OK");
	}
?>
