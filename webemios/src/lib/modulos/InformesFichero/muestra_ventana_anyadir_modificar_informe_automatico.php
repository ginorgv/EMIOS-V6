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
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/InformeAutomatico.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_INFORME_AUTOMATICO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_informe_automatico = $_POST["id_informe_automatico"];
    if ($id_informe_automatico === NULL)
    {
        $id_informe_automatico = ID_NINGUNO;
    }

    // Añadir o modificar informe automático
    $anyadir_informe_automatico = ($id_informe_automatico == ID_NINGUNO);
    if ($anyadir_informe_automatico == true)
    {
        $titulo .= $idiomas->_("Añadir");
        $tipo = $_POST["tipo"];
        $parametros_tipo = $_POST["parametros_tipo"];
        $parametros_tipo_json = $_POST["parametros_tipo_json"];
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_informe_automatico">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("informe automático");
    $error = rellena_contenido_ventana_anyadir_modificar_informe_automatico(
        $anyadir_informe_automatico,
        $id_informe_automatico,
        $tipo,
        $parametros_tipo,
        $parametros_tipo_json,
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar informe automático
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar informe automático
	function rellena_contenido_ventana_anyadir_modificar_informe_automatico(
        $anyadir_informe_automatico,
        $id_informe_automatico,
        $tipo,
        $parametros_tipo,
        $parametros_tipo_json,
        &$contenido)
	{
		$idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

		$nombre = "";

		// Si hay que modificar el informe automático, se recupera la información actual de la base de datos
		if ($anyadir_informe_automatico == false)
		{
			$consulta_informe_automatico = "
				SELECT *
				FROM informes_automaticos
				WHERE
					id = '".$bd_red->_($id_informe_automatico)."'";
			$res_informe_automatico = $bd_red->ejecuta_consulta($consulta_informe_automatico);
			if (($res_informe_automatico == false) || ($res_informe_automatico->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_informe_automatico."'");
            }

			$fila_informe_automatico = $res_informe_automatico->dame_siguiente_fila();
			$nombre = $fila_informe_automatico["nombre"];
			$periodicidad = $fila_informe_automatico["periodicidad"];
            $cadena_parametros_periodicidad = $fila_informe_automatico["parametros_periodicidad"];
            $cadena_parametros_periodo_tiempo = $fila_informe_automatico["parametros_periodo_tiempo"];
            $numero_horas_desplazamiento = $fila_informe_automatico["numero_horas_desplazamiento"];
            $tipo = $fila_informe_automatico["tipo"];
            $parametros_tipo = $fila_informe_automatico["parametros_tipo"];
            $parametros_tipo_json = $fila_informe_automatico["parametros_tipo_json"];
            $direcciones_email_destino = $fila_informe_automatico["direcciones_email_destino"];
            $hora_envio_informe = $fila_informe_automatico["hora_envio"];
            $periodo_personalizado = $fila_informe_automatico["parametros_periodo_personalizado"];

            // Parámetros de periodicidad
            $parametros_periodicidad = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_periodicidad);
            $dia_generacion = $parametros_periodicidad[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_DIA_GENERACION_INFORME];
            $numero_dias_retraso_generacion = $parametros_periodicidad[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODICIDAD_NUMERO_DIAS_RETRASO_GENERACION_INFORME];

            // Parámetros de periodo de tiempo
            $parametros_periodo_tiempo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_periodo_tiempo);
            $tipo_seleccion_periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_TIPO_SELECCION_PERIODO_TIEMPO];
            $periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_PERIODO_TIEMPO];
            $iniciar_comienzo_periodo_tiempo = $parametros_periodo_tiempo[INDICE_PARAMETRO_INFORME_AUTOMATICO_PERIODO_TIEMPO_INICIAR_COMIENZO_PERIODO_TIEMPO];

            if ($periodicidad == PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA)
            {
                // Parametros de periodo personalizado
                $parametros_periodo_personalizado = explode(SEPARADOR_PARAMETROS_SIMPLES, $periodo_personalizado);
                $tipo_periodo_seleccionado = $parametros_periodo_personalizado[0];
                $numero_periodos_periodo_personalizado = $parametros_periodo_personalizado[1];
                $fecha_siguiente_envio = convierte_formato_fecha($parametros_periodo_personalizado[2], 'Y-m-d', 'd/m/Y');
            }

		}
        else
        {
            $dia_generacion = 1;
            $numero_dias_retraso_generacion = 0;
            $tipo_seleccion_periodo_tiempo = TIPO_SELECCION_PERIODO_TIEMPO_AUTOMATICO;
            $periodo_tiempo = PERIODO_TIEMPO_SEMANA;
            $iniciar_comienzo_periodo_tiempo = VALOR_SI;
            $numero_horas_desplazamiento = 0;
            $hora_envio_informe = "06:00";
            $tipo_periodo_seleccionado = "DIARIO";
            $numero_periodos_periodo_personalizado = 1;
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_informe_automatico'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de envío").": "."</span><br/>
                    <span class='bootstrap-timepicker'>
                        <input type='text' id='hora_envio_informe_automatico' class='selector-hora timepicker TLNT_input_mandatory no_minute_selector'
                            readonly='readonly' value='".htmlspecialchars($hora_envio_informe, ENT_QUOTES)."'>
                    </span>
                    <i class='icon-info-sign color-azul'></i>
                        Solo es seleccionable la hora
                </div>
            </div>
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Periodicidad').": "."</span><br/>
					<select id='periodicidad_informe_automatico' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(PERIODICIDAD_INFORME_AUTOMATICO_DIARIA, $idiomas->_("Diaria")),
                array(PERIODICIDAD_INFORME_AUTOMATICO_SEMANAL, $idiomas->_("Semanal")),
                array(PERIODICIDAD_INFORME_AUTOMATICO_MENSUAL, $idiomas->_("Mensual")),
                array(PERIODICIDAD_INFORME_AUTOMATICO_PERSONALIZADA, $idiomas->_("Personalizada"))),
            array($periodicidad));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_dia_semana_informe_automatico'>
                <divclass='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de semana").": "."</span><br/>
                    <select id='dia_semana_informe_automatico' class='select-administracion'>";
        $dia_semana = $parametros_periodicidad;
        if ($dia_semana == "")
        {
            $dia_semana = 1;
        }
        for ($i = 1; $i <= 7; $i++)
        {
            $contenido .= dame_opcion_dia_semana($i, $dia_generacion);
        }
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_dia_mes_informe_automatico'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de mes").": "."</span><br/>
					<input type='text' id='dia_mes_informe_automatico'
						class='TLNT_input_integer input-administracion' value='".$dia_generacion."'>
				</div>
			</div>

            <div class='row-fluid clase_control_periodo_personalizado' id='control_tipo_periodo_informe_automatico'>
                <divclass='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo periodo").": "."</span><br/>
                    <select id='tipo_periodo_informe_automatico' class='select-administracion'>";
                        $tipo_periodo = $tipo_periodo_seleccionado;
                        $tipos_periodos = array('DIARIO','SEMANAL','MENSUAL');
                        if ($tipo_periodo == "")
                        {
                            $tipo_periodo = $tipos_periodos[0];
                        }
                        for ($i = 0; $i <= 2; $i++)
                        {
                            $contenido .= dame_lista_tipos_periodos($tipos_periodos[$i], $tipo_periodo);
                        }
                        $contenido .= "
                    </select>
                </div>
			</div>

            <div class='row-fluid clase_control_periodo_personalizado' id='control_numero_periodos_personalizado_informe_automatico'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de periodos").": "."</span><br/>
					<input type='text' id='numero_periodos_informe_automatico'
						class='TLNT_input_integer input-administracion' value='".$numero_periodos_periodo_personalizado."'>
				</div>
			</div>
            <div class='row-fluid clase_control_periodo_personalizado' id='control_fecha_primer_envio'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha siguiente envío").": "."</span><br/>
                    <input size='10' type='text' id='fecha_proximo_envio_informe_automatico' class='datepicker selector-fechas-administracion'
                        readonly='readonly' value='".htmlspecialchars($fecha_siguiente_envio, ENT_QUOTES)."'>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de días de retraso").": "."</span><br/>
					<input type='text' id='numero_dias_retraso_informe_automatico'
						class='TLNT_input_integer input-administracion' value='".$numero_dias_retraso_generacion."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de periodo de tiempo").": "."</span><br/>
                    <select id='tipo_seleccion_periodo_tiempo_informe_automatico' class='select-administracion'>";
        $contenido .= dame_lista_tipo_seleccion_periodo_tiempo($tipo_seleccion_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_periodo_tiempo_informe_automatico'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo").": "."</span><br/>
                    <select id='periodo_tiempo_informe_automatico' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo($periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_iniciar_comienzo_periodo_tiempo_informe_automatico'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo actual").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_informe_automatico' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas de desplazamiento").": "."</span><br/>
					<input type='text' id='numero_horas_desplazamiento_informe_automatico'
						class='TLNT_input_integer input-administracion' value='".$numero_horas_desplazamiento."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Direcciones e-mail de destino').": "."</span><br/>
					<input type='text' id='direcciones_email_destino_informe_automatico'
						class='TLNT_input_mandatory input-administracion' value='".htmlspecialchars($direcciones_email_destino, ENT_QUOTES)."'>
				</div>
			</div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_informe_automatico"
                anyadir_informe_automatico="'.$anyadir_informe_automatico.'"
                id_informe_automatico="'.$id_informe_automatico.'"
                tipo="'.$tipo.'"
                parametros_tipo="'.$parametros_tipo.'"
                parametros_tipo_json="'.htmlspecialchars($parametros_tipo_json, ENT_QUOTES).'"
                hidden>
            </div>';

        return ("OK");
	}


    //
    // Funciones auxiliares
    //


    // Devuelve la opción del día de la semana especificado
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


    // Devuelve la lista con el tipo de selección de periodo de tiempo
    function dame_lista_tipo_seleccion_periodo_tiempo($tipo_seleccion_periodo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_tipo_seleccion_periodo_tiempo = "";
        $lista_tipo_seleccion_periodo_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Automático"), TIPO_SELECCION_PERIODO_TIEMPO_AUTOMATICO, $tipo_seleccion_periodo_seleccionado);
        $lista_tipo_seleccion_periodo_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Configurable"), TIPO_SELECCION_PERIODO_TIEMPO_CONFIGURABLE, $tipo_seleccion_periodo_seleccionado);
        return ($lista_tipo_seleccion_periodo_tiempo);
    }

    // Devuelve los tipos de periodos implementados
    function dame_lista_tipos_periodos($tipo_periodo, $tipo_periodo_seleccionado)
    {
        $opcion .= "<option value='".$tipo_periodo."'";
        if ($tipo_periodo == $tipo_periodo_seleccionado)
        {
            $opcion .= " selected";
        }
        $opcion .= ">".dame_nombre_tipo_periodo($tipo_periodo)."</option>";

        return ($opcion);
    }
?>
