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

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_OBSERVACIONES_ACCION_USUARIO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_accion = $_POST["id_accion"];

    // Contenido de la ventana
    $error = rellena_contenido_ventana_modificar_observaciones_accion($id_accion, $contenido, $observaciones_accion);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    // Título y texto del botón
    if ($observaciones_accion == "")
    {
        $titulo .= $idiomas->_("Añadir observaciones");
        $texto_boton_modificar = $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar observaciones");
        $texto_boton_modificar = $idiomas->_("Modificar");
    }

    // Botones en el pie de la ventana
    $pie .= '<button class="btn btn-success boton_modificar_observaciones_accion_usuario">'.$texto_boton_modificar.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_modificar_observaciones_accion"
            id_accion="'.$id_accion.'"
            observaciones_anteriores="'.$observaciones_accion.'"
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
	// Funcion para mostrar el contenido de la ventana de modificar observaciones de acción
	//


	// Función que rellena el contenido de la ventana de modificar observaciones de acción
	function rellena_contenido_ventana_modificar_observaciones_accion($id_accion, &$contenido, &$observaciones_accion)
	{
        $idiomas = new Idiomas();
		$bd_datos = BaseDatosDatos::dame_base_datos();

		// Se recupera la información actual de la base de datos
		$consulta_accion = "
            SELECT observaciones
            FROM acciones_usuario
            WHERE
                id = '".$bd_datos->_($id_accion)."'";
        $res_accion = $bd_datos->ejecuta_consulta($consulta_accion);
        if (($res_accion == false) || ($res_accion->dame_numero_filas() == 0))
        {
            throw new Exception("Error en la consulta: '".$consulta_accion."'");
        }
        $fila_accion = $res_accion->dame_siguiente_fila();
        $observaciones_accion = $fila_accion["observaciones"];

        // Observaciones
        $numero_caracteres_actuales = dame_numero_caracteres($observaciones_accion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_OBSERVACIONES;
		$contenido = "
			<div class='row-fluid'>
				<div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Observaciones').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='observaciones_accion'
						class='TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($observaciones_accion, ENT_QUOTES)."</textarea>
				</div>
			</div>";

        return ("OK");
	}
?>


