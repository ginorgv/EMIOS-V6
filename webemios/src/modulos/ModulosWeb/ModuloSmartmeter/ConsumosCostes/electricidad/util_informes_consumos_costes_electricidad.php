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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/util_informes_consumos_costes_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/util_consumos_costes_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/util_electricidad.php');


    //
    // Funciones de información de consumos y costes (electricidad)
    //


    // Devuelve información de consumos y costes por tramo de un sensor
    function dame_consumos_costes_sensor_tramos_electricidad($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $valor = $parametros["valor"];
        $agrupacion_valores = $parametros["agrupacion_valores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $mostrar_tablas_tramos = $parametros["mostrar_tablas_tramos"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera el valor del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TODOS);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
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

        // Variables
        $min_fecha_hora_datos_tramos_utc = NULL;
        $max_fecha_hora_datos_tramos_utc = NULL;
        $min_fecha_hora_datos_tramos_local = NULL;
        $max_fecha_hora_datos_tramos_local = NULL;
        $min_fecha_datos_tramos_local = NULL;
        $max_fecha_datos_tramos_local = NULL;

        // Unidades de medida
        $unidad_medida_potencia = $idiomas->_("kW");
        $unidad_medida_consumo = NodoSensor::dame_unidad_medida_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, ID_NINGUNO, CAMPO_INCREMENTO);
        $unidad_medida_coste = $_SESSION["moneda"];
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
        }

        // Filtro de consulta de  horario semanal y la exclusión e inclusión de fechas
        $filtro_consulta_horario_semanal_fechas = dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Consulta de tramos
        $consulta_tramos = "
            SELECT
                DISTINCT(tramo) AS tramo
            FROM ".TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (tramo IS NOT NULL)
                AND (incremento IS NOT NULL)
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_tramos .= $filtro_consulta_horario_semanal_fechas;

        // Se añade el orden y se ejecuta la consulta
        $consulta_tramos .= "
            ORDER BY tramo";
        $res_tramos = $bd_datos->ejecuta_consulta($consulta_tramos);
        if ($res_tramos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tramos."'");
        }
        $tramos = array();
        $nombres_tramos = array();
        while ($fila_tramo = $res_tramos->dame_siguiente_fila())
        {
            $tramo = $fila_tramo["tramo"];
            array_push($tramos, $tramo);
            array_push($nombres_tramos, "P".$tramo);
        }

        // Si no hay datos no se hace nada
        if (count($tramos) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $valores_coste_nulos = true;

        // Se realiza la consulta de consumos y costes horarios:
        // - Se guardan los datos para las gráficas de consumos y costes por horas
        if (($agrupacion_valores == ID_TODOS) || ($agrupacion_valores == AGRUPACION_VALORES_HORA))
        {
            // Se recupera la información del ratio (si aplica)
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor_horas = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    INTERVALO_VALORES_HORA,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Consulta de consumos y costes horarios
            $consulta_consumos_costes_horarios = "
                SELECT
                    hora AS fecha_hora,
                    incremento AS consumo,
                    coste,
                    tramo
                FROM ".TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS."
                WHERE
                    (sensor = '".$bd_datos->_($nombre_sensor)."')
                    AND (red = '".$_SESSION["id_red"]."')
                    AND (tramo IS NOT NULL)
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

            // Se añaden el horario semanal y la exclusión e inclusión de fechas
            $consulta_consumos_costes_horarios .= $filtro_consulta_horario_semanal_fechas;

            // Se añade el orden y se ejecuta la consulta
            $consulta_consumos_costes_horarios .= "
                ORDER BY hora ASC";
            $res_consumos_costes_horarios = $bd_datos->ejecuta_consulta($consulta_consumos_costes_horarios);
            if ($res_consumos_costes_horarios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_consumos_costes_horarios."'");
            }

            // Variables para guardar los datos de consumo y coste de tramos por horas
            $datos_consumo_tramos_horas = array();
            $datos_coste_tramos_horas = array();
            foreach ($tramos as $tramo)
            {
                $datos_consumo_tramo_horas = new VectorDatos();
                $datos_coste_tramo_horas = new VectorDatos();
                $datos_consumo_tramos_horas[$tramo] = $datos_consumo_tramo_horas;
                $datos_coste_tramos_horas[$tramo] = $datos_coste_tramo_horas;
            }

            // Se guarda la información de consumos y costes por tramo horarios
            $max_consumo_hora = -INF;
            $max_coste_hora = -INF;
            $grafica_consumos_tramos_horarios = new VectorDatos();
            $grafica_costes_tramos_horarios = new VectorDatos();
            $numero_huecos_datos_consumos_costes_tramos_horarios = 0;
            $timestamp_fecha_hora_anterior_utc = NULL;
            while ($fila_consumos_costes_horarios = $res_consumos_costes_horarios->dame_siguiente_fila())
            {
                // Fecha de la fila
                $cadena_fecha_hora_base_datos_utc = $fila_consumos_costes_horarios['fecha_hora'];

                // Valor del ratio
                if ($aplicar_ratio == true)
                {
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio_sensor_horas, $cadena_fecha_hora_base_datos_utc, true);
                }

                // Valores de la fila
                $consumo = $fila_consumos_costes_horarios['consumo'];
                if ($consumo !== NULL)
                {
                    $consumo = (float) $consumo;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_valor($valor_ratio, $consumo);
                    }
                }
                if ($consumo === NULL)
                {
                    continue;
                }
                $coste = $fila_consumos_costes_horarios['coste'];
                if ($coste !== NULL)
                {
                    $valores_coste_nulos = false;
                    $coste = (float) $coste;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_valor($valor_ratio, $coste);
                    }
                }
                if ($coste === NULL){
                    $coste = "";
                }
                $tramo = $fila_consumos_costes_horarios['tramo'];

                // Máximos y mínimos
                $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria);
                if ($min_fecha_hora_datos_tramos_utc === NULL)
                {
                    $min_fecha_hora_datos_tramos_utc = $fecha_hora_utc;
                }
                $max_fecha_hora_datos_tramos_utc = $fecha_hora_utc;
                if ($min_fecha_hora_datos_tramos_local === NULL)
                {
                    $min_fecha_hora_datos_tramos_local = $fecha_hora_local;
                }
                $max_fecha_hora_datos_tramos_local = $fecha_hora_local;
                if ($consumo > $max_consumo_hora)
                {
                    $max_consumo_hora = $consumo;
                }
                if ($coste > $max_coste_hora)
                {
                    $max_coste_hora = $coste;
                }

                // Fechas
                $timestamp_fecha_hora_utc = dame_timestamp_fecha_milisegundos($fecha_hora_utc);
                $timestamp_fecha_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                if ($timestamp_fecha_hora_anterior_utc !== NULL)
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_utc - $timestamp_fecha_hora_anterior_utc) / 1000;
                    if ($segundos_entre_valores > 3600)
                    {
                        $numero_huecos_datos_consumos_costes_tramos_horarios += (int) ($segundos_entre_valores / 3600) - 1;
                    }
                }
                $timestamp_fecha_hora_anterior_utc = $timestamp_fecha_hora_utc;
                $cadena_fecha_hora_local_utc = convierte_fecha_a_cadena($fecha_hora_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);

                // Se recorren cada uno de los tramos
                foreach ($tramos as $tramo)
                {
                    $datos_consumo_tramo_horas = $datos_consumo_tramos_horas[$tramo];
                    $datos_coste_tramo_horas = $datos_coste_tramos_horas[$tramo];
                    if ($tramo == $fila_consumos_costes_horarios['tramo'])
                    {
                        $consumo_tramo = $consumo;
                        $coste_tramo = $coste;
                    }
                    else
                    {
                        $consumo_tramo = 0;
                        $coste_tramo = 0;
                    }

                    // Nota: Se suman 30 min para que se muestre la barra de hora en la hora correspondiente
                    $timestamp_fecha_hora_grafica_utc = $timestamp_fecha_hora_utc + (1800 * 1000);
                    $datos_consumo_tramo_horas->anyade_tupla_pareja_datos_etiqueta(
                        $timestamp_fecha_hora_grafica_utc,
                        $consumo_tramo,
                        $cadena_fecha_hora_local_local);
                    $datos_coste_tramo_horas->anyade_tupla_pareja_datos_etiqueta(
                        $timestamp_fecha_hora_grafica_utc,
                        $coste_tramo,
                        $cadena_fecha_hora_local_local);
                }
            }
            foreach ($tramos as $tramo)
            {
                $grafica_consumos_tramos_horarios->anyade_dato($datos_consumo_tramos_horas[$tramo]->dame_datos());
                $grafica_costes_tramos_horarios->anyade_dato($datos_coste_tramos_horas[$tramo]->dame_datos());
            }

            // Se recuperan los datos de las gráficas si es necesario
            if (($valor == ID_TODOS) || ($valor == VALOR_CONSUMO))
            {
                $datos_grafica_consumos_tramos_horarios = $grafica_consumos_tramos_horarios->dame_datos();
            }
            else
            {
                $datos_grafica_consumos_tramos_horarios = NULL;
            }
            if (($valor == ID_TODOS) || ($valor == VALOR_COSTE))
            {
                $datos_grafica_costes_tramos_horarios = $grafica_costes_tramos_horarios->dame_datos();
            }
            else
            {
                $datos_grafica_costes_tramos_horarios = NULL;
            }
        }

        // Si no hay datos no se hace nada (por no haber valores en los ratios)
        if (($agrupacion_valores == ID_TODOS) || ($agrupacion_valores == AGRUPACION_VALORES_HORA))
        {
            if ($max_consumo_hora == -INF)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
        }

        // Se realiza la consulta de consumos y costes diarios:
        // - Se guardan los datos para las gráficas de consumos y costes por días (fechas)
        // - Se guardan los datos para las gráficas de consumos y costes por días de la semana
        // - Se guardan los datos por tramo y día para las tablas de consumos y costes por tramo
        if (($agrupacion_valores == ID_TODOS) ||
            ($agrupacion_valores == AGRUPACION_VALORES_FECHA) || ($agrupacion_valores == AGRUPACION_VALORES_DIA_SEMANA))
        {
            // Se recupera la información del ratio (si aplica)
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor_dias = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    INTERVALO_VALORES_DIA,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Consulta de consumos y costes diarios
            $fila_sensor = dame_fila_sensor($id_sensor);
            $granularidad_cuartohoraria = $fila_sensor["granularidad_cuartohoraria"];
            if ($granularidad_cuartohoraria == VALOR_SI)
            {
                $consulta_consumos_costes_diarios = "
                    SELECT
                        hora AS fecha_hora,
                        DATE(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS fecha,
                        WEEKDAY(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS dia_semana,
                        SUM(incremento) AS consumo_diario,
                        SUM(coste) AS coste_diario,
                        tramo,
                        SUM(horas) AS horas_dia,
                        MAX(incremento) AS max_potencia_cuartohoraria,
                        MAX(coste) AS max_coste_cuartohorario
                    FROM ".TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_CUARTOSHORA."
                    WHERE
                        (sensor = '".$bd_datos->_($nombre_sensor)."')
                        AND (red = '".$_SESSION["id_red"]."')
                        AND (tramo IS NOT NULL)
                        AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                        AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            }
            else
            {
                $consulta_consumos_costes_diarios = "
                    SELECT
                        hora AS fecha_hora,
                        DATE(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS fecha,
                        WEEKDAY(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS dia_semana,
                        SUM(incremento) AS consumo_diario,
                        SUM(coste) AS coste_diario,
                        tramo,
                        SUM(horas) AS horas_dia,
                        MAX(incremento / 4) AS max_potencia_cuartohoraria,
                        MAX(coste / 4) AS max_coste_cuartohorario
                    FROM ".TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS."
                    WHERE
                        (sensor = '".$bd_datos->_($nombre_sensor)."')
                        AND (red = '".$_SESSION["id_red"]."')
                        AND (tramo IS NOT NULL)
                        AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                        AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            }

            // Se añaden el horario semanal y la exclusión e inclusión de fechas
            $consulta_consumos_costes_diarios .= $filtro_consulta_horario_semanal_fechas;

            // Se añaden la agrupación y el orden y se ejecuta la consulta
            $consulta_consumos_costes_diarios .= "
                GROUP BY
                    tramo,
                    DATE(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'))
                ORDER BY fecha";
            $res_consumos_costes_diarios = $bd_datos->ejecuta_consulta($consulta_consumos_costes_diarios);
            if ($res_consumos_costes_diarios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_consumos_costes_diarios."'");
            }

            // Se guarda la información de consumos y costes por tramo diarios (fechas) y por días de la semana
            $datos_tramos = array();
            $datos_tramos_dias_semana = array();
            $consumos_totales_dias = array();
            $costes_totales_dias = array();
            while ($fila_consumos_costes_diarios = $res_consumos_costes_diarios->dame_siguiente_fila())
            {
                // Fecha de la fila
                $cadena_fecha_hora_base_datos_utc = $fila_consumos_costes_diarios['fecha_hora'];

                // Valor del ratio
                if ($aplicar_ratio == true)
                {
                    $valor_ratio = dame_valor_ratio_fecha_local_ignora_horas($info_ratio_sensor_dias, $cadena_fecha_hora_base_datos_utc, false);
                }

                // Fecha y valores de la fila
                $cadena_fecha_base_datos_local = $fila_consumos_costes_diarios['fecha'];
                $dia_semana = $fila_consumos_costes_diarios['dia_semana'];
                $consumo_diario = $fila_consumos_costes_diarios['consumo_diario'];
                if ($consumo_diario !== NULL)
                {
                    $consumo_diario = (float) $consumo_diario;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_valor($valor_ratio, $consumo_diario);
                    }
                }
                if ($consumo_diario === NULL)
                {
                    continue;
                }
                $coste_diario = $fila_consumos_costes_diarios['coste_diario'];
                if ($coste_diario !== NULL)
                {
                    $valores_coste_nulos = false;
                    $coste_diario = (float) $coste_diario;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_valor($valor_ratio, $coste_diario);
                    }
                }
                if ($coste_diario === NULL)
                {
                    $coste_diario = "";
                }
                
                $tramo = $fila_consumos_costes_diarios['tramo'];
                $horas_dia = (float) $fila_consumos_costes_diarios['horas_dia'];
                $max_potencia_cuartohoraria = ((float) $fila_consumos_costes_diarios['max_potencia_cuartohoraria']) * 4;
                $max_coste_cuartohorario = $fila_consumos_costes_diarios['max_coste_cuartohorario'];
                if ($max_coste_cuartohorario !== NULL)
                {
                    $max_coste_cuartohorario = (float) $max_coste_cuartohorario;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_valor($valor_ratio, $max_coste_cuartohorario);
                    }
                }
                if ($max_coste_cuartohorario === NULL)
                {
                    $max_coste_cuartohorario = "";
                }

                // https://github.com/kartik-v/yii2-datecontrol/issues/29
                $fecha_local = convierte_cadena_a_fecha($cadena_fecha_base_datos_local, FORMATO_FECHA_BASE_DATOS, $zona_horaria);
                if ($min_fecha_datos_tramos_local === NULL)
                {
                    $min_fecha_datos_tramos_local = clone $fecha_local;
                    $min_fecha_datos_tramos_local->setTime(0, 0, 0);
                }
                $max_fecha_datos_tramos_local = clone $fecha_local;
                $max_fecha_datos_tramos_local->setTime(0, 0, 0);
                $cadena_fecha_local_local = convierte_fecha_a_cadena($fecha_local, $_SESSION["formato_fecha_local"]);

                // Se añade la información de la fila de consumos y costes diarios por tramo por día (fecha)
                if (array_key_exists($tramo, $datos_tramos) == false)
                {
                    $datos_tramos[$tramo] = array(
                        "fechas" => array(),
                        "consumos_diarios" => array(),
                        "costes_diarios" => array(),
                        "total_horas" => 0.0,
                        "total_consumo" => 0.0,
                        "total_coste" => 0.0,
                        "max_potencia_cuartohoraria" => 0.0,
                        "max_coste_hora" => 0.0);
                }
                array_push($datos_tramos[$tramo]["fechas"], $cadena_fecha_local_local);
                array_push($datos_tramos[$tramo]["consumos_diarios"], $consumo_diario);
                array_push($datos_tramos[$tramo]["costes_diarios"], $coste_diario);
                $datos_tramos[$tramo]["total_horas"] += $horas_dia;
                $datos_tramos[$tramo]["total_consumo"] += $consumo_diario;
                $datos_tramos[$tramo]["total_coste"] += $coste_diario;
                if ($max_potencia_cuartohoraria > $datos_tramos[$tramo]["max_potencia_cuartohoraria"])
                {
                    $datos_tramos[$tramo]["max_potencia_cuartohoraria"] = $max_potencia_cuartohoraria;
                }
                if ($max_coste_cuartohorario > $datos_tramos[$tramo]["max_coste_cuartohorario"])
                {
                    $datos_tramos[$tramo]["max_coste_cuartohorario"] = $max_coste_cuartohorario;
                }

                // Máximo de consumos y costes por días (para los máximos de las gráficas)
                if (array_key_exists($cadena_fecha_local_local, $consumos_totales_dias) == false)
                {
                    $consumos_totales_dias[$cadena_fecha_local_local] = 0;
                }
                $consumos_totales_dias[$cadena_fecha_local_local] += $consumo_diario;
                if (array_key_exists($cadena_fecha_local_local, $costes_totales_dias) == false)
                {
                    $costes_totales_dias[$cadena_fecha_local_local] = 0;
                }
                $costes_totales_dias[$cadena_fecha_local_local] += $coste_diario;

                // Se añade la información de la fila de consumos y costes por tramo por día de la semana
                if (array_key_exists($tramo, $datos_tramos_dias_semana) == false)
                {
                    $datos_tramos_dias_semana[$tramo] = array();
                }
                if (array_key_exists($dia_semana, $datos_tramos_dias_semana[$tramo]) == false)
                {
                    $datos_tramos_dias_semana[$tramo][$dia_semana] = array(
                        "consumos_dia_semana" => array(),
                        "costes_dia_semana" => array(),
                        "total_dias_consumo" => 0,
                        "total_dias_coste" => 0,
                        "medias_consumos_dia_semana" => NULL,
                        "medias_costes_dia_semana" => NULL);
                }
                array_push($datos_tramos_dias_semana[$tramo][$dia_semana]["consumos_dia_semana"], $consumo_diario);

                // Se compara con el valor de cadena vacía para distinguir si es un valor nulo o un 0 (para contabilizarlo o no en la media)
                if($coste_diario !== ""){
                    array_push($datos_tramos_dias_semana[$tramo][$dia_semana]["costes_dia_semana"], $coste_diario);
                    $datos_tramos_dias_semana[$tramo][$dia_semana]["total_dias_coste"] += 1;
                }
                $datos_tramos_dias_semana[$tramo][$dia_semana]["total_dias_consumo"] += 1;
            }

            // Si no hay datos no se hace nada (por no haber valores en los ratios o cuartohorarios si corresponde)
            if (($agrupacion_valores == ID_TODOS) ||
                ($agrupacion_valores == AGRUPACION_VALORES_FECHA) || ($agrupacion_valores == AGRUPACION_VALORES_DIA_SEMANA))
            {
                if (($min_fecha_datos_tramos_local === NULL) || ($max_fecha_datos_tramos_local === NULL))
                {
                    $resultado = array(
                        "res" => "ERROR",
                        "msg" => $idiomas->_("No hay datos"));
                    return ($resultado);
                }
            }

            // Si la agrupación de valores es todos y las fechas mínimas y máximas no coinciden, se devuelve sin datos
            if ($agrupacion_valores == ID_TODOS)
            {
                $min_fecha_hora_datos_tramos_local_aux = clone $min_fecha_hora_datos_tramos_local;
                $min_fecha_hora_datos_tramos_local_aux->setTime(0, 0, 0);
                $max_fecha_hora_datos_tramos_local_aux = clone $max_fecha_hora_datos_tramos_local;
                $max_fecha_hora_datos_tramos_local_aux->setTime(0, 0, 0);
                if (($min_fecha_datos_tramos_local <> $min_fecha_hora_datos_tramos_local_aux) ||
                    ($max_fecha_datos_tramos_local <> $max_fecha_hora_datos_tramos_local_aux))
                {
                    $resultado = array(
                        "res" => "ERROR",
                        "msg" => $idiomas->_("Las fechas de los valores horarios y cuartohorarios no coinciden"));
                    return ($resultado);
                }
            }

            // Número de días de datos de tramos
            if ($max_fecha_hora_datos_tramos_utc !== NULL)
            {
                $separacion_datos_tramos = ($max_fecha_hora_datos_tramos_utc->diff($min_fecha_hora_datos_tramos_utc));
                $numero_dias_datos_tramos = $separacion_datos_tramos->days + 1;
            }

            // Máximo de consumos y costes por días (para los máximos de las gráficas)
            $max_consumo_dia = max($consumos_totales_dias);
            $max_coste_dia = max($costes_totales_dias);

            // Se calculan las medias de consumos y costes por día de la semana
            $tramos_datos_tramos_dias_semana = array_keys($datos_tramos_dias_semana);
            foreach ($tramos_datos_tramos_dias_semana as $tramo)
            {
                $datos_tramo_dias_semana = $datos_tramos_dias_semana[$tramo];
                $dias_semana_datos_tramo_dias_semana = array_keys($datos_tramo_dias_semana);
                foreach ($dias_semana_datos_tramo_dias_semana as $dia_semana)
                {
                    $datos_tramo_dia_semana = $datos_tramo_dias_semana[$dia_semana];
                    $media_consumo_dia_semana = array_sum($datos_tramo_dia_semana["consumos_dia_semana"]) / $datos_tramo_dia_semana["total_dias_consumo"];
                    $media_coste_dia_semana = array_sum($datos_tramo_dia_semana["costes_dia_semana"]) / $datos_tramo_dia_semana["total_dias_coste"];
                    $datos_tramos_dias_semana[$tramo][$dia_semana]["media_consumo_dia_semana"] = $media_consumo_dia_semana;
                    $datos_tramos_dias_semana[$tramo][$dia_semana]["media_coste_dia_semana"] = $media_coste_dia_semana;
                }
            }

            // Máximo de medias de consumos y costes por días de la semana (para los máximos de las gráficas)
            $total_medias_consumos_dias_semana = array();
            $total_medias_costes_dias_semana = array();
            for ($dia_semana = 0; $dia_semana < 7; $dia_semana++)
            {
                foreach ($datos_tramos_dias_semana as $tramo => $datos_tramo_dias_semana)
                {
                    if (array_key_exists($dia_semana, $datos_tramo_dias_semana) == true)
                    {
                        if (array_key_exists($dia_semana, $total_medias_consumos_dias_semana) == false)
                        {
                            $total_medias_consumos_dias_semana[$dia_semana] = 0;
                        }
                        $total_medias_consumos_dias_semana[$dia_semana] += $datos_tramo_dias_semana[$dia_semana]["media_consumo_dia_semana"];
                        if (array_key_exists($dia_semana, $total_medias_costes_dias_semana) == false)
                        {
                            $total_medias_costes_dias_semana[$dia_semana] = 0;
                        }
                        $total_medias_costes_dias_semana[$dia_semana] += $datos_tramo_dias_semana[$dia_semana]["media_coste_dia_semana"];
                    }
                }
            }
            $max_media_consumo_dia_semana = max($total_medias_consumos_dias_semana);
            $max_media_coste_dia_semana = max($total_medias_costes_dias_semana);

            // Se ordenan los tramos de menor a mayor y se añaden los datos para:
            // - Gráficas de barras de consumos y costes por tramo por día de la semana
            ksort($datos_tramos_dias_semana, SORT_NUMERIC);
            $grafica_medias_consumos_tramos_dias_semana = new VectorDatos();
            $grafica_medias_costes_tramos_dias_semana = new VectorDatos();
            foreach ($datos_tramos_dias_semana as $tramo => $datos_tramo_dias_semana)
            {
                $datos_consumo_tramo_dia_semana = new VectorDatos();
                $datos_coste_tramo_dia_semana = new VectorDatos();
                for ($dia_semana = 0; $dia_semana < 7; $dia_semana++)
                {
                    if (array_key_exists($dia_semana, $datos_tramo_dias_semana) == true)
                    {
                        $media_consumo_tramo_dia_semana = $datos_tramo_dias_semana[$dia_semana]["media_consumo_dia_semana"];
                        $media_coste_tramo_dia_semana = $datos_tramo_dias_semana[$dia_semana]["media_coste_dia_semana"];
                    }
                    else
                    {
                        $media_consumo_tramo_dia_semana = 0.0;
                        $media_coste_tramo_dia_semana = 0.0;
                    }
                    $datos_consumo_tramo_dia_semana->anyade_tupla_pareja_datos($dia_semana + 1, $media_consumo_tramo_dia_semana);
                    $datos_coste_tramo_dia_semana->anyade_tupla_pareja_datos($dia_semana + 1, $media_coste_tramo_dia_semana);
                }
                $grafica_medias_consumos_tramos_dias_semana->anyade_dato($datos_consumo_tramo_dia_semana->dame_datos());
                $grafica_medias_costes_tramos_dias_semana->anyade_dato($datos_coste_tramo_dia_semana->dame_datos());
            }

            // Se recuperan los datos de las gráficas si es necesario
            if (($valor == ID_TODOS) || ($valor == VALOR_CONSUMO))
            {
                $datos_grafica_medias_consumos_tramos_dias_semana = $grafica_medias_consumos_tramos_dias_semana->dame_datos();
            }
            else
            {
                $datos_grafica_medias_consumos_tramos_dias_semana = NULL;
            }
            if (($valor == ID_TODOS) || ($valor == VALOR_COSTE))
            {
                $datos_grafica_medias_costes_tramos_dias_semana = $grafica_medias_costes_tramos_dias_semana->dame_datos();
            }
            else
            {
                $datos_grafica_medias_costes_tramos_dias_semana = NULL;
            }

            // Tablas de consumos y costes por tramo
            if ($mostrar_tablas_tramos == true)
            {
                // Parámetros de tablas de consumos y costes por tramo
                $params_tabla_consumos_costes_tramos = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTES_TRAMOS,
                    "generar_valores_xml" => true
                );

                // Tabla de consumos por tramo
                $titulo_tabla_consumos_tramos = $idiomas->_("Consumos por tramo");
                $tabla_consumos_tramos = new TablaDatos(
                    "tabla-consumos-tramos-consumos-costes-tramos",
                    $titulo_tabla_consumos_tramos,
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_consumos_costes_tramos
                );
                $cabecera_tablas_consumos_tramos = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Total"),
                    $idiomas->_("Media diaria"),
                    $idiomas->_("Potencia media"),
                    $idiomas->_("Potencia máxima cuartohoraria")
                );
                $tabla_consumos_tramos->anyade_cabecera("", $cabecera_tablas_consumos_tramos);

                // Tabla de costes por tramo
                $titulo_tabla_costes_tramos = $idiomas->_("Costes por tramo");
                $tabla_costes_tramos = new TablaDatos(
                    "tabla-costes-tramos-consumos-costes-tramos",
                    $titulo_tabla_costes_tramos,
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_consumos_costes_tramos
                );
                $cabecera_tablas_costes_tramos = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Total"),
                    $idiomas->_("Media diaria"),
                    $idiomas->_("Media horaria"),
                    $idiomas->_("Máximo cuartohorario")
                );
                $tabla_costes_tramos->anyade_cabecera("", $cabecera_tablas_costes_tramos);
            }

            // Se ordenan los tramos de menor a mayor y se añaden los datos para:
            // - Gráficas de barras de consumos y costes por tramo diarios
            // - Tablas de consumos y costes por tramo diarios

            // Fechas (fecha inicial y final incluidas)
            // Nota: Es necesario establecer la hora 23:59:59 en la fecha final para que incluya la última fecha
            if ($min_fecha_datos_tramos_local !== NULL)
            {
                $min_fecha_datos_tramos_local_aux = clone $min_fecha_datos_tramos_local;
                $min_fecha_datos_tramos_local_aux->add(new DateInterval("PT12H"));
                $max_fecha_datos_tramos_local_aux = clone $max_fecha_datos_tramos_local;
                $max_fecha_datos_tramos_local_aux->setTime(23, 59, 59);
                $fechas_datos_tramos_local = new DatePeriod(
                    $min_fecha_datos_tramos_local_aux,
                    new DateInterval('P1D'),
                    $max_fecha_datos_tramos_local_aux
                );
            }

            // Se recorren los datos de los tramos
            ksort($datos_tramos, SORT_NUMERIC);
            $grafica_consumos_tramos_diarios = new VectorDatos();
            $grafica_costes_tramos_diarios = new VectorDatos();
            foreach ($datos_tramos as $tramo => $datos_tramo)
            {
                $total_consumo = $datos_tramo["total_consumo"];
                $total_coste = $datos_tramo["total_coste"];
                $media_consumo_diario = $datos_tramo["total_consumo"] / $numero_dias_datos_tramos;
                $media_coste_diario = $datos_tramo["total_coste"] / $numero_dias_datos_tramos;
                $potencia_media = $datos_tramo["total_consumo"] / $datos_tramo["total_horas"];
                $media_coste_hora = $datos_tramo["total_coste"] / $datos_tramo["total_horas"];
                $max_potencia_cuartohoraria = $datos_tramo["max_potencia_cuartohoraria"];
                $max_coste_cuartohorario = $datos_tramo["max_coste_cuartohorario"];

                if ($mostrar_tablas_tramos == true)
                {
                    $fila_consumos_tramo = array(
                        "P".$tramo,
                        formatea_numero($total_consumo, 2)." ".$unidad_medida_consumo,
                        formatea_numero($media_consumo_diario, 2)." ".$unidad_medida_consumo,
                        formatea_numero($potencia_media, 2)." ".$unidad_medida_potencia,
                        formatea_numero($max_potencia_cuartohoraria, 2)." ".$unidad_medida_potencia);
                    $params_fila_consumos_tramo = array("textos_eliminar_valores_xml" => array(
                        " ".$unidad_medida_consumo,
                        " ".$unidad_medida_potencia));
                    $tabla_consumos_tramos->anyade_fila("", $fila_consumos_tramo, $params_fila_consumos_tramo);

                    $fila_costes_tramo = array(
                        "P".$tramo,
                        formatea_numero($total_coste, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($media_coste_diario, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($media_coste_hora, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($max_coste_cuartohorario, 2, false)." ".$unidad_medida_coste);
                    $params_fila_costes_tramo = array("texto_eliminar_valores_xml" => " ".$unidad_medida_coste);
                    $tabla_costes_tramos->anyade_fila("", $fila_costes_tramo, $params_fila_costes_tramo);
                }

                // Nota: Hay que recorrer todas las fechas (entre la fecha mínima y máxima de valores) para en aquellas fechas
                // en las que no hay valores añadir un valor nulo (0.0)
                $datos_consumo_tramo_dia = new VectorDatos();
                $datos_coste_tramo_dia = new VectorDatos();
                $indice_datos_tramo_dia = 0;
                foreach ($fechas_datos_tramos_local as $fecha_datos_tramos_local)
                {
                    $cadena_fecha_datos_tramos_local_local = convierte_fecha_a_cadena($fecha_datos_tramos_local, $_SESSION["formato_fecha_local"]);
                    if ($datos_tramo["fechas"][$indice_datos_tramo_dia] == $cadena_fecha_datos_tramos_local_local)
                    {
                        $consumo_tramo_dia = $datos_tramo["consumos_diarios"][$indice_datos_tramo_dia];
                        $coste_tramo_dia = $datos_tramo["costes_diarios"][$indice_datos_tramo_dia];
                        $indice_datos_tramo_dia += 1;
                    }
                    else
                    {
                        $consumo_tramo_dia = 0.0;
                        $coste_tramo_dia = 0.0;
                    }

                    // Se establece la fecha en la mitad del día (para que se muestre correctamente en la gráfica de barras)
                    $cadena_fecha_jqplot_local = convierte_fecha_a_cadena($fecha_datos_tramos_local, FORMATO_FECHA_JQPLOT);
                    $cadena_fecha_jqplot_local .= " 12:00:00";
                    $cadena_fecha_local_local = convierte_fecha_a_cadena($fecha_datos_tramos_local, $_SESSION["formato_fecha_local"]);

                    // Se añaden los datos de la gráfica
                    $datos_consumo_tramo_dia->anyade_tupla_etiqueta_dato_etiqueta(
                        $cadena_fecha_jqplot_local,
                        $consumo_tramo_dia,
                        $cadena_fecha_local_local);
                    $datos_coste_tramo_dia->anyade_tupla_etiqueta_dato_etiqueta(
                        $cadena_fecha_jqplot_local,
                        $coste_tramo_dia,
                        $cadena_fecha_local_local);
                }
                $grafica_consumos_tramos_diarios->anyade_dato($datos_consumo_tramo_dia->dame_datos());
                $grafica_costes_tramos_diarios->anyade_dato($datos_coste_tramo_dia->dame_datos());
            }

            // Se recuperan los datos de las gráficas si es necesario
            if (($valor == ID_TODOS) || ($valor == VALOR_CONSUMO))
            {
                $datos_grafica_consumos_tramos_diarios = $grafica_consumos_tramos_diarios->dame_datos();
            }
            else
            {
                $datos_grafica_consumos_tramos_diarios = NULL;
            }
            if (($valor == ID_TODOS) || ($valor == VALOR_COSTE))
            {
                $datos_grafica_costes_tramos_diarios = $grafica_costes_tramos_diarios->dame_datos();
            }
            else
            {
                $datos_grafica_costes_tramos_diarios = NULL;
            }

            // Fila de consumos y costes totales en tablas de consumos y costes por tramo
            if ($mostrar_tablas_tramos == true)
            {
                if (count($datos_tramos) > 0)
                {
                    $total_consumo_tramos = 0;
                    $total_coste_tramos = 0;
                    $total_horas_tramos = 0;
                    $max_potencia_cuartohoraria_tramos = -INF;
                    $max_coste_cuartohorario_tramos = -INF;
                    foreach ($datos_tramos as $tramo => $datos_tramo)
                    {
                        $total_horas = $datos_tramo["total_horas"];
                        $total_consumo = $datos_tramo["total_consumo"];
                        $total_coste = $datos_tramo["total_coste"];
                        $max_potencia_cuartohoraria = $datos_tramo["max_potencia_cuartohoraria"];
                        $max_coste_cuartohorario = $datos_tramo["max_coste_cuartohorario"];

                        $total_consumo_tramos += $total_consumo;
                        $total_coste_tramos += $total_coste;
                        $total_horas_tramos += $total_horas;
                        if ($max_potencia_cuartohoraria_tramos < $max_potencia_cuartohoraria)
                        {
                            $max_potencia_cuartohoraria_tramos = $max_potencia_cuartohoraria;
                        }
                        if ($max_coste_cuartohorario_tramos < $max_coste_cuartohorario)
                        {
                            $max_coste_cuartohorario_tramos = $max_coste_cuartohorario;
                        }
                    }
                    $media_consumo_diario_tramos = $total_consumo_tramos / $numero_dias_datos_tramos;
                    $media_coste_diario_tramos = $total_coste_tramos / $numero_dias_datos_tramos;
                    $potencia_media_tramos = $total_consumo_tramos / $total_horas_tramos;
                    $media_coste_hora_tramos = $total_coste_tramos / $total_horas_tramos;

                    $fila_consumos_tramo_tramos = array(
                        $idiomas->_("Total"),
                        formatea_numero($total_consumo_tramos, 2)." ".$unidad_medida_consumo,
                        formatea_numero($media_consumo_diario_tramos, 2)." ".$unidad_medida_consumo,
                        formatea_numero($potencia_media_tramos, 2)." ".$unidad_medida_potencia,
                        formatea_numero($max_potencia_cuartohoraria_tramos, 2)." ".$unidad_medida_potencia);
                    $params_fila_consumos_tramo_tramos = array("textos_eliminar_valores_xml" => array(
                        " ".$unidad_medida_consumo,
                        " ".$unidad_medida_potencia));
                    $tabla_consumos_tramos->anyade_fila("", $fila_consumos_tramo_tramos, $params_fila_consumos_tramo_tramos);

                    $fila_costes_tramo_tramos = array(
                        $idiomas->_("Total"),
                        formatea_numero($total_coste_tramos, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($media_coste_diario_tramos, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($media_coste_hora_tramos, 2, false)." ".$unidad_medida_coste,
                        formatea_numero($max_coste_cuartohorario_tramos, 2, false)." ".$unidad_medida_coste);
                    $params_fila_costes_tramo_tramos = array("texto_eliminar_valores_xml" => " ".$unidad_medida_coste);
                    $tabla_costes_tramos->anyade_fila("", $fila_costes_tramo_tramos, $params_fila_costes_tramo_tramos);
                }

                // Se crean las tablas si es necesario
                $datos_tabla_consumos_tramos = $tabla_consumos_tramos->dame_tabla();
                $datos_tabla_costes_tramos = $tabla_costes_tramos->dame_tabla();
            }
        }

        // Fechas y horas mínima y máxima
        $cadena_min_fecha_hora_jqplot_local = NULL;
        $cadena_max_fecha_hora_jqplot_local = NULL;
        $cadena_min_fecha_hora_grafica_jqplot_local = NULL;
        $cadena_max_fecha_hora_grafica_jqplot_local = NULL;
        if (($min_fecha_hora_datos_tramos_local !== NULL) && ($max_fecha_hora_datos_tramos_local !== NULL))
        {
            $cadena_min_fecha_hora_jqplot_local = convierte_fecha_a_cadena($min_fecha_hora_datos_tramos_local, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_hora_jqplot_local = convierte_fecha_a_cadena($max_fecha_hora_datos_tramos_local, FORMATO_FECHA_HORA_JQPLOT);

            // Nota: Para la gráfica suma 1 hora (si no no se muestra la última barra)
            $min_fecha_hora_datos_tramos_grafica_local = clone $min_fecha_hora_datos_tramos_local;
            $max_fecha_hora_datos_tramos_grafica_local = clone $max_fecha_hora_datos_tramos_local;
            $max_fecha_hora_datos_tramos_grafica_local->add(new DateInterval("PT1H"));
            $cadena_min_fecha_hora_grafica_jqplot_local = convierte_fecha_a_cadena($min_fecha_hora_datos_tramos_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_hora_grafica_jqplot_local = convierte_fecha_a_cadena($max_fecha_hora_datos_tramos_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
        }

        // Fechas mínima y máxima
        $cadena_min_fecha_jqplot_local = NULL;
        $cadena_max_fecha_jqplot_local = NULL;
        $cadena_min_fecha_grafica_jqplot_local = NULL;
        $cadena_max_fecha_grafica_jqplot_local = NULL;
        if (($min_fecha_datos_tramos_local !== NULL) && ($max_fecha_datos_tramos_local !== NULL))
        {
            $cadena_min_fecha_jqplot_local = convierte_fecha_a_cadena($min_fecha_datos_tramos_local, FORMATO_FECHA_JQPLOT);
            $cadena_max_fecha_jqplot_local = convierte_fecha_a_cadena($max_fecha_datos_tramos_local, FORMATO_FECHA_JQPLOT);

            // Nota: Para la gráfica se suman 24 horas (si no no se muestra la última barra)
            $min_fecha_datos_tramos_grafica_local = clone $min_fecha_datos_tramos_local;
            $max_fecha_datos_tramos_grafica_local = clone $max_fecha_datos_tramos_local;
            $max_fecha_datos_tramos_grafica_local->add(new DateInterval("PT24H"));

            // Nota: Si cambia el horario de verano o de invierno, se suma o resta 1 hora para que se muestren todas las fechas en la gráfica
            $horario_verano_min_fecha = dame_horario_verano_fecha_hora_utc($min_fecha_datos_tramos_grafica_local, $zona_horaria);
            $horario_verano_max_fecha = dame_horario_verano_fecha_hora_utc($max_fecha_datos_tramos_grafica_local, $zona_horaria);
            if (($horario_verano_min_fecha == true) && ($horario_verano_max_fecha == false))
            {
                $max_fecha_datos_tramos_grafica_local->sub(new DateInterval("PT1H"));
            }
            if (($horario_verano_min_fecha == false) && ($horario_verano_max_fecha == true))
            {
                $max_fecha_datos_tramos_grafica_local->add(new DateInterval("PT1H"));
            }
            $cadena_min_fecha_grafica_jqplot_local = convierte_fecha_a_cadena($min_fecha_datos_tramos_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_grafica_jqplot_local = convierte_fecha_a_cadena($max_fecha_datos_tramos_grafica_local, FORMATO_FECHA_HORA_JQPLOT);
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena... hay que establecer una valor "válido"
        if ($max_consumo_hora == -INF)
        {
            $max_consumo_hora = "ND";
        }
        if ($max_coste_hora == -INF)
        {
            $max_coste_hora = "ND";
        }

        // Los valores de coste pueden ser nulos para las tarifas de cierre cuando no se haya terminado el mes
        // Se devuelve que no hay datos si se están solicitando los costes (el consumo sí se devuelve)
        if ($valores_coste_nulos and $parametros["valor"] == "coste"){
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "nombres_tramos" => $nombres_tramos,
            "min_hora" => $cadena_min_fecha_hora_jqplot_local,
            "max_hora" => $cadena_max_fecha_hora_jqplot_local,
            "min_hora_grafica" => $cadena_min_fecha_hora_grafica_jqplot_local,
            "max_hora_grafica" => $cadena_max_fecha_hora_grafica_jqplot_local,
            "grafica_consumos_tramos_horarios" => $datos_grafica_consumos_tramos_horarios,
            "grafica_costes_tramos_horarios" => $datos_grafica_costes_tramos_horarios,
            "numero_huecos_datos_consumos_costes_tramos_horarios" => $numero_huecos_datos_consumos_costes_tramos_horarios,
            "max_consumo_hora" => $max_consumo_hora,
            "max_coste_hora" => $max_coste_hora,
            "min_fecha" => $cadena_min_fecha_jqplot_local,
            "max_fecha" => $cadena_max_fecha_jqplot_local,
            "min_fecha_grafica" => $cadena_min_fecha_grafica_jqplot_local,
            "max_fecha_grafica" => $cadena_max_fecha_grafica_jqplot_local,
            "grafica_consumos_tramos_diarios" => $datos_grafica_consumos_tramos_diarios,
            "grafica_costes_tramos_diarios" => $datos_grafica_costes_tramos_diarios,
            "max_consumo_dia" => $max_consumo_dia,
            "max_coste_dia" => $max_coste_dia,
            "tabla_consumos_tramos" => $datos_tabla_consumos_tramos,
            "tabla_costes_tramos" => $datos_tabla_costes_tramos,
            "grafica_medias_consumos_tramos_dias_semana" => $datos_grafica_medias_consumos_tramos_dias_semana,
            "grafica_medias_costes_tramos_dias_semana" => $datos_grafica_medias_costes_tramos_dias_semana,
            "max_media_consumo_dia_semana" => $max_media_consumo_dia_semana,
            "max_media_coste_dia_semana" => $max_media_coste_dia_semana,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste);
        return ($resultado);
    }


    // Devuelve información de sobrepotencias (excesos de potencia) de un sensor
    function dame_sobrepotencias_sensor_electricidad($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_sobrepotencias_sensor_electricidad_Espanya($parametros);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $resultado = dame_sobrepotencias_sensor_electricidad_Espanya($parametros);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve información de excesos de energía reactiva de un sensor
    function dame_excesos_energia_reactiva_sensor_electricidad($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_excesos_energia_reactiva_sensor_electricidad_Espanya($parametros, NULL);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $resultado = dame_excesos_energia_reactiva_sensor_electricidad_Espanya($parametros, NULL);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve información de cortes de tensión de un sensor
    function dame_cortes_tension_sensor_electricidad($parametros, $filas_valores_sensor_energia_activa)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $cadena_fecha_hora_inicio_local_local = $parametros['fecha_hora_inicio'];
        $cadena_fecha_hora_fin_local_local = $parametros['fecha_hora_fin'];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
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

        // Se obtiene el sensor de energía activa asociado (si no hay se devuelve error)
        $info_sensor_energia_activa = dame_info_sensor_energia_activa_asociado_sensor_cortes_tension($id_sensor, NULL);
        if ($info_sensor_energia_activa === NULL)
        {
            $mensaje_error = $idiomas->_("No hay sensor de energía activa asociado");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        $id_sensor_energia_activa = $info_sensor_energia_activa['id'];
        $nombre_sensor_energia_activa = $info_sensor_energia_activa['nombre'];
        $granularidad_cuartohoraria_sensor_energia_activa = $info_sensor_energia_activa['granularidad_cuartohoraria'];

        // Variables
        $max_consumo = 0;
        $datos_cortes_tension = new VectorDatos();
        $datos_consumo = new VectorDatos();
        $grafica_consumos_cortes_tension = new VectorDatos();
        $nombres_grafica = new VectorDatos();
        $nombres_grafica_unidad = new VectorDatos();
        $unidades_medida = new VectorDatos();

        // Se recuperan los valores del sensor de energía activa (si es necesario)
        if ($granularidad_cuartohoraria_sensor_energia_activa == VALOR_SI)
        {
            $recuperar_valores_sensor_energia_activa = true;
            $intervalo_valores_energia_activa = INTERVALO_VALORES_CUARTOHORA;
        }
        else
        {
            if ($filas_valores_sensor_energia_activa === NULL)
            {
                $recuperar_valores_sensor_energia_activa = true;
                $intervalo_valores_energia_activa = INTERVALO_VALORES_HORAS;
            }
            else
            {
                $recuperar_valores_sensor_energia_activa = false;
            }
        }

        // Consulta de valores del sensor de energía activa
        if ($recuperar_valores_sensor_energia_activa == true)
        {
            $consulta_energia_activa = dame_consulta_valores_sensor(
                $id_sensor_energia_activa,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores_energia_activa,
                NULL,
                NULL,
                NULL,
                NULL);
            $res_energia_activa = $bd_datos->ejecuta_consulta($consulta_energia_activa);
            if ($res_energia_activa == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_energia_activa."'");
            }

            $filas_valores_sensor_energia_activa = array();
            while ($fila = $res_energia_activa->dame_siguiente_fila())
            {
                array_push($filas_valores_sensor_energia_activa, $fila);
            }
        }

        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor_energia_activa) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Datos de consumo de energía activa (por horas o tiempo real, según corresponda)
        foreach ($filas_valores_sensor_energia_activa as $fila)
        {
            // Fecha y consumo
            $cadena_fecha_hora_base_datos_utc = $fila["fecha_hora"];
            $consumo = (float) $fila[CAMPO_INCREMENTO];
            if ($consumo > $max_consumo)
            {
                $max_consumo = $consumo;
            }

            // Fechas
            $timestamp_fecha_hora_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            $cadena_fecha_hora_local_local = convierte_fecha_a_cadena($fecha_hora_local, $_SESSION["formato_fecha_hora_local_sin_segundos"]);

            // Se añade el valor a la gráfica
            $datos_consumo->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_utc,
                $consumo,
                $cadena_fecha_hora_local_local);
        }

        // Consulta de valores del sensor de cortes de tensión
        $consulta_cortes_tension = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_TIEMPO_REAL,
            NULL,
            NULL,
            NULL,
            NULL);
        $res_cortes_tension = $bd_datos->ejecuta_consulta($consulta_cortes_tension);
        if ($res_cortes_tension == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_cortes_tension."'");
        }

        // Si no hay datos no se hace nada
        if ($res_cortes_tension->dame_numero_filas() == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recorren las filas de valores de cortes de tensión
        $cadenas_fechas_cortes_tension_cortes_tension_local_local = array();
        if (count($filas_valores_sensor_energia_activa) > 0)
        {
            while ($fila = $res_cortes_tension->dame_siguiente_fila())
            {
                // Fecha y cortes de tensión
                $cadena_fecha_hora_base_datos_utc = $fila["fecha_hora"];
                $cortes_tension = $fila[CAMPO_CORTES];

                // Fechas
                $timestamp_fecha_hora_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_fecha_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $cadena_fecha_hora_local_local = convierte_fecha_a_cadena($fecha_hora_local, $_SESSION["formato_fecha_hora_local_sin_segundos"]);

                // Se añade el valor a la gráfica
                $datos_cortes_tension->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_utc,
                    $cortes_tension,
                    $cadena_fecha_hora_local_local);

                // Fechas con cortes de tensión
                if ($cortes_tension == VALOR_SI)
                {
                    array_push($cadenas_fechas_cortes_tension_cortes_tension_local_local, $cadena_fecha_hora_local_local);
                }
            }
        }

        // Tabla de cortes de tensión
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_CORTES_TENSION,
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Fecha")
        );
        $tabla_cortes_tension = new TablaDatos(
            "tabla-cortes-tension-cortes-tension",
            $idiomas->_("Cortes de tensión"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_cortes_tension->anyade_cabecera("", $cabecera_tabla);

        // Se rellena la tabla de cortes de tensión:
        // - Fecha
        $datos_fila_cortes_tension = array();
        foreach ($cadenas_fechas_cortes_tension_cortes_tension_local_local as $cadena_fecha_cortes_tension_cortes_tension_local_local)
        {
            $datos_fila_cortes_tension[0] = $cadena_fecha_cortes_tension_cortes_tension_local_local;
            $tabla_cortes_tension->anyade_fila("", $datos_fila_cortes_tension);
        }
        $texto_pie_tabla = $idiomas->_("Número de cortes de tensión").": ".count($cadenas_fechas_cortes_tension_cortes_tension_local_local);
        if ($granularidad_cuartohoraria_sensor_energia_activa == true)
        {
            $texto_pie_tabla .= " (".strtolower(dame_descripcion_granularidad(GRANULARIDAD_CUARTOHORARIA)).")";
        }
        else
        {
            $texto_pie_tabla .= " (".strtolower(dame_descripcion_granularidad(GRANULARIDAD_HORARIA)).")";
        }
        $tabla_cortes_tension->anyade_pie($texto_pie_tabla);

        // Datos de gráficas
        $grafica_consumos_cortes_tension->anyade_dato($datos_cortes_tension->dame_datos());
        $grafica_consumos_cortes_tension->anyade_dato($datos_consumo->dame_datos());

        // Etiquetas de las gráficas
        $nombres_grafica->anyade_etiqueta($idiomas->_("Cortes de tensión"));
        $nombres_grafica->anyade_etiqueta($idiomas->_("Consumo"));
        $nombres_grafica_unidad->anyade_etiqueta($idiomas->_("Cortes de tensión")." (".$nombre_sensor.")");
        $nombres_grafica_unidad->anyade_etiqueta($idiomas->_("Consumo")." (".$nombre_sensor_energia_activa.") (".$idiomas->_("kWh").")");

        // Unidades de medida
        $unidades_medida->anyade_etiqueta("");
        $unidades_medida->anyade_etiqueta($idiomas->_("kWh"));

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "grafica_consumos_cortes_tension" => $grafica_consumos_cortes_tension->dame_datos(),
            "tabla_cortes_tension" => $tabla_cortes_tension->dame_tabla(),
            "etiquetas" => $nombres_grafica->dame_datos(),
            "etiquetas_unidad" => $nombres_grafica_unidad->dame_datos(),
            "unidades_medida" => $unidades_medida->dame_datos(),
            "max_consumo" => $max_consumo);
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve la tabla de evolución de consumos por tramo
    function dame_tabla_evolucion_consumos_tramos(
        $texto_periodo,
        $unidad_medida_consumo,
        $filas_periodo_anterior,
        $filas_periodo_posterior,
        $campo_consumo,
        $claves_periodos_consumos_ambos_periodos)
    {
        $idiomas = new Idiomas();

        // Tabla de evolución de consumos por tramo
        $params_tabla_evolucion_consumos_tramos = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_TRAMOS,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_TRAMOS),
            "generar_valores_xml" => true
        );
        $titulo_tabla_evolucion_consumos_tramos = $idiomas->_("Evolución de consumos por tramo");
        if ($texto_periodo != "")
        {
            $titulo_tabla_evolucion_consumos_tramos .= " (".$texto_periodo.")";
        }
        $tabla_evolucion_consumos_tramos = new TablaDatos(
            "tabla-evolucion-consumos-tramo-comparacion-periodos",
            $titulo_tabla_evolucion_consumos_tramos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_evolucion_consumos_tramos
        );
        $cabecera_tabla_evolucion_consumos_tramos = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Total"),
            $idiomas->_("Media por día"),
            $idiomas->_("Media por hora"),
            $idiomas->_("Máximo horario")
        );
        $tabla_evolucion_consumos_tramos->anyade_cabecera("", $cabecera_tabla_evolucion_consumos_tramos);

        // Medias de tramos del periodo anterior y posterior
        $medias_tramos_anterior = dame_medias_consumo_sensor_tramos(
            $filas_periodo_anterior,
            $campo_consumo,
            "clave_periodo_adelantado",
            $claves_periodos_consumos_ambos_periodos);
        $medias_tramos_posterior = dame_medias_consumo_sensor_tramos(
            $filas_periodo_posterior,
            $campo_consumo,
            "clave_periodo",
            $claves_periodos_consumos_ambos_periodos);

        // Se obtiene un vector con todos los tramos encontrados tanto en la consulta anterior como en la posterior
        // (ya que podría haber tramos que solo estuvieran en una de las consultas)
        // (Nota: Los datos de cada tramo pueden no ser correctos ya que se habrán machacado en los casos en que el tramo esté en las dos consultas)
        $datos_tramos = array_replace($medias_tramos_anterior, $medias_tramos_posterior);
        ksort($datos_tramos, SORT_NUMERIC);
        $tramos_datos_tramos = array_keys($datos_tramos);
        foreach ($tramos_datos_tramos as $tramo)
        {
            // Comprobar que el tramo está en los dos periodos
            if (array_key_exists($tramo, $medias_tramos_anterior) == false)
            {
                $fila_evolucion_tramo = array(
                    "P".$tramo,
                    $idiomas->_("ND")." (".$idiomas->_("no hay datos en el periodo anterior").")",
                    $idiomas->_("ND"),
                    $idiomas->_("ND"),
                    $idiomas->_("ND"));
            }
            else if (array_key_exists($tramo, $medias_tramos_posterior) == false)
            {
                $fila_evolucion_tramo = array(
                    "P".$tramo,
                    $idiomas->_("ND")." (".$idiomas->_("no hay datos en el periodo posterior").")",
                    $idiomas->_("ND"),
                    $idiomas->_("ND"),
                    $idiomas->_("ND"));
            }
            else
            {
                // Diferencias de totales
                if ($medias_tramos_posterior[$tramo]["total"] == $medias_tramos_anterior[$tramo]["total"])
                {
                    $texto_diferencia_totales = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_totales = abs($medias_tramos_posterior[$tramo]["total"] - $medias_tramos_anterior[$tramo]["total"]);
                    $cadena_diferencia_totales = formatea_numero($diferencia_totales, 2);
                    $porcentaje_diferencia_totales = dame_porcentaje_valor_referencia($medias_tramos_posterior[$tramo]["total"], $medias_tramos_anterior[$tramo]["total"]);
                    $cadena_porcentaje_diferencia_totales = formatea_numero($porcentaje_diferencia_totales, 2);

                    if ($medias_tramos_posterior[$tramo]["total"] > $medias_tramos_anterior[$tramo]["total"])
                    {
                        $texto_diferencia_totales = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_totales." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_totales." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_totales = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_totales." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_totales." "."%".")";
                        $diferencia_totales *=-1;
                        $porcentaje_diferencia_totales *=-1;
                    }
                }

                // Diferencias de medias por día
                if ($medias_tramos_posterior[$tramo]["media_por_dia"] == $medias_tramos_anterior[$tramo]["media_por_dia"])
                {
                    $texto_diferencia_medias_por_dia = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_medias_por_dia = abs($medias_tramos_posterior[$tramo]["media_por_dia"] - $medias_tramos_anterior[$tramo]["media_por_dia"]);
                    $cadena_diferencia_medias_por_dia = formatea_numero($diferencia_medias_por_dia, 2);
                    $porcentaje_diferencia_medias_por_dia = dame_porcentaje_valor_referencia($medias_tramos_posterior[$tramo]["media_por_dia"], $medias_tramos_anterior[$tramo]["media_por_dia"]);
                    $cadena_porcentaje_diferencia_medias_por_dia = formatea_numero($porcentaje_diferencia_medias_por_dia, 2);

                    if ($medias_tramos_posterior[$tramo]["media_por_dia"] > $medias_tramos_anterior[$tramo]["media_por_dia"])
                    {
                        $texto_diferencia_medias_por_dia = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_medias_por_dia." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_medias_por_dia." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_medias_por_dia = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_medias_por_dia." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_medias_por_dia." "."%".")";
                        $diferencia_medias_por_dia *=-1;
                        $porcentaje_diferencia_medias_por_dia *=-1;
                    }
                }

                // Diferencias de medias por hora
                if ($medias_tramos_posterior[$tramo]["media_por_hora"] == $medias_tramos_anterior[$tramo]["media_por_hora"])
                {
                    $texto_diferencia_medias_por_hora = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_medias_por_hora = abs($medias_tramos_posterior[$tramo]["media_por_hora"] - $medias_tramos_anterior[$tramo]["media_por_hora"]);
                    $cadena_diferencia_medias_por_hora = formatea_numero($diferencia_medias_por_hora, 2);
                    $porcentaje_diferencia_medias_por_hora = dame_porcentaje_valor_referencia($medias_tramos_posterior[$tramo]["media_por_hora"], $medias_tramos_anterior[$tramo]["media_por_hora"]);
                    $cadena_porcentaje_diferencia_medias_por_hora = formatea_numero($porcentaje_diferencia_medias_por_hora, 2);

                    if ($medias_tramos_posterior[$tramo]["media_por_hora"] > $medias_tramos_anterior[$tramo]["media_por_hora"])
                    {
                        $texto_diferencia_medias_por_hora = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_medias_por_hora." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_medias_por_hora." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_medias_por_hora = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_medias_por_hora." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_medias_por_hora." "."%".")";
                        $diferencia_medias_por_hora *=-1;
                        $porcentaje_diferencia_medias_por_hora *=-1;
                    }
                }

                // Diferencias de máximos por hora
                if ($medias_tramos_posterior[$tramo]["maximo_por_hora"] == $medias_tramos_anterior[$tramo]["maximo_por_hora"])
                {
                    $texto_diferencia_maximos_por_hora = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_maximos_por_hora = abs($medias_tramos_posterior[$tramo]["maximo_por_hora"] - $medias_tramos_anterior[$tramo]["maximo_por_hora"]);
                    $cadena_diferencia_maximos_por_hora = formatea_numero($diferencia_maximos_por_hora, 2);
                    $porcentaje_diferencias_maximos_por_hora = dame_porcentaje_valor_referencia($medias_tramos_posterior[$tramo]["maximo_por_hora"], $medias_tramos_anterior[$tramo]["maximo_por_hora"]);
                    $cadena_porcentaje_diferencias_maximos_por_hora = formatea_numero($porcentaje_diferencias_maximos_por_hora, 2);

                    if ($medias_tramos_posterior[$tramo]["maximo_por_hora"] > $medias_tramos_anterior[$tramo]["maximo_por_hora"])
                    {
                        $texto_diferencia_maximos_por_hora = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_maximos_por_hora." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencias_maximos_por_hora." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_maximos_por_hora = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_maximos_por_hora." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencias_maximos_por_hora." "."%".")";
                        $diferencia_maximos_por_hora *=-1;
                        $porcentaje_diferencias_maximos_por_hora *=-1;
                    }
                }

                $fila_evolucion_tramo = array(
                    $tramo,
                    $texto_diferencia_totales,
                    $texto_diferencia_medias_por_dia,
                    $texto_diferencia_medias_por_hora,
                    $texto_diferencia_maximos_por_hora);
            }
            $tabla_evolucion_consumos_tramos->anyade_fila("", $fila_evolucion_tramo);
        }

        return ($tabla_evolucion_consumos_tramos);
    }


    // Cálculo por tramo del consumo medio por hora, consumo medio por día y consumo máximo
    function dame_medias_consumo_sensor_tramos(
        $filas_periodo,
        $campo_consumo,
        $nombre_clave_periodo,
        $claves_periodos_consumos_ambos_periodos)
    {
        $datos_tramos = array();
        $horas_totales_tramos = 0;
        foreach ($filas_periodo as $fila_periodo)
        {
            $clave_periodo = $fila_periodo[$nombre_clave_periodo];
            if (in_array($clave_periodo, $claves_periodos_consumos_ambos_periodos) == false)
            {
                continue;
            }

            $tramo = $fila_periodo["tramo"];
            $horas = $fila_periodo["horas"];
            $consumo = $fila_periodo[$campo_consumo];

            $consumo_por_hora = $consumo / $horas;
            if (array_key_exists($tramo, $datos_tramos) == false)
            {
                $datos_tramos[$tramo] = array(
                    "horas_totales" => $horas,
                    "consumo_total" => $consumo,
                    "maximo_por_hora" => $consumo_por_hora);
            }
            else
            {
                $horas_totales = $datos_tramos[$tramo]["horas_totales"] + $horas;
                $consumo_total = $datos_tramos[$tramo]["consumo_total"] + $consumo;
                $maximo_por_hora = $datos_tramos[$tramo]["maximo_por_hora"];
                if ($consumo_por_hora > $maximo_por_hora)
                {
                    $maximo_por_hora = $consumo_por_hora;
                }
                $datos_tramos[$tramo]["horas_totales"] = $horas_totales;
                $datos_tramos[$tramo]["consumo_total"] = $consumo_total;
                $datos_tramos[$tramo]["maximo_por_hora"] = $maximo_por_hora;
            }

            $horas_totales_tramos += $horas;
        }

        $medias_tramos = array();
        $dias_totales_tramos = ceil($horas_totales_tramos / 24);
        ksort($datos_tramos);
        foreach ($datos_tramos as $tramo => $datos_tramo)
        {
            $total = $datos_tramo["consumo_total"];
            $media_por_hora = $total / $horas_totales_tramos;
            $media_por_dia = $total / $dias_totales_tramos;
            $maximo_por_hora = $datos_tramo["maximo_por_hora"];

            $medias_tramos[$tramo] = array(
                "total" => $total,
                "media_por_hora" => $media_por_hora,
                "media_por_dia" => $media_por_dia,
                "maximo_por_hora" => $maximo_por_hora);
        }
        return ($medias_tramos);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_consumos_costes_tramos_electricidad()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_HORARIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_DIARIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_CONSUMOS_TRAMOS_DIAS_SEMANA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_CONSUMOS_TRAMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_HORARIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_DIARIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_COSTES_TRAMOS_DIAS_SEMANA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_COSTES_TRAMOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_cortes_tension_electricidad()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_GRAFICA_CORTES_TENSION_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TABLA_CORTES_TENSION);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_excesos_potencia_electricidad()
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_potencia_electricidad_Espanya();
                break;
            }

            case PAIS_PORTUGAL:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_potencia_electricidad_Espanya();
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_excesos_energia_reactiva_electricidad()
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya();
                break;
            }

            case PAIS_PORTUGAL:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya();
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_consumos_costes_tramos_electricidad($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_HORARIOS:
            {
                $descripcion = "Gráfica de consumos por tramo horarios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_CONSUMOS_TRAMOS_DIARIOS:
            {
                $descripcion = "Gráfica de consumos por tramo diarios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_CONSUMOS_TRAMOS_DIAS_SEMANA:
            {
                $descripcion = "Gráfica de media de consumos por tramo por día de la semana";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_CONSUMOS_TRAMOS:
            {
                $descripcion = "Tabla de consumos por tramo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_HORARIOS:
            {
                $descripcion = "Gráfica de costes por tramo horarios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_COSTES_TRAMOS_DIARIOS:
            {
                $descripcion = "Gráfica de costes por tramo diarios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_GRAFICA_MEDIAS_COSTES_TRAMOS_DIAS_SEMANA:
            {
                $descripcion = "Gráfica de media de costes por tramo por día de la semana";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TABLA_COSTES_TRAMOS:
            {
                $descripcion = "Tabla de costes por tramo";
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


    function dame_descripcion_elemento_informe_smartmeter_cortes_tension_electricidad($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_GRAFICA_CORTES_TENSION_CONSUMOS:
            {
                $descripcion = "Gráfica de cortes de tensión y consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TABLA_CORTES_TENSION:
            {
                $descripcion = "Tabla de cortes de tensión";
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


    function dame_descripcion_elemento_informe_smartmeter_excesos_potencia_electricidad($elemento_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_excesos_potencia_electricidad_Espanya($elemento_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_excesos_potencia_electricidad_Espanya($elemento_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($descripcion_elemento);
    }


    function dame_descripcion_elemento_informe_smartmeter_excesos_energia_reactiva_electricidad($elemento_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya($elemento_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_excesos_energia_reactiva_electricidad_Espanya($elemento_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($descripcion_elemento);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_consumos_costes_tramos_electricidad($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-consumos-costes-tramos'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-consumos-costes-tramos' hidden>
                        <div class='grafica100' id='grafica-consumos-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100' id='grafica-consumos-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100' id='grafica-consumos-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-tramos-consumos-costes-tramos'></div>
                        <div class='grafica100' id='grafica-costes-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100' id='grafica-costes-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100' id='grafica-costes-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-costes-tramos-consumos-costes-tramos'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Consumos'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-tramos-consumos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-tramos-consumos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-tramos-consumos-costes-tramos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Costes'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-tramos-costes'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-tramos-costes'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-costes-tramos-consumos-costes-tramos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_cortes_tension_electricidad($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-cortes-tension'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-cortes-tension' hidden>
                        <div class='grafica100' id='grafica-cortes-tension-consumos-cortes-tension'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-cortes-tension-cortes-tension'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Cortes de tensión'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-cortes-tension-cortes-tension'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CORTES_TENSION);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-cortes-tension-cortes-tension'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-cortes-tension-consumos-cortes-tension'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-cortes-tension-cortes-tension'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_excesos_potencia_electricidad($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya($tipo_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya($tipo_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya($tipo_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya($tipo_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-tramos-consumos-costes-tramos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-tramos-consumos-costes-tramos'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-tramos-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-horarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-diarios-consumos-costes-tramos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-tramos-dias-semana-consumos-costes-tramos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-tramos-consumos-costes-tramos'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-cortes-tension-consumos-cortes-tension'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-cortes-tension-cortes-tension'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-cortes-tension-consumos-cortes-tension'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-cortes-tension-cortes-tension'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_tramos_electricidad(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Parámetros a añadir para el informe
        $parametros_informe["valor"] = ID_TODOS;
        $parametros_informe["agrupacion_valores"] = ID_TODOS;
        $parametros_informe["mostrar_tablas_tramos"] = true;

        $datos_elemento = dame_consumos_costes_sensor_tramos_electricidad($parametros_informe, NULL);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_cortes_tension_electricidad(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_cortes_tension_sensor_electricidad($parametros_informe, NULL);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_potencia_electricidad_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }

            case PAIS_PORTUGAL:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }

            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($datos_elemento);
    }
?>
