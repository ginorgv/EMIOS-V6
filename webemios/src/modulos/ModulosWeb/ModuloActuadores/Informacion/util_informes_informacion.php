<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoActuador.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    //
    // Funciones de información de información
    //


    // Devuelve la información de acciones enviadas
    function dame_informacion_acciones_enviadas($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $clase_actuador = $parametros["clase_actuador"];
        $nombre_clase_actuador = $parametros["nombre_clase_actuador"];
        $destino_accion = $parametros["destino_accion"];
        $id_destino_accion = $parametros["id_destino_accion"];
        $nombre_destino_accion = $parametros["nombre_destino_accion"];
        $origen_acciones = $parametros["origen_acciones"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $nombre_campo = $parametros["nombre_campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

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
        $hay_sensor_seleccionado = ($id_sensor != ID_NINGUNO);
        $mostrar_grafica_valores = ($hay_sensor_seleccionado == true);

        // Variables para la gráfica de valores
        $grafica_valores_sensor = new VectorDatos();
        $grafica_valores_acumulados_sensor = new VectorDatos();
        $unidad_medida = NULL;
        $min_valor = NULL;
        $max_valor = NULL;
        $min_valor_acumulado = NULL;
        $max_valor_acumulado = NULL;

        // Se obtienen los datos para la gráfica de valores (si es necesario)
        if ($mostrar_grafica_valores == true)
        {
            // Se realiza la consulta de valores del sensor
            $consulta_valores_sensor = dame_consulta_valores_sensor(
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $parametros_extra_campo);
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Flag de campo incremental
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
            $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

            // Segundos máximos entre valores (para separar las líneas de las gráficas)
            $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

            // Se recorren los valores del sensor
            $datos_grafica_valores_sensor = new VectorDatos();
            $datos_grafica_valores_acumulados_sensor = new VectorDatos();
            $min_valor = (float) (INF);
            $max_valor = (float) (-INF);
            $min_valor_acumulado = (float) (INF);
            $max_valor_acumulado = (float) (-INF);
            $suma_valores = 0;
            $timestamp_fecha_hora_valor_sensor_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica = 0;
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
            {
                // Fecha y valor
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo];
                if ($valor === NULL)
                {
                    continue;
                }
                $valor = (float) $valor;

                // Valor máximo y mínimo
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                }

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                $timestamp_fecha_hora_valor_sensor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_valor_sensor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if (($numero_puntos_seguidos_grafica > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_sensor_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_valor_sensor_utc - $timestamp_fecha_hora_valor_sensor_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica = 0;
                        $datos_grafica_valores_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_valor_sensor_anterior_utc = $timestamp_fecha_hora_valor_sensor_utc;
                $numero_puntos_seguidos_grafica += 1;

                // Se añade el valor
                $datos_grafica_valores_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_sensor_utc, $valor);

                // Si el campo es incremental
                if ($campo_incremental == true)
                {
                    $suma_valores += $valor;
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
                $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);

                if ($campo_incremental == true)
                {
                    $grafica_valores_acumulados_sensor->anyade_dato($datos_grafica_valores_acumulados_sensor->dame_datos());
                }
            }
        }

        // Se realiza la consulta de acciones enviadas
        $consulta_acciones = dame_consulta_acciones_enviadas(
            $destino_accion,
            $nombre_destino_accion,
            $origen_acciones,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);
        $res_acciones = $bd_datos->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }

        // Datos de gráfica de acciones enviadas
        $datos_grafica_acciones_enviadas = new VectorDatos();

        // Tabla de acciones enviadas
        $params_tabla_acciones_enviadas = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_ACCIONES_ENVIADAS,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ACCIONES_ENVIADAS),
            "generar_valores_xml" => true
        );
        $titulo_tabla_acciones_enviadas = $nombre_clase_actuador;
        switch ($destino_accion)
        {
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $titulo_tabla_acciones_enviadas .= " (".$idiomas->_("grupo").")";
                break;
            }
        }
        $tabla_acciones_enviadas = new TablaDatos(
            "tabla-acciones-enviadas-acciones-enviadas",
            $titulo_tabla_acciones_enviadas,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_acciones_enviadas
        );
        $cabecera_tabla_acciones_enviadas = array(
            $idiomas->_("Fecha"),
            $idiomas->_("Destino"),
            $idiomas->_("Origen"),
            $idiomas->_("Acción"),
            $idiomas->_("Estado")
        );
        $tabla_acciones_enviadas->anyade_cabecera("", $cabecera_tabla_acciones_enviadas);

        // Características de la clase de actuador
        $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase_actuador);
        $clase_estado_persistente = $caracteristicas_clase_actuador["estado_persistente"];
        $clase_acciones_predefinidas = $caracteristicas_clase_actuador["acciones_predefinidas"];

        // Líneas verticales de acciones enviadas
        $lineas_verticales_acciones_enviadas = array();

        // Líneas verticales de errores de acciones enviadas
        $lineas_verticales_errores_acciones_enviadas = array();

        // Se recuperan las acciones predefinidas
        if ($clase_acciones_predefinidas == true)
        {
            $acciones_predefinidas = dame_acciones_predefinidas($clase_actuador);
        }

        // Se recorren las acciones enviadas
        $numero_acciones_enviadas = 0;
        $primer_valor_accion = NULL;
        $ultimo_valor_accion = NULL;
        $numero_elementos_tabla_acciones_enviadas = 0;
        $limite_elementos_tabla_acciones_enviadas_superado = false;
        while ($fila_accion = $res_acciones->dame_siguiente_fila())
        {
            // Conversión de fechas
            $cadena_fecha_hora_accion_base_datos_utc = $fila_accion["fecha_hora"];
            $timestamp_fecha_hora_accion_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_accion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_accion_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            $fecha_hora_accion_utc = convierte_cadena_a_fecha($cadena_fecha_hora_accion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_accion_local = dame_fecha_hora_local($fecha_hora_accion_utc);
            $cadena_fecha_hora_accion_local_local = convierte_fecha_a_cadena($fecha_hora_accion_local, $_SESSION["formato_fecha_hora_local"]);

            // Valor de la acción enviada
            $valor_accion = $fila_accion["valor"];

            // Se añade el valor inicial del actuador (sólo si es de clase de estado persistente)
            if ($primer_valor_accion == NULL)
            {
                if ($clase_estado_persistente == true)
                {
                    $valor_ultima_accion_anterior = dame_valor_ultima_accion_anterior(
                        $destino_accion,
                        $nombre_destino_accion,
                        $origen_acciones,
                        $cadena_fecha_hora_accion_base_datos_utc);
                    if ($valor_ultima_accion_anterior !== NULL)
                    {
                        $datos_grafica_acciones_enviadas->anyade_tupla_pareja_datos_etiqueta(
                            $timestamp_fecha_hora_inicio_utc,
                            $valor_ultima_accion_anterior,
                            "[".$nombre_destino_accion."]"."<br/>".$idiomas->_("Valor inicial"));
                    }
                }
                $primer_valor_accion = $valor_accion;
            }
            $ultimo_valor_accion = $valor_accion;

            // Tooltip de la acción enviada
            $tooltip_accion_enviada = dame_tooltip_accion_enviada(
                $destino_accion,
                $fila_accion,
                $clase_actuador,
                $clase_acciones_predefinidas,
                $acciones_predefinidas,
                $fecha_hora_accion_utc,
                $cadena_fecha_hora_accion_local_local);

            // Se añade la acción enviada
            $datos_grafica_acciones_enviadas->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_accion_utc,
                $valor_accion,
                $tooltip_accion_enviada);

            // Si hay error en la ejecución de la acción, se añade una línea vertical
            if ($destino_accion == DESTINO_ACCION_ACTUADOR)
            {
                $estado_ejecucion_accion = $fila_accion["estado_ejecucion"];
                switch ($estado_ejecucion_accion)
                {
                    case ESTADO_EJECUCION_ACCION_ERROR:
                    case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
                    {
                        $texto_tooltip = $idiomas->_("Error en la ejecución de la acción");
                        $linea_vertical_error_accion_enviada = array(
                            "valor" => $timestamp_fecha_hora_accion_utc,
                            "color" => COLOR_LINEA_GRAFICA_ROJO,
                            "texto_tooltip" => $texto_tooltip);
                        array_push($lineas_verticales_errores_acciones_enviadas, $linea_vertical_error_accion_enviada);
                        break;
                    }
                }
            }

            // Se añade la línea vertical de la activación (si es necesario)
            if ($mostrar_grafica_valores == true)
            {
                if ($valor_accion >= 0.5)
                {
                    $color_linea_vertical = COLOR_LINEA_GRAFICA_VERDE_OSCURO;
                }
                else
                {
                    $color_linea_vertical = COLOR_LINEA_GRAFICA_ROJO;
                }
                $texto_tooltip = $idiomas->_("Acción enviada")." (".$cadena_fecha_hora_accion_local_local.")";
                $linea_vertical_accion_enviada = array(
                    "valor" => $timestamp_fecha_hora_accion_utc,
                    "color" => $color_linea_vertical,
                    "texto_tooltip" => $texto_tooltip);
                array_push($lineas_verticales_acciones_enviadas, $linea_vertical_accion_enviada);
            }

            // Fila de la acción enviada
            if ($numero_elementos_tabla_acciones_enviadas < NUMERO_MAXIMO_FILAS_TABLA_ACCIONES_ENVIADAS)
            {
                $fila_accion_enviada = dame_fila_accion_enviada_tabla_acciones_enviadas(
                    $destino_accion,
                    $nombre_destino_accion,
                    $fila_accion,
                    $clase_actuador,
                    $clase_acciones_predefinidas,
                    $acciones_predefinidas,
                    $fecha_hora_accion_utc,
                    $cadena_fecha_hora_accion_local_local);
                $tabla_acciones_enviadas->anyade_fila("fila-accion-enviada", $fila_accion_enviada);
                $numero_elementos_tabla_acciones_enviadas += 1;
            }
            else
            {
                $limite_elementos_tabla_acciones_enviadas_superado = true;
            }

            $numero_acciones_enviadas += 1;
        }

        // Si no hay datos no se hace nada
        if ($numero_acciones_enviadas == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Pie de tabla de acciones enviadas
        $texto_pie = $idiomas->_("Número de acciones enviadas").": ".$numero_acciones_enviadas;
        if ($limite_elementos_tabla_acciones_enviadas_superado == true)
        {
            $texto_pie .= " (".$idiomas->_("mostradas primeras").": ".$numero_elementos_tabla_acciones_enviadas.")";
        }
        $tabla_acciones_enviadas->anyade_pie($texto_pie);

        // Si la clase es de estado persistente
        // - Se añade el valor final de la acción
        // - Si no hay acciones durante el periodo,
        //   se establecen el valor inicial y final al último valor de la acción antes de la fecha de inicio (si no hay acciones no se añade ningún valor)
        if ($clase_estado_persistente == true)
        {
            // Si la fecha final es mayor que la fecha actual, se utiliza la fecha actual
            $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);
            $fecha_actual_utc = dame_fecha_hora_actual_utc();
            if ($fecha_actual_utc >= $fecha_hora_fin_utc)
            {
                $fecha_fin_ahora = false;
                $timestamp_fecha_hora_valor_final_utc = $timestamp_fecha_hora_fin_utc;
            }
            else
            {
                $fecha_fin_ahora = true;
                $timestamp_fecha_hora_valor_final_utc = dame_timestamp_ahora_milisegundos_utc();
                $timestamp_fecha_hora_valor_final_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            }

            if ($primer_valor_accion !== NULL)
            {
                $etiqueta_valor_final = "[".$nombre_destino_accion."]"."<br/>".$idiomas->_("Valor final");
                if ($fecha_fin_ahora == true)
                {
                    $etiqueta_valor_final .= " (".$idiomas->_("ahora").")";
                }
                $datos_grafica_acciones_enviadas->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_valor_final_utc,
                    $ultimo_valor_accion,
                    $etiqueta_valor_final);
            }
            else
            {
                $valor_ultima_accion_anterior = dame_valor_ultima_accion_anterior(
                    $destino_accion,
                    $nombre_destino_accion,
                    $origen_acciones,
                    $cadena_fecha_hora_inicio_base_datos_utc);
                if ($valor_ultima_accion_anterior !== NULL)
                {
                    // Sólo se añaden las acciones inicial y final si la fecha de la acción final es mayor que la fecha de la acción inicial
                    if ($timestamp_fecha_hora_valor_final_utc > $timestamp_fecha_hora_inicio_utc)
                    {
                        $datos_grafica_acciones_enviadas->anyade_tupla_pareja_datos_etiqueta(
                            $timestamp_fecha_hora_inicio_utc,
                            $valor_ultima_accion_anterior,
                            "[".$nombre_destino_accion."]"."<br/>".$idiomas->_("Valor inicial"));

                        $etiqueta_valor_final = "[".$nombre_destino_accion."]"."<br/>".$idiomas->_("Valor final");
                        if ($fecha_fin_ahora == true)
                        {
                            $etiqueta_valor_final .= " (".$idiomas->_("ahora").")";
                        }
                        $datos_grafica_acciones_enviadas->anyade_tupla_pareja_datos_etiqueta(
                            $timestamp_fecha_hora_valor_final_utc,
                            $valor_ultima_accion_anterior,
                            $etiqueta_valor_final);
                    }
                }
            }
        }

        // Variables para dibujar la gráfica de acciones enviadas
        $grafica_acciones_enviadas = new VectorDatos();
        $grafica_acciones_enviadas->anyade_dato($datos_grafica_acciones_enviadas->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Se recuperan los comentarios y las líneas verticales para la gráfica
        // - Nota: No se utiliza el horario semanal ni las fechas para mostrar todos los comentarios entre la fecha de inicio y fin del informe
        //   (puede ser que haya comentarios en periodos que no se visualicen en la gráfica pero que puedan ser relevantes)
        switch ($comentarios)
        {
            case COMENTARIOS_GRAFICA:
            case COMENTARIOS_GRAFICA_TABLA:
            {
                switch ($destino_accion)
                {
                    case DESTINO_ACCION_ACTUADOR:
                    {
                        $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR;
                        $ids_actuadores_comentarios = array($id_destino_accion);
                        $ids_grupos_actuadores_comentarios = dame_ids_grupos_nodos(TIPO_NODO_ACTUADOR, $ids_actuadores_comentarios);
                        break;
                    }
                    case DESTINO_ACCION_GRUPO_ACTUADORES:
                    {
                        $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES;
                        $ids_grupos_actuadores_comentarios = array($id_destino_accion);
                        $ids_actuadores_comentarios = dame_ids_nodos_grupos(TIPO_NODO_GRUPO_ACTUADORES, $ids_grupos_actuadores_comentarios);
                        break;
                    }
                }

                // Se recuperan las filas de los comentarios
                $nombres_actuadores_comentarios = dame_nombres_actuadores($ids_actuadores_comentarios);
                $nombres_grupos_actuadores_comentarios = dame_nombres_grupos_actuadores($ids_grupos_actuadores_comentarios);
                $filas_comentarios = Comentario::dame_filas_comentarios_nodos(
                    VISIBILIDAD_TODAS,
                    array(),
                    $nombres_actuadores_comentarios,
                    $nombres_grupos_actuadores_comentarios,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    NULL,
                    NULL,
                    NULL);
                $numero_comentarios = count($filas_comentarios);
                $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                    $filas_comentarios,
                    true,
                    $milisegundos_desfase_zonas_horarias_cliente_local);
                break;
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoActuador::dame_administracion_comentarios_actuadores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    switch ($destino_accion)
                    {
                        case DESTINO_ACCION_ACTUADOR:
                        {
                            $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR;
                            $parametros_origen_comentarios = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS;
                            break;
                        }
                        case DESTINO_ACCION_GRUPO_ACTUADORES:
                        {
                            $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES;
                            $parametros_origen_comentarios = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS;
                            break;
                        }
                    }
                }
                else
                {
                    switch ($destino_accion)
                    {
                        case DESTINO_ACCION_ACTUADOR:
                        {
                            $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR;
                            $parametros_origen_comentarios = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS.",".$numero_elemento_plantilla_informe;
                            break;
                        }
                        case DESTINO_ACCION_GRUPO_ACTUADORES:
                        {
                            $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES;
                            $parametros_origen_comentarios = TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS.",".$numero_elemento_plantilla_informe;
                            break;
                        }
                    }
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-actuadores-acciones-enviadas",
                    $filas_comentarios,
                    NULL,
                    array($nombre_destino_accion),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del destino
        $descripcion_destino = dame_descripcion_destino_accion_informe($destino_accion, $id_destino_accion);

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
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "min_valor_acumulado" => $min_valor_acumulado,
            "max_valor_acumulado" => $max_valor_acumulado,
            "grafica_valores_sensor" => $grafica_valores_sensor->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados_sensor" => $grafica_valores_acumulados_sensor->dame_datos(),
            "unidad_medida" => $unidad_medida,
            "lineas_verticales_acciones_enviadas" => $lineas_verticales_acciones_enviadas,
            "lineas_verticales_errores_acciones_enviadas" => $lineas_verticales_errores_acciones_enviadas,
            "grafica_acciones_enviadas" => $grafica_acciones_enviadas->dame_datos(),
            "clase_estado_persistente" => $clase_estado_persistente,
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_acciones_enviadas" => $tabla_acciones_enviadas->dame_tabla(),
            "limite_elementos_tabla_acciones_enviadas_superado" => $limite_elementos_tabla_acciones_enviadas_superado,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "nombre_clase_actuador" => $nombre_clase_actuador,
            "destino_accion" => $destino_accion,
            "nombre_destino_accion" => $nombre_destino_accion,
            "nombre_sensor" => $nombre_sensor,
            "nombre_campo" => $nombre_campo,
            "descripcion_destino" => $descripcion_destino);
        return ($resultado);
    }


    //
	// Funciones auxiliares
	//


    // Devuelve la consulta de acciones enviadas
    function dame_consulta_acciones_enviadas(
        $destino_accion,
        $nombre_destino_accion,
        $origen_acciones,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        $consulta_acciones = "
            SELECT
                hora AS fecha_hora,
                contenido,
                valor,
                origen,
                nombre_origen";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_acciones .= ",
                        estado_ejecucion,
                        hora_fin,
                        actuador,
                        destino
                    FROM acciones_actuadores";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_acciones .= "
                    FROM acciones_grupos_actuadores";
                break;
            }
        }
        $consulta_acciones .= "
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_acciones .= "
                    AND (actuador = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_acciones .= "
                    AND (grupo_actuadores = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
        }
        switch ($origen_acciones)
        {
            case ORIGEN_ACCIONES_MANUAL:
            {
                $consulta_acciones .= "
                    AND (origen = '".ORIGEN_ACCION_MANUAL."')";
                break;
            }
            case ORIGEN_ACCIONES_ULTIMA_ACCION:
            {
                $consulta_acciones .= "
                    AND ((origen = '".ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_GRUPO_ACTUADORES."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_REENVIO_ULTIMA_ACCION."'))";
                break;
            }
            case ORIGEN_ACCIONES_REGLA:
            {
                $consulta_acciones .= "
                    AND ((origen = '".ORIGEN_ACCION_AUTOMATICO_REGLA_ACTIVADA."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_REGLA_DESACTIVADA."'))";
                break;
            }
            case ORIGEN_ACCIONES_PROGRAMACION:
            {
                $consulta_acciones .= "
                    AND (origen = '".ORIGEN_ACCION_AUTOMATICO_PROGRAMACION."')";
                break;
            }
        }

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_acciones .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se añade la restricción de valor no nulo y el orden
        $consulta_acciones .= "
                AND (valor IS NOT NULL)
            ORDER BY hora ASC";
        return ($consulta_acciones);
    }


    // Devuelve el valor de la última acción anterior a la fecha especificada
    function dame_valor_ultima_accion_anterior(
        $destino_accion,
        $nombre_destino_accion,
        $origen_acciones,
        $cadena_fecha_hora_inicio_base_datos_utc)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Se realiza la consulta de acciones enviadas
        $consulta_acciones = "
            SELECT
                valor";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_acciones .= "
                    FROM acciones_actuadores";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_acciones .= "
                    FROM acciones_grupos_actuadores";
                break;
            }
        }
        $consulta_acciones .= "
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (hora < '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_acciones .= "
                    AND (actuador = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_acciones .= "
                    AND (grupo_actuadores = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
        }
        switch ($origen_acciones)
        {
            case ORIGEN_ACCIONES_MANUAL:
            {
                $consulta_acciones .= "
                    AND (origen = '".ORIGEN_ACCION_MANUAL."')";
                break;
            }
            case ORIGEN_ACCIONES_ULTIMA_ACCION:
            {
                $consulta_acciones .= "
                    AND ((origen = '".ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_PROGRAMACION."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_ULTIMA_ACCION_GRUPO_ACTUADORES."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_REENVIO_ULTIMA_ACCION."'))";
                break;
            }
            case ORIGEN_ACCIONES_REGLA:
            {
                $consulta_acciones .= "
                    AND ((origen = '".ORIGEN_ACCION_AUTOMATICO_REGLA_ACTIVADA."')
                        OR (origen = '".ORIGEN_ACCION_AUTOMATICO_REGLA_DESACTIVADA."'))";
                break;
            }
            case ORIGEN_ACCIONES_PROGRAMACION:
            {
                $consulta_acciones .= "
                    AND (origen = '".ORIGEN_ACCION_AUTOMATICO_PROGRAMACION."')";
                break;
            }
        }
        $consulta_acciones .= "
                AND (valor IS NOT NULL)
            ORDER BY hora DESC
            LIMIT 1";

        $res_acciones = $bd_datos->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }

        if ($res_acciones->dame_numero_filas() > 0)
        {
            $fila_accion = $res_acciones->dame_siguiente_fila();
            $valor_accion = $fila_accion["valor"];
        }
        else
        {
            $valor_accion = NULL;
        }
        return ($valor_accion);
    }


    // Devuelve el tooltip de la acción enviada
    function dame_tooltip_accion_enviada(
        $destino_accion,
        $fila_accion,
        $clase_actuador,
        $clase_acciones_predefinidas,
        $acciones_predefinidas,
        $fecha_hora_accion_utc,
        $cadena_fecha_hora_accion_local_local)
    {
        $idiomas = new Idiomas();

        $tooltip_accion = "";

        // Origen de la acción
        $origen_accion = dame_descripcion_origen_accion($fila_accion["origen"]);
        $nombre_origen_accion = $fila_accion["nombre_origen"];
        if ($nombre_origen_accion != "")
        {
            $origen_accion .= " (".$nombre_origen_accion.")";
        }
        $tooltip_accion .= $idiomas->_("Origen").": ".$origen_accion." (".$cadena_fecha_hora_accion_local_local.")"."<br/>";

        // Contenido de la acción y fecha
        $contenido_accion = $fila_accion["contenido"];
        if ($clase_acciones_predefinidas == true)
        {
            $contenido_accion_sin_espacios = str_replace(" ", "", $contenido_accion);
            $indice = array_search($contenido_accion_sin_espacios, $acciones_predefinidas["contenidos_sin_espacios"]);
            if (($indice !== false) && ($indice !== NULL))
            {
                $nombre_accion = $acciones_predefinidas["nombres"][$indice];
            }
            else
            {
                $nombre_accion = $idiomas->_("Desconocida")." [".$contenido_accion."]";
            }
            $tooltip_accion .= $idiomas->_("Acción").": ".$nombre_accion."<br/>";
        }
        else
        {
            switch ($clase_actuador)
            {
                case CLASE_ACTUADOR_MENSAJE:
                {
                    // Nota: Se convierte de UTF-8 a ISO-8859-1
                    // http://php.net/manual/es/function.utf8-decode.php
                    $contenido_accion = reemplaza_saltos_linea_tabuladores($contenido_accion, " ");
                    $contenido_accion = reemplaza_multiples_espacios_espacio($contenido_accion);
                    $parametros_mensaje = json_decode($contenido_accion, true);
                    if ($parametros_mensaje === NULL)
                    {
                        $tooltip_accion .= "(".$idiomas->_("no se puede mostrar el contenido del mensaje").")"."<br/>";
                    }
                    else
                    {
                        $titulo = $parametros_mensaje["titulo"];
                        $titulo = utf8_decode($titulo);
                        if (strlen($titulo) > NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TOOLTIP)
                        {
                            $titulo = substr($titulo, 0, NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TOOLTIP)." ...";
                        }
                        $contenido = $parametros_mensaje["contenido"];
                        $contenido = utf8_decode($contenido);
                        if (strlen($contenido) > NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE_TOOLTIP)
                        {
                            $contenido = substr($contenido, 0, NUMERO_MAXIMO_CARACTERES_CONTENIDO_MENSAJE_TOOLTIP)." ...";
                        }
                        $tooltip_accion .= $idiomas->_("Título").": ".$titulo."<br/>";
                        $tooltip_accion .= $idiomas->_("Contenido").": ".$contenido."<br/>";
                    }
                    break;
                }
                case CLASE_ACTUADOR_GENERICA:
                {
                    $tooltip_accion .= $idiomas->_("Acción").": [".$contenido_accion."]"."<br/>";
                    break;
                }
            }
        }

        // Si el destino es un actuador, se añade si ha habido error y si la acción es de grupo
        if ($destino_accion == DESTINO_ACCION_ACTUADOR)
        {
            $estado_ejecucion = $fila_accion["estado_ejecucion"];
            $cadena_fecha_hora_fin_base_datos_utc = $fila_accion["hora_fin"];

            if ($estado_ejecucion != ESTADO_EJECUCION_ACCION_OK)
            {
                $tooltip_accion .= $idiomas->_("Estado de ejecución").": ".dame_descripcion_estado_ejecucion_accion($estado_ejecucion)."<br/>";
            }
            if ($cadena_fecha_hora_fin_base_datos_utc !== NULL)
            {
                $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $tiempo_ejecucion = $fecha_hora_fin_utc->diff($fecha_hora_accion_utc);
                $segundos_ejecucion = dame_segundos_intervalo_tiempo($tiempo_ejecucion);
                if ($segundos_ejecucion > 0)
                {
                    $tooltip_accion .= $idiomas->_("Tiempo de ejecución").": ".dame_texto_periodo($segundos_ejecucion)."<br/>";
                }
            }
            $destino = $fila_accion["destino"];
            if ($destino == DESTINO_ACCION_GRUPO_ACTUADORES)
            {
                $tooltip_accion .= $idiomas->_("Tipo de destino").": ".dame_descripcion_destino_accion($destino)."<br/>";
            }
        }

        return ($tooltip_accion);
    }


    // Devuelve la fila de la acción enviada para la tabla de acciones enviadas
    function dame_fila_accion_enviada_tabla_acciones_enviadas(
        $destino_accion,
        $nombre_destino_accion,
        $fila_accion,
        $clase_actuador,
        $clase_acciones_predefinidas,
        $acciones_predefinidas,
        $fecha_hora_accion_utc,
        $cadena_fecha_hora_accion_local_local)
    {
        $idiomas = new Idiomas();

        // Origen de la acción
        $origen_accion = dame_descripcion_origen_accion($fila_accion["origen"]);
        $nombre_origen_accion = $fila_accion["nombre_origen"];
        if ($nombre_origen_accion != "")
        {
            $origen_accion .= " (".$nombre_origen_accion.")";
        }

        // Descripción de la acción
        $contenido_accion = $fila_accion["contenido"];
        if ($clase_acciones_predefinidas == true)
        {
            $contenido_accion_sin_espacios = str_replace(" ", "", $contenido_accion);
            $indice = array_search($contenido_accion_sin_espacios, $acciones_predefinidas["contenidos_sin_espacios"]);
            if (($indice !== false) && ($indice !== NULL))
            {
                $imagen_accion = NodoActuador::dame_imagen_accion_clase($clase_actuador, $contenido_accion);
                $descripcion_accion = $acciones_predefinidas["nombres"][$indice]." [".$imagen_accion."]";
            }
            else
            {
                $descripcion_accion = $idiomas->_("desconocida")." [".$contenido_accion."]";
            }
        }
        else
        {
            switch ($clase_actuador)
            {
                case CLASE_ACTUADOR_MENSAJE:
                {
                    // Nota: Se convierte de UTF-8 a ISO-8859-1
                    // http://php.net/manual/es/function.utf8-decode.php
                    $contenido_accion = reemplaza_saltos_linea_tabuladores($contenido_accion, " ");
                    $contenido_accion = reemplaza_multiples_espacios_espacio($contenido_accion);
                    $parametros_mensaje = json_decode($contenido_accion, true);
                    if ($parametros_mensaje === NULL)
                    {
                        $descripcion_accion .= "(".$idiomas->_("no se puede mostrar el contenido del mensaje").")";
                    }
                    else
                    {
                        $titulo = $parametros_mensaje["titulo"];
                        $titulo = utf8_decode($titulo);
                        if (strlen($titulo) > NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TABLA)
                        {
                            $titulo = substr($titulo, 0, NUMERO_MAXIMO_CARACTERES_TITULO_MENSAJE_TABLA)." ...";
                        }
                        $descripcion_accion .= $idiomas->_("Título").": ".$titulo;
                    }
                    $descripcion_accion = htmlspecialchars($descripcion_accion, ENT_QUOTES);
                    break;
                }
                case CLASE_ACTUADOR_GENERICA:
                {
                    $descripcion_accion .= $idiomas->_("Acción").": [".$contenido_accion."]";
                    break;
                }
            }
        }

        // Si el destino es un actuador, se añade si ha habido error y si la acción es de grupo
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $estado_ejecucion = $fila_accion["estado_ejecucion"];
                $cadena_fecha_hora_fin_base_datos_utc = $fila_accion["hora_fin"];

                $estado_ejecucion_accion = dame_descripcion_estado_ejecucion_accion($estado_ejecucion);
                if ($cadena_fecha_hora_fin_base_datos_utc !== NULL)
                {
                    $fecha_hora_fin_utc = convierte_cadena_a_fecha($cadena_fecha_hora_fin_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $tiempo_ejecucion = $fecha_hora_fin_utc->diff($fecha_hora_accion_utc);
                    $segundos_ejecucion = dame_segundos_intervalo_tiempo($tiempo_ejecucion);
                    if ($segundos_ejecucion > 0)
                    {
                        $estado_ejecucion_accion .= " (".dame_texto_periodo($segundos_ejecucion).")";
                    }
                }
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $estado_ejecucion_accion = $idiomas->_("NA");
                break;
            }
        }

        // Fila de la acción enviada
        $fila_accion_enviada = array(
            $cadena_fecha_hora_accion_local_local,
            htmlspecialchars($nombre_destino_accion, ENT_QUOTES),
            htmlspecialchars($origen_accion, ENT_QUOTES),
            $descripcion_accion,
            $estado_ejecucion_accion);
        return ($fila_accion_enviada);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_actuadores_informacion_acciones_enviadas()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_SENSOR);
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_ACUMULADOS_SENSOR);
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_ACCIONES_ENVIADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESCRIPCION_DESTINO);
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_ACCIONES_ENVIADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_COMENTARIOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_actuadores_informacion_acciones_enviadas($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_SENSOR:
            {
                $descripcion = "Gráfica de valores de sensor";
                break;
            }
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_ACUMULADOS_SENSOR:
            {
                $descripcion = "Gráfica de valores acumulados de sensor";
                break;
            }
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_ACCIONES_ENVIADAS:
            {
                $descripcion = "Gráfica de acciones enviadas";
                break;
            }
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESCRIPCION_DESTINO:
            {
                $descripcion = "Descripción de destino";
                break;
            }
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_ACCIONES_ENVIADAS:
            {
                $descripcion = "Tabla de acciones enviadas";
                break;
            }
            case ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_COMENTARIOS:
            {
                $descripcion = "Tabla de comentarios";
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


    function dame_html_informe_tipo_actuadores_informacion_acciones_enviadas($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-actuadores-informacion-acciones-enviadas'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-actuadores-informacion-acciones-enviadas' hidden>
                        <div class='grafica100' id='grafica-valores-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100' id='grafica-valores-acumulados-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100' id='grafica-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='texto100' id='descripcion-destino-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas'></div>
                        <div id='parametros-resultado-informe-informacion-acciones-enviadas' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de acciones enviadas
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-acciones-enviadas'>";
                $html_informe .= dame_html_cabecera_informe_fichero_actuadores_informacion(TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-acciones-enviadas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-acumulados-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-destino-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-destino-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay destino seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-destino-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas'></div>
                        <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-acciones-enviadas' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-destino-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay destino seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-acumulados-sensor-informacion-acciones-enviadas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-destino-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-acciones-enviadas-informacion-acciones-enviadas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-actuador-informacion-acciones-enviadas'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_actuadores_informacion_acciones_enviadas(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay destino de acción seleccionado, se devuelve sin destino
        if ($parametros_tipo_elemento["id_destino_accion"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_destino_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["clase_actuador"] = $parametros_tipo_elemento["clase_actuador"];
        $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($parametros_tipo_elemento["clase_actuador"]);
        $parametros_informe["nombre_clase_actuador"] = $nombre_clase_actuador;
        $parametros_informe["destino_accion"] = $parametros_tipo_elemento["destino_accion"];
        $parametros_informe["id_destino_accion"] = $parametros_tipo_elemento["id_destino_accion"];
        switch ($parametros_tipo_elemento["destino_accion"])
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $nombre_destino_accion = dame_nombre_actuador($parametros_tipo_elemento["id_destino_accion"]);
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $nombre_destino_accion = dame_nombre_grupo_actuadores($parametros_tipo_elemento["id_destino_accion"]);
                break;
            }
        }
        $parametros_informe["nombre_destino_accion"] = $nombre_destino_accion;
        $parametros_informe["origen_acciones"] = $parametros_tipo_elemento["origen_acciones"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        if ($parametros_tipo_elemento["clase_sensor"] == CLASE_NINGUNA)
        {
            $nombre_campo = NULL;
        }
        else
        {
            $nombre_campo = dame_descripcion_campo_clase_sensor(
                $parametros_tipo_elemento["clase_sensor"],
                $parametros_tipo_elemento["campo"]);
        }
        $parametros_informe["nombre_campo"] = $nombre_campo;
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["comentarios"] = $parametros_tipo_elemento["comentarios"];
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_informacion_acciones_enviadas($parametros_informe);
        return ($datos_elemento);
    }
?>
