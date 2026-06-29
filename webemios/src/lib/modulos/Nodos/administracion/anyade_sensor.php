<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_elementos_adicionales_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_SENSOR, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $visible_localizaciones_hijas = $_POST['visible_localizaciones_hijas'];
    $clase = $_POST['clase'];
    $cadena_parametros_clase = $_POST['parametros_clase'];
    $tipo = $_POST['tipo'];
    $cadena_parametros_tipo = $_POST['parametros_tipo'];
    $calibracion = $_POST['calibracion'];
    $tipo_valores = $_POST['tipo_valores'];
    $cambio_valores_puntuales = $_POST['cambio_valores_puntuales'];
    $incrementos_tiempo_real_horarios = $_POST['incrementos_tiempo_real_horarios'];
    $incrementos_negativos_validos = $_POST['incrementos_negativos_validos'];
    $guardar_valores_base_datos = $_POST['guardar_valores_base_datos'];
    $notificar_todos_eventos = $_POST['notificar_todos_eventos'];
    $granularidad_cuartohoraria = $_POST['granularidad_cuartohoraria'];
    $id_grupo = $_POST['id_grupo'];
    $frecuencia_muestreo = $_POST['frecuencia_muestreo'];
    $frecuencia_envio = $_POST['frecuencia_envio'];
    $mostrar_en_mapa = $_POST['mostrar_en_mapa'];
    $latitud_mapa = $_POST['latitud_mapa'];
    $longitud_mapa = $_POST['longitud_mapa'];
    $zoom_mapa = $_POST['zoom_mapa'];
    $id_sensor_anterior = $_POST["id_sensor_anterior"];

    // Parámetros de tipo y de clase de sensor
    $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
    $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);

    // Tipo de mensaje en la respuesta (correcta)
    $tipo_mensaje = TIPO_MENSAJE_INFORMACION;

    // Aviso en la respuesta
    $aviso = "";

	// Se comprueba si existen el número máximo de sensores
    $consulta_numero_sensores = "
        SELECT
            COUNT(*) AS numero_sensores
        FROM sensores
        WHERE
            red = '".$_SESSION["id_red"]."'";
    $res_numero_sensores = $bd_red->ejecuta_consulta($consulta_numero_sensores);
    if (($res_numero_sensores == false) || ($res_numero_sensores->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_sensores."'");
    }

    $fila_numero_sensores = $res_numero_sensores->dame_siguiente_fila();
    $numero_maximo_sensores = dame_numero_maximo_elementos_modulo(MODULO_SENSORES);
    if (($numero_maximo_sensores != 0) &&
        ($fila_numero_sensores['numero_sensores'] >= $numero_maximo_sensores))
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen el número máximo de sensores");
    }
    else
    {
        // Se comprueba si existe un sensor con el mismo nombre
        $consulta_existe = "
            SELECT nombre
            FROM sensores
            WHERE
                (nombre = '".$bd_red->_($nombre)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
        if ($res_existe == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_existe."'");
        }
        if ($res_existe->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un sensor con el mismo nombre");
        }
        else
        {
            // Comprobaciones antes de añadir el sensor:
            // - Si la clase de sensor es incremental y no tiene procesado, no puede ser tipo real ni virtual (sólo tiene valores incrementales)
            // - Si el sensor es de procesado, la clase de sensor tiene que tener procesado de valores
            // - Si el sensor es de energía activa o de gas, el CUPS debe ser único (si está definido)
            // - Si el sensor es de energía activa, el prefijo de validación de cierres debe ser único (si está definido)
            // - Si el sensor es de energía reactiva o de cortes de tensión, el sensor de energía activa asociado debe ser único (no asociado a otro sensor de la misma clase)
            // - Si el sensor es externo, el identificador externo debe ser único
            // - Si el sensor es externo y de ficheros CSV, el prefijo de fichero no puede estar incluído en otro prefijo (o viceversa)
            // - Localizaciones de grupo y de sensor correctas
            // - Adición de elementos adicionales posible
            $anyadir_sensor = true;

            // Características de clase de sensor
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
            $clase_tipo = $caracteristicas_clase_sensor["tipo"];
            $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];

            // Clase de sensor incremental
            if ($anyadir_sensor == true)
            {
                if (($clase_tipo == TIPO_CLASE_SENSOR_INCREMENTAL) && ($clase_procesado_valores == false))
                {
                    switch ($tipo)
                    {
                        case TIPO_SENSOR_REAL:
                        case TIPO_SENSOR_VIRTUAL:
                        {
                            $anyadir_sensor = false;

                            $res = "ERROR";
                            $msg = $idiomas->_("Los sensores de esta clase no pueden ser reales ni virtuales");
                            break;
                        }
                    }
                }
            }

            // Tipo procesado
            if ($anyadir_sensor == true)
            {
                switch ($tipo)
                {
                    case TIPO_SENSOR_PROCESADO:
                    {
                        if ($clase_procesado_valores == false)
                        {
                            $anyadir_sensor = false;

                            $res = "ERROR";
                            $msg = $idiomas->_("Los sensores de esta clase no pueden ser de procesado");
                        }
                        break;
                    }
                }
            }

            // Identificador de CUPS único
            if ($anyadir_sensor == true)
            {
                switch ($clase)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_GAS:
                    case CLASE_SENSOR_AGUA:
                    {
                        // Índice de CUPS en los parámetros de clase
                        $indice_parametro_clase_sensor_cups = dame_indice_parametro_clase_sensor_cups($clase);
                        if ($indice_parametro_clase_sensor_cups !== NULL)
                        {
                            $cups = $parametros_clase[$indice_parametro_clase_sensor_cups];
                            if ($cups != "")
                            {
                                // Se comprueba si existe un sensor con el mismo CUPS
                                $consulta_sensores_cups = "
                                    SELECT nombre
                                    FROM sensores
                                    WHERE
                                        (red = '".$_SESSION["id_red"]."')
                                        AND (clase = '".$clase."')
                                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametro_clase_sensor_cups + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($cups)."')";
                                $res_sensores_cups = $bd_red->ejecuta_consulta($consulta_sensores_cups);
                                if ($res_sensores_cups == false)
                                {
                                    throw new Exception("Error en la consulta: '".$consulta_sensores_cups."'");
                                }
                                if ($res_sensores_cups->dame_numero_filas() > 0)
                                {
                                    $anyadir_sensor = false;

                                    $fila_sensor_cups = $res_sensores_cups->dame_siguiente_fila();
                                    $nombre_sensor_cups = $fila_sensor_cups["nombre"];

                                    $res = "ERROR";
                                    $msg = $idiomas->_("Ya existe un sensor de la misma clase con este CUPS")."\n(".
                                        $nombre_sensor_cups.")";
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // Prefijo de fichero de validación de cierres único
            if ($anyadir_sensor == true)
            {
                switch ($clase)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        // Índice de prefijo de fichero de validación de cierres en los parámetros de clase
                        $indice_parametro_clase_sensor_tipo_fichero_validacion_facturas = dame_indice_parametro_clase_sensor_tipo_fichero_validacion_facturas($clase);
                        $indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas = dame_indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas($clase);
                        if ($indice_parametro_clase_sensor_tipo_fichero_validacion_facturas !== NULL)
                        {
                            $tipo_fichero_validacion_facturas = $parametros_clase[$indice_parametro_clase_sensor_tipo_fichero_validacion_facturas];
                            $prefijo_fichero_validacion_facturas = $parametros_clase[$indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas];
                            if ($tipo_fichero_validacion_facturas != TIPO_NINGUNO)
                            {
                                // Se comprueba si existe un sensor con el mismo prefijo
                                $consulta_sensores_energia_activa = "
                                    SELECT
                                        nombre,
                                        SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametro_clase_sensor_prefijo_fichero_validacion_facturas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS prefijo_fichero_validacion_facturas
                                    FROM sensores
                                    WHERE
                                        (red = '".$_SESSION["id_red"]."')
                                        AND (clase = '".$clase."')
                                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametro_clase_sensor_tipo_fichero_validacion_facturas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($tipo_fichero_validacion_facturas)."')
                                    ORDER BY nombre ASC";
                                $res_sensores_energia_activa = $bd_red->ejecuta_consulta($consulta_sensores_energia_activa);
                                while (($fila_sensor_energia_activa = $res_sensores_energia_activa->dame_siguiente_fila()) && ($anyadir_sensor == true))
                                {
                                    $prefijo_fichero_validacion_facturas_bucle = $fila_sensor_energia_activa["prefijo_fichero_validacion_facturas"];
                                    if ((strpos($prefijo_fichero_validacion_facturas, $prefijo_fichero_validacion_facturas_bucle) === 0) ||
                                        (strpos($prefijo_fichero_validacion_facturas_bucle, $prefijo_fichero_validacion_facturas) === 0))
                                    {
                                        $anyadir_sensor = false;

                                        $nombre_sensor_energia_activa = $fila_sensor_energia_activa["nombre"];

                                        $res = "ERROR";
                                        $msg = $idiomas->_("Existe un sensor de energía activa con un prefijo de fichero de validación de facturas y cierres incompatible")."\n(".
                                            $nombre_sensor_energia_activa.")";
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // Sensor de energía activa asociado único
            if ($anyadir_sensor == true)
            {
                switch ($clase)
                {
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    {
                        $id_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];//INDICE = 0
                        $tipo_energia_reacitva_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA]; //INDICE = 1
                        if ($id_sensor_energia_activa != ID_NINGUNO) //INDICE = -1
                        {
                            $consulta_sensor_energia_reactiva = "
                                SELECT nombre
                                FROM sensores
                                WHERE
                                    (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')
                                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($tipo_energia_reacitva_sensor)."')";
                            $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
                            if ($res_sensor_energia_reactiva == false)
                            {
                                throw new Exception("Error en la consulta: '".$consulta_sensor_energia_reactiva."'");
                            }

                            if ($res_sensor_energia_reactiva->dame_numero_filas() > 0)
                            {
                                $anyadir_sensor = false;

                                $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
                                $nombre_sensor_energia_reactiva = $fila_sensor_energia_reactiva["nombre"];
                                $res = "ERROR";

                                switch ($tipo_energia_reacitva_sensor) {
                                    case TIPO_ENERGIA_REACTIVA_Q1:
                                        $msg = $idiomas->_("Ya existe un sensor de reactiva inductiva con el mismo sensor de energía activa")."\n(".
                                        $nombre_sensor_energia_reactiva.")";
                                        break;

                                    case TIPO_ENERGIA_REACTIVA_Q2:
                                        $msg = $idiomas->_("Ya existe un sensor de reactiva Q2 con el mismo sensor de energía activa")."\n(".
                                        $nombre_sensor_energia_reactiva.")";
                                        break;

                                    case TIPO_ENERGIA_REACTIVA_Q3:
                                        $msg = $idiomas->_("Ya existe un sensor de reactiva Q3 con el mismo sensor de energía activa")."\n(".
                                        $nombre_sensor_energia_reactiva.")";
                                        break;

                                    case TIPO_ENERGIA_REACTIVA_Q4:
                                        $msg = $idiomas->_("Ya existe un sensor de reactiva capacitiva con el mismo sensor de energía activa")."\n(".
                                        $nombre_sensor_energia_reactiva.")";
                                        break;

                                    default:
                                        $msg = $idiomas->_("Ya existe un sensor de energía reactiva con el mismo sensor de energía activa")."\n(".
                                        $nombre_sensor_energia_reactiva.")";
                                        break;
                                }
                            }
                        }
                        break;
                    }
                    case CLASE_SENSOR_CORTES_TENSION:
                    {
                        $id_sensor_cortes_tension = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
                        if ($id_sensor_cortes_tension != ID_NINGUNO)
                        {
                            $consulta_sensor_cortes_tension = "
                                SELECT nombre
                                FROM sensores
                                WHERE
                                    (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')";
                            $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
                            if ($res_sensor_cortes_tension == false)
                            {
                                throw new Exception("Error en la consulta: '".$consulta_sensor_cortes_tension."'");
                            }

                            if ($res_sensor_cortes_tension->dame_numero_filas() > 0)
                            {
                                $anyadir_sensor = false;

                                $fila_sensor_cortes_tension = $res_sensor_cortes_tension->dame_siguiente_fila();
                                $nombre_sensor_cortes_tension = $fila_sensor_cortes_tension["nombre"];

                                $res = "ERROR";
                                $msg = $idiomas->_("Ya existe un sensor de cortes de tensión con el mismo sensor de energía activa")."\n(".
                                    $nombre_sensor_cortes_tension.")";
                            }
                        }
                        break;
                    }
                }
            }

            // Identificador de sensor externo único
            if ($anyadir_sensor == true)
            {
                switch ($tipo)
                {
                    case TIPO_SENSOR_EXTERNO:
                    {
                        // Id de sensor externo
                        $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];

                        // Se comprueba si existe un sensor externo con el mismo identificador externo
                        $consulta_existe = "
                            SELECT
                                nombre,
                                red
                            FROM sensores
                            WHERE
                                (tipo = '".TIPO_SENSOR_EXTERNO."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_externo)."')";
                        $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
                        if ($res_existe == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_existe."'");
                        }
                        if ($res_existe->dame_numero_filas() > 0)
                        {
                            $anyadir_sensor = false;

                            $fila_sensor_externo = $res_existe->dame_siguiente_fila();
                            $nombre_sensor_externo = $fila_sensor_externo["nombre"];
                            $id_red_sensor_externo = $fila_sensor_externo["red"];

                            $res = "ERROR";
                            if ($id_red_sensor_externo == $_SESSION["id_red"])
                            {
                                $msg = $idiomas->_("Ya existe un sensor externo con el mismo identificador externo")."\n(".
                                    $nombre_sensor_externo.")";
                            }
                            else
                            {
                                $msg = $idiomas->_("Ya existe un sensor externo con el mismo identificador externo en otra red");
                            }
                        }
                        break;
                    }
                }
            }

            // Prefijo de fichero no puede estar incluído en otro prefijo (o viceversa)
            if ($anyadir_sensor == true)
            {
                switch ($tipo)
                {
                    case TIPO_SENSOR_EXTERNO:
                    {
                        // Clase de sensor externo
                        $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                        if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_FICHEROS_CSV)
                        {
                            $cadena_opciones_generales = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                            $opciones_generales = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales);
                            $prefijo_fichero_csv = $opciones_generales[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PREFIJO_FICHERO];

                            // Se comprueba si existe un sensor con el mismo prefijo
                            $consulta_sensores_externos = "
                                SELECT
                                    nombre,
                                    red,
                                    SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) AS opciones_generales
                                FROM sensores
                                WHERE
                                    (tipo = '".TIPO_SENSOR_EXTERNO."')
                                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".CLASE_SENSOR_EXTERNO_FICHEROS_CSV."')
                                ORDER BY nombre ASC";
                            $res_sensores_externos = $bd_red->ejecuta_consulta($consulta_sensores_externos);
                            while (($fila_sensor_externo = $res_sensores_externos->dame_siguiente_fila()) && ($anyadir_sensor == true))
                            {
                                $cadena_opciones_generales_bucle = $fila_sensor_externo["opciones_generales"];
                                $opciones_generales_bucle = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales_bucle);
                                $prefijo_fichero_csv_bucle = $opciones_generales_bucle[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PREFIJO_FICHERO];
                                if (($prefijo_fichero_csv != $prefijo_fichero_csv_bucle) &&
                                    ((strpos($prefijo_fichero_csv, $prefijo_fichero_csv_bucle) === 0) ||
                                    (strpos($prefijo_fichero_csv_bucle, $prefijo_fichero_csv) === 0)))
                                {
                                    $anyadir_sensor = false;

                                    $nombre_sensor_externo = $fila_sensor_externo["nombre"];
                                    $id_red_sensor_externo = $fila_sensor_externo["red"];

                                    $res = "ERROR";
                                    if ($id_red_sensor_externo == $_SESSION["id_red"])
                                    {
                                        $msg = $idiomas->_("Existe un sensor externo con un prefijo de fichero CSV incompatible")."\n(".
                                            $nombre_sensor_externo.")";
                                    }
                                    else
                                    {
                                        $msg = $idiomas->_("Existe un sensor externo con un prefijo de fichero CSV incompatible en otra red");
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }

            // Comprobación de localizaciones correctas
            if ($anyadir_sensor == true)
            {
                if ($id_grupo != ID_NINGUNO)
                {
                    $localizaciones_correctas = dame_localizaciones_correctas_grupo_localizacion_nodo(TIPO_NODO_SENSOR, $id_grupo, $id_localizacion);
                    if ($localizaciones_correctas == false)
                    {
                        $anyadir_sensor = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Las localizaciones del sensor y del grupo no son correctas");
                    }
                }
            }

            // Se comprueba si se pueden añadir los elementos adicionales según la clase de sensor
            if ($anyadir_sensor == true)
            {
                $msg = "";
                $posible_anyadir_elementos_adicionales_sensor = dame_posible_anyadir_elementos_adicionales_clase_sensor(
                    $nombre_sensor,
                    $clase,
                    $parametros_clase,
                    $msg,
                    $aviso);
                if ($posible_anyadir_elementos_adicionales_sensor == false)
                {
                    $anyadir_sensor = false;

                    $res = "ERROR";
                }
            }

            // Se añade el sensor
            if ($anyadir_sensor == true)
            {
                // Se añade el sensor
                $operacion_insercion = "
                    INSERT INTO sensores (
                        nombre,
                        red,
                        descripcion,
                        tipo,
                        parametros_tipo,
                        clase,
                        parametros_clase,
                        orden,
                        grupo,
                        localizacion,
                        visible_localizaciones_hijas,
                        frecuencia_muestreo,
                        frecuencia_envio,
                        calibracion,
                        tipo_valores,
                        cambio_valores_puntuales,
                        incrementos_tiempo_real_horarios,
                        incrementos_negativos_validos,
                        guardar_valores_base_datos,
                        notificar_todos_eventos,
                        granularidad_cuartohoraria,
                        administrable,
                        hora_ultimos_valores,
                        ultimos_valores,
                        eventos_activados,
                        eventos_alarma_activados,
                        hora_ultimos_valores_clase_cuartoshora,
                        ultimos_valores_clase_cuartoshora,
                        eventos_activados_clase_cuartoshora,
                        eventos_alarma_activados_clase_cuartoshora,
                        hora_ultimos_valores_clase_horas,
                        ultimos_valores_clase_horas,
                        eventos_activados_clase_horas,
                        eventos_alarma_activados_clase_horas,
                        hora_timeout_envio,
                        timeout_envio,
                        ultimo_error_valores_tiempo_real_json,
                        ultimo_error_valores_horarios_json,
                        ultimo_error_valores_cuartohorarios_json,
                        ultimo_error_valores_clase_horarios_json,
                        ultimo_error_valores_clase_cuartohorarios_json
                    ) VALUES (
                        '".$bd_red->_($nombre)."',
                        '".$_SESSION["id_red"]."',
                        '".$bd_red->_($descripcion)."',
                        '".$bd_red->_($tipo)."',
                        '".$bd_red->_($cadena_parametros_tipo)."',
                        '".$bd_red->_($clase)."',
                        '".$bd_red->_($cadena_parametros_clase)."',
                        '0',
                        '".$bd_red->_($id_grupo)."',
                        '".$bd_red->_($id_localizacion)."',
                        '".$bd_red->_($visible_localizaciones_hijas)."',
                        '".$bd_red->_($frecuencia_muestreo)."',
                        '".$bd_red->_($frecuencia_envio)."',
                        '".$bd_red->_($calibracion)."',
                        '".$bd_red->_($tipo_valores)."',
                        '".$bd_red->_($cambio_valores_puntuales)."',
                        '".$bd_red->_($incrementos_tiempo_real_horarios)."',
                        '".$bd_red->_($incrementos_negativos_validos)."',
                        '".$bd_red->_($guardar_valores_base_datos)."',
                        '".$bd_red->_($notificar_todos_eventos)."',
                        '".$bd_red->_($granularidad_cuartohoraria)."',
                        '".VALOR_SI."',
                        NULL,
                        NULL,
                        '',
                        '".VALOR_NO."',
                        NULL,
                        NULL,
                        '',
                        '".VALOR_NO."',
                        NULL,
                        NULL,
                        '',
                        '".VALOR_NO."',
                        NULL,
                        '".VALOR_NO."',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )";
                $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);                
                if ($res_insercion == true)
                {
                    // Se recupera el id del sensor añadido
                    $id_sensor = $bd_red->dame_id_autoincremental_ultima_insercion();

                    // Se guarda la información de la posición en el mapa
                    if ($mostrar_en_mapa == VALOR_SI)
                    {
                        // Se recupera el origen del mapa 'final'
                        $parametros_origen_mapa = array("modulo" => MODULO_SENSORES);
                        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
                        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
                        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

                        // Se guarda la información de la posición en el mapa en base de datos
                        $info_posicion_mapa = array(
                            "tipo_elemento" => TIPO_ELEMENTO_MAPA_SENSOR,
                            "id_elemento" => $id_sensor,
                            "origen" => $origen_mapa,
                            "id_origen" => $id_origen_mapa,
                            "latitud" => $latitud_mapa,
                            "longitud" => $longitud_mapa,
                            "zoom" => $zoom_mapa);
                        guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
                    }
                    else
                    {
                        $info_posicion_mapa = NULL;
                    }

                    // Si el identificador de sensor existe, es un duplicado de un sensor existente:
                    // - Se duplican los hijos (si los hay)
                    if ($id_sensor_anterior != ID_NINGUNO)
                    {
                        // Duplica los hijos del sensor anterior
                        switch ($tipo)
                        {
                            case TIPO_SENSOR_VIRTUAL:
                            case TIPO_SENSOR_PROCESADO:
                            {
                                duplica_hijos_sensor_anterior(
                                    $id_sensor_anterior,
                                    $id_sensor,
                                    $tipo,
                                    $parametros_tipo,
                                    $clase);
                                break;
                            }
                        }
                    }

                    // Se añaden los elementos adicionales según la clase de sensor
                    anyade_elementos_adicionales_clase_sensor(
                        $id_sensor,
                        $nombre,
                        $clase,
                        $parametros_clase);

                    // Se recupera la fila del sensor añadido
                    // (se recupera después de añadir los elementos adicionales de clase de sensor,
                    //  porque se pueden haber modificado los parámetros de clase)
                    $fila_sensor = dame_fila_sensor($id_sensor);

                    // Acciones a realizar al añadir un sensor
                    realiza_acciones_sensor_anyadido($id_sensor, $fila_sensor);

                    // Se añade la acción de usuario
                    anyade_accion_usuario_anyadir_sensor($fila_sensor, $info_posicion_mapa);

                    // Si el sensor es de tipo API, se envía una peticion a la API de emios
                    // externa para notificar al servicio.

                    if ($tipo==TIPO_SENSOR_EXTERNO)
                    {
                        // Se comprueba si es de tipo API
                        $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                        if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_API)
                        {
                            $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                            $opciones_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
                            $api_seleccionada = $opciones_valores[0];


                            switch ($api_seleccionada) {
                                case API_AXONTIME:

                                    anyade_sensor_axontime($parametros_tipo, $opciones_valores,$nombre);
                                    break;

                                case API_SGCLIMA:

                                    anyade_sensor_sgclima($parametros_tipo,$opciones_valores,$nombre,$clase);
                                    break;

                                default:
                                    $msg = "ERROR, tipo de API desconocida";
                                    break;
                            }
                        }
                        if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_FICHEROS_CSV)
                        {
                            // Si es de CSV y de DATADIS
                            // se notifica al servicio externo
                            $cadena_opciones_generales = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                            $opciones_generales = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales);
                            if ($opciones_generales[1] == "datadis")
                            {
                                $cadena_opciones_valores_datadis = $parametros_tipo[4];
                                $opciones_valores_datadis = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_datadis);
                                anyade_sensor_datadis($parametros_tipo, $opciones_valores_datadis, $nombre);
                            }
                        }
                    }


                    // Adición correcta
                    $res = "OK";
                    $msg = $idiomas->_("Sensor añadido correctamente");

                    // Comprobación de ubicación del sensor real
                    switch ($tipo)
                    {
                        case TIPO_SENSOR_REAL:
                        {
                            $aviso = dame_aviso_comprobacion_ubicacion_sensor_real($id_sensor, $parametros_tipo);
                            break;
                        }
                    }

                    // Aviso en el mensaje de adición
                    if ($aviso != "")
                    {
                        $tipo_mensaje = TIPO_MENSAJE_AVISO;
                        $msg .= "\n(".$aviso.")";
                    }
                }
                else
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion."'");
                }
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "tipo_mensaje" => $tipo_mensaje,
        "id_sensor" => $id_sensor))
    );


    //
    // Funciones auxiliares
    //


    // Duplica los hijos del sensor anterior
    function duplica_hijos_sensor_anterior(
        $id_sensor_anterior,
        $id_sensor,
        $tipo,
        $parametros_tipo,
        $clase)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la información del sensor anterior
        $fila_sensor_anterior = dame_fila_sensor($id_sensor_anterior);
        $tipo_anterior = $fila_sensor_anterior["tipo"];
        $clase_anterior = $fila_sensor_anterior["clase"];
        $parametros_tipo_anterior = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor_anterior["parametros_tipo"]);
        if ($tipo != $tipo_anterior)
        {
            return;
        }

        // Si el sensor es virtual y ha cambiado la clase de sensor o la clase de sensor virtual, no se duplican los hijos
        switch ($tipo)
        {
            case TIPO_SENSOR_VIRTUAL:
            {
                $clase_virtual = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
                $clase_virtual_anterior = $parametros_tipo_anterior[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
                if (($clase != $clase_anterior) ||
                    ($clase_virtual != $clase_virtual_anterior))
                {
                    return;
                }
                break;
            }
        }

        // Se recorren los hijos del sensor anterior, se cambia el sensor padre y se añaden
        $consulta_hijos = "
            SELECT *
            FROM hijos_sensores
            WHERE
                sensor_padre = '".$bd_red->_($id_sensor_anterior)."'";
        $res_hijos = $bd_red->ejecuta_consulta($consulta_hijos);
        if ($res_hijos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_hijos."'");
        }

        while ($fila_hijo = $res_hijos->dame_siguiente_fila())
        {
            $operacion_insercion_hijo = "
                INSERT INTO hijos_sensores (
                    red,
                    sensor_padre,
                    sensor_hijo,
                    parametros_tipo
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_sensor)."',
                    '".$bd_red->_($fila_hijo["sensor_hijo"])."',
                    '".$bd_red->_($fila_hijo["parametros_tipo"])."'
                )";
            $res_insercion_hijo = $bd_red->ejecuta_operacion($operacion_insercion_hijo);
            if ($res_insercion_hijo == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_hijo."'");
            }
        }
    }


    // Añade la acción de usuario de adición del sensor
    function anyade_accion_usuario_anyadir_sensor($fila, $info_posicion_mapa)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_SENSOR;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_sensores($fila["grupo"]);
        $nombre_localizacion = dame_nombre_localizacion($fila["localizacion"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila["visible_localizaciones_hijas"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase"];
        if ($fila["parametros_clase"] != "")
        {
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_clase"]);
            sustituye_ids_nombres_parametros_clase_sensor_accion_usuario($fila["clase"], $parametros_clase);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_SENSOR] = array(
                "clase" => $fila["clase"],
                "parametros_clase" => $parametros_clase);
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_SENSOR] = $fila["tipo"];
        if ($fila["parametros_tipo"] != "")
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_tipo"]);
            sustituye_ids_nombres_parametros_tipo_sensor_accion_usuario($fila["tipo"], $parametros_tipo);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_SENSOR] = array(
                "tipo" => $fila["tipo"],
                "parametros_tipo" => $parametros_tipo);
        }
        if (NodoSensor::dame_mostrar_calibracion($fila["tipo"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila["calibracion"];
        }
        if (NodoSensor::dame_mostrar_tipo_valores($fila["tipo"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_VALORES] = $fila["tipo_valores"];
        }
        if (NodoSensor::dame_mostrar_cambio_valores_puntuales($fila["clase"], $fila["tipo_valores"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMBIO_VALORES_PUNTUALES] = $fila["cambio_valores_puntuales"];
        }
        if (NodoSensor::dame_mostrar_incrementos_tiempo_real_horarios($fila["tipo"], $fila["clase"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCREMENTOS_TIEMPO_REAL_HORARIOS] = $fila["incrementos_tiempo_real_horarios"];
        }
        if (NodoSensor::dame_mostrar_incrementos_negativos_validos($fila["clase"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCREMENTOS_NEGATIVOS_VALIDOS] = $fila["incrementos_negativos_validos"];
        }
        if (NodoSensor::dame_mostrar_granularidad_cuartohoraria($fila["clase"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_GRANULARIDAD_CUARTOHORARIA] = $fila["granularidad_cuartohoraria"];
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        if (NodoSensor::dame_mostrar_guardar_valores_base_datos($fila["tipo"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_GUARDAR_VALORES_BASE_DATOS] = $fila["guardar_valores_base_datos"];
        }
        if (NodoSensor::dame_mostrar_notificar_todos_eventos($fila["tipo"], $fila["clase"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOTIFICAR_TODOS_EVENTOS] = $fila["notificar_todos_eventos"];
        }
        if (NodoSensor::dame_mostrar_frecuencia_muestreo($fila["tipo"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FRECUENCIA_MUESTREO] = $fila["frecuencia_muestreo"];
        }
        if (NodoSensor::dame_mostrar_frecuencia_envio($fila["tipo"]) == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FRECUENCIA_ENVIO] = $fila["frecuencia_envio"];
        }

        // Información de posición en mapa
        anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }

    // Anyade el los sensores en los microservicios externos a EMIOS
    function anyade_sensor_axontime($parametros_tipo, $opciones_valores,$nombre)
    {
        $direccion_api_externa = API_EXTERNA_SENSORES_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
        $cups_id = $opciones_valores[1];
        $telemedida = $opciones_valores[2];
        $campo_lectura = $opciones_valores[3];
        $ip_servidor = file_get_contents('https://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SENSORES_DIRECCION.'/anyadir_sensor_axon';

        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);

        // Dependiendo de si es activa o reactiva
        // ataca con distintos metodos POST o PUT
        // ademas cambia el nombre de uno de los
        // parametros
        switch ($campo_lectura) {
            case 'energia':
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&nombre_activa='.$nombre_url.'&id_externo='.$id_sensor_externo.'&telemedida='.$telemedida.'&red='.$_SESSION['id_red'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_HTTPHEADER => array(
                      'token:'.$token),
                ));
                break;

            case 'ie1q':
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&nombre_inductiva='.$nombre_url.'&id_externo='.$id_sensor_externo.'&telemedida='.$telemedida.'&red='.$_SESSION['id_red'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'PUT',
                  CURLOPT_HTTPHEADER => array(
                      'token:'.$token),
                ));
                break;

            case 'ce4q':
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url.'?cups_id='.$cups_id.'&ip_servidor='.$ip_servidor.'&nombre_capacitiva='.$nombre_url.'&id_externo='.$id_sensor_externo.'&telemedida='.$telemedida.'&red='.$_SESSION['id_red'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_HTTPHEADER => array(
                        'token:'.$token),
                  ));
                break;

            default:
                return;
                break;
        }

        $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return;
            }
          }
        curl_close($curl);

    }

    function anyade_sensor_sgclima($parametros_tipo, $opciones_valores,$nombre,$clase)
    {
        $direccion_api_externa = API_EXTERNA_SGCLIMA_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
        $usuario = $opciones_valores[1];
        $password = $opciones_valores[2];
        $id_loc = $opciones_valores[3];
        $id_param = $opciones_valores[4];
        $ip_servidor = file_get_contents('https://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SGCLIMA_DIRECCION.'/anyadir_sensor_sgclima';

        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre='.$nombre_url.'&ip_servidor='.$ip_servidor.'&id_localizacion='.$id_loc.
                            '&ps_id='.$id_param.'&usuario='.$usuario.'&pass='.$password.'&red='.$_SESSION['id_red'].
                            '&tipo_dato='.$clase.'&id_externo='.$id_sensor_externo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'token:'.$token),
          ));

        $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return;
            }
          }
        curl_close($curl);
    }

    function anyade_sensor_datadis($parametros_tipo, $opciones_valores, $nombre)
    {
        $direccion_api_externa = API_EXTERNA_SENSORES_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
        $cups = $opciones_valores[0];
        $distributorCode = $opciones_valores[1];
        $measurementType = $opciones_valores[2];
        $pointType = $opciones_valores[3];
        $authorizedNif = $opciones_valores[4];
        $ip_servidor = file_get_contents('https://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SENSORES_DIRECCION.'/datadis/anyadir_sensor';

        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre_sensor='.$nombre_url.'&ip_servidor='.$ip_servidor.'&cups='.$cups.
                            '&distributor_code='.$distributorCode.'&measurement_type='.$measurementType.
                            '&point_type='.$pointType.'&authorized_nif='.$authorizedNif.'&red='.$_SESSION['id_red'].
                            '&id_externo='.$id_sensor_externo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'token:'.$token),
          ));

        $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return;
            }
          }
        curl_close($curl);
    }

    function obtiene_token_api($direccion_api_externa)
    {
        // Primero se obtiene el token para autenticarse en la API
        $curl = curl_init();
        $url = $direccion_api_externa.'/login';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?usuario='.API_EXTERNA_SENSORES_USUARIO.'&password='.API_EXTERNA_SENSORES_PASS,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST'
        ));
        $response = curl_exec($curl);
        $data = json_decode($response,true);
        curl_close($curl);
        $token = $data['token'];
        return $token;
    }
?>
