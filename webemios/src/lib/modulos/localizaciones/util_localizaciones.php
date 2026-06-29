<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');


    //
    // Funciones de consultas de localizaciones
    //


    // Devuelve la condición de consulta de localizaciones del usuario actual
    function dame_condicion_consulta_localizaciones_usuario_actual()
    {
        if (!isset($GLOBALS['condicion_consulta_localizaciones_usuario_actual']))
        {
            $condicion_consulta_localizaciones = "";
            $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(true);
            $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones_usuario);
            $condicion_consulta_localizaciones .= "
                (localizaciones.id IN (".$cadena_ids_localizaciones_consulta."))";
            $GLOBALS['condicion_consulta_localizaciones_usuario_actual'] = $condicion_consulta_localizaciones;
        }
        else
        {
            $condicion_consulta_localizaciones = $GLOBALS['condicion_consulta_localizaciones_usuario_actual'];
        }
        return ($condicion_consulta_localizaciones);
    }


    //
    // Funciones de identificadores de localizaciones
    //


    // Devuelve los identificadores de las localizaciones (de la red actual)
    function dame_ids_localizaciones()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_localizaciones = "
            SELECT id
            FROM localizaciones
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }
        $ids_localizaciones = array();
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            array_push($ids_localizaciones, $fila_localizacion["id"]);
        }
        return ($ids_localizaciones);
    }


    // Devuelve los identificadores de las localizaciones visibles para el usuario actual (vacío si puede ver todas las localizaciones)
    function dame_ids_localizaciones_usuario_actual($incluir_localizaciones_descendientes)
    {
        // Ids de localizaciones de los parámetros de los módulos
        $ids_localizaciones = $_SESSION["parametros_modulo_localizaciones"]["ids_localizaciones"];
        if ($ids_localizaciones === NULL)
        {
            $ids_localizaciones = array();
        }

        // Identificadores de localizaciones
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == true)
        {
            $ids_todas_localizaciones = dame_ids_localizaciones();
            return ($ids_todas_localizaciones);
        }

        // Se actualizan los identificadores de localizaciones con las localizaciones descendientes de las localizaciones del usuario
        if ($incluir_localizaciones_descendientes == true)
        {
            // Se recuperan las localizaciones padres e hijas{
            $info_localizaciones_padres = NULL;
            $info_localizaciones_hijas = NULL;
            carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

            // Se recuperan las localizaciones descendientes de las localizaciones asignadas al usuario
            $ids_localizaciones_usuario_actual = dame_ids_localizaciones_descendientes_localizaciones($info_localizaciones_hijas, $ids_localizaciones, true);
        }
        else
        {
            $ids_localizaciones_usuario_actual = $ids_localizaciones;
        }
        return ($ids_localizaciones_usuario_actual);
    }


    // Devuelve los identificadores de las localizaciones ascendientes de las localizaciones especificadas
    function dame_ids_localizaciones_ascendientes($ids_localizaciones)
    {
        // Se recuperan las localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Se recuperan las localizaciones ascendientes de las localizaciones especificadas
        $ids_localizaciones_ascendientes = dame_ids_localizaciones_ascendientes_localizaciones($info_localizaciones_padres, $ids_localizaciones, false);
        return ($ids_localizaciones_ascendientes);
    }


    // Devuelve los identificadores de las localizaciones descendientes de las localizaciones especificadas
    function dame_ids_localizaciones_descendientes($ids_localizaciones)
    {
        // Se recuperan las localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Se recuperan las localizaciones descendientes de las localizaciones especificadas
        $ids_localizaciones_descendientes = dame_ids_localizaciones_descendientes_localizaciones($info_localizaciones_hijas, $ids_localizaciones, false);
        return ($ids_localizaciones_descendientes);
    }


    // Devuelve los identificadores y los grados de las localizaciones ascendientes de la localización especificada
    function dame_ids_grados_localizaciones_ascendientes($id_localizacion)
    {
        // Se recuperan las localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Se recuperan las localizaciones ascendientes de las localizaciones especificada
        $ids_grados_localizaciones = dame_ids_localizaciones_grados_ascendientes_localizaciones($info_localizaciones_padres, array($id_localizacion), false);
        return ($ids_grados_localizaciones);
    }


    // Devuelve los identificadores y los grados de las localizaciones descendientes de la localización especificada
    function dame_ids_grados_localizaciones_descendientes($id_localizacion)
    {
        // Se recuperan las localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Se recuperan las localizaciones descendientes de las localizaciones especificada
        $ids_grados_localizaciones = dame_ids_localizaciones_grados_descendientes_localizaciones($info_localizaciones_hijas, array($id_localizacion), false);
        return ($ids_grados_localizaciones);
    }


    //
    // Funciones de permisos de localizaciones
    //


    // Devuelve si se muestran todas las localizaciones
    function dame_mostrar_todas_localizaciones()
    {
        $mostrar_todas_localizaciones = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_localizaciones"]["permiso_todas_localizaciones"] == VALOR_SI);
        return ($mostrar_todas_localizaciones);
    }


    //
    // Funciones de listas de localizaciones, instalaciones y ratios
    //


    // Crea una lista desplegable para la selección de la localización actual
    function dame_control_lista_seleccion_localizacion_actual(
        $id_controles,
        $nombre_lista,
        $id_localizacion,
        $opciones_extra,
        $modo_seleccion_localizacion_actual)
    {
        $control_lista_seleccion_localizacion_actual .= "<div id='etiqueta_localizacion_".$id_controles."'>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</div>";
        $control_lista_seleccion_localizacion_actual .= "
            <select id='id_localizacion_".$id_controles."'";
        switch ($modo_seleccion_localizacion_actual)
        {
            case MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA:
            {
                $control_lista_seleccion_localizacion_actual .= "
                    class='chosen-select' hidden>";
                break;
            }
            case MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE:
            {
                $control_lista_seleccion_localizacion_actual .= "
                    class='filtro-desplegable'>";
                break;
            }
        }
        $control_lista_seleccion_localizacion_actual .= dame_lista_seleccion_localizacion_actual($id_localizacion, $opciones_extra, $modo_seleccion_localizacion_actual);
        $control_lista_seleccion_localizacion_actual .= "
            </select>";
        return ($control_lista_seleccion_localizacion_actual);
    }


    // Crea una lista desplegable para la selección de una localización
    function dame_control_lista_localizaciones(
        $id_controles,
        $nombre_lista,
        $id_localizacion,
        $opciones_extra)
    {
        $control_lista_localizaciones .= "<div id='etiqueta_localizacion_".$id_controles."'>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</div>";
        $control_lista_localizaciones .= "
            <select id='id_localizacion_".$id_controles."'
                class='chosen-select' hidden>";
        $control_lista_localizaciones .= dame_lista_localizaciones(array($id_localizacion), $opciones_extra);
        $control_lista_localizaciones .= "
            </select>";
        return ($control_lista_localizaciones);
    }


    // Crea una lista doble de localizaciones para selección múltiple de localizaciones
    function dame_control_lista_doble_localizaciones(
        $id_controles,
        $nombre_lista,
        $ids_localizaciones,
        $max_localizaciones)
    {
        // Nota: En las listas dobles es necesario el atributo 'name'
        $control_lista_doble_localizaciones = "<div id='control_lista_doble_localizaciones_".$id_controles."'>";
        $control_lista_doble_localizaciones .= "<span>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</span><br/>";
        $control_lista_doble_localizaciones .= "<div id='select_localizaciones_no_visible_".$id_controles."' hidden></div>";
        $control_lista_doble_localizaciones .= "
            <select id='ids_localizaciones_".$id_controles."'
                name='ids_localizaciones_".$id_controles."'
                max_selected='".$max_localizaciones."' multiple='multiple'
                class='select100' hidden>";
        $control_lista_doble_localizaciones .= dame_lista_localizaciones($ids_localizaciones, OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA);
        $control_lista_doble_localizaciones .= "
            </select>";
        $control_lista_doble_localizaciones .= "</div>";
        return ($control_lista_doble_localizaciones);
    }


    // Devuelve la lista de selección de localización actual
    function dame_lista_seleccion_localizacion_actual($id_localizacion_seleccionada, $opciones_extra, $modo_seleccion_localizacion_actual)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $ids_extra = array();
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_NINGUNA:
            {
                array_push($ids_extra, ID_NINGUNO);
                break;
            }
            case OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_TODAS:
            {
                array_push($ids_extra, ID_TODOS);
                break;
            }
            case OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_NINGUNA_TODAS:
            {
                array_push($ids_extra, ID_DESACTIVADO);
                array_push($ids_extra, ID_NINGUNO);
                array_push($ids_extra, ID_TODOS);
                break;
            }
            case OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_TODAS:
            {
                array_push($ids_extra, ID_DESACTIVADO);
                array_push($ids_extra, ID_TODOS);
                break;
            }
        }
        if ($modo_seleccion_localizacion_actual == MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE)
        {
            array_push($ids_extra, ID_LOCALIZACIONES_SELECCIONADAS_AND);
            array_push($ids_extra, ID_LOCALIZACIONES_SELECCIONADAS_OR);
        }
        $lista_seleccion_localizacion_actual = "";
        foreach ($ids_extra as $id_extra)
        {
            $lista_seleccion_localizacion_actual .= "<option value='".$id_extra."'";
			if ($id_extra == $id_localizacion_seleccionada)
			{
				$lista_seleccion_localizacion_actual .= " selected";
			}
			$lista_seleccion_localizacion_actual .= ">".dame_descripcion_id_extra_lista_seleccion_localizacion_actual($id_extra)."</option>";
        }

        // Si el modo de selección es única, se añaden las localizaciones a la lista,
        // (si no hay una lista doble para seleccionar las localizaciones)
        if ($modo_seleccion_localizacion_actual == MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA)
        {
            $consulta_localizaciones = "
                SELECT
                    id,
                    nombre
                FROM localizaciones
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
            if ($mostrar_todas_localizaciones == false)
            {
                $cadena_ids_localizaciones_seleccionadas = implode(",", $id_localizacion_seleccionada);
                $consulta_localizaciones .= "
                    AND (".dame_condicion_consulta_localizaciones_usuario_actual();
                if ($cadena_ids_localizaciones_seleccionadas != "")
                {
                    $consulta_localizaciones .= " OR (localizaciones.id = '".$id_localizacion_seleccionada."')";
                }
                $consulta_localizaciones .= ")";
            }
            $consulta_localizaciones .= "
                ORDER BY nombre ASC";
            $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
            if ($res_localizaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
            }

            while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
            {
                $lista_seleccion_localizacion_actual .= "<option value='".$fila_localizacion['id']."'";
                if ($fila_localizacion['id'] == $id_localizacion_seleccionada)
                {
                    $lista_seleccion_localizacion_actual .= " selected";
                }
                $lista_seleccion_localizacion_actual .= ">".htmlspecialchars($fila_localizacion['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_seleccion_localizacion_actual);
    }


    // Devuelve la descripción de identificador 'extra' de la lista de selección de localización actual
    function dame_descripcion_id_extra_lista_seleccion_localizacion_actual($id_extra)
    {
        switch ($id_extra)
        {
            case ID_DESACTIVADO:
            {
                $descripcion = "Sin localizaciones";
                break;
            }
            case ID_NINGUNO:
            {
                $descripcion = "Ninguna";
                break;
            }
            case ID_TODOS:
            {
                $descripcion = "Todas";
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            {
                $descripcion = "Todas las seleccionadas";
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $descripcion = "Cualquiera de las seleccionadas";
                break;
            }
            default:
            {
                $descripcion = "Desconocida";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la lista de localizaciones
    function dame_lista_localizaciones($ids_localizaciones_seleccionadas, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_localizaciones = "
            SELECT
                id,
                nombre
            FROM localizaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == false)
        {
            $consulta_localizaciones .= "
                AND (".dame_condicion_consulta_localizaciones_usuario_actual();
            if (count($ids_localizaciones_seleccionadas) > 0)
            {
                $cadena_ids_localizaciones_seleccionadas_consulta = dame_cadena_ids_consulta($ids_localizaciones_seleccionadas);
                $consulta_localizaciones .= " OR (localizaciones.id IN (".$cadena_ids_localizaciones_seleccionadas_consulta."))";
            }
            $consulta_localizaciones .= ")";
        }
        $consulta_localizaciones .= "
            ORDER BY nombre ASC";
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }

        $lista_localizaciones = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA:
            {
                $lista_localizaciones .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_LOCALIZACIONES_TODAS:
            {
                $lista_localizaciones .= "<option value='".ID_TODOS."'>".$idiomas->_("Todas")."</option>";
                break;
            }
        }
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            $lista_localizaciones .= "<option value='".$fila_localizacion['id']."'";
            if (in_array($fila_localizacion['id'], $ids_localizaciones_seleccionadas) == true)
            {
                $lista_localizaciones .= " selected";
            }
            $lista_localizaciones .= ">".htmlspecialchars($fila_localizacion['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_localizaciones);
    }


    // Crea una lista desplegable para la selección de una instalación de una localización
    function dame_control_lista_instalaciones_localizacion(
        $id_controles,
        $nombre_lista,
        $id_localizacion,
        $id_instalacion,
        $opciones_extra)
    {
        $control_lista_instalaciones .= "<div id='etiqueta_instalacion_".$id_controles."'>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</div>";
        $control_lista_instalaciones .= "
            <select id='id_instalacion_".$id_controles."'
                class='chosen-select' hidden>";
        $control_lista_instalaciones .= dame_lista_instalaciones_localizacion($id_localizacion, $id_instalacion, $opciones_extra);
        $control_lista_instalaciones .= "
            </select>";
        return ($control_lista_instalaciones);
    }


    // Devuelve la lista de instalaciones de una localización
    function dame_lista_instalaciones_localizacion($id_localizacion, $id_instalacion_seleccionada, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_instalaciones = "
            SELECT
                id,
                nombre,
                imagen
            FROM instalaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($id_localizacion != ID_TODOS)
        {
            $consulta_instalaciones .= "
                AND (localizacion = '".$bd_red->_($id_localizacion)."')";
        }
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == false)
        {
            $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
            $cadena_ids_localizaciones_usuario_actual = dame_cadena_ids_consulta($ids_localizaciones_usuario_actual);
            $consulta_instalaciones .= "
                AND (localizacion IN (".$cadena_ids_localizaciones_usuario_actual."))";
        }
        $consulta_instalaciones .= "
            ORDER BY nombre ASC";
        $res_instalaciones = $bd_red->ejecuta_consulta($consulta_instalaciones);
        if ($res_instalaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_instalaciones."'");
        }

        $lista_instalaciones = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_INSTALACIONES_NINGUNA:
            case OPCIONES_EXTRA_LISTA_INSTALACIONES_CON_IMAGEN_NINGUNA:
            {
                $lista_instalaciones .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
                break;
            }
        }
        while ($fila_instalacion = $res_instalaciones->dame_siguiente_fila())
        {
            $id_instalacion = $fila_instalacion['id'];
            $nombre_instalacion = $fila_instalacion['nombre'];
            $imagen_instalacion = $fila_instalacion['imagen'];

            // Se comprueba si hay que añadir la instalación según las opciones extra
            $anyadir_instalacion = true;
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_INSTALACIONES_CON_IMAGEN_NINGUNA:
                {
                    if ($imagen_instalacion == VALOR_NO)
                    {
                        $anyadir_instalacion = false;
                    }
                    break;
                }
            }

            // Se añade la instalación a la lista
            if ($anyadir_instalacion == true)
            {
                $lista_instalaciones .= "<option value='".$id_instalacion."'";
                if ($id_instalacion == $id_instalacion_seleccionada)
                {
                    $lista_instalaciones .= " selected";
                }
                $lista_instalaciones .= ">".htmlspecialchars($nombre_instalacion, ENT_QUOTES)."</option>";
            }
        }
        return ($lista_instalaciones);
    }


    // Crea una lista desplegable para la selección de un ratio
    function dame_control_lista_ratios(
        $id_controles,
        $nombre_lista,
        $visible,
        $id_ratio_seleccionado)
    {
        $control_lista_ratios = "
            <div id='control_id_ratio_".$id_controles."'";
        if ($visible == false)
        {
            $control_lista_ratios .= " hidden";
        }
        $control_lista_ratios .= ">";
        $control_lista_ratios .= "
                <div id='etiqueta_ratio_".$id_controles."'>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</div>";
        $control_lista_ratios .= "
                <select id='id_ratio_".$id_controles."'";
        $control_lista_ratios .= "
                    class='chosen-select' hidden>";
        $control_lista_ratios .= dame_lista_ratios($id_ratio_seleccionado);
        $control_lista_ratios .= "
                </select>";
        $control_lista_ratios .= "
            </div>";
        return ($control_lista_ratios);
    }


    // Devuelve la lista de ratios
    function dame_lista_ratios($id_ratio_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ratios = "
            SELECT
                id,
                nombre
            FROM ratios
            WHERE
                (red = '".$_SESSION["id_red"]."')
            ORDER BY nombre ASC";
        $res_ratios = $bd_red->ejecuta_consulta($consulta_ratios);
        if ($res_ratios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ratios."'");
        }

        $lista_ratios = "";
        $lista_ratios .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_ratio = $res_ratios->dame_siguiente_fila())
        {
            $lista_ratios .= "<option value='".$fila_ratio['id']."'";
            if ($fila_ratio['id'] == $id_ratio_seleccionado)
            {
                $lista_ratios .= " selected";
            }
            $lista_ratios .= ">".htmlspecialchars($fila_ratio['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_ratios);
    }


    // Devuelve la lista de modos de selección de localización actual
    function dame_lista_modos_seleccion_localizacion_actual($modo_seleccion_localizacion_actual)
    {
        $idiomas = new Idiomas();
        $lista = dame_lista_valores(
            array(
                array(MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA, $idiomas->_("Única")),
                array(MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE, $idiomas->_("Múltiple"))),
            array($modo_seleccion_localizacion_actual));
        return ($lista);
    }


    // Devuelve la lista de nodos de una localización sin asignar a equipos de sus instalaciones
    function dame_lista_nodos_localizacion_sin_equipo_instalacion($id_localizacion, $tipo_nodo, $ids_nodos_seleccionados)
    {
        $ids_nodos_equipos_instalaciones = array();
        $info_instalaciones = dame_info_instalaciones_localizacion($id_localizacion);
        foreach ($info_instalaciones as $info_instalacion)
        {
            $ids_nodos_equipos_instalacion = dame_ids_nodos_otros_equipos_instalacion($info_instalacion["id"], $tipo_nodo, NULL);
            $ids_nodos_equipos_instalaciones = array_merge($ids_nodos_equipos_instalaciones, $ids_nodos_equipos_instalacion);
        }

        $lista = "";
        $info_nodos = dame_info_nodos_localizaciones(
            array($id_localizacion),
            $tipo_nodo,
            CLASE_TODAS,
            false);
        foreach ($info_nodos as $info_nodo)
        {
            if ((in_array($info_nodo["id"], $ids_nodos_equipos_instalaciones) == false) || (in_array($info_nodo["id"], $ids_nodos_seleccionados) == true))
            {
                $lista .= dame_opcion_valor_lista_multiple($info_nodo["nombre"], $info_nodo["id"], $ids_nodos_seleccionados);
            }
        }
        return ($lista);
    }


    //
    // Funciones de filtros
    //


    // Devuelve la tabla con la selección de localización actual y de ratio
    function dame_tabla_seleccion_localizacion_actual_ratio(
        $mostrar_seleccion_ratio,
        $seleccion_ratio_visible,
        $contenido_oculto)
    {
        $idiomas = new Idiomas();

        // Modo de selección de localización actual
        $modo_seleccion_localizacion_actual = $_SESSION["modo_seleccion_localizacion_actual"] ?? MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA;

        // Anchuras de las columnas (depende de si se muestra el ratio)
        switch ($modo_seleccion_localizacion_actual)
        {
            case MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA:
            {
                if ($mostrar_seleccion_ratio == true)
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_UNICA_RATIO);
                }
                else
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_UNICA);
                }
                break;
            }
            case MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE:
            {
                if ($mostrar_seleccion_ratio == true)
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_MULTIPLE_RATIO);
                }
                else
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_LOCALIZACION_MULTIPLE);
                }
                break;
            }
            default:
            {
                throw new Exception("Modo de selección de localización actual desconocido: '".$modo_seleccion_localizacion_actual."'");
            }
        }

        // Se recuperan los controles a mostrar
        $controles_seleccion_localizacion = dame_seleccion_localizacion_actual_ratio(
            "seleccion_localizacion_actual",
            $mostrar_seleccion_ratio,
            $seleccion_ratio_visible,
            $modo_seleccion_localizacion_actual);
        switch ($modo_seleccion_localizacion_actual)
        {
            case MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA:
            {
                $clase_dato_fila = "desplegable-simple margenes-verticales";
                $lista_doble_localizaciones = NULL;
                break;
            }
            case MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE:
            {
                $clase_dato_fila = "desplegable-simple margen-superior-pequenyo";
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                $lista_doble_localizaciones = dame_control_lista_doble_localizaciones(
                    "seleccion_localizacion_actual",
                    $idiomas->_("Localizaciones seleccionadas"),
                    $ids_localizaciones_seleccionadas,
                    MAX_LOCALIZACIONES_SELECCIONADAS);
                array_push($anchuras_columnas, 100);
                array_push($controles_seleccion_localizacion, $lista_doble_localizaciones);
                break;
            }
            default:
            {
                throw new Exception("Modo de selección de localización actual desconocido: '".$modo_seleccion_localizacion_actual."'");
            }
        }

        // Se crea la tabla contenedora
        $params_tabla = array(
            "contenido_desplegable" => true,
            "contenido_oculto" => $contenido_oculto
        );
        $tabla = new TablaDatos(
            "tabla-seleccion-localizacion-actual",
            $idiomas->_("Selección de localización"),
            TIPO_TABLA_DATOS_CONTENEDOR,
            $params_tabla
        );

        $params_fila = array(
            "clase_dato" => $clase_dato_fila,
            "anchuras_columnas" => $anchuras_columnas
        );
        $tabla->anyade_fila("seleccion-localizacion-actual", $controles_seleccion_localizacion, $params_fila);

        return ($tabla->dame_tabla());
    }


    // Crea una selección de localización actual y de ratio
    function dame_seleccion_localizacion_actual_ratio(
        $id_controles,
        $mostrar_seleccion_ratio,
        $seleccion_ratio_visible,
        $modo_seleccion_localizacion_actual)
    {
        $idiomas = new Idiomas();

        // Se recupera la localización de la sesión
        $id_localizacion_seleccionada = $_SESSION["id_localizacion"];

        // Se recuperan los controles para la selección de localización actual
        $opciones_extra_lista_seleccion_localizacion_actual = dame_opciones_extra_lista_seleccion_localizacion_actual();
        $control_lista_seleccion_localizacion_actual = dame_control_lista_seleccion_localizacion_actual(
            $id_controles,
            $idiomas->_("Localización actual"),
            $id_localizacion_seleccionada,
            $opciones_extra_lista_seleccion_localizacion_actual,
            $modo_seleccion_localizacion_actual);
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Seleccionar localización"));

        // Controles
        $controles = array(
            $control_lista_seleccion_localizacion_actual,
            $boton);

        // Selección de ratio
        if ($mostrar_seleccion_ratio == true)
        {
            // Se recupera y añade el control para la selección del ratio
            $control_lista_ratios = dame_control_lista_ratios(
                $id_controles,
                $idiomas->_("Ratio"),
                $seleccion_ratio_visible,
                ID_NINGUNO);
            array_push($controles, $control_lista_ratios);
        }

        // Se devuelven los controles
        return ($controles);
    }


    // Recupera las opciones extra de la lista de selección de localización actual
    function dame_opciones_extra_lista_seleccion_localizacion_actual()
    {
        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $permiso_todos_sensores = ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI);
                $permiso_todos_actuadores = ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI);
                $ids_sensores_usuario_actual = dame_ids_sensores_usuario_actual_sensores(false);
                $ids_grupos_sensores_usuario_actual = dame_ids_grupos_sensores_usuario_actual_sensores(false);
                $ids_actuadores_usuario_actual = dame_ids_actuadores_usuario_actual_actuadores(false);
                $ids_grupos_actuadores_usuario_actual = dame_ids_grupos_actuadores_usuario_actual_actuadores(false);
                if (($permiso_todos_sensores == true) || ($permiso_todos_actuadores == true) ||
                    (count($ids_sensores_usuario_actual) > 0) || (count($ids_grupos_sensores_usuario_actual) > 0) ||
                    (count($ids_actuadores_usuario_actual) > 0) || (count($ids_grupos_actuadores_usuario_actual) > 0))
                {
                    $mostrar_sin_localizaciones = true;
                }
                else
                {
                    $mostrar_sin_localizaciones = false;
                }
                if ($mostrar_sin_localizaciones == true)
                {
                    if (($permiso_todos_sensores == true) || ($permiso_todos_actuadores == true))
                    {
                        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_NINGUNA_TODAS;
                    }
                    else
                    {
                        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_TODAS;
                    }
                }
                else
                {
                    $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_TODAS;
                }
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_SELECCION_LOCALIZACION_ACTUAL_DESACTIVADAS_NINGUNA_TODAS;
                break;
            }
        }
        return ($opciones_extra_lista_localizaciones);
    }


    // Crea una selección de localización
    function dame_seleccion_localizacion($id_controles)
    {
        $idiomas = new Idiomas();

        // Localización seleccionada por defecto
        $id_localizacion_seleccionada = $_SESSION["id_localizacion"];
        switch ($id_localizacion_seleccionada)
        {
            case ID_NINGUNO:
            case ID_DESACTIVADO:
            {
                $id_localizacion_seleccionada = ID_NINGUNO;
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                if (count($ids_localizaciones_seleccionadas) == 1)
                {
                    $id_localizacion_seleccionada = $ids_localizaciones_seleccionadas[0];
                }
                else
                {
                    $id_localizacion_seleccionada = ID_NINGUNO;
                }
                break;
            }
        }

        $control_lista_localizaciones = dame_control_lista_localizaciones(
            $id_controles,
            $idiomas->_("Localización"),
            $id_localizacion_seleccionada,
            OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA);
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Seleccionar localización"));

        $controles = array(
            $control_lista_localizaciones,
            $boton
        );
        return ($controles);
    }


    // Crea una selección de instalación de una localización
    function dame_seleccion_instalacion_localizacion($id_controles, $opciones_extra_lista_instalaciones)
    {
        $idiomas = new Idiomas();

        // Localización seleccionada por defecto
        $id_localizacion_seleccionada = $_SESSION["id_localizacion"];
        switch ($id_localizacion_seleccionada)
        {
            case ID_NINGUNO:
            case ID_DESACTIVADO:
            {
                $id_localizacion_seleccionada = ID_TODOS;
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                if (count($ids_localizaciones_seleccionadas) == 1)
                {
                    $id_localizacion_seleccionada = $ids_localizaciones_seleccionadas[0];
                }
                else
                {
                    $id_localizacion_seleccionada = ID_TODOS;
                }
                break;
            }
        }

        $control_lista_localizaciones = dame_control_lista_localizaciones(
            $id_controles,
            $idiomas->_("Localización"),
            $id_localizacion_seleccionada,
            OPCIONES_EXTRA_LISTA_LOCALIZACIONES_TODAS);
        $control_lista_instalaciones = dame_control_lista_instalaciones_localizacion(
            $id_controles,
            $idiomas->_("Instalación"),
            $id_localizacion_seleccionada,
            ID_NINGUNA,
            $opciones_extra_lista_instalaciones);
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Seleccionar instalación"));

        $controles = array(
            $control_lista_localizaciones,
            $control_lista_instalaciones,
            $boton
        );
        return ($controles);
    }


    //
    // Funciones de controles de localizaciones
    //


    // Devuelve si hay que mostrar los controles de localizaciones
    function dame_mostrar_controles_localizaciones()
    {
        if (!isset($_SESSION["modulos"][MODULO_LOCALIZACIONES]))
        {
            return (false);
        }

        $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(false);
        if (count($ids_localizaciones_usuario) == 0)
        {
            $mostrar_controles_localizaciones = false;
        }
        else
        {
            $mostrar_controles_localizaciones = true;
        }
        return ($mostrar_controles_localizaciones);
    }


    // Devuelve los controles de la pestaña de valores y sensores de ratios
    function dame_controles_valores_sensores_ratios_localizacion($id_localizacion)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los ratios de la localización
        $filas_ratios_localizacion = dame_filas_ratios_localizacion($id_localizacion);
        foreach ($filas_ratios_localizacion as $fila_ratio_localizacion)
        {
            $valores_sensores_ratios_localizacion[$fila_ratio_localizacion["ratio"]] = array(
                "valor" => $fila_ratio_localizacion["valor"],
                "id_sensor" => $fila_ratio_localizacion["sensor"]);
        }

        // Se recuperan los ratios (de la red actual)
        $consulta_ratios = "
            SELECT *
            FROM ratios
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_ratios = $bd_red->ejecuta_consulta($consulta_ratios);
        if ($res_ratios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ratios."'");
        }

        // Se añaden los controles de cada uno de los ratios
        $ids_ratios = array();
        $tipos_ratios = array();
        while ($fila_ratio = $res_ratios->dame_siguiente_fila())
        {
            $id_ratio = $fila_ratio["id"];
            $nombre_ratio = $fila_ratio["nombre"];
            $unidad_medida_ratio = $fila_ratio["unidad_medida"];
            $tipo_ratio = $fila_ratio["tipo"];
            $clase_sensor_ratio = $fila_ratio["clase_sensor"];

            array_push($ids_ratios, $id_ratio);
            array_push($tipos_ratios, $tipo_ratio);

            $titulo_control_ratio = $nombre_ratio." (".$unidad_medida_ratio.")";
            $id_control_ratio = "valor_sensor_ratio__".$id_ratio;
            if (array_key_exists($id_ratio, $valores_sensores_ratios_localizacion) == true)
            {
                $valor_ratio = $valores_sensores_ratios_localizacion[$id_ratio]["valor"];
                $id_sensor_ratio = $valores_sensores_ratios_localizacion[$id_ratio]["id_sensor"];
            }
            else
            {
                $valor_ratio = NULL;
                $id_sensor_ratio = NULL;
            }
            switch ($tipo_ratio)
            {
                case TIPO_RATIO_FIJO:
                {
                    $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'><span class='titulo-campo-administracion'>".$titulo_control_ratio.": "."</span><br/>
                                <input type='text' id='".$id_control_ratio."'
                                    class='TLNT_input_float input-administracion' value='".$valor_ratio."'>
                            </div>
                        </div>";
                    break;
                }
                case TIPO_RATIO_VARIABLE:
                {
                    $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'><span class='titulo-campo-administracion'>".$titulo_control_ratio.": "."</span><br/>
                                <select id='".$id_control_ratio."' class='chosen-select-administracion'>";
                    $controles .= dame_lista_sensores($clase_sensor_ratio, array($id_sensor_ratio), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
                    $controles .= "
                                </select>
                            </div>
                        </div>";
                    break;
                }
            }
        }

        // Si no hay ratios se devuelve un texto de información
        $numero_ratios = count($ids_ratios);
        if ($numero_ratios == 0)
        {
            $controles .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("No hay ratios configurados");
        }

        // Se añade información de los ratios en un div oculto
        $lista_ids_ratios = implode(",", $ids_ratios);
        $lista_tipos_ratios = implode(",", $tipos_ratios);
        $controles .= "
            <div id='parametros_ratios' ".
                "numero_ratios='".$numero_ratios."' ".
                "ids_ratios='".$lista_ids_ratios."' ".
                "tipos_ratios='".$lista_tipos_ratios."' ".
                "hidden>
            </div>";
        return ($controles);
    }


    //
    // Funciones de comprobación de localizaciones correctas entre grupos de nodos y nodos
    //


    // Devuelve si las localizaciones del grupo y del nodo son correctas
    function dame_localizaciones_correctas_grupo_localizacion_nodo($tipo_nodo, $id_grupo, $id_localizacion_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la localización del grupo
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_grupos = "grupos_sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_grupos = "grupos_actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $consulta_grupo = "
            SELECT
                localizacion
            FROM ".$tabla_grupos."
            WHERE
                id = '".$bd_red->_($id_grupo)."'";
        $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
        if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
        }
        $fila_grupo = $res_grupo->dame_siguiente_fila();
        $id_localizacion_grupo = $fila_grupo["localizacion"];

        // Localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;

        // Devuelve si las localizaciones son correctas
        $localizaciones_correctas = dame_localizaciones_correctas_grupo_nodo(
            $info_localizaciones_padres,
            $info_localizaciones_hijas,
            $id_localizacion_grupo,
            $id_localizacion_nodo);
        return ($localizaciones_correctas);
    }


    // Devuelve si la localización del grupo es correcta
    function dame_localizacion_correcta_grupo_localizacion_grupo(
        $info_localizaciones_padres,
        $info_localizaciones_hijas,
        $tipo_nodo,
        $id_grupo,
        $id_localizacion_grupo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan las localizaciones de los nodos del grupo
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_nodos = "sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_nodos = "actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $consulta_nodos = "
            SELECT localizacion
            FROM ".$tabla_nodos."
            WHERE
                grupo = '".$bd_red->_($id_grupo)."'";
        $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
        if ($res_nodos == false)
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_nodos."'");
        }

        // Se comprueba la localización del grupo con cada uno de sus nodos
        $localizaciones_correctas = true;
        while ($fila_nodo = $res_nodos->dame_siguiente_fila())
        {
            $id_localizacion_nodo = $fila_nodo["localizacion"];

            // Devuelve si las localizaciones son correctas
            $localizaciones_correctas = dame_localizaciones_correctas_grupo_nodo(
                $info_localizaciones_padres,
                $info_localizaciones_hijas,
                $id_localizacion_grupo,
                $id_localizacion_nodo);
            if ($localizaciones_correctas == false)
            {
                break;
            }
        }
        return ($localizaciones_correctas);
    }


    // Devuelve si las localizaciones del grupo y del nodo son correctas
    function dame_localizaciones_correctas_grupo_nodo(&$info_localizaciones_padres, &$info_localizaciones_hijas, $id_localizacion_grupo, $id_localizacion_nodo)
    {
        // Si la localización del grupo o del nodo es ninguna, las dos localizaciones deben ser ninguna
        if (($id_localizacion_grupo == ID_NINGUNO) || ($id_localizacion_nodo == ID_NINGUNO))
        {
            $localizaciones_correctas = ($id_localizacion_grupo == ID_NINGUNO) && ($id_localizacion_nodo == ID_NINGUNO);
        }
        else
        {
            // Si es la misma localización se permite
            if ($id_localizacion_grupo == $id_localizacion_nodo)
            {
                $localizaciones_correctas = true;
            }
            else
            {
                // Se recuperan las localizaciones padres e hijas (si no se han recuperado ya)
                if ($info_localizaciones_padres === NULL)
                {
                    carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);
                }

                // Se recupera si la localización del grupo es ascendiente de la localización del nodo
                $localizaciones_correctas = existe_localizacion_ascendiente(
                    $info_localizaciones_padres,
                    $id_localizacion_grupo,
                    $id_localizacion_nodo);
            }
        }
        return ($localizaciones_correctas);
    }


    // Si la localización del grupo es ninguna y se modifica, se inicializan las localizaciones de los hijos a la localización del grupo
    function inicializa_localizaciones_nodos_grupo_localizacion_grupo($tipo_nodo, $id_grupo, $id_localizacion_grupo, &$numero_nodos_modificados)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la localización del grupo
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_grupos = "grupos_sensores";
                $tabla_nodos = "sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_grupos = "grupos_actuadores";
                $tabla_nodos = "actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $consulta_grupo = "
            SELECT
                localizacion
            FROM ".$tabla_grupos."
            WHERE
                id = '".$bd_red->_($id_grupo)."'";
        $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
        if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
        }
        $fila_grupo = $res_grupo->dame_siguiente_fila();
        $id_localizacion_grupo_actual = $fila_grupo["localizacion"];

        // Si la localización del grupo era ninguna y ahora es diferente se inicializan
        if (($id_localizacion_grupo_actual == ID_NINGUNO) && ($id_localizacion_grupo != ID_NINGUNO))
        {
            $operacion_modificacion = "
                UPDATE ".$tabla_nodos."
                SET
                    localizacion = '".$bd_red->_($id_localizacion_grupo)."'
                WHERE
                    grupo = '".$bd_red->_($id_grupo)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
            $numero_nodos_modificados = $bd_red->dame_numero_filas_afectadas_ultima_operacion();

            $localizaciones_nodos_inicializadas = true;
        }
        else
        {
            $localizaciones_nodos_inicializadas = false;
        }
        return ($localizaciones_nodos_inicializadas);
    }


    // Devuelve si las localizaciones del grupo y del nodo son correctas
    function dame_localizaciones_grupos_localizacion_correctas($info_localizaciones_padres, $info_localizaciones_hijas, $tipo_nodo, $id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan las localizaciones de los nodos del grupo
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_grupos_nodos = "grupos_sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_grupos_nodos = "grupos_actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $consulta_grupos_nodos = "
            SELECT
                id
            FROM ".$tabla_grupos_nodos."
            WHERE
                localizacion = '".$bd_red->_($id_localizacion)."'";
        $res_grupos_nodos = $bd_red->ejecuta_consulta($consulta_grupos_nodos);
        if ($res_grupos_nodos == false)
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupos_nodos."'");
        }
        if ($res_grupos_nodos->dame_numero_filas() == 0)
        {
            return (true);
        }

        // Se comprueba la localización de cada uno de los grupos
        $localizaciones_correctas = true;
        while ($fila_grupo_nodo = $res_grupos_nodos->dame_siguiente_fila())
        {
            $id_grupo = $fila_grupo_nodo["id"];

            // Devuelve si la localización es correcta
            $localizaciones_correctas = dame_localizacion_correcta_grupo_localizacion_grupo(
                $info_localizaciones_padres,
                $info_localizaciones_hijas,
                $tipo_nodo,
                $id_grupo,
                $id_localizacion);
            if ($localizaciones_correctas == false)
            {
                break;
            }
        }
        return ($localizaciones_correctas);
    }


    //
    // Funciones de nombres de localizaciones
    //


    // Devuelve el nombre de la localización
    function dame_nombre_localizacion($id_localizacion)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($id_localizacion)
        {
            case ID_NINGUNO:
            {
                $nombre_localizacion = $idiomas->_("Ninguna");
                break;
            }
            default:
            {
                $consulta_localizacion = "
                    SELECT nombre
                    FROM localizaciones
                    WHERE
                        id = '".$bd_red->_($id_localizacion)."'";
                $res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
                if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
                }
                $fila_localizacion = $res_localizacion->dame_siguiente_fila();
                $nombre_localizacion = $fila_localizacion["nombre"];
                break;
            }
        }
        return ($nombre_localizacion);
    }


    // Devuelve los nombres de las localizaciones
    function dame_nombres_localizaciones($ids_localizaciones)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_localizaciones = array();
        if (in_array(ID_NINGUNO, $ids_localizaciones) == true)
        {
            $nombres_localizaciones[ID_NINGUNO] = $idiomas->_("Ninguna");
        }
        $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
        $consulta_localizaciones = "
            SELECT
                id,
                nombre
            FROM localizaciones
            WHERE
                id IN (".$cadena_ids_localizaciones_consulta.")";
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            $nombres_localizaciones[$fila_localizacion["id"]] = $fila_localizacion["nombre"];
        }
        return ($nombres_localizaciones);
    }


    //
    // Funciones de nodos de localizaciones
    //


    // Devuelve la información de los nodos asignados a las localizaciones
    function dame_info_nodos_localizaciones(
        $ids_localizaciones,
        $tipo_nodo,
        $clase_nodo,
        $nodos_visibles_localizaciones_hijas)
    {
        // Si no hay localizaciones se devuelve un vector vacío
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los nombres de las localizaciones
        $nombres_localizaciones = dame_nombres_localizaciones($ids_localizaciones);

        // Se recuperan los nodos de las localizaciones
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_nodos = "sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_nodos = "actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $info_nodos_localizaciones = array();
        $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
        $consulta_nodos_localizaciones = "
            SELECT
                id,
                nombre,
                localizacion,
                visible_localizaciones_hijas
            FROM ".$tabla_nodos."
            WHERE
                (localizacion IN (".$cadena_ids_localizaciones_consulta."))";
        if ($clase_nodo != CLASE_TODAS)
        {
            $consulta_nodos_localizaciones .= "
                AND (clase = '".$bd_red->_($clase_nodo)."')";
        }
        if ($nodos_visibles_localizaciones_hijas == true)
        {
            $consulta_nodos_localizaciones .= "
                AND (visible_localizaciones_hijas = '".VALOR_SI."')";
        }
        $consulta_nodos_localizaciones .= "
            ORDER BY nombre ASC";
        $res_nodos_localizaciones = $bd_red->ejecuta_consulta($consulta_nodos_localizaciones);
        if ($res_nodos_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_nodos_localizaciones."'");
        }
        while ($fila_nodo_localizaciones = $res_nodos_localizaciones->dame_siguiente_fila())
        {
            $nombre_localizacion = $nombres_localizaciones[$fila_nodo_localizaciones["localizacion"]];
            $info_nodo_localizaciones = array(
                "id" => $fila_nodo_localizaciones["id"],
                "nombre" => $fila_nodo_localizaciones["nombre"],
                "nombre_localizacion" => $nombre_localizacion,
                "visible_localizaciones_hijas" => $fila_nodo_localizaciones["visible_localizaciones_hijas"]);
            array_push($info_nodos_localizaciones, $info_nodo_localizaciones);
        }
        return ($info_nodos_localizaciones);
    }


    // Devuelve la información de los grupos de nodos asignados a las localizaciones
    function dame_info_grupos_nodos_localizaciones($ids_localizaciones, $tipo_nodo, $clase_nodo)
    {
        // Si no hay localizaciones se devuelve un vector vacío
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los nodos de las localizaciones
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_grupos = "grupos_sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_grupos = "grupos_actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $info_grupos_localizaciones = array();
        $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
        $consulta_grupos_localizaciones = "
            SELECT
                id,
                nombre
            FROM ".$tabla_grupos."
            WHERE
                (localizacion IN (".$cadena_ids_localizaciones_consulta."))";
        if ($clase_nodo != CLASE_TODAS)
        {
            $consulta_grupos_localizaciones .= "
                AND (clase = '".$bd_red->_($clase_nodo)."')";
        }
        $consulta_grupos_localizaciones .= "
            ORDER BY nombre ASC";
        $res_grupos_localizaciones = $bd_red->ejecuta_consulta($consulta_grupos_localizaciones);
        if ($res_grupos_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos_localizaciones."'");
        }
        while ($fila_grupo_localizaciones = $res_grupos_localizaciones->dame_siguiente_fila())
        {
            $info_grupo_localizaciones = array(
                "id" => $fila_grupo_localizaciones["id"],
                "nombre" => $fila_grupo_localizaciones["nombre"]);
            array_push($info_grupos_localizaciones, $info_grupo_localizaciones);
        }
        return ($info_grupos_localizaciones);
    }


    // Devuelve los identificadores de los nodos asignados a las localizaciones
    function dame_ids_nodos_localizaciones($ids_localizaciones, $tipo_nodo, $nodos_visibles_localizaciones_hijas)
    {
        // Si no hay localizaciones se devuelve un vector vacío
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los nodos de las localizaciones
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_nodos = "sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_nodos = "actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $ids_nodos_localizaciones = array();
        $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
        $consulta_nodos_localizaciones = "
            SELECT
                id
            FROM ".$tabla_nodos."
            WHERE
                (localizacion IN (".$cadena_ids_localizaciones_consulta."))";
        if ($nodos_visibles_localizaciones_hijas == true)
        {
            $consulta_nodos_localizaciones .= "
                AND (visible_localizaciones_hijas = '1')";
        }
        $res_nodos_localizaciones = $bd_red->ejecuta_consulta($consulta_nodos_localizaciones);
        if ($res_nodos_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_nodos_localizaciones."'");
        }
        while ($fila_nodo_localizaciones = $res_nodos_localizaciones->dame_siguiente_fila())
        {
            array_push($ids_nodos_localizaciones, $fila_nodo_localizaciones["id"]);
        }
        return ($ids_nodos_localizaciones);
    }


    // Devuelve los identificadores de los grupos de nodos asignados a las localizaciones
    function dame_ids_grupos_nodos_localizaciones($ids_localizaciones, $tipo_nodo)
    {
        // Si no hay localizaciones se devuelve un vector vacío
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los nodos de las localizaciones
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tabla_grupos = "grupos_sensores";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tabla_grupos = "grupos_actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $ids_grupos_localizaciones = array();
        $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
        $consulta_grupos_localizaciones = "
            SELECT
                id
            FROM ".$tabla_grupos."
            WHERE
                (localizacion IN (".$cadena_ids_localizaciones_consulta."))
            ORDER BY nombre ASC";
        $res_grupos_localizaciones = $bd_red->ejecuta_consulta($consulta_grupos_localizaciones);
        if ($res_grupos_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos_localizaciones."'");
        }
        while ($fila_grupo_localizaciones = $res_grupos_localizaciones->dame_siguiente_fila())
        {
            array_push($ids_grupos_localizaciones, $fila_grupo_localizaciones["id"]);
        }
        return ($ids_grupos_localizaciones);
    }


    // Devuelve los identificadores de los nodos visibles en las localizaciones especificadas
    function dame_ids_nodos_visibles_localizaciones($ids_localizaciones, $tipo_nodo)
    {
        // Si no hay localizaciones
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        // Localizaciones ascendientes y descendientes
        $ids_localizaciones_ascendientes = dame_ids_localizaciones_ascendientes($ids_localizaciones);
        $ids_localizaciones_descendientes = dame_ids_localizaciones_descendientes($ids_localizaciones);

        // Se recuperan los nodos de la localización
        $ids_nodos_localizaciones = dame_ids_nodos_localizaciones(
            $ids_localizaciones,
            $tipo_nodo,
            false);

        // Se recuperan los nodos de las localizaciones ascendientes y descendientes
        $ids_nodos_localizaciones_ascendientes = dame_ids_nodos_localizaciones(
            $ids_localizaciones_ascendientes,
            $tipo_nodo,
            true);
        $ids_nodos_localizaciones_descendientes = dame_ids_nodos_localizaciones(
            $ids_localizaciones_descendientes,
            $tipo_nodo,
            false);

        // Nodos visibles en las localizaciones
        $ids_nodos_visibles_localizaciones = array_unique(array_merge(
            $ids_nodos_localizaciones,
            $ids_nodos_localizaciones_ascendientes,
            $ids_nodos_localizaciones_descendientes));
        return ($ids_nodos_visibles_localizaciones);
    }


    // Devuelve los identificadores de los grupos de nodos visibles en las localizaciones especificadas
    function dame_ids_grupos_nodos_visibles_localizaciones($ids_localizaciones, $tipo_nodo)
    {
        // Si no hay localizaciones
        if (count($ids_localizaciones) == 0)
        {
            return (array());
        }

        // Localizaciones descendientes
        $ids_localizaciones_descendientes = dame_ids_localizaciones_descendientes($ids_localizaciones);

        // Se recuperan los nodos de la localización
        $ids_grupos_nodos_localizaciones = dame_ids_grupos_nodos_localizaciones(
            $ids_localizaciones,
            $tipo_nodo);

        // Se recuperan los grupos de nodos de las localizaciones descendientes
        $ids_grupos_nodos_localizaciones_descendientes = dame_ids_grupos_nodos_localizaciones(
            $ids_localizaciones_descendientes,
            $tipo_nodo);

        // Nodos visibles en la localización
        $ids_grupos_nodos_visibles_localizaciones = array_unique(array_merge(
            $ids_grupos_nodos_localizaciones,
            $ids_grupos_nodos_localizaciones_descendientes));
        return ($ids_grupos_nodos_visibles_localizaciones);
    }


    // Devuelve los identificadores de los nodos administrables en las localizaciones especificadas
    function dame_ids_nodos_administrables_localizaciones($ids_localizaciones, $tipo_nodo)
    {
        // Localizaciones ascendientes y descendientes
        $ids_localizaciones_descendientes = dame_ids_localizaciones_descendientes($ids_localizaciones);

        // Se recuperan los nodos de las localizaciones
        $ids_nodos_localizaciones = dame_ids_nodos_localizaciones(
            $ids_localizaciones,
            $tipo_nodo,
            false);

        // Se recuperan los nodos de las localizaciones descendientes
        $ids_nodos_localizaciones_descendientes = dame_ids_nodos_localizaciones(
            $ids_localizaciones_descendientes,
            $tipo_nodo,
            false);

        // Nodos visibles en la localización
        $ids_nodos_visibles_localizaciones = array_unique(array_merge(
            $ids_nodos_localizaciones,
            $ids_nodos_localizaciones_descendientes));
        return ($ids_nodos_visibles_localizaciones);
    }


    //
    // Funciones de obtención de información de localizaciones
    //


    function dame_fila_localizacion($id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_localizacion = "
            SELECT *
            FROM localizaciones
            WHERE
                id = '".$bd_red->_($id_localizacion)."'";
        $res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
        if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
        }
        $fila_localizacion = $res_localizacion->dame_siguiente_fila();
        return ($fila_localizacion);
    }


    function dame_filas_ratios_localizacion($id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ratios = "
            SELECT *
            FROM ratios_localizaciones
            WHERE
                localizacion = '".$bd_red->_($id_localizacion)."'";
        $res_ratios = $bd_red->ejecuta_consulta($consulta_ratios);
        if ($res_ratios == false)
        {
            throw new Exception("Ha ocurrido un error en la consulta: '".$consulta_ratios."'");
        }
        $filas_ratios = array();
        while ($fila_ratio = $res_ratios->dame_siguiente_fila())
        {
            array_push($filas_ratios, $fila_ratio);
        }
        return ($filas_ratios);
    }


    //
    // Funciones de instalaciones
    //


    // Devuelve la fila del equipo del nodo especificado
    function dame_fila_equipo_instalacion_localizacion_nodo($id_localizacion, $tipo_nodo, $id_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipos = "
            SELECT
                equipos_instalaciones.*
            FROM
                equipos_instalaciones,
                instalaciones
            WHERE
                (instalaciones.localizacion = '".$bd_red->_($id_localizacion)."')
                AND (instalaciones.id = equipos_instalaciones.instalacion)";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }

        $fila_equipo_nodo = NULL;
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $cadena_ids_nodos_equipo = $fila_equipo["sensores"];
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $cadena_ids_nodos_equipo = $fila_equipo["actuadores"];
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            if ($cadena_ids_nodos_equipo != "")
            {
                $ids_nodos_equipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_nodos_equipo);
                if (in_array($id_nodo, $ids_nodos_equipo) == true)
                {
                    $fila_equipo_nodo = $fila_equipo;
                    break;
                }
            }
        }
        return ($fila_equipo_nodo);
    }


    //
    // Funciones de ratios
    //


    // Recupera hay que aplicar el ratio en el campo de clase de sensor
    function dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo)
    {
        // Si no hay ratio seleccionado no se aplica el ratio
        if ($id_ratio == ID_NINGUNO)
        {
            return (false);
        }

        // Nota: Si es usuario interno, no se comprueba si están visibles los controles de localización
        if (!isset($_SESSION["usuario_interno"]))
        {
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == false)
            {
                return (false);
            }
        }

        // Campo sin agrupación de valores
        $campo_sin_agrupaciones_valores = elimina_tipo_agrupacion_valores_campo_sensor($campo);

        // Se aplica el ratio según la clase y campo de sensor
        $aplicar_ratio = false;
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                switch ($campo_sin_agrupaciones_valores)
                {
                    case CAMPO_INCREMENTO:
                    case CAMPO_INCREMENTO_POTENCIA:
                    case CAMPO_COSTE:
                    case CAMPO_TODOS:
                    {
                        $aplicar_ratio = true;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($campo_sin_agrupaciones_valores)
                {
                    case CAMPO_INCREMENTO:
                    case CAMPO_INCREMENTO_POTENCIA:
                    case CAMPO_TODOS:
                    {
                        $aplicar_ratio = true;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                switch ($campo_sin_agrupaciones_valores)
                {
                    case CAMPO_INCREMENTO:
                    case CAMPO_CONSUMO:
                    case CAMPO_COSTE:
                    case CAMPO_TODOS:
                    {
                        $aplicar_ratio = true;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                switch ($campo_sin_agrupaciones_valores)
                {
                    case CAMPO_INCREMENTO:
                    case CAMPO_TODOS:
                    {
                        $aplicar_ratio = true;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($campo_sin_agrupaciones_valores)
                {
					case CAMPO_VALOR:
					case CAMPO_INCREMENTO:
                    case CAMPO_TODOS:
                    {
                        $aplicar_ratio = true;
                        break;
                    }
                }
                break;
            }
        }
        return ($aplicar_ratio);
    }


    // Devuelve la información de un ratio
    function dame_info_ratio($id_ratio)
    {
        // Si no hay ratio se devuelve NULL
        if ($id_ratio == ID_NINGUNO)
        {
            return (NULL);
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la información del ratio
        $consulta_ratio = "
            SELECT *
            FROM ratios
            WHERE
                id = '".$bd_red->_($id_ratio)."'";
        $res_ratio = $bd_red->ejecuta_consulta($consulta_ratio);
        if (($res_ratio == false) || ($res_ratio->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_ratio."'");
        }
        $fila_ratio = $res_ratio->dame_siguiente_fila();
        $nombre_ratio = $fila_ratio["nombre"];
        $sustituir_unidad_medida_sensor_ratio = $fila_ratio["sustituir_unidad_medida_sensor"];
        $unidad_medida_ratio = $fila_ratio["unidad_medida"];
        $tipo_ratio = $fila_ratio["tipo"];
        $clase_sensor_ratio = $fila_ratio["clase_sensor"];
        $campo_sensor_ratio = $fila_ratio["campo_sensor"];
        $valor_defecto_ratio = $fila_ratio["valor_defecto"];
        $id_sensor_defecto_ratio = $fila_ratio["sensor_defecto"];

        // Información del ratio
        $info_ratio = array(
            "nombre" => $nombre_ratio,
            "sustituir_unidad_medida_sensor" => $sustituir_unidad_medida_sensor_ratio,
            "unidad_medida" => $unidad_medida_ratio,
            "tipo" => $tipo_ratio,
            "clase_sensor" => $clase_sensor_ratio,
            "campo_sensor" => $campo_sensor_ratio,
            "valor_defecto" => $valor_defecto_ratio,
            "id_sensor_defecto" => $id_sensor_defecto_ratio);
        return ($info_ratio);
    }


    // Devuelve la información de un ratio para el sensor especificado
    function dame_info_ratio_sensor_fechas(
        $id_ratio,
        $id_sensor,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas)
    {
        // Si no hay ratio se devuelve NULL
        if ($id_ratio == ID_NINGUNO)
        {
            return (NULL);
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la información del ratio
        $info_ratio = dame_info_ratio($id_ratio);

        // Se recupera la localización del sensor
        $consulta_sensor = "
            SELECT localizacion
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
        if (($res_sensor == false) || ($res_sensor->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor."'");
        }
        $fila_sensor = $res_sensor->dame_siguiente_fila();
        $id_localizacion = $fila_sensor["localizacion"];

        // Se recuperan el valor y sensor del ratio de la localización
        if ($id_localizacion != ID_NINGUNO)
        {
            $consulta_ratio_localizacion = "
                SELECT
                    valor,
                    sensor
                FROM ratios_localizaciones
                WHERE
                    (localizacion = '".$bd_red->_($id_localizacion)."')
                    AND (ratio = '".$bd_red->_($id_ratio)."')";
            $res_ratio_localizacion = $bd_red->ejecuta_consulta($consulta_ratio_localizacion);
            if ($res_ratio_localizacion == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_ratio_localizacion."'");
            }
            if ($res_ratio_localizacion->dame_numero_filas() > 0)
            {
                $fila_ratio_localizacion = $res_ratio_localizacion->dame_siguiente_fila();
                $valor_ratio = $fila_ratio_localizacion["valor"];
                $id_sensor_ratio = $fila_ratio_localizacion["sensor"];
            }
            else
            {
                $valor_ratio = $info_ratio["valor_defecto"];
                $id_sensor_ratio = $info_ratio["id_sensor_defecto"];
            }
        }
        else
        {
            $valor_ratio = $info_ratio["valor_defecto"];
            $id_sensor_ratio = $info_ratio["id_sensor_defecto"];
        }

        // Se recuperan los valores del ratio según el tipo de ratio
        switch ($info_ratio["tipo"])
        {
            case TIPO_RATIO_FIJO:
            {
                $info_ratio["valor"] = $valor_ratio;
                break;
            }
            case TIPO_RATIO_VARIABLE:
            {
                anyade_valores_sensor_ratio_variable(
                    $info_ratio,
                    $id_sensor_ratio,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
                break;
            }
        }

        // Se devuelve la información del ratio
        return ($info_ratio);
    }


    // Añade los valores de un sensor de un ratio variable (con sus fechas correspondientes)
    function anyade_valores_sensor_ratio_variable(
        &$info_ratio,
        $id_sensor_ratio,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Intervalo de valores en tiempo real
        $intervalo_valores_tiempo_real =
            ($intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL) ||
            ($intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS) ||
            ($intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS);

        // Nota: Si es intervalo de valores en tiempo real o cuartohorario y el campo del ratio es incremento,
        // no se recuperan valores (no tiene sentido)
        $campo_sensor = $info_ratio["campo_sensor"];
        if (($id_sensor_ratio == ID_NINGUNO) ||
            ((($intervalo_valores_tiempo_real == true) || ($intervalo_valores == INTERVALO_VALORES_CUARTOHORA)) &&
            ($campo_sensor == CAMPO_INCREMENTO)))
        {
            $valores_ratio = NULL;
        }
        else
        {
            // Campo de sensor
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                case INTERVALO_VALORES_SEMANA:
                case INTERVALO_VALORES_MES:
                {
                    switch ($campo_sensor)
                    {
                        case CAMPO_VALOR:
                        {
                            $campo_sensor = CAMPO_VALOR_MEDIA;
                            break;
                        }
                        case CAMPO_INCREMENTO:
                        {
                            $campo_sensor = CAMPO_INCREMENTO_SUMA;
                            break;
                        }
                    }
                    break;
                }
            }

            // Consulta de valores del sensor
            $consulta_valores_sensor = dame_consulta_valores_sensor(
                $id_sensor_ratio,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                NULL);
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Se recorren los valores del sensor
            $valores_ratio = NULL;
            $primera_cadena_fecha_hora_base_datos_utc = NULL;
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
            {
                // Fecha y valor
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo_sensor];
                if ($valor === NULL)
                {
                    continue;
                }
                $valor = (float) $valor;

                // Se añaden la fecha y el valor
                if ($valores_ratio === NULL)
                {
                    $primera_cadena_fecha_hora_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                    $valores_ratio = array(
                        "cadenas_fechas_horas_base_datos_utc" => array(),
                        "valores" => array()
                    );
                    $info_ratio["indice_fecha_valor_ratio_actual"] = 0;
                }
                array_push($valores_ratio["cadenas_fechas_horas_base_datos_utc"], $cadena_fecha_hora_base_datos_utc);
                array_push($valores_ratio["valores"], $valor);
            }

            // Si el campo es valor (puntual)
            if ($campo_sensor == CAMPO_VALOR)
            {
                // Si no hay valores:
                // - Se añade (si existe) el último valor en tiempo real
                if ($valores_ratio === NULL)
                {
                    $recuperar_ultimo_valor_tiempo_real_sensor = true;
                }

                // Si el intervalo tiempo real y hay valores:
                // - Se añade (si existe) el último valor en tiempo real anterior a la fecha del primer valor
                if (($intervalo_valores_tiempo_real == true) &&
                    ($primera_cadena_fecha_hora_base_datos_utc !== NULL))
                {
                    $recuperar_ultimo_valor_tiempo_real_sensor = true;
                }

                // Si hay que añadir el último valor en tiempo real sel sensor
                if ($recuperar_ultimo_valor_tiempo_real_sensor == true)
                {
                    $fila_sensor = dame_fila_sensor($id_sensor_ratio);
                    $nombre_sensor = $fila_sensor["nombre"];
                    $consulta_valor_sensor = "
                        SELECT
                            hora,
                            valor
                        FROM datos_genericos
                        WHERE
                            (sensor = '".$bd_datos->_($nombre_sensor)."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    if ($primera_cadena_fecha_hora_base_datos_utc !== NULL)
                    {
                        $consulta_valor_sensor .= "
                            AND (hora < '".$primera_cadena_fecha_hora_base_datos_utc."')";
                    }
                    $consulta_valor_sensor .= "
                        ORDER BY hora DESC
                        LIMIT 1";
                    $res_valor_sensor = $bd_datos->ejecuta_consulta($consulta_valor_sensor);
                    if ($res_valor_sensor == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_valor_sensor."'");
                    }
                    if ($res_valor_sensor->dame_numero_filas() > 0)
                    {
                        $fila_valor_sensor = $res_valor_sensor->dame_siguiente_fila();
                        $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor["hora"];
                        $valor = (float) $fila_valor_sensor["valor"];
                        if ($valores_ratio === NULL)
                        {
                            $valores_ratio = array(
                                "cadenas_fechas_horas_base_datos_utc" => array(),
                                "valores" => array(),
                                "intervalo_valores" => array()
                            );
                            $info_ratio["indice_fecha_valor_ratio_actual"] = 0;
                        }
                        array_unshift($valores_ratio["cadenas_fechas_horas_base_datos_utc"], $cadena_fecha_hora_base_datos_utc);
                        array_unshift($valores_ratio["valores"], $valor);
                        array_unshift($valores_ratio["intervalo_valores"], $intervalo_valores);
                    }
                }
            }
        }

        // Se guardan los valores del ratio en la información del ratio
        $info_ratio["valores"] = $valores_ratio;
    }


    // Devuelve la información de un ratio para el sensor especificado
    function dame_info_ratio_sensor_fecha(
        $id_ratio,
        $id_sensor,
        $cadena_fecha_hora_base_datos_utc,
        $intervalo_valores)
    {
        $info_ratio = dame_info_ratio_sensor_fechas(
            $id_ratio,
            $id_sensor,
            $cadena_fecha_hora_base_datos_utc,
            $cadena_fecha_hora_base_datos_utc,
            $intervalo_valores);
        return ($info_ratio);
    }


    // Modifica la unidad de medida según el ratio especificado
    function modifica_unidad_medida_ratio($info_ratio, &$unidad_medida)
    {
        $idiomas = new Idiomas();

        $sustituir_unidad_medida_sensor = $info_ratio["sustituir_unidad_medida_sensor"];
        $unidad_medida_ratio = $info_ratio["unidad_medida"];
        if ($sustituir_unidad_medida_sensor == VALOR_SI)
        {
            $unidad_medida = $unidad_medida_ratio;
        }
        else
        {
            if ($unidad_medida != "")
            {
                $unidad_medida .= " ";
            }
            $unidad_medida .= $idiomas->_("por")." ".$unidad_medida_ratio;
        }
    }


    // Devuelve el valor del ratio para la fecha especificada
    function dame_valor_ratio_fecha(&$info_ratio, $cadena_fecha_hora_base_datos_utc, $actualiza_indice_fecha_valor_ratio_actual)
    {
        switch ($info_ratio["tipo"])
        {
            case TIPO_RATIO_FIJO:
            {
                $valor_ratio = $info_ratio["valor"];
                break;
            }
            case TIPO_RATIO_VARIABLE:
            {
                // Se recupera el valor de la fecha especificada:
                // - Si es puntual el inmediatamente anterior si no existe
                // - Si es incremental debe ser la misma fecha
                $valor_ratio = NULL;
                $valores_ratio = $info_ratio["valores"];
                if ($valores_ratio !== NULL)
                {
                    $indice_fecha_valor_ratio_inicial = $info_ratio["indice_fecha_valor_ratio_actual"];
                    $indice_fecha_valor_ratio = NULL;
                    $cadenas_fechas_horas_ratio_base_datos_utc = $info_ratio["valores"]["cadenas_fechas_horas_base_datos_utc"];
                    $valores_ratio = $info_ratio["valores"]["valores"];
                    $numero_fechas_horas_ratio = count($cadenas_fechas_horas_ratio_base_datos_utc);
                    if ($indice_fecha_valor_ratio_inicial < $numero_fechas_horas_ratio)
                    {
                        for ($i = $indice_fecha_valor_ratio_inicial; $i < $numero_fechas_horas_ratio; $i++)
                        {
                            if ($cadenas_fechas_horas_ratio_base_datos_utc[$i] > $cadena_fecha_hora_base_datos_utc)
                            {
                                break;
                            }
                            switch ($info_ratio["campo_sensor"])
                            {
                                case CAMPO_VALOR:
                                {
                                    $indice_fecha_valor_ratio = $i;
                                    break;
                                }
                                case CAMPO_INCREMENTO:
                                {
                                    if ($cadenas_fechas_horas_ratio_base_datos_utc[$i] == $cadena_fecha_hora_base_datos_utc)
                                    {
                                        $indice_fecha_valor_ratio = $i;
                                    }
                                    break;
                                }
                            }
                            if (($info_ratio["campo_sensor"] == CAMPO_INCREMENTO) && ($indice_fecha_valor_ratio !== NULL))
                            {
                                break;
                            }
                        }
                        if ($indice_fecha_valor_ratio === NULL)
                        {
                            if ($actualiza_indice_fecha_valor_ratio_actual == true)
                            {
                                $info_ratio["indice_fecha_valor_ratio_actual"] = $i;
                            }
                        }
                        else
                        {
                            $valor_ratio = $valores_ratio[$indice_fecha_valor_ratio];
                            if ($actualiza_indice_fecha_valor_ratio_actual == true)
                            {
                                $info_ratio["indice_fecha_valor_ratio_actual"] = $indice_fecha_valor_ratio;
                                if ($info_ratio["campo_sensor"] == CAMPO_INCREMENTO)
                                {
                                    $info_ratio["indice_fecha_valor_ratio_actual"] += 1;
                                }
                            }
                        }
                    }
                }
                break;
            }
        }
        return ($valor_ratio);
    }


    // Devuelve el valor del ratio para la fecha especificada (fecha local, ignorando las horas)
    function dame_valor_ratio_fecha_local_ignora_horas(&$info_ratio, $cadena_fecha_hora_base_datos_utc, $actualiza_indice_fecha_valor_ratio_actual)
    {
        switch ($info_ratio["tipo"])
        {
            case TIPO_RATIO_FIJO:
            {
                $valor_ratio = $info_ratio["valor"];
                break;
            }
            case TIPO_RATIO_VARIABLE:
            {
                // Zona horaria
                $zona_horaria_local = dame_zona_horaria_local();

                // Fecha local
                $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora(
                    $cadena_fecha_hora_base_datos_utc,
                    FORMATO_FECHA_HORA_BASE_DATOS,
                    ZONA_HORARIA_UTC,
                    $zona_horaria_local);
                $cadena_fecha_base_datos_local = convierte_formato_fecha(
                    $cadena_fecha_hora_base_datos_local,
                    FORMATO_FECHA_HORA_BASE_DATOS,
                    FORMATO_FECHA_BASE_DATOS);

                // Se recupera el valor de la fecha especificada:
                // - Si es puntual el inmediatamente anterior si no existe
                // - Si es incremental debe ser la misma fecha (fecha local, ignorando las horas)
                $valor_ratio = NULL;
                $valores_ratio = $info_ratio["valores"];
                if ($valores_ratio !== NULL)
                {
                    $indice_fecha_valor_ratio_inicial = $info_ratio["indice_fecha_valor_ratio_actual"];
                    $indice_fecha_valor_ratio = NULL;
                    $cadenas_fechas_horas_ratio_base_datos_utc = $info_ratio["valores"]["cadenas_fechas_horas_base_datos_utc"];
                    $valores_ratio = $info_ratio["valores"]["valores"];
                    $numero_fechas_horas_ratio = count($cadenas_fechas_horas_ratio_base_datos_utc);
                    if ($indice_fecha_valor_ratio_inicial < $numero_fechas_horas_ratio)
                    {
                        for ($i = $indice_fecha_valor_ratio_inicial; $i < $numero_fechas_horas_ratio; $i++)
                        {
                            // Fecha local de ratio
                            $cadena_fecha_hora_ratio_base_datos_local = cambia_zona_horaria_cadena_fecha_hora(
                                $cadenas_fechas_horas_ratio_base_datos_utc[$i],
                                FORMATO_FECHA_HORA_BASE_DATOS,
                                ZONA_HORARIA_UTC,
                                $zona_horaria_local);
                            $cadena_fecha_ratio_base_datos_local = convierte_formato_fecha(
                                $cadena_fecha_hora_ratio_base_datos_local,
                                FORMATO_FECHA_HORA_BASE_DATOS,
                                FORMATO_FECHA_BASE_DATOS);

                            // Se comparan las fechas locales
                            if ($cadena_fecha_ratio_base_datos_local > $cadena_fecha_base_datos_local)
                            {
                                break;
                            }
                            switch ($info_ratio["campo_sensor"])
                            {
                                case CAMPO_VALOR:
                                {
                                    $indice_fecha_valor_ratio = $i;
                                    break;
                                }
                                case CAMPO_INCREMENTO:
                                {
                                    if ($cadena_fecha_ratio_base_datos_local == $cadena_fecha_base_datos_local)
                                    {
                                        $indice_fecha_valor_ratio = $i;
                                    }
                                    break;
                                }
                            }
                            if (($info_ratio["campo_sensor"] == CAMPO_INCREMENTO) && ($indice_fecha_valor_ratio !== NULL))
                            {
                                break;
                            }
                        }
                        if ($indice_fecha_valor_ratio === NULL)
                        {
                            if ($actualiza_indice_fecha_valor_ratio_actual == true)
                            {
                                $info_ratio["indice_fecha_valor_ratio_actual"] = $i;
                            }
                        }
                        else
                        {
                            $valor_ratio = $valores_ratio[$indice_fecha_valor_ratio];
                            if ($actualiza_indice_fecha_valor_ratio_actual == true)
                            {
                                $info_ratio["indice_fecha_valor_ratio_actual"] = $indice_fecha_valor_ratio;
                                if ($info_ratio["campo_sensor"] == CAMPO_INCREMENTO)
                                {
                                    $info_ratio["indice_fecha_valor_ratio_actual"] += 1;
                                }
                            }
                        }
                    }
                }
                break;
            }
        }
        return ($valor_ratio);
    }


    // Aplica el valor del ratio al valor especificado
    function aplica_ratio_valor($valor_ratio, &$valor)
    {
        if (($valor_ratio !== NULL) && ($valor_ratio > 0))
        {
            $valor /= $valor_ratio;
        }
        else
        {
            $valor = NULL;
        }
    }


    // Aplica el ratio en la fecha especificada al valor especificado
    function aplica_ratio_fecha_valor(&$info_ratio, $cadena_fecha_hora_base_datos_utc, &$valor)
    {
        $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_base_datos_utc, true);
        if ($valor_ratio === NULL)
        {
            $info_ratio["indice_fecha_valor_ratio_actual"] -= 1;
            $zona_horaria = dame_zona_horaria_local();
            $fecha_hora_ratio = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria);
            $fecha_hora_ratio->setTime(0, 0, 0);
            $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_ratio, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);
            $valor_ratio = dame_valor_ratio_fecha($info_ratio, $cadena_fecha_hora_base_datos_utc, true);
        }
        aplica_ratio_valor($valor_ratio, $valor);
    }

    // Se elimina el sensor de los ratios de tipo variable correspondientes (y de los ratios localizaciones)
    function elimina_sensor_ratios_variables_localizaciones($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_modificacion_ratios = "
            UPDATE ratios
            SET
                sensor_defecto = '".$bd_red->_(ID_NINGUNO)."'
            WHERE
                sensor_defecto = '".$bd_red->_($id_sensor)."'";
        $res_modificacion_ratios = $bd_red->ejecuta_operacion($operacion_modificacion_ratios);
        if ($res_modificacion_ratios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion_ratios."'");
        }

        $operacion_modificacion_ratios_localizaciones = "
            UPDATE ratios_localizaciones
            SET
                sensor = '".$bd_red->_(ID_NINGUNO)."'
            WHERE
                sensor = '".$bd_red->_($id_sensor)."'";
        $res_modificacion_ratios_localizaciones = $bd_red->ejecuta_operacion($operacion_modificacion_ratios_localizaciones);
        if ($res_modificacion_ratios_localizaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion_ratios_localizaciones."'");
        }
    }


    //
    // Funciones de acciones de usuario
    //


    // Devuelve la información de usuario de los ratios de una localización para las acciones de usuario
    function dame_info_ratios_localizacion_accion_usuario($filas_ratios_localizacion)
    {
        $info_ratios_localizacion_accion_usuario = array();
        foreach ($filas_ratios_localizacion as $fila_ratio_localizacion)
        {
            $id_ratio = $fila_ratio_localizacion["ratio"];
            $fila_ratio = dame_fila_ratio($id_ratio);
            $nombre_ratio = $fila_ratio["nombre"];
            $unidad_medida_ratio = $fila_ratio["unidad_medida"];
            $tipo_ratio = $fila_ratio["tipo"];
            switch ($tipo_ratio)
            {
                case TIPO_RATIO_FIJO:
                {
                    $valor_sensor_ratio = $fila_ratio_localizacion["valor"];
                    break;
                }
                case TIPO_RATIO_VARIABLE:
                {
                    $id_sensor_ratio = $fila_ratio_localizacion["sensor"];
                    $valor_sensor_ratio = dame_nombre_sensor($id_sensor_ratio);
                    break;
                }
            }
            $info_ratios_localizacion_accion_usuario[$nombre_ratio] = array(
                "unidad_medida" => $unidad_medida_ratio,
                "tipo" => $tipo_ratio,
                "valor_sensor" => $valor_sensor_ratio);
        }
        ksort($info_ratios_localizacion_accion_usuario);
        return ($info_ratios_localizacion_accion_usuario);
    }


    //
    // Funciones de eliminación de nodos de equipos de instalaciones
    //


    // Elimina el id del nodo correspondiente de los equipos de las instalaciones
    function elimina_id_nodo_equipos_instalaciones($tipo_nodo, $id_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipos = "
            SELECT
                equipos_instalaciones.id,
                equipos_instalaciones.sensores,
                equipos_instalaciones.actuadores
            FROM
                equipos_instalaciones,
                instalaciones
            WHERE
                (equipos_instalaciones.instalacion = instalaciones.id)
                AND (instalaciones.red = '".$_SESSION["id_red"]."')";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }

        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $campo_ids_nodos = "sensores";
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $campo_ids_nodos = "actuadores";
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            $cadena_ids_nodos_equipo = $fila_equipo[$campo_ids_nodos];
            if ($cadena_ids_nodos_equipo != "")
            {
                $ids_nodos_equipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_nodos_equipo);
                if (in_array($id_nodo, $ids_nodos_equipo) == true)
                {
                    $ids_nodos_equipo_modificados = array_diff($ids_nodos_equipo, array($id_nodo));
                    $cadena_ids_nodos_equipo_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_nodos_equipo_modificados);

                    // Se modifican los ids de los nodos corrrespondientes
                    $operacion_modificacion = "
                        UPDATE equipos_instalaciones
                        SET
                            ".$campo_ids_nodos." = '".$bd_red->_($cadena_ids_nodos_equipo_modificada)."'
                        WHERE
                            id = '".$bd_red->_($fila_equipo["id"])."'";
                    $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                    if ($res_modificacion == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                    }
                }
            }
        }
    }
?>
