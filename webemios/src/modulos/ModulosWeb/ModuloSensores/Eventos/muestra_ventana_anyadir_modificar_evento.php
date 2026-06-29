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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_EVENTO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_evento = $_POST["id_evento"];
    if ($id_evento === NULL)
    {
        $id_evento = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar evento
    $anyadir_evento = (($id_evento == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_evento == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_sensores_anyadir_modificar_evento">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("evento");
    if (($anyadir_evento == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_evento($anyadir_evento, $id_evento, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_evento"
            anyadir_evento="'.$anyadir_evento.'"
            id_evento="'.$id_evento.'"
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar evento
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar evento
	function rellena_contenido_ventana_anyadir_modificar_evento($anyadir_evento, $id_evento, &$contenido)
	{
		$idiomas = new Idiomas();

        // Si hay que modificar el evento (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_evento != ID_NINGUNO)
		{
			$fila_evento = dame_fila_evento($id_evento);

			$nombre = $fila_evento["nombre"];
            $descripcion = $fila_evento["descripcion"];
            $clase_sensor = $fila_evento["clase"];
            $origen = $fila_evento["origen"];
            $id_origen = $fila_evento["id_origen"];
            $granularidad = $fila_evento["granularidad"];
            $tipo = $fila_evento["tipo"];
            $cadena_parametros = $fila_evento["parametros"];
            $alarma = $fila_evento["alarma"];

            // Se recuperan los parámetros del evento
            $parametros = Evento::dame_nombres_valores_parametros_evento(
                $clase_sensor,
                $granularidad,
                $tipo,
                $cadena_parametros);
            if ($parametros !== NULL)
            {
                $cadena_parametros_campo = $parametros["cadena_parametros_campo"];
                $campo = $parametros["campo"];
            }
		}
        else
        {
            $nombre = "";
            $clase_sensor = CLASE_NINGUNA;
            $origen = ORIGEN_EVENTO_SENSOR;
            $id_origen = ID_NINGUNO;
            $granularidad = GRANULARIDAD_HORARIA;
            $tipo = ID_NINGUNO;
            $alarma = VALOR_NO;

            $cadena_parametros_campo = "";
            $campo = CAMPO_NINGUNO;
        }

        // Se muestran las pestañas de la ventana
        $contenido = "
            <div id='tabs-administracion-evento' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='tabs-pestanyas-eventos'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-parametros' id='titulo-tab-parametros' style='display: none;'>".$idiomas->_("Parámetros")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-horario-semanal-fechas' id='titulo-tab-horario-semanal-fechas' style='display: none;'>".$idiomas->_("Horario semanal y fechas")."</a></li>
                </ul>
                <div id='tabs-content-administracion-widget' class='tab-content'>";

        // Pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_evento'
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
                    <textarea id='descripcion_evento'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
					<select id='clase_sensor_evento' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de origen").": "."</span><br/>
					<select id='origen_evento' class='select-administracion'>";
        $contenido .= dame_lista_origenes_evento($origen, OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_TODOS);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Origen").": "."</span><br/>
                    <select id='id_origen_evento' class='chosen-select-administracion'>";
        $contenido .= dame_lista_ids_origenes_evento($clase_sensor, $origen, $id_origen, OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_NINGUNO);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_granularidad_evento'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad").": "."</span><br/>
                    <select id='granularidad_evento' class='select-administracion'>";
        $contenido .= dame_lista_granularidades_evento($clase_sensor, $granularidad, OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_SIN_OPCIONES_EXTRA);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_tipo_evento'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_evento' class='select-administracion'>";
        $contenido .= dame_lista_tipos_evento($clase_sensor, $origen, $granularidad, $tipo);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Alarma").": "."</span><br/>
					<select id='alarma_evento' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($alarma);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Pestaña de parámetros
        $contenido .= "
                    <div class='tab-pane' id='tab-parametros'>";

        $contenido .= "
            <div class='row-fluid' id='control_campo_evento'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_evento' class='select-administracion'>";
        $contenido .= dame_lista_campos_evento($clase_sensor, $granularidad, $tipo, $campo);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Parámetros específicos de eventos

        // Eventos de incremento acumulado máximo
        anyade_controles_evento_incremento_acumulado_maximo($tipo, $parametros, $contenido);

        // Evento de línea base
        anyade_controles_evento_linea_base($tipo, $parametros, $id_origen, $contenido);

        // Evento de perfil horario
        anyade_controles_evento_perfil_horario($tipo, $parametros, $contenido);

        // Campo de parámetros (común a todos los eventos)
        $contenido .= "
			<div class='row-fluid' id='control_parametros_evento'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Parámetros').": "."</span><br/>
					<input type='text' id='parametros_evento'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cadena_parametros_campo, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_parametros_evento' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de horario semanal, exclusión e inclusión de fechas (utilizada por varios tipos de eventos)
        anyade_controles_pestanya_horario_semanal_fechas("evento", $parametros, $contenido);

        return ("OK");
	}


    //
    // Funciones de controles de tipos de evento
    //


    function anyade_controles_evento_incremento_acumulado_maximo($tipo, $parametros, &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if (($tipo != TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL) &&
            ($tipo != TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO))
        {
        }
        else
        {
            $periodo_tiempo = $parametros["periodo_tiempo"];
        }

        // Controles del tipo de evento
        $contenido .= "
            <div class='row-fluid' id='control_periodo_evento_incremento_acumulado_maximo'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_evento_incremento_acumulado_maximo' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_evento($periodo_tiempo);
		$contenido .= "
                    </select>
				</div>
			</div>";
    }


    function anyade_controles_evento_linea_base($tipo, $parametros, $id_origen, &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_EVENTO_LINEA_BASE)
        {
            $id_linea_base = ID_NINGUNO;
        }
        else
        {
            $id_linea_base = $parametros["id_linea_base"];
        }

        // Controles del tipo de evento
        $contenido .= "
            <div class='row-fluid' id='control_id_linea_base_evento_linea_base'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Línea base").": "."</span><br/>
                    <select id='id_linea_base_evento_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_lineas_base_sensor($id_origen, $id_linea_base);
		$contenido .= "
                    </select>
				</div>
			</div>";
    }


    function anyade_controles_evento_perfil_horario($tipo, $parametros, &$contenido)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo
        if ($tipo != TIPO_EVENTO_PERFIL_HORARIO)
        {
            $intervalo_valores = INTERVALO_VALORES_HORA;
            $numero_dias_perfil_horario = 7;
            $tipo_perfil_horario = TIPO_PERFIL_HORARIO_DIARIO;
            $cadena_agrupaciones_dias_semana = "1-2-3-4-5, 6-7";
        }
        else
        {
            $intervalo_valores = $parametros["intervalo_valores"];
            $numero_dias_perfil_horario = $parametros["numero_dias_perfil_horario"];
            $tipo_perfil_horario = $parametros["tipo_perfil_horario"];
            $agrupaciones_dias_semana = $parametros["agrupaciones_dias_semana"];
            $cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana($agrupaciones_dias_semana);
        }

        $contenido .= "
            <div class='row-fluid' id='control_intervalo_valores_evento_perfil_horario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
                    <select id='intervalo_valores_evento_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)),
                array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA))),
            array($intervalo_valores));
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
			<div class='row-fluid' id='control_numero_dias_perfil_horario_evento_perfil_horario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Número de días de perfil horario').": "."</span><br/>
					<input type='text' id='numero_dias_perfil_horario_evento_perfil_horario'
						class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_dias_perfil_horario."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_tipo_perfil_horario_evento_perfil_horario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de perfil horario").": "."</span><br/>
                    <select id='tipo_perfil_horario_evento_perfil_horario' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_PERFIL_HORARIO_SEMANAL, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_SEMANAL)),
                array(TIPO_PERFIL_HORARIO_DIARIO, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_DIARIO)),
                array(TIPO_PERFIL_HORARIO_CONFIGURABLE, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_CONFIGURABLE))),
            array($tipo_perfil_horario));
		$contenido .= "
                    </select>
				</div>
			</div>

			<div class='row-fluid' id='control_cadena_agrupaciones_dias_semana_evento_perfil_horario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Agrupaciones de días de la semana').": "."</span><br/>
					<input type='text' id='cadena_agrupaciones_dias_semana_evento_perfil_horario'
						class='TLNT_input_valid_characters input-administracion' value='".$cadena_agrupaciones_dias_semana."'>
                    <span id='boton_sensores_ayuda_agrupaciones_dias_semana_evento_perfil_horario' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";
    }
?>
