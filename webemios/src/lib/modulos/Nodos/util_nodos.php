<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Licencias/Licencia.php');


	//
    // Funciones para obtener las tablas de nodos
    //


    function dame_tabla_nodos(
        $tipo,
        $filtro = "",
        $parametros_tipo_nodo = NULL,
        $tipo_nodo_actualizacion_periodica = NULL)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Tipo de nodo
        $numero_maximo_elementos = -1;
        $administracion_nodos = Nodo::dame_administracion_nodos($tipo);
        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $titulo = $idiomas->_("Redes");
                $texto_nodos = $idiomas->_("redes");
                $texto_nodo = $idiomas->_("red");
                $mostrar_nodos_conectados_pie_tabla = false;
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $titulo = $idiomas->_("Dispositivos");
                $texto_nodos = $idiomas->_("dispositivos");
                $texto_nodo = $idiomas->_("dispositivo");
                $mostrar_nodos_conectados_pie_tabla = true;
                if ($administracion_nodos == true)
                {
                    $numero_maximo_elementos = dame_numero_maximo_elementos_modulo(MODULO_RED);
                }
                break;
            }
            case TIPO_NODO_AXON:
            {
                $titulo = $idiomas->_("Axones");
                $texto_nodos = $idiomas->_("axones");
                $texto_nodo = $idiomas->_("axón");
                $mostrar_nodos_conectados_pie_tabla = true;
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                $titulo = $idiomas->_("Sensores");
                $texto_nodos = $idiomas->_("sensores");
                $texto_nodo = $idiomas->_("sensor");
                $mostrar_nodos_conectados_pie_tabla = true;
                if ($administracion_nodos == true)
                {
                    $numero_maximo_elementos = dame_numero_maximo_elementos_modulo(MODULO_SENSORES);
                }
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $titulo = $idiomas->_("Grupos");
                $texto_nodos = $idiomas->_("grupos");
                $texto_nodo = $idiomas->_("grupo");
                $mostrar_nodos_conectados_pie_tabla = false;
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $titulo = $idiomas->_("Actuadores");
                $texto_nodos = $idiomas->_("actuadores");
                $texto_nodo = $idiomas->_("actuador");
                $mostrar_nodos_conectados_pie_tabla = true;
                if ($administracion_nodos == true)
                {
                    $numero_maximo_elementos = dame_numero_maximo_elementos_modulo(MODULO_ACTUADORES);
                }
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $titulo = $idiomas->_("Grupos");
                $texto_nodos = $idiomas->_("grupos");
                $texto_nodo = $idiomas->_("grupo");
                $mostrar_nodos_conectados_pie_tabla = false;
                break;
            }
            default:
            {
                throw new Exception("Tabla no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        // Parámetros dependientes del tipo de nodo
        if ($parametros_tipo_nodo === NULL)
        {
            switch ($tipo)
            {
                case TIPO_NODO_SENSOR:
                {
                    // Si no hay parámetros de tipo de nodo es la pantalla inicial, el ratio es ninguno por defecto
                    $id_ratio = ID_NINGUNO;
                    $parametros_tipo_nodo = array(
                        "tipo" => TIPO_TODOS,
                        "clase" => CLASE_TODAS,
                        "id_grupo" => ID_TODOS,
                        "estado" => ESTADO_SENSOR_TODOS,
                        "id_ratio" => $id_ratio);
                    break;
                }
                case TIPO_NODO_GRUPO_SENSORES:
                {
                    $parametros_tipo_nodo = array(
                        "clase" => CLASE_TODAS);
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $parametros_tipo_nodo = array(
                        "tipo" => TIPO_TODOS,
                        "clase" => CLASE_TODAS,
                        "id_grupo" => ID_TODOS,
                        "estado" => ESTADO_ACTUADOR_TODOS);
                    break;
                }
                case TIPO_NODO_GRUPO_ACTUADORES:
                {
                    $parametros_tipo_nodo = array(
                        "clase" => CLASE_TODAS);
                    break;
                }
            }
        }
        $consulta_nodos = dame_consulta_nodos($tipo, $filtro, $parametros_tipo_nodo);
        $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
        if ($res_nodos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_nodos."'");
        }

        // Guarda parámetros en la sesión según el tipo de nodo
        guarda_parametros_sesion_tabla_nodos($tipo, $parametros_tipo_nodo);

        // Se crea la tabla
        $opciones = array();
        $permitir_adicion_nodos = false;
        if ($administracion_nodos == true)
        {
            if ($numero_maximo_elementos > 0)
            {
                $consulta_numero_nodos = dame_consulta_numero_nodos($tipo);
                $res_numero_nodos = $bd_red->ejecuta_consulta($consulta_numero_nodos);
                if (($res_numero_nodos == false) || ($res_numero_nodos->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_nodos."'");
                }
                $fila_numero_nodos = $res_numero_nodos->dame_siguiente_fila();
                $numero_nodos_totales = $fila_numero_nodos["numero_nodos"];
            }

            if (($numero_maximo_elementos <= 0) || ($numero_nodos_totales < $numero_maximo_elementos))
            {
                $permitir_adicion_nodos = true;
                $boton_anyadir_nodo = "<i id='anyade_modifica_nodo__".$tipo."' ".
                    "class='icon-plus color-blanco boton_mostrar_ventana_anyadir_modificar_nodo boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_nodo);
            }
        }
        if ($tipo != $tipo_nodo_actualizacion_periodica)
        {
            $icono_boton_actualizacion_periodica = "icon-play";
        }
        else
        {
            $icono_boton_actualizacion_periodica = "icon-pause";
        }
        $boton_actualizacion_periodica_tabla_nodos = "<i id='boton_actualizacion_periodica_tabla__".$tipo."' class='".$icono_boton_actualizacion_periodica." color-blanco boton-tabla-datos boton_actualizacion_periodica_tabla_nodos'></i>";
        array_push($opciones, $boton_actualizacion_periodica_tabla_nodos);
        $boton_actualizar_tabla_nodos = "<i id='actualiza__".$tipo."' class='icon-refresh color-blanco boton_actualizar_tabla_nodos boton-tabla-datos'></i>";
        array_push($opciones, $boton_actualizar_tabla_nodos);

        // Se crea la tabla
        $params_tabla = array(
            "opciones" => $opciones,
            "numero_columnas" => dame_numero_columnas_tabla_nodos($tipo),
            "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
            "filas_con_opciones" => ($administracion_nodos == true),
            "generar_valores_xml" => true
        );
        $anchuras_columnas_tabla = dame_anchuras_columnas_tabla_nodos($tipo);
        if ($anchuras_columnas_tabla !== NULL)
        {
            $params_tabla["anchuras_columnas"] = $anchuras_columnas_tabla;
        }
        $tabla = new TablaDatos(
            "tabla-nodos-".$tipo,
            $titulo,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $cabecera = Nodo::dame_cabecera_tabla_tipo_nodo($tipo);
        $tabla->anyade_cabecera("", $cabecera);

        // Filas de los nodos
        $filas_nodos = array();
        while ($fila_nodo = $res_nodos->dame_siguiente_fila())
        {
            array_push($filas_nodos, $fila_nodo);
        }

        // Añade información y ordena las filas de los nodos (si es necesario)
        anyade_informacion_ordena_filas_nodos_tabla_nodos($tipo, $filas_nodos);

        // Se recuperan los identificadores de nodos administrables y no administrables
        // (Nota: Se recuperan por separado porque p.e. en sensores y actuadores sólo se comprueba
        //  si son administrables si hay localizaciones, si no se devuelve NULL, y la comprobación de no administables
        //  se hace sólo en sensores por un campo en la base de datos)
        $ids_nodos_administrables = dame_ids_nodos_administrables($tipo);
        $ids_nodos_no_administrables = dame_ids_nodos_no_administrables($tipo, $filas_nodos);

        // Se añade cada uno de los nodos a la tabla
        $numero_nodos_tabla = 0;
        $nodos_conectados = 0;
        $nodos_desconectados = 0;
        foreach ($filas_nodos as $fila_nodo)
        {
            $anyadir_nodo = dame_anyadir_nodo_tabla_nodos($tipo, $parametros_tipo_nodo, $fila_nodo);
            if ($anyadir_nodo == true)
            {
                $id_nodo = $fila_nodo['id'];
                $nodo = Nodo::crea_nodo($id_nodo, $tipo, $fila_nodo);
                if ($mostrar_nodos_conectados_pie_tabla == true)
                {
                    switch ($nodo->conexion)
                    {
                        case "ON":
                        {
                            $nodos_conectados++;
                            break;
                        }
                        case "OFF":
                        case "FINISHED":
                        case "TIMEOUT":
                        {
                            $nodos_desconectados++;
                            break;
                        }
                    }
                }

                $nodo_administrable = ($ids_nodos_administrables === NULL) || (in_array($id_nodo, $ids_nodos_administrables) == true);
                if ($nodo_administrable == true)
                {
                    $nodo_administrable = (in_array($id_nodo, $ids_nodos_no_administrables) == false);
                }
                $params_fila = array(
                    "opciones" => $nodo->dame_opciones_tabla($nodo_administrable, $permitir_adicion_nodos)
                );
                $valor_nodo_administrable = ($nodo_administrable == true)? VALOR_SI: VALOR_NO;
                $valor_permitir_adicion_nodos = ($permitir_adicion_nodos == true)? VALOR_SI: VALOR_NO;
                $tabla->anyade_fila(
                    "datosNodo".$tipo."__".$id_nodo."__".$valor_nodo_administrable."__".$valor_permitir_adicion_nodos,
                    $nodo->dame_datos_tabla(),
                    $params_fila
                );
                $numero_nodos_tabla += 1;
            }
        }

        // Pie de tabla
        if ($numero_nodos_tabla == 1)
        {
            $texto_nodos = $texto_nodo;
        }
        $texto_pie = $idiomas->_("Total").": ".$numero_nodos_tabla." ".$texto_nodos;
        if ($administracion_nodos == true)
        {
            if ($numero_maximo_elementos > 0)
            {
                $texto_pie .= " (".$idiomas->_("máximo").": ".$numero_maximo_elementos.")";
            }
        }
        $texto_pie .= dame_texto_adicional_pie_tabla_nodos(
            $tipo,
            $filas_nodos,
            $numero_nodos_tabla,
            $mostrar_nodos_conectados_pie_tabla,
            $nodos_conectados,
            $nodos_desconectados);
        $tabla->anyade_pie($texto_pie);

        // Se devuelve la tabla
        return ($tabla->dame_tabla());
    }


    function dame_numero_columnas_tabla_nodos($tipo)
    {
        // Flag de mostrar localizaciones
        $mostrar_localizaciones = (dame_mostrar_controles_localizaciones() == true);

        // Número de columnas
        $numero_columnas = -1;
        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_RED;
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_DISPOSITIVO;
                break;
            }
            case TIPO_NODO_AXON:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_AXON;
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                if ($mostrar_localizaciones == true)
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_SENSOR_CON_LOCALIZACION;
                }
                else
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_SENSOR_SIN_LOCALIZACION;
                }
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                if ($mostrar_localizaciones == true)
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_GRUPO_SENSORES_CON_LOCALIZACION;
                }
                else
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_GRUPO_SENSORES_SIN_LOCALIZACION;
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                if ($mostrar_localizaciones == true)
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_ACTUADOR_CON_LOCALIZACION;
                }
                else
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_ACTUADOR_SIN_LOCALIZACION;
                }
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                if ($mostrar_localizaciones == true)
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_GRUPO_ACTUADORES_CON_LOCALIZACION;
                }
                else
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_NODO_GRUPO_ACTUADORES_SIN_LOCALIZACION;
                }
                break;
            }
            default:
            {
                throw new Exception("Tabla no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        return ($numero_columnas);
    }


    function dame_anchuras_columnas_tabla_nodos($tipo)
    {
        // Flag de mostrar localizaciones
        $mostrar_localizaciones = (dame_mostrar_controles_localizaciones() == true);

        // Anchuras de columnas
        $anchuras_columnas = NULL;
        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_NODO_RED);
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                if ($mostrar_localizaciones == true)
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_NODO_SENSOR_CON_LOCALIZACION);
                }
                else
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_NODO_SENSOR_SIN_LOCALIZACION);
                }
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                if ($mostrar_localizaciones == true)
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_NODO_ACTUADOR_CON_LOCALIZACION);
                }
                else
                {
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_NODO_ACTUADOR_SIN_LOCALIZACION);
                }
                break;
            }
        }

        return ($anchuras_columnas);
    }


    function guarda_parametros_sesion_tabla_nodos($tipo, $parametros_tipo_nodo)
    {
        // Si es un sensor, se guarda el ratio en la sesión
        switch ($tipo)
        {
            case TIPO_NODO_SENSOR:
            {
                $id_ratio = $parametros_tipo_nodo["id_ratio"];
                $_SESSION["id_ratio_sensores"] = $id_ratio;
                break;
            }
        }
    }


    function dame_ids_nodos_administrables($tipo)
    {
        // Si es un sensor o un actuador, se recuperan los identificadores de nodos administrables
        $ids_nodos_administrables = NULL;
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            switch ($tipo)
            {
                case TIPO_NODO_SENSOR:
                case TIPO_NODO_ACTUADOR:
                {
                    if ($_SESSION["id_localizacion"] != ID_DESACTIVADO)
                    {
                        $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(false);
                        $ids_nodos_administrables = dame_ids_nodos_administrables_localizaciones($ids_localizaciones_usuario_actual, $tipo);
                    }
                    break;
                }
                default:
                {
                    break;
                }
            }
        }
        return ($ids_nodos_administrables);
    }


    function dame_ids_nodos_no_administrables($tipo, $filas_nodos)
    {
        // Si es un sensor, se recuperan los identificadores de nodos no administrables
        $ids_nodos_no_administrables = array();
        switch ($tipo)
        {
            case TIPO_NODO_SENSOR:
            {
                foreach ($filas_nodos as $fila_nodo)
                {
                    $id_nodo = $fila_nodo["id"];
                    $administrable = $fila_nodo["administrable"];
                    if ($administrable == VALOR_NO)
                    {
                        array_push($ids_nodos_no_administrables, $id_nodo);
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_nodos_no_administrables);
    }


    function anyade_informacion_ordena_filas_nodos_tabla_nodos($tipo, &$filas_nodos)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Si hay localización se ordena por nombre de localización y de nodo
        // (para mostrar los nodos agrupados por localización)
        // (y se añaden los nombres de las localizaciones a las filas de los nodos)
        switch ($tipo)
        {
            case TIPO_NODO_SENSOR:
            case TIPO_NODO_GRUPO_SENSORES:
            case TIPO_NODO_ACTUADOR:
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);
                if ($mostrar_localizacion == true)
                {
                    $consulta_localizaciones = "
                        SELECT
                            id,
                            nombre
                        FROM localizaciones
                        WHERE
                            red = '".$_SESSION["id_red"]."'";
                    $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
                    $nombres_localizaciones = array();
                    while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
                    {
                        $nombres_localizaciones[$fila_localizacion["id"]] = $fila_localizacion["nombre"];
                    }
                    for ($i = 0; $i < count($filas_nodos); $i++)
                    {
                        $id_localizacion = $filas_nodos[$i]["localizacion"];
                        if ($id_localizacion == ID_NINGUNO)
                        {
                            $nombre_localizacion = $idiomas->_("Ninguna");
                        }
                        else
                        {
                            $nombre_localizacion = $nombres_localizaciones[$id_localizacion];
                        }
                        $filas_nodos[$i]["nombre_localizacion"] = $nombre_localizacion;
                        $filas_nodos[$i]["mostrar_localizacion"] = $mostrar_localizacion;
                    }

                    // (https://stackoverflow.com/questions/832709/natural-sorting-algorithm-in-php-with-support-for-unicode)
                    $locale_anterior = setlocale(LC_ALL, $_SESSION["idioma"].".utf8");
                    if ($locale_anterior === false)
                    {
                        foreach ($filas_nodos as $indice => $fila_nodo)
                        {
                            $nombres_localizaciones_ordenacion[$indice] = convierte_ascii_estandar($fila_nodo['nombre_localizacion']);
                            $nombres_ordenacion[$indice] = convierte_ascii_estandar($fila_nodo['nombre']);
                        }
                    }
                    else
                    {
                        foreach ($filas_nodos as $indice => $fila_nodo)
                        {
                            $nombres_localizaciones_ordenacion[$indice] = $fila_nodo['nombre_localizacion'];
                            $nombres_ordenacion[$indice] = $fila_nodo['nombre'];
                        }
                    }
                    array_multisort(
                        $nombres_localizaciones_ordenacion, SORT_ASC, SORT_LOCALE_STRING,
                        $nombres_ordenacion, SORT_ASC, SORT_LOCALE_STRING,
                        $filas_nodos);
                    if ($locale_anterior !== false)
                    {
                        setlocale(LC_COLLATE, $locale_anterior);
                    }
                }
                break;
            }
        }

        // Si el nodo es sensor se añaden los flags para los iconos de procesado de datos
        switch ($tipo)
        {
            case TIPO_NODO_SENSOR:
            {
                $ids_sensores_importaciones_pendientes = dame_ids_sensores_importaciones_pendientes();
                $nombres_sensores_recalculos_pendientes = dame_nombres_sensores_recalculos_pendientes();
                for ($i = 0; $i < count($filas_nodos); $i++)
                {
                    $id_sensor = $filas_nodos[$i]["id"];
                    $nombre_sensor = $filas_nodos[$i]["nombre"];
                    $tipo_sensor = $filas_nodos[$i]["tipo"];

                    // Flag de importaciones de valores pendientes
                    $hay_importaciones_valores_pendientes = (in_array($id_sensor, $ids_sensores_importaciones_pendientes) == true);
                    $filas_nodos[$i]["hay_importaciones_valores_pendientes"] = $hay_importaciones_valores_pendientes;

                    // Flag de recálculos de valores de clase pendientes
                    $hay_recalculos_valores_clase_pendientes = (in_array($nombre_sensor, $nombres_sensores_recalculos_pendientes) == true);
                    $filas_nodos[$i]["hay_recalculos_valores_clase_pendientes"] = $hay_recalculos_valores_clase_pendientes;

                    // Flag de último valor antiguo
                    switch ($tipo_sensor)
                    {
                        case TIPO_SENSOR_PROCESADO:
                        {
                            $ultimo_valor_antiguo_procesado = false;
                            $cadena_fecha_hora_ultimos_valores_base_datos_utc = $filas_nodos[$i]["hora_ultimos_valores"];
                            if ($cadena_fecha_hora_ultimos_valores_base_datos_utc === NULL)
                            {
                                $ultimo_valor_antiguo_procesado = true;
                            }
                            if ($ultimo_valor_antiguo_procesado == false)
                            {
                                $fecha_hora_ultimos_valores_utc = convierte_cadena_a_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                                $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                                $periodo_antiguedad_valores = $fecha_hora_actual_utc->diff($fecha_hora_ultimos_valores_utc);
                                $numero_dias_antiguedad_valores = $periodo_antiguedad_valores->days;
                                if ($numero_dias_antiguedad_valores > NUMERO_MAXIMO_DIAS_CALCULO_VALORES_SENSORES_PROCESADO_EJECUCION_SIN_RECALCULOS)
                                {
                                    $ultimo_valor_antiguo_procesado = true;
                                }
                            }

                            break;
                        }
                        default:
                        {
                            $ultimo_valor_antiguo_procesado = false;
                            break;
                        }
                    }
                    $filas_nodos[$i]["ultimo_valor_antiguo_procesado"] = $ultimo_valor_antiguo_procesado;
                }
                break;
            }
        }

        // Adición de nombres de parámetros según el tipo de nodo
        switch ($tipo)
        {
            // Se añade la siguiente información a los datos de las filas de las redes:
            // - Nombres de los clientes
            // - Número de sensores y actuadores
            // - Número de dispositivos (conectados y desconectados)
            case TIPO_NODO_RED:
            {
                $consulta_clientes = "
                    SELECT
                        id,
                        nombre
                    FROM clientes";
                $res_clientes = $bd_red->ejecuta_consulta($consulta_clientes);
                $nombres_clientes = array();
                while ($fila_cliente = $res_clientes->dame_siguiente_fila())
                {
                    $nombres_clientes[$fila_cliente["id"]] = $fila_cliente["nombre"];
                }

                $consulta_dispositivos = "
                    SELECT
                        conexion,
                        red
                    FROM dispositivos";
                $res_dispositivos = $bd_red->ejecuta_consulta($consulta_dispositivos);
                $numeros_dispositivos_totales_redes = array();
                $numeros_dispositivos_conectados_redes = array();
                $numeros_dispositivos_desconectados_redes = array();
                while ($fila_dispositivo = $res_dispositivos->dame_siguiente_fila())
                {
                    $conexion = $fila_dispositivo["conexion"];
                    $id_red = $fila_dispositivo["red"];
                    if (array_key_exists($id_red, $numeros_dispositivos_totales_redes) == false)
                    {
                        $numeros_dispositivos_totales_redes[$id_red] = 0;
                        $numeros_dispositivos_conectados_redes[$id_red] = 0;
                        $numeros_dispositivos_desconectados_redes[$id_red] = 0;
                    }
                    $numeros_dispositivos_totales_redes[$id_red] += 1;
                    switch ($conexion)
                    {
                        case "ON":
                        {
                            $numeros_dispositivos_conectados_redes[$id_red] += 1;
                            break;
                        }
                        case "OFF":
                        case "FINISHED":
                        case "TIMEOUT":
                        {
                            $numeros_dispositivos_desconectados_redes[$id_red] += 1;
                            break;
                        }
                    }
                }

                $consulta_sensores = "
                    SELECT
                        COUNT(*) AS numero_sensores,
                        red
                    FROM sensores
                    GROUP BY red";
                $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                $numeros_sensores_redes = array();
                while ($fila_sensores = $res_sensores->dame_siguiente_fila())
                {
                    $numero_sensores = $fila_sensores["numero_sensores"];
                    $id_red = $fila_sensores["red"];
                    $numeros_sensores_redes[$id_red] = $numero_sensores;
                }

                $consulta_actuadores = "
                    SELECT
                        COUNT(*) AS numero_actuadores,
                        red
                    FROM actuadores
                    GROUP BY red";
                $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
                $numeros_actuadores_redes = array();
                while ($fila_actuadores = $res_actuadores->dame_siguiente_fila())
                {
                    $numero_actuadores = $fila_actuadores["numero_actuadores"];
                    $id_red = $fila_actuadores["red"];
                    $numeros_actuadores_redes[$id_red] = $numero_actuadores;
                }

                for ($i = 0; $i < count($filas_nodos); $i++)
                {
                    $id_red = $filas_nodos[$i]["id"];
                    $id_cliente = $filas_nodos[$i]["cliente"];
                    $nombre_cliente = $nombres_clientes[$id_cliente];
                    $filas_nodos[$i]["nombre_cliente"] = $nombre_cliente;
                    if (array_key_exists($id_red, $numeros_dispositivos_totales_redes) == true)
                    {
                        $numero_dispositivos_totales_red = $numeros_dispositivos_totales_redes[$id_red];
                        $numero_dispositivos_conectados_red = $numeros_dispositivos_conectados_redes[$id_red];
                        $numero_dispositivos_desconectados_red = $numeros_dispositivos_desconectados_redes[$id_red];
                    }
                    else
                    {
                        $numero_dispositivos_totales_red = 0;
                        $numero_dispositivos_conectados_red = 0;
                        $numero_dispositivos_desconectados_red = 0;
                    }
                    $filas_nodos[$i]["numero_dispositivos_totales"] = $numero_dispositivos_totales_red;
                    $filas_nodos[$i]["numero_dispositivos_conectados"] = $numero_dispositivos_conectados_red;
                    $filas_nodos[$i]["numero_dispositivos_desconectados"] = $numero_dispositivos_desconectados_red;
                    if (array_key_exists($id_red, $numeros_sensores_redes) == true)
                    {
                        $numero_sensores_red = $numeros_sensores_redes[$id_red];
                    }
                    else
                    {
                        $numero_sensores_red = 0;
                    }
                    $filas_nodos[$i]["numero_sensores"] = $numero_sensores_red;
                    if (array_key_exists($id_red, $numeros_actuadores_redes) == true)
                    {
                        $numero_actuadores_red = $numeros_actuadores_redes[$id_red];
                    }
                    else
                    {
                        $numero_actuadores_red = 0;
                    }
                    $filas_nodos[$i]["numero_actuadores"] = $numero_actuadores_red;
                }
                break;
            }
            // Se añaden los nombres de los grupos a los datos de las filas de los nodos (sensores y actuadores)
            case TIPO_NODO_SENSOR:
            case TIPO_NODO_ACTUADOR:
            {
                if ($tipo == TIPO_NODO_SENSOR)
                {
                    $nombre_tabla_grupos = "grupos_sensores";
                }
                if ($tipo == TIPO_NODO_ACTUADOR)
                {
                    $nombre_tabla_grupos = "grupos_actuadores";
                }
                $consulta_grupos = "
                    SELECT
                        id,
                        nombre
                    FROM ".$nombre_tabla_grupos."
                    WHERE
                        red = '".$_SESSION["id_red"]."'";
                $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
                $nombres_grupos = array();
                while ($fila_grupo = $res_grupos->dame_siguiente_fila())
                {
                    $nombres_grupos[$fila_grupo["id"]] = $fila_grupo["nombre"];
                }

                for ($i = 0; $i < count($filas_nodos); $i++)
                {
                    $id_grupo = $filas_nodos[$i]["grupo"];
                    if ($id_grupo == ID_NINGUNO)
                    {
                        $nombre_grupo = $idiomas->_("Ninguno");
                    }
                    else
                    {
                        $nombre_grupo = $nombres_grupos[$id_grupo];
                    }
                    $filas_nodos[$i]["nombre_grupo"] = $nombre_grupo;
                }
                break;
            }
        }
    }


    function dame_anyadir_nodo_tabla_nodos($tipo, $parametros_tipo_nodo, $fila_nodo)
    {
        $anyadir_nodo = true;
        switch ($tipo)
        {
            case TIPO_NODO_SENSOR:
            {
                $estado = $parametros_tipo_nodo["estado"];
                switch ($estado)
                {
                    case ESTADO_SENSOR_OPERACIONES_DATOS_PENDIENTES:
                    {
                        if (($fila_nodo["hay_importaciones_valores_pendientes"] == false) &&
                            ($fila_nodo["hay_recalculos_valores_clase_pendientes"] == false) &&
                            ($fila_nodo["ultimo_valor_antiguo_procesado"] == false))
                        {
                            $anyadir_nodo = false;
                        }
                        break;
                    }
                    case ESTADO_SENSOR_IMPORTACIONES_VALORES_PENDIENTES:
                    {
                        if ($fila_nodo["hay_importaciones_valores_pendientes"] == false)
                        {
                            $anyadir_nodo = false;
                        }
                        break;
                    }
                    case ESTADO_SENSOR_RECALCULOS_VALORES_CLASE_PENDIENTES:
                    {
                        if ($fila_nodo["hay_recalculos_valores_clase_pendientes"] == false)
                        {
                            $anyadir_nodo = false;
                        }
                        break;
                    }
                    case ESTADO_SENSOR_ULTIMOS_VALORES_ANTIGUOS_PROCESADO:
                    {
                        if ($fila_nodo["ultimo_valor_antiguo_procesado"] == false)
                        {
                            $anyadir_nodo = false;
                        }
                        break;
                    }
                }
                break;
            }
        }
        return ($anyadir_nodo);
    }


    function dame_texto_adicional_pie_tabla_nodos(
        $tipo,
        $filas_nodos,
        $numero_nodos_tabla,
        $mostrar_nodos_conectados_pie_tabla,
        $nodos_conectados,
        $nodos_desconectados)
    {
        $idiomas = new Idiomas();

        $texto_adicional_pie = "";
        switch ($tipo)
        {
            // Nota: Si el nodo es red, se muestran el número de sensores y actuadores (totales) de las redes
            case TIPO_NODO_RED:
            {
                $numero_sensores_redes = 0;
                $numero_actuadores_redes = 0;
                foreach ($filas_nodos as $fila_nodo)
                {
                    $numero_sensores_redes += $fila_nodo["numero_sensores"];
                    $numero_actuadores_redes += $fila_nodo["numero_actuadores"];
                }
                $texto_adicional_pie .= " (".$numero_sensores_redes." ".$idiomas->_("sensores").", ".
                    $numero_actuadores_redes." ".$idiomas->_("actuadores").")";
                break;
            }
            // Resto de tipos de nodos: Se muestran (si aplica) el número de nodos conectados y desconectados
            default:
            {
                if (($mostrar_nodos_conectados_pie_tabla == true) && ($numero_nodos_tabla > 0))
                {
                    if (($nodos_conectados > 0) || ($nodos_desconectados > 0))
                    {
                        $texto_adicional_pie .= " (";
                        if ($nodos_conectados > 0)
                        {
                            if ($nodos_conectados == 1)
                            {
                                $texto_nodos_conectados = $idiomas->_("conectado");
                            }
                            else
                            {
                                $texto_nodos_conectados = $idiomas->_("conectados");
                            }
                            $texto_adicional_pie .= $nodos_conectados." ".$texto_nodos_conectados;
                            if ($nodos_desconectados > 0)
                            {
                                $texto_adicional_pie .= ", ";
                            }
                        }
                        if ($nodos_desconectados > 0)
                        {
                            if ($nodos_desconectados == 1)
                            {
                                $texto_nodos_desconectados = $idiomas->_("no conectado");
                            }
                            else
                            {
                                $texto_nodos_desconectados = $idiomas->_("no conectados");
                            }
                            $texto_adicional_pie .= $nodos_desconectados." ".$texto_nodos_desconectados;
                        }
                        $texto_adicional_pie .= ")";
                    }
                    break;
                }
            }
        }
        return ($texto_adicional_pie);
    }


    //
    // Funciones de nodos
    //


    function dame_consulta_nodos($tipo, $filtro, $parametros_tipo_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $consulta = "
                    SELECT redes.*
                    FROM
                        redes,
                        clientes
                    WHERE
                        (redes.cliente = clientes.id)";
                if ($filtro != "")
                {
                    $consulta .= "
                        AND (".dame_condicion_consulta_filtro_redes($filtro).")";
                }
                $consulta .= "
                    ORDER BY
                        clientes.nombre ASC,
                        redes.nombre ASC";
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $consulta = "
                    SELECT *
                    FROM dispositivos
                    WHERE
                        red = '".$_SESSION["id_red"]."'
                    ORDER BY nombre ASC";
                break;
            }
            case TIPO_NODO_AXON:
            {
                $consulta = "
                    SELECT *
                    FROM axones
                    WHERE
                        red = '".$_SESSION["id_red"]."'
                    ORDER BY nombre ASC";
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                $consulta = "
                    SELECT *
                    FROM sensores
                    WHERE
                        (red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_filtro_sensores($filtro);
                }
                $tipo = $parametros_tipo_nodo["tipo"];
                if ($tipo != TIPO_TODOS)
                {
                    $consulta .= "
                        AND (tipo = '".$bd_red->_($tipo)."')";
                }
                $clase = $parametros_tipo_nodo["clase"];
                if ($clase != CLASE_TODAS)
                {
                    $consulta .= "
                        AND (clase = '".$bd_red->_($clase)."')";
                }
                $id_grupo = $parametros_tipo_nodo["id_grupo"];
                if ($id_grupo != ID_TODOS)
                {
                    $consulta .= "
                        AND (grupo = '".$bd_red->_($id_grupo)."')";
                }
                $estado = $parametros_tipo_nodo["estado"];
                $consulta .= dame_condicion_consulta_estado_sensores($estado);
                $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                if ($mostrar_todos_sensores == false)
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_sensores_usuario_actual(true);
                }
                $consulta .= "
                    ORDER BY nombre ASC";
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $consulta = "
                    SELECT *
                    FROM grupos_sensores
                    WHERE
                        (red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_filtro_grupos_sensores($filtro);
                }
                $clase = $parametros_tipo_nodo["clase"];
                if ($clase != CLASE_TODAS)
                {
                    $consulta .= "
                        AND (clase = '".$bd_red->_($clase)."')";
                }
                $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                if ($mostrar_todos_sensores == false)
                {
                    $consulta .=
                        "AND ".dame_condicion_consulta_grupos_sensores_usuario_actual(false);
                }
                $consulta .= "
                    ORDER BY nombre ASC";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $consulta = "
                    SELECT *
                    FROM actuadores
                    WHERE
                        (red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_filtro_actuadores($filtro);
                }
                $tipo = $parametros_tipo_nodo["tipo"];
                if ($tipo != TIPO_TODOS)
                {
                    $consulta .= "
                        AND (tipo = '".$bd_red->_($tipo)."')";
                }
                $clase = $parametros_tipo_nodo["clase"];
                if ($clase != CLASE_TODAS)
                {
                    $consulta .= "
                        AND (clase = '".$bd_red->_($clase)."')";
                }
                $id_grupo = $parametros_tipo_nodo["id_grupo"];
                if ($id_grupo != ID_TODOS)
                {
                    $consulta .= "
                        AND (grupo = '".$bd_red->_($id_grupo)."')";
                }
                $estado = $parametros_tipo_nodo["estado"];
                $consulta .= dame_condicion_consulta_estado_actuadores($estado);
                $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                if ($mostrar_todos_actuadores == false)
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_actuadores_usuario_actual(true);
                }
                $consulta .= "
                    ORDER BY nombre ASC";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $consulta = "
                    SELECT *
                    FROM grupos_actuadores
                    WHERE
                        (grupos_actuadores.red = '".$_SESSION["id_red"]."')";
                if ($filtro != "")
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_filtro_grupos_actuadores($filtro);
                }
                $clase = $parametros_tipo_nodo["clase"];
                if ($clase != CLASE_TODAS)
                {
                    $consulta .= "
                        AND (clase = '".$bd_red->_($clase)."')";
                }
                $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                if ($mostrar_todos_actuadores == false)
                {
                    $consulta .= "
                        AND ".dame_condicion_consulta_grupos_actuadores_usuario_actual(false);
                }
                $consulta .= "
                    ORDER BY nombre ASC";
                break;
            }
            default:
            {
                throw new Exception("Consulta no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        return ($consulta);
    }


    function dame_consulta_numero_nodos($tipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM redes";
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM dispositivos
                    WHERE
                        dispositivos.red = '".$_SESSION["id_red"]."'";
                break;
            }
            case TIPO_NODO_AXON:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM axones
                    WHERE
                        axones.red = '".$_SESSION["id_red"]."'";
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM sensores
                    WHERE
                        sensores.red = '".$_SESSION["id_red"]."'";
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM grupos_sensores
                    WHERE
                        grupos_sensores.red = '".$_SESSION["id_red"]."'";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM actuadores
                    WHERE
                        actuadores.red = '".$_SESSION["id_red"]."'";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $consulta = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM grupos_actuadores
                    WHERE
                        grupos_actuadores.red = '".$_SESSION["id_red"]."'";
                break;
            }
            default:
            {
                throw new Exception("Consulta no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        return ($consulta);
    }


    function dame_consulta_nodo($tipo, $id)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $consulta = "
                    SELECT *
                    FROM redes
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $consulta = "
                    SELECT *
                    FROM dispositivos
                    WHERE
                        dispositivos.id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_AXON:
            {
                $consulta = "
                    SELECT *
                    FROM axones
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                $consulta = "
                    SELECT *
                    FROM sensores
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $consulta = "
                    SELECT *
                    FROM grupos_sensores
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $consulta = "
                    SELECT *
                    FROM actuadores
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $consulta = "
                    SELECT *
                    FROM grupos_actuadores
                    WHERE
                        id = '".$bd_red->_($id)."'";
                break;
            }
            default:
            {
                throw new Exception("Consulta no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        return ($consulta);
    }


    function dame_id_nodo($tipo, $nombre, $id_red)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($tipo)
        {
            case TIPO_NODO_RED:
            {
                $consulta = "
                    SELECT
                        id
                    FROM redes
                    WHERE
                        nombre = '".$bd_red->_($nombre)."'";
                break;
            }
            case TIPO_NODO_DISPOSITIVO:
            {
                $consulta = "
                    SELECT
                        id
                    FROM dispositivos
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            case TIPO_NODO_AXON:
            {
                $consulta = "
                    SELECT
                        id
                    FROM axones
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            case TIPO_NODO_SENSOR:
            {
                $consulta = "
                    SELECT
                        id
                    FROM sensores
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $consulta = "
                    SELECT
                        id
                    FROM grupos_sensores
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $consulta = "
                    SELECT
                        id
                    FROM actuadores
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $consulta = "
                    SELECT
                        id
                    FROM grupos_actuadores
                    WHERE
                        (nombre = '".$bd_red->_($nombre)."')
                        AND (red = '".$bd_red->_($id_red)."')";
                break;
            }
            default:
            {
                throw new Exception("Consulta no implementada para este tipo de nodo: '".$tipo."'");
            }
        }

        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }

        $id = ID_NINGUNO;
        if ($res->dame_numero_filas() > 0)
        {
            $fila = $res->dame_siguiente_fila();
            $id = $fila["id"];
        }
        return ($id);
    }


    function dame_nodo($tipo, $id)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta = dame_consulta_nodo($tipo, $id);
        $res = $bd_red->ejecuta_consulta($consulta);
        if (($res == false) || ($res->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
        }
        $fila = $fila = $res->dame_siguiente_fila();

        $nodo = Nodo::crea_nodo($id, $tipo, $fila);
        return ($nodo);
    }


    //
    // Funciones de información de nodos
    //


    function dame_fila_dispositivo($id_dispositivo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_dispositivo = "
            SELECT *
            FROM dispositivos
            WHERE
                id = '".$bd_red->_($id_dispositivo)."'";
        $res_dispositivo = $bd_red->ejecuta_consulta($consulta_dispositivo);
        if (($res_dispositivo == false) || ($res_dispositivo->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_dispositivo."'");
        }
        $fila_dispositivo = $res_dispositivo->dame_siguiente_fila();
        return ($fila_dispositivo);
    }


    function dame_fila_axon($id_axon)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_axon = "
            SELECT *
            FROM axones
            WHERE
                id = '".$bd_red->_($id_axon)."'";
        $res_axon = $bd_red->ejecuta_consulta($consulta_axon);
        if (($res_axon == false) || ($res_axon->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_axon."'");
        }
        $fila_axon = $res_axon->dame_siguiente_fila();
        return ($fila_axon);
    }


    function dame_nombre_axon($id_axon)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_axon = "
            SELECT nombre
            FROM axones
            WHERE
                id = '".$bd_red->_($id_axon)."'";
        $res_axon = $bd_red->ejecuta_consulta($consulta_axon);
        if (($res_axon == false) || ($res_axon->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_axon."'");
        }
        $fila_axon = $res_axon->dame_siguiente_fila();
        $nombre_axon = $fila_axon["nombre"];
        return ($nombre_axon);
    }


    function dame_numero_dispositivos()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_numero_dispositivos = "
            SELECT
                COUNT(*) AS numero_dispositivos
            FROM dispositivos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_numero_dispositivos = $bd_red->ejecuta_consulta($consulta_numero_dispositivos);
        if ($res_numero_dispositivos == false)
        {
            throw new Exception("Ha ocurrido un error en la consulta: '".$consulta_numero_dispositivos."'");
        }

        $fila_numero_dispositivos = $res_numero_dispositivos->dame_siguiente_fila();
        $numero_dispositivos = $fila_numero_dispositivos["numero_dispositivos"];
        return ($numero_dispositivos);
    }


    //
    // Funciones de grupos de nodos
    //


    function dame_ids_grupos_nodos($tipo_nodos, $ids_nodos)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        if (count($ids_nodos) == 0)
        {
            return (array());
        }
        switch ($tipo_nodos)
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
                throw new Exception("Tipo de nodos incorrecto: '".$tipo_nodos."'");
            }
        }
        $cadena_ids_nodos = dame_cadena_ids_consulta($ids_nodos);
        $consulta_nodos = "
            SELECT
                DISTINCT(grupo)
            FROM ".$tabla_nodos."
            WHERE
                (id IN (".$bd_red->_($cadena_ids_nodos)."))
                AND (grupo <> ".ID_NINGUNO.")";
        $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
        if ($res_nodos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_nodos."'");
        }

        $ids_grupos_nodos = array();
        while ($fila_nodo = $res_nodos->dame_siguiente_fila())
        {
            array_push($ids_grupos_nodos, $fila_nodo["grupo"]);
        }
        return ($ids_grupos_nodos);
    }


    function dame_ids_nodos_grupos($tipo_nodo, $ids_grupos)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        if (count($ids_grupos) == 0)
        {
            return (array());
        }
        switch ($tipo_nodo)
        {
            case TIPO_NODO_GRUPO_SENSORES:
            {
                $tabla_nodos = "sensores";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $tabla_nodos = "actuadores";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
            }
        }
        $cadena_ids_grupos = dame_cadena_ids_consulta($ids_grupos);
        $consulta_nodos = "
            SELECT id
            FROM ".$tabla_nodos."
            WHERE
                grupo IN (".$bd_red->_($cadena_ids_grupos).")";
        $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
        if ($res_nodos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_nodos."'");
        }

        $ids_nodos_grupos = array();
        while ($fila_nodo = $res_nodos->dame_siguiente_fila())
        {
            array_push($ids_nodos_grupos, $fila_nodo["id"]);
        }
        return ($ids_nodos_grupos);
    }
?>
