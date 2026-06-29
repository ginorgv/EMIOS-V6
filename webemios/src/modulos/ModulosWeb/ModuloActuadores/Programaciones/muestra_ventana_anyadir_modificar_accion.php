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

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_ACCION_PROGRAMACION, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";
    $clase_actuador = "";

    // Parámetros
    $id_programacion = $_POST["id_programacion"];
    $id_accion = $_POST["id_accion"];
    if ($id_accion === NULL)
    {
        $id_accion = ID_NINGUNO;
    }

    // Añadir o modificar acción
    $anyadir_accion = ($id_accion == ID_NINGUNO);
    if ($anyadir_accion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_accion_programacion">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("acción");
    $error = rellena_contenido_ventana_anyadir_modificar_accion($anyadir_accion, $id_programacion, $id_accion, $clase_actuador, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_accion_programacion"
            anyadir_accion="'.$anyadir_accion.'"
            clase_actuador="'.$clase_actuador.'"
            id_programacion="'.$id_programacion.'"
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
	function rellena_contenido_ventana_anyadir_modificar_accion($anyadir_accion, $id_programacion, $id_accion, &$clase_actuador, &$contenido)
	{
		$idiomas = new Idiomas();

        // Se recupera la clase de actuador de la programación
        $fila_programacion = dame_fila_programacion($id_programacion);
        $clase_actuador = $fila_programacion["clase"];

		// Si hay que modificar la acción, se recupera la información actual de la base de datos
		if ($anyadir_accion == false)
		{
			$fila_accion_programacion = dame_fila_accion_programacion($id_accion);

            $nombre_accion = $fila_accion_programacion["nombre"];
            $contenido_accion = $fila_accion_programacion["contenido"];
            $valor_accion = $fila_accion_programacion["valor"];
            $cadena_dias_semana = $fila_accion_programacion["dias_semana"];
            // (1: lunes - 7: domingo, -1: todos)
            if ($cadena_dias_semana == "-1")
            {
                $cadena_dias_semana = "1,2,3,4,5,6,7";
            }
            $dias_semana = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_dias_semana);
			$hora = $fila_accion_programacion["hora"];
		}

        // Contenido de la ventana

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Días").": "."</span><br/>
                    <div id='select_dias_semana_accion_programacion_no_visible' hidden></div>
					<select id='dias_semana_accion_programacion'
                        name='dias_semana_accion_programacion'
                        max_selected='7' multiple='multiple'
						class='select-administracion' hidden>";
        for ($i = 1; $i <= 7; $i++)
        {
            $contenido .= dame_opcion_dia_semana($i, $dias_semana);
        }
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Hora").": "."</span><br/>
                    <span class='bootstrap-timepicker '>
                        <input id='hora_accion_programacion' type='text' class='selector-hora timepicker' readonly='readonly'";
        if ($hora <> "")
        {
            $contenido .= "value='".$hora."'";
        }
        $contenido .= ">
                    </span>
                </div>
            </div>";

        // Nombre de la acción (sólo si no son acciones predefinidas)
        $acciones_predefinidas = false;
        switch ($clase_actuador)
        {
            case CLASE_ACTUADOR_INTERRUPTOR:
            case CLASE_ACTUADOR_TELEPOSTE:
            case CLASE_ACTUADOR_LUZ_GRADUAL_4:
            {
                $acciones_predefinidas = true;
                break;
            }
        }
        if ($acciones_predefinidas == false)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                        <input type='text' id='nombre_accion_programacion'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_accion, ENT_QUOTES)."'>
                    </div>
                </div>";
        }

        // Se muestran los controles de la acción
        $contenido .= dame_controles_accion($clase_actuador, $contenido_accion, $valor_accion, ORIGEN_CONTROLES_ACCION_PROGRAMACION);

        return ("OK");
	}


    function dame_opcion_dia_semana($dia_semana, $dias_semana_seleccionados)
    {
        $opcion .= "<option value='".$dia_semana."' sort_id='".$dia_semana."'";
        if (in_array($dia_semana, $dias_semana_seleccionados) == true)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".dame_nombre_dia_semana($dia_semana)."</option>";

        return ($opcion);
    }
?>
