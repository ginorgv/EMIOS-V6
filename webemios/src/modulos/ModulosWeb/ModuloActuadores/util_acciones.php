<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');


    //
    // Funciones de listas de acciones
    //


    // Devuelve la lista de destinos de una acción
    function dame_lista_destinos_accion(&$destino_seleccionado)
    {
        $destinos_accion = array(
            DESTINO_ACCION_ACTUADOR,
            DESTINO_ACCION_GRUPO_ACTUADORES);
        foreach ($destinos_accion as $destino_accion)
        {
            if ($destino_seleccionado == ID_NINGUNO)
            {
                $destino_seleccionado = $destino_accion;
            }
            $lista .= "<option value='".$destino_accion."'";
			if ($destino_accion == $destino_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".dame_descripcion_destino_accion($destino_accion)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de identificadores de destinos de una acción
    function dame_lista_ids_destinos_accion($clase_actuador, $destino, &$id_destino_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Tipos de destinos de la acción
        switch ($destino)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_destinos = "
                    SELECT
                        id,
                        nombre
                    FROM actuadores
                    WHERE
                        (red = '".$_SESSION["id_red"]."')
                        AND (clase = '".$bd_red->_($clase_actuador)."')";
                $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                if ($mostrar_todos_actuadores == false)
                {
                    $consulta_destinos .= "
                        AND ".dame_condicion_consulta_actuadores_usuario_actual(true);
                }
                $consulta_destinos .= "
                    ORDER BY nombre ASC";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_destinos = "
                    SELECT
                        id,
                        nombre
                    FROM grupos_actuadores
                    WHERE
                        (red = '".$_SESSION["id_red"]."')
                        AND (clase = '".$bd_red->_($clase_actuador)."')";
                $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                if ($mostrar_todos_actuadores == false)
                {
                    $consulta_destinos .= "
                        AND ".dame_condicion_consulta_grupos_actuadores_usuario_actual(false);
                }
                $consulta_destinos .= "
                    ORDER BY nombre ASC";
                break;
            }
        }
        $res_destinos = $bd_red->ejecuta_consulta($consulta_destinos);
        if ($res_destinos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_destinos."'");
        }

        $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_destino = $res_destinos->dame_siguiente_fila())
        {
            $lista .= "<option value='".$fila_destino['id']."'";
			if ($fila_destino['id'] == $id_destino_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$fila_destino['nombre']."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de origenes de acciones
    function dame_lista_origenes_acciones($origen_acciones_seleccionado)
    {
        $origenes_acciones = array(
            ORIGEN_ACCIONES_TODOS,
            ORIGEN_ACCIONES_MANUAL,
            ORIGEN_ACCIONES_ULTIMA_ACCION,
            ORIGEN_ACCIONES_REGLA,
            ORIGEN_ACCIONES_PROGRAMACION);
        foreach ($origenes_acciones as $origen_acciones)
        {
            $lista .= "<option value='".$origen_acciones."'";
			if ($origen_acciones == $origen_acciones_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".dame_descripcion_origen_acciones($origen_acciones)."</option>";
        }

        return ($lista);
    }


    // Devuelve los controles para el filtro de acciones en acciones enviadas
    function dame_controles_filtro_acciones_acciones_enviadas($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_clases_actuador = dame_control_lista_clases_actuador($id_controles, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA, true, $idiomas->_("Clase de actuador"));
        $control_lista_destinos_accion = dame_control_lista_destinos_accion($id_controles, DESTINO_ACCION_ACTUADOR);
        $control_lista_ids_destinos_accion = dame_control_lista_ids_destinos_accion($id_controles, CLASE_NINGUNA, DESTINO_ACCION_ACTUADOR);
        $control_lista_origenes_acciones = dame_control_lista_origenes_acciones($id_controles, ORIGEN_ACCIONES_TODOS);

        $controles_listas = array(
            $control_lista_clases_actuador,
            $control_lista_destinos_accion,
            $control_lista_ids_destinos_accion,
            $control_lista_origenes_acciones,
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección del destino de una acción
    function dame_control_lista_destinos_accion($id_controles, $destino_seleccionado)
    {
        $idiomas = new Idiomas();

        $control_lista_origenes = "";
        $control_lista_origenes .= "<div id='etiqueta_destino_accion_".$id_controles."'>".$idiomas->_("Tipo de destino").": "."</div>";
        $control_lista_origenes .= "<select id='destino_accion_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_origenes .= dame_lista_destinos_accion($destino_seleccionado);
        $control_lista_origenes .= "</select>";
        return ($control_lista_origenes);
    }


    // Crea una lista desplegable para la selección del identificador del destino de una acción
    function dame_control_lista_ids_destinos_accion($id_controles, $clase_actuador, $destino, $id_destino_seleccionado)
    {
        $idiomas = new Idiomas();

        $control_lista_ids_destinos = "";
        $control_lista_ids_destinos .= "<div id='etiqueta_id_destino_accion_".$id_controles."'>".$idiomas->_("Destino").": "."</div>";
        $control_lista_ids_destinos .= "<select id='id_destino_accion_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_ids_destinos .= dame_lista_ids_destinos_accion($clase_actuador, $destino, $id_destino_seleccionado);
        $control_lista_ids_destinos .= "</select>";
        return ($control_lista_ids_destinos);
    }


    // Crea una lista desplegable para la selección del origen de las acciones
    function dame_control_lista_origenes_acciones($id_controles, $origen_seleccionado)
    {
        $idiomas = new Idiomas();

        $control_lista_origenes = "";
        $control_lista_origenes .= "<div id='etiqueta_origen_acciones_".$id_controles."'>".$idiomas->_("Origen").": "."</div>";
        $control_lista_origenes .= "<select id='origen_acciones_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_origenes .= dame_lista_origenes_acciones($origen_seleccionado);
        $control_lista_origenes .= "</select>";
        return ($control_lista_origenes);
    }


    //
    // Funciones de controles de acciones
    //


    // Devuelve los controles de una acción
    function dame_controles_accion($clase_actuador, $contenido, $valor, $origen_accion)
    {
        // Se comprueba la clase de actuador para mostrar acciones predefinidas o controles para rellenar el contenido de las acciones
        $controles_accion = "";
        switch ($clase_actuador)
        {
            case CLASE_ACTUADOR_MENSAJE:
            {
                $controles_accion .= dame_controles_mensaje($contenido, $origen_accion);
                break;
            }
            case CLASE_ACTUADOR_INTERRUPTOR:
            case CLASE_ACTUADOR_TELEPOSTE:
            case CLASE_ACTUADOR_LUZ_GRADUAL_4:
            {
                $controles_accion .= dame_botones_opcion_acciones_predefinidas($clase_actuador, $contenido);
                break;
            }
            case CLASE_ACTUADOR_GENERICA:
            {
                $controles_accion .= dame_controles_accion_generica($contenido, $valor);
                break;
            }
        }

        return ($controles_accion);
    }


    // Devuelve los controles para rellenar el contenido de un mensaje
    function dame_controles_mensaje($contenido_accion, $origen_controles_accion)
    {
        $idiomas = new Idiomas();

        // Se recuperan los datos del mensaje (en JSON) y se establecen en los controles
        if (($contenido_accion === NULL) || ($origen_controles_accion == ORIGEN_CONTROLES_ACCION_ENVIO_ACCION))
        {
            $titulo_mensaje = "";
            $contenido_mensaje = "";
        }
        else
        {
            $parametros_mensaje = json_decode_caracteres_especiales($contenido_accion);
            $titulo_mensaje = $parametros_mensaje['titulo'];
            $contenido_mensaje = $parametros_mensaje['contenido'];
        }

        // Identificador de botón de ayuda de comodines de mensajes
        switch ($origen_controles_accion)
        {
            case ORIGEN_CONTROLES_ACCION_ENVIO_ACCION:
            case ORIGEN_CONTROLES_ACCION_PROGRAMACION:
            {
                $id_boton_ayuda_comodines_mensajes = "boton_actuadores_ayuda_comodines_mensajes_texto_acciones_manuales_programaciones";
                break;
            }
            case ORIGEN_CONTROLES_ACCION_REGLA:
            {
                $id_boton_ayuda_comodines_mensajes = "boton_actuadores_ayuda_comodines_mensajes_texto_acciones_reglas";
                break;
            }
        }

        $controles .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Título de mensaje').": "."</span><br/>
					<input type='text' id='titulo_mensaje'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo_mensaje, ENT_QUOTES)."'>
				</div>
			</div>";

        $numero_caracteres_actuales = dame_numero_caracteres($contenido_mensaje);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE;
        $controles .= "
			<div class='row-fluid'>
				<div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Contenido de mensaje').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
					<textarea id='contenido_mensaje'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' rows='5'>".htmlspecialchars($contenido_mensaje, ENT_QUOTES)."</textarea>
                    <span id='".$id_boton_ayuda_comodines_mensajes."' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        return ($controles);
    }


    // Devuelve los controles ('botones de opción') de las acciones predefinidas
    function dame_botones_opcion_acciones_predefinidas($clase_actuador, $contenido_accion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $idiomas = new Idiomas();

        $consulta = "
            SELECT
                id,
                nombre,
                contenido,
                valor
            FROM acciones_predefinidas
            WHERE
                clase = '".$bd_red->_($clase_actuador)."'";
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }

        $acciones = "
            <div class='row-fluid'>
                <div class='span11'><span class='titulo-campo-administracion'>".$idiomas->_("Acciones").": "."</span><br/>";
        while ($fila = $res->dame_siguiente_fila())
        {
            $acciones .= "
                    <div class='span12 opcion-accion-predefinida'>";
            $acciones .= "
                        <div class='span6'>".dame_boton_opcion_accion_predefinida($fila['id'], $fila['nombre'], $fila['contenido'], $contenido_accion)."</div>";
            $acciones .= "
                        <div class='span6'>".NodoActuador::dame_imagen_accion_clase($clase_actuador, $fila['contenido'])."</div>
                    </div>";
        }
        $acciones .=
                "</div>
            </div>";

        return ($acciones);
    }


    // Devuelve el control ('botón de opción') de la acción predefinida
    function dame_boton_opcion_accion_predefinida($id_accion, $nombre_accion, $contenido_accion, $contenido_accion_seleccionada)
    {
        $idiomas = new Idiomas();

        // Nota: Las acción seleccionada se recupera después de la siguiente forma: $("input[name=acciones_predefinidas]:checked").val()
        $boton_opcion = "<input type='radio' class='alineado-texto' name='acciones_predefinidas' value='accion__".$id_accion."'";
        $contenido_accion_sin_espacios = str_replace(" ", "", $contenido_accion);
        $contenido_accion_seleccionada_sin_espacios = str_replace(" ", "", $contenido_accion_seleccionada);
        if ($contenido_accion_sin_espacios == $contenido_accion_seleccionada_sin_espacios)
        {
            $boton_opcion .= " checked";
        }
        $boton_opcion .= "> ".$idiomas->_($nombre_accion).":";

        return ($boton_opcion);
     }


    // Devuelve los controles para rellenar el contenido de una acción genérica
    function dame_controles_accion_generica($contenido_accion, $valor_accion)
    {
        $idiomas = new Idiomas();

        $controles .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Contenido').": "."</span><br/>
					<input type='text' id='contenido_accion'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$contenido_accion."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Valor').": "."</span><br/>
                    <input type='text' id='valor_accion'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$valor_accion."'>
                </div>
            </div>";

        return ($controles);
    }


    //
    // Funciones de acciones predefinidas
    //


    // Devuelve las acciones predefinidas de una clase de actuador
    function dame_acciones_predefinidas($clase_actuador)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $idiomas = new Idiomas();

        $consulta = "
            SELECT
                nombre,
                contenido
            FROM acciones_predefinidas
            WHERE
                clase = '".$bd_red->_($clase_actuador)."'";
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }

        $acciones = array(
            "nombres" => array(),
            "contenidos_sin_espacios" => array()
        );
        while ($fila = $res->dame_siguiente_fila())
        {
            $nombre = $fila["nombre"];
            $contenido_sin_espacios = $valor_accion = str_replace(" ", "", $fila["contenido"]);

            array_push($acciones["nombres"], $idiomas->_($nombre));
            array_push($acciones["contenidos_sin_espacios"], $contenido_sin_espacios);
		}
        return ($acciones);
    }


    // Devuelve el nombre del estado (final) de una acción predefinia
    function dame_nombre_estado_accion_predefinida($clase_actuador, $contenido_accion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $idiomas = new Idiomas();

        $contenido_accion_sin_espacios = str_replace(" ", "", $contenido_accion);
        $consulta = "
            SELECT nombre_estado
            FROM acciones_predefinidas
            WHERE
                (clase = '".$bd_red->_($clase_actuador)."')
                AND (REPLACE(contenido, ' ', '') = '".$bd_red->_($contenido_accion_sin_espacios)."')";
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }

        if ($res->dame_numero_filas() > 0)
        {
            $fila = $res->dame_siguiente_fila();
            $nombre_estado = $idiomas->_($fila["nombre_estado"]);
        }
        else
        {
            $nombre_estado = $idiomas->_("Desconocido");
        }
        return ($nombre_estado);
    }


    //
    // Funciones de descripciones
    //


    // Devuelve la descripción del destino de la acción
    function dame_descripcion_destino_accion($destino_accion)
    {
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $descripcion = "Actuador";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $descripcion = "Grupo";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción de un origen de una acción
    function dame_descripcion_origen_accion($origen_accion)
    {
        switch ($origen_accion)
        {
            case ORIGEN_ACCION_MANUAL:
            {
                $descripcion_origen_accion = "Manual";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION:
            {
                $descripcion_origen_accion = "Última acción de programación";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_GRUPO_ACTUADORES:
            {
                $descripcion_origen_accion = "Última acción de grupo de actuadores";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_REENVIO_ULTIMA_ACCION:
            {
                $descripcion_origen_accion = "Reenvío de última acción";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_REGLA_ACTIVADA:
            {
                $descripcion_origen_accion = "Regla activada";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_REGLA_DESACTIVADA:
            {
                $descripcion_origen_accion = "Regla desactivada";
                break;
            }
            case ORIGEN_ACCION_AUTOMATICO_PROGRAMACION:
            {
                $descripcion_origen_accion = "Programación";
                break;
            }
            default:
            {
                $descripcion_origen_accion = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_origen_accion));
    }


    // Devuelve la descripción de un origen de acciones (para el informe de acciones enviadas)
    function dame_descripcion_origen_acciones($origen_acciones)
    {
        switch ($origen_acciones)
        {
            case ORIGEN_ACCIONES_TODOS:
            {
                $descripcion_origen_acciones = "Todos";
                break;
            }
            case ORIGEN_ACCIONES_MANUAL:
            {
                $descripcion_origen_acciones = "Manual";
                break;
            }
            case ORIGEN_ACCIONES_ULTIMA_ACCION:
            {
                $descripcion_origen_acciones = "Última acción";
                break;
            }
            case ORIGEN_ACCIONES_REGLA:
            {
                $descripcion_origen_acciones = "Regla";
                break;
            }
            case ORIGEN_ACCIONES_PROGRAMACION:
            {
                $descripcion_origen_acciones = "Programación";
                break;
            }
            default:
            {
                $descripcion_origen_acciones = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_origen_acciones));
    }


    // Devuelve la descripción de un estado de ejecución de una acción
    function dame_descripcion_estado_ejecucion_accion($estado_ejecucion_accion)
    {
        switch ($estado_ejecucion_accion)
        {
            case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
            {
                $descripcion_estado_ejecucion = "No conectado";
                break;
            }
            case ESTADO_EJECUCION_ACCION_EN_EJECUCION:
            {
                $descripcion_estado_ejecucion = "En ejecución";
                break;
            }
            case ESTADO_EJECUCION_ACCION_ERROR:
            {
                $descripcion_estado_ejecucion = "Error";
                break;
            }
            case ESTADO_EJECUCION_ACCION_OK:
            {
                $descripcion_estado_ejecucion = "Ok";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_estado_ejecucion));
    }


    //
    // Funciones de contenido de acciones
    //


    // Actualiza el contenido de la acción dependiendo de la fecha de envío de la acción (si es necesario)
    function actualiza_contenido_accion_fecha(&$contenido_accion, $tipo_acciones, $cadena_fecha_hora_accion_base_datos_utc)
	{
        switch ($tipo_acciones)
        {
            case TIPO_ACCIONES_VALORES_INICIAL_FINAL:
            {
                $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                $fecha_hora_accion_utc = convierte_cadena_a_fecha($cadena_fecha_hora_accion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_accion_utc->getTimestamp();
                if ($segundos_transcurridos < 0)
                {
                    $segundos_transcurridos = 0;
                }

                $valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $contenido_accion);
                $contenido_accion = "";
                foreach ($valores_accion as $valor_accion)
                {
                    if ($contenido_accion != "")
                    {
                        $contenido_accion .= SEPARADOR_PARAMETROS_VALORES;
                    }
                    $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                    $valor_inicial = $parametros_valor_accion[0];
                    $tiempo_duracion_valor_inicial = $parametros_valor_accion[1];
                    $valor_final = $parametros_valor_accion[2];

                    $nuevo_tiempo_duracion_valor_inicial = $tiempo_duracion_valor_inicial - $segundos_transcurridos;
                    if ($nuevo_tiempo_duracion_valor_inicial < 0)
                    {
                        $nuevo_tiempo_duracion_valor_inicial = 0;
                    }

                    $contenido_accion .= implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                        $valor_inicial,
                        $nuevo_tiempo_duracion_valor_inicial,
                        $valor_final));
                }

                break;
            }
            case TIPO_ACCIONES_VALORES_FIJOS_GRADUALES:
            {
                $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                $fecha_hora_accion_utc = convierte_cadena_a_fecha($cadena_fecha_hora_accion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $segundos_transcurridos = $fecha_hora_actual_utc->getTimestamp() - $fecha_hora_accion_utc->getTimestamp();
                if ($segundos_transcurridos < 0)
                {
                    $segundos_transcurridos = 0;
                }

                if ($segundos_transcurridos > 0)
                {
                    $valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $contenido_accion);
                    $contenido_accion = "";
                    foreach ($valores_accion as $valor_accion)
                    {
                        if ($contenido_accion != "")
                        {
                            $contenido_accion .= SEPARADOR_PARAMETROS_VALORES;
                        }
                        if ($valor_accion == "")
                        {
                            continue;
                        }
                        $valor_accion = str_replace(" ", "", $valor_accion);
                        $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                        $tipo_valores = $parametros_valor_accion[0];
                        switch ($tipo_valores)
                        {
                            case TIPO_VALORES_FIJOS:
                            {
                                $numero_repeticiones = $parametros_valor_accion[1];
                                if ($numero_repeticiones == -1)
                                {
                                    $contenido_accion .= $valor_accion;
                                }
                                else
                                {
                                    $valor_final = $parametros_valor_accion[count($parametros_valor_accion) - 1];
                                    $contenido_accion .= implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                                        "F",
                                        0,
                                        0,
                                        $valor_final));
                                }
                                break;
                            }
                            case TIPO_VALORES_GRADUALES:
                            {
                                $granularidad = $parametros_valor_accion[1];
                                $numero_repeticiones = $parametros_valor_accion[2];
                                if ($numero_repeticiones == -1)
                                {
                                    $contenido_accion .= $valor_accion;
                                }
                                else
                                {
                                    $valor_final = $parametros_valor_accion[count($parametros_valor_accion) - 1];
                                    $segundos_transicion = 2;
                                    $contenido_accion .= implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                                        "G",
                                        $granularidad,
                                        0,
                                        0,
                                        $segundos_transicion,
                                        $valor_final));
                                }
                                break;
                            }
                        }
                    }
                }

                break;
            }
            default:
            {
                break;
            }
        }
    }


    //
    // Funciones de obtención de información de acciones
    //


    function dame_fila_accion_predefinida($id_accion_predefinida)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_accion_predefinida = "
            SELECT *
            FROM acciones_predefinidas
            WHERE
                id = '".$bd_red->_($id_accion_predefinida)."'";
        $res_accion_predefinida = $bd_red->ejecuta_consulta($consulta_accion_predefinida);
        if (($res_accion_predefinida == false) || ($res_accion_predefinida->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_accion_predefinida."'");
        }
        $fila_accion_predefinida = $res_accion_predefinida->dame_siguiente_fila();
        return ($fila_accion_predefinida);
    }
?>