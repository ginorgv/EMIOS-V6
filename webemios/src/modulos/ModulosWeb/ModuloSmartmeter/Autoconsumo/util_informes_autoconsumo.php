<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/util_informes_autoconsumo.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    //
    // Funciones de información de autoconsumo
    //


    // Devuelve la información de simulación de autoconsumo de un sensor
    function dame_simulacion_autoconsumo_sensor($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $medicion = $parametros["medicion"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_sensor_generacion = $parametros["id_sensor_generacion"];
        $nombre_sensor_generacion = $parametros["nombre_sensor_generacion"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $tipo_autoconsumo = $parametros["tipo_autoconsumo"];
        $capacidad_acumulacion = $parametros["capacidad_acumulacion"];
        $factor_multiplicacion_generacion = $parametros["factor_multiplicacion_generacion"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }
        if (in_array($id_sensor_generacion, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
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

        // Clase de sensor y campo de consumo
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];

        // Variables (de consumo)
        $max_consumo = -INF;
        $max_consumo_acumulado = -INF;
        $total_consumo_sensor = 0;
        $total_consumo_sensor_generacion = 0;
        $total_consumo_simulado_sensor = 0;
        $total_excedente_generacion_simulado = 0;
        $datos_consumo_sensor = new VectorDatos();
        $datos_consumo_sensor_generacion = new VectorDatos();
        $datos_consumo_simulado_sensor = new VectorDatos();
        $datos_excedente_generacion_simulado = new VectorDatos();
        $datos_consumo_acumulado_sensor = new VectorDatos();
        $datos_consumo_acumulado_sensor_generacion = new VectorDatos();
        $datos_consumo_acumulado_simulador_sensor = new VectorDatos();
        $datos_excedente_generacion_acumulado_simulado = new VectorDatos();
        $grafica_consumos = new VectorDatos();
        $grafica_consumos_acumulados = new VectorDatos();
        $etiquetas_graficas_consumos = new VectorDatos();
        $etiquetas_graficas_consumos_acumulados = new VectorDatos();
        $info_consumos_sensor = array(
            "fechas" => array(),
            "consumos" => array()
        );
        $info_consumos_simulados_sensor = array(
            "fechas" => array(),
            "consumos" => array()
        );

        // Flag que indica si hay que mostrar el excedente de generación perdido
        if ($tipo_autoconsumo == TIPO_AUTOCONSUMO_CON_ACUMULACION)
        {
            $mostrar_excedente_generacion_perdido_simulado = true;
            $total_excedente_generacion_perdido_simulado = 0;
            $datos_excedente_generacion_perdido_simulado = new VectorDatos();
            $datos_excedente_generacion_perdido_acumulado_simulado = new VectorDatos();
        }
        else
        {
            $mostrar_excedente_generacion_perdido_simulado = false;
        }

        // Flag que indica si hay que mostrar información de costes
        // - Si la medición tiene curva de coste y hay tarifa seleccionada
        $caracteristicas_tarifas_pais = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if (($caracteristicas_tarifas_pais["curva_coste"] == true) && ($id_tarifa != ID_NINGUNO))
        {
            $mostrar_info_costes = true;
        }
        else
        {
            $mostrar_info_costes = false;
        }

        // Consulta de valores del sensor
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_HORA,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            NULL);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Datos de consumo del sensor por horas
        $datos_consumo_sensor_horas = array();
        while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $consumo = (float) $fila_valores_sensor[$campo_consumo];

            $timestamp_fecha_hora_hora_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            array_push($datos_consumo_sensor_horas,
                array(
                    "timestamp_fecha_hora_utc" => $timestamp_fecha_hora_hora_utc,
                    "consumo" => $consumo)
            );
        }

        // Si no hay datos no se hace nada
        if (count($datos_consumo_sensor_horas) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se guardan los datos de consumo de los sensores (en las horas en las que hay datos de los dos sensores)
        $consumo_sensor = array();
        $consumo_sensor_generacion = array();
        $consumo_simulado_sensor = array();

        // Consulta de valores del sensor de autoconsumo
        $consulta_valores_sensor_generacion = dame_consulta_valores_sensor(
            $id_sensor_generacion,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_HORA,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            NULL);
        $res_valores_sensor_generacion = $bd_datos->ejecuta_consulta($consulta_valores_sensor_generacion);
        if ($res_valores_sensor_generacion == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor_generacion."'");
        }

        // Si no hay datos no se hace nada
        if ($res_valores_sensor_generacion->dame_numero_filas() == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Número de datos de los sensores en las mismas horas
        $numero_datos_sensores_mismas_horas = 0;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, NULL);

        // Se recorren las filas de valores del sensor de autoconsumo
        $numero_dato_consumo_sensor_horas = 0;
        $timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valores_sensor_generacion = $res_valores_sensor_generacion->dame_siguiente_fila())
        {
            // Fecha y valores
            $cadena_fecha_hora_consumo_sensor_generacion_base_datos_utc = $fila_valores_sensor_generacion['fecha_hora'];
            $consumo_sensor_generacion = (float) $fila_valores_sensor_generacion[$campo_consumo];

            // Timestamps
            $timestamp_fecha_hora_consumo_sensor_generacion_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_consumo_sensor_generacion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_consumo_sensor_generacion_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Si la primera hora de consumo del sensor es mayor que la fecha de consumo del sensor de autoconsumo,
            // se pasa a la siguiente fila de consumo del sensor de autoconsumo
            if ($numero_dato_consumo_sensor_horas == 0)
            {
                $timestamp_fecha_hora_consumo_sensor_utc = $datos_consumo_sensor_horas[$numero_dato_consumo_sensor_horas]["timestamp_fecha_hora_utc"];
                if ($timestamp_fecha_hora_consumo_sensor_utc > $timestamp_fecha_hora_consumo_sensor_generacion_utc)
                {
                    continue;
                }
            }

            // Si la fecha de consumo del sensor es menor, se incrementa el número de fila hasta que sea igual (si es mayor es que hay huecos)
            $timestamp_fecha_hora_consumo_sensor_utc = $datos_consumo_sensor_horas[$numero_dato_consumo_sensor_horas]["timestamp_fecha_hora_utc"];
            while ($timestamp_fecha_hora_consumo_sensor_utc < $timestamp_fecha_hora_consumo_sensor_generacion_utc)
            {
                $numero_dato_consumo_sensor_horas += 1;
                if (count($datos_consumo_sensor_horas) < ($numero_dato_consumo_sensor_horas + 1))
                {
                    break;
                }
                $timestamp_fecha_hora_consumo_sensor_utc = $datos_consumo_sensor_horas[$numero_dato_consumo_sensor_horas]["timestamp_fecha_hora_utc"];
            }

            // Si la fecha no es igual es que hay huecos (los datos son incompletos), se pasa a la siguiente fila de sensor de autoconsumo
            if ($timestamp_fecha_hora_consumo_sensor_utc != $timestamp_fecha_hora_consumo_sensor_generacion_utc)
            {
                continue;
            }

            // Hay datos de los dos sensores y la hora es la misma (se puede continuar)
            $numero_datos_sensores_mismas_horas += 1;

            // Datos de consumo del sensor
            $datos_consumo_sensor_hora = $datos_consumo_sensor_horas[$numero_dato_consumo_sensor_horas];
            $consumo_sensor = $datos_consumo_sensor_hora["consumo"];

            // Consumo de autoconsumo (aplicando el factor de multiplicación de autoconsumo)
            $consumo_sensor_generacion *= $factor_multiplicacion_generacion;

            // Consumo simulado y excedente de consumo simulado
            $consumo_simulado_sensor = $consumo_sensor - $consumo_sensor_generacion;
            $excedente_generacion_simulado = 0;
            if (($tipo_autoconsumo == TIPO_AUTOCONSUMO_CON_ACUMULACION) &&
                (($consumo_simulado_sensor > 0) && ($total_excedente_generacion_simulado > 0)))
            {
                if ($total_excedente_generacion_simulado >= $consumo_simulado_sensor)
                {
                    $total_excedente_generacion_simulado -= $consumo_simulado_sensor;
                    $consumo_simulado_sensor = 0;
                }
                else
                {
                    $consumo_simulado_sensor -= $total_excedente_generacion_simulado;
                    $total_excedente_generacion_simulado = 0;
                }
            }
            if ($consumo_simulado_sensor < 0)
            {
                $excedente_generacion_simulado = $consumo_simulado_sensor * -1;
                $consumo_simulado_sensor = 0;
            }

            // Consumo máximo
            if ($consumo_sensor > $max_consumo)
            {
                $max_consumo = $consumo_sensor;
            }
            if ($consumo_sensor_generacion > $max_consumo)
            {
                $max_consumo = $consumo_sensor_generacion;
            }
            if ($consumo_simulado_sensor > $max_consumo)
            {
                $max_consumo = $consumo_simulado_sensor;
            }
            if ($excedente_generacion_simulado > $max_consumo)
            {
                $max_consumo = $excedente_generacion_simulado;
            }

            // Totales de consumo (acumulados)
            $total_consumo_sensor += $consumo_sensor;
            $total_consumo_sensor_generacion += $consumo_sensor_generacion;
            $total_consumo_simulado_sensor += $consumo_simulado_sensor;
            $total_excedente_generacion_simulado += $excedente_generacion_simulado;

            // Consumo acumulado máximo
            if ($total_consumo_sensor > $max_consumo_acumulado)
            {
                $max_consumo_acumulado = $total_consumo_sensor;
            }
            if ($total_consumo_sensor_generacion > $max_consumo_acumulado)
            {
                $max_consumo_acumulado = $total_consumo_sensor_generacion;
            }
            if ($total_consumo_simulado_sensor > $max_consumo_acumulado)
            {
                $max_consumo_acumulado = $total_consumo_simulado_sensor;
            }
            if ($total_excedente_generacion_simulado > $max_consumo_acumulado)
            {
                $max_consumo_acumulado = $total_excedente_generacion_simulado;
            }

            // Si hay que mostrar el excedente de generación perdido, se calcula si se ha superado la capacidad de acumulación
            if ($mostrar_excedente_generacion_perdido_simulado == true)
            {
                if ($total_excedente_generacion_simulado > $capacidad_acumulacion)
                {
                    $excendente_generacion_perdido_simulado = ($total_excedente_generacion_simulado - $capacidad_acumulacion);
                    $total_excedente_generacion_simulado = $capacidad_acumulacion;
                    $total_excedente_generacion_perdido_simulado += $excendente_generacion_perdido_simulado;

                    // Consumo y consumo acumulado máximo
                    if ($excendente_generacion_perdido_simulado > $max_consumo)
                    {
                        $max_consumo = $excendente_generacion_perdido_simulado;
                    }
                    if ($total_excedente_generacion_perdido_simulado > $max_consumo_acumulado)
                    {
                        $max_consumo_acumulado = $total_excedente_generacion_perdido_simulado;
                    }
                }
                else
                {
                    $excendente_generacion_perdido_simulado = 0;
                }
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_consumo_sensor_generacion_utc - $timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_consumo_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_consumo_sensor_generacion->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_consumo_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_excedente_generacion_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_consumo_acumulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_consumo_acumulado_sensor_generacion->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_consumo_acumulado_simulador_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    $datos_excedente_generacion_acumulado_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);

                    // Excedente de generación perdido
                    if ($mostrar_excedente_generacion_perdido_simulado == true)
                    {
                        $datos_excedente_generacion_perdido_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                        $datos_excedente_generacion_perdido_acumulado_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_consumo_sensor_generacion_anterior_utc = $timestamp_fecha_hora_consumo_sensor_generacion_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Datos para la gráficas de consumo y consumo acumulado
            $datos_consumo_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $consumo_sensor);
            $datos_consumo_sensor_generacion->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $consumo_sensor_generacion);
            $datos_consumo_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $consumo_simulado_sensor);
            $datos_excedente_generacion_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $excedente_generacion_simulado);
            $datos_consumo_acumulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $total_consumo_sensor);
            $datos_consumo_acumulado_sensor_generacion->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $total_consumo_sensor_generacion);
            $datos_consumo_acumulado_simulador_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $total_consumo_simulado_sensor);
            $datos_excedente_generacion_acumulado_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $total_excedente_generacion_simulado);

            // Excedente de generación perdido
            if ($mostrar_excedente_generacion_perdido_simulado == true)
            {
                $datos_excedente_generacion_perdido_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $excendente_generacion_perdido_simulado);
                $datos_excedente_generacion_perdido_acumulado_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_sensor_generacion_utc, $total_excedente_generacion_perdido_simulado);
            }

            // Información de consumos (para el cálculo de costes si es necesario)
            if ($mostrar_info_costes == true)
            {
                array_push($info_consumos_sensor["fechas"], $cadena_fecha_hora_consumo_sensor_generacion_base_datos_utc);
                array_push($info_consumos_sensor["consumos"], $consumo_sensor);
                array_push($info_consumos_simulados_sensor["fechas"], $cadena_fecha_hora_consumo_sensor_generacion_base_datos_utc);
                array_push($info_consumos_simulados_sensor["consumos"], $consumo_simulado_sensor);
            }

            // Se incrementa el número de dato de consumo del sensor
            $numero_dato_consumo_sensor_horas += 1;
            if (count($datos_consumo_sensor_horas) < ($numero_dato_consumo_sensor_horas + 1))
            {
                break;
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_datos_sensores_mismas_horas == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Nombre del excedente de generación simulado
        switch ($tipo_autoconsumo)
        {
            case TIPO_AUTOCONSUMO_SIN_ACUMULACION:
            {
                $nombre_excedente_generacion_simulado = $idiomas->_("Excedente de generación simulado");
                break;
            }
            case TIPO_AUTOCONSUMO_CON_ACUMULACION:
            {
                $nombre_excedente_generacion_simulado = $idiomas->_("Generación acumulada simulada");
                break;
            }
        }

        // Tabla de consumos
        $numero_columnas_tabla_consumos = NUMERO_COLUMNAS_TABLA_CONSUMOS_SIMULADOR_AUTOCONSUMO;
        if ($mostrar_excedente_generacion_perdido_simulado == true)
        {
            $numero_columnas_tabla_consumos += 1;
        }
        $params_tabla_consumos = array(
            "numero_columnas" => $numero_columnas_tabla_consumos,
            "generar_valores_xml" => true
        );
        $cabecera_tabla_consumos = array(
            $idiomas->_("Consumo actual"),
            $idiomas->_("Generación simulada"),
            $idiomas->_("Consumo simulado"),
            $nombre_excedente_generacion_simulado
        );
        if ($mostrar_excedente_generacion_perdido_simulado == true)
        {
            $numero_columnas_tabla_consumos += 1;
            array_push($cabecera_tabla_consumos, $idiomas->_("Generación perdida simulada"));
        }
        $tabla_consumos = new TablaDatos(
            "tabla-consumos-simulador-autoconsumo",
            $idiomas->_("Consumos"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_consumos
        );
        $tabla_consumos->anyade_cabecera("", $cabecera_tabla_consumos);

        // Datos para la tabla de consumos
        $cadena_total_consumo_sensor = formatea_numero($total_consumo_sensor, 2, false);
        $cadena_total_consumo_sensor_generacion = formatea_numero($total_consumo_sensor_generacion, 2, false);
        $cadena_total_consumo_simulado_sensor = formatea_numero($total_consumo_simulado_sensor, 2, false);
        $cadena_total_excedente_generacion_simulado = formatea_numero($total_excedente_generacion_simulado, 2, false);
        if (($total_consumo_sensor == 0) && ($total_consumo_simulado_sensor != 0))
        {
           $cadena_porcentaje_consumo_simulado = "ND";
           $signo_porcentaje_consumo_simulado = "";
        }
        else
        {
            $porcentaje_consumo_simulado = dame_porcentaje_valor_referencia($total_consumo_simulado_sensor, $total_consumo_sensor);
            $cadena_porcentaje_consumo_simulado = formatea_numero($porcentaje_consumo_simulado, 2);
            if ($total_consumo_sensor == $total_consumo_simulado_sensor)
            {
                $imagen_porcentaje_consumo_simulado = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
                $signo_porcentaje_consumo_simulado = "";
            }
            else
            {
                if ($total_consumo_sensor < $total_consumo_simulado_sensor)
                {
                    $imagen_porcentaje_consumo_simulado = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje_consumo_simulado = "+";
                }
                else
                {
                    $imagen_porcentaje_consumo_simulado = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje_consumo_simulado = "-";
                }
            }
        }
        if (($total_consumo_sensor_generacion == 0) && ($total_excedente_generacion_simulado != 0))
        {
           $cadena_porcentaje_excedente_generacion_simulado = "ND";
        }
        else
        {
            $porcentaje_excedente_generacion_simulado = ($total_excedente_generacion_simulado * 100) / $total_consumo_sensor_generacion;
            $cadena_porcentaje_excedente_generacion_simulado = formatea_numero($porcentaje_excedente_generacion_simulado, 2);
        }
        $datos_fila_tabla_consumos = array();
        $datos_fila_tabla_consumos[0] = $cadena_total_consumo_sensor." ".$unidad_medida_consumo;
        $datos_fila_tabla_consumos[1] = $cadena_total_consumo_sensor_generacion." ".$unidad_medida_consumo;
        $datos_fila_tabla_consumos[2] = $imagen_porcentaje_consumo_simulado." ".$cadena_total_consumo_simulado_sensor." ".$unidad_medida_consumo.
            " (".$signo_porcentaje_consumo_simulado.$cadena_porcentaje_consumo_simulado." "."%".")";
        $datos_fila_tabla_consumos[3] = $cadena_total_excedente_generacion_simulado." ".$unidad_medida_consumo.
            " (".$cadena_porcentaje_excedente_generacion_simulado." "."%".")";
        if ($mostrar_excedente_generacion_perdido_simulado == true)
        {
            $cadena_total_excedente_generacion_perdido_simulado = formatea_numero($total_excedente_generacion_perdido_simulado, 2, false);
            if (($total_excedente_generacion_simulado == 0) && ($total_excedente_generacion_perdido_simulado != 0))
            {
               $cadena_porcentaje_excedente_generacion_perdido_simulado = "ND";
            }
            else
            {
                $porcentaje_excedente_generacion_perdido_simulado = ($total_excedente_generacion_perdido_simulado * 100) / $total_consumo_sensor_generacion;
                $cadena_porcentaje_excedente_generacion_perdido_simulado = formatea_numero($porcentaje_excedente_generacion_perdido_simulado, 2);
            }
            $datos_fila_tabla_consumos[4] = $cadena_total_excedente_generacion_perdido_simulado." ".$unidad_medida_consumo.
                " (".$cadena_porcentaje_excedente_generacion_perdido_simulado." "."%".")";
        }
        $tabla_consumos->anyade_fila("", $datos_fila_tabla_consumos);

        // Si hay que mostrar información de costes
        if ($mostrar_info_costes == true)
        {
            // País de tarifas
            $pais_tarifas = dame_pais_tarifas_medicion($medicion);

            // Informaciones de consumos
            $infos_consumos = array(
                $info_consumos_sensor,
                $info_consumos_simulados_sensor);

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_TARIFA_CONSUMOS,
                    "medicion" => $medicion,
                    "pais_tarifas" => $pais_tarifas,
                    "id_red" => $_SESSION["id_red"],
                    "id_tarifa" => $id_tarifa,
                    "infos_consumos" => $infos_consumos
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Se recuperan los costes del consumo actual y del consumo simulado del resultado de la función externa
            $infos_costes = $resultado_funcion_externa["infos_costes"];
            $info_costes_sensor = $infos_costes[0];
            $info_costes_simulados_sensor = $infos_costes[1];
            $fechas_sensor = $info_costes_sensor["fechas"];
            $costes_sensor = $info_costes_sensor["costes"];
            $costes_simulados_sensor = $info_costes_simulados_sensor["costes"];
            $numero_costes_sensor = count($info_costes_sensor["costes"]);
            $numero_costes_simulados_sensor = count($info_costes_simulados_sensor["costes"]);
            if (($numero_costes_sensor > 0) && ($numero_costes_simulados_sensor > 0))
            {
                // El número de costes debe ser el mismo
                // (sólo se han calculado costes donde hay consumo en los dos sensores con la misma tarifa)
                if ($numero_costes_sensor != $numero_costes_simulados_sensor)
                {
                    throw new Exception("El número de costes y costes simulados del sensor es diferente");
                }

                // Variables (de costes)
                $max_coste = -INF;
                $max_coste_acumulado = -INF;
                $total_coste_sensor = 0;
                $total_coste_simulado_sensor = 0;
                $datos_coste_sensor = new VectorDatos();
                $datos_coste_simulado_sensor = new VectorDatos();
                $datos_coste_acumulado_sensor = new VectorDatos();
                $datos_coste_acumulado_simulado_sensor = new VectorDatos();
                $grafica_costes = new VectorDatos();
                $grafica_costes_acumulados = new VectorDatos();
                $etiquetas_graficas_costes = new VectorDatos();

                // Flag de datos de costes
                $hay_datos_costes = true;

                // Se recorren los datos de costes
                $timestamp_fecha_hora_costes_anterior_utc = NULL;
                $numero_puntos_seguidos_grafica = 0;
                for ($i = 0; $i < $numero_costes_sensor; $i++)
                {
                    $coste_sensor = $costes_sensor[$i];
                    $coste_simulado_sensor = $costes_simulados_sensor[$i];
                    if ($coste_sensor > $max_coste)
                    {
                        $max_coste = $coste_sensor;
                    }
                    if ($coste_simulado_sensor > $max_coste)
                    {
                        $max_coste = $coste_simulado_sensor;
                    }

                    // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                    $timestamp_fecha_hora_costes_utc = dame_timestamp_cadena_fecha_milisegundos($fechas_sensor[$i], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $timestamp_fecha_hora_costes_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                    if (($numero_puntos_seguidos_grafica > 1) &&
                        ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_costes_anterior_utc !== NULL))
                    {
                        $segundos_entre_valores = ($timestamp_fecha_hora_costes_utc - $timestamp_fecha_hora_costes_anterior_utc) / 1000;
                        if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                        {
                            $numero_puntos_seguidos_grafica = 0;
                            $datos_coste_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_anterior_utc + 1, NULL);
                            $datos_coste_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_anterior_utc + 1, NULL);
                            $datos_coste_acumulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_anterior_utc + 1, NULL);
                            $datos_coste_acumulado_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_anterior_utc + 1, NULL);
                        }
                    }
                    $timestamp_fecha_hora_costes_anterior_utc = $timestamp_fecha_hora_costes_utc;
                    $numero_puntos_seguidos_grafica += 1;

                    // Se añaden los costes
                    $datos_coste_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_utc, $coste_sensor);
                    $datos_coste_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_utc, $coste_simulado_sensor);

                    // Totales de coste (acumulados)
                    $total_coste_sensor += $coste_sensor;
                    $total_coste_simulado_sensor += $coste_simulado_sensor;

                    // Coste acumulado máximo
                    if ($total_coste_sensor > $max_coste_acumulado)
                    {
                        $max_coste_acumulado = $total_coste_sensor;
                    }
                    if ($total_coste_simulado_sensor > $max_coste_acumulado)
                    {
                        $max_coste_acumulado = $total_coste_simulado_sensor;
                    }

                    // Se añaden los costes acumulados
                    $datos_coste_acumulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_utc, $total_coste_sensor);
                    $datos_coste_acumulado_simulado_sensor->anyade_tupla_pareja_datos($timestamp_fecha_hora_costes_utc, $total_coste_simulado_sensor);
                }

                // Tabla de costes
                $params_tabla_costes = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_COSTES_SIMULADOR_AUTOCONSUMO,
                    "generar_valores_xml" => true
                );
                $cabecera_tabla_costes = array(
                    $idiomas->_("Coste actual"),
                    $idiomas->_("Coste simulado"),
                    $idiomas->_("Diferencia de coste")
                );
                $tabla_costes = new TablaDatos(
                    "tabla-costes-simulador-autoconsumo",
                    $idiomas->_("Costes"),
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_costes
                );
                $tabla_costes->anyade_cabecera("", $cabecera_tabla_costes);

                // Datos para la tabla de costes
                $cadena_total_coste_sensor = formatea_numero($total_coste_sensor, 2, false);
                $cadena_total_coste_simulado_sensor = formatea_numero($total_coste_simulado_sensor, 2, false);
                $cadena_diferencia_coste = formatea_numero($total_coste_sensor - $total_coste_simulado_sensor, 2, false);
                $porcentaje_diferencia_coste = dame_porcentaje_valor_referencia($total_coste_simulado_sensor, $total_coste_sensor);
                $cadena_porcentaje_diferencia_coste = formatea_numero($porcentaje_diferencia_coste, 2);
                if ($total_coste_sensor == $total_coste_simulado_sensor)
                {
                    $imagen_porcentaje_diferencia_coste = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje_diferencia_coste = "";
                }
                else
                {
                    if ($total_coste_sensor < $total_coste_simulado_sensor)
                    {
                        $imagen_porcentaje_diferencia_coste = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i>";
                        $signo_porcentaje_diferencia_coste = "+";
                    }
                    else
                    {
                        $imagen_porcentaje_diferencia_coste = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i>";
                        $signo_porcentaje_diferencia_coste = "-";
                    }
                }
                $datos_fila_tabla_costes = array();
                $datos_fila_tabla_costes[0] = $cadena_total_coste_sensor." ".$unidad_medida_coste;
                $datos_fila_tabla_costes[1] = $cadena_total_coste_simulado_sensor." ".$unidad_medida_coste;
                $datos_fila_tabla_costes[2] = $imagen_porcentaje_diferencia_coste." ".$cadena_diferencia_coste." ".$unidad_medida_coste.
                    " (".$signo_porcentaje_diferencia_coste.$cadena_porcentaje_diferencia_coste." "."%".")";
                $tabla_costes->anyade_fila("", $datos_fila_tabla_costes);
                $datos_tabla_costes = $tabla_costes->dame_tabla();

                // Datos de gráficas (de costes)
                $grafica_costes->anyade_dato($datos_coste_sensor->dame_datos());
                $grafica_costes->anyade_dato($datos_coste_simulado_sensor->dame_datos());
                $grafica_costes_acumulados->anyade_dato($datos_coste_acumulado_sensor->dame_datos());
                $grafica_costes_acumulados->anyade_dato($datos_coste_acumulado_simulado_sensor->dame_datos());
                $datos_grafica_costes = $grafica_costes->dame_datos();
                $datos_grafica_costes_acumulados = $grafica_costes_acumulados->dame_datos();

                // Etiquetas de las gráficas (de costes)
                $etiquetas_graficas_costes->anyade_etiqueta($idiomas->_("Coste actual")." (".$nombre_sensor.")");
                $etiquetas_graficas_costes->anyade_etiqueta($idiomas->_("Coste simulado"));
                $datos_etiquetas_graficas_costes = $etiquetas_graficas_costes->dame_datos();
            }
            else
            {
                $hay_datos_costes = false;
            }
        }
        else
        {
            $hay_datos_costes = false;
        }
        if ($hay_datos_costes == false)
        {
            $datos_grafica_costes = NULL;
            $datos_grafica_costes_acumulados = NULL;
            $datos_tabla_costes = NULL;
            $max_coste = -INF;
            $max_coste_acumulado = -INF;
            $datos_etiquetas_graficas_costes = NULL;
        }

        // Datos de gráficas (de consumo)
        $grafica_consumos->anyade_dato($datos_consumo_sensor->dame_datos());
        $grafica_consumos->anyade_dato($datos_consumo_sensor_generacion->dame_datos());
        $grafica_consumos->anyade_dato($datos_consumo_simulado_sensor->dame_datos());
        $grafica_consumos->anyade_dato($datos_excedente_generacion_simulado->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_consumo_acumulado_sensor->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_consumo_acumulado_sensor_generacion->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_consumo_acumulado_simulador_sensor->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_excedente_generacion_acumulado_simulado->dame_datos());

        // Etiquetas de las gráficas (de consumo)
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo actual")." (".$nombre_sensor.")");
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Generación simulada")." (".$nombre_sensor_generacion.")");
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo simulado"));
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Excedente de generación simulado"));
        $etiquetas_graficas_consumos_acumulados->anyade_etiqueta($idiomas->_("Consumo actual")." (".$nombre_sensor.")");
        $etiquetas_graficas_consumos_acumulados->anyade_etiqueta($idiomas->_("Generación simulada")." (".$nombre_sensor_generacion.")");
        $etiquetas_graficas_consumos_acumulados->anyade_etiqueta($idiomas->_("Consumo simulado"));
        $etiquetas_graficas_consumos_acumulados->anyade_etiqueta($nombre_excedente_generacion_simulado);

        // Excedente de generación perdido
        if ($mostrar_excedente_generacion_perdido_simulado == true)
        {
            $grafica_consumos->anyade_dato($datos_excedente_generacion_perdido_simulado->dame_datos());
            $grafica_consumos_acumulados->anyade_dato($datos_excedente_generacion_perdido_acumulado_simulado->dame_datos());
            $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Generación perdida simulada"));
            $etiquetas_graficas_consumos_acumulados->anyade_etiqueta($idiomas->_("Generación perdida simulada"));
        }

        // Los valores 'INF' y '-INF' no se pueden convertir a cadena, se cambian por NA (ocurre cuando no hay datos)
        if ($max_consumo == -INF)
        {
            $max_consumo = "ND";
        }
        if ($max_consumo_acumulado == -INF)
        {
            $max_consumo_acumulado = "ND";
        }
        if ($max_coste == -INF)
        {
            $max_coste = "ND";
        }
        if ($max_coste_acumulado == -INF)
        {
            $max_coste_acumulado = "ND";
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "grafica_consumos" => $grafica_consumos->dame_datos(),
            "grafica_consumos_acumulados" => $grafica_consumos_acumulados->dame_datos(),
            "tabla_consumos" => $tabla_consumos->dame_tabla(),
            "mostrar_info_costes" => $mostrar_info_costes,
            "hay_datos_costes" => $hay_datos_costes,
            "grafica_costes" => $datos_grafica_costes,
            "grafica_costes_acumulados" => $datos_grafica_costes_acumulados,
            "tabla_costes" => $datos_tabla_costes,
            "etiquetas_consumos" => $etiquetas_graficas_consumos->dame_datos(),
            "etiquetas_consumos_acumulados" => $etiquetas_graficas_consumos_acumulados->dame_datos(),
            "etiquetas_costes" => $datos_etiquetas_graficas_costes,
            "max_consumo" => $max_consumo,
            "max_consumo_acumulado" => $max_consumo_acumulado,
            "max_coste" => $max_coste,
            "max_coste_acumulado" => $max_coste_acumulado,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste);
        return ($resultado);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_simulador_autoconsumo($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-autoconsumo'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-autoconsumo' hidden>
                        <div class='grafica100' id='grafica-consumos-simulador-autoconsumo'></div>
                        <div class='grafica100' id='grafica-consumos-acumulados-simulador-autoconsumo'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-simulador-autoconsumo'></div>
                        <div class='grafica100' id='grafica-costes-simulador-autoconsumo'></div>
                        <div class='grafica100' id='grafica-costes-acumulados-simulador-autoconsumo'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-costes-simulador-autoconsumo'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulador de autoconsumo (consumos)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-autoconsumo-consumos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_autoconsumo(TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-autoconsumo-consumos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-simulador-autoconsumo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-acumulados-simulador-autoconsumo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-simulador-autoconsumo'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de simulador de autoconsumo (costes)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-autoconsumo-costes'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_autoconsumo(TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-autoconsumo-costes'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-simulador-autoconsumo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-acumulados-simulador-autoconsumo'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-costes-simulador-autoconsumo'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
