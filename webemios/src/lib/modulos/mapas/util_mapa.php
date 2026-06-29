<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    //
    // Información de mapa
    //


    // Devuelve la información del mapa según el origen
    function dame_info_mapa($origen_mapa, $parametros_origen_mapa)
    {
        // Origen del mapa 'final'
        $resultado_origen_mapa = dame_origen_mapa_final($origen_mapa, $parametros_origen_mapa);
        $origen_mapa_final = $resultado_origen_mapa["origen_mapa_final"];
        $id_origen_mapa_final = $resultado_origen_mapa["id_origen_mapa_final"];

        // Origen del mapa
        switch ($origen_mapa_final)
        {
            case ORIGEN_MAPA_RED:
            {
                $id_red = $id_origen_mapa_final;
                $fila_red = dame_fila_red($id_red);

                // Opciones del mapa
                $tipo_mapa = $fila_red["tipo_mapa"];
                $nombre_mapa = $fila_red["nombre_mapa"];
                $factor_reduccion_imagen_mapa_local = $fila_red["factor_reduccion_imagen_mapa_local"];
                $latitud_mapa_defecto = $fila_red["latitud_mapa_defecto"];
                $longitud_mapa_defecto = $fila_red["longitud_mapa_defecto"];
                $zoom_mapa_defecto = $fila_red["zoom_mapa_defecto"];

                // Origen de la imagen del mapa
                if ($tipo_mapa == TIPO_MAPA_LOCAL)
                {
                    $origen_imagen = ORIGEN_IMAGEN_RED_MAPA;
                    $id_origen_imagen = $id_red;
                }
                break;
            }
            case ORIGEN_MAPA_LOCALIZACION:
            {
                $id_localizacion = $id_origen_mapa_final;
                $fila_localizacion = dame_fila_localizacion($id_localizacion);

                // Opciones del mapa
                $tipo_mapa = $fila_localizacion["tipo_mapa"];
                $nombre_mapa = $fila_localizacion["nombre_mapa"];
                $factor_reduccion_imagen_mapa_local = $fila_localizacion["factor_reduccion_imagen_mapa_local"];
                $latitud_mapa_defecto = $fila_localizacion["latitud_mapa_defecto"];
                $longitud_mapa_defecto = $fila_localizacion["longitud_mapa_defecto"];
                $zoom_mapa_defecto = $fila_localizacion["zoom_mapa_defecto"];

                // Origen de la imagen del mapa
                if ($tipo_mapa == TIPO_MAPA_LOCAL)
                {
                    $origen_imagen = ORIGEN_IMAGEN_LOCALIZACION_MAPA;
                    $id_origen_imagen = $id_localizacion;
                }
                break;
            }
            case ORIGEN_MAPA_INSTALACION:
            {
                $id_instalacion = $id_origen_mapa_final;
                $fila_instalacion = dame_fila_instalacion($id_instalacion);

                // Opciones del mapa
                $tipo_mapa = TIPO_MAPA_LOCAL;
                $nombre_mapa = $fila_instalacion["nombre_imagen"];
                $factor_reduccion_imagen_mapa_local = $fila_instalacion["factor_reduccion_imagen"];
                $latitud_mapa_defecto = $fila_instalacion["latitud_imagen_defecto"];
                $longitud_mapa_defecto = $fila_instalacion["longitud_imagen_defecto"];
                $zoom_mapa_defecto = $fila_instalacion["zoom_imagen_defecto"];

                // Origen de la imagen del mapa
                if ($tipo_mapa == TIPO_MAPA_LOCAL)
                {
                    $origen_imagen = ORIGEN_IMAGEN_INSTALACION_IMAGEN;
                    $id_origen_imagen = $id_instalacion;
                }
                break;
            }
            default:
            {
                throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
            }
        }

        $info_mapa = array(
            "tipo_mapa" => $tipo_mapa,
            "nombre_mapa" => $nombre_mapa,
            "factor_reduccion_imagen_mapa_local" => $factor_reduccion_imagen_mapa_local,
            "latitud_mapa_defecto" => $latitud_mapa_defecto,
            "longitud_mapa_defecto" => $longitud_mapa_defecto,
            "zoom_mapa_defecto" => $zoom_mapa_defecto,
            "origen_imagen" => $origen_imagen,
            "id_origen_imagen" => $id_origen_imagen);
        return ($info_mapa);
    }


    // Devuelve el origen de mapa 'final' (con el objeto al que pertenece el mapa)
    function dame_origen_mapa_final($origen_mapa, $parametros_origen_mapa)
    {
        // Origen del mapa
        switch ($origen_mapa)
        {
            case ORIGEN_MAPA_RED:
            case ORIGEN_MAPA_LOCALIZACION:
            case ORIGEN_MAPA_RED_LOCALIZACION:
            case ORIGEN_MAPA_INSTALACION:
            {
                $origen_mapa_final = $origen_mapa;
                $id_origen_mapa_final = $parametros_origen_mapa;
                break;
            }
            case ORIGEN_MAPA_POSICION:
            case ORIGEN_MAPA_SECCION:
            {
                $modulo = $parametros_origen_mapa["modulo"];
                switch ($modulo)
                {
                    case MODULO_RED:
                    {
                        $origen_mapa_final = ORIGEN_MAPA_RED;
                        $id_origen_mapa_final = $_SESSION["id_red"];
                        break;
                    }
                    case MODULO_LOCALIZACIONES:
                    {
                        if (array_key_exists("tipo_elemento_mapa", $parametros_origen_mapa) == false)
                        {
                            $tipo_elemento_mapa = TIPO_ELEMENTO_MAPA_LOCALIZACION;
                        }
                        else
                        {
                            $tipo_elemento_mapa = $parametros_origen_mapa["tipo_elemento_mapa"];
                        }
                        switch ($tipo_elemento_mapa)
                        {
                            case TIPO_ELEMENTO_MAPA_LOCALIZACION:
                            {
                                $origen_mapa_final = ORIGEN_MAPA_RED;
                                $id_origen_mapa_final = $_SESSION["id_red"];
                                break;
                            }
                            case TIPO_ELEMENTO_MAPA_INSTALACION:
                            {
                                $origen_mapa_final = ORIGEN_MAPA_RED_LOCALIZACION;
                                $id_origen_mapa_final = $parametros_origen_mapa["id_localizacion"];
                                break;
                            }
                            case TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION:
                            {
                                $origen_mapa_final = ORIGEN_MAPA_INSTALACION;
                                $id_origen_mapa_final = $parametros_origen_mapa["id_instalacion"];
                                break;
                            }
                            default:
                            {
                                throw new Exception("Tipo de elemento de mapa del módulo de localizaciones incorrecto: '".$tipo_elemento_mapa."'");
                            }
                        }
                        break;
                    }
                    case MODULO_SENSORES:
                    case MODULO_ACTUADORES:
                    case MODULO_SMARTMETER:
                    {
                        $origen_mapa_final = ORIGEN_MAPA_RED_LOCALIZACION;
                        $id_origen_mapa_final = $_SESSION["id_localizacion"];
                        break;
                    }
                    default:
                    {
                        throw new Exception("Módulo incorrecto: '".$modulo."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
            }
        }

        // Orígenes del mapa final 'auxiliares'
        switch ($origen_mapa_final)
        {
            case ORIGEN_MAPA_RED_LOCALIZACION:
            {
                switch ($id_origen_mapa_final)
                {
                    case ID_DESACTIVADO:
                    case ID_NINGUNO:
                    case ID_TODOS:
                    {
                        $id_localizacion = ID_NINGUNO;
                        break;
                    }
                    case ID_LOCALIZACIONES_SELECCIONADAS_AND:
                    case ID_LOCALIZACIONES_SELECCIONADAS_OR:
                    {
                        $ids_localizaciones_seleccionadas = $_SESSION["ids_localizaciones_seleccionadas"];
                        if (count($ids_localizaciones_seleccionadas) == 1)
                        {
                            $id_localizacion = $ids_localizaciones_seleccionadas[0];
                        }
                        else
                        {
                            $id_localizacion = ID_NINGUNO;
                        }
                        break;
                    }
                    default:
                    {
                        $id_localizacion = $id_origen_mapa_final;
                        break;
                    }
                }

                // Origen del mapa 'final'
                $origen_mapa_final = ORIGEN_MAPA_RED;
                $id_origen_mapa_final = $_SESSION["id_red"];

                // Localización
                if ($id_localizacion != ID_NINGUNO)
                {
                    $fila_localizacion = dame_fila_localizacion($id_localizacion);
                    switch ($fila_localizacion["mapa_personalizado"])
                    {
                        case VALOR_NO:
                        {
                            break;
                        }
                        case VALOR_SI:
                        {
                            // Origen del mapa 'final'
                            $origen_mapa_final = ORIGEN_MAPA_LOCALIZACION;
                            $id_origen_mapa_final = $id_localizacion;
                            break;
                        }
                    }
                }
                break;
            }
        }

        // Se devuelve el resutlado
        $resultado = array(
            "origen_mapa_final" => $origen_mapa_final,
            "id_origen_mapa_final" => $id_origen_mapa_final);
        return ($resultado);
    }


    //
    // Funciones de capas de mapa
    //


    function dame_capas_mapa($modulo, $parametros_filtro_mapa)
	{
        // Parámetros del filtro del mapa
        $filtro = $parametros_filtro_mapa["filtro"];
        $tipo = $parametros_filtro_mapa["tipo"];
        $clase = $parametros_filtro_mapa["clase"];
        $id_grupo = $parametros_filtro_mapa["id_grupo"];
        $estado = $parametros_filtro_mapa["estado"];
        $id_ratio = $parametros_filtro_mapa["id_ratio"];

        // Se recuperan las capas del módulo correspondiente
        $capas = array();
        switch ($modulo)
        {
            case MODULO_RED:
            {
                $capas = dame_capas_mapa_red($filtro);
                break;
            }
            case MODULO_LOCALIZACIONES:
            {
                $capas = dame_capas_mapa_localizaciones($filtro);
                break;
            }
            case MODULO_SENSORES:
            {
                $_SESSION["id_ratio_sensores"] = $id_ratio;
                $capas = dame_capas_mapa_sensores($filtro, $tipo, $clase, $id_grupo, $estado);
                break;
            }
            case MODULO_ACTUADORES:
            {
                $capas = dame_capas_mapa_actuadores($filtro, $tipo, $clase, $id_grupo, $estado);
                break;
            }
            default:
            {
                throw new Exception("Modulo incorrecto");
            }
        }

        // Sólo se devuelven las capas con elementos
        $capas_elementos = array();
        foreach ($capas as $capa)
        {
            if (count($capa["info_elementos"]) > 0)
            {
                array_push($capas_elementos, $capa);
            }
        }
        return ($capas_elementos);
    }


    function dame_capas_mapa_red($filtro)
	{
        $idiomas = new Idiomas();

        $capas = array();
        $numero_dispositivos = dame_numero_dispositivos();
        $mostrar_capa_dispositivos = (($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR) || ($numero_dispositivos > 0));
        if ($mostrar_capa_dispositivos == true)
        {
            $params = array();
            $params["filtro"] = $filtro;
            $capa_dispositivos = dame_capa_mapa_tipo_nodo(TIPO_NODO_DISPOSITIVO, $params, $idiomas->_("Dispositivos"));
            array_push($capas, $capa_dispositivos);
        }
        return ($capas);
    }


    function dame_capas_mapa_localizaciones($filtro)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los órdenes de las localizaciones
        $consulta_ordenes = "
            SELECT
                DISTINCT(orden) AS orden
            FROM localizaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == false)
        {
            $consulta_ordenes .=
                "AND ".dame_condicion_consulta_localizaciones_usuario_actual();
        }
        $consulta_ordenes .= "
            ORDER BY
                orden DESC";
        $res_ordenes = $bd_red->ejecuta_consulta($consulta_ordenes);
        if ($res_ordenes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ordenes."'");
        }

        $params = array();
        $params["filtro"] = $filtro;

        // Se recuperan las capas de las localizaciones (por órdenes)
        $capas = array();
        while ($fila_orden = $res_ordenes->dame_siguiente_fila())
        {
            $orden = $fila_orden['orden'];
            $params["orden"] = $orden;
            $nombre_capa = $idiomas->_("Nivel").": ".$orden;
            $capa_localizaciones = dame_capa_mapa_localizaciones($params, $nombre_capa);
            array_push($capas, $capa_localizaciones);
        }
        return ($capas);
    }


    function dame_capas_mapa_sensores($filtro, $tipo, $clase, $id_grupo, $estado)
    {
        return (dame_capas_mapa_sensores_actuadores(TIPO_NODO_SENSOR, $filtro, $tipo, $clase, $id_grupo, $estado));
    }


    function dame_capas_mapa_actuadores($filtro, $tipo, $clase, $id_grupo, $estado)
    {
        return (dame_capas_mapa_sensores_actuadores(TIPO_NODO_ACTUADOR, $filtro, $tipo, $clase, $id_grupo, $estado));
    }


    function dame_capas_mapa_sensores_actuadores($tipo_nodo, $filtro, $tipo, $clase, $id_grupo, $estado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Capas a devolver
        $capas = array();

        // Origen de mapa
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $parametros_origen_mapa = array("modulo" => MODULO_SENSORES);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $parametros_origen_mapa = array("modulo" => MODULO_ACTUADORES);
                break;
            }
        }
        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_SECCION, $parametros_origen_mapa);
        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

        // Parámetros para recuperar cada una de las capas
        $params = array();
        $params["filtro"] = $filtro;
        $params["tipo"] = $tipo;
        $params["clase"] = $clase;
        $params["id_grupo"] = $id_grupo;
        $params["estado"] = $estado;
        $params["origen_mapa"] = $origen_mapa;
        $params["id_origen_mapa"] = $id_origen_mapa;
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $params["mostrar_todos_sensores_actuadores"] = dame_mostrar_todos_sensores();
                if ($params["mostrar_todos_sensores_actuadores"] == false)
                {
                    $params["condicion_consulta_sensores_actuadores_usuario_actual"] = dame_condicion_consulta_sensores_usuario_actual(true);
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $params["mostrar_todos_sensores_actuadores"] = dame_mostrar_todos_actuadores();
                if ($params["mostrar_todos_sensores_actuadores"] == false)
                {
                    $params["condicion_consulta_sensores_actuadores_usuario_actual"] = dame_condicion_consulta_actuadores_usuario_actual(true);
                }
                break;
            }
        }

        // Si las localizaciones están desactivadas, se devuelven las capas por grupos, si no por localizaciones
        if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
        {
            $nombre_parametro_grupo_localizacion = "id_grupo";
            $nombre_capa_sin_grupo_localizacion = $idiomas->_("Sin grupo");
            $nombre_capa_grupos_localizaciones = $idiomas->_("Con grupo");
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $tabla_grupos_localizaciones = "grupos_sensores";
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $tabla_grupos_localizaciones = "grupos_actuadores";
                    break;
                }
            }
        }
        else
        {
            $nombre_parametro_grupo_localizacion = "id_localizacion";
            $nombre_capa_sin_grupo_localizacion = $idiomas->_("Sin localización");
            $nombre_capa_grupos_localizaciones = $idiomas->_("Con localización");
            $tabla_grupos_localizaciones = "localizaciones";
        }

        // Sin grupo/localización
        $params[$nombre_parametro_grupo_localizacion] = ID_NINGUNO;
        $capa_sin_grupo_localizacion = dame_capa_mapa_tipo_nodo($tipo_nodo, $params, $nombre_capa_sin_grupo_localizacion);
        if (count($capa_sin_grupo_localizacion["info_elementos"]) > 0)
        {
            array_push($capas, $capa_sin_grupo_localizacion);
        }

        // Nota: Se recorren todos los grupos/localizaciones y solo se mostrarán los grupos/localizaciones que tengan nodos visibles por el usuario actual
        // (aunque el usuario no vea el grupo/localización)
        $consulta_grupos_localizaciones = "
            SELECT
                id,
                nombre
            FROM ".$tabla_grupos_localizaciones."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($_SESSION["id_localizacion"] == ID_DESACTIVADO)
        {
            if ($clase != CLASE_TODAS)
            {
                $consulta_grupos_localizaciones .= "
                    AND (clase = '".$bd_red->_($clase)."')";
            }
        }
        $consulta_grupos_localizaciones .= "
            ORDER BY
                nombre ASC";
        $res_grupos_localizaciones = $bd_red->ejecuta_consulta($consulta_grupos_localizaciones);
        if ($res_grupos_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$res_grupos_localizaciones."'");
        }

        // Capas por grupo y localización
        $capas_grupos_localizaciones = array();
        while ($fila_grupo_localizacion = $res_grupos_localizaciones->dame_siguiente_fila())
        {
            $params[$nombre_parametro_grupo_localizacion] = $fila_grupo_localizacion['id'];
            $capa_grupo_localizacion = dame_capa_mapa_tipo_nodo($tipo_nodo, $params, $fila_grupo_localizacion['nombre']);
            if (count($capa_grupo_localizacion["info_elementos"]) > 0)
            {
                array_push($capas_grupos_localizaciones, $capa_grupo_localizacion);
            }
        }

        // Si hay más del número máximo de capas, se mostrarán sólo 2 capas: Sin grupo/localización y con grupo/localización
        if ((count($capas_grupos_localizaciones) + count ($capas)) <= NUMERO_MAXIMO_CAPAS_MAPA_GRUPOS_LOCALIZACIONES)
        {
            foreach ($capas_grupos_localizaciones as $capa_grupo_localizacion)
            {
                array_push($capas, $capa_grupo_localizacion);
            }
        }
        else
        {
            $info_elementos = array();
            foreach ($capas_grupos_localizaciones as $capa_grupos_localizaciones)
            {
                foreach ($capa_grupos_localizaciones["info_elementos"] as $info_elemento)
                {
                    array_push($info_elementos, $info_elemento);
                }
            }
            $capa_grupos_localizaciones = array(
                "nombre" => $nombre_capa_grupos_localizaciones,
                "info_elementos" => $info_elementos);
            array_push($capas, $capa_grupos_localizaciones);
        }

        // Se devuelven las capas
        return ($capas);
    }


    function dame_capa_mapa_tipo_nodo($tipo_nodo, $params, $nombre_capa)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $filtro = $params["filtro"];
        $tipo = $params["tipo"];
        $clase = $params["clase"];
        $id_grupo = $params["id_grupo"];
        $estado = $params["estado"];

        $info_elementos = array();
        switch ($tipo_nodo)
        {
            case TIPO_NODO_DISPOSITIVO:
            {
                $consulta_dispositivos = "
                    SELECT *
                    FROM dispositivos
                    WHERE
                        red = '".$_SESSION["id_red"]."'";
                if ($filtro != "")
                {
                    $campos = array("nombre");
                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                    $consulta_dispositivos .= " AND ".$condicion_consulta_filtro_busqueda;
                }
                $res_dispositivos = $bd_red->ejecuta_consulta($consulta_dispositivos);
                if ($res_dispositivos == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_dispositivos."'");
                }
                while ($fila_dispositivo = $res_dispositivos->dame_siguiente_fila())
                {
                    // Se recupera la información de mapa de la localización (si existe)
                    $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                        TIPO_ELEMENTO_MAPA_DISPOSITIVO,
                        $fila_dispositivo["id"],
                        ORIGEN_MAPA_RED,
                        $_SESSION["id_red"]);
                    if ($info_posicion_mapa !== NULL)
                    {
                        $nodo = Nodo::crea_nodo($fila_dispositivo["id"], $tipo_nodo, $fila_dispositivo);
                        array_push($info_elementos, dame_info_mapa_objeto(
                            $nodo,
                            $info_posicion_mapa,
                            ID_MAPA_MAPA_SECCION));
                    }
                }
                break;
            }
            case TIPO_NODO_SENSOR:
            case TIPO_NODO_ACTUADOR:
            {
                // Origen de mapa
                $origen_mapa = $params["origen_mapa"];
                $id_origen_mapa = $params["id_origen_mapa"];

                // Consulta de nodos
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $tabla_sensores_actuadores = "sensores";
                        $condicion_consulta_estado = dame_condicion_consulta_estado_sensores($estado);
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    {
                        $tabla_sensores_actuadores = "actuadores";
                        $condicion_consulta_estado = dame_condicion_consulta_estado_actuadores($estado);
                        break;
                    }
                }
                $consulta_sensores_actuadores = "
                    SELECT *
                    FROM ".$tabla_sensores_actuadores."
                    WHERE
                        (red = '".$_SESSION["id_red"]."')";
                if (array_key_exists("id_localizacion", $params) == true)
                {
                    $id_localizacion = $params["id_localizacion"];
                    $consulta_sensores_actuadores .= "
                        AND (localizacion = '".$bd_red->_($id_localizacion)."')";
                }
                if ($filtro != "")
                {
                    $campos = array("nombre");
                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                    $consulta_sensores_actuadores .= " AND ".$condicion_consulta_filtro_busqueda;
                }
                if ($tipo != TIPO_TODOS)
                {
                    $consulta_sensores_actuadores .= "
                        AND (tipo = '".$bd_red->_($tipo)."')";
                }
                if ($clase != CLASE_TODAS)
                {
                    $consulta_sensores_actuadores .= "
                        AND (clase = '".$bd_red->_($clase)."')";
                }
                if ($id_grupo != ID_TODOS)
                {
                    $consulta_sensores_actuadores .= "
                        AND (grupo = '".$bd_red->_($id_grupo)."')";
                }
                $consulta_sensores_actuadores .= $condicion_consulta_estado;
                if ($params["mostrar_todos_sensores_actuadores"] == false)
                {
                    $consulta_sensores_actuadores .= "
                        AND ".$params["condicion_consulta_sensores_actuadores_usuario_actual"];
                }
                $res_sensores_actuadores = $bd_red->ejecuta_consulta($consulta_sensores_actuadores);
                if ($res_sensores_actuadores == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_sensores_actuadores."'");
                }
                while ($fila_sensor_actuador = $res_sensores_actuadores->dame_siguiente_fila())
                {
                    // Se recupera la información de mapa del nodo (si existe)
                    switch ($tipo_nodo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $tipo_elemento_mapa = TIPO_ELEMENTO_MAPA_SENSOR;
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        {
                            $tipo_elemento_mapa = TIPO_ELEMENTO_MAPA_ACTUADOR;
                            break;
                        }
                    }
                    $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                        $tipo_elemento_mapa,
                        $fila_sensor_actuador["id"],
                        $origen_mapa,
                        $id_origen_mapa);
                    if ($info_posicion_mapa !== NULL)
                    {
                        $nodo = Nodo::crea_nodo($fila_sensor_actuador["id"], $tipo_nodo, $fila_sensor_actuador);
                        array_push($info_elementos, dame_info_mapa_objeto(
                            $nodo,
                            $info_posicion_mapa,
                            ID_MAPA_MAPA_SECCION));
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }

        // Se crea y devuelve la capa
        $capa = array(
            "nombre" => $nombre_capa,
            "info_elementos" => $info_elementos
        );
        return ($capa);
    }


    function dame_capa_mapa_localizaciones($params, $nombre_capa)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $filtro = $params["filtro"];
        $orden = $params["orden"];
        $info_elementos = array();

        // Se añaden las localizaciones
        $consulta_localizaciones = "
            SELECT *
            FROM localizaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($filtro != "")
        {
            $campos = array("nombre");
            $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
            $consulta_localizaciones .= " AND ".$condicion_consulta_filtro_busqueda;
        }
        $consulta_localizaciones .= "
                AND (orden = '".$bd_red->_($orden)."')";
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == false)
        {
            $consulta_localizaciones .= "
                AND ".dame_condicion_consulta_localizaciones_usuario_actual();
        }
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            // Se recupera la información de mapa de la localización (si existe)
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_LOCALIZACION,
                $fila_localizacion["id"],
                ORIGEN_MAPA_RED,
                $_SESSION["id_red"]);
            if ($info_posicion_mapa !== NULL)
            {
                $localizacion = new Localizacion($fila_localizacion);
                array_push($info_elementos, dame_info_mapa_objeto(
                    $localizacion,
                    $info_posicion_mapa,
                    ID_MAPA_MAPA_SECCION));
            }
        }

        // Se devuelve la capa de localizaciones
        $capa = array(
            "nombre" => $nombre_capa,
            "info_elementos" => $info_elementos
        );
        return ($capa);
    }


    //
    // Objetos mostrados en mapa
    //


    // Funciones a implementar en objetos que se muestren en el mapa:
    // - dame_datos_imagen_mapa();
    // - crea_imagen_texto_auxiliar(&$ruta_imagen_texto_auxiliar);
    // - anyade_rutas_imagenes_satelite(&$rutas_imagenes_satelite);
    // - dame_tooltip_mapa();


    // Devuelve información para mostrar un objeto en el mapa
    function dame_info_mapa_objeto($objeto, $info_posicion_mapa, $id_mapa)
    {
        $datos_imagen = $objeto->dame_datos_imagen_mapa();
        $tooltip_mapa = $objeto->dame_tooltip_mapa($id_mapa);

        $info_mapa = array(
            "nombre" => $objeto->params["nombre"],
            "icono" => $datos_imagen["imagen"],
            "anchura_icono" => $datos_imagen["tamanyo"][0],
            "altura_icono" => $datos_imagen["tamanyo"][1],
            "latitud" => $info_posicion_mapa["latitud"],
            "longitud" => $info_posicion_mapa["longitud"],
            "tooltip" => $tooltip_mapa
        );
        return ($info_mapa);
    }


    // Devuelve información de la imagen de un objeto en el mapa
    function dame_imagen_mapa_objeto(
        $objeto,
        $id_objeto,
        $nombre_objeto,
        $tipo_objeto,
        $ruta_imagen_base)
    {
        // Se recupera el directorio de ficheros temporales del usuario
        $directorio_absoluto_imagen_mapa_actual = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_imagen_mapa_actual = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_imagen_mapa_actual);

        // Se calcula el nombre de la imagen actual y se eliminan los iconos anteriores de este objeto
        $nombre_imagen_mapa_actual = $tipo_objeto."-".$id_objeto;
        foreach (glob($directorio_absoluto_imagen_mapa_actual."/".$nombre_imagen_mapa_actual.'*') as $imagen_anterior)
        {
            unlink($imagen_anterior);
        }
        $timestamp_utc = dame_timestamp_ahora_milisegundos_utc();
        $nombre_imagen_mapa_actual .= "-".$timestamp_utc;

        // Se crea la imagen con el texto auxiliar y los satélites
        $ruta_imagen_texto_auxiliar = "";
        $objeto->crea_imagen_texto_auxiliar($ruta_imagen_texto_auxiliar);
        $rutas_fila_imagenes_satelite_1 = array();
        $rutas_fila_imagenes_satelite_2 = array();
        $objeto->anyade_rutas_imagenes_satelite($rutas_fila_imagenes_satelite_1, $rutas_fila_imagenes_satelite_2);
        $nombre_imagen_mapa_actual .= ".png";

        // Se crea el fichero con la imagen
        $ruta_absoluta_imagen_mapa_actual = $directorio_absoluto_imagen_mapa_actual."/".$nombre_imagen_mapa_actual;
        $ruta_imagen_satelites_1 = $directorio_absoluto_imagen_mapa_actual."/"."satelites_1_tmp_mapa.png";
        $ruta_imagen_satelites_2 = $directorio_absoluto_imagen_mapa_actual."/"."satelites_2_tmp_mapa.png";
        $ruta_imagen_etiqueta = $directorio_absoluto_imagen_mapa_actual."/"."etiqueta_tmp_mapa.png";
        $ruta_imagen_base_satelites = $directorio_absoluto_imagen_mapa_actual."/"."base_satelites_tmp_mapa.png";

        // Se añaden los satélites (si existen)
        if ((count($rutas_fila_imagenes_satelite_1) > 0) || (count($rutas_fila_imagenes_satelite_2) > 0))
        {
            if ((count($rutas_fila_imagenes_satelite_1) > 0) && (count($rutas_fila_imagenes_satelite_2) > 0))
            {
                crea_imagen_satelites($rutas_fila_imagenes_satelite_2, $ruta_imagen_satelites_2);
                crea_imagen_base_satelites($ruta_imagen_base, $ruta_imagen_satelites_2, $ruta_imagen_base_satelites, "ARRIBA");
                crea_imagen_satelites($rutas_fila_imagenes_satelite_1, $ruta_imagen_satelites_1);
                crea_imagen_base_satelites($ruta_imagen_base_satelites, $ruta_imagen_satelites_1, $ruta_imagen_base_satelites, "ARRIBA");
            }
            else
            {
                if (count($rutas_fila_imagenes_satelite_1) > 0)
                {
                    crea_imagen_satelites($rutas_fila_imagenes_satelite_1, $ruta_imagen_satelites_1);
                    crea_imagen_base_satelites($ruta_imagen_base, $ruta_imagen_satelites_1, $ruta_imagen_base_satelites, "ARRIBA");
                }
                else
                {
                    if (count($rutas_fila_imagenes_satelite_2) > 0)
                    {
                        crea_imagen_satelites($rutas_fila_imagenes_satelite_2, $ruta_imagen_satelites_2);
                        crea_imagen_base_satelites($ruta_imagen_base, $ruta_imagen_satelites_2, $ruta_imagen_base_satelites, "ARRIBA");
                    }
                }
            }
        }
        else
        {
            copy($ruta_imagen_base, $ruta_imagen_base_satelites);
        }

        // Se redimensiona la imagen
        $tamanyo_letra = $_SESSION["tamanyo_letra"];
        $factor = $tamanyo_letra / TAMANYO_LETRA_DEFECTO;
        if ($factor != 1)
        {
            redimensiona_imagen($ruta_imagen_base_satelites, $factor);
        }

        // Se añaden el texto auxiliar (si existe)
        if ($ruta_imagen_texto_auxiliar != "")
        {
            crea_imagen_base_satelites($ruta_imagen_base_satelites, $ruta_imagen_texto_auxiliar, $ruta_imagen_base_satelites, "ARRIBA");
        }

        // Se añade la etiqueta
        if ($_SESSION["etiquetas_mapa"] == VALOR_SI)
        {
            crea_imagen_texto($nombre_objeto, $ruta_imagen_etiqueta);
            crea_imagen_base_satelites($ruta_imagen_base_satelites, $ruta_imagen_etiqueta, $ruta_absoluta_imagen_mapa_actual, "ARRIBA");
        }
        else
        {
            copy($ruta_imagen_base_satelites, $ruta_absoluta_imagen_mapa_actual);
        }

        // Se recupera el tamaño de la imagen
        $ruta_relativa_imagen_mapa_actual = $directorio_relativo_imagen_mapa_actual."/".$nombre_imagen_mapa_actual;
        $tamanyo_imagen = getimagesize($ruta_absoluta_imagen_mapa_actual);

        // Se eliminan las imágenes temporales
        $sufijo_imagenes_temporales = "_tmp_mapa.png";
        foreach (glob($directorio_absoluto_imagen_mapa_actual."/".'*'.$sufijo_imagenes_temporales) as $imagen_anterior)
        {
            unlink($imagen_anterior);
        }

        $datos_imagen_mapa_actual = array("imagen" => $ruta_relativa_imagen_mapa_actual, "tamanyo" => $tamanyo_imagen);
        return ($datos_imagen_mapa_actual);
    }


    //
    // Funciones de localizador de mapa
    //


    // Devuelve los controles del localizador del mapa
    function dame_localizador_mapa(
        $sufijo_controles,
        $origen_mapa,
        $id_origen_mapa,
        $latitud,
        $longitud,
        $zoom)
    {
        $localizador_mapa = "<div class='localizador-mapa margen-inferior' id='localizador_mapa_oculto".$sufijo_controles."' hidden></div>";
        $localizador_mapa .= "
            <input type='hidden' id='origen_mapa".$sufijo_controles."' value='".$origen_mapa."'>
            <input type='hidden' id='id_origen_mapa".$sufijo_controles."' value='".json_encode($id_origen_mapa)."'>
            <div class='row-fluid' id='controles_localizador_mapa".$sufijo_controles."'>
                <div id='controles_latitud_longitud_mapa".$sufijo_controles."' hidden>
                    <div class='span12'><span class='titulo-campo-administracion' id='titulo_latitud_longitud_localizador_mapa".$sufijo_controles."'></span><br/>
                        <input type='text' id='latitud_mapa".$sufijo_controles."'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion-50-izda latitud-mapa' value='".$latitud."'>
                        <input type='text' id='longitud_mapa".$sufijo_controles."'
                            class='TLNT_input_mandatory TLNT_input_float input-administracion-50-izda longitud-mapa' value='".$longitud."'>
                    </div>
                    <input type='hidden' id='zoom_mapa".$sufijo_controles."' value='".$zoom."'>
                </div>
            </div>";
        return ($localizador_mapa);
    }


    //
    // Funciones de posiciones de mapa
    //


    // Recupera la posición del mapa de base de datos
    function dame_info_posicion_mapa_base_datos(
        $tipo_elemento,
        $id_elemento,
        $origen,
        $id_origen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta = "
            SELECT *
            FROM posiciones_mapa
            WHERE
                (tipo_elemento = '".$bd_red->_($tipo_elemento)."')
                AND (id_elemento = '".$bd_red->_($id_elemento)."')
                AND (origen = '".$bd_red->_($origen)."')
                AND (id_origen = '".$bd_red->_($id_origen)."')";
        $res_consulta = $bd_red->ejecuta_consulta($consulta);
        if ($res_consulta == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        if ($res_consulta->dame_numero_filas() == 0)
        {
            $info_posicion_mapa = NULL;
        }
        else
        {
            $fila_posicion_mapa = $res_consulta->dame_siguiente_fila();
            $info_posicion_mapa = array(
                "tipo_elemento" => $fila_posicion_mapa["tipo_elemento"],
                "id_elemento" => $fila_posicion_mapa["id_elemento"],
                "origen" => $fila_posicion_mapa["origen"],
                "id_origen" => $fila_posicion_mapa["id_origen"],
                "latitud" => $fila_posicion_mapa["latitud"],
                "longitud" => $fila_posicion_mapa["longitud"],
                "zoom" => $fila_posicion_mapa["zoom"]);
        }
        return ($info_posicion_mapa);
    }


    // Guarda la posición del mapa en base de datos
    function guarda_info_posicion_mapa_base_datos($info_posicion_mapa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de la posición del mapa
        $tipo_elemento = $info_posicion_mapa["tipo_elemento"];
        $id_elemento = $info_posicion_mapa["id_elemento"];
        $origen = $info_posicion_mapa["origen"];
        $id_origen = $info_posicion_mapa["id_origen"];
        $latitud = $info_posicion_mapa["latitud"];
        $longitud = $info_posicion_mapa["longitud"];
        $zoom = $info_posicion_mapa["zoom"];

        // Se elimina la información (por si ya existía)
        elimina_info_posicion_mapa_base_datos(
            $tipo_elemento,
            $id_elemento,
            $origen,
            $id_origen);

        // se añade la información
        $operacion_insercion = "
            INSERT INTO posiciones_mapa (
                red,
                tipo_elemento,
                id_elemento,
                origen,
                id_origen,
                latitud,
                longitud,
                zoom
            ) VALUES (
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($tipo_elemento)."',
                '".$bd_red->_($id_elemento)."',
                '".$bd_red->_($origen)."',
                '".$bd_red->_($id_origen)."',
                '".$bd_red->_($latitud)."',
                '".$bd_red->_($longitud)."',
                '".$bd_red->_($zoom)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }


    // Elimina la información del mapa en base de datos
    function elimina_info_posicion_mapa_base_datos(
        $tipo_elemento,
        $id_elemento,
        $origen,
        $id_origen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado = "
            DELETE
            FROM posiciones_mapa
            WHERE
                (tipo_elemento = '".$bd_red->_($tipo_elemento)."')
                AND (id_elemento = '".$bd_red->_($id_elemento)."')
                AND (origen = '".$bd_red->_($origen)."')
                AND (id_origen = '".$bd_red->_($id_origen)."')";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    // Elimina la información de posiciones del mapa del elemento especificado en base de datos
    function elimina_info_posiciones_mapa_elemento_base_datos($tipo_elemento, $id_elemento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado = "
            DELETE
            FROM posiciones_mapa
            WHERE
                (tipo_elemento = '".$bd_red->_($tipo_elemento)."')
                AND (id_elemento = '".$bd_red->_($id_elemento)."')";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    // Elimina la información de posiciones del mapa del origen especificado en base de datos
    function elimina_info_posiciones_mapa_origen_base_datos($origen, $id_origen)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado = "
            DELETE
            FROM posiciones_mapa
            WHERE
                (origen = '".$bd_red->_($origen)."')
                AND (id_origen = '".$bd_red->_($id_origen)."')";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    //
    // Funciones de parámetros de accion de usuario
    //


    // Añade los parámetros de información de posición en el mapa
    function anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa, &$parametros_accion_usuario)
    {
        if ($info_posicion_mapa === NULL)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MOSTRAR_EN_MAPA] = VALOR_NO;
        }
        else
        {
            // Nombre de origen del mapa
            $nombre_origen = dame_nombre_origen_mapa($info_posicion_mapa["origen"], $info_posicion_mapa["id_origen"]);

            // Parámetros de la acción
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MOSTRAR_EN_MAPA] = VALOR_SI;
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_MAPA] = $info_posicion_mapa["origen"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA] = $info_posicion_mapa["latitud"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA] = $info_posicion_mapa["longitud"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA] = $info_posicion_mapa["zoom"];
        }
    }


    //
    // Funciones auxiliares
    //


    // Devuelve los botones del mapa
    function dame_botones_mapa()
    {
        $boton_actualizacion_periodica_mapa = "<i id='boton_actualizacion_periodica_mapa' class='icon-play color-blanco boton-tabla-datos'></i>";
        $boton_actualizar_mapa = "<i id='boton_actualizar_mapa' class='icon-refresh color-blanco boton-tabla-datos'></i>";
        $boton_centrar_mapa = "<i id='boton_centrar_mapa' class='icon-screenshot color-blanco boton-tabla-datos'></i>";
        $boton_etiquetas_mapa = "<i id='boton_etiquetas_mapa' class='icon-tags color-blanco boton-tabla-datos'></i>";

        $botones = array(
            $boton_actualizacion_periodica_mapa,
            $boton_actualizar_mapa,
            $boton_centrar_mapa,
            $boton_etiquetas_mapa);
        return ($botones);
    }


    // Devuelve la lista de los tipos de mapa
    function dame_lista_tipos_mapa($tipo_mapa)
    {
        $lista_tipos_mapa = dame_lista_valores(
            array(
                array(TIPO_MAPA_INTERNET, dame_descripcion_tipo_mapa(TIPO_MAPA_INTERNET)),
                array(TIPO_MAPA_LOCAL, dame_descripcion_tipo_mapa(TIPO_MAPA_LOCAL))),
            array($tipo_mapa));
        return ($lista_tipos_mapa);
    }


    // Devuelve la descripción del tipo de mapa
    function dame_descripcion_tipo_mapa($tipo_mapa)
    {
        switch ($tipo_mapa)
        {
            case TIPO_MAPA_INTERNET:
            {
                $descripcion = "Internet";
                break;
            }
            case TIPO_MAPA_LOCAL:
            {
                $descripcion = "Local";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve la descripción del origen del mapa
    function dame_descripcion_origen_mapa($origen_mapa)
    {
        switch ($origen_mapa)
        {
            case ORIGEN_MAPA_RED:
            {
                $descripcion = "Red";
                break;
            }
            case ORIGEN_MAPA_LOCALIZACION:
            {
                $descripcion = "Localización";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    // Devuelve el nombre del origen del mapa
    function dame_nombre_origen_mapa($origen_mapa, $id_origen_mapa)
    {
        $idiomas = new Idiomas();

        switch ($origen_mapa)
        {
            case ORIGEN_MAPA_RED:
            {
                $nombre_origen = dame_nombre_red($id_origen_mapa);
                break;
            }
            case ORIGEN_MAPA_LOCALIZACION:
            {
                $nombre_origen = dame_nombre_localizacion($id_origen_mapa);
                break;
            }
            default:
            {
                $nombre_origen = $idiomas->_("Desconocido");
                break;
            }
        }
        return ($nombre_origen);
    }


    // Devuelve la descripción del icono de mapa
    function dame_descripcion_icono_mapa($icono)
    {
        switch ($icono)
        {
            case "Equipo":
            case "Sensor":
            case "Actuador":
            {
                $descripcion = "Genérico";
                break;
            }
            case "datalogger":
            {
                $descripcion = "Data logger";
                break;
            }
            case "modem":
            {
                $descripcion = "Módem";
                break;
            }
            case "electrico":
            {
                $descripcion = "Eléctrico";
                break;
            }
            case "temperatura":
            {
                $descripcion = "Temperatura";
                break;
            }
            case "vela":
            {
                $descripcion = "Vela";
                break;
            }
            case "viento":
            {
                $descripcion = "Viento";
                break;
            }
            case "agua":
            {
                $descripcion = "Agua";
                break;
            }
            case "gas":
            {
                $descripcion = "Gas";
                break;
            }
            case "contador":
            {
                $descripcion = "Contador";
                break;
            }
            case "movimiento":
            {
                $descripcion = "Movimiento";
                break;
            }
            case "bombilla":
            {
                $descripcion = "Bombilla";
                break;
            }
            case "interruptor":
            {
                $descripcion = "Interruptor";
                break;
            }
            case "sobre":
            {
                $descripcion = "Sobre";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }
?>
