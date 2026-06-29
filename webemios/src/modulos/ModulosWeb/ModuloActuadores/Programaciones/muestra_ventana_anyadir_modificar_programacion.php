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
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PROGRAMACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_programacion = $_POST["id_programacion"];
    if ($id_programacion === NULL)
    {
        $id_programacion = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar programación
    $anyadir_programacion = (($id_programacion == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_programacion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_programacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("programación");
    if (($anyadir_programacion == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_programacion($anyadir_programacion, $id_programacion, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_programacion"
            anyadir_programacion="'.$anyadir_programacion.'"
            id_programacion="'.$id_programacion.'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar programación
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar programación
	function rellena_contenido_ventana_anyadir_modificar_programacion($anyadir_programacion, $id_programacion, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la programación (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_programacion != ID_NINGUNO)
		{
			$fila_programacion = dame_fila_programacion($id_programacion);

			$nombre = $fila_programacion["nombre"];
			$clase_actuador = $fila_programacion["clase"];
		}
        else
        {
            $nombre = "";
            $clase_actuador = CLASE_NINGUNA;
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_programacion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // No se permite cambiar la clase de actuador si se está modificando
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Clase de actuador').": "."</span><br/>
					<select id='clase_actuador_programacion' class='select-administracion'";
        if ($anyadir_programacion == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_clases_actuador($clase_actuador, true, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        return ("OK");
	}
?>
