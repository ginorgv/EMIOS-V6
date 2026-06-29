<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ENVIO_ACCION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $destino = $_POST["destino"];
    $id_destino = $_POST["id_destino"];
    $origen_envio_accion = $_POST["origen_envio_accion"];
    $id_origen_envio_accion = $_POST["id_origen_envio_accion"];

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_actuadores_enviar_accion">'.$idiomas->_("Enviar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Enviar acción");
    $error = rellena_contenido_ventana_envio_accion(
        $destino,
        $id_destino,
        $origen_envio_accion,
        $id_origen_envio_accion,
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
	// Funciones para mostrar el contenido de la ventana de enviar acción a un actuador o grupo de actuadores
	//


	// Función que rellena el contenido de la ventana de enviar acción
	function rellena_contenido_ventana_envio_accion(
        $destino,
        $id_destino,
        $origen_envio_accion,
        $id_origen_envio_accion,
        &$contenido)
	{
        $idiomas = new Idiomas();

        // Se recupera la información del actuador o grupo de actuadores
        if ($id_destino != ID_NINGUNO)
        {
            switch ($destino)
            {
                case DESTINO_ACCION_ACTUADOR:
                {
                    $fila_actuador_grupo = dame_fila_actuador($id_destino);
                    break;
                }
                case DESTINO_ACCION_GRUPO_ACTUADORES:
                {
                    $fila_actuador_grupo = dame_fila_grupo_actuadores($id_destino);
                    break;
                }
            }
            $clase_actuador = $fila_actuador_grupo["clase"];
            $contenido_ultima_accion = $fila_actuador_grupo["contenido_ultima_accion"];
            $valor_ultima_accion = $fila_actuador_grupo["valor_ultima_accion"];
        }
        else
        {
            $clase_actuador = CLASE_NINGUNA;
        }

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_envio_accion' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de destino").": "."</span><br/>
                    <select id='destino_accion_envio_accion' class='select-administracion'>";
        $contenido .= dame_lista_destinos_accion($destino);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Destino").": "."</span><br/>
                    <select id='id_destino_accion_envio_accion' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_destinos_accion($clase_actuador, $destino, $id_destino);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Se recuperan los controles de la acción
		$contenido .= "
            <div id='controles_accion_envio_accion'>".
                dame_controles_accion($clase_actuador, $contenido_ultima_accion, $valor_ultima_accion, ORIGEN_CONTROLES_ACCION_ENVIO_ACCION)."
            </div>";

        // Parámetros no visibles (en un 'div' oculto)
        $contenido .= '
            <div id="parametros_ventana_envio_accion"
                origen_envio_accion="'.$origen_envio_accion.'"
                id_origen_envio_accion="'.$id_origen_envio_accion.'"
                hidden>
            </div>';
        return ("OK");
	}
?>
