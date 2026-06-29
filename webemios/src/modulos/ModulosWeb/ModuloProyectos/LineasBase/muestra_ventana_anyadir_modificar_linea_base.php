<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_LINEA_BASE, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_linea_base = $_POST["id_linea_base"];
    if ($id_linea_base === NULL)
    {
        $id_linea_base = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Flag que indica si se puede realizar la operación
    $operacion_permitida = True;

    // Añadir o modificar línea base
    if ($operacion_permitida == true)
    {
        $anyadir_linea_base = (($id_linea_base == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
        if ($anyadir_linea_base == true)
        {
            $titulo .= $idiomas->_("Añadir");
        }
        else
        {
            $titulo .= $idiomas->_("Modificar");
        }
        $pie .= '<button class="btn btn-success boton_proyectos_anyadir_modificar_linea_base">'.$titulo.'</button>';
        $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

        // Título
        $titulo .= " ".$idiomas->_("línea base");
        if (($anyadir_linea_base == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
        {
            $titulo .= " (".$idiomas->_("duplicar").")";
        }

        // Se recupera el contenido de la ventana
        $error = rellena_contenido_ventana_anyadir_modificar_linea_base($anyadir_linea_base, $id_linea_base, $contenido);
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
        if ($id_linea_base != ID_NINGUNO)
        {
            $fila_linea_base = dame_fila_linea_base($id_linea_base);
            $id_sensor = $fila_linea_base["sensor"];
            $campo = $fila_linea_base["campo"];
            $intervalo_valores = $fila_linea_base["intervalo_valores"];
            $cadena_fecha_inicio_periodo_referencia_base_datos_local = $fila_linea_base["fecha_inicio_periodo_referencia"];
            $cadena_fecha_fin_periodo_referencia_base_datos_local = $fila_linea_base["fecha_fin_periodo_referencia"];

            // Conversión de fechas
            $cadena_fecha_inicio_periodo_referencia_local_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_referencia_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_periodo_referencia_local_local = convierte_formato_fecha($cadena_fecha_fin_periodo_referencia_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        }
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_linea_base"
                anyadir_linea_base="'.$anyadir_linea_base.'"
                id_linea_base="'.$id_linea_base.'"
                id_sensor="'.$id_sensor.'"
                campo="'.$campo.'"
                intervalo_valores="'.$intervalo_valores.'"
                fecha_inicio_periodo_referencia="'.$cadena_fecha_inicio_periodo_referencia_local_local.'"
                fecha_fin_periodo_referencia="'.$cadena_fecha_fin_periodo_referencia_local_local.'"
                hidden>
            </div>';
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Función para mostrar el contenido de la ventana de anyadir/modificar línea base
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar línea base
	function rellena_contenido_ventana_anyadir_modificar_linea_base($anyadir_linea_base, $id_linea_base, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la línea base (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_linea_base != ID_NINGUNO)
		{
			$fila_linea_base = dame_fila_linea_base($id_linea_base);

			$nombre = $fila_linea_base["nombre"];
            $descripcion = $fila_linea_base["descripcion"];
            $clase_sensor = $fila_linea_base["clase_sensor"];
            $id_sensor = $fila_linea_base["sensor"];
            $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $fila_linea_base["campo_parametros_extra"]);
            $campo = $campo_parametros_extra[0];
            $parametros_extra_campo = $campo_parametros_extra[1];
            $tipo = $fila_linea_base["tipo"];
            $parametros_tipo = $fila_linea_base["parametros_tipo"];
            $intervalo_valores = $fila_linea_base["intervalo_valores"];
            $cadena_fecha_inicio_periodo_referencia_local_local = convierte_formato_fecha($fila_linea_base["fecha_inicio_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_periodo_referencia_local_local = convierte_formato_fecha($fila_linea_base["fecha_fin_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $error_estandar = $fila_linea_base["error_estandar"];
            $coeficiente_variacion = $fila_linea_base["coeficiente_variacion"];
            $coeficiente_correlacion = $fila_linea_base["coeficiente_correlacion"];
            $cadena_horario_semanal = $fila_linea_base['horario_semanal'];
            $cadena_exclusion_fechas = $fila_linea_base['exclusion_fechas'];
            $horario_semanal = dame_horario_semanal($cadena_horario_semanal);
            $exclusion_fechas = dame_fechas($cadena_exclusion_fechas);

            // Parámetros específicos del tipo de la línea base
            switch ($tipo)
            {
                case TIPO_LINEA_BASE_PERIODICA:
                {
                    $parametros_linea_base_periodica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
                    $periodicidad_valores = $parametros_linea_base_periodica[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_PERIODICIDAD_VALORES];
                    $tipo_calculo_valores = $parametros_linea_base_periodica[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_TIPO_CALCULO_VALORES];
                    break;
                }
                case TIPO_LINEA_BASE_FUNCIONAL:
                {
                    $parametros_linea_base_funcional = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
                    $funcion_valores = $parametros_linea_base_funcional[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];
                    break;
                }
            }
		}
        else
        {
            $nombre = "";
            $descripcion = "";
            $clase_sensor = CLASE_NINGUNA;
            $id_sensor = ID_NINGUNO;
            $campo = CAMPO_NINGUNO;
            $tipo = TIPO_NINGUNO;
            $intervalo_valores = INTERVALO_VALORES_NINGUNO;
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $cadena_fecha_inicio_periodo_referencia_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_periodo_referencia_local_local = convierte_fecha_a_cadena($fecha_hora_actual_local, $_SESSION["formato_fecha_local"]);
            $periodicidad_valores = ID_NINGUNO;
            $tipo_calculo_valores = TIPO_NINGUNO;
            $horario_semanal = NULL;
            $exclusion_fechas = NULL;
            $funcion_valores = "";
            $error_estandar = "";
            $coeficiente_variacion = "";
            $coeficiente_correlacion = "";
        }

        // Se muestran las siguientes pestañas:
        // - Principal, periódica, horario semanal y fechas y funcional
        $contenido = "
            <div id='tabs-administracion-linea-base' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-periodica' id='titulo-tab-tipo-periodica'>".$idiomas->_("Periódica")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-funcional' id='titulo-tab-tipo-funcional'>".$idiomas->_("Funcional")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-horario-semanal-fechas' id='titulo-tab-horario-semanal-fechas'>".$idiomas->_("Horario semanal y fechas")."</a></li>
                </ul>
                <div id='tabs-content-administracion-linea-base' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_linea_base'
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
                    <textarea id='descripcion_linea_base'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor").": "."</span><br/>
                    <select id='id_sensor_linea_base' class='chosen-select-administracion'>";
        $contenido .= dame_lista_sensores($clase_sensor, array($id_sensor), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Campo").": "."</span><br/>
                    <select id='campo_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores, $campo);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_parametros_extra_campo_linea_base' hidden>
                <div class='span12'><span id='etiqueta_parametros_extra_campo_linea_base' class='titulo-campo-administracion'>"."ND".": "."</span><br/>
                    <input type='text' id='parametros_extra_campo_linea_base'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$parametros_extra_campo."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_tipos_linea_base(OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO, $tipo);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Intervalo de valores").": "."</span><br/>
					<select id='intervalo_valores_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_intervalos_valores_linea_base($tipo, OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO, $intervalo_valores);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio del periodo de referencia").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_referencia_linea_base' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_inicio_periodo_referencia_local_local."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin del periodo de referencia").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_periodo_referencia_linea_base' class='selector-fecha datepicker'
                        readonly='readonly' value='".$cadena_fecha_fin_periodo_referencia_local_local."'>
                    </span>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña periódica
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-periodica'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodicidad de valores").": "."</span><br/>
					<select id='periodicidad_valores_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_periodicidades_valores_linea_base_periodica($periodicidad_valores);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de cálculo de valores").": "."</span><br/>
					<select id='tipo_calculo_valores_linea_base' class='select-administracion'>";
        $contenido .= dame_lista_tipos_calculo_valores_linea_base_periodica($tipo_calculo_valores);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña funcional
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-funcional'>";

        // Función de valores
        $numero_caracteres_actuales = dame_numero_caracteres($funcion_valores);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_LINEA_BASE_FUNCIONAL;
        $contenido .= "
            <div class='row-fluid' id='control_funcion_valores_linea_base'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Función de valores").": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='funcion_valores_linea_base'
                        class='TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($funcion_valores, ENT_QUOTES)."</textarea>
                    <span id='boton_proyectos_ayuda_funcion_valores_linea_base' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>

            <div class='row-fluid' id='control_valores_prueba_funcion_valores_linea_base'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores de prueba de la función de valores").": "."</span><br/>
                    <input type='text' id='valores_prueba_funcion_valores_linea_base'
                        class='TLNT_input_valid_characters input-administracion' value=''>
                    <span id='boton_proyectos_ayuda_valores_prueba_funcion_valores_linea_base' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Error estándar')." (".$idiomas->_("RMSE")."): "."</span><br/>
					<input type='text' id='error_estandar_linea_base'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$error_estandar."'>
				</div>
			</div>

			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Coeficiente de variación').": "."</span><br/>
					<input type='text' id='coeficiente_variacion_linea_base'
						class='TLNT_input_float input-administracion' value='".$coeficiente_variacion."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Coeficiente de correlación')." (".$idiomas->_("R2").")".": "."</span><br/>
					<input type='text' id='coeficiente_correlacion_linea_base'
						class='TLNT_input_float input-administracion' value='".$coeficiente_correlacion."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de horario semanal y fechas
        $contenido .= "
                    <div class='tab-pane' id='tab-horario-semanal-fechas'>";

        $controles_horario_semanal = dame_controles_horario_semanal(
            "linea_base",
            ORIGEN_CONTROLES_VENTANA_MODAL,
            true,
            $horario_semanal);
        anyade_controles_horario_semanal_ventana_modal(
            $contenido,
            "linea_base",
            $controles_horario_semanal,
            true);
        $controles_exclusion_fechas = dame_controles_fechas("exclusion_fechas_linea_base", ORIGEN_CONTROLES_VENTANA_MODAL, $exclusion_fechas);
        anyade_controles_fechas_ventana_modal(
            $contenido,
            "exclusion_fechas_linea_base",
            $idiomas->_("Exclusión de fechas"),
            $controles_exclusion_fechas);

        $contenido .= "
                    </div>";

        return ("OK");
	}
?>
