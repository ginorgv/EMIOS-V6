<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_elementos_adicionales_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_SENSOR, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_sensor = $_POST['id_sensor'];
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

    // Parámetros auxiliares
    $parametros_auxiliares = $_POST['parametros_auxiliares'];

    // Parámetros de tipo y de clase de sensor
    $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
    $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);

    // Tipo de mensaje en la respuesta (correcta)
    $tipo_mensaje = TIPO_MENSAJE_INFORMACION;

    // Mensaje extra en la respuesta y cerrar ventana de modificación
    $aviso = "";
    $msg_extra = NULL;
    $cerrar_ventana = true;

    // Obtenemos el log
    $log = dame_log();

    // Se comprueba si existe otro sensor con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM sensores
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_sensor)."')";
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
        // Comprobaciones antes de modificar el sensor:
        // - Si la clase de sensor es incremental y no tiene procesado, no puede ser tipo real ni virtual (sólo tiene valores incrementales)
        // - Si el sensor es de energía activa o de gas, el CUPS debe ser único (si está definido)
        // - Si el sensor es de energía activa, el prefijo de validación de cierres debe ser único (si está definido)
        // - Si el sensor es de energía reactiva o de cortes de tensión, el sensor de energía activa asociado debe ser único (no asociado a otro sensor de la misma clase)
        // - Si el sensor es externo, el identificador externo debe ser único
        // - Si el sensor es externo y de ficheros CSV, el prefijo de fichero no puede estar incluído en otro prefijo (o viceversa)
        // - Si el sensor es de procesado, se validan las fórmulas de cálculo de valores
        // - Localizaciones de grupo y de sensor correctas
        // - Modificación de elementos adicionales posible
        $modificar_sensor = true;

        // Características de clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
        $clase_tipo = $caracteristicas_clase_sensor["tipo"];
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
        $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

        // Clase de sensor incremental
        if ($modificar_sensor == true)
        {
            if (($clase_tipo == TIPO_CLASE_SENSOR_INCREMENTAL) && ($clase_procesado_valores == false))
            {
                switch ($tipo)
                {
                    case TIPO_SENSOR_REAL:
                    case TIPO_SENSOR_VIRTUAL:
                    {
                        $modificar_sensor = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Los sensores de esta clase no pueden ser reales ni virtuales");
                        break;
                    }
                }
            }
        }

        // Identificador de CUPS único
        if ($modificar_sensor == true)
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
                            // Se comprueba si existe otro sensor con el mismo CUPS
                            $consulta_sensores_cups = "
                                SELECT nombre
                                FROM sensores
                                WHERE
                                    (red = '".$_SESSION["id_red"]."')
                                    AND (clase = '".$clase."')
                                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametro_clase_sensor_cups + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($cups)."')
                                    AND (id <> '".$bd_red->_($id_sensor)."')";
                            $res_sensores_cups = $bd_red->ejecuta_consulta($consulta_sensores_cups);
                            if ($res_sensores_cups == false)
                            {
                                throw new Exception("Error en la consulta: '".$consulta_sensores_cups."'");
                            }
                            if ($res_sensores_cups->dame_numero_filas() > 0)
                            {
                                $modificar_sensor = false;

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
        if ($modificar_sensor == true)
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
                                    AND (id <> '".$bd_red->_($id_sensor)."')
                                ORDER BY nombre ASC";
                            $res_sensores_energia_activa = $bd_red->ejecuta_consulta($consulta_sensores_energia_activa);
                            while (($fila_sensor_energia_activa = $res_sensores_energia_activa->dame_siguiente_fila()) && ($modificar_sensor == true))
                            {
                                $prefijo_fichero_validacion_facturas_bucle = $fila_sensor_energia_activa["prefijo_fichero_validacion_facturas"];
                                if ((strpos($prefijo_fichero_validacion_facturas, $prefijo_fichero_validacion_facturas_bucle) === 0) ||
                                    (strpos($prefijo_fichero_validacion_facturas_bucle, $prefijo_fichero_validacion_facturas) === 0))
                                {
                                    $modificar_sensor = false;

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
        if ($modificar_sensor == true)
        {
            switch ($clase)
            {
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $id_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
                    $tipo_energia_reacitva_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA]; //INDICE = 1
                    if ($id_sensor_energia_activa != ID_NINGUNO)
                    {
                        $consulta_sensor_energia_reactiva = "
                            SELECT nombre
                            FROM sensores
                            WHERE
                                (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($tipo_energia_reacitva_sensor)."')
                                AND (id <> '".$bd_red->_($id_sensor)."')";
                        $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
                        if ($res_sensor_energia_reactiva == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_sensor_energia_reactiva."'");
                        }

                        if ($res_sensor_energia_reactiva->dame_numero_filas() > 0)
                        {
                            $modificar_sensor = false;

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
                        $consulta_sensor_energia_activa = "
                            SELECT parametros_tipo
                            FROM sensores
                            WHERE
                                id = ".$id_sensor_energia_activa;

                        $res_sensor_energia_activa_asociado = $bd_red->ejecuta_consulta($consulta_sensor_energia_activa);

                        $fila_sensor_activa_asociado = $res_sensor_energia_activa_asociado->dame_siguiente_fila();
                        $parametros_tipo_activa_asociado = $fila_sensor_activa_asociado['parametros_tipo'];
                        $parametros_separados = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_activa_asociado);
                        $tipo_sensor_activa_asociado = $parametros_separados[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];

                        $tipo_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                        // Se obtiene el tipo de Api del sensor de activa
                        $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                        $opciones_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
                        $tipo_api_activa_asociado = $opciones_valores[0];

			$log->info("Tipo sensor anterior: ".$tipo_sensor_activa_asociado." Tipo sensor nuevo: ".$tipo_sensor_externo);

                        if (($tipo_sensor_externo == CLASE_SENSOR_EXTERNO_API) && ($tipo_sensor_activa_asociado != $tipo_sensor_externo))
                        {
                            $modificar_sensor = false;
                            $res = "ERROR";
                            $msg = $idiomas->_("El sensor de energía activa asociado no es de la misma clase");
                        }
                        if ($tipo_sensor_activa_asociado == CLASE_SENSOR_EXTERNO_API)
                        {
                            if ($tipo_api_activa_asociado == API_AXONTIME)
                            {
                                // Obtencion del CUPS id del sensor que se esta modificando
                                $cups_id = $opciones_valores[1];

                                // Obtencion del CUPS id del sensor de activa asociado
                                $cadena_opciones_valores_activa = $parametros_separados[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                                $opciones_valores_activa = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_activa);
                                $cups_id_activa_asociado = $opciones_valores_activa[1];

                                if ($cups_id_activa_asociado != $cups_id)
                                {
                                    $log->warn("Se está intentando modificar un sensor de reactiva antes que el de activa");
                                    $modificar_sensor = false;
                                    $res = "ERROR";
                                    $msg = $idiomas->_("El sensor de energía activa asociado no tiene el mismo CUPS.\nModifica ese primero");
                                }

                            }
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION:
                {
                    $id_sensor_energia_activa = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
                    if ($id_sensor_energia_activa != ID_NINGUNO)
                    {
                        $consulta_sensor_cortes_tension = "
                            SELECT nombre
                            FROM sensores
                            WHERE
                                (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')
                                AND (id <> '".$bd_red->_($id_sensor)."')";
                        $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
                        if ($res_sensor_cortes_tension == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_sensor_cortes_tension."'");
                        }

                        if ($res_sensor_cortes_tension->dame_numero_filas() > 0)
                        {
                            $modificar_sensor = false;

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
        if ($modificar_sensor == true)
        {
            switch ($tipo)
            {
                case TIPO_SENSOR_EXTERNO:
                {
                    // Id de sensor externo
                    $id_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];

                    // Se comprueba si existe otro sensor externo con el mismo identificador externo
                    $consulta_existe = "
                        SELECT
                            nombre,
                            red
                        FROM sensores
                        WHERE
                            (tipo = '".TIPO_SENSOR_EXTERNO."')
                            AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_externo)."')
                            AND (id <> '".$bd_red->_($id_sensor)."')";
                    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
                    if ($res_existe == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_existe."'");
                    }
                    if ($res_existe->dame_numero_filas() > 0)
                    {
                        $modificar_sensor = false;

                        $fila_sensor_externo = $res_existe->dame_siguiente_fila();
                        $nombre_sensor_externo = $fila_sensor_externo["nombre"];
                        $id_red_sensor_externo = $fila_sensor_externo["red"];

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
        if ($modificar_sensor == true)
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
                                AND (id <> '".$bd_red->_($id_sensor)."')
                            ORDER BY nombre ASC";
                        $res_sensores_externos = $bd_red->ejecuta_consulta($consulta_sensores_externos);
                        while (($fila_sensor_externo = $res_sensores_externos->dame_siguiente_fila()) && ($modificar_sensor == true))
                        {
                            $cadena_opciones_generales_bucle = $fila_sensor_externo["opciones_generales"];
                            $opciones_generales_bucle = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales_bucle);
                            $prefijo_fichero_csv_bucle = $opciones_generales_bucle[INDICE_PARAMETRO_CLASE_SENSOR_EXTERNO_FICHEROS_CSV_OPCIONES_GENERALES_PREFIJO_FICHERO];
                            if (($prefijo_fichero_csv != $prefijo_fichero_csv_bucle) &&
                                ((strpos($prefijo_fichero_csv, $prefijo_fichero_csv_bucle) === 0) ||
                                (strpos($prefijo_fichero_csv_bucle, $prefijo_fichero_csv) === 0)))
                            {
                                $modificar_sensor = false;

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

        // Si los datos son correctos se evalua la función de procesado
        if (($modificar_sensor == true) && ($tipo == TIPO_SENSOR_PROCESADO))
        {
            $clase_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO];
            if ($clase_sensor_procesado == CLASE_SENSOR_PROCESADO_FUNCION_VALORES)
            {
                $funcion_valores_horaria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_HORARIA];
                $misma_funcion_valores_cuartohoraria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_MISMA_FUNCION_VALORES_CUARTOHORARIA];
                $funcion_valores_cuartohoraria_sensor_procesado = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_CUARTOHORARIA];
                if (($funcion_valores_horaria_sensor_procesado != "") ||
                    (($misma_funcion_valores_cuartohoraria_sensor_procesado == VALOR_NO) && ($funcion_valores_cuartohoraria_sensor_procesado != "")))
                {
                    // Se recuperan los hijos del sensor de procesado (para evaluar las funciones de valores)
                    $consulta_hijos_sensor_procesado = "
                        SELECT
                            hijos_sensores.sensor_hijo AS sensor_hijo,
                            sensores.nombre AS nombre_sensor,
                            hijos_sensores.parametros_tipo AS parametros_tipo
                        FROM
                            hijos_sensores,
                            sensores
                        WHERE
                            (hijos_sensores.sensor_padre = '".$bd_red->_($id_sensor)."')
                            AND (hijos_sensores.sensor_hijo = sensores.id)
                        ORDER BY nombre_sensor ASC";
                    $res_hijos_sensor_procesado = $bd_red->ejecuta_consulta($consulta_hijos_sensor_procesado);
                    if ($res_hijos_sensor_procesado == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_hijos_sensor_procesado."'");
                    }
                    $numero_hijos_sensor_procesado = $res_hijos_sensor_procesado->dame_numero_filas();
                    $nombres_variables_hijos_sensor_procesado = array();
                    while ($fila_hijo_sensor_procesado = $res_hijos_sensor_procesado->dame_siguiente_fila())
                    {
                        $parametros_hijo_sensor_procesado = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_hijo_sensor_procesado['parametros_tipo']);
                        $nombre_variable_hijo_sensor_procesado = $parametros_hijo_sensor_procesado[INDICE_PARAMETRO_HIJO_SENSOR_PROCESADO_VARIABLE];
                        array_push($nombres_variables_hijos_sensor_procesado, $nombre_variable_hijo_sensor_procesado);
                    }

                    // Se recuperan los valores de prueba de la función de valores (si los hay)
                    if ($parametros_auxiliares == "")
                    {
                        if ($numero_hijos_sensor_procesado > 0)
                        {
                            $valores_variables_hijos_sensor_procesado = array_fill(0, $numero_hijos_sensor_procesado, VALOR_PRUEBA_DEFECTO_FUNCION_SENSOR_PROCESADO);
                        }
                        else
                        {
                            $valores_variables_hijos_sensor_procesado = array();
                        }
                        $mostrar_valor_evaluado_funcion_valores_sensor_procesado = False;
                    }
                    else
                    {
                        $valores_variables_hijos_sensor_procesado = explode(",", $parametros_auxiliares);
                        $mostrar_valor_evaluado_funcion_valores_sensor_procesado = True;
                    }

                    // Evaluación de la función de valores horaria
                    if ($funcion_valores_horaria_sensor_procesado != "")
                    {
                        // Parámetros de la función a llamar
                        $parametros_funcion_externa =
                            array(
                                "llamante" => "web_emios",
                                "nombre" => NOMBRE_FUNCION_EVALUA_FUNCION_VALORES,
                                "funcion_valores" => $funcion_valores_horaria_sensor_procesado,
                                "nombres_variables" => $nombres_variables_hijos_sensor_procesado,
                                "valores_variables" => $valores_variables_hijos_sensor_procesado
                            );

                        // Llamada a función 'externa'
                        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
                        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

                        // Si la función de valores es incorrecta se devuelve un error
                        if ($resultado_funcion_externa["funcion_correcta"] == 0)
                        {
                            $modificar_sensor = False;

                            $error = $resultado_funcion_externa["error"];
                            $descripcion_error = dame_descripcion_error_funcion_variables($error);

                            $res = "ERROR";
                            $msg = $idiomas->_("Ha ocurrido un error al evaluar la función de valores")."\n(".
                                $descripcion_error.")";
                        }
                        else
                        {
                            if ($mostrar_valor_evaluado_funcion_valores_sensor_procesado == True)
                            {
                                $valor_evaluado_funcion_valores_horaria_sensor_procesado = $resultado_funcion_externa["valor"];
                                $cadena_valor_evaluado_funcion_valores_sensor_procesado = formatea_numero($valor_evaluado_funcion_valores_horaria_sensor_procesado, 2);
                                $msg_extra = $idiomas->_("valor de prueba evaluado de la función de valores").": ".$cadena_valor_evaluado_funcion_valores_sensor_procesado;
                                $cerrar_ventana = false;
                            }
                        }
                    }

                    // Evaluación de la función de valores cuartohoraria
                    if (($misma_funcion_valores_cuartohoraria_sensor_procesado == VALOR_NO) && ($funcion_valores_cuartohoraria_sensor_procesado != ""))
                    {
                        // Parámetros de la función a llamar
                        $parametros_funcion_externa =
                            array(
                                "llamante" => "web_emios",
                                "nombre" => NOMBRE_FUNCION_EVALUA_FUNCION_VALORES,
                                "funcion_valores" => $funcion_valores_cuartohoraria_sensor_procesado,
                                "nombres_variables" => $nombres_variables_hijos_sensor_procesado,
                                "valores_variables" => $valores_variables_hijos_sensor_procesado
                            );

                        // Llamada a función 'externa'
                        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
                        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

                        // Si la función de valores es incorrecta se devuelve un error
                        if ($resultado_funcion_externa["funcion_correcta"] == 0)
                        {
                            $modificar_sensor = False;

                            $error = $resultado_funcion_externa["error"];
                            $descripcion_error = dame_descripcion_error_funcion_variables($error);

                            $res = "ERROR";
                            $msg = $idiomas->_("Ha ocurrido un error al evaluar la función de valores cuartohoraria")."\n(".
                                $descripcion_error.")";
                        }
                        else
                        {
                            if ($mostrar_valor_evaluado_funcion_valores_sensor_procesado == True)
                            {
                                $valor_evaluado_funcion_valores_cuartohoraria_procesado = $resultado_funcion_externa["valor"];
                                $cadena_valor_evaluado_funcion_valores_cuartohoraria_procesado = formatea_numero($valor_evaluado_funcion_valores_cuartohoraria_procesado, 2);
                                if ($msg_extra !== NULL)
                                {
                                    $msg_extra .= ","."\n";
                                }
                                else
                                {
                                    $msg_extra = "";
                                }
                                $msg_extra = $idiomas->_("valor de prueba evaluado de la función de valores cuartohoraria").": ".
                                    $cadena_valor_evaluado_funcion_valores_cuartohoraria_procesado;
                                $cerrar_ventana = false;
                            }
                        }
                    }
                }
            }
        }

        // Comprobación de localizaciones correctas
        if ($modificar_sensor == true)
        {
            if ($id_grupo != ID_NINGUNO)
            {
                $localizaciones_correctas = dame_localizaciones_correctas_grupo_localizacion_nodo(TIPO_NODO_SENSOR, $id_grupo, $id_localizacion);
                if ($localizaciones_correctas == false)
                {
                    $modificar_sensor = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Las localizaciones del sensor y del grupo no son correctas");
                }
            }
        }

        // Se comprueba si se pueden modificar los elementos adicionales según la clase de sensor
        if ($modificar_sensor == true)
        {
            $msg = "";
            $posible_modificar_elementos_adicionales_sensor = dame_posible_modificar_elementos_adicionales_clase_sensor(
                $nombre,
                $clase,
                $parametros_clase,
                $msg,
                $aviso);
            if ($posible_modificar_elementos_adicionales_sensor == false)
            {
                $modificar_sensor = false;

                $res = "ERROR";
            }
        }

        // Si los datos son correctos se modifican los valores del sensor (sólo si ha cambiado el nombre del sensor)
        if ($modificar_sensor == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_sensor_anterior = dame_fila_sensor($id_sensor);
            $nombre_anterior = $fila_sensor_anterior["nombre"];
            $incrementos_tiempo_real_horarios = $fila_sensor_anterior["incrementos_tiempo_real_horarios"];

            // Si ha cambiado el nombre del sensor
            if ($nombre != $nombre_anterior)
            {
                // Tabla especifica de datos de la clase
                $tabla_datos = dame_nombre_tabla_datos_clase_sensor($clase);

                // Parámetros específicos por clase de sensor
                if ($clase_granularidad_cuartohoraria == true)
                {
                    $granularidad_cuartohoraria = VALOR_SI;
                }
                else
                {
                    $granularidad_cuartohoraria = VALOR_NO;
                }

                // Parámetros de la función a llamar
                $parametros_funcion_externa =
                    array(
                        "llamante" => "web_emios",
                        "nombre" => NOMBRE_FUNCION_MODIFICA_VALORES_SENSOR,
                        "nombre_sensor_anterior" => $nombre_anterior,
                        "nombre_sensor_actual" => $nombre,
                        "id_red" => $_SESSION["id_red"],
                        "tipo" => $tipo,
                        "clase" => $clase,
                        "tipo_valores" => $tipo_valores,
                        "tabla_destino" => $tabla_datos,
                        "granularidad_cuartohoraria" => $granularidad_cuartohoraria,
                        "incrementos_tiempo_real_horarios" => $incrementos_tiempo_real_horarios
                    );

                // Llamada a función 'externa'
                $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
                $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

                // Si los datos del sensor están bloqueados (hay alguna operación de datos de este sensor en ejecución)
                $datos_sensor_bloqueados = $resultado_funcion_externa["datos_sensor_bloqueados"];
                if ($datos_sensor_bloqueados == VALOR_SI)
                {
                    $modificar_sensor = False;

                    $res = "ERROR";
                    $msg = $idiomas->_("Se está realizando una operación de datos en este sensor, inténtelo de nuevo en unos minutos");
                }

            }

            if ($tipo==TIPO_SENSOR_EXTERNO)
                {
                    // Se comprueba si es de tipo API
                    $clase_sensor_externo = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                    if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_API)
                    {
                        $parametros_tipo_old = $fila_sensor_anterior["parametros_tipo"];
                        $parametros_tipo_old_array = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_old);
                        $clase_sensor_externo_old = $parametros_tipo_old_array[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];

                        // Si antes era de tipo API se modifica
                        if ($clase_sensor_externo_old == $clase_sensor_externo)
                        {
                            $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                            $opciones_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
                            $api_seleccionada = $opciones_valores[0];

                            switch ($api_seleccionada)
                            {
                                case API_AXONTIME:

                                    modifica_sensor_axontime($parametros_tipo_old, $opciones_valores,$nombre);
                                    break;

                                case API_SGCLIMA:

                                    modifica_sensor_sgclima($opciones_valores, $nombre, $nombre_anterior);
                                    break;

                                default:
                                    $modificar_sensor = false;
                                    $res = "ERROR";
                                    $msg = "Tipo de API desconocida";
                                    break;
                            }
                        }
                        // Si no era de tipo API anteriormente se crea en la BD externa
                        else
                        {
                            $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
                            $opciones_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
                            $api_seleccionada = $opciones_valores[0];

                            switch ($api_seleccionada)
                            {
                                case API_AXONTIME:
                                    anyade_sensor_axontime($parametros_tipo, $opciones_valores,$nombre);
                                    break;

                                default:
                                    $modificar_sensor = false;
                                    $res = "ERROR";
                                    $msg = "Tipo de API desconocida";
                                    break;
                            }

                        }
                    }
                    if ($clase_sensor_externo == CLASE_SENSOR_EXTERNO_FICHEROS_CSV)
                    {
                        // Se obtiene el tipo de sensor CSV [datadis, no datadis] que era antes
                        $parametros_tipo_old = $fila_sensor_anterior["parametros_tipo"];
                        $parametros_tipo_old_array = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_old);
                        $clase_sensor_externo_old = $parametros_tipo_old_array[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
                        $cadena_opciones_generales_old = $parametros_tipo_old_array[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                        $opciones_generales_old = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales_old);

                        $cadena_opciones_generales = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
                        $opciones_generales = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_generales);

                        // Si antes también era de tipo CSV y de tipo DATADIS, se modifica
                        if (($clase_sensor_externo_old == $clase_sensor_externo)
                            && ($opciones_generales[1] == $opciones_generales_old[1])
                            && ($opciones_generales[1] == "datadis"))
                        {
                            $cadena_opciones_valores_datadis = $parametros_tipo[4];
                            $opciones_valores_datadis = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_datadis);
                            $modificar_sensor = modifica_sensor_datadis($opciones_valores_datadis, $nombre, $nombre_anterior);
                        }
                        // Si antes no era de tipo datadis, entonces hay que crearlo
                        elseif ($opciones_generales[1] == "datadis")
                        {
                            $cadena_opciones_valores_datadis = $parametros_tipo[4];
                            $opciones_valores_datadis = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores_datadis);
                            $modificar_sensor = anyade_sensor_datadis($parametros_tipo, $opciones_valores_datadis, $nombre);
                        }
                    }
                }
        }

        // Se modifica el sensor
        if ($modificar_sensor == true)
        {
            // Se recupera el origen del mapa 'final'
            $parametros_origen_mapa = array("modulo" => MODULO_SENSORES);
            $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
            $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
            $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

            // Se recupera la información de mapa anterior (antes de la modificación)
            $info_posicion_mapa_anterior = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_SENSOR,
                $id_sensor,
                $origen_mapa,
                $id_origen_mapa);

            // Se modifica el sensor
            $operacion_modificacion = "
                UPDATE sensores
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo)."',
                    clase = '".$bd_red->_($clase)."',
                    parametros_clase = '".$bd_red->_($cadena_parametros_clase)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    localizacion = '".$bd_red->_($id_localizacion)."',
                    visible_localizaciones_hijas = '".$bd_red->_($visible_localizaciones_hijas)."',
                    frecuencia_muestreo = '".$bd_red->_($frecuencia_muestreo)."',
                    frecuencia_envio = '".$bd_red->_($frecuencia_envio)."',
                    calibracion = '".$bd_red->_($calibracion)."',
                    tipo_valores = '".$bd_red->_($tipo_valores)."',
                    cambio_valores_puntuales = '".$bd_red->_($cambio_valores_puntuales)."',
                    incrementos_tiempo_real_horarios = '".$bd_red->_($incrementos_tiempo_real_horarios)."',
                    incrementos_negativos_validos = '".$bd_red->_($incrementos_negativos_validos)."',
                    guardar_valores_base_datos = '".$bd_red->_($guardar_valores_base_datos)."',
                    notificar_todos_eventos = '".$bd_red->_($notificar_todos_eventos)."',
                    granularidad_cuartohoraria = '".$bd_red->_($granularidad_cuartohoraria)."',
                    ultimo_error_valores_tiempo_real_json = '',
                    ultimo_error_valores_horarios_json = '',
                    ultimo_error_valores_cuartohorarios_json = '',
                    ultimo_error_valores_clase_horarios_json = '',
                    ultimo_error_valores_clase_cuartohorarios_json = ''
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se guarda o elimina la información de la posición en el mapa
                if ($mostrar_en_mapa == VALOR_SI)
                {
                    $info_posicion_mapa_actual = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_SENSOR,
                        "id_elemento" => $id_sensor,
                        "origen" => $origen_mapa,
                        "id_origen" => $id_origen_mapa,
                        "latitud" => $latitud_mapa,
                        "longitud" => $longitud_mapa,
                        "zoom" => $zoom_mapa);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
                }
                else
                {
                    elimina_info_posicion_mapa_base_datos(
                        TIPO_ELEMENTO_MAPA_SENSOR,
                        $id_sensor,
                        $origen_mapa,
                        $id_origen_mapa);
                }

                // Se modifican los elementos adicionales según la clase de sensor
                modifica_elementos_adicionales_clase_sensor(
                    $id_sensor,
                    $nombre,
                    $clase,
                    $parametros_clase);

                // Se recupera la fila actual
                $fila_sensor_actual = dame_fila_sensor($id_sensor);

                // Acciones a realizar al modificar un sensor
                realiza_acciones_sensor_modificado(
                    $id_sensor,
                    $fila_sensor_actual,
                    $fila_sensor_anterior);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_sensor(
                    $fila_sensor_actual,
                    $fila_sensor_anterior,
                    $info_posicion_mapa_actual,
                    $info_posicion_mapa_anterior);

                // Modificación correcta
                $res = "OK";
                $msg = $idiomas->_("Sensor modificado correctamente");
                if ($msg_extra !== NULL)
                {
                    $msg .= "\n(".$msg_extra.")";
                }

                // Comprobación de ubicación del sensor real
                switch ($tipo)
                {
                    case TIPO_SENSOR_REAL:
                    {
                        $aviso = dame_aviso_comprobacion_ubicacion_sensor_real($id_sensor, $parametros_tipo);
                        break;
                    }
                }

                // Aviso en el mensaje de modificación
                if ($aviso != "")
                {
                    $tipo_mensaje = TIPO_MENSAJE_AVISO;
                    $msg .= "\n(".$aviso.")";
                }
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "tipo_mensaje" => $tipo_mensaje,
        "cerrar_ventana" => $cerrar_ventana))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del sensor
    function anyade_accion_usuario_modificar_sensor(
        $fila_actual,
        $fila_anterior,
        $info_posicion_mapa_actual,
        $info_posicion_mapa_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_SENSOR;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["localizacion"] != $fila_anterior["localizacion"])
        {
            $nombre_localizacion = dame_nombre_localizacion($fila_actual["localizacion"]);
            $nombre_localizacion_anterior = dame_nombre_localizacion($fila_anterior["localizacion"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion_anterior;
        }
        if ($fila_actual["visible_localizaciones_hijas"] != $fila_anterior["visible_localizaciones_hijas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila_actual["visible_localizaciones_hijas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila_anterior["visible_localizaciones_hijas"];
        }
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase"];
        }
        if ($fila_actual["parametros_clase"] != $fila_anterior["parametros_clase"])
        {
            if ($fila_actual["parametros_clase"] != "")
            {
                $parametros_clase_actuales = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_clase"]);
                sustituye_ids_nombres_parametros_clase_sensor_accion_usuario($fila_actual["clase"], $parametros_clase_actuales);
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_SENSOR] = array(
                    "clase" => $fila_actual["clase"],
                    "parametros_clase" => $parametros_clase_actuales);
            }
            if ($fila_anterior["parametros_clase"] != "")
            {
                $parametros_clase_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_clase"]);
                sustituye_ids_nombres_parametros_clase_sensor_accion_usuario($fila_anterior["clase"], $parametros_clase_anteriores);
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_SENSOR] = array(
                    "clase" => $fila_anterior["clase"],
                    "parametros_clase" => $parametros_clase_anteriores);
            }
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_SENSOR] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_SENSOR] = $fila_anterior["tipo"];
        }
        if ($fila_actual["parametros_tipo"] != $fila_anterior["parametros_tipo"])
        {
            if ($fila_actual["parametros_tipo"] != "")
            {
                $parametros_tipo_actuales = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_tipo"]);
                sustituye_ids_nombres_parametros_tipo_sensor_accion_usuario($fila_actual["tipo"], $parametros_tipo_actuales);
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_SENSOR] = array(
                    "tipo" => $fila_actual["tipo"],
                    "parametros_tipo" => $parametros_tipo_actuales);
            }
            if ($fila_anterior["parametros_tipo"] != "")
            {
                $parametros_tipo_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_tipo"]);
                sustituye_ids_nombres_parametros_tipo_sensor_accion_usuario($fila_anterior["tipo"], $parametros_tipo_anteriores);
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_SENSOR] = array(
                    "tipo" => $fila_anterior["tipo"],
                    "parametros_tipo" => $parametros_tipo_anteriores);
            }
        }
        if ((NodoSensor::dame_mostrar_calibracion($fila_actual["tipo"]) == true) &&
            ($fila_actual["calibracion"] != $fila_anterior["calibracion"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila_actual["calibracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila_anterior["calibracion"];
        }
        if ((NodoSensor::dame_mostrar_tipo_valores($fila_actual["tipo"]) == true) &&
            ($fila_actual["tipo_valores"] != $fila_anterior["tipo_valores"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_VALORES] = $fila_actual["tipo_valores"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_VALORES] = $fila_anterior["tipo_valores"];
        }
        if ((NodoSensor::dame_mostrar_cambio_valores_puntuales($fila_actual["clase"], $fila_actual["tipo_valores"]) == true) &&
            ($fila_actual["cambio_valores_puntuales"] != $fila_anterior["cambio_valores_puntuales"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMBIO_VALORES_PUNTUALES] = $fila_actual["cambio_valores_puntuales"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMBIO_VALORES_PUNTUALES] = $fila_anterior["cambio_valores_puntuales"];
        }
        if ((NodoSensor::dame_mostrar_incrementos_tiempo_real_horarios($fila_actual["tipo"], $fila_actual["clase"]) == true) &&
            ($fila_actual["incrementos_tiempo_real_horarios"] != $fila_anterior["incrementos_tiempo_real_horarios"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCREMENTOS_TIEMPO_REAL_HORARIOS] = $fila_actual["incrementos_tiempo_real_horarios"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_INCREMENTOS_TIEMPO_REAL_HORARIOS] = $fila_anterior["incrementos_tiempo_real_horarios"];
        }
        if ((NodoSensor::dame_mostrar_incrementos_negativos_validos($fila_actual["clase"]) == true) &&
            ($fila_actual["incrementos_negativos_validos"] != $fila_anterior["incrementos_negativos_validos"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCREMENTOS_NEGATIVOS_VALIDOS] = $fila_actual["incrementos_negativos_validos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_INCREMENTOS_NEGATIVOS_VALIDOS] = $fila_anterior["incrementos_negativos_validos"];
        }
        if ((NodoSensor::dame_mostrar_granularidad_cuartohoraria($fila_actual["clase"]) == true) &&
            ($fila_actual["granularidad_cuartohoraria"] != $fila_anterior["granularidad_cuartohoraria"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_GRANULARIDAD_CUARTOHORARIA] = $fila_actual["granularidad_cuartohoraria"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_GRANULARIDAD_CUARTOHORARIA] = $fila_anterior["granularidad_cuartohoraria"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_sensores($fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_sensores($fila_anterior["grupo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo_anterior;
        }
        if ((NodoSensor::dame_mostrar_guardar_valores_base_datos($fila_actual["tipo"]) == true) &&
            ($fila_actual["guardar_valores_base_datos"] != $fila_anterior["guardar_valores_base_datos"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_GUARDAR_VALORES_BASE_DATOS] = $fila_actual["guardar_valores_base_datos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_GUARDAR_VALORES_BASE_DATOS] = $fila_anterior["guardar_valores_base_datos"];
        }
        if ((NodoSensor::dame_mostrar_notificar_todos_eventos($fila_actual["tipo"], $fila_actual["clase"]) == true) &&
            ($fila_actual["notificar_todos_eventos"] != $fila_anterior["notificar_todos_eventos"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOTIFICAR_TODOS_EVENTOS] = $fila_actual["notificar_todos_eventos"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOTIFICAR_TODOS_EVENTOS] = $fila_anterior["notificar_todos_eventos"];
        }
        if ((NodoSensor::dame_mostrar_frecuencia_muestreo($fila_actual["tipo"]) == true) &&
            ($fila_actual["frecuencia_muestreo"] != $fila_anterior["frecuencia_muestreo"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FRECUENCIA_MUESTREO] = $fila_actual["frecuencia_muestreo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FRECUENCIA_MUESTREO] = $fila_anterior["frecuencia_muestreo"];
        }
        if ((NodoSensor::dame_mostrar_frecuencia_envio($fila_actual["tipo"]) == true) &&
            ($fila_actual["frecuencia_envio"] != $fila_anterior["frecuencia_envio"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FRECUENCIA_ENVIO] = $fila_actual["frecuencia_envio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FRECUENCIA_ENVIO] = $fila_anterior["frecuencia_envio"];
        }

        // Información de posición en mapa
        if ($info_posicion_mapa_actual !== $info_posicion_mapa_anterior)
        {
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_actual, $parametros_accion_usuario);
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_anterior, $parametros_accion_usuario_anteriores);
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }

    function modifica_sensor_axontime($cadena_parametros_tipo, $opciones_valores, $nombre)
    {

        $direccion_api_externa = API_EXTERNA_SENSORES_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);


        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $cadena_opciones_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
        $opciones_valores_old = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_opciones_valores);
        $id_sensor_externo = $parametros_tipo[0];
        $cups_id_old = $opciones_valores_old[1];
        $cups_id = $opciones_valores[1];
        $tipo_dato = $opciones_valores[3];

        switch ($tipo_dato) {
            case 'energia':
                $control_flag_reactiva = '';
                break;

            case 'ie1q':
                $control_flag_reactiva = '&flag_reactiva=Q1';
                break;
            case 'ce4q':
                $control_flag_reactiva = '&flag_reactiva=Q4';
                break;
            default:
                $res = 'ERROR';
                break;
        }

        $ip_servidor = file_get_contents('https://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SENSORES_DIRECCION.'/modifica_sensor_axon';


	$curl = curl_init();
	$nombre_url = curl_escape($curl, $nombre);

	$ful_url = $url.'?ip_servidor='.$ip_servidor.'&id_externo='.$id_sensor_externo.'&nombre_nuevo='.$nombre_url.'&cups_id_old='.$cups_id_old.'&cups_id='.$cups_id.$control_flag_reactiva;

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ful_url,
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
          $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $modificar_sensor = false;
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return;
            }
          }
        curl_close($curl);

    }

    function modifica_sensor_sgclima($opciones_valores, $nombre, $nombre_anterior)
    {
        $direccion_api_externa = API_EXTERNA_SGCLIMA_DIRECCION;
        $token = obtiene_token_api($direccion_api_externa);

        $id_loc = $opciones_valores[3];
        $id_param = $opciones_valores[4];
        $ip_servidor = file_get_contents('https://ipecho.net/plain');
        // TEMPORAL PARA DOCKERS LOCAL
        // UN DOCKER NO TIENE IP PUBLICA POR TANTO NO ENVIA NADA
        // SE ESTABLECE UNO PARA DEBUG
        //$ip_servidor = '52.208.201.150';
        $url = API_EXTERNA_SGCLIMA_DIRECCION.'/modificar_sensor_sgclima';

        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);
        $nombre_anterior_url = curl_escape($curl, $nombre_anterior);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre_old='.$nombre_anterior_url.'&ip_servidor='.$ip_servidor.'&id_localizacion='.$id_loc.
                            '&ps_id='.$id_param.'&red='.$_SESSION['id_red'].'&nombre='.$nombre_url,
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

        $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $modificar_sensor = false;
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return;
            }
          }
        curl_close($curl);
    }

    function modifica_sensor_datadis($opciones_valores, $nombre, $nombre_anterior)
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
        $url = API_EXTERNA_SENSORES_DIRECCION.'/datadis/modifica_sensor';

        $curl = curl_init();
        $nombre_url = curl_escape($curl, $nombre);
        $nombre_anterior_url = curl_escape($curl, $nombre_anterior);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'?nombre_anterior_sensor='.$nombre_anterior_url.'&nombre_sensor='.$nombre_url.
                            '&ip_servidor='.$ip_servidor.'&cups='.$cups.
                            '&distributor_code='.$distributorCode.'&measurement_type='.$measurementType.
                            '&point_type='.$pointType.'&authorized_nif='.$authorizedNif.'&red='.$_SESSION['id_red'],
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

        $response = curl_exec($curl);

        // Control del codigo de estado
        // de la peticion

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
              case 201:  # OK
                break;
              default:
                $modificar_sensor = false;
                $res = "ERROR";
                $msg = 'Error en la peticion a la API code:'.$http_code;
                return False;
            }
          }
        curl_close($curl);
        return True;
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
                $modificar_sensor = false;
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
        $log = dame_log();

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
                return False;
            }
          }
        curl_close($curl);
        return True;
    }
?>
