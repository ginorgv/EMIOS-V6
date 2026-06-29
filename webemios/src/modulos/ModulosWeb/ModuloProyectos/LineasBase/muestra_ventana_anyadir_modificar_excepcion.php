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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_EXCEPCION_LINEA_BASE, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_linea_base_padre = $_POST["id_linea_base_padre"];
    $id_linea_base_hija = $_POST["id_linea_base_hija"];
    $id_excepcion = $_POST["id_excepcion"];
    if ($id_excepcion === NULL)
    {
        $id_excepcion = ID_NINGUNO;
    }

    // Añadir o modificar excepción
    $anyadir_excepcion = ($id_excepcion == ID_NINGUNO);
    if ($anyadir_excepcion == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_proyectos_anyadir_modificar_excepcion_linea_base">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("excepción");
    $error = rellena_contenido_ventana_anyadir_modificar_excepcion_linea_base(
        $anyadir_excepcion,
        $id_linea_base_padre,
        $id_excepcion,
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
        <div id="parametros_ventana_anyadir_modificar_excepcion_linea_base"
            anyadir_excepcion="'.$anyadir_excepcion.'"
            id_linea_base_padre="'.$id_linea_base_padre.'"
            id_linea_base_hija="'.$id_linea_base_hija.'"
            id_excepcion="'.$id_excepcion.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar excepción
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar excepción de línea base
	function rellena_contenido_ventana_anyadir_modificar_excepcion_linea_base(
        $anyadir_excepcion,
        $id_linea_base_padre,
        $id_excepcion,
        &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar la excepción, se recupera la información actual de la base de datos
		if ($anyadir_excepcion == false)
		{
            $fila_excepcion_linea_base = dame_fila_excepcion_linea_base($id_excepcion);
            $nombre = $fila_excepcion_linea_base["nombre"];
            $descripcion = $fila_excepcion_linea_base["descripcion"];
            $id_linea_base_hija = $fila_excepcion_linea_base["linea_base_hija"];
            $cadena_horario_semanal = $fila_excepcion_linea_base["horario_semanal"];
            $cadena_inclusion_fechas = $fila_excepcion_linea_base["inclusion_fechas"];
            $horario_semanal = dame_horario_semanal($cadena_horario_semanal);
            $inclusion_fechas = dame_fechas($cadena_inclusion_fechas);
        }
        else
        {
            $horario_semanal = array(
                "correcto" => true,
                "selecciones_dias_semana" => array(0, 0, 0, 0, 0, 0, 0),
                "periodos_dias_semana" => array(
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59")),
                    array(array("00:00:00", "23:59:59"))));
            $inclusion_fechas = NULL;
        }

        // Intervalo de valores de la línea base padre
        $intervalo_valores_linea_base_padre = dame_intervalo_valores_linea_base($id_linea_base_padre);

        // Se muestran las siguientes pestañas:
        // - Principal, horario semanal y fechas
        $contenido = "
            <div id='tabs-administracion-excepcion-linea-base' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-horario-semanal-fechas' id='titulo-tab-horario-semanal-fechas'>".$idiomas->_("Horario semanal y fechas")."</a></li>
                </ul>
                <div id='tabs-content-administracion-excepcion-linea-base' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_excepcion_linea_base'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // Descripción
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='descripcion_excepcion_linea_base'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Línea base").": "."</span><br/>
                    <select id='id_linea_base_hija_excepcion_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_lineas_base_intervalo_valores($intervalo_valores_linea_base_padre, $id_linea_base_hija);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de horario semanal y fechas
        $contenido .= "
                    <div class='tab-pane' id='tab-horario-semanal-fechas'>";

        switch ($intervalo_valores_linea_base_padre)
        {
            case INTERVALO_VALORES_HORA:
            {
                $mostrar_horario_semanal = true;
                $mostrar_horas_horario_semanal = true;
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $mostrar_horario_semanal = true;
                $mostrar_horas_horario_semanal = false;
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                $mostrar_horario_semanal = false;
                $mostrar_horas_horario_semanal = false;
                break;
            }
        }
        $controles_horario_semanal = dame_controles_horario_semanal(
            "excepcion_linea_base",
            ORIGEN_CONTROLES_VENTANA_MODAL,
            $mostrar_horas_horario_semanal,
            $horario_semanal);
        anyade_controles_horario_semanal_ventana_modal(
            $contenido,
            "excepcion_linea_base",
            $controles_horario_semanal,
            $mostrar_horario_semanal);
        $controles_inclusion_fechas = dame_controles_fechas("inclusion_fechas_excepcion_linea_base", ORIGEN_CONTROLES_VENTANA_MODAL, $inclusion_fechas);
        anyade_controles_fechas_ventana_modal(
            $contenido,
            "inclusion_fechas_excepcion_linea_base",
            $idiomas->_("Inclusión de fechas"),
            $controles_inclusion_fechas);

        $contenido .= "
                    </div>";

        return ("OK");
    }
?>
