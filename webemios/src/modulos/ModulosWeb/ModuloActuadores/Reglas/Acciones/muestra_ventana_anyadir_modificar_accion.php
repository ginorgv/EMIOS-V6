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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_ACCION_REGLA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_regla = $_POST["id_regla"];
    $tipo = $_POST["tipo"];
    $id_accion = $_POST["id_accion"];
    if ($id_accion === NULL)
    {
        $id_accion = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar acción
    $anyadir_accion = (($id_accion == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_accion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_accion_regla">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    switch ($tipo)
    {
        case TIPO_ACCION_ACTIVACION:
        {
            $titulo .= " ".$idiomas->_("acción de activación");
            break;
        }
        case TIPO_ACCION_DESACTIVACION:
        {
            $titulo .= " ".$idiomas->_("acción de desactivación");
            break;
        }
    }
    if (($anyadir_accion == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se muestra el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_accion($anyadir_accion, $id_accion, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_accion_regla"
            anyadir_accion="'.$anyadir_accion.'"
            id_regla="'.$id_regla.'"
            tipo="'.$tipo.'"
            id_accion="'.$id_accion.'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar acción
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar acción
	function rellena_contenido_ventana_anyadir_modificar_accion($anyadir_accion, $id_accion, &$contenido)
	{
		$idiomas = new Idiomas();

        // Si hay que modificar la acción (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_accion != ID_NINGUNO)
		{
			$fila_accion_regla = dame_fila_accion_regla($id_accion);

            $nombre = $fila_accion_regla["nombre"];
            $causa = $fila_accion_regla["causa"];
            $clase_actuador = $fila_accion_regla["clase"];
            $destino = $fila_accion_regla["destino"];
            $id_destino = $fila_accion_regla["id_destino"];
            $contenido_accion = $fila_accion_regla["contenido_accion"];
            $valor_accion = $fila_accion_regla["valor_accion"];
		}
        else
        {
            $clase_actuador = CLASE_NINGUNA;
            $destino = DESTINO_ACCION_ACTUADOR;
            $id_destino = ID_NINGUNO;
        }

        // Se muestran las siguientes pestañas:
        // - Principal, acciones (predefinidas), mensaje y acción (sólo un contenido de tipo texto texto)
        $contenido = "
            <div id='tabs-administracion-accion-reglas' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-accion' id='titulo-tab-accion'>".$idiomas->_("Acción")."</a></li>
                </ul>
                <div id='tabs-content-administracion-accion-reglas' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_accion_regla'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Causa").": "."</span><br/>
                    <select id='causa_accion_regla' class='select-administracion'>";
        $contenido .= dame_lista_causas_ejecucion_accion_regla($causa);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
					<select id='clase_actuador_accion_regla' class='select-administracion'>";
        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de destino").": "."</span><br/>
                    <select id='destino_accion_regla' class='select-administracion'>";
        $contenido .= dame_lista_destinos_accion($destino);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Destino").": "."</span><br/>
                    <select id='id_destino_accion_regla' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_destinos_accion($clase_actuador, $destino, $id_destino);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de acción
        $contenido .= "
                    <div class='tab-pane' id='tab-accion'>";

        // Se recuperan los controles de la acción
		$contenido .= "
            <div id='controles_accion_regla'>".
                dame_controles_accion($clase_actuador, $contenido_accion, $valor_accion, ORIGEN_CONTROLES_ACCION_REGLA)."
            </div>";

        $contenido .= "
                    </div>";

        return ("OK");
	}
?>
