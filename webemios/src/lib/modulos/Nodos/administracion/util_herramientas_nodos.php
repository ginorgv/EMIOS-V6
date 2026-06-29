<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');


    //
    // Funciones de herramientas de nodos
    //


    function dame_ids_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            case TIPO_NODO_ACTUADOR:
            {
                $info_nodos_localizacion = dame_info_nodos_localizaciones(
                    array($id_localizacion),
                    $tipo_nodo,
                    $clase_nodo,
                    false);
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $ids_nodos_localizacion = array();
        foreach ($info_nodos_localizacion as $info_nodo_localizacion)
        {
            array_push($ids_nodos_localizacion, $info_nodo_localizacion["id"]);
        }
        return ($ids_nodos_localizacion);
    }


    function dame_ids_grupos_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            case TIPO_NODO_ACTUADOR:
            {
                $info_grupos_nodos_localizacion = dame_info_grupos_nodos_localizaciones(
                    array($id_localizacion),
                    $tipo_nodo,
                    $clase_nodo);
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        $ids_grupos_nodos_localizacion = array();
        foreach ($info_grupos_nodos_localizacion as $info_grupo_nodo_localizacion)
        {
            array_push($ids_grupos_nodos_localizacion, $info_grupo_nodo_localizacion["id"]);
        }
        return ($ids_grupos_nodos_localizacion);
    }


    function dame_ids_nodos_grupo($tipo_nodo, $id_grupo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $ids_nodos_grupo = array();
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
            SELECT id
            FROM ".$tabla_nodos."
            WHERE
                grupo = '".$bd_red->_($id_grupo)."'
            ORDER BY nombre ASC";
        $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
        if ($res_nodos == false)
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_nodos."'");
        }
        while ($fila_nodo = $res_nodos->dame_siguiente_fila())
        {
            array_push($ids_nodos_grupo, $fila_nodo["id"]);
        }
        return ($ids_nodos_grupo);
    }


    function dame_lista_nodos(
        $tipo_nodo,
        $clase_nodo,
        $ids_nodos_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista_nodos = dame_lista_sensores($clase_nodo, $ids_nodos_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista_nodos = dame_lista_actuadores($clase_nodo, $ids_nodos_seleccionados, $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        return ($lista_nodos);
    }


    function dame_lista_grupos_nodos(
        $tipo_nodo,
        $clase_nodo,
        $ids_grupos_nodos_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista_nodos = dame_lista_grupos_sensores($clase_nodo, $ids_grupos_nodos_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista_nodos = dame_lista_grupos_actuadores($clase_nodo, $ids_grupos_nodos_seleccionados, $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        return ($lista_nodos);
    }


    function asigna_localizacion_nodos($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_localizacion = $parametros["id_localizacion"];
        $tipo_nodo = $parametros["tipo_nodo"];
        $clase_nodo = $parametros["clase_nodo"];
        if (array_key_exists("ids_nodos", $parametros) == true)
        {
            $ids_nodos = $parametros["ids_nodos"];
        }
        else
        {
            $ids_nodos = array();
        }
        $ids_grupos_nodos = $parametros["ids_grupos_nodos"];
        if (array_key_exists("ids_grupos_nodos", $parametros) == true)
        {
            $ids_grupos_nodos = $parametros["ids_grupos_nodos"];
        }
        else
        {
            $ids_grupos_nodos = array();
        }

        // Se recuperan los nodos de la localización
        $ids_nodos_anteriores = dame_ids_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);
        $ids_grupos_nodos_anteriores = dame_ids_grupos_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);

        // Nodos y grupos de nodos asignados y desasignados de la localización
        $ids_nodos_asignados = array_diff($ids_nodos, $ids_nodos_anteriores);
        $ids_nodos_desasignados = array_diff($ids_nodos_anteriores, $ids_nodos);
        $ids_grupos_nodos_asignados = array_diff($ids_grupos_nodos, $ids_grupos_nodos_anteriores);
        $ids_grupos_nodos_desasignados = array_diff($ids_grupos_nodos_anteriores, $ids_grupos_nodos);

        // Nodos y grupos de nodos modificados
        $ids_nodos_modificados = array_merge($ids_nodos_asignados, $ids_nodos_desasignados);
        $ids_grupos_nodos_modificados = array_merge($ids_grupos_nodos_asignados, $ids_grupos_nodos_desasignados);

        // Se comprueba si los nodos y los grupos de nodos son visibles por el usuario actual
        if (count($ids_nodos_modificados) > 0)
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_nodos_usuario_actual = dame_todos_ids_sensores_usuario_actual();
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_nodos_usuario_actual = dame_todos_ids_actuadores_usuario_actual();
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            foreach ($ids_nodos_modificados as $id_nodo)
            {
                if (in_array($id_nodo, $ids_nodos_usuario_actual) == false)
                {
                    throw new Exception("Nodo no visible por el usuario actual (id: '".$id_nodo."')");
                }
            }
        }
        if (count($ids_grupos_nodos_modificados) > 0)
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_grupos_nodos_usuario_actual = dame_todos_ids_grupos_sensores_usuario_actual();
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_grupos_nodos_usuario_actual = dame_todos_ids_grupos_actuadores_usuario_actual();
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            foreach ($ids_grupos_nodos_modificados as $id_grupo)
            {
                if (in_array($id_grupo, $ids_grupos_nodos_usuario_actual) == false)
                {
                    throw new Exception("Grupo de nodos no visible por el usuario actual (id: '".$id_grupo."')");
                }
            }
        }
        if ((count($ids_nodos_modificados) == 0) && (count($ids_grupos_nodos_modificados) == 0))
        {
            // No hay cambios en los nodos
            $resultado = array(
                "res" => "OK",
                "msg" => $idiomas->_("Localización asignada correctamente"));
            return ($resultado);
        }

        // Carga de información de localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Flag de localizaciones correctas
        $localizaciones_correctas = true;

        // Se comprueban las localizaciones correctas de los nodos añadidos y eliminados de la localización
        if ($localizaciones_correctas == true)
        {
            foreach ($ids_nodos_modificados as $id_nodo)
            {
                // Se recupera la fila del nodo
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $fila_nodo = dame_fila_sensor($id_nodo);

                        // No se permite modificar la localización si el sensor no es administrable
                        $nombre_sensor = $fila_nodo["nombre"];
                        $administrable = $fila_nodo["administrable"];
                        if ($administrable == VALOR_NO)
                        {
                            $mensaje_error = $idiomas->_("No se permite modificar el sensor")."\n(".
                                $nombre_sensor.")";
                            $resultado = array(
                                "res" => "ERROR",
                                "msg" => $mensaje_error);
                            return ($resultado);
                        }
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    {
                        $fila_nodo = dame_fila_actuador($id_nodo);
                        break;
                    }
                }

                // Información del nodo
                $id_nodo = $fila_nodo["id"];
                $nombre_nodo = $fila_nodo["nombre"];
                $id_grupo = $fila_nodo["grupo"];

                // Si no hay grupo no se comprueba nada en el nodo
                if ($id_grupo == ID_NINGUNO)
                {
                    continue;
                }

                // Localización del nodo
                if (in_array($id_nodo, $ids_nodos_asignados) == true)
                {
                    $id_localizacion_nodo = $id_localizacion;
                }
                else
                {
                    $id_localizacion_nodo = ID_NINGUNO;
                }

                // Localización del grupo
                if (in_array($id_grupo, $ids_grupos_nodos_asignados) == true)
                {
                    $id_localizacion_grupo = $id_localizacion;
                }
                else
                {
                    if (in_array($id_grupo, $ids_grupos_nodos_desasignados) == true)
                    {
                        $id_localizacion_grupo = ID_NINGUNO;
                    }
                    else
                    {
                        // Se recupera la localización del grupo
                        switch ($tipo_nodo)
                        {
                            case TIPO_NODO_SENSOR:
                            {
                                $fila_grupo = dame_fila_grupo_sensores($id_grupo);
                                break;
                            }
                            case TIPO_NODO_ACTUADOR:
                            {
                                $fila_grupo = dame_fila_grupo_actuadores($id_grupo);
                                break;
                            }
                        }
                        $id_localizacion_grupo = $fila_grupo["localizacion"];
                    }
                }

                // Devuelve si las localizaciones son correctas
                $localizaciones_correctas = dame_localizaciones_correctas_grupo_nodo(
                    $info_localizaciones_padres,
                    $info_localizaciones_hijas,
                    $id_localizacion_grupo,
                    $id_localizacion_nodo);
                if ($localizaciones_correctas == false)
                {
                    $res = "ERROR";
                    switch ($tipo_nodo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $nombre_grupo = dame_nombre_grupo_sensores($id_grupo);
                            $msg = $idiomas->_("Las localizaciones de los sensores y sus grupos no son correctas")."\n".
                                "(".$idiomas->_("sensor").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).", ".
                                $idiomas->_("grupo").": ".htmlspecialchars($nombre_grupo, ENT_QUOTES).")";
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        {
                            $nombre_grupo = dame_nombre_grupo_actuadores($id_grupo);
                            $msg = $idiomas->_("Las localizaciones de los actuadores y sus grupos no son correctas")."\n".
                                "(".$idiomas->_("actuador").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).", ".
                                $idiomas->_("grupo").": ".htmlspecialchars($nombre_grupo, ENT_QUOTES).")";
                            break;
                        }
                    }
                }
            }
        }

        // Se comprueban las localizaciones correctas de los grupos añadidos y eliminados de la localización
        if ($localizaciones_correctas == true)
        {
            foreach ($ids_grupos_nodos_modificados as $id_grupo)
            {
                // Si las localizaciones no son correctas se sale del bucle
                if ($localizaciones_correctas == false)
                {
                    break;
                }

                // Localización del grupo
                if (in_array($id_grupo, $ids_grupos_nodos_asignados) == true)
                {
                    $id_localizacion_grupo = $id_localizacion;
                }
                else
                {
                    $id_localizacion_grupo = ID_NINGUNO;
                }

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
                }
                $consulta_nodos = "
                    SELECT
                        id,
                        localizacion,
                        nombre
                    FROM ".$tabla_nodos."
                    WHERE
                        grupo = '".$bd_red->_($id_grupo)."'";
                $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
                if ($res_nodos == false)
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_nodos."'");
                }

                // Se comprueba la localización del grupo con cada uno de sus nodos
                while ($fila_nodo = $res_nodos->dame_siguiente_fila())
                {
                    $id_nodo = $fila_nodo["id"];
                    $nombre_nodo = $fila_nodo["nombre"];

                    // Localización del nodo (a comprobar)
                    if (in_array($id_nodo, $ids_nodos_asignados) == true)
                    {
                        $id_localizacion_nodo = $id_localizacion;
                    }
                    else
                    {
                        if (in_array($id_nodo, $ids_nodos_desasignados) == true)
                        {
                            $id_localizacion_nodo = ID_NINGUNO;
                        }
                        else
                        {
                            $id_localizacion_nodo = $fila_nodo["localizacion"];
                        }
                    }

                    // Devuelve si las localizaciones son correctas
                    $localizaciones_correctas = dame_localizaciones_correctas_grupo_nodo(
                        $info_localizaciones_padres,
                        $info_localizaciones_hijas,
                        $id_localizacion_grupo,
                        $id_localizacion_nodo);
                    if ($localizaciones_correctas == false)
                    {
                        $res = "ERROR";
                        switch ($tipo_nodo)
                        {
                            case TIPO_NODO_SENSOR:
                            {
                                $nombre_grupo = dame_nombre_grupo_sensores($id_grupo);
                                $msg = $idiomas->_("Las localizaciones de los grupos y sus sensores no son correctas")."\n".
                                    "(".$idiomas->_("grupo").": ".htmlspecialchars($nombre_grupo, ENT_QUOTES).", ".
                                    $idiomas->_("sensor").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).")";
                                break;
                            }
                            case TIPO_NODO_ACTUADOR:
                            {
                                $nombre_grupo = dame_nombre_grupo_actuadores($id_grupo);
                                $msg = $idiomas->_("Las localizaciones de los grupos y sus actuadores no son correctas")."\n".
                                    "(".$idiomas->_("grupo").": ".htmlspecialchars($nombre_grupo, ENT_QUOTES).", ".
                                    $idiomas->_("actuador").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).")";
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        // Se modifican las localizaciones de los nodos y los grupos de nodos
        if ($localizaciones_correctas == true)
        {
            // Tablas de nodos y grupos de nodos
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $tabla_nodos = "sensores";
                    $tabla_grupos_nodos = "grupos_sensores";
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $tabla_nodos = "actuadores";
                    $tabla_grupos_nodos = "grupos_actuadores";
                    break;
                }
            }

            // Se modifican las localizaciones de los nodos
            foreach ($ids_nodos_modificados as $id_nodo)
            {
                // Localización del nodo
                if (in_array($id_nodo, $ids_nodos_asignados) == true)
                {
                    $id_localizacion_nodo = $id_localizacion;
                }
                else
                {
                    $id_localizacion_nodo = ID_NINGUNO;
                }

                // Se modifica el nodo
                $operacion_modificacion = "
                    UPDATE ".$tabla_nodos."
                    SET
                        localizacion = '".$bd_red->_($id_localizacion_nodo)."'
                    WHERE
                        id = '".$bd_red->_($id_nodo)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }

                // Se elimina el nodo de los equipos de las instalaciones de la localización anterior
                elimina_id_nodo_equipos_instalaciones($tipo_nodo, $id_nodo);

                // Nota: No es necesario notificar la operación de administración del nodo (sensor o actuador)
                // (las localizaciones no afectan a los servicios)
            }

            // Se modifican las localizaciones de los grupos de nodos
            foreach ($ids_grupos_nodos_modificados as $id_grupo)
            {
                // Localización del grupo de nodos
                if (in_array($id_grupo, $ids_grupos_nodos_asignados) == true)
                {
                    $id_localizacion_grupo = $id_localizacion;
                }
                else
                {
                    $id_localizacion_grupo = ID_NINGUNO;
                }

                // Se modifica el grupo de nodos
                $operacion_modificacion = "
                    UPDATE ".$tabla_grupos_nodos."
                    SET
                        localizacion = '".$bd_red->_($id_localizacion_grupo)."'
                    WHERE
                        id = '".$bd_red->_($id_grupo)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_asignar_localizacion_nodos(
                $id_localizacion,
                $tipo_nodo,
                $ids_nodos_asignados,
                $ids_nodos_desasignados,
                $ids_grupos_nodos_asignados,
                $ids_grupos_nodos_desasignados);

            $resultado = array(
                "res" => "OK",
                "msg" => $idiomas->_("Localización asignada correctamente"));
        }
        else
        {
            // Error en las localizaciones
            $resultado = array(
                "res" => $res,
                "msg" => $msg);
        }

        // Se devuelve el resultado
        return ($resultado);
    }


    function asigna_grupo_nodos($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_grupo = $parametros["id_grupo"];
        $tipo_nodo = $parametros["tipo_nodo"];
        if (array_key_exists("ids_nodos", $parametros) == true)
        {
            $ids_nodos = $parametros["ids_nodos"];
        }
        else
        {
            $ids_nodos = array();
        }

        // Se recuperan los nodos de la localización
        $ids_nodos_anteriores = dame_ids_nodos_grupo($tipo_nodo, $id_grupo);

        // Nodos nodos asignados y desasignados del grupo
        $ids_nodos_asignados = array_diff($ids_nodos, $ids_nodos_anteriores);
        $ids_nodos_desasignados = array_diff($ids_nodos_anteriores, $ids_nodos);

        // Nodos modificados
        $ids_nodos_modificados = array_merge($ids_nodos_asignados, $ids_nodos_desasignados);

        // Se comprueba si los nodos son visibles por el usuario actual
        if (count($ids_nodos_modificados) > 0)
        {
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_nodos_usuario_actual = dame_todos_ids_sensores_usuario_actual();
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_nodos_usuario_actual = dame_todos_ids_actuadores_usuario_actual();
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            foreach ($ids_nodos_modificados as $id_nodo)
            {
                if (in_array($id_nodo, $ids_nodos_usuario_actual) == false)
                {
                    throw new Exception("Nodo no visible por el usuario actual (id: '".$id_nodo."')");
                }
            }
        }
        else
        {
            // No hay cambios en los nodos
            $resultado = array(
                "res" => "OK",
                "msg" => $idiomas->_("Grupo asignado correctamente"));
            return ($resultado);
        }

        // Carga de información de localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);

        // Flag de localizaciones correctas
        $localizaciones_correctas = true;

        // Se comprueban las localizaciones correctas del grupo
        if ($localizaciones_correctas == true)
        {
            // Localización del grupo
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $fila_grupo = dame_fila_grupo_sensores($id_grupo);
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $fila_grupo = dame_fila_grupo_actuadores($id_grupo);
                    break;
                }
            }
            $id_localizacion_grupo = $fila_grupo["localizacion"];

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
            }
            $cadena_ids_nodos_asignados_consulta = dame_cadena_ids_consulta($ids_nodos_asignados);
            $consulta_nodos = "
                SELECT *
                FROM ".$tabla_nodos."
                WHERE
                    id IN (".$cadena_ids_nodos_asignados_consulta.")";
            $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
            if ($res_nodos == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_nodos."'");
            }

            // Se comprueba la localización del grupo con cada uno de sus nodos
            while ($fila_nodo = $res_nodos->dame_siguiente_fila())
            {
                $id_nodo = $fila_nodo["id"];
                $nombre_nodo = $fila_nodo["nombre"];
                $id_localizacion_nodo = $fila_nodo["localizacion"];

                // Se comprueba si el nodo es administrable
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        // No se permite modificar el grupo si el sensor no es administrable
                        $nombre_sensor = $fila_nodo["nombre"];
                        $administrable = $fila_nodo["administrable"];
                        if ($administrable == VALOR_NO)
                        {
                            $mensaje_error = $idiomas->_("No se permite modificar el sensor")."\n(".
                                $nombre_sensor.")";
                            $resultado = array(
                                "res" => "ERROR",
                                "msg" => $mensaje_error);
                            return ($resultado);
                        }
                        break;
                    }
                }

                // Devuelve si las localizaciones son correctas
                $localizaciones_correctas = dame_localizaciones_correctas_grupo_nodo(
                    $info_localizaciones_padres,
                    $info_localizaciones_hijas,
                    $id_localizacion_grupo,
                    $id_localizacion_nodo);
                if ($localizaciones_correctas == false)
                {
                    $res = "ERROR";
                    switch ($tipo_nodo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $nombre_grupo = dame_nombre_grupo_sensores($id_grupo);
                            $msg = $idiomas->_("Las localizaciones de los sensores no son correctas")."\n".
                                "(".$idiomas->_("sensor").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).")";
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        {
                            $nombre_grupo = dame_nombre_grupo_actuadores($id_grupo);
                            $msg = $idiomas->_("Las localizaciones de los actuadores no son correctas")."\n".
                                "(".$idiomas->_("actuador").": ".htmlspecialchars($nombre_nodo, ENT_QUOTES).")";
                            break;
                        }
                    }
                    break;
                }
            }
        }

        // Se modifican los grupos de los nodos
        if ($localizaciones_correctas == true)
        {
            // Tabla de nodos
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
            }

            // Se modifican los grupos de los nodos
            foreach ($ids_nodos_modificados as $id_nodo)
            {
                // Grupo del nodo
                if (in_array($id_nodo, $ids_nodos_asignados) == true)
                {
                    $id_grupo_nodo = $id_grupo;
                }
                else
                {
                    $id_grupo_nodo = ID_NINGUNO;
                }

                // Se modifica el nodo
                $operacion_modificacion = "
                    UPDATE ".$tabla_nodos."
                    SET
                        grupo = '".$bd_red->_($id_grupo_nodo)."'
                    WHERE
                        id = '".$bd_red->_($id_nodo)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }

                // Acciones a realizar al modificar el grupo de un nodo
                realiza_acciones_grupo_nodo_modificado($tipo_nodo, $id_nodo);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_asignar_grupo_nodos(
                $id_grupo,
                $tipo_nodo,
                $ids_nodos_asignados,
                $ids_nodos_desasignados);

            $resultado = array(
                "res" => "OK",
                "msg" => $idiomas->_("Grupo asignado correctamente"));
        }
        else
        {
            // Error en las localizaciones
            $resultado = array(
                "res" => $res,
                "msg" => $msg);
        }

        // Se devuelve el resultado
        return ($resultado);
    }


    // Realiza acciones al modificar el grupo de un nodo
    function realiza_acciones_grupo_nodo_modificado($tipo_nodo, $id_nodo)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $fila_sensor = dame_fila_sensor($id_nodo);
                $tipo_sensor = $fila_sensor["tipo"];

                // Se notifica la operación de administración del sensor
                switch ($tipo_sensor)
                {
                    case TIPO_SENSOR_EXTERNO:
                    {
                        $cadena_parametros_tipo_sensor = $fila_sensor["parametros_tipo"];
                        $parametros_sensor_externo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_sensor);
                        $clase_sensor_externo = $parametros_sensor_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                        $parametros_extra = array(
                            "clase_sensor_externo" => $clase_sensor_externo,
                            "clase_sensor_externo_anterior" => $clase_sensor_externo);
                        break;
                    }
                    default:
                    {
                        $parametros_extra = array();
                        break;
                    }
                }
                notifica_operacion_administracion_sensor($tipo_sensor, OPERACION_MODIFICACION, $id_nodo, $parametros_extra);

                // Se recarga la configuración del dispositivo del sensor (no se ha modificado)
                switch ($tipo_sensor)
                {
                    case TIPO_SENSOR_REAL:
                    {
                        $id_dispositivo = dame_dispositivo_sensor_real($fila_sensor);
                        recarga_configuracion_dispositivo($id_dispositivo);
                        break;
                    }
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $fila_actuador = dame_fila_actuador($id_nodo);
                $tipo_actuador = $fila_actuador["tipo"];

                // Se notifica la operación de administración del actuador
                switch ($tipo_actuador)
                {
                    case TIPO_ACTUADOR_SOFTWARE:
                    {
                        notifica_operacion_administracion_actuador_software(OPERACION_MODIFICACION, $id_nodo);
                        break;
                    }
                }

                // Se recarga la configuración del dispositivo del actuador (no se ha modificado)
                switch ($tipo_actuador)
                {
                    case TIPO_ACTUADOR_HARDWARE:
                    {
                        $id_dispositivo = dame_dispositivo_actuador_hardware($fila_actuador);
                        recarga_configuracion_dispositivo($id_dispositivo);
                        break;
                    }
                }
                break;
            }
        }
    }


    //
    // Funciones de acciones de usuario
    //


    // Añade la acción de usuario de asignación de localización a nodos
    function anyade_accion_usuario_asignar_localizacion_nodos(
        $id_localizacion,
        $tipo_nodo,
        $ids_nodos_asignados,
        $ids_nodos_desasignados,
        $ids_grupos_nodos_asignados,
        $ids_grupos_nodos_desasignados)
    {
        // Nombre de localización
        $nombre_localizacion = dame_nombre_localizacion($id_localizacion);

        // Parámetros de la acción
        $parametros_accion_usuario = array();

        // Tipo y objeto de la acción
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tipo_accion_usuario = TIPO_ACCION_USUARIO_ASIGNAR_LOCALIZACION_SENSORES;
                if (count($ids_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES_ASIGNADOS] = dame_nombres_sensores($ids_nodos_asignados);
                }
                if (count($ids_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES_DESASIGNADOS] = dame_nombres_sensores($ids_nodos_desasignados);
                }
                if (count($ids_grupos_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_GRUPOS_ASIGNADOS] = dame_nombres_grupos_sensores($ids_grupos_nodos_asignados);
                }
                if (count($ids_grupos_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_GRUPOS_DESASIGNADOS] = dame_nombres_grupos_sensores($ids_grupos_nodos_desasignados);
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tipo_accion_usuario = TIPO_ACCION_USUARIO_ASIGNAR_LOCALIZACION_ACTUADORES;
                if (count($ids_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES_ASIGNADOS] = dame_nombres_actuadores($ids_nodos_asignados);
                }
                if (count($ids_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES_DESASIGNADOS] = dame_nombres_actuadores($ids_nodos_desasignados);
                }
                if (count($ids_grupos_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_GRUPOS_ASIGNADOS] = dame_nombres_grupos_actuadores($ids_grupos_nodos_asignados);
                }
                if (count($ids_grupos_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_GRUPOS_DESASIGNADOS] = dame_nombres_grupos_actuadores($ids_grupos_nodos_desasignados);
                }
                break;
            }
        }

        // Objeto de acción de usuario
        $objeto_accion_usuario = $nombre_localizacion;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de asignación de grupo a nodos
    function anyade_accion_usuario_asignar_grupo_nodos(
        $id_grupo,
        $tipo_nodo,
        $ids_nodos_asignados,
        $ids_nodos_desasignados)
    {
        // Nombre de grupo
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $nombre_grupo = dame_nombre_grupo_sensores($id_grupo);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $nombre_grupo = dame_nombre_grupo_actuadores($id_grupo);
                break;
            }
        }

        // Parámetros de la acción
        $parametros_accion_usuario = array();

        // Tipo y objeto de la acción
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $tipo_accion_usuario = TIPO_ACCION_USUARIO_ASIGNAR_GRUPO_SENSORES;
                if (count($ids_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES_ASIGNADOS] = dame_nombres_sensores($ids_nodos_asignados);
                }
                if (count($ids_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES_DESASIGNADOS] = dame_nombres_sensores($ids_nodos_desasignados);
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $tipo_accion_usuario = TIPO_ACCION_USUARIO_ASIGNAR_GRUPO_ACTUADORES;
                if (count($ids_nodos_asignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES_ASIGNADOS] = dame_nombres_actuadores($ids_nodos_asignados);
                }
                if (count($ids_nodos_desasignados) > 0)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES_DESASIGNADOS] = dame_nombres_actuadores($ids_nodos_desasignados);
                }
                break;
            }
        }

        // Objeto de acción de usuario
        $objeto_accion_usuario = $nombre_grupo;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
