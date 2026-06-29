<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/util_eventos_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    //
    // Funciones de información de eventos
    //


    // Devuelve la información de activaciones de eventos
    function dame_activaciones_eventos($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $clase_sensor = $parametros["clase_sensor"];
        $origen_eventos = $parametros["origen_evento"];
        $id_origen_eventos = $parametros["id_origen_evento"];
        $nombre_origen_eventos = $parametros["nombre_origen_evento"];
        $granularidad_eventos = $parametros["granularidad_evento"];
        $ids_eventos = $parametros["ids_eventos"];
        $nombres_eventos = $parametros["nombres_eventos"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $campo = $parametros["campo"];
        $nombre_campo = $parametros["nombre_campo"];

        // Comprueba si los eventos son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        $ids_eventos_usuario_actual = Evento::dame_ids_eventos_usuario_actual($ids_sensores_usuario_actual);
        foreach ($ids_eventos as $id_evento)
        {
            if (in_array($id_evento, $ids_eventos_usuario_actual) == false)
            {
                throw new Exception("Evento no visible por el usuario actual (id: '".$id_evento."')");
            }
        }

        // Comprobación de eventos seleccionados
        if (count($ids_eventos) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "sin_eventos_seleccionados" => true);
            return ($resultado);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Timestamps de las fechas inicial y final de la consulta
        $timestamp_fecha_hora_inicio_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
        $timestamp_fecha_hora_inicio_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
        $timestamp_fecha_hora_fin_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
        $timestamp_fecha_hora_fin_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;

        // Sensor seleccionado y mostrar gráfica de valores
        $sensor_seleccionado = (($origen_eventos == ORIGEN_EVENTO_SENSOR) && (($id_origen_eventos != ID_NINGUNO) && ($id_origen_eventos != ID_TODOS)));
        $mostrar_grafica_valores = (($sensor_seleccionado == true) && ($campo != CAMPO_NINGUNO));

        // Variables para la gráfica de valores de sensor
        $grafica_valores_sensor = new VectorDatos();
        $grafica_valores_acumulados_sensor = new VectorDatos();
        $intervalo_valores = NULL;
        $unidad_medida = NULL;
        $min_valor = NULL;
        $max_valor = NULL;
        $min_valor_acumulado = NULL;
        $max_valor_acumulado = NULL;

        // Se obtienen los datos para la gráfica de valores (si es necesario)
        if ($mostrar_grafica_valores == true)
        {
            // Se recupera el nombre del origen de los eventos (si es necesario)
            if ($nombre_origen_eventos === NULL)
            {
                switch ($origen_eventos)
                {
                    case ORIGEN_EVENTO_SENSOR:
                    {
                        $nombre_origen_eventos = dame_nombre_sensor($id_origen_eventos);
                        break;
                    }
                    case ORIGEN_EVENTO_GRUPO_SENSORES:
                    {
                        $nombre_origen_eventos = dame_nombre_grupo_sensores($id_origen_eventos);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Origen de eventos incorrecto: '".$origen_eventos."'");
                    }
                }
            }

            // Intervalo de valores (dependiente de la granularidad de los eventos)
            switch ($granularidad_eventos)
            {
                case GRANULARIDAD_TIEMPO_REAL:
                {
                    $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                    break;
                }
                case GRANULARIDAD_CUARTOHORARIA:
                {
                    $intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                    break;
                }
                case GRANULARIDAD_HORARIA:
                {
                    $intervalo_valores = INTERVALO_VALORES_HORA;
                    break;
                }
                default:
                {
                    throw new Exception("Granularidad de eventos incorrecta: '".$granularidad_eventos."'");
                }
            }

            // Se realiza la consulta de valores del sensor
            $consulta_valores = dame_consulta_valores_sensor(
                $id_origen_eventos,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                NULL,
                NULL,
                NULL,
                NULL);
            $res_valores = $bd_datos->ejecuta_consulta($consulta_valores);
            if ($res_valores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores."'");
            }

            // Flag de campo incremental
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
            $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica_sensor = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_origen_eventos);

            // Se recorren los valores del sensor
            $datos_grafica_valores_sensor = new VectorDatos();
            $datos_grafica_valores_acumulados_sensor = new VectorDatos();
            $valores_sensor = array();
            $min_valor = (float) (INF);
            $max_valor = (float) (-INF);
            $min_valor_acumulado = (float) (INF);
            $max_valor_acumulado = (float) (-INF);
            $suma_valores = 0;
            $timestamp_fecha_hora_valor_sensor_anterior_utc = NULL;
            while ($fila_valores = $res_valores->dame_siguiente_fila())
            {
                // Fecha y valor
                if ($fila_valores[$campo] === NULL)
                {
                    continue;
                }
                $cadena_fecha_valor_sensor_base_datos_utc = $fila_valores["fecha_hora"];
                $valor_sensor = (float) $fila_valores[$campo];

                // Máximo y mínimo
                if ($valor_sensor > $max_valor)
                {
                    $max_valor = $valor_sensor;
                }
                if ($valor_sensor < $min_valor)
                {
                    $min_valor = $valor_sensor;
                }

                // Se añade un valor nulo si la diferencia es mayor que el intervalo de valores para separar los valores
                $timestamp_fecha_hora_valor_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_valor_sensor_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_valor_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if (($segundos_maximos_entre_valores_grafica_sensor !== NULL) && ($timestamp_fecha_hora_valor_sensor_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_valor_sensor_utc - $timestamp_fecha_hora_valor_sensor_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica_sensor)
                    {
                        $datos_grafica_valores_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_valor_sensor_anterior_utc = $timestamp_fecha_hora_valor_sensor_utc;

                // Se añade el valor
                $datos_grafica_valores_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_utc, $valor_sensor);

                // Si el campo es incremental
                if ($campo_incremental == true)
                {
                    $suma_valores += $valor_sensor;
                    if ($suma_valores > $max_valor_acumulado)
                    {
                        $max_valor_acumulado = $suma_valores;
                    }
                    if ($suma_valores < $min_valor_acumulado)
                    {
                        $min_valor_acumulado = $suma_valores;
                    }

                    $datos_grafica_valores_acumulados_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_utc, $suma_valores);
                }
            }

            // Gráfica de valores del sensor (y acumulado si el campo es incremental)
            if ($datos_grafica_valores_sensor->dame_numero_datos() > 0)
            {
                $grafica_valores_sensor->anyade_dato($datos_grafica_valores_sensor->dame_datos());
                $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_origen_eventos, $campo);

                if ($campo_incremental == true)
                {
                    $grafica_valores_acumulados_sensor->anyade_dato($datos_grafica_valores_acumulados_sensor->dame_datos());
                }
            }
        }

        // Se realiza la consulta de activaciones (y desactivaciones) de eventos
        $consulta_activaciones = "
            SELECT
                granularidad,
                clase,
                parametros_clase,
                sensor,
                hora AS fecha_hora,
                nombres_eventos_instantaneos,
                nombres_eventos_activados,
                nombres_eventos_desactivados,
                hora_valores AS fecha_hora_valores,
                valores
            FROM activaciones_eventos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (hora_valores >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora_valores <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                AND (valores IS NOT NULL)";
        if ($sensor_seleccionado == true)
        {
            $consulta_activaciones .= "
                AND (sensor = '".$bd_datos->_($nombre_origen_eventos)."')";
        }
        $consulta_activaciones .= "
            ORDER BY hora_valores ASC";
        $res_activaciones = $bd_datos->ejecuta_consulta($consulta_activaciones);
        if ($res_activaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_activaciones."'");
        }

        // Número de eventos
        $numero_eventos = count($ids_eventos);

        // Información y datos de los eventos
        $filas_eventos = array();
        $origenes_eventos = array();
        $eventos_persistentes = array();
        $datos_graficas_activaciones_eventos = array();
        $primeros_valores_eventos = array();
        $ultimos_valores_eventos = array();
        $tablas_activaciones_eventos = array();
        $numeros_activaciones_eventos = array();
        $numeros_elementos_tablas_activaciones_eventos = array();
        $limites_elementos_tablas_activaciones_eventos_superados = array();
        $limite_elementos_tablas_activaciones_eventos_superado = false;
        foreach ($ids_eventos as $id_evento)
        {
            $fila_evento = dame_fila_evento($id_evento);
            array_push($filas_eventos, $fila_evento);

            $origen_evento = $fila_evento["origen"];
            array_push($origenes_eventos, $origen_evento);

            $evento_persistente = Evento::dame_tipo_evento_persistente($fila_evento["tipo"]);
            array_push($eventos_persistentes, $evento_persistente);

            $datos_grafica_activaciones_evento = new VectorDatos();
            array_push($datos_graficas_activaciones_eventos, $datos_grafica_activaciones_evento);

            array_push($primeros_valores_eventos, NULL);
            array_push($ultimos_valores_eventos, NULL);

            // Tabla de activaciones de evento
            $params_tabla_activaciones_evento = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_ACTIVACIONES_EVENTO,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ACTIVACIONES_EVENTO),
                "generar_valores_xml" => true
            );
            $titulo_tabla_activaciones_evento = $fila_evento["nombre"];
            switch ($origen_evento)
            {
                case ORIGEN_EVENTO_GRUPO_SENSORES:
                {
                    $titulo_tabla_activaciones_evento .= " (".$idiomas->_("grupo").")";
                    break;
                }
            }
            $tabla_activaciones_evento = new TablaDatos(
                "tabla-activaciones-evento-activaciones-eventos",
                $titulo_tabla_activaciones_evento,
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_activaciones_evento
            );
            $cabecera_tabla_activaciones_evento = array(
                $idiomas->_("Activación"),
                $idiomas->_("Fecha")." (".$idiomas->_("valores").")",
                $idiomas->_("Fecha")." (".$idiomas->_("evento").")",
                $idiomas->_("Sensor"),
                $idiomas->_("Valores")
            );
            $tabla_activaciones_evento->anyade_cabecera("", $cabecera_tabla_activaciones_evento);
            array_push($tablas_activaciones_eventos, $tabla_activaciones_evento);
            array_push($numeros_activaciones_eventos, 0);
            array_push($numeros_elementos_tablas_activaciones_eventos, 0);
            array_push($limites_elementos_tablas_activaciones_eventos_superados, false);
        }

        // Líneas verticales de activaciones de eventos
        $lineas_verticales_activaciones_eventos = array();

        // Se recorren las activaciones de eventos
        while ($fila_activaciones = $res_activaciones->dame_siguiente_fila())
        {
            // Nombres de los eventos
            $nombres_eventos_instantaneos = explode(",", $fila_activaciones["nombres_eventos_instantaneos"]);
            $nombres_eventos_activados = explode(",", $fila_activaciones["nombres_eventos_activados"]);
            $nombres_eventos_desactivados = explode(",", $fila_activaciones["nombres_eventos_desactivados"]);

            // Fechas
            $cadena_fecha = $fila_activaciones["fecha_hora"];
            $cadena_fecha_valores_sensor_base_datos_utc = $fila_activaciones["fecha_hora_valores"];
            $timestamp_fecha_hora_valores_sensor_utc = NULL;

            // Nombre y valores del sensor del evento
            $nombre_sensor = $fila_activaciones["sensor"];
            $clase_activaciones = $fila_activaciones["clase"];
            $parametros_clase_activaciones = $fila_activaciones["parametros_clase"];
            $incrementos_tiempo_real_horarios_activaciones = $fila_activaciones["incrementos_tiempo_real_horarios"];
            $valores_sensor = $fila_activaciones["valores"];
            $cadena_valores_sensor = NULL;

            // Se recorren los eventos
            for ($i = 0; $i < $numero_eventos; $i++)
            {
                // Información del evento
                $fila_evento = $filas_eventos[$i];
                $nombre_evento = $fila_evento["nombre"];
                $origen_evento = $fila_evento["origen"];
                $granularidad_evento = $fila_evento["granularidad"];
                $evento_persistente = $eventos_persistentes[$i];
                $primer_valor_evento = $primeros_valores_eventos[$i];
                $ultimo_valor_evento = $ultimos_valores_eventos[$i];

                // Evento instantáneo, activado o desactivado
                $evento_instantaneo = (in_array($nombre_evento, $nombres_eventos_instantaneos) == true);
                $evento_activado = (in_array($nombre_evento, $nombres_eventos_activados) == true);
                $evento_desactivado = (in_array($nombre_evento, $nombres_eventos_desactivados) == true);
                if (($evento_instantaneo == true) || ($evento_activado == true) || ($evento_desactivado == true))
                {
                    // Fecha de los valores del sensor en el evento
                    if ($timestamp_fecha_hora_valores_sensor_utc === NULL)
                    {
                        $timestamp_fecha_hora_valores_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_valores_sensor_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $timestamp_fecha_hora_valores_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                        $fecha_valores_sensor_utc = convierte_cadena_a_fecha($cadena_fecha_valores_sensor_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $fecha_valores_sensor_local = dame_fecha_hora_local($fecha_valores_sensor_utc);
                        $cadena_fecha_valores_sensor_local_local = convierte_fecha_a_cadena($fecha_valores_sensor_local, $_SESSION["formato_fecha_hora_local"]);
                    }

                    // Cadena de valores del sensor en el evento
                    if ($cadena_valores_sensor === NULL)
                    {
                        switch ($granularidad_evento)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                $cadena_valores_sensor = NodoSensor::dame_cadena_valores_sensor(
                                    ID_NINGUNO,
                                    ID_NINGUNO,
                                    $cadena_fecha_valores_sensor_base_datos_utc,
                                    $valores_sensor,
                                    $clase_activaciones,
                                    $parametros_clase_activaciones,
                                    $incrementos_tiempo_real_horarios_activaciones,
                                    GRANULARIDAD_TIEMPO_REAL,
                                    SEPARADOR_VALOR_INCREMENTO_SENSOR,
                                    FORMATO_CADENA_VALORES_SENSOR_COMPLETO,
                                    NULL);
                                break;
                            }
                            case GRANULARIDAD_CUARTOHORARIA:
                            case GRANULARIDAD_HORARIA:
                            {
                                $cadena_valores_sensor = NodoSensor::dame_cadena_valores_clase_sensor(
                                    ID_NINGUNO,
                                    ID_NINGUNO,
                                    $cadena_fecha_valores_sensor_base_datos_utc,
                                    $valores_sensor,
                                    $clase_activaciones,
                                    $parametros_clase_activaciones,
                                    $granularidad_evento,
                                    NULL);
                                break;
                            }
                        }
                    }

                    // Valor del evento (para la gráfica)
                    if (($evento_instantaneo == true) || ($evento_activado == true))
                    {
                        $valor_evento = VALOR_SI;
                    }
                    else
                    {
                        $valor_evento = VALOR_NO;
                    }

                    // Se añade el valor inicial del evento (sólo si es persistente y es evento de sensor)
                    if ($primer_valor_evento === NULL)
                    {
                        if (($evento_persistente == true) && ($origen_evento == ORIGEN_EVENTO_SENSOR))
                        {
                            if ($valor_evento == VALOR_SI)
                            {
                                $valor_inicial_evento = VALOR_NO;
                            }
                            else
                            {
                                $valor_inicial_evento = VALOR_SI;
                            }

                            $datos_grafica_activaciones_evento = $datos_graficas_activaciones_eventos[$i];
                            $datos_grafica_activaciones_evento->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_inicio_utc,
                                $valor_inicial_evento,
                                "[".$nombre_sensor."]"."<br/>".$idiomas->_("Estado inicial"));
                            $primeros_valores_eventos[$i] = $valor_evento;
                        }
                    }
                    $ultimos_valores_eventos[$i] = $valor_evento;

                    // Se añade el valor del evento
                    $datos_grafica_activaciones_evento = $datos_graficas_activaciones_eventos[$i];
                    $datos_grafica_activaciones_evento->anyade_tupla_pareja_datos_etiqueta(
                        $timestamp_fecha_hora_valores_sensor_utc,
                        $valor_evento,
                        "[".$nombre_sensor."]"."<br/>".$cadena_valores_sensor." (".$cadena_fecha_valores_sensor_local_local.")");

                    // Se añade la línea vertical de la activación (si es necesario)
                    if ($mostrar_grafica_valores == true)
                    {
                        if ($valor_evento == VALOR_SI)
                        {
                            $color_linea_vertical = COLOR_LINEA_GRAFICA_VERDE_OSCURO;
                            $texto_tooltip = $nombre_evento." (".$idiomas->_("activación").")"." (".$cadena_fecha_valores_sensor_local_local.")";
                        }
                        else
                        {
                            $color_linea_vertical = COLOR_LINEA_GRAFICA_ROJO;
                            $texto_tooltip = $nombre_evento." (".$idiomas->_("desactivación").")"." (".$cadena_fecha_valores_sensor_local_local.")";
                        }
                        $linea_vertical_activacion_evento = array(
                            "valor" => $timestamp_fecha_hora_valores_sensor_utc,
                            "color" => $color_linea_vertical,
                            "texto_tooltip" => $texto_tooltip);
                        array_push($lineas_verticales_activaciones_eventos, $linea_vertical_activacion_evento);
                    }

                    // Se añade la fila a la tabla de activaciones del evento
                    if ($numeros_elementos_tablas_activaciones_eventos[$i] < NUMERO_MAXIMO_FILAS_TABLAS_ACTIVACIONES_EVENTOS)
                    {
                        $tabla_activaciones_evento = $tablas_activaciones_eventos[$i];
                        if (($evento_instantaneo == true) || ($evento_activado == true))
                        {
                            $icono_activado = "<i class='icon-circle color-verde'>".
                                "<texto class='elemento-oculto'>".htmlspecialchars($idiomas->_("Activación"), ENT_QUOTES)."</texto></i>";
                        }
                        else
                        {
                            $icono_activado = "<i class='icon-circle color-rojo'>".
                                "<texto class='elemento-oculto'>".htmlspecialchars($idiomas->_("Desactivación"), ENT_QUOTES)."</texto></i>";
                        }
                        $timestamp_fecha = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $timestamp_fecha -= $milisegundos_desfase_zonas_horarias_cliente_local;
                        $fecha_valores_utc = convierte_cadena_a_fecha($cadena_fecha, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $fecha_valores_local = dame_fecha_hora_local($fecha_valores_utc);
                        $cadena_fecha_local = convierte_fecha_a_cadena($fecha_valores_local, $_SESSION["formato_fecha_hora_local"]);
                        $fila_activacion_evento = array(
                            $icono_activado,
                            $cadena_fecha_valores_sensor_local_local,
                            $cadena_fecha_local,
                            $nombre_sensor,
                            $cadena_valores_sensor);
                        $tabla_activaciones_evento->anyade_fila("fila-activacion-evento", $fila_activacion_evento);
                        $numeros_elementos_tablas_activaciones_eventos[$i] += 1;
                    }
                    else
                    {
                        $limites_elementos_tablas_activaciones_eventos_superados[$i] = true;
                        $limite_elementos_tablas_activaciones_eventos_superado = true;
                    }

                    $numeros_activaciones_eventos[$i] += 1;
                }
            }
        }

        // Se recorren los eventos
        for ($i = 0; $i < $numero_eventos; $i++)
        {
            // Información del evento
            $fila_evento = $filas_eventos[$i];
            $nombre_evento = $fila_evento["nombre"];
            $origen_evento = $fila_evento["origen"];
            $granularidad_evento = $fila_evento["granularidad"];
            $evento_persistente = $eventos_persistentes[$i];
            $primer_valor_evento = $primeros_valores_eventos[$i];
            $ultimo_valor_evento = $ultimos_valores_eventos[$i];

            // Si el origen de evento es sensor:
            // - Se añade el estado final del evento
            // - Si no hay activaciones durante el periodo,
            //   se establecen el estado inicial y final al último estado del evento antes de la fecha de inicio (si no hay estado se establece el evento a desactivado)
            switch ($origen_evento)
            {
                case ORIGEN_EVENTO_SENSOR:
                {
                    // Si la fecha final es mayor que la fecha actual, se utiliza la fecha actual
                    $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
                    $fecha_actual_utc = dame_fecha_hora_actual_utc();
                    if ($fecha_actual_utc >= $fecha_hora_fin_utc)
                    {
                        $fecha_fin_ahora = false;
                        $timestamp_fecha_hora_estado_final_utc = $timestamp_fecha_hora_fin_utc;
                    }
                    else
                    {
                        $fecha_fin_ahora = true;
                        $timestamp_fecha_hora_estado_final_utc = dame_timestamp_ahora_milisegundos_utc();
                        $timestamp_fecha_hora_estado_final_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                    }

                    // Se añade el valor final del evento (sólo si es persistente)
                    if ($evento_persistente == true)
                    {
                        if ($primer_valor_evento !== NULL)
                        {
                            $datos_grafica_activaciones_evento = $datos_graficas_activaciones_eventos[$i];

                            $etiqueta_estado_final = "[".$nombre_sensor."]"."<br/>".$idiomas->_("Estado final");
                            if ($fecha_fin_ahora == true)
                            {
                                $etiqueta_estado_final .= " (".$idiomas->_("ahora").")";
                            }
                            $datos_grafica_activaciones_evento->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_estado_final_utc,
                                $ultimo_valor_evento,
                                $etiqueta_estado_final);
                        }
                        else
                        {
                            // Se recupera si el evento estaba activado o desactivado
                            $fila_evento = $filas_eventos[$i];
                            $nombre_evento = $fila_evento["nombre"];
                            $nombre_sensor = $idiomas->_("Ninguno");
                            $consulta_activaciones = "
                                SELECT
                                    sensor,
                                    nombres_eventos_activados,
                                    nombres_eventos_desactivados
                                FROM activaciones_eventos
                                WHERE
                                    (red = '".$_SESSION["id_red"]."')
                                    AND (hora_valores < '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                                    AND ((FIND_IN_SET('".$bd_datos->_($nombre_evento)."', nombres_eventos_activados) > 0) OR
                                        (FIND_IN_SET('".$bd_datos->_($nombre_evento)."', nombres_eventos_desactivados) > 0))
                                ORDER BY hora_valores DESC
                                LIMIT 1";
                            $res_activaciones = $bd_datos->ejecuta_consulta($consulta_activaciones);
                            if ($res_activaciones == false)
                            {
                                throw new Exception("Error en la consulta: '".$consulta_activaciones."'");
                            }
                            if ($res_activaciones->dame_numero_filas() > 0)
                            {
                                $fila_activaciones = $res_activaciones->dame_siguiente_fila();
                                $nombres_eventos_activados = explode(",", $fila_activaciones["nombres_eventos_activados"]);
                                if (in_array($nombre_evento, $nombres_eventos_activados) == true)
                                {
                                    $nombre_sensor = $fila_activaciones["sensor"];
                                    $ultimo_valor_evento = VALOR_SI;
                                }
                                else
                                {
                                    $ultimo_valor_evento = VALOR_NO;
                                }
                            }
                            else
                            {
                                $ultimo_valor_evento = VALOR_NO;
                            }

                            $datos_grafica_activaciones_evento = $datos_graficas_activaciones_eventos[$i];
                            $datos_grafica_activaciones_evento->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_inicio_utc,
                                $ultimo_valor_evento,
                                "[".$nombre_sensor."]"."<br/>".$idiomas->_("Estado inicial"));

                            $etiqueta_estado_final = "[".$nombre_sensor."]"."<br/>".$idiomas->_("Estado final");
                            if ($fecha_fin_ahora == true)
                            {
                                $etiqueta_estado_final .= " (".$idiomas->_("ahora").")";
                            }
                            $datos_grafica_activaciones_evento->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_estado_final_utc,
                                $ultimo_valor_evento,
                                $etiqueta_estado_final);
                        }
                    }
                    break;
                }
            }
        }

        // Variables para dibujar las gráficas y de activaciones de los eventos
        $graficas_activaciones_eventos = array();
        foreach ($datos_graficas_activaciones_eventos as $datos_grafica_activaciones_evento)
        {
            $grafica_activaciones_evento = new VectorDatos();
            $grafica_activaciones_evento->anyade_dato($datos_grafica_activaciones_evento->dame_datos());
            array_push($graficas_activaciones_eventos, $grafica_activaciones_evento->dame_datos());
        }

        // Variables para las tablas de activaciones de los eventos
        $datos_tablas_activaciones_eventos = array();
        for ($i = 0; $i < count($tablas_activaciones_eventos); $i++)
        {
            $tabla_activaciones_evento = $tablas_activaciones_eventos[$i];
            $texto_pie = $idiomas->_("Número de activaciones y desactivaciones").": ".$numeros_activaciones_eventos[$i];
            if ($limites_elementos_tablas_activaciones_eventos_superados[$i] == true)
            {
                $texto_pie .= " (".$idiomas->_("mostradas primeras").": ".$numeros_elementos_tablas_activaciones_eventos[$i].")";
            }
            $tabla_activaciones_evento->anyade_pie($texto_pie);
            array_push($datos_tablas_activaciones_eventos, $tabla_activaciones_evento->dame_tabla());
        }

        // Valores máximos y mínimos
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_valor_acumulado == INF)
        {
            $min_valor_acumulado = "ND";
        }
        if ($max_valor_acumulado == -INF)
        {
            $max_valor_acumulado = "ND";
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "intervalo_valores" => $intervalo_valores,
            "unidad_medida" => $unidad_medida,
            "grafica_valores_sensor" => $grafica_valores_sensor->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados_sensor" => $grafica_valores_acumulados_sensor->dame_datos(),
            "min_valor_acumulado" => $min_valor_acumulado,
            "max_valor_acumulado" => $max_valor_acumulado,
            "lineas_verticales_activaciones_eventos" => $lineas_verticales_activaciones_eventos,
            "graficas_activaciones_eventos" => $graficas_activaciones_eventos,
            "tablas_activaciones_eventos" => $datos_tablas_activaciones_eventos,
            "limite_elementos_tablas_activaciones_eventos_superado" => $limite_elementos_tablas_activaciones_eventos_superado,
            "origenes_eventos" => $origenes_eventos,
            "eventos_persistentes" => $eventos_persistentes,
            "nombre_campo" => $nombre_campo,
            "nombres_eventos" => $nombres_eventos,
            "nombre_origen_eventos" => $nombre_origen_eventos);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_sensores_activaciones_eventos()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_SENSOR);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_ACUMULADOS_SENSOR);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICAS_ACTIVACIONES_EVENTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TABLAS_ACTIVACIONES_EVENTOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_sensores_activaciones_eventos($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_SENSOR:
            {
                $descripcion = "Gráfica de valores de sensor";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_ACUMULADOS_SENSOR:
            {
                $descripcion = "Gráfica de valores acumulados de sensor";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICAS_ACTIVACIONES_EVENTOS:
            {
                $descripcion = "Gráficas de activaciones de eventos";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TABLAS_ACTIVACIONES_EVENTOS:
            {
                $descripcion = "Tablas de activaciones de eventos";
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


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_sensores_activaciones_eventos($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-activaciones-eventos'>
                    <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                        </div>
                        <div id='informe-sensores-activaciones-eventos' hidden>
                            <div class='grafica100' id='grafica-valores-sensor-activaciones-eventos'></div>
                            <div class='grafica100' id='grafica-valores-acumulados-sensor-activaciones-eventos'></div>";
                    for ($i = 1; $i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; $i++)
                    {
                        $html_informe .= "
                            <div class='grafica100' id='grafica-activaciones-evento-activaciones-eventos-".$i."'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-activaciones-evento-activaciones-eventos-".$i."'></div>";
                    }
                    $html_informe .= "
                        </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de activaciones de eventos
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-activaciones-eventos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_eventos(TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-activaciones-eventos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-sensor-activaciones-eventos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-acumulados-sensor-activaciones-eventos'></div>
                        <div class='salto-pagina-informe-fichero' id='salto-pagina-graficas-valores-sensor-activaciones-eventos-activaciones-eventos'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; $i++)
                {
                    $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-activaciones-evento-activaciones-eventos-".$i."'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-activaciones-evento-activaciones-eventos-".$i."'></div>
                        <div class='salto-pagina-informe-fichero'></div>";
                }
                $html_informe .= "
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_sensores_activaciones_eventos(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-eventos-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay eventos seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-sensor-activaciones-eventos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-sensor-activaciones-eventos'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; $i++)
                {
                    $html_elemento .= "
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-activaciones-evento-activaciones-eventos-".$i."'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-activaciones-evento-activaciones-eventos-".$i."'></div>";
                }
                $html_elemento .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-eventos-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay eventos seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-sensor-activaciones-eventos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-sensor-activaciones-eventos'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS; $i++)
                {
                    $html_elemento .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-activaciones-evento-activaciones-eventos-".$i."'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."contenedor-tabla-activaciones-evento-activaciones-eventos-".$i."'></div>";
                }
                $html_elemento .= "
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Identificadores de eventos
        $tipo_seleccion_origen_evento = $parametros_tipo_elemento["tipo_seleccion_origen_evento"];
        switch ($tipo_seleccion_origen_evento)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $ids_eventos = $parametros_tipo_elemento["ids_eventos"];
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $ids_eventos = dame_ids_eventos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos_plantilla_configurable(
                    $parametros_tipo_elemento["clase_sensor"],
                    $parametros_tipo_elemento["origen_evento"],
                    $parametros_tipo_elemento["id_origen_evento"],
                    $parametros_tipo_elemento["granularidad_evento"],
                    $parametros_tipo_elemento["filtro_nombres_eventos"]);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de origen de evento desconocida: '".$tipo_seleccion_origen_evento."'");
            }
        }

        // Si no hay eventos seleccionados, se devuelve sin eventos
        $hay_eventos_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_eventos"]) > 0)
        {
            // Nota: En principio no debería haber ids de eventos a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (eventos eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_eventos"] as $id_evento)
            {
                if ($id_evento != ID_NINGUNO)
                {
                    $hay_eventos_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_eventos_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_eventos_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["origen_evento"] = $parametros_tipo_elemento["origen_evento"];
        $parametros_informe["id_origen_evento"] = $parametros_tipo_elemento["id_origen_evento"];
        switch ($parametros_informe["origen_evento"])
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                $nombre_origen_evento = dame_nombre_sensor($parametros_tipo_elemento["id_origen_evento"]);
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $nombre_origen_evento = dame_nombre_grupo_sensores($parametros_tipo_elemento["id_origen_evento"]);
                break;
            }
        }
        $parametros_informe["nombre_origen_evento"] = $nombre_origen_evento;
        $parametros_informe["granularidad_evento"] = $parametros_tipo_elemento["granularidad_evento"];
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $nombre_campo = dame_descripcion_campo_clase_sensor(
            $parametros_tipo_elemento["clase_sensor"],
            $parametros_tipo_elemento["campo"]);
        $parametros_informe["nombre_campo"] = $nombre_campo;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;
        $parametros_informe["ids_eventos"] = $ids_eventos;
        $nombres_eventos = dame_nombres_eventos($ids_eventos);
        $parametros_informe["nombres_eventos"] = $nombres_eventos;

        // Se recuperan los datos del elemento
        $datos_elemento = dame_activaciones_eventos($parametros_informe);
        return ($datos_elemento);
    }


    //
    // Funciones auxiliares
    //


    function dame_ids_eventos_elemento_plantilla_informe_tipo_sensores_activaciones_eventos_plantilla_configurable(
        $clase_sensor,
        $origen_evento,
        $id_origen_evento,
        $granularidad_evento,
        $filtro_nombres_eventos)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_eventos = "
            SELECT
                id
            FROM eventos
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_eventos .= "
                AND (clase = '".$bd_red->_($clase_sensor)."')";
        }
        // Si el origen es sensor y hay sensor seleccionado, también se buscan los eventos del grupo del sensor (si tiene asignado)
        switch ($origen_evento)
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                if ($id_origen_evento != ID_TODOS)
                {
                    $fila_sensor = dame_fila_sensor($id_origen_evento);
                    $id_grupo_sensor = $fila_sensor["grupo"];
                    if ($id_grupo_sensor != ID_NINGUNO)
                    {
                        $consulta_eventos .= "
                            AND (((origen = '".$bd_red->_($origen_evento)."') AND (id_origen = '".$bd_red->_($id_origen_evento)."')) OR
                                ((origen = '".$bd_red->_(ORIGEN_EVENTO_GRUPO_SENSORES)."') AND (id_origen = '".$bd_red->_($id_grupo_sensor)."')))";
                    }
                    else
                    {
                        $consulta_eventos .= "
                            AND (id_origen = '".$bd_red->_($id_origen_evento)."')";
                        if ($id_origen_evento != ID_TODOS)
                        {
                            $consulta_eventos .= "
                                AND (id_origen = '".$bd_red->_($id_origen_evento)."')";
                        }
                    }
                }
                else
                {
                    $consulta_eventos .= "
                        AND (origen = '".$bd_red->_($origen_evento)."')";
                }
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $consulta_eventos .= "
                    AND (origen = '".$bd_red->_($origen_evento)."')";
                if ($id_origen_evento != ID_TODOS)
                {
                    $consulta_eventos .= "
                        AND (id_origen = '".$bd_red->_($id_origen_evento)."')";
                }
                break;
            }
        }
        $consulta_eventos .= "
                AND (granularidad = '".$bd_red->_($granularidad_evento)."')";
        if ($filtro_nombres_eventos != "")
        {
            $consulta_eventos .= "
                AND ".dame_condicion_consulta_filtro_busqueda(array("nombre"), $filtro_nombres_eventos);
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
        $ids_eventos = array();
        while ($fila_evento = $res_eventos->dame_siguiente_fila())
        {
            $id_evento = $fila_evento["id"];

            $anyadir_evento = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($id_evento, $ids_eventos_usuario) == false)
                {
                    $anyadir_evento = false;
                }
            }

            if ($anyadir_evento == true)
            {
                array_push($ids_eventos, $id_evento);
                if (count($ids_eventos) == NUMERO_MAXIMO_EVENTOS_ACTIVACIONES_EVENTOS)
                {
                    break;
                }
            }
        }
        return ($ids_eventos);
    }
?>
