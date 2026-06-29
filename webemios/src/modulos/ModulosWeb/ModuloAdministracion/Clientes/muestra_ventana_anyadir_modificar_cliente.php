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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_CLIENTE, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_cliente = $_POST["id_cliente"];
    if ($id_cliente === NULL)
    {
        $id_cliente = ID_NINGUNO;
    }

    // Añadir o modificar cliente
    $anyadir_cliente = ($id_cliente == ID_NINGUNO);
    if ($anyadir_cliente == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_administracion_anyadir_modificar_cliente">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("cliente");
    $error = rellena_contenido_ventana_anyadir_modificar_cliente($anyadir_cliente, $id_cliente, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_cliente"
            anyadir_cliente="'.$anyadir_cliente.'"
            id_cliente="'.$id_cliente.'"
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar cliente
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar cliente
	function rellena_contenido_ventana_anyadir_modificar_cliente($anyadir_cliente, $id_cliente, &$contenido)
	{
		$idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

		// Si hay que modificar el cliente, se recupera la información actual de la base de datos
		if ($anyadir_cliente == false)
		{
			$consulta = "
				SELECT *
				FROM clientes
				WHERE
					id = '".$bd_red->_($id_cliente)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
			if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }

			$fila = $res->dame_siguiente_fila();
			$nombre = $fila["nombre"];
		}
        else
        {
            $nombre = "";
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_cliente'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        return ("OK");
	}
?>
