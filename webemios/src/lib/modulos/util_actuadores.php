<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    //
    // Funciones de envío de mensajes MQTT de administración de actuadores software
    //


    function notifica_operacion_administracion_actuador_software($operacion_administracion, $id_actuador)
    {
        // Se envia la accion al servidor MQTT del servidor EMIOS
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta() == true)
        {
            switch ($operacion_administracion)
            {
                // Operaciones de administración
                case OPERACION_ADICION:
                {
                    $mqtt->publica("SOFTWARE_ACT/ACT/".$id_actuador."/ADDED", "", 1);
                    break;
                }
                case OPERACION_MODIFICACION:
                {
                    $mqtt->publica("SOFTWARE_ACT/ACT/".$id_actuador."/MODIFIED", "", 1);
                    break;
                }
                case OPERACION_BORRADO:
                {
                    $mqtt->publica("SOFTWARE_ACT/ACT/".$id_actuador."/DELETED", "", 1);
                    break;
                }
            }
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT");
        }
    }


    //
    // Funciones de actuador hardware
    //


    // Devuelve el identificador del dispositivo de un actuador hardware
    function dame_dispositivo_actuador_hardware($fila_actuador)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera el identificador del axón
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actuador['parametros_tipo']);
        $id_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];

        // Se recupera el identificador del dispositivo del axón
        $consulta_dispositivo = "
            SELECT dispositivo
            FROM axones
            WHERE
                id = '".$bd_red->_($id_axon)."'";
        $res_dispositivo = $bd_red->ejecuta_consulta($consulta_dispositivo);
        if (($res_dispositivo == false) || ($res_dispositivo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_dispositivo."'");
        }
        $fila_dispositivo = $res_dispositivo->dame_siguiente_fila();
        $id_dispositivo = $fila_dispositivo["dispositivo"];

        // Se devuelve el identificador del dispositivo
        return ($id_dispositivo);
    }


    //
    // Funciones de consultas de actuadores y grupos
    //


    // Devuelve la condición de consulta del filtro de actuadores
    function dame_condicion_consulta_filtro_actuadores($filtro)
    {
        $campos = array("actuadores.nombre");
        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
        return ($condicion_consulta_filtro_busqueda);
    }


    // Devuelve la condición de consulta del filtro de grupos de actuadores
    function dame_condicion_consulta_filtro_grupos_actuadores($filtro)
    {
        $campos = array("grupos_actuadores.nombre");
        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
        return ($condicion_consulta_filtro_busqueda);
    }


    // Devuelve la condición de consulta del estado de actuadores
    function dame_condicion_consulta_estado_actuadores($estado)
    {
        $condicion = "";
        if ($estado != ESTADO_ACTUADOR_TODOS)
        {
            switch ($estado)
            {
                case ESTADO_ACTUADOR_OK:
                {
                    $condicion .= "
                        AND (((actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_OK."')
                                OR (actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_EN_EJECUCION."')
                                OR (actuadores.estado_ejecucion_ultima_accion IS NULL))
                            AND (actuadores.ultimo_error_ejecucion_accion_json = ''))";
                    break;
                }
                case ESTADO_ACTUADOR_ERROR:
                {
                    $condicion .= "
                        AND (((actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_ERROR."')
                                OR (actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_NO_CONECTADO."'))
                            OR (actuadores.ultimo_error_ejecucion_accion_json <> ''))";
                    break;
                }
                case ESTADO_ACTUADOR_ERROR_EJECUCION_ACCION:
                {
                    $condicion .= "
                        AND ((actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_ERROR."')
                            OR (actuadores.ultimo_error_ejecucion_accion_json <> ''))";
                    break;
                }
                case ESTADO_ACTUADOR_NO_CONECTADO:
                {
                    $condicion .= "
                        AND (actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_NO_CONECTADO."')";
                    break;
                }
                case ESTADO_ACTUADOR_EN_EJECUCION:
                {
                    $condicion .= "
                        AND (actuadores.estado_ejecucion_ultima_accion = '".ESTADO_EJECUCION_ACCION_EN_EJECUCION."')";
                    break;
                }
                case ESTADO_ACTUADOR_SIN_ACCION:
                {
                    $condicion .= "
                        AND (actuadores.estado_ejecucion_ultima_accion IS NULL)";
                    break;
                }
            }
        }
        return ($condicion);
    }


    // Devuelve la condición de consulta de actuadores del usuario actual
    function dame_condicion_consulta_actuadores_usuario_actual($incluir_actuadores_grupos)
    {
        if (!isset($GLOBALS['condicion_consulta_actuadores_usuario_actual']))
        {
            $condicion_consulta_actuadores = "";
            if ($incluir_actuadores_grupos == false)
            {
                $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(false);
                $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores_usuario);
                $condicion_consulta_actuadores .= "
                    (actuadores.id IN (".$cadena_ids_actuadores_consulta."))";
            }
            else
            {
                $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(false);
                $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(false);
                $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores_usuario);
                $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_usuario);
                $condicion_consulta_actuadores .= "
                    ((actuadores.id IN (".$cadena_ids_actuadores_consulta.")) OR (actuadores.grupo IN (".$cadena_ids_grupos_actuadores_consulta.")))";
            }
            $GLOBALS['condicion_consulta_actuadores_usuario_actual'] = $condicion_consulta_actuadores;
        }
        else
        {
            $condicion_consulta_actuadores = $GLOBALS['condicion_consulta_actuadores_usuario_actual'];
        }
        return ($condicion_consulta_actuadores);
    }


    // Devuelve la condición de consulta de grupos de actuadores del usuario actual
    function dame_condicion_consulta_grupos_actuadores_usuario_actual($incluir_grupos_actuadores)
    {
        if (!isset($GLOBALS['condicion_consulta_grupos_actuadores_usuario_actual']))
        {
            $condicion_consulta_grupos_actuadores = "";
            $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual($incluir_grupos_actuadores);
            $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_usuario);
            $condicion_consulta_grupos_actuadores .= "
                (grupos_actuadores.id IN (".$cadena_ids_grupos_actuadores_consulta."))";
        }
        else
        {
            $condicion_consulta_grupos_actuadores = $GLOBALS['condicion_consulta_grupos_actuadores_usuario_actual'];
        }
        return ($condicion_consulta_grupos_actuadores);
    }


    //
    // Funciones de identificadores de actuadores y grupos
    //


    // Devuelve los identificadores de los actuadores
    function dame_ids_actuadores()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_actuadores = "
            SELECT id
            FROM actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }
        $ids_actuadores = array();
        while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
        {
            array_push($ids_actuadores, $fila_actuador["id"]);
        }
        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores
    function dame_ids_grupos_actuadores()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos = "
            SELECT id
            FROM grupos_actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }
        $ids_grupos = array();
        while ($fila_grupo = $res_grupos->dame_siguiente_fila())
        {
            array_push($ids_grupos, $fila_grupo["id"]);
        }
        return ($ids_grupos);
    }


    //
    // Funciones de permisos de actuadores y grupos
    //


    // Devuelve si se muestran todos los actuadores
    function dame_mostrar_todos_actuadores()
    {
        $mostrar_todos_actuadores =
            (($_SESSION["id_localizacion"] == ID_DESACTIVADO) &&
            (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI)));
        return ($mostrar_todos_actuadores);
    }


    // Devuelve los identificadores de los actuadores visibles para el usuario actual
    function dame_ids_actuadores_usuario_actual($incluir_actuadores_grupos)
    {
        // Se recuperan los identificadores de los actuadores correspondientes
        if (!isset($_SESSION["usuario_interno"]))
        {
            if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
            {
                $ids_actuadores = dame_ids_actuadores_usuario_actual_actuadores($incluir_actuadores_grupos);
            }
            else
            {
                $ids_actuadores = dame_ids_actuadores_usuario_actual_localizaciones($_SESSION["id_localizacion"]);
            }
        }
        else
        {
            // Si es usuario interno, se devuelven todos los actuadores visibles por actuadores y por localizaciones
            $ids_actuadores = dame_todos_ids_actuadores_usuario_actual();
        }
        return ($ids_actuadores);
    }


    // Devuelve todos los identificadores de actuadores del usuario actual
    function dame_todos_ids_actuadores_usuario_actual()
    {
        if (!isset($GLOBALS['todos_ids_actuadores_usuario_actual']))
        {
            $ids_actuadores_actuadores = dame_ids_actuadores_usuario_actual_actuadores(true);
            $ids_actuadores_localizaciones = dame_ids_actuadores_usuario_actual_localizaciones(ID_TODOS);
            $ids_actuadores = array_unique(array_merge($ids_actuadores_actuadores, $ids_actuadores_localizaciones));
            array_push($ids_actuadores, ID_NINGUNO);
            $GLOBALS['todos_ids_actuadores_usuario_actual'] = $ids_actuadores;
        }
        else
        {
            $ids_actuadores = $GLOBALS['todos_ids_actuadores_usuario_actual'];
        }
        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los actuadores visibles para el usuario actual (según los permisos del módulo Actuadores)
    function dame_ids_actuadores_usuario_actual_actuadores($incluir_actuadores_grupos)
    {
        // Ids de actuadores y grupos de actuadores de los parámetros de los módulos
        $ids_actuadores = $_SESSION["parametros_modulo_actuadores"]["ids_actuadores"];
        $ids_grupos_actuadores = $_SESSION["parametros_modulo_actuadores"]["ids_grupos_actuadores"];
        if ($ids_actuadores === NULL)
        {
            $ids_actuadores = array();
        }
        if ($ids_grupos_actuadores === NULL)
        {
            $ids_grupos_actuadores = array();
        }

        // Identificadores de actuadores
        $mostrar_todos_actuadores_usuario_actual_actuadores = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI));
        if ($mostrar_todos_actuadores_usuario_actual_actuadores == true)
        {
            $ids_actuadores = dame_ids_actuadores();
            return ($ids_actuadores);
        }

        // Se actualizan los identificadores de actuadores con los actuadores de los grupos del usuario
        if ($incluir_actuadores_grupos == true)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores);
            $consulta_actuadores_grupos = "
                SELECT id
                FROM actuadores
                WHERE
                    grupo IN (".$cadena_ids_grupos_actuadores_consulta.")";
            $res_actuadores_grupos = $bd_red->ejecuta_consulta($consulta_actuadores_grupos);
            if ($res_actuadores_grupos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_actuadores_grupos."'");
            }
            while ($fila_actuador_grupo = $res_actuadores_grupos->dame_siguiente_fila())
            {
                if (in_array($fila_actuador_grupo["id"], $ids_actuadores) == false)
                {
                    array_push($ids_actuadores, $fila_actuador_grupo["id"]);
                }
            }
        }

        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los actuadores visibles para el usuario actual (según los permisos del módulo Localizaciones)
    function dame_ids_actuadores_usuario_actual_localizaciones($id_localizacion_actual)
    {
        // Se recuperan los identificadores de las localizaciones visibles por el usuario (según la localización actual)
        $ids_localizaciones = array();
        switch ($id_localizacion_actual)
        {
            case ID_NINGUNO:
            {
                $permiso_todos_actuadores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI);
                if ($permiso_todos_actuadores == true)
                {
                    array_push($ids_localizaciones, ID_NINGUNO);
                }
                break;
            }
            case ID_TODOS:
            {
                $ids_localizaciones = dame_ids_localizaciones_usuario_actual(true);
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                $ids_actuadores = NULL;
                foreach ($ids_localizaciones_seleccionadas as $id_localizacion_seleccionada)
                {
                    $ids_actuadores_localizacion_seleccionada = dame_ids_actuadores_usuario_actual_localizaciones($id_localizacion_seleccionada);
                    if ($ids_actuadores === NULL)
                    {
                        $ids_actuadores = $ids_actuadores_localizacion_seleccionada;
                    }
                    else
                    {
                        switch ($id_localizacion_actual)
                        {
                            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                            {
                                $ids_actuadores = array_intersect($ids_actuadores, $ids_actuadores_localizacion_seleccionada);
                                break;
                            }
                            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                            {
                                $ids_actuadores_con_duplicados = array_merge($ids_actuadores, $ids_actuadores_localizacion_seleccionada);
                                $ids_actuadores = array_unique($ids_actuadores_con_duplicados);
                                break;
                            }
                        }
                    }
                }
                return ($ids_actuadores);
            }
            default:
            {
                $ids_localizaciones = dame_ids_localizaciones_descendientes(array($id_localizacion_actual));
                array_push($ids_localizaciones, $id_localizacion_actual);
                break;
            }
        }

        // Se recuperan los identificadores de los actuadores visibles en las localizaciones seleccionadas
        $ids_actuadores = dame_ids_nodos_visibles_localizaciones($ids_localizaciones, TIPO_NODO_ACTUADOR);
        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores visibles para el usuario actual
    function dame_ids_grupos_actuadores_usuario_actual($incluir_grupos_actuadores)
    {
        // Se recuperan los identificadores de los grupos de actuadores correspondientes
        if (!isset($_SESSION["usuario_interno"]))
        {
            if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
            {
                $ids_grupos_actuadores = dame_ids_grupos_actuadores_usuario_actual_actuadores($incluir_grupos_actuadores);
            }
            else
            {
                $ids_grupos_actuadores = dame_ids_grupos_actuadores_usuario_actual_localizaciones($_SESSION["id_localizacion"]);
            }
        }
        else
        {
            // Si es usuario interno, se devuelven todos los grupos de actuadores visibles por actuadores y por localizaciones
            $ids_grupos_actuadores = dame_todos_ids_grupos_actuadores_usuario_actual();
        }
        return ($ids_grupos_actuadores);
    }


    // Devuelve todos los identificadores de grupos de actuadores del usuario actual
    function dame_todos_ids_grupos_actuadores_usuario_actual()
    {
        if (!isset($GLOBALS['todos_ids_grupos_actuadores_usuario_actual']))
        {
            $ids_grupos_actuadores_actuadores = dame_ids_grupos_actuadores_usuario_actual_actuadores(true);
            $ids_grupos_actuadores_localizaciones = dame_ids_grupos_actuadores_usuario_actual_localizaciones(ID_TODOS);
            $ids_grupos_actuadores = array_unique(array_merge($ids_grupos_actuadores_actuadores, $ids_grupos_actuadores_localizaciones));
            $GLOBALS['todos_ids_grupos_actuadores_usuario_actual'] = $ids_grupos_actuadores;
        }
        else
        {
            $ids_grupos_actuadores = $GLOBALS['todos_ids_grupos_actuadores_usuario_actual'];
        }
        return ($ids_grupos_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores visibles para el usuario actual (según los permisos del módulo Actuadores)
    function dame_ids_grupos_actuadores_usuario_actual_actuadores($incluir_grupos_actuadores)
    {
        $ids_actuadores = $_SESSION["parametros_modulo_actuadores"]["ids_actuadores"];
        $ids_grupos_actuadores = $_SESSION["parametros_modulo_actuadores"]["ids_grupos_actuadores"];

        // Identificadores de grupos de actuadores
        $mostrar_todos_actuadores_usuario_actual_actuadores = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI));
        if ($mostrar_todos_actuadores_usuario_actual_actuadores == true)
        {
            $ids_grupos_actuadores = dame_ids_grupos_actuadores();
            return ($ids_grupos_actuadores);
        }

        // Se actualizan los identificadores de grupos de actuadores con los grupos a los que pertenecen los actuadores del usuario
        if ($incluir_grupos_actuadores == true)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores);
            $consulta_grupos_actuadores = "
                SELECT
                    grupos_actuadores.id AS id
                FROM actuadores, grupos_actuadores
                WHERE
                    (actuadores.id IN (".$cadena_ids_actuadores_consulta."))
                    AND (actuadores.grupo = grupos_actuadores.id)";
            $res_grupos_actuadores = $bd_red->ejecuta_consulta($consulta_grupos_actuadores);
            if ($res_grupos_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_grupos_actuadores."'");
            }
            while ($fila_grupo_actuador = $res_grupos_actuadores->dame_siguiente_fila())
            {
                if (in_array($fila_grupo_actuador["id"], $ids_grupos_actuadores) == false)
                {
                    array_push($ids_grupos_actuadores, $fila_grupo_actuador["id"]);
                }
            }
        }

        return ($ids_grupos_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores visibles para el usuario actual (según los permisos del módulo Localizaciones)
    function dame_ids_grupos_actuadores_usuario_actual_localizaciones($id_localizacion_actual)
    {
        // Se recuperan los identificadores de las localizaciones visibles por el usuario (según la localización actual)
        $ids_localizaciones = array();
        switch ($id_localizacion_actual)
        {
            case ID_NINGUNO:
            {
                $permiso_todos_actuadores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) || ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI);
                if ($permiso_todos_actuadores == true)
                {
                    array_push($ids_localizaciones, ID_NINGUNO);
                }
                break;
            }
            case ID_TODOS:
            {
                $ids_localizaciones = dame_ids_localizaciones_usuario_actual(true);
                break;
            }
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                $ids_grupos_actuadores = NULL;
                foreach ($ids_localizaciones_seleccionadas as $id_localizacion_seleccionada)
                {
                    $ids_grupos_actuadores_localizacion_seleccionada = dame_ids_grupos_actuadores_usuario_actual_localizaciones($id_localizacion_seleccionada);
                    if ($ids_grupos_actuadores === NULL)
                    {
                        $ids_grupos_actuadores = $ids_grupos_actuadores_localizacion_seleccionada;
                    }
                    else
                    {
                        switch ($id_localizacion_actual)
                        {
                            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                            {
                                $ids_grupos_actuadores = array_intersect($ids_grupos_actuadores, $ids_grupos_actuadores_localizacion_seleccionada);
                                break;
                            }
                            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                            {
                                $ids_grupos_actuadores_con_duplicados = array_merge($ids_grupos_actuadores, $ids_grupos_actuadores_localizacion_seleccionada);
                                $ids_grupos_actuadores = array_unique($ids_grupos_actuadores_con_duplicados);
                                break;
                            }
                        }
                    }
                }
                return ($ids_grupos_actuadores);
            }
            default:
            {
                $ids_localizaciones = dame_ids_localizaciones_descendientes(array($id_localizacion_actual));
                array_push($ids_localizaciones, $id_localizacion_actual);
                break;
            }
        }

        // Se recuperan los identificadores de los grupos de actuadores
        $ids_grupos_actuadores = dame_ids_grupos_nodos_visibles_localizaciones($ids_localizaciones, TIPO_NODO_ACTUADOR);
        return ($ids_grupos_actuadores);
    }


    //
    // Funciones de tipos y clases de actuador
    //


    // Devuelve los tipos de actuador visibles para el usuario actual
    function dame_tipos_actuador_usuario_actual()
    {
        // Si es usuario superadministrador se devuelven todos los tipos
        if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            return (NodoActuador::dame_tipos_actuador());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        $tipos_actuador_usuario = array();

        // Tipos de los actuadores
        $consulta_tipos_actuadores = "
            SELECT
                DISTINCT(tipo)
            FROM actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_tipos_actuadores .= "
                AND ".dame_condicion_consulta_actuadores_usuario_actual(true);
        }
        $res_tipos_actuadores = $bd_red->ejecuta_consulta($consulta_tipos_actuadores);
        if ($res_tipos_actuadores == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_tipos_actuadores."'");
		}
		while ($fila_tipo_actuador = $res_tipos_actuadores->dame_siguiente_fila())
		{
            $tipo_actuador = $fila_tipo_actuador["tipo"];
            array_push($tipos_actuador_usuario, $tipo_actuador);
        }

        // Se ordenan los tipos de actuador
        $tipos_actuador_usuario_ordenados = array();
        $tipos_actuador = NodoActuador::dame_tipos_actuador();
        foreach ($tipos_actuador as $tipo_actuador)
        {
            if (in_array($tipo_actuador, $tipos_actuador_usuario) == true)
            {
                array_push($tipos_actuador_usuario_ordenados, $tipo_actuador);
            }
        }
        return ($tipos_actuador_usuario_ordenados);
    }


    // Devuelve las clases de actuador visibles para el usuario actual
    function dame_clases_actuador_usuario_actual($incluir_clases_grupos)
    {
        // Si es usuario superadministrador se devuelven todas las clases
        if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            return (NodoActuador::dame_clases_actuador());
        }

        $bd_red = BaseDatosRed::dame_base_datos();

        $clases_actuador_usuario = array();

        // Clases de los actuadores
        $consulta_clases_actuadores = "
            SELECT
                DISTINCT(clase)
            FROM actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_clases_actuadores .= "
                AND ".dame_condicion_consulta_actuadores_usuario_actual(true);
        }
        $res_clases_actuadores = $bd_red->ejecuta_consulta($consulta_clases_actuadores);
        if ($res_clases_actuadores == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_clases_actuadores."'");
		}
		while ($fila_clase_actuador = $res_clases_actuadores->dame_siguiente_fila())
		{
            $clase_actuador = $fila_clase_actuador["clase"];
            array_push($clases_actuador_usuario, $clase_actuador);
        }

        // Clases de los grupos de actuadores
        if ($incluir_clases_grupos == true)
        {
            $consulta_clases_grupos = "
                SELECT
                    DISTINCT(clase)
                FROM grupos_actuadores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($mostrar_todos_actuadores == false)
            {
                $consulta_clases_grupos .= "
                    AND ".dame_condicion_consulta_grupos_actuadores_usuario_actual(true);
            }
            $res_clases_grupos = $bd_red->ejecuta_consulta($consulta_clases_grupos);
            if ($res_clases_grupos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_clases_grupos."'");
            }
            while ($fila_clase_grupo = $res_clases_grupos->dame_siguiente_fila())
            {
                $clase_grupo = $fila_clase_grupo["clase"];
                if (in_array($clase_grupo, $clases_actuador_usuario) == false)
                {
                    array_push($clases_actuador_usuario, $clase_grupo);
                }
            }
        }

        // Se ordenan las clases de actuador
        $clases_actuador_usuario_ordenadas = array();
        $clases_actuador = NodoActuador::dame_clases_actuador();
        foreach ($clases_actuador as $clase_actuador)
        {
            if (in_array($clase_actuador, $clases_actuador_usuario) == true)
            {
                array_push($clases_actuador_usuario_ordenadas, $clase_actuador);
            }
        }
        return ($clases_actuador_usuario_ordenadas);
    }


    //
    // Funciones de comprobaciones de configuraciones de actuadores
    //


    // Devuelve un mensaje de aviso de comprobación de ubicación de actuador hardware
    function dame_aviso_comprobacion_ubicacion_actuador_hardware($id_actuador, $cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $id_axon = $parametros_tipo[0];
        $clase_interfaz = $parametros_tipo[1];
        $ubicacion_interfaz = $parametros_tipo[2];

        $aviso = "";
        switch ($clase_interfaz)
        {
            case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
            {
                $consulta_actuadores = "
                    SELECT
                        nombre,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS ubicacion_interfaz
                    FROM actuadores
                    WHERE
                        (id <> '".$bd_red->_($id_actuador)."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_interfaz)."')";
                $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
                if ($res_actuadores == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
                }

                while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
                {
                    $ubicacion_interfaz_bucle = $fila_actuador["ubicacion_interfaz"];
                    if ($ubicacion_interfaz_bucle != $ubicacion_interfaz)
                    {
                        $aviso = $idiomas->_("existe un interfaz de actuador de la misma clase con una ubicación diferente en el mismo axón").": ".$fila_actuador["nombre"];
                        break;
                    }
                }
                break;
            }
        }

        // Si no hay aviso
        if ($aviso == "")
        {
            switch ($clase_interfaz)
            {
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
                {
                    $clase_interfaz_sensor = CLASE_INTERFAZ_SENSOR_MODBUS_SERIE;
                    $consulta_sensores = "
                        SELECT
                            nombre,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS ubicacion_interfaz
                        FROM sensores
                        WHERE
                            (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_axon)."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_interfaz_sensor)."')";
                    $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                    if ($res_sensores == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_sensores."'");
                    }

                    while ($fila_sensor = $res_sensores->dame_siguiente_fila())
                    {
                        $ubicacion_interfaz_bucle = $fila_sensor["ubicacion_interfaz"];
                        if ($ubicacion_interfaz_bucle != $ubicacion_interfaz)
                        {
                            $aviso = $idiomas->_("existe un interfaz de sensor de la misma clase con una ubicación diferente en el mismo axón").": ".$fila_sensor["nombre"];
                            break;
                        }
                    }
                }
            }
        }

        return ($aviso);
    }


    //
    // Funciones de listas de actuadores
    //


    // Devuelve la lista de actuadores
    function dame_lista_actuadores($clase_actuador, $ids_actuadores_seleccionados, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_actuadores = "
            SELECT
                id,
                nombre
            FROM actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_actuadores .= "
                AND (clase = '".$bd_red->_($clase_actuador)."')";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_actuadores = true;
        }
        else
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        }
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_actuadores .= "
                AND (".dame_condicion_consulta_actuadores_usuario_actual(true);

            // Nota: En algunas lista el actuador seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese actuador en la lista)
            // (p.e. un actuador en un widget de una localización diferente a la actual)
            $cadena_ids_actuadores_seleccionados_consulta = dame_cadena_ids_consulta($ids_actuadores_seleccionados);
            $consulta_actuadores .= "
                OR (id IN (".$cadena_ids_actuadores_seleccionados_consulta.")))";
        }
        $consulta_actuadores .= "
            ORDER BY nombre ASC";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }

        $lista_actuadores = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO)
        {
            $lista_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
        {
            $lista_actuadores .= "<option value='".$fila_actuador['id']."'";
			if (in_array($fila_actuador['id'], $ids_actuadores_seleccionados) == true)
			{
				$lista_actuadores .= " selected";
			}
			$lista_actuadores .= ">".htmlspecialchars($fila_actuador['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_actuadores);
    }


    // Devuelve la lista de actuadores con los identificadores especificados
    function dame_lista_actuadores_ids(
        $clase_actuador,
        $ids_actuadores,
        $ids_actuadores_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_actuadores = "
            SELECT
                id,
                nombre
            FROM actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_actuadores .= "
                AND (clase = '".$clase_actuador."')";
        }
        $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores);
        $consulta_actuadores .= "
                AND (id IN (".$cadena_ids_actuadores_consulta."))";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_actuadores = true;
        }
        else
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        }
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_actuadores .= "
                AND (".dame_condicion_consulta_actuadores_usuario_actual(true);

            // Nota: En algunas listas el actuador seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese actuador en la lista)
            // (p.e. un actuador en un widget de una localización diferente a la actual)
            $cadena_ids_actuadores_seleccionados_consulta = dame_cadena_ids_consulta($ids_actuadores_seleccionados);
            $consulta_actuadores .= " OR (id IN (".$cadena_ids_actuadores_seleccionados_consulta.")))";
        }
        $consulta_actuadores .= "
            ORDER BY nombre ASC";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }

        $lista_actuadores = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_actuadores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
        {
            $lista_actuadores .= "<option value='".$fila_actuador['id']."'";
			if (in_array($fila_actuador['id'], $ids_actuadores_seleccionados) == true)
			{
				$lista_actuadores .= " selected";
			}
			$lista_actuadores .= ">".htmlspecialchars($fila_actuador['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_actuadores);
    }


    // Devuelve la lista de grupos de actuadores
    function dame_lista_grupos_actuadores($clase_actuador, $ids_grupos_seleccionados, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos = "
            SELECT
                id,
                nombre
            FROM grupos_actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_grupos .= "
                AND (clase = '".$bd_red->_($clase_actuador)."')";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_actuadores = true;
        }
        else
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        }
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_grupos .= "
                AND (".dame_condicion_consulta_grupos_actuadores_usuario_actual(false);

            // Nota: En algunas listas el grupo seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese actuador en la lista)
            // (p.e. un actuador en un widget de una localización diferente a la actual)
            $cadena_ids_grupos_seleccionados = dame_cadena_ids_consulta($ids_grupos_seleccionados);
            $consulta_grupos .= "
                OR (id IN (".$cadena_ids_grupos_seleccionados.")))";
        }
        $consulta_grupos .= "
            ORDER BY nombre ASC";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }

        $lista_grupos = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO)
        {
            $lista_grupos .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_grupos .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_grupo = $res_grupos->dame_siguiente_fila())
        {
            $lista_grupos .= "<option value='".$fila_grupo['id']."'";
            if (in_array($fila_grupo['id'], $ids_grupos_seleccionados) == true)
			{
                $lista_grupos .= " selected";
			}
			$lista_grupos .= ">".htmlspecialchars($fila_grupo['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos);
    }


    // Devuelve la lista de grupos de actuadores con los identificadores especificados
    function dame_lista_grupos_actuadores_ids(
        $clase_actuador,
        $ids_grupos_actuadores,
        $ids_grupos_actuadores_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos_actuadores = "
            SELECT
                id,
                nombre
            FROM grupos_actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_grupos_actuadores .= "
                AND (clase = '".$clase_actuador."')";
        }
        $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores);
        $consulta_grupos_actuadores .= "
                AND (id IN (".$cadena_ids_grupos_actuadores_consulta."))";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS)
        {
            $mostrar_todos_actuadores = true;
        }
        else
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        }
        if ($mostrar_todos_actuadores == false)
        {
            $consulta_grupos_actuadores .= "
                AND (".dame_condicion_consulta_grupos_actuadores_usuario_actual(true);

            // Nota: En algunas listas el actuador seleccionado puede no estar visible en el usuario actual
            // (se muestra también ese actuador en la lista)
            // (p.e. un actuador en un widget de una localización diferente a la actual)
            $cadena_ids_grupos_actuadores_seleccionados_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_seleccionados);
            $consulta_grupos_actuadores .= " OR (id IN (".$cadena_ids_grupos_actuadores_seleccionados_consulta.")))";
        }
        $consulta_grupos_actuadores .= "
            ORDER BY nombre ASC";
        $res_grupos_actuadores = $bd_red->ejecuta_consulta($consulta_grupos_actuadores);
        if ($res_grupos_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos_actuadores."'");
        }

        $lista_grupos_actuadores = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_grupos_actuadores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO))
        {
            $lista_grupos_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_grupo_actuadores = $res_grupos_actuadores->dame_siguiente_fila())
        {
            $lista_grupos_actuadores .= "<option value='".$fila_grupo_actuadores['id']."'";
			if (in_array($fila_grupo_actuadores['id'], $ids_grupos_actuadores_seleccionados) == true)
			{
				$lista_grupos_actuadores .= " selected";
			}
			$lista_grupos_actuadores .= ">".htmlspecialchars($fila_grupo_actuadores['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos_actuadores);
    }


    // Crea una lista desplegable para la selección de un tipo de actuador
    function dame_control_lista_tipos_actuador($id_controles, $opciones_extra, $mostrar_etiqueta, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_tipos = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_tipos .= "<div id='etiqueta_tipo_actuador_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_tipos .= "<select id='tipo_actuador_".$id_controles."' class='filtro-desplegable'>";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
        {
            $control_lista_tipos .= "<option value=".TIPO_TODOS.">".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
        {
            $control_lista_tipos .= "<option value=".TIPO_NINGUNO.">".$idiomas->_("Ninguno")."</option>";
        }
        $tipos_actuador = dame_tipos_actuador_usuario_actual();
        foreach ($tipos_actuador as $tipo_actuador)
        {
            $nombre_tipo_actuador = NodoActuador::dame_descripcion_tipo_actuador($tipo_actuador);
            $control_lista_tipos .= "<option value='".$tipo_actuador."'>".htmlspecialchars($nombre_tipo_actuador, ENT_QUOTES)."</option>";
        }
        $control_lista_tipos .= "
            </select>";

        return ($control_lista_tipos);
    }


    // Crea una lista desplegable para la selección de una clase de actuador
    function dame_control_lista_clases_actuador($id_controles, $opciones_extra, $mostrar_etiqueta, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_clases = "";
        if ($mostrar_etiqueta == true)
        {
            $control_lista_clases .= "<div id='etiqueta_clase_actuador_".$id_controles."'>".$etiqueta.": "."</div>";
        }
        $control_lista_clases .= "<select id='clase_actuador_".$id_controles."' class='filtro-desplegable'>";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS:
            {
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA:
            {
                $control_lista_clases .= "<option value=".CLASE_TODAS.">".$idiomas->_("Todas")."</option>";
                $control_lista_clases .= "<option value=".CLASE_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
                break;
            }
        }
        $clases_actuador = dame_clases_actuador_usuario_actual(true);
        foreach ($clases_actuador as $clase_actuador)
        {
            $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
            $control_lista_clases .= "<option value='".$clase_actuador."'>".htmlspecialchars($nombre_clase_actuador, ENT_QUOTES)."</option>";
        }
        $control_lista_clases .= "
            </select>";

        return ($control_lista_clases);
    }


    // Crea una lista desplegable para la selección de un grupo de actuadores
    function dame_control_lista_grupos_actuadores($id_controles, $etiqueta)
    {
        $control_lista_grupos = "";
        $control_lista_grupos .= "<div id='etiqueta_grupo_actuadores_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_grupos .= "
            <select id='id_grupo_actuadores_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_grupos .= dame_lista_grupos_actuadores(CLASE_TODAS, array(), OPCIONES_EXTRA_LISTA_NODOS_TODOS_NINGUNO);
        $control_lista_grupos .= "</select>";

        return ($control_lista_grupos);
    }


    // Crea una lista desplegable para la selección de un estado de actuador
    function dame_control_lista_estados_actuador($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_estados .= "<div id='etiqueta_estado_actuador_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_estados .= "<select id='estado_actuador_".$id_controles."' class='filtro-desplegable'>";

        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_TODOS.">".$idiomas->_("Todos")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_OK.">".$idiomas->_("Ok")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_ERROR.">".$idiomas->_("Error")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_ERROR_EJECUCION_ACCION.">".$idiomas->_("Error en ejecución de acción")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_NO_CONECTADO.">".$idiomas->_("No conectado")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_EN_EJECUCION.">".$idiomas->_("En ejecución")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_ACTUADOR_SIN_ACCION.">".$idiomas->_("Sin acción")."</option>";

        $control_lista_estados .= "</select>";

        return ($control_lista_estados);
    }


    //
    // Funciones de obtención de información de actuadores
    //


    // Devuelve la fila del actuador
    function dame_fila_actuador($id_actuador)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_actuador = "
            SELECT *
            FROM actuadores
            WHERE
                id = '".$bd_red->_($id_actuador)."'";
        $res_actuador = $bd_red->ejecuta_consulta($consulta_actuador);
        if (($res_actuador == false) || ($res_actuador->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuador."'");
        }
        $fila_actuador = $res_actuador->dame_siguiente_fila();
        return ($fila_actuador);
    }


    // Devuelve el nombre de un actuador
    function dame_nombre_actuador($id_actuador)
    {
        $ids_actuadores = array($id_actuador);
        $nombres_actuadores = dame_nombres_actuadores($ids_actuadores);
        $nombre_actuador = $nombres_actuadores[0];
        return ($nombre_actuador);
    }


    // Devuelve los nombres de los actuadores
    function dame_nombres_actuadores($ids_actuadores)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_actuadores = array();
        foreach ($ids_actuadores AS $id_actuador)
        {
            switch ($id_actuador)
            {
                case ID_NINGUNO:
                {
                    $nombre_actuador = $idiomas->_("Ninguno");
                    break;
                }
                case ID_TODOS:
                {
                    $nombre_actuador = $idiomas->_("Todos");
                    break;
                }
                default:
                {
                    $consulta_actuador = "
                        SELECT nombre
                        FROM actuadores
                        WHERE
                            id = '".$bd_red->_($id_actuador)."'";
                    $res_actuador = $bd_red->ejecuta_consulta($consulta_actuador);
                    if (($res_actuador == false) || ($res_actuador->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuador."'");
                    }
                    $fila_actuador = $res_actuador->dame_siguiente_fila();
                    $nombre_actuador = $fila_actuador["nombre"];
                    break;
                }
            }
            array_push($nombres_actuadores, $nombre_actuador);
        }
        return ($nombres_actuadores);
    }


    // Devuelve la fila del grupo de actuadores
    function dame_fila_grupo_actuadores($id_grupo_actuadores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupo_actuadores = "
            SELECT *
            FROM grupos_actuadores
            WHERE
                id = '".$bd_red->_($id_grupo_actuadores)."'";
        $res_grupo_actuadores = $bd_red->ejecuta_consulta($consulta_grupo_actuadores);
        if (($res_grupo_actuadores == false) || ($res_grupo_actuadores->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo_actuadores."'");
        }
        $fila_grupo_actuadores = $res_grupo_actuadores->dame_siguiente_fila();
        return ($fila_grupo_actuadores);
    }


    // Devuelve el nombre de un grupo de actuadores
    function dame_nombre_grupo_actuadores($id_grupo_actuadores)
    {
        $ids_grupos_actuadores = array($id_grupo_actuadores);
        $nombres_grupos_actuadores = dame_nombres_grupos_actuadores($ids_grupos_actuadores);
        $nombre_grupo_actuadores = $nombres_grupos_actuadores[0];
        return ($nombre_grupo_actuadores);
    }


    // Devuelve los nombres de los grupos de actuadores
    function dame_nombres_grupos_actuadores($ids_grupos_actuadores)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_grupos_actuadores = array();
        foreach ($ids_grupos_actuadores AS $id_grupo_actuadores)
        {
            switch ($id_grupo_actuadores)
            {
                case ID_NINGUNO:
                {
                    $nombre_grupo_actuadores = $idiomas->_("Ninguno");
                    break;
                }
                case ID_TODOS:
                {
                    $nombre_grupo_actuadores = $idiomas->_("Todos");
                    break;
                }
                default:
                {
                    $consulta_grupo_actuadores = "
                        SELECT nombre
                        FROM grupos_actuadores
                        WHERE
                            id = '".$bd_red->_($id_grupo_actuadores)."'";
                    $res_grupo_actuadores = $bd_red->ejecuta_consulta($consulta_grupo_actuadores);
                    if (($res_grupo_actuadores == false) || ($res_grupo_actuadores->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo_actuadores."'");
                    }
                    $fila_grupo_actuadores = $res_grupo_actuadores->dame_siguiente_fila();
                    $nombre_grupo_actuadores = $fila_grupo_actuadores["nombre"];
                    break;
                }
            }
            array_push($nombres_grupos_actuadores, $nombre_grupo_actuadores);
        }
        return ($nombres_grupos_actuadores);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve si un actuador está en la lista de actuadores o pertenece a algun grupo de los especificados
    function dame_actuador_actuadores_grupos($id_actuador, $ids_actuadores, $ids_grupos_actuadores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se comprueba:
        // 1. Si el actuador está en la lista de actuadores
        // 2. Si el grupo del actuadores es alguno de los grupos de actuadores
        if (in_array($id_actuador, $ids_actuadores) == true)
        {
            return (true);
        }
        if (count($ids_grupos_actuadores) == 0)
        {
            return (false);
        }

        $consulta_grupo_actuador = "
            SELECT grupo
            FROM actuadores
            WHERE
                id = '".$bd_red->_($id_actuador)."'";
        $res_grupo_actuador = $bd_red->ejecuta_consulta($consulta_grupo_actuador);
        if (($res_grupo_actuador == false) || ($res_grupo_actuador->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos");
        }
        $fila_grupo_actuador = $res_grupo_actuador->dame_siguiente_fila();
        $id_grupo = $fila_grupo_actuador["grupo"];
        if (in_array($id_grupo, $ids_grupos_actuadores) == true)
        {
            return (true);
        }
        else
        {
            return (false);
        }
    }


    //
    // Funciones de acciones de usuario
    //


    // Convierte los ids de parámetros de tipo de actuador a los nombres correspondientes para acciones de usuario
    function sustituye_ids_nombres_parametros_tipo_actuador_accion_usuario($tipo, &$parametros_tipo)
    {
        // Parámetros específicos de tipo de actuador
        switch ($tipo)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                $id_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];
                $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ];
                $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ];
                $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_OPCIONES_INTERFAZ];

                $nombre_axon = dame_nombre_axon($id_axon);
                sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_axon);

                $parametros_tipo = array(
                    $nombre_axon,
                    $clase_interfaz,
                    $ubicacion_interfaz,
                    $opciones_interfaz);
                break;
            }
        }
    }


    //
    // Funciones de descripciones de errores (en obtención de valores)
    //


    // Devuelve la descripción del error de ejecución de acción de actuador e-mail
    function dame_descripcion_error_ejecucion_accion_email($error)
    {
        switch ($error)
        {
            case ERROR_EJECUCION_ACCION_EMAIL_ERROR_CREACION_CLIENTE_SMTP:
            {
                $descripcion_error = "Error al crear el cliente SMTP";
                break;
            }
            case ERROR_EJECUCION_ACCION_EMAIL_ERROR_AUTENTICACION_CLIENTE_SMTP:
            {
                $descripcion_error = "Error en autenticación SMTP";
                break;
            }
            case ERROR_EJECUCION_ACCION_EMAIL_ERROR_ENVIO_MENSAJE_SERVIDOR_SMTP:
            {
                $descripcion_error = "Error al enviar el mensaje SMTP";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }


    // Devuelve la descripción del error de ejecución de acción de actuador Modbus IP
    function dame_descripcion_error_ejecucion_accion_modbus_ip($error)
    {
        switch ($error)
        {
            case ERROR_EJECUCION_ACCION_MODBUS_IP_ERROR_APERTURA_SOCKET:
            {
                $descripcion_error = "Error en la apertura del socket";
                break;
            }
            case ERROR_EJECUCION_ACCION_MODBUS_IP_ERROR_ESCRITURA_VALORES:
            {
                $descripcion_error = "Error en la escritura de valores";
                break;
            }
            default:
            {
                $descripcion_error = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_error));
    }
?>
