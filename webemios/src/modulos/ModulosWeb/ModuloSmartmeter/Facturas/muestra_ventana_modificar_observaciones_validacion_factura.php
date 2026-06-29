<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_facturas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_OBSERVACIONES_VALIDACION_FACTURA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_validacion_factura = $_POST["id_validacion_factura"];

    // Contenido de la ventana
    $error = rellena_contenido_ventana_modificar_observaciones_validacion_factura(
        $medicion,
        $id_validacion_factura,
        $contenido,
        $observaciones_validacion_factura);
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
    if ($observaciones_validacion_factura == "")
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
    $pie .= '<button class="btn btn-success boton_smartmeter_modificar_observaciones_validacion_factura">'.$texto_boton_modificar.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_modificar_observaciones_validacion_factura"
            id_validacion_factura="'.$id_validacion_factura.'"
            observaciones_anteriores="'.$observaciones_validacion_factura.'"
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
	// Funcion para mostrar el contenido de la ventana de modificar observaciones de validación de factura
	//


	// Función que rellena el contenido de la ventana de modificar observaciones de validación de factura
	function rellena_contenido_ventana_modificar_observaciones_validacion_factura(
        $medicion,
        $id_validacion_factura,
        &$contenido,
        &$observaciones_validacion_factura)
	{
        $idiomas = new Idiomas();
		$bd_datos = BaseDatosDatos::dame_base_datos();

		// Se recupera la información actual de la base de datos
        $tabla_validaciones_facturas = dame_nombre_tabla_validaciones_facturas($medicion);
		$consulta_validacion = "
            SELECT observaciones
            FROM ".$tabla_validaciones_facturas."
            WHERE
                id = '".$bd_datos->_($id_validacion_factura)."'";
        $res_validacion = $bd_datos->ejecuta_consulta($consulta_validacion);
        if (($res_validacion == false) || ($res_validacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error en la consulta: '".$consulta_validacion."'");
        }
        $fila_validacion = $res_validacion->dame_siguiente_fila();

        $observaciones_validacion_factura = $fila_validacion["observaciones"];

        // Observaciones
        $numero_caracteres_actuales = dame_numero_caracteres($observaciones_validacion_factura);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_OBSERVACIONES;
		$contenido = "
			<div class='row-fluid'>
				<div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Observaciones').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
					<textarea id='observaciones_validacion_factura'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($observaciones_validacion_factura, ENT_QUOTES)."</textarea>
				</div>
			</div>";

        return ("OK");
	}
?>


