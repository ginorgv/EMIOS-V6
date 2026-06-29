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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/util_periodos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PERIODO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $id_periodo = $_POST["id_periodo"];
    if ($id_periodo === NULL)
    {
        $id_periodo = ID_NINGUNO;
    }

    // Añadir o modificar periodo
    $anyadir_periodo = ($id_periodo == ID_NINGUNO);
    if ($anyadir_periodo == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_periodo">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("periodo");
    $error = rellena_contenido_ventana_anyadir_modificar_periodo($anyadir_periodo, $id_periodo, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_periodo"
            anyadir_periodo="'.$anyadir_periodo.'"
            origen="'.$origen.'"
            id_origen="'.$id_origen.'"
            id_periodo="'.$id_periodo.'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar periodo
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar periodo
	function rellena_contenido_ventana_anyadir_modificar_periodo($anyadir_periodo, $id_periodo, &$contenido)
	{
		// Si hay que modificar el periodo, se recupera la información actual de la base de datos
		if ($anyadir_periodo == false)
		{
            $fila_periodo = dame_fila_periodo($id_periodo);

            $dia_inicio = $fila_periodo["dia_inicio"];
            $dia_fin = $fila_periodo["dia_fin"];
            $hora_inicio = $fila_periodo["hora_inicio"];
			$hora_fin = $fila_periodo["hora_fin"];
		}

        // Se recuperan los controles del periodo
		$contenido .= dame_controles_periodo($dia_inicio, $dia_fin, $hora_inicio, $hora_fin);

        return ("OK");
	}


    function dame_controles_periodo($dia_inicio, $dia_fin, $hora_inicio, $hora_fin)
    {
        $idiomas = new Idiomas();

        // Controles del periodo
        $controles_periodo = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de inicio").": "."</span><br/>
                    <select id='dia_inicio_periodo' class='select-administracion'>";
        for ($i = 1; $i <= 7; $i++)
        {
            $controles_periodo .= dame_opcion_dia_semana($i, $dia_inicio);
        }
        $controles_periodo .= "
                    </select>
                </div>
            </div>";

        $controles_periodo .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de fin").": "."</span><br/>
                    <select id='dia_fin_periodo' class='select-administracion'>";
        for ($i = 1; $i <= 7; $i++)
        {
            $controles_periodo .= dame_opcion_dia_semana($i, $dia_fin);
        }
        $controles_periodo .= "
                    </select>
                </div>
            </div>";

        $controles_periodo .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Hora de inicio").": "."</span><br/>
                    <span class='bootstrap-timepicker '>
                        <input id='hora_inicio_periodo' type='text' class='selector-hora timepicker' readonly='readonly'";
        if ($hora_inicio <> "")
        {
            $controles_periodo .= "value='".$hora_inicio."'";
        }
        $controles_periodo .= ">
                    </span>
                </div>
            </div>";

        $controles_periodo .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Hora de fin").": "."</span><br/>
                    <span class='bootstrap-timepicker '>
                        <input id='hora_fin_periodo' type='text' class='selector-hora timepicker' readonly='readonly'";
        if ($hora_fin <> "")
        {
            $controles_periodo .= "value='".$hora_fin."'";
        }
        $controles_periodo .= ">
                    </span>
                </div>
            </div>";

        return ($controles_periodo);
    }


    function dame_opcion_dia_semana($dia_semana, $dia_semana_seleccionado)
    {
        $opcion .= "<option value='".$dia_semana."'";
        if ($dia_semana == $dia_semana_seleccionado)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".dame_nombre_dia_semana($dia_semana)."</option>";

        return ($opcion);
    }
?>
