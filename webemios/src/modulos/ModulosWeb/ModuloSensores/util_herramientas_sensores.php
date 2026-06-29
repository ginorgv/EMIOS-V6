<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones de herramientas de sensores
    //


    // Añade la importación de valores de sensor pendiente
    function anyade_importacion_valores_sensor_pendiente($parametros, $ficheros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $clase_sensor = $parametros["clase_sensor"];
        $aplicar_calibracion = $parametros["aplicar_calibracion"];
        $tipo_valores = $parametros["tipo_valores"];
        $opciones_fichero_csv = $parametros["opciones_fichero_valores"];
        $opciones_valores_fichero_csv = $parametros["opciones_valores_fichero_valores"];

        // Fichero de valores
        $ruta_fichero_valores_cliente = $ficheros["fichero_valores"]["tmp_name"];
        $nombre_fichero_valores = basename($ficheros["fichero_valores"]["name"]);

        // Se recupera la información del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];
        $guardar_valores_base_datos_sensor = $fila_sensor["guardar_valores_base_datos"];

        // Comprobaciones antes de añadir la importacion de valores del sensor pendiente:
        // - Flag de guardar valores en base de datos activado
        // - Se comprueba que el tipo de valores a importar sea el mismo que el tipo de valores del sensor
        // (Nota: El tamaño del fichero se ha comprobado en 'javascript')
        $anyadir_importacion_pendiente = true;

        // Flag de guardar valores en base de datos activado
        if ($guardar_valores_base_datos_sensor == VALOR_NO)
        {
            $anyadir_importacion_pendiente = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se guardan los valores del sensor en base de datos");
        }

        // Se comprueba que el tipo de valores a importar sea el mismo que el tipo de valores del sensor
        if ($anyadir_importacion_pendiente == true)
        {
            if ($tipo_valores_sensor != $tipo_valores)
            {
                $anyadir_importacion_pendiente = false;

                $res = "ERROR";
                $msg = $idiomas->_("El tipo de valores a importar debe ser igual que el tipo de valores del sensor");
            }
        }

        // Se añade la importación de valores del sensor pendiente
        if ($anyadir_importacion_pendiente == true)
        {
            // Se lee el contenido del fichero de valores
            $contenido_fichero_valores = file_get_contents($ruta_fichero_valores_cliente);

            // Fecha y hora actual UTC
            $fecha_hora_utc = dame_fecha_hora_actual_utc();
            $cadena_fecha_hora_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_utc, FORMATO_FECHA_HORA_BASE_DATOS);

            // Se añade la importación
            $operacion_insercion = "
                INSERT INTO importaciones_valores_sensores_pendientes (
                    hora,
                    red,
                    sensor,
                    clase_sensor,
                    tipo_valores,
                    aplicar_calibracion,
                    calcular_valores_periodos_sensor_posteriores_hora_ultimo_calculo,
                    nombre_fichero_csv,
                    contenido_fichero_csv,
                    opciones_fichero_csv,
                    opciones_valores_fichero_csv,
                    usuario,
                    estado,
                    hora_ultimo_estado,
                    ultimo_error
                ) VALUES (
                    '".$bd_red->_($cadena_fecha_hora_base_datos_utc)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_sensor)."',
                    '".$bd_red->_($clase_sensor)."',
                    '".$bd_red->_($tipo_valores)."',
                    '".$bd_red->_($aplicar_calibracion)."',
                    '".$bd_red->_(VALOR_SI)."',
                    '".$bd_red->_($nombre_fichero_valores)."',
                    '".$bd_red->_($contenido_fichero_valores)."',
                    '".$bd_red->_($opciones_fichero_csv)."',
                    '".$bd_red->_($opciones_valores_fichero_csv)."',
                    '".$_SESSION["id_usuario"]."',
                    '".$bd_red->_(ESTADO_IMPORTACION_PENDIENTE_EN_ESPERA)."',
                    '".$bd_red->_($cadena_fecha_hora_base_datos_utc)."',
                    '".$bd_red->_(ERROR_IMPORTACION_PENDIENTE_NINGUNO)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la importación pendiente añadida
                $id_importacion_pendiente = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_importacion_pendiente = dame_fila_importacion_valores_sensor_pendiente($id_importacion_pendiente);

                // Se notifica la operación de administración
                notifica_operacion_administracion_importacion_valores_sensor_pendiente(
                    OPERACION_ADICION,
                    $id_importacion_pendiente);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_importacion_valores_sensor_pendiente(
                    $fila_importacion_pendiente,
                    $nombre_sensor,
                    $clase_sensor);

                $res = "OK";
                $msg = $idiomas->_("Importación de valores del sensor añadida correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Exporta los valores de un sensor
    function exporta_valores_sensor($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $clase_sensor = $parametros["clase_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_incrementos_valores = $parametros["tipo_incrementos_valores"];
        $valores_clase_sensor = $parametros["valores_clase_sensor"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $punto_decimal_exportacion_valores_sensor = $parametros["punto_decimal"];
        $zona_horaria_exportacion_valores_sensor = $parametros["zona_horaria"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Información del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];
        $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];

        // Tabla de valores
        $tabla_valores = dame_nombre_tabla_datos_clase_sensor($clase_sensor);

        // Campos puntuales e incrementos
        $campos_puntuales = dame_campos_puntuales_clase_sensor($clase_sensor);
        $campos_incrementos = dame_campos_incrementos_clase_sensor($clase_sensor);

        // Campos de clase de sensor (se eliminan los campos que ya estuvieran en campos de valores o incrementos)
        if ($valores_clase_sensor == VALOR_SI)
        {
            $campos_clase_sensor = dame_campos_clase_clase_sensor($clase_sensor);
            $campos_clase_sensor = array_diff($campos_clase_sensor, $campos_puntuales);
            $campos_clase_sensor = array_diff($campos_clase_sensor, $campos_incrementos);
        }
        else
        {
            $campos_clase_sensor = array();
        }

        // Rutas relativas de ficheros de valores exportados
        $rutas_relativas_ficheros_valores_exportados = array();

        // Intervalo de valores a exportar
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            {
                // Tabla origen
                $tabla_origen = $tabla_valores;
                switch ($fila_sensor["tipo"])
                {
                    case TIPO_SENSOR_PROCESADO:
                    {
                        $tabla_origen .= SUFIJO_TABLA_HORAS;
                        break;
                    }
                    default:
                    {
                        if ($tipo_valores_sensor == TIPO_VALORES_SENSOR_INCREMENTALES)
                        {
                            $tabla_origen .= SUFIJO_TABLA_INCREMENTOS;

                            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                            $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
                            if (($clase_procesado_valores == false) || ($incrementos_tiempo_real_horarios == VALOR_NO))
                            {
                                $tabla_origen .= SUFIJO_TABLA_TIEMPO_REAL;
                            }
                        }
                        break;
                    }
                }

                // Se exportan los campos puntuales o incrementales en tiempo real (dependiendo del tipo de valores del sensor)
                switch ($tipo_valores_sensor)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        $consulta_valores_sensor = "
                            SELECT
                                hora AS fecha_hora";
                        foreach ($campos_puntuales as $campo_puntual)
                        {
                            $consulta_valores_sensor .= ", ".$bd_datos->_($campo_puntual);
                        }
                        if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
                        {
                            $consulta_valores_sensor .= ",
                                NULL AS horario_verano";
                        }
                        $consulta_valores_sensor .= "
                            FROM ".$tabla_origen."
                            WHERE
                                (sensor = '".$bd_datos->_($nombre_sensor)."')
                                AND (red = '".$_SESSION["id_red"]."')
                                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                            ORDER BY hora ASC";
                        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
                        if ($res_valores_sensor == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
                        }
                        $filas_valores_sensor_csv = array();
                        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
                        {
                            array_push($filas_valores_sensor_csv, $fila_valores_sensor);
                        }

                        // Se crea el fichero CSV de valores exportados
                        $numero_valores_exportados = crea_fichero_csv_valores_exportados(
                            $id_sensor,
                            $nombre_sensor,
                            $clase_sensor,
                            "valores",
                            $filas_valores_sensor_csv,
                            $campos_puntuales,
                            array(),
                            $punto_decimal_exportacion_valores_sensor,
                            $zona_horaria_exportacion_valores_sensor,
                            $rutas_relativas_ficheros_valores_exportados);
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        // Consulta de incrementos de valores del sensor entre las fechas especificadas
                        $consulta_incrementos_valores_sensor = "
                            SELECT
                                hora AS fecha_hora,
                                horas";
                        foreach ($campos_incrementos as $campo_incremento)
                        {
                            $consulta_incrementos_valores_sensor .= ", ".$bd_datos->_($campo_incremento);
                        }
                        if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
                        {
                            $consulta_valores_sensor .= ",
                                NULL AS horario_verano";
                        }
                        $consulta_incrementos_valores_sensor .= "
                            FROM ".$tabla_origen."
                            WHERE
                                (sensor = '".$bd_datos->_($nombre_sensor)."')
                                AND (red = '".$_SESSION["id_red"]."')
                                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                            ORDER BY hora ASC";
                        $res_incrementos_valores_sensor = $bd_datos->ejecuta_consulta($consulta_incrementos_valores_sensor);
                        if ($res_incrementos_valores_sensor == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_incrementos_valores_sensor."'");
                        }
                        $filas_incrementos_valores_sensor_csv = array();
                        while ($fila_incrementos_valores_sensor = $res_incrementos_valores_sensor->dame_siguiente_fila())
                        {
                            array_push($filas_incrementos_valores_sensor_csv, $fila_incrementos_valores_sensor);
                        }

                        // Se modifica el sufijo del fichero y la fecha según el tipo de incrementos de valores
                        // - Si es fecha final, se suman las horas de incrementos a la hora (para que quede como hora final)
                        switch ($tipo_incrementos_valores)
                        {
                            case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL:
                            {
                                $sufijo_fichero_incrementos_valores = $idiomas->_("incrementos");
                                break;
                            }
                            case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL:
                            {
                                $sufijo_fichero_incrementos_valores = $idiomas->_("incrementos")."_".$idiomas->_("finales");
                                for ($i = 0; $i < count($filas_incrementos_valores_sensor_csv); $i++)
                                {
                                    $cadena_fecha_hora_base_datos_utc = $filas_incrementos_valores_sensor_csv[$i]['fecha_hora'];
                                    $horas = $filas_incrementos_valores_sensor_csv[$i]['horas'];

                                    $segundos = round($horas * 3600);
                                    $intervalo_segundos = new DateInterval("PT".$segundos."S");
                                    $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                                    $fecha_hora_utc->add($intervalo_segundos);
                                    $cadena_fecha_hora_final_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_utc, FORMATO_FECHA_HORA_BASE_DATOS);
                                    $filas_incrementos_valores_sensor_csv[$i]['fecha_hora'] = $cadena_fecha_hora_final_base_datos_utc;
                                }
                                break;
                            }
                            default:
                            {
                                throw new Exception("Tipo de incrementos de valores incorrecto: '".$tipo_incrementos_valores."'");
                            }
                        }

                        // Se elimina el campo 'horas' de las filas de los incrementos de valores del sensor
                        for ($i = 0; $i < count($filas_incrementos_valores_sensor_csv); $i++)
                        {
                            unset($filas_incrementos_valores_sensor_csv[$i]['horas']);
                        }

                        // Se crea el fichero CSV de incrementos de valores exportados
                        $numero_incrementos_valores_exportados = crea_fichero_csv_valores_exportados(
                            $id_sensor,
                            $nombre_sensor,
                            $clase_sensor,
                            $sufijo_fichero_incrementos_valores,
                            $filas_incrementos_valores_sensor_csv,
                            $campos_incrementos,
                            array(),
                            $punto_decimal_exportacion_valores_sensor,
                            $zona_horaria_exportacion_valores_sensor,
                            $rutas_relativas_ficheros_valores_exportados);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de valores del sensor incorrecto: ".$tipo_valores_sensor);
                    }
                }

                // Se crea el mensaje de resultado a mostrar
                if (($numero_valores_exportados == 0) && ($numero_incrementos_valores_exportados == 0))
                {
                    $msg = $idiomas->_("No se han exportado valores");
                }
                else
                {
                    $msg = $idiomas->_("Valores exportados correctamente").":\n";
                    if ($numero_valores_exportados > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[0]["fecha_hora"];
                        $cadena_fecha_hora_fin_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[$numero_valores_exportados - 1]["fecha_hora"];
                        $cadena_fecha_hora_inicio_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_inicio_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

                        $msg .= "- ".$idiomas->_("Número de valores exportados").": ".$numero_valores_exportados."\n";
                        $msg .= "(".$idiomas->_("inicio").": ".$cadena_fecha_hora_inicio_valores_exportados_local_local.", ";
                        $msg .= $idiomas->_("fin").": ".$cadena_fecha_hora_fin_valores_exportados_local_local.")\n";
                    }
                    if ($numero_incrementos_valores_exportados > 0)
                    {
                        $cadena_fecha_hora_inicio_incrementos_valores_exportados_base_datos_utc = $filas_incrementos_valores_sensor_csv[0]["fecha_hora"];
                        $cadena_fecha_hora_fin_incrementos_valores_exportados_base_datos_utc = $filas_incrementos_valores_sensor_csv[$numero_incrementos_valores_exportados - 1]["fecha_hora"];
                        $cadena_fecha_hora_inicio_incrementos_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_incrementos_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_incrementos_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_incrementos_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_inicio_incrementos_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_incrementos_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_incrementos_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_incrementos_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

                        $msg .= "- ".$idiomas->_("Número de incrementos de valores exportados").": ".$numero_incrementos_valores_exportados."\n";
                        $msg .= "(".$idiomas->_("inicio").": ".$cadena_fecha_hora_inicio_incrementos_valores_exportados_local_local.", ";
                        $msg .= $idiomas->_("fin").": ".$cadena_fecha_hora_fin_incrementos_valores_exportados_local_local.")\n";
                    }
                }
                break;
            }
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA:
            {
                // Tabla origen y descripción de tipo de valores
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_CUARTOHORA:
                    {
                        $tabla_origen = $tabla_valores.SUFIJO_TABLA_CUARTOSHORA;
                        $sufijo_fichero_valores = "cuartoshora";
                        $descripcion_intervalo_valores = $idiomas->_("cuartos de hora");
                        break;
                    }
                    case INTERVALO_VALORES_HORA:
                    {
                        $tabla_origen = $tabla_valores.SUFIJO_TABLA_HORAS;
                        $sufijo_fichero_valores = "horas";
                        $descripcion_intervalo_valores = $idiomas->_("horas");
                        break;
                    }
                }

                // Consulta de campos puntuales e incrementales por hora del sensor entre las fechas especificadas
                $consulta_valores_sensor = "
                    SELECT
                        hora AS fecha_hora";
                foreach ($campos_puntuales as $campo_puntual)
                {
                    $consulta_valores_sensor .= ", ".$bd_datos->_($campo_puntual);
                }
                foreach ($campos_incrementos as $campo_incremento)
                {
                    $consulta_valores_sensor .= ", ".$bd_datos->_($campo_incremento);
                }
                if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
                {
                    $consulta_valores_sensor .= ",
                        NULL AS horario_verano";
                }
                foreach ($campos_clase_sensor as $campo_clase_sensor)
                {
                    $consulta_valores_sensor .= ", ".$bd_datos->_($campo_clase_sensor);
                }
                $consulta_valores_sensor .= "
                    FROM ".$tabla_origen."
                    WHERE
                        (sensor = '".$bd_datos->_($nombre_sensor)."')
                        AND (red = '".$_SESSION["id_red"]."')
                        AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                        AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                    ORDER BY hora ASC";
                $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
                if ($res_valores_sensor == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
                }
                $filas_valores_sensor_csv = array();
                while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
                {
                    array_push($filas_valores_sensor_csv, $fila_valores_sensor);
                }

                // Se crea el fichero CSV de valores (e incrementos si los hay) exportados
                $numero_valores_exportados = crea_fichero_csv_valores_exportados(
                    $id_sensor,
                    $nombre_sensor,
                    $clase_sensor,
                    $sufijo_fichero_valores,
                    $filas_valores_sensor_csv,
                    array_merge($campos_puntuales, $campos_incrementos),
                    $campos_clase_sensor,
                    $punto_decimal_exportacion_valores_sensor,
                    $zona_horaria_exportacion_valores_sensor,
                    $rutas_relativas_ficheros_valores_exportados);

                // Se crea el mensaje de resultado a mostrar
                if ($numero_valores_exportados == 0)
                {
                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_CUARTOHORA:
                        {
                            $msg = $idiomas->_("No se han exportado valores por cuartos de hora");
                            break;
                        }
                        case INTERVALO_VALORES_HORA:
                        {
                            $msg = $idiomas->_("No se han exportado valores por horas");
                            break;
                        }
                    }
                }
                else
                {
                    $cadena_fecha_hora_inicio_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[0]["fecha_hora"];
                    $cadena_fecha_hora_fin_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[$numero_valores_exportados - 1]["fecha_hora"];
                    $cadena_fecha_hora_inicio_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_fecha_hora_fin_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_fecha_hora_inicio_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_fecha_hora_fin_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_CUARTOHORA:
                        {
                            $msg = $idiomas->_("Valores por cuartos de hora exportados correctamente").":\n";
                            $msg .= "- ".$idiomas->_("Número de valores por cuartos de hora exportados").": ".$numero_valores_exportados."\n";
                            break;
                        }
                        case INTERVALO_VALORES_HORA:
                        {
                            $msg = $idiomas->_("Valores por horas exportados correctamente").":\n";
                            $msg .= "- ".$idiomas->_("Número de valores por horas exportados").": ".$numero_valores_exportados."\n";
                            break;
                        }
                    }
                    $msg .= "(".$idiomas->_("inicio").": ".$cadena_fecha_hora_inicio_valores_exportados_local_local.", ";
                    $msg .= $idiomas->_("fin").": ".$cadena_fecha_hora_fin_valores_exportados_local_local.")\n";
                }
                break;
            }
            case INTERVALO_VALORES_DIA:
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                // Tabla origen y descripción de tipo de valores
                $tabla_origen = $tabla_valores.SUFIJO_TABLA_HORAS;
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    {
                        $agrupacion_valores = "GROUP BY
                            DATE(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))";
                        $sufijo_fichero_valores = "dias";
                        $descripcion_intervalo_valores = $idiomas->_("días");
                        break;
                    }
                    case INTERVALO_VALORES_SEMANA:
                    {
                        $agrupacion_valores = "GROUP BY
                            YEARWEEK(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), 1)";
                        $sufijo_fichero_valores = "semanas";
                        $descripcion_intervalo_valores = $idiomas->_("semanas");
                        break;
                    }
                    case INTERVALO_VALORES_MES:
                    {
                        $agrupacion_valores = "GROUP BY
                            YEAR(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')),
                            MONTH(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))";
                        $sufijo_fichero_valores = "meses";
                        $descripcion_intervalo_valores = $idiomas->_("meses");
                        break;
                    }
                }

                // Consulta de campos puntuales e incrementales por hora del sensor entre las fechas especificadas
                $consulta_valores_sensor = "
                    SELECT
                        MIN(hora) AS fecha_hora";
                $campos = array();
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_GENERICA:
                    {
                        $consulta_valores_sensor .= ", AVG(".CAMPO_VALOR.") AS ".CAMPO_VALOR_MEDIA;
                        $consulta_valores_sensor .= ", SUM(".CAMPO_VALOR.") AS ".CAMPO_VALOR_SUMA;
                        $consulta_valores_sensor .= ", SUM(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO_SUMA;
                        $consulta_valores_sensor .= ", AVG(".CAMPO_INCREMENTO.") AS ".CAMPO_INCREMENTO_MEDIA;
                        if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
                        {
                            $consulta_valores_sensor .= ",
                                NULL AS horario_verano";
                        }
                        $campos = array(
                            CAMPO_VALOR_MEDIA,
                            CAMPO_VALOR_SUMA,
                            CAMPO_INCREMENTO_SUMA,
                            CAMPO_INCREMENTO_MEDIA);
                        break;
                    }
                    default:
                    {
                        foreach ($campos_puntuales as $campo_puntual)
                        {
                            $consulta_valores_sensor .= ", AVG(".$bd_datos->_($campo_puntual).")";
                            array_push($campos, $campo_puntual);
                        }
                        foreach ($campos_incrementos as $campo_incremento)
                        {
                            $consulta_valores_sensor .= ", SUM(".$bd_datos->_($campo_incremento).")";
                            array_push($campos, $campo_incremento);
                        }
                        if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
                        {
                            $consulta_valores_sensor .= ",
                                NULL AS horario_verano";
                        }
                        foreach ($campos_clase_sensor as $campo_clase_sensor)
                        {
                            $operacion_agrupacion_valores = dame_operacion_agrupacion_valores_campo_clase_sensor($clase_sensor, $campo_clase_sensor);
                            $consulta_valores_sensor .= ", ".$operacion_agrupacion_valores."(".$bd_datos->_($campo_clase_sensor).")";
                        }
                        break;
                    }
                }
                $consulta_valores_sensor .= "
                    FROM ".$tabla_origen."
                    WHERE
                        (sensor = '".$bd_datos->_($nombre_sensor)."')
                        AND (red = '".$_SESSION["id_red"]."')
                        AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                        AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
                $consulta_valores_sensor .= " ".$agrupacion_valores."
                    ORDER BY hora ASC";
                $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
                if ($res_valores_sensor == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
                }
                $filas_valores_sensor_csv = array();
                while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
                {
                    array_push($filas_valores_sensor_csv, $fila_valores_sensor);
                }

                // Se crea el fichero CSV de valores (e incrementos si los hay) exportados
                $numero_valores_exportados = crea_fichero_csv_valores_exportados(
                    $id_sensor,
                    $nombre_sensor,
                    $clase_sensor,
                    $sufijo_fichero_valores,
                    $filas_valores_sensor_csv,
                    $campos,
                    $campos_clase_sensor,
                    $punto_decimal_exportacion_valores_sensor,
                    $zona_horaria_exportacion_valores_sensor,
                    $rutas_relativas_ficheros_valores_exportados);

                // Se crea el mensaje de resultado a mostrar
                if ($numero_valores_exportados == 0)
                {
                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_DIA:
                        {
                            $msg = $idiomas->_("No se han exportado valores por días");
                            break;
                        }
                        case INTERVALO_VALORES_SEMANA:
                        {
                            $msg = $idiomas->_("No se han exportado valores por semanas");
                            break;
                        }
                        case INTERVALO_VALORES_MES:
                        {
                            $msg = $idiomas->_("No se han exportado valores por meses");
                            break;
                        }
                    }
                }
                else
                {
                    $cadena_fecha_hora_inicio_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[0]["fecha_hora"];
                    $cadena_fecha_hora_fin_valores_exportados_base_datos_utc = $filas_valores_sensor_csv[$numero_valores_exportados - 1]["fecha_hora"];
                    $cadena_fecha_hora_inicio_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_fecha_hora_fin_valores_exportados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_exportados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_fecha_hora_inicio_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_fecha_hora_fin_valores_exportados_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_exportados_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_DIA:
                        {
                            $msg = $idiomas->_("Valores por días exportados correctamente").":\n";
                            $msg .= "- ".$idiomas->_("Número de valores por días exportados").": ".$numero_valores_exportados."\n";
                            break;
                        }
                        case INTERVALO_VALORES_SEMANA:
                        {
                            $msg = $idiomas->_("Valores por semanas exportados correctamente").":\n";
                            $msg .= "- ".$idiomas->_("Número de valores por semanas exportados").": ".$numero_valores_exportados."\n";
                            break;
                        }
                        case INTERVALO_VALORES_MES:
                        {
                            $msg = $idiomas->_("Valores por meses exportados correctamente").":\n";
                            $msg .= "- ".$idiomas->_("Número de valores por meses exportados").": ".$numero_valores_exportados."\n";
                            break;
                        }
                    }
                    $msg .= "(".$idiomas->_("inicio").": ".$cadena_fecha_hora_inicio_valores_exportados_local_local.", ";
                    $msg .= $idiomas->_("fin").": ".$cadena_fecha_hora_fin_valores_exportados_local_local.")\n";
                }
                break;
            }
        }

        // Se devuelve el resultado
        return(array(
            "res" => "OK",
            "msg" => $msg,
            "rutas_ficheros_valores_exportados" => $rutas_relativas_ficheros_valores_exportados)
        );
    }


    // Borra los valores de un sensor sensor
    function borra_valores_sensor($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $clase_sensor = $parametros["clase_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $borrado_valores_pendientes_borrado = $parametros["borrado_valores_pendientes_borrado"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $borrar_valores_tiempo_real = $parametros["borrar_valores_tiempo_real"];

        // Cadenas de fechas e información de sensor
        if ($borrado_valores_pendientes_borrado == VALOR_SI)
        {
            $cadena_fecha_hora_inicio_funcion_utc = "";
            $cadena_fecha_hora_fin_funcion_utc = "";
            $cadena_fecha_hora_inicio_base_datos_local = "";
            $cadena_fecha_hora_fin_base_datos_local = "";

            // Parámetros sólo si no hay fechas de valores
            $tipo_sensor = $parametros["tipo_sensor"];
            $tipo_valores = $parametros["tipo_valores"];
            $incrementos_tiempo_real_horarios_sensor = $parametros["incrementos_tiempo_real_horarios"];
        }
        else
        {
            $zona_horaria = dame_zona_horaria_local();
            if ($cadena_fecha_hora_inicio_local_local != "")
            {
                $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
                $cadena_fecha_hora_inicio_funcion_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
                $cadena_fecha_hora_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
            }
            else
            {
                $cadena_fecha_hora_inicio_funcion_utc = "";
                $cadena_fecha_hora_inicio_base_datos_local = "";
            }
            if ($cadena_fecha_hora_fin_local_local != "")
            {
                $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
                $cadena_fecha_hora_fin_funcion_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
                $cadena_fecha_hora_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
            }
            else
            {
                $cadena_fecha_hora_fin_funcion_utc = "";
                $cadena_fecha_hora_fin_base_datos_local = "";
            }

            // Información del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);
            $tipo_sensor = $fila_sensor["tipo"];
            $tipo_valores = $fila_sensor["tipo_valores"];
            $incrementos_tiempo_real_horarios_sensor = $fila_sensor["incrementos_tiempo_real_horarios"];
        }

        // Parámetros específicos por clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        if ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true)
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
                "nombre" => NOMBRE_FUNCION_BORRA_VALORES_SENSOR,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "tipo_sensor" => $tipo_sensor,
                "clase_sensor" => $clase_sensor,
                "tipo_valores" => $tipo_valores,
                "incrementos_tiempo_real_horarios" => $incrementos_tiempo_real_horarios_sensor,
                "hora_inicio" => $cadena_fecha_hora_inicio_funcion_utc,
                "hora_fin" => $cadena_fecha_hora_fin_funcion_utc,
                "granularidad_cuartohoraria" => $granularidad_cuartohoraria,
                "borrar_valores_tiempo_real" => $borrar_valores_tiempo_real
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si los datos de sensor están bloqueados (hay alguna operación de datos de este sensor en ejecución)
        $datos_sensor_bloqueados = $resultado_funcion_externa["datos_sensor_bloqueados"];
        if ($datos_sensor_bloqueados == VALOR_SI)
        {
            if ($borrado_valores_pendientes_borrado == true)
            {
                $res = "ERROR";
                $msg = $idiomas->_("No se han podido eliminar automáticamente los valores pendientes de borrado, inténtelo de nuevo en unos minutos");
            }
            else
            {
                $res = "ERROR";
                $msg = $idiomas->_("Se está realizando una operación de datos en este sensor, inténtelo de nuevo en unos minutos");
            }
        }
        else
        {
            // Se recuperan los parámetros devueltos
            $numero_valores_sensor_borrados = $resultado_funcion_externa["numero_valores_borrados"];
            if ($numero_valores_sensor_borrados > 0)
            {
                $cadena_fecha_hora_inicio_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados"];
                $cadena_fecha_hora_fin_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados"];
            }
            $numero_incrementos_valores_sensor_borrados = $resultado_funcion_externa["numero_incrementos_valores_borrados"];
            if ($numero_incrementos_valores_sensor_borrados > 0)
            {
                $cadena_fecha_hora_inicio_incrementos_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_inicio_incrementos_valores_borrados"];
                $cadena_fecha_hora_fin_incrementos_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_fin_incrementos_valores_borrados"];
            }
            $numero_valores_sensor_borrados_cuartoshora = $resultado_funcion_externa["numero_valores_borrados_cuartoshora"];
            if ($numero_valores_sensor_borrados_cuartoshora > 0)
            {
                $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_cuartoshora"];
                $cadena_fecha_hora_fin_valores_borrados_cuartoshora_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_cuartoshora"];
            }
            $numero_valores_sensor_borrados_horas = $resultado_funcion_externa["numero_valores_borrados_horas"];
            if ($numero_valores_sensor_borrados_horas > 0)
            {
                $cadena_fecha_hora_inicio_valores_borrados_horas_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_horas"];
                $cadena_fecha_hora_fin_valores_borrados_horas_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_horas"];
            }
            $numero_valores_sensor_borrados_dias = $resultado_funcion_externa["numero_valores_borrados_dias"];
            if ($numero_valores_sensor_borrados_dias > 0)
            {
                $cadena_fecha_hora_inicio_valores_borrados_dias_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_dias"];
                $cadena_fecha_hora_fin_valores_borrados_dias_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_dias"];
            }
            $numero_valores_sensor_borrados_meses = $resultado_funcion_externa["numero_valores_borrados_meses"];
            if ($numero_valores_sensor_borrados_meses > 0)
            {
                $cadena_fecha_hora_inicio_valores_borrados_meses_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_meses"];
                $cadena_fecha_hora_fin_valores_borrados_meses_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_meses"];
            }

            // Mensaje de resultado de borrado de valores
            if (($numero_valores_sensor_borrados == 0) &&
                ($numero_incrementos_valores_sensor_borrados == 0) &&
                ($numero_valores_sensor_borrados_cuartoshora == 0) &&
                ($numero_valores_sensor_borrados_horas == 0) &&
                ($numero_valores_sensor_borrados_dias == 0) &&
                ($numero_valores_sensor_borrados_meses == 0))
            {
                $res = "OK";
                $msg = $idiomas->_("No se han borrado valores");
            }
            else
            {
                // Mensaje de resultado de borrado de valores
                // (si es un borrado de valores pendientes de borrado no se crea el mensaje de resultado)
                $res = "OK";
                if ($borrado_valores_pendientes_borrado == true)
                {
                    $msg = "";
                }
                else
                {
                    $msg = $idiomas->_("Valores borrados correctamente").":\n";
                    if ($numero_valores_sensor_borrados > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_borrados_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_borrados_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_valores_borrados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_borrados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de valores borrados").": ".$numero_valores_sensor_borrados."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_valores_borrados_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_valores_borrados_local_local.")\n";
                    }
                    if ($numero_incrementos_valores_sensor_borrados > 0)
                    {
                        $cadena_fecha_hora_inicio_incrementos_valores_borrados_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_incrementos_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_incrementos_valores_borrados_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_incrementos_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_incrementos_valores_borrados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_incrementos_valores_borrados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_incrementos_valores_borrados_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_incrementos_valores_borrados_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de incrementos de valores borrados").": ".$numero_incrementos_valores_sensor_borrados."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_incrementos_valores_borrados_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_incrementos_valores_borrados_local_local.")\n";
                    }
                    if ($numero_valores_sensor_borrados_cuartoshora > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_cuartoshora_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_borrados_cuartoshora_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_cuartoshora_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_borrados_cuartoshora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_cuartoshora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de valores por cuartos de hora borrados").": ".$numero_valores_sensor_borrados_cuartoshora."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_valores_borrados_cuartoshora_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_valores_borrados_cuartoshora_local_local.")\n";
                    }
                    if ($numero_valores_sensor_borrados_horas > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_borrados_horas_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_horas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_borrados_horas_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_horas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_valores_borrados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_borrados_horas_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_horas_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de valores por horas borrados").": ".$numero_valores_sensor_borrados_horas."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_valores_borrados_horas_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_valores_borrados_horas_local_local.")\n";
                    }
                    if ($numero_valores_sensor_borrados_dias > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_borrados_dias_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_dias_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_borrados_dias_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_dias_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_valores_borrados_dias_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_dias_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_borrados_dias_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_dias_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de valores por días borrados").": ".$numero_valores_sensor_borrados_dias."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_valores_borrados_dias_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_valores_borrados_dias_local_local.")\n";
                    }
                    if ($numero_valores_sensor_borrados_meses > 0)
                    {
                        $cadena_fecha_hora_inicio_valores_borrados_meses_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_meses_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_fin_valores_borrados_meses_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_meses_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                        $cadena_fecha_hora_inicio_valores_borrados_meses_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_meses_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                        $cadena_fecha_hora_fin_valores_borrados_meses_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_meses_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                        $msg .= "- ".$idiomas->_("Número de valores por meses borrados").": ".$numero_valores_sensor_borrados_meses."\n";
                        $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_valores_borrados_meses_local_local.", ";
                        $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_valores_borrados_meses_local_local.")\n";
                    }
                    $msg = substr($msg, 0, -1);
                }
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_borrar_valores_sensor(
                $clase_sensor,
                $nombre_sensor,
                $cadena_fecha_hora_inicio_base_datos_local,
                $cadena_fecha_hora_fin_base_datos_local,
                $borrar_valores_tiempo_real,
                $resultado_funcion_externa);
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Guarda la fecha de recálculo de valores de clase de sensor
    function guarda_fecha_recalculo_valores_clase_sensor($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $clase_sensor = $parametros["clase_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $cadena_fecha_hora_local_local = $parametros["fecha_hora"];

        // Zona horaria local
        $zona_horaria = dame_zona_horaria_local();

        // Flag para guardar la fecha de recálculo
        $guardar_fecha_recalculo = true;

        // Se comprueba el número de días de recálculos (sólo si el usuario no es superadministrador)
        if ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $fecha_hora_local = convierte_cadena_a_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
            $numero_dias_recalculo = $fecha_hora_actual_local->diff($fecha_hora_local)->days + 1;
            if ($numero_dias_recalculo > NUMERO_MAXIMO_DIAS_RECALCULO_DATOS)
            {
                $res = "ERROR";
                $msg = $idiomas->_("El número de días de recálculo es mayor que el máximo permitido")." (".NUMERO_MAXIMO_DIAS_RECALCULO_DATOS.")";
                $guardar_fecha_recalculo = false;
            }
        }

        // Se guarda la fecha de recálculo
        if ($guardar_fecha_recalculo == true)
        {
            // Conversión de fechas
            $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_BASE_DATOS);

            // Se recupera la fila del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);

            // Se actualiza la hora de recálculo en las tablas correspondientes
            actualiza_hora_tablas_recalculos_valores_clase_sensores(
                $cadena_fecha_hora_base_datos_utc,
                $clase_sensor,
                array($fila_sensor));

            // Se añade la acción de usuario
            anyade_accion_usuario_recalcular_valores_clase_sensor(
                $clase_sensor,
                $nombre_sensor,
                $cadena_fecha_base_datos_local);

            $res = "OK";
            $msg = $idiomas->_("Fecha de inicio de recálculo de valores de clase de sensor guardada correctamente").".\n".
                $idiomas->_("Los datos se recalcularán en el siguiente procesado de datos. Esto puede tardar unos minutos");
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Envía valores manuales a un sensor
    function envia_valores_manuales_sensor($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $cadena_fecha_hora_local_local = $parametros["fecha_hora"];
        $valores = $parametros["valores"];
        $incrementos = $parametros["incrementos"];
        $tipo_incrementos = $parametros["tipo_incrementos"];
        $horas_incrementos = $parametros["horas_incrementos"];

        // Se recupera la fila del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);

        // Tipo de valores del sensor
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
        $timestamp_utc = floor(dame_timestamp_fecha_milisegundos($fecha_hora_utc) / 1000);

        // Se comprueba que la hora sea posterior a la hora de últimos valores o incrementos
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor["hora_ultimos_valores"];
        if ($cadena_fecha_hora_ultimos_valores_base_datos_utc !== NULL)
        {
            switch ($tipo_valores_sensor)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $info_ultimos_valores_sensor = dame_info_ultimos_valores_sensor($fila_sensor);
                    $fecha_hora_ultimos_valores_incrementos_utc = $info_ultimos_valores_sensor["fecha_hora_ultimos_valores_utc"];
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $info_ultimos_incrementos_sensor = dame_info_ultimos_incrementos_sensor($fila_sensor);
                    $fecha_hora_ultimos_valores_incrementos_utc = $info_ultimos_incrementos_sensor["fecha_hora_fin_ultimos_incrementos_utc"];
                    if ($horas_incrementos == 0)
                    {
                        $fecha_hora_ultimos_valores_incrementos_utc->add(new DateInterval('PT1S'));
                    }
                    break;
                }
            }
            if ($fecha_hora_utc <= $fecha_hora_ultimos_valores_incrementos_utc)
            {
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_ultimos_valores_incrementos_local_utc = convierte_fecha_a_cadena($fecha_hora_ultimos_valores_incrementos_utc, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_ultimos_valores_incrementos_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimos_valores_incrementos_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                switch ($tipo_valores_sensor)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        $msg = $idiomas->_("La fecha debe ser posterior a la fecha de últimos valores")."\n";
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        $msg = $idiomas->_("La fecha debe ser posterior a la fecha de fin últimos incrementos")."\n";
                        break;
                    }
                }
                $msg .= "(".$cadena_fecha_hora_ultimos_valores_incrementos_local_local.")";
                print(json_encode(array(
                    "res" => "ERROR",
                    "msg" => $msg))
                );
                return;
            }
        }

        // Se crea el contenido del mensaje MQTT
        $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_tipo"]);
        $id_sensor_externo = $parametros_tipo_sensor[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
        switch ($tipo_valores_sensor)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $valores_sustituto_separador = str_replace(SEPARADOR_PARAMETROS_VALORES, SUSTITUTO_SEPARADOR, $valores);
                $datos = implode("#", array(
                    $id_sensor_externo,
                    $timestamp_utc,
                    CAUSA_ENVIO_VALORES_SENSOR_MANUAL,
                    "P",
                    $valores_sustituto_separador,
                    VALOR_SI));
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                switch ($tipo_incrementos)
                {
                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL:
                    {
                        $tipo_incrementos_mqtt = "S";
                        break;
                    }
                    case TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL:
                    {
                        $tipo_incrementos_mqtt = "E";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de incrementos desconocido: '".$tipo_incrementos."'");
                    }
                }
                if ($horas_incrementos > 0)
                {
                    $segundos_incrementos = (int) ($horas_incrementos * 3600);
                }
                else
                {
                    $segundos_incrementos = 0;
                }
                $incrementos_sustituto_separador = str_replace(SEPARADOR_PARAMETROS_VALORES, SUSTITUTO_SEPARADOR, $incrementos);
                $datos = implode("#", array(
                    $id_sensor_externo,
                    $timestamp_utc,
                    CAUSA_ENVIO_VALORES_SENSOR_MANUAL,
                    "I,".$segundos_incrementos.",".$tipo_incrementos_mqtt,
                    $incrementos_sustituto_separador,
                    VALOR_SI));
                break;
            }
        }

        // Se envía el mensaje por MQTT
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta() == true)
        {
            $mqtt->publica("EXTERNAL_SENS/VALUES", $datos, 1);
            $mqtt->desconecta();

            // Se añade la acción de usuario
            anyade_accion_usuario_enviar_valores_manuales_sensor(
                $fila_sensor,
                $cadena_fecha_hora_base_datos_local,
                $valores,
                $incrementos,
                $horas_incrementos);

            $res = "OK";
            $msg = $idiomas->_("Valores manuales enviados correctamente");
        }
        else
        {
            $res = "ERROR";
            $msg = $idiomas->_("No se han podido enviar los valores manuales");
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    //
    // Funciones de acciones de usuario
    //


    // Añade la acción de usuario de adición de la importación de valores del sensor pendiente
    function anyade_accion_usuario_anyadir_importacion_valores_sensor_pendiente($fila, $nombre_sensor, $clase_sensor)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_IMPORTACION_VALORES_SENSOR_PENDIENTE;
        $objeto_accion_usuario = $nombre_sensor;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $clase_sensor;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_VALORES] = $fila["tipo_valores"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_FICHERO] = $fila["nombre_fichero_csv"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_OPCIONES_FICHERO_CSV] = array(
            "tipo_valores" => $fila["tipo_valores"],
            "opciones_fichero_csv" => $fila["opciones_fichero_csv"]);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_OPCIONES_VALORES_FICHERO_CSV] = $fila["opciones_valores_fichero_csv"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_APLICAR_CALIBRACION] = $fila["aplicar_calibacion"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de borrado de valores del sensor
    function anyade_accion_usuario_borrar_valores_sensor(
        $clase_sensor,
        $nombre_sensor,
        $cadena_fecha_hora_inicio_borrado_base_datos_local,
        $cadena_fecha_hora_fin_borrado_base_datos_local,
        $borrar_valores_tiempo_real,
        $resultado_funcion_externa)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_BORRAR_VALORES_SENSOR;
        $objeto_accion_usuario = $nombre_sensor;

        // Parámetros de la acción
        $parametros_accion_usuario = array(
            PARAMETRO_ACCION_USUARIO_CLASE_SENSOR => $clase_sensor,
            PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR => $nombre_sensor,
            PARAMETRO_ACCION_USUARIO_FECHA_HORA_INICIO => $cadena_fecha_hora_inicio_borrado_base_datos_local,
            PARAMETRO_ACCION_USUARIO_FECHA_HORA_FIN => $cadena_fecha_hora_fin_borrado_base_datos_local,
            PARAMETRO_ACCION_USUARIO_BORRAR_VALORES_TIEMPO_REAL => $borrar_valores_tiempo_real);

        // Se recuperan los parámetros del resultado de la función externa
        $numero_valores_sensor_borrados = $resultado_funcion_externa["numero_valores_borrados"];
        if ($numero_valores_sensor_borrados > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados"];
            $cadena_fecha_hora_fin_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados"];
        }
        $numero_incrementos_valores_sensor_borrados = $resultado_funcion_externa["numero_incrementos_valores_borrados"];
        if ($numero_incrementos_valores_sensor_borrados > 0)
        {
            $cadena_fecha_hora_inicio_incrementos_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_inicio_incrementos_valores_borrados"];
            $cadena_fecha_hora_fin_incrementos_valores_borrados_funciones_utc = $resultado_funcion_externa["hora_fin_incrementos_valores_borrados"];
        }
        $numero_valores_sensor_borrados_cuartoshora = $resultado_funcion_externa["numero_valores_borrados_cuartoshora"];
        if ($numero_valores_sensor_borrados_cuartoshora > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_cuartoshora"];
            $cadena_fecha_hora_fin_valores_borrados_cuartoshora_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_cuartoshora"];
        }
        $numero_valores_sensor_borrados_horas = $resultado_funcion_externa["numero_valores_borrados_horas"];
        if ($numero_valores_sensor_borrados_horas > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_horas_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_horas"];
            $cadena_fecha_hora_fin_valores_borrados_horas_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_horas"];
        }
        $numero_valores_sensor_borrados_dias = $resultado_funcion_externa["numero_valores_borrados_dias"];
        if ($numero_valores_sensor_borrados_dias > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_dias_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_dias"];
            $cadena_fecha_hora_fin_valores_borrados_dias_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_dias"];
        }
        $numero_valores_sensor_borrados_meses = $resultado_funcion_externa["numero_valores_borrados_meses"];
        if ($numero_valores_sensor_borrados_meses > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_meses_funciones_utc = $resultado_funcion_externa["hora_inicio_valores_borrados_meses"];
            $cadena_fecha_hora_fin_valores_borrados_meses_funciones_utc = $resultado_funcion_externa["hora_fin_valores_borrados_meses"];
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        if ($numero_valores_sensor_borrados > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_valores_borrados_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_valores_borrados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_borrados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }
        if ($numero_incrementos_valores_sensor_borrados > 0)
        {
            $cadena_fecha_hora_inicio_incrementos_valores_borrados_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_incrementos_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_incrementos_valores_borrados_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_incrementos_valores_borrados_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_incrementos_valores_borrados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_incrementos_valores_borrados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_incrementos_valores_borrados_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_incrementos_valores_borrados_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }
        if ($numero_valores_sensor_borrados_cuartoshora > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_cuartoshora_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_valores_borrados_cuartoshora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_cuartoshora_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_cuartoshora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_borrados_cuartoshora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_cuartoshora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }
        if ($numero_valores_sensor_borrados_horas > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_horas_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_horas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_valores_borrados_horas_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_horas_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_valores_borrados_horas_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_horas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_borrados_horas_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_horas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }
        if ($numero_valores_sensor_borrados_dias > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_dias_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_dias_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_valores_borrados_dias_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_dias_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_valores_borrados_dias_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_dias_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_borrados_dias_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_dias_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }
        if ($numero_valores_sensor_borrados_meses > 0)
        {
            $cadena_fecha_hora_inicio_valores_borrados_meses_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_borrados_meses_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_fin_valores_borrados_meses_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_borrados_meses_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_BASE_DATOS);
            $cadena_fecha_hora_inicio_valores_borrados_meses_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_borrados_meses_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_borrados_meses_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_borrados_meses_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        }

        // Resultado de la acción
        $resultado_accion_usuario = array();
        if ($numero_valores_sensor_borrados > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_VALORES_BORRADOS_FECHAS_HORAS] = array(
                "numero" => $numero_valores_sensor_borrados,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_valores_borrados_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_valores_borrados_base_datos_local);
        }
        if ($numero_incrementos_valores_sensor_borrados > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_INCREMENTOS_VALORES_BORRADOS_FECHAS_HORAS] = array(
                "numero" => $numero_incrementos_valores_sensor_borrados,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_incrementos_valores_borrados_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_incrementos_valores_borrados_base_datos_local);
        }
        if ($numero_valores_sensor_borrados_cuartoshora > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_VALORES_BORRADOS_CUARTOSHORA_FECHAS_HORAS] = array(
                "numero" => $numero_valores_sensor_borrados_cuartoshora,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_valores_borrados_cuartoshora_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_valores_borrados_cuartoshora_base_datos_local);
        }
        if ($numero_valores_sensor_borrados_horas > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_VALORES_BORRADOS_HORAS_FECHAS_HORAS] = array(
                "numero" => $numero_valores_sensor_borrados_horas,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_valores_borrados_horas_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_valores_borrados_horas_base_datos_local);
        }
        if ($numero_valores_sensor_borrados_dias > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_VALORES_BORRADOS_DIAS_FECHAS_HORAS] = array(
                "numero" => $numero_valores_sensor_borrados_dias,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_valores_borrados_dias_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_valores_borrados_dias_base_datos_local);
        }
        if ($numero_valores_sensor_borrados_meses > 0)
        {
            $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_VALORES_BORRADOS_MESES_FECHAS_HORAS] = array(
                "numero" => $numero_valores_sensor_borrados_meses,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_valores_borrados_meses_base_datos_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_valores_borrados_meses_base_datos_local);
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            $resultado_accion_usuario);
    }


    // Añade la acción de usuario de recálculo de valores de clase de un sensor
    function anyade_accion_usuario_recalcular_valores_clase_sensor(
        $clase_sensor,
        $nombre_sensor,
        $cadena_fecha_base_datos_local)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_GUARDAR_FECHA_RECALCULO_VALORES_CLASE_SENSOR;
        $objeto_accion_usuario = $nombre_sensor;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $clase_sensor;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $cadena_fecha_base_datos_local;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de envío de valores manuales a un sensor
    function anyade_accion_usuario_enviar_valores_manuales_sensor(
        $fila_sensor,
        $cadena_fecha_hora_base_datos_local,
        $valores,
        $incrementos,
        $horas_incrementos)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ENVIAR_VALORES_MANUALES_SENSOR;
        $objeto_accion_usuario = $fila_sensor["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_sensor["clase"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_HORA] = $cadena_fecha_hora_base_datos_local;
        switch ($fila_sensor["tipo_valores"])
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALORES_SENSOR] = $valores;
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCREMENTOS_SENSOR] = $incrementos;
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORAS_INCREMENTOS_SENSOR] = $horas_incrementos;
                break;
            }
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    //
    // Funciones auxiliares
    //


    // Convierte el fichero de valores diarios en fichero de valores horarios (con hora UTC)
    function convierte_fichero_valores_diarios_en_fichero_valores_horarios_utc(
        $ruta_fichero_valores_diarios,
        $ruta_fichero_valores_horarios,
        $funcion_conversion_valores)
    {
        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Se crean las filas de valores horarios
        $filas_valores_horarios = array();
        $fichero_valores_diarios = fopen($ruta_fichero_valores_diarios, 'r');
        if ($fichero_valores_diarios != false)
        {
            $numero_fila_fichero_valores_diarios = 0;
            while (feof($fichero_valores_diarios) == false)
            {
                // Se recuperan los valores de cada una de las filas (se ignora la primera fila de cabeceras)
                $numero_fila_fichero_valores_diarios += 1;
                $fila_fichero_valores_diarios = fgets($fichero_valores_diarios);
                if (($fila_fichero_valores_diarios == false) || ($numero_fila_fichero_valores_diarios == 1))
                {
                    continue;
                }

                // Nota: Al leer la fila se lee un último caracter como espacio en blanco (aunque no hay y sólo hay un salto de fila), se elimina
                $fila_fichero_valores_diarios = trim($fila_fichero_valores_diarios);
                $valores_fila_fichero_valores_diarios = explode(SEPARADOR_COLUMNAS_FICHERO_CSV_VALORES_DIARIOS_COMPRA_ENERGIA, $fila_fichero_valores_diarios);

                // Se añaden los valores de la fila al fichero
                $cadena_fecha_hora_inicial_fila_local_local = $valores_fila_fichero_valores_diarios[0].", 00:00";
                $cadena_fecha_hora_inicial_fila_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicial_fila_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
                $fecha_hora_inicial_fila_utc = convierte_cadena_a_fecha($cadena_fecha_hora_inicial_fila_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
                $numero_hora_fila = 0;
                for ($i = 1; $i < count($valores_fila_fichero_valores_diarios); $i++)
                {
                    $numero_hora_fila += 1;
                    $cadena_valor_hora = $valores_fila_fichero_valores_diarios[$i];
                    if ($cadena_valor_hora == "")
                    {
                        continue;
                    }
                    $valor_hora = (float) $cadena_valor_hora;

                    // Se convierte el valor (si es necesario)
                    if ($funcion_conversion_valores !== NULL)
                    {
                        $valor_hora = $funcion_conversion_valores($valor_hora);
                    }

                    $fecha_hora_valor_hora_utc = clone $fecha_hora_inicial_fila_utc;
                    $fecha_hora_valor_hora_utc->add(new DateInterval("PT".($numero_hora_fila - 1)."H"));
                    $cadena_fecha_hora_valor_hora_local_utc = convierte_fecha_a_cadena($fecha_hora_valor_hora_utc, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_fecha_hora_valor_hora_local_utc .= ":00";

                    array_push($filas_valores_horarios, array(
                        $cadena_fecha_hora_valor_hora_local_utc,
                        $valor_hora));
                }
            }
        }

        // Se escriben las filas de valores horarios en el fichero CSV
        escribe_fichero_valores_csv($ruta_fichero_valores_horarios, $filas_valores_horarios);
    }


    // Crea un fichero CSV con los valores exportados
    function crea_fichero_csv_valores_exportados(
        $id_sensor,
        $nombre_sensor,
        $clase_sensor,
        $sufijo_fichero_valores,
        $filas_valores_sensor_csv,
        $campos,
        $campos_clase_sensor,
        $punto_decimal_exportacion_valores_sensor,
        $zona_horaria_exportacion_valores_sensor,
        &$rutas_relativas_ficheros_valores_exportados)
    {
        $idiomas = new Idiomas();

        // Pasos de creación de un fichero CSV de valores exportados de un sensor:
        // - Conversión de punto decimal
        // - Conversión de fechas UTC a zona horaria especificada
        // - Se crea el nombre del fichero
        // - Se crean las rutas tanto de cliente como de servidor
        // - Se crean las cabeceras del fichero CSV
        // - Se escribe el fichero CSV

        if ($punto_decimal_exportacion_valores_sensor == ",")
        {
            for ($i = 0; $i < count($filas_valores_sensor_csv); $i++)
            {
                foreach ($filas_valores_sensor_csv[$i] as $clave => $valor)
                {
                    if (($clave == "fecha_hora") || ($clave == "horario_verano"))
                    {
                        continue;
                    }
                    $valor = str_replace(".", ",", $valor);
                    $filas_valores_sensor_csv[$i][$clave] = $valor;
                }
            }
        }

        for ($i = 0; $i < count($filas_valores_sensor_csv); $i++)
        {
            $cadena_fecha_hora_fichero_csv_utc = convierte_formato_fecha($filas_valores_sensor_csv[$i]["fecha_hora"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_FICHERO_CSV);
            $cadena_fecha_hora_fichero_csv_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_FICHERO_CSV, ZONA_HORARIA_UTC, $zona_horaria_exportacion_valores_sensor);
            $filas_valores_sensor_csv[$i]["fecha_hora"] = $cadena_fecha_hora_fichero_csv_local;
            if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
            {
                $horario_verano = dame_horario_verano_cadena_fecha_hora_utc($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_FICHERO_CSV, $zona_horaria_exportacion_valores_sensor);
                $cadena_horario_verano = $horario_verano? 1:0;
                $filas_valores_sensor_csv[$i]["horario_verano"] = $cadena_horario_verano;
            }
        }

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        $numero_valores_exportados = count($filas_valores_sensor_csv);
        if ($numero_valores_exportados > 0)
        {
            $cadena_fecha_hora_inicio_valores_fichero_local = convierte_formato_fecha($filas_valores_sensor_csv[0]["fecha_hora"], FORMATO_FECHA_HORA_FICHERO_CSV, FORMATO_FECHA_HORA_FICHERO);
            $cadena_fecha_hora_fin_valores_fichero_local = convierte_formato_fecha($filas_valores_sensor_csv[$numero_valores_exportados - 1]["fecha_hora"], FORMATO_FECHA_HORA_FICHERO_CSV, FORMATO_FECHA_HORA_FICHERO);
            $sufijo_nombre_sensor = convierte_ascii_estandar($nombre_sensor);
            $sufijo_nombre_sensor = reemplaza_caracteres_no_alfanumericos($sufijo_nombre_sensor, "_");
            $nombre_fichero_valores_exportados = $sufijo_nombre_sensor."-".$sufijo_fichero_valores."-".$cadena_fecha_hora_inicio_valores_fichero_local."-".$cadena_fecha_hora_fin_valores_fichero_local;
            if ($zona_horaria_exportacion_valores_sensor == ZONA_HORARIA_UTC)
            {
                $nombre_fichero_valores_exportados .= "_UTC";
            }
            $nombre_fichero_valores_exportados .= ".csv";

            $ruta_absoluta_fichero_valores_exportados = $directorio_absoluto_ficheros_temporales_usuario.'/'.$nombre_fichero_valores_exportados;
            $ruta_relativa_fichero_valores_exportados = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero_valores_exportados;

            $cabecera_fichero_valores_sensor = array();
            $nombre_columna_hora = $idiomas->_("Fecha");
            if ($zona_horaria_exportacion_valores_sensor == ZONA_HORARIA_UTC)
            {
                $nombre_columna_hora .= " (".$idiomas->_("UTC").")";
            }
            array_push($cabecera_fichero_valores_sensor, $nombre_columna_hora);
            foreach ($campos as $campo)
            {
                $nombre_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
                $unidad_medida_campo = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                $nombre_columna_campo = $nombre_campo;
                if ($unidad_medida_campo != "")
                {
                    $nombre_columna_campo .= " (".$unidad_medida_campo.")";
                }
                array_push($cabecera_fichero_valores_sensor, $nombre_columna_campo);
            }
            if ($zona_horaria_exportacion_valores_sensor != ZONA_HORARIA_UTC)
            {
                array_push($cabecera_fichero_valores_sensor, $idiomas->_("Horario de verano"));
            }
            foreach ($campos_clase_sensor as $campo_clase_sensor)
            {
                $nombre_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo_clase_sensor);
                $unidad_medida_campo = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo_clase_sensor);
                $nombre_columna_campo = $nombre_campo;
                if ($unidad_medida_campo != "")
                {
                    $nombre_columna_campo .= " (".$unidad_medida_campo.")";
                }
                array_push($cabecera_fichero_valores_sensor, $nombre_columna_campo);
            }
            array_unshift($filas_valores_sensor_csv, $cabecera_fichero_valores_sensor);

            escribe_fichero_valores_csv($ruta_absoluta_fichero_valores_exportados, $filas_valores_sensor_csv);
            array_push($rutas_relativas_ficheros_valores_exportados, $ruta_relativa_fichero_valores_exportados);
        }

        return ($numero_valores_exportados);
    }
?>
