<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


    //
    // Funciones de listas de eventos
    //


    // Devuelve la lista de orígenes de un evento
    function dame_lista_origenes_evento($origen_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_TODOS)
        {
            $lista .= "<option value='".ORIGEN_EVENTO_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        $origenes_evento = array(
            ORIGEN_EVENTO_SENSOR,
            ORIGEN_EVENTO_GRUPO_SENSORES);
        foreach ($origenes_evento as $origen_evento)
        {
            $lista .= "<option value='".$origen_evento."'";
            if ($origen_evento == $origen_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".Evento::dame_descripcion_origen_evento($origen_evento)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de identificadores de orígenes de un evento
    function dame_lista_ids_origenes_evento(
        $clase_sensor,
        $origen,
        $id_origen_seleccionado,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Nota: Si la clase es TODAS no se recupera ningún origen de evento
        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_NINGUNO)
        {
            $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_TODOS)
        {
            $lista .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if ($clase_sensor != CLASE_TODAS)
        {
            switch ($origen)
            {
                case ORIGEN_EVENTO_TODOS:
                {
                    $consulta_origenes = NULL;
                    break;
                }
                case ORIGEN_EVENTO_SENSOR:
                {
                    $consulta_origenes = "
                        SELECT
                            id,
                            nombre
                        FROM sensores
                        WHERE
                            (clase = '".$bd_red->_($clase_sensor)."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                    if ($mostrar_todos_sensores == false)
                    {
                        $consulta_origenes .= "
                            AND ".dame_condicion_consulta_sensores_usuario_actual(true);
                    }
                    $consulta_origenes .= "
                        ORDER BY nombre ASC";
                    break;
                }
                case ORIGEN_EVENTO_GRUPO_SENSORES:
                {
                    $consulta_origenes = "
                        SELECT
                            id,
                            nombre
                        FROM grupos_sensores
                        WHERE
                            (clase = '".$bd_red->_($clase_sensor)."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                    if ($mostrar_todos_sensores == false)
                    {
                        $consulta_origenes .=
                            "AND ".dame_condicion_consulta_grupos_sensores_usuario_actual(false);
                    }
                    $consulta_origenes .= "
                        ORDER BY nombre ASC";
                    break;
                }
            }
            if ($consulta_origenes !== NULL)
            {
                $res_origenes = $bd_red->ejecuta_consulta($consulta_origenes);
                if ($res_origenes == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_origenes."'");
                }
                while ($fila_origen = $res_origenes->dame_siguiente_fila())
                {
                    $lista .= "<option value='".$fila_origen['id']."'";
                    if ($fila_origen['id'] == $id_origen_seleccionado)
                    {
                        $lista .= " selected";
                    }
                    $lista .= ">".$fila_origen['nombre']."</option>";
                }
            }
        }

        return ($lista);
    }


    // Devuelve la lista de granularidades de un evento
    function dame_lista_granularidades_evento(
        $clase_sensor,
        $granularidad_seleccionada,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS)
        {
            $lista .= "<option value='".GRANULARIDAD_TODAS."'>".$idiomas->_("Todas")."</option>";
        }
        $granularidades_evento = Evento::dame_granularidades_evento_clase_sensor($clase_sensor);
        foreach ($granularidades_evento as $granularidad_evento)
        {
            $lista .= "<option value='".$granularidad_evento."'";
            if ($granularidad_evento == $granularidad_seleccionada)
            {
                $lista .= " selected";
            }
            $lista .= ">".dame_descripcion_granularidad($granularidad_evento)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de un evento
    function dame_lista_tipos_evento(
        $clase_sensor,
        $origen,
        $granularidad,
        $tipo_seleccionado)
    {
        $idiomas = new Idiomas();

        $tipos_evento = Evento::dame_tipos_evento_clase_sensor_origen_granularidad($clase_sensor, $origen, $granularidad);
        $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        foreach ($tipos_evento as $tipo_evento)
        {
            $lista .= "<option value='".$tipo_evento."'";
			if ($tipo_evento == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".Evento::dame_descripcion_tipo_evento($tipo_evento)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de campos de un evento
    function dame_lista_campos_evento(
        $clase_sensor,
        $granularidad,
        $tipo_evento,
        $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista = "";
        switch ($granularidad)
        {
            case GRANULARIDAD_TIEMPO_REAL:
            {
                $campos = dame_campos_clase_sensor($clase_sensor);
                $campos_puntuales = dame_campos_puntuales_clase_sensor($clase_sensor);
                $campos_incrementos = dame_campos_incrementos_clase_sensor($clase_sensor);
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            case GRANULARIDAD_HORARIA:
            {
                switch ($tipo_evento)
                {
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                    {
                        $campos = dame_campos_incrementos_horarios_clase_sensor($clase_sensor);
                        break;
                    }
                    default:
                    {
                        $campos = dame_campos_horarios_clase_sensor($clase_sensor);
                        break;
                    }
                }
                break;
            }
        }

        // Si la granularidad es tiempo real y hay campos puntuales e incrementos, la lista de campos va por 'parejas' (valor / incremento valor)
        // Nota: Si hay campos puntuales e incrementos, debe haber el mismo número de campos puntuales e incrementos
        if (($granularidad == GRANULARIDAD_TIEMPO_REAL) && (count($campos_puntuales) > 0) && (count($campos_incrementos) > 0))
        {
            if (count($campos_puntuales) > 1)
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
            }
            for ($i = 0; $i < count($campos_puntuales); $i++)
            {
                $campo_puntual = $campos_puntuales[$i];
                $campo_incremento = $campos_incrementos[$i];
                $descripcion_campo_puntual = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_puntual);
                $descripcion_campo_incremento = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_incremento);
                $descripcion_campos_puntual_incremento = $descripcion_campo_puntual." / ".$descripcion_campo_incremento;
                $campos_puntual_incremento = $campo_puntual."-".$campo_incremento;
                $lista .= dame_opcion_valor_lista_simple($descripcion_campos_puntual_incremento, $campos_puntual_incremento, $campo_seleccionado);
            }
        }
        else
        {
            if (count($campos) > 1)
            {
                switch ($tipo_evento)
                {
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
                    case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO:
                    case TIPO_EVENTO_LINEA_BASE:
                    case TIPO_EVENTO_PERFIL_HORARIO:
                    {
                        break;
                    }
                    default:
                    {
                        $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        break;
                    }
                }
            }
            foreach ($campos as $campo)
            {
                $lista .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
            }
        }

        return ($lista);
    }


    // Devuelve la lista de campos de un evento para el informe de activaciones de eventos
    function dame_lista_campos_sensor_activaciones_eventos(
        $clase_sensor,
        $id_sensor,
        $granularidad,
        $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista = "";
        $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), CAMPO_NINGUNO, $campo_seleccionado);
        if ($clase_sensor != CLASE_TODAS)
        {
            if (($id_sensor != ID_NINGUNO) && ($id_sensor != ID_TODOS))
            {
                switch ($granularidad)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $campos = dame_campos_clase_sensor($clase_sensor);
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $campos = dame_campos_horarios_clase_sensor($clase_sensor);
                        break;
                    }
                    case GRANULARIDAD_TODAS:
                    {
                        // Nota: Si la granularidad es todas, no se puede seleccionar ningún campo de sensor ya que
                        // puede haber eventos con diferentes granularidades y no se sabría a que valores corresponderían
                        $campos = array();
                        break;
                    }
                    default:
                    {
                        throw new Exception("Granularidad desconocida o incorrecta: '".$granularidad."'");
                    }
                }
                foreach ($campos as $campo)
                {
                    $lista .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
                }
            }
        }

        return ($lista);
    }


    // Devuelve la lista de periodos de tiempo de un evento
    function dame_lista_periodos_tiempo_evento($periodo_tiempo_seleccionado)
    {
        $periodos_tiempo_evento = array(
            PERIODO_TIEMPO_EVENTO_HORA,
            PERIODO_TIEMPO_EVENTO_DIA,
            PERIODO_TIEMPO_EVENTO_SEMANA,
            PERIODO_TIEMPO_EVENTO_MES);

        $lista = "";
        foreach ($periodos_tiempo_evento as $periodo_tiempo_evento)
        {
            $lista .= "<option value='".$periodo_tiempo_evento."'";
            if ($periodo_tiempo_evento == $periodo_tiempo_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".Evento::dame_descripcion_periodo_tiempo_evento($periodo_tiempo_evento)."</option>";
        }

        return ($lista);
    }


    // Devuelve los controles para el filtrado de eventos en el informe de activaciones de eventos
    function dame_controles_filtro_eventos_activaciones_eventos($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_clases_sensor = dame_control_lista_clases_sensor(
            $id_controles,
            OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS,
            true,
            true,
            $idiomas->_("Clase de sensor"));
        $control_lista_origenes_evento = dame_control_lista_origenes_evento(
            $id_controles,
            ORIGEN_EVENTO_TODOS,
            OPCIONES_EXTRA_LISTA_ORIGENES_EVENTO_TODOS);
        $control_lista_ids_origenes_evento = dame_control_lista_ids_origenes_evento(
            $id_controles,
            CLASE_TODAS,
            ORIGEN_EVENTO_TODOS,
            ID_NINGUNO,
            OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_TODOS);
        $control_lista_granularidades_evento = dame_control_lista_granularidades_evento(
            $id_controles,
            CLASE_TODAS,
            GRANULARIDAD_TODAS,
            OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS);

        $controles_listas = array(
            $control_lista_clases_sensor,
            $control_lista_origenes_evento,
            $control_lista_ids_origenes_evento,
            $control_lista_granularidades_evento
        );
        return ($controles_listas);
    }


    // Crea una lista desplegable para la selección del origen de un evento
    function dame_control_lista_origenes_evento(
        $id_controles,
        $origen_seleccionado,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_origenes = "";
        $control_lista_origenes .= "<div id='etiqueta_origen_evento_".$id_controles."'>".$idiomas->_("Tipo de origen").": "."</div>";
        $control_lista_origenes .= "<select id='origen_evento_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_origenes .= dame_lista_origenes_evento($origen_seleccionado, $opciones_extra);
        $control_lista_origenes .= "</select>";
        return ($control_lista_origenes);
    }


    // Crea una lista desplegable para la selección del identificador del origen de un evento
    function dame_control_lista_ids_origenes_evento(
        $id_controles,
        $clase_sensor,
        $origen,
        $id_origen_seleccionado,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_ids_origenes = "";
        $control_lista_ids_origenes .= "<div id='etiqueta_id_origen_evento_".$id_controles."'>".$idiomas->_("Origen").": "."</div>";
        $control_lista_ids_origenes .= "<select id='id_origen_evento_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_ids_origenes .= dame_lista_ids_origenes_evento(
            $clase_sensor,
            $origen,
            $id_origen_seleccionado,
            $opciones_extra);
        $control_lista_ids_origenes .= "</select>";
        return ($control_lista_ids_origenes);
    }


    // Crea una lista desplegable para la selección de la granularidad de un evento
    function dame_control_lista_granularidades_evento(
        $id_controles,
        $clase_sensor,
        $granularidad_seleccionada,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_granularidades = "";
        $control_lista_granularidades .= "<div id='etiqueta_granularidad_evento_".$id_controles."'>".$idiomas->_("Granularidad").": "."</div>";
        $control_lista_granularidades .= "<select id='granularidad_evento_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_granularidades .= dame_lista_granularidades_evento(
            $clase_sensor,
            $granularidad_seleccionada,
            $opciones_extra);
        $control_lista_granularidades .= "</select>";
        return ($control_lista_granularidades);
    }


    // Crea una lista desplegable para la selección del campo de un evento para el informe de activaciones de eventos
    function dame_control_lista_campos_sensor_activaciones_eventos(
        $id_controles,
        $clase_sensor,
        $id_sensor,
        $granularidad,
        $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $control_lista_campos = "";
        $control_lista_campos .= "<div id='etiqueta_campo_".$id_controles."'>".$idiomas->_("Campo").": "."</div>";
        $control_lista_campos .= "<select id='campo_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_campos .= dame_lista_campos_sensor_activaciones_eventos($clase_sensor, $id_sensor, $granularidad, $campo_seleccionado);
        $control_lista_campos .= "</select>";
        return ($control_lista_campos);
    }


    // Crea una lista doble de eventos
    function dame_control_lista_doble_eventos(
        $id_controles,
        $clase_sensor,
        $origen,
        $id_origen,
        $granularidad,
        $max_eventos)
    {
        $idiomas = new Idiomas();

        // Nota: En las listas dobles es necesario el atributo 'name'
        $control_lista_doble_eventos = "<span>".$idiomas->_("Eventos").": "."</span><br/>";
        $control_lista_doble_eventos .= "<div id='select_eventos_no_visible_".$id_controles."' hidden></div>";
        $control_lista_doble_eventos .= "
            <select id='ids_eventos_".$id_controles."'
                name='ids_eventos_".$id_controles."'
                max_selected='".$max_eventos."' multiple='multiple'
                class='select100' hidden>";
        $control_lista_doble_eventos .= dame_lista_eventos(
            $clase_sensor,
            $origen,
            $id_origen,
            $granularidad,
            array());
        $control_lista_doble_eventos .= "
            </select>";

        return ($control_lista_doble_eventos);
    }


    // Devuelve la lista de eventos
    function dame_lista_eventos(
        $clase_sensor,
        $origen,
        $id_origen,
        $granularidad,
        $ids_eventos)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $lista = "";
        if ($clase_sensor == CLASE_NINGUNA)
        {
            return ($lista);
        }
        $consulta_eventos = "
            SELECT
                id,
                nombre,
                origen,
                granularidad
            FROM eventos
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_eventos .= "
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        }
        // Si el origen es sensor y hay sensor seleccionado, también se buscan los eventos del grupo del sensor (si tiene asignado)
        switch ($origen)
        {
            case ORIGEN_EVENTO_TODOS:
            {
                break;
            }
            case ORIGEN_EVENTO_SENSOR:
            {
                if ($id_origen != ID_TODOS)
                {
                    $fila_sensor = dame_fila_sensor($id_origen);
                    $id_grupo_sensor = $fila_sensor["grupo"];
                    if ($id_grupo_sensor != ID_NINGUNO)
                    {
                        $consulta_eventos .= "
                            AND (((origen = '".$bd_red->_($origen)."') AND (id_origen = '".$bd_red->_($id_origen)."')) OR
                                ((origen = '".$bd_red->_(ORIGEN_EVENTO_GRUPO_SENSORES)."') AND (id_origen = '".$bd_red->_($id_grupo_sensor)."')))";
                    }
                    else
                    {
                        $consulta_eventos .= "
                            AND (id_origen = '".$bd_red->_($id_origen)."')";
                        if ($id_origen != ID_TODOS)
                        {
                            $consulta_eventos .= "
                                AND (id_origen = '".$bd_red->_($id_origen)."')";
                        }
                    }
                }
                else
                {
                    $consulta_eventos .= "
                        AND (origen = '".$bd_red->_($origen)."')";
                }
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $consulta_eventos .= "
                    AND (origen = '".$bd_red->_($origen)."')";
                if ($id_origen != ID_TODOS)
                {
                    $consulta_eventos .= "
                        AND (id_origen = '".$bd_red->_($id_origen)."')";
                }
                break;
            }
            default:
            {
                throw new Exception("Origen de evento desconocido: '".$origen."'");
            }
        }
        if ($granularidad != GRANULARIDAD_TODAS)
        {
            $consulta_eventos .= "
                AND (granularidad = '".$bd_red->_($granularidad)."')";
        }
        $consulta_eventos .= "
            ORDER BY nombre ASC";
        $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
        if ($res_eventos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_eventos."'");
        }

        // Identificadores de eventos del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_eventos_usuario = Evento::dame_ids_eventos_usuario_actual();
        }

        // Se añaden los eventos
        while ($fila_evento = $res_eventos->dame_siguiente_fila())
        {
            $anyadir_evento = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_evento["id"], $ids_eventos_usuario) == false)
                {
                    $anyadir_evento = false;
                }
            }

            if ($anyadir_evento == true)
            {
                $texto_evento = $fila_evento["nombre"];
                if ($origen == ORIGEN_EVENTO_TODOS)
                {
                    switch ($fila_evento["origen"])
                    {
                        case ORIGEN_EVENTO_GRUPO_SENSORES:
                        {
                            $texto_evento .= " (".$idiomas->_("grupo").")";
                            break;
                        }
                    }
                }
                if ($granularidad == GRANULARIDAD_TODAS)
                {
                    switch ($fila_evento["granularidad"])
                    {
                        case GRANULARIDAD_CUARTOHORARIA:
                        {
                            $texto_evento .= " (".$idiomas->_("cuartohoraria").")";
                            break;
                        }
                        case GRANULARIDAD_HORARIA:
                        {
                            $texto_evento .= " (".$idiomas->_("horaria").")";
                            break;
                        }
                    }
                }

                $lista .= "<option value='".$fila_evento["id"]."'";
                if (in_array($fila_evento["id"], $ids_eventos) == true)
                {
                    $lista .= " selected";
                }
                $lista .= ">".$texto_evento."</option>";
            }
        }

        return ($lista);
    }


    //
    // Funciones de controles para el filtrado de eventos
    //


    // Crea una lista desplegable para la selección de la alarma de un evento
    function dame_control_lista_alarmas_evento($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_alarmas .= "<div id='etiqueta_alarma_evento_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_alarmas .= "<select id='alarma_evento_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_alarmas .= "<option value=".ALARMA_EVENTO_TODOS.">".$idiomas->_("Todos")."</option>";
        $control_lista_alarmas .= "<option value=".ALARMA_EVENTO_SI.">".$idiomas->_("Sí")."</option>";
        $control_lista_alarmas .= "<option value=".ALARMA_EVENTO_NO.">".$idiomas->_("No")."</option>";
        $control_lista_alarmas .= "</select>";
        return ($control_lista_alarmas);
    }


    // Crea una lista desplegable para la selección de la activación de un evento
    function dame_control_lista_activaciones_evento($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_activaciones .= "<div id='etiqueta_activacion_evento_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_activaciones .= "<select id='activacion_evento_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_EVENTO_TODOS.">".$idiomas->_("Todos")."</option>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_EVENTO_ACTIVADO.">".$idiomas->_("Sí")."</option>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_EVENTO_DESACTIVADO.">".$idiomas->_("No")."</option>";
        $control_lista_activaciones .= "</select>";
        return ($control_lista_activaciones);
    }


    //
    // Funciones de obtención de información de eventos
    //


    function dame_fila_evento($id_evento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_evento = "
            SELECT *
            FROM eventos
            WHERE
                id = '".$bd_red->_($id_evento)."'";
        $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
        if (($res_evento == false) || ($res_evento->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_evento."'");
        }
        $fila_evento = $res_evento->dame_siguiente_fila();
        return ($fila_evento);
    }


    function dame_nombre_evento($id_evento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_evento = "
            SELECT nombre
            FROM eventos
            WHERE
                id = '".$bd_red->_($id_evento)."'";
        $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
        if (($res_evento == false) || ($res_evento->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_evento."'");
        }
        $fila_evento = $res_evento->dame_siguiente_fila();
        $nombre_evento = $fila_evento["nombre"];
        return ($nombre_evento);
    }


    //
    // Funciones auxiliares
    //


    // Recarga de configuraciones de los sensores del origen de un evento
    function recarga_configuraciones_sensores_origen_evento($origen, $id_origen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan las filas de los sensores
        $filas_sensores = array();
        switch ($origen)
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                $fila_sensor = dame_fila_sensor($id_origen);
                array_push($filas_sensores, $fila_sensor);
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $consulta_sensores = "
                    SELECT *
                    FROM sensores
                    WHERE
                        grupo = '".$bd_red->_($id_origen)."'
                    ORDER BY id ASC";
                $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                if ($res_sensores == false)
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
                }
                while ($fila_sensor = $res_sensores->dame_siguiente_fila())
                {
                    array_push($filas_sensores, $fila_sensor);
                }
                break;
            }
        }

        // Se conecta al servidor MQTT y se recarga la configuración de cada sensor
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        foreach ($filas_sensores as $fila_sensor)
        {
            $id_sensor = $fila_sensor["id"];
            $tipo_sensor = $fila_sensor["tipo"];
            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_REAL:
                {
                    $id_dispositivo = dame_dispositivo_sensor_real($fila_sensor);
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("MNG/DEV/".$id_dispositivo."/RELOAD", "", 0);
                    }
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("VIRTUAL_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
                    }
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("PROCESS_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
                    }
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    if ($mqtt->conecta() == true)
                    {
                        $mqtt->publica("EXTERNAL_SENS/SENS/".$id_sensor."/RELOAD", "", 1);
                    }
                    break;
                }
            }
        }
        $mqtt->desconecta();
    }
?>