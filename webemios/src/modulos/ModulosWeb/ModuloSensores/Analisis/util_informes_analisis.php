<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_estadisticas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/util_analisis_informes_fichero.php');


    //
    // Funciones de información de análisis
    //


    // Devuelve la información de análisis horario de valores de un sensor
    function dame_analisis_horario_valores_sensor($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
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

        // Se recupera el valor del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                INTERVALO_VALORES_HORA,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Variables
        $datos_valores = new VectorDatos();
        $grafica_valores = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $numero_valores = 0;
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $suma_valores = 0;
        $sumas_valores_horas_dia = array();

        // Características de clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];

        // Tabla de datos
        $tabla_datos = dame_nombre_tabla_datos_clase_sensor($clase_sensor).SUFIJO_TABLA_HORAS;

        // Valor para la consulta de valores
        $res_valor_consulta = dame_valor_consulta_campo_clase_sensor($clase_sensor, $campo, $parametros_extra_campo);
        $campo_valor = $res_valor_consulta["campo_valor"];
        $valor_consulta = $res_valor_consulta["valor_consulta"];

        // Consulta de valores del sensor
        $consulta_valores_sensor = "
            SELECT
                hora AS fecha_hora,
                HOUR(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS hora_dia,
                (".$valor_consulta.") AS valor
            FROM ".$tabla_datos."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (".$bd_datos->_($campo_valor)." IS NOT NULL)
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_valores_sensor .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se añade el orden y se ejecuta la consulta
        $consulta_valores_sensor .= "
            ORDER BY hora ASC";
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        if ($clase_procesado_valores == true)
        {
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
        }
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren los valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor["fecha_hora"];
            $hora_dia = $fila_valor_sensor["hora_dia"];
            $valor = $fila_valor_sensor["valor"];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Fecha y valor mínimo y máximo
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor
            $datos_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $valor);

            // Número de valores
            $numero_valores += 1;

            // Datos del mapa de calor de valores
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc, $minutos_desfase_utc);
            $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);

            // Sumas de valores
            if (array_key_exists($hora_dia, $sumas_valores_horas_dia) == false)
            {
                $sumas_valores_horas_dia[$hora_dia] = 0;
            }
            $suma_valores += $valor;
            $sumas_valores_horas_dia[$hora_dia] += $valor;
        }

        // Si no hay datos no se hace nada
        if ($numero_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se añaden los datos de la gráfica de valores
        $grafica_valores->anyade_dato($datos_valores->dame_datos());

        // Etiquetas de gráfica de valores
        $etiquetas_grafica_valores = new VectorDatos();
        $etiquetas_grafica_valores->anyade_etiqueta($nombre_sensor);

        // Porcentajes de valores por horas
        // (http://php.net/manual/es/array.sorting.php)
        $porcentajes_valores_horas_dia = array();
        foreach ($sumas_valores_horas_dia as $hora_dia => $suma_valores_hora_dia)
        {
            $porcentajes_valores_horas_dia[$hora_dia] = ($suma_valores_hora_dia / $suma_valores) * 100;
        }
        uasort($porcentajes_valores_horas_dia, function($a, $b)
        {
            if ($a == $b)
            {
                $res = 0;
            }
            elseif ($a < $b)
            {
                $res = 1;
            }
            else
            {
                $res = -1;
            }
            return ($res);
        });

        // Datos de porcentajes de valores por horas
        $grafica_porcentajes_valores = new VectorDatos();
        $etiquetas_porcentajes_valores = new VectorDatos();
        $max_porcentaje_valores = 0;
        $porcentajes = array();
        foreach ($porcentajes_valores_horas_dia as $hora_dia => $porcentaje_valores_hora_dia)
        {
            if ($porcentaje_valores_hora_dia > $max_porcentaje_valores)
            {
                $max_porcentaje_valores = $porcentaje_valores_hora_dia;
            }
            $etiquetas_porcentajes_valores->anyade_etiqueta($hora_dia." ".$idiomas->_("H"));
            $grafica_porcentajes_valores->anyade_dato($porcentaje_valores_hora_dia);
            array_push($porcentajes, (float) $porcentaje_valores_hora_dia);
        }

        // Cálculo de curva de Lorenz de valores
        $valores_lorenz = array();
        $grafica_lorenz_valores = new VectorDatos();
        $valor_inicial_lorenz = array(0.0, 0.0);
        $grafica_lorenz_valores->anyade_tupla_pareja_datos($valor_inicial_lorenz[0], $valor_inicial_lorenz[1]);
        $porcentaje_anterior = 0.0;
        $indice_valor_lorenz = 1;
        sort($porcentajes_valores_horas_dia);
        foreach ($porcentajes_valores_horas_dia as $porcentaje_valores_hora_dia)
        {
            $valor_lorenz = array(100.0 * $indice_valor_lorenz / count($porcentajes), $porcentaje_valores_hora_dia + $porcentaje_anterior);
            array_push($valores_lorenz, $valor_lorenz);
            $grafica_lorenz_valores->anyade_tupla_pareja_datos($valor_lorenz[0], $valor_lorenz[1]);
            $porcentaje_anterior += (float) $porcentaje_valores_hora_dia;
            $indice_valor_lorenz++;
        }

        // Cálculo de percentiles
        $quintiles = array(50, 70, 80, 90);
        $percentiles = array();
        foreach ($quintiles as &$quintil)
        {
            array_unshift($percentiles, formatea_numero((100.0 - dame_valor_quintil($quintil, $valores_lorenz)), 2)." "."%");
        }

        // Tabla de percentiles de valores
        $params_tabla_percentiles_valores = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_PERCENTILES_VALORES_ANALISIS_HORARIO,
            "generar_valores_xml" => true
        );
        $titulo_tabla_percentiles_valores = $idiomas->_("Valores por percentiles");
        $tabla_percentiles_valores = new TablaDatos(
            "tabla-percentiles-valores-analisis-horario",
            $titulo_tabla_percentiles_valores,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_percentiles_valores
        );
        $cabecera_tabla_percentiles_valores = array(
            "10 "."%"." ".$idiomas->_("horas"),
            "25 "."%"." ".$idiomas->_("horas"),
            "50 "."%"." ".$idiomas->_("horas"),
            "75 "."%"." ".$idiomas->_("horas"));
        $tabla_percentiles_valores->anyade_cabecera("", $cabecera_tabla_percentiles_valores);

        // Fila de la tabla de percentiles
        $params_fila_percentiles = array("texto_eliminar_valores_xml" => " %");
        $tabla_percentiles_valores->anyade_fila("", array(
            $percentiles[0],
            $percentiles[1],
            $percentiles[2],
            $percentiles[3]),
            $params_fila_percentiles);

        // Comprobación de valores mínimo y máximo
        if ($numero_valores == 0)
        {
            // Nota: Los valores -INF y INF no se convierten correctamente a cadena... hay que establecer una valor "válido"
            $min_valor = "ND";
            $max_valor = "ND";
        }

        // Unidad de medida
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Resultado del análisis horario
        $resultado_analisis_horario = array(
            "res" => "OK",
            "hay_datos" => true,
            "etiquetas_grafica_valores" => $etiquetas_grafica_valores->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "clase_procesado_valores" => $clase_procesado_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "etiquetas_porcentajes_valores" => $etiquetas_porcentajes_valores->dame_datos(),
            "grafica_porcentajes_valores" => $grafica_porcentajes_valores->dame_datos(),
            "max_porcentaje_valores" => $max_porcentaje_valores,
            "grafica_lorenz_valores" => $grafica_lorenz_valores->dame_datos(),
            "tabla_percentiles_valores" => $tabla_percentiles_valores->dame_tabla(),
            "unidad_medida" => $unidad_medida);

        // Se recupera y añade la información de las medias de los valores del sensor
        $parametros["agrupacion_valores"] = AGRUPACION_VALORES_HORA;
        $resultado_medias_valores_sensor = dame_medias_valores_sensor($parametros);
        $resultado = array_merge($resultado_analisis_horario, $resultado_medias_valores_sensor);

        // Se devuelve el resutlado
        return ($resultado);
    }


    // Devuelve la información de análisis diario de valores de un sensor
    function dame_analisis_diario_valores_sensor($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
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

        // Se recupera el valor del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                INTERVALO_VALORES_HORA,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Variables
        $dias_valores = array();
        $datos_valores = new VectorDatos();
        $grafica_valores = new VectorDatos();
        $grafica_sumas_valores = new VectorDatos();
        $grafica_valores_medias_maximos_minimos = new VectorDatos();
        $nombres_grafica_valores_medias_maximos_minimos = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $numero_valores = 0;
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);

        $max_valor_dia = (float) -INF;
        $min_valor_dia = (float) INF;

        $datos_sumas_valores_diarios = array();
        $datos_valores_maximos_diarios = array();
        $datos_valores_minimos_diarios = array();

        $datos_sumas_valores = new VectorDatos();
        $datos_medias_valores = new VectorDatos();
        $datos_valores_maximos = new VectorDatos();
        $datos_valores_minimos = new VectorDatos();

        // Tipo de valores del campo de sensor
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Características de clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];

        // Tabla de datos
        $tabla_datos = dame_nombre_tabla_datos_clase_sensor($clase_sensor).SUFIJO_TABLA_HORAS;

        // Valor para la consulta de valores
        $res_valor_consulta = dame_valor_consulta_campo_clase_sensor($clase_sensor, $campo, $parametros_extra_campo);
        $campo_valor = $res_valor_consulta["campo_valor"];
        $valor_consulta = $res_valor_consulta["valor_consulta"];

        // Consulta de valores del sensor
        $consulta_valores_sensor = "
            SELECT
                hora AS fecha_hora,
                (".$valor_consulta.") AS valor
            FROM ".$tabla_datos."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (".$bd_datos->_($campo_valor)." IS NOT NULL)
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_valores_sensor .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se añade el orden y se ejecuta la consulta
        $consulta_valores_sensor .= "
            ORDER BY hora ASC";
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, $id_sensor);

        // Se recorren los valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor["fecha_hora"];
            $valor = $fila_valor_sensor["valor"];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Fechas y valor mínimo y máximo
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc, $minutos_desfase_utc);
            $cadena_fecha_local_local = convierte_fecha_a_cadena($fecha_hora_local, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_jqplot_local = convierte_fecha_a_cadena($fecha_hora_local, FORMATO_FECHA_JQPLOT);
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor y se incrementa el número de valores
            $datos_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $valor);
            $numero_valores += 1;

            // Valores máximos y mínimos por día
            if (in_array($cadena_fecha_local_local, $dias_valores) == false)
            {
                array_push($dias_valores, $cadena_fecha_local_local);
                $max_valor_dia = (float) -INF;
                $min_valor_dia = (float) INF;
            }

            // Medias de valores
            if (array_key_exists($cadena_fecha_local_local, $datos_sumas_valores_diarios) == false)
            {
                $datos_sumas_valores_diarios[$cadena_fecha_local_local]["suma_valores"] = $valor;
                $datos_sumas_valores_diarios[$cadena_fecha_local_local]["horas"] = 1;
                $datos_sumas_valores_diarios[$cadena_fecha_local_local]["cadena_fecha_jqplot_local"] = $cadena_fecha_jqplot_local;
            }
            else
            {
                $datos_sumas_valores_diarios[$cadena_fecha_local_local]["suma_valores"] += $valor;
                $datos_sumas_valores_diarios[$cadena_fecha_local_local]["horas"] += 1;
            }

            // Valores máximos y mínimos
            if ($valor > $max_valor_dia)
            {
                $max_valor_dia = $valor;
                $datos_valores_maximos_diarios[$cadena_fecha_local_local] = array(
                    "max_valor" => $max_valor_dia,
                    "cadena_fecha_hora_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                    "cadena_fecha_jqplot_local" => $cadena_fecha_jqplot_local);
            }
            if ($valor < $min_valor_dia)
            {
                $min_valor_dia = $valor;
                $datos_valores_minimos_diarios[$cadena_fecha_local_local] = array(
                    "min_valor" => $min_valor_dia,
                    "cadena_fecha_hora_base_datos_utc" => $cadena_fecha_hora_base_datos_utc,
                    "cadena_fecha_jqplot_local" => $cadena_fecha_jqplot_local);
            }

            // Datos del mapa de calor de valores
            $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
        }

        // Si no hay datos no se hace nada
        if ($numero_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se añaden los datos de la gráfica de valores
        $grafica_valores->anyade_dato($datos_valores->dame_datos());

        // Etiquetas de gráfica de valores
        $etiquetas_grafica_valores = new VectorDatos();
        $etiquetas_grafica_valores->anyade_etiqueta($nombre_sensor);

        // Sumas de valores
        if ($campo_incremental == true)
        {
            $max_suma_valores = 0.0;
            $contador_dias_sumas_valores = 0;
            $mayor_suma_valores = (float) -INF;
            $fecha_hora_mayor_suma_valores = NULL;
            $menor_suma_valores = (float) INF;
            $fecha_hora_menor_suma_valores = NULL;
            $media_sumas_valores = 0.0;
            $fecha_anterior_sumas_valores = NULL;
            foreach ($datos_sumas_valores_diarios as $dia => $datos)
            {
                // Fecha y suma de valores máxima y mínima
                $cadena_fecha_jqplot_local = $datos["cadena_fecha_jqplot_local"];
                $suma_valores = $datos["suma_valores"];
                if ($suma_valores > $max_suma_valores)
                {
                    $max_suma_valores = $suma_valores;
                }
                if ($suma_valores > $mayor_suma_valores)
                {
                    $mayor_suma_valores = $suma_valores;
                    $fecha_hora_mayor_suma_valores = $dia;
                }
                if ($suma_valores < $menor_suma_valores)
                {
                    $menor_suma_valores = $suma_valores;
                    $fecha_hora_menor_suma_valores = $dia;
                }

                // Adición de valor nulo si hay 'huecos' entre fechas
                $fecha_sumas_valores = convierte_cadena_a_fecha($cadena_fecha_jqplot_local, FORMATO_FECHA_JQPLOT, ZONA_HORARIA_UTC);
                $fecha_sumas_valores->setTime(0, 0, 0);
                if ($fecha_anterior_sumas_valores !== NULL)
                {
                    $dias_entre_fechas = $fecha_sumas_valores->diff($fecha_anterior_sumas_valores)->days;
                    if ($dias_entre_fechas > 1)
                    {
                        $fecha_anterior_sumas_valores->modify('+1 day');
                        $cadena_fecha_anterior_sumas_valores = convierte_fecha_a_cadena($fecha_anterior_sumas_valores, FORMATO_FECHA_BASE_DATOS);
                        $datos_sumas_valores->anyade_tupla_etiqueta_dato($cadena_fecha_anterior_sumas_valores, NULL);
                    }
                }
                $fecha_anterior_sumas_valores = $fecha_sumas_valores;

                // Se añade la suma de valores
                $datos_sumas_valores->anyade_tupla_etiqueta_dato($cadena_fecha_jqplot_local, $suma_valores);

                // Media y contador de días
                $media_sumas_valores += $suma_valores;
                $contador_dias_sumas_valores += 1;
            }
            if ($contador_dias_sumas_valores > 0)
            {
                $media_sumas_valores /= $contador_dias_sumas_valores;
            }
        }
        $grafica_sumas_valores->anyade_dato($datos_sumas_valores->dame_datos());

        // Cálculo de medias de valores
        $contador_dias_medias_valores = 0;
        $mayor_media_valores = (float) -INF;
        $fecha_hora_mayor_media_valores = NULL;
        $menor_media_valores = (float) INF;
        $fecha_hora_menor_media_valores = NULL;
        $media_medias_valores = 0.0;
        $fecha_anterior_medias_valores = NULL;
        foreach ($datos_sumas_valores_diarios as $dia => $datos)
        {
            // Fecha y media de valores máxima y mínima
            $cadena_fecha_jqplot_local = $datos["cadena_fecha_jqplot_local"];
            $media_valores = $datos["suma_valores"] / $datos["horas"];
            $datos_medias_valores->anyade_tupla_etiqueta_dato($cadena_fecha_jqplot_local, $media_valores);
            if ($media_valores > $mayor_media_valores)
            {
                $mayor_media_valores = $media_valores;
                $fecha_hora_mayor_media_valores = $dia;
            }
            if ($media_valores < $menor_media_valores)
            {
                $menor_media_valores = $media_valores;
                $fecha_hora_menor_media_valores = $dia;
            }

            // Adición de valor nulo si hay 'huecos' entre fechas
            $fecha_medias_valores = convierte_cadena_a_fecha($cadena_fecha_jqplot_local, FORMATO_FECHA_JQPLOT, ZONA_HORARIA_UTC);
            $fecha_medias_valores->setTime(0, 0, 0);
            if ($fecha_anterior_medias_valores !== NULL)
            {
                $dias_entre_fechas = $fecha_medias_valores->diff($fecha_anterior_medias_valores)->days;
                if ($dias_entre_fechas > 1)
                {
                    $fecha_anterior_medias_valores->modify('+1 day');
                    $cadena_fecha_anterior_medias_valores = convierte_fecha_a_cadena($fecha_anterior_medias_valores, FORMATO_FECHA_BASE_DATOS);
                    $datos_medias_valores->anyade_tupla_etiqueta_dato($cadena_fecha_anterior_medias_valores, NULL);
                }
            }
            $fecha_anterior_medias_valores = $fecha_medias_valores;

            // Se añade la media de valores
            $datos_medias_valores->anyade_tupla_etiqueta_dato($cadena_fecha_jqplot_local, $media_valores);

            // Media y contador de días
            $media_medias_valores += $media_valores;
            $contador_dias_medias_valores += 1;
        }
        if ($contador_dias_medias_valores > 0)
        {
            $media_medias_valores /= $contador_dias_medias_valores;
        }
        $grafica_valores_medias_maximos_minimos->anyade_dato($datos_medias_valores->dame_datos());
        $nombres_grafica_valores_medias_maximos_minimos->anyade_etiqueta($idiomas->_("Media por hora"));

        // Cálculo de valores máximos
        $contador_dias_valores_maximos = 0;
        $mayor_valor_maximo = (float) -INF;
        $cadena_fecha_hora_mayor_valor_maximo_base_datos_utc = NULL;
        $menor_valor_maximo = (float) INF;
        $cadena_fecha_hora_menor_valor_maximo_base_datos_utc = NULL;
        $media_valores_maximos = 0.0;
        $fecha_anterior_valores_maximos = NULL;
        foreach ($datos_valores_maximos_diarios as $dia => $datos)
        {
            // Fecha y valor máximo máximo y mínimo
            // (Nota: La variable se llama 'max_valor_aux' porque ya hay una variable 'max_valor')
            $cadena_fecha_jqplot_local = $datos["cadena_fecha_jqplot_local"];
            $max_valor_aux = $datos["max_valor"];
            if ($max_valor_aux > $mayor_valor_maximo)
            {
                $mayor_valor_maximo = $max_valor_aux;
                $cadena_fecha_hora_mayor_valor_maximo_base_datos_utc = $datos["cadena_fecha_hora_base_datos_utc"];
            }
            if ($max_valor_aux < $menor_valor_maximo)
            {
                $menor_valor_maximo = $max_valor_aux;
                $cadena_fecha_hora_menor_valor_maximo_base_datos_utc = $datos["cadena_fecha_hora_base_datos_utc"];
            }

            // Adición de valor nulo si hay 'huecos' entre fechas
            $fecha_valores_maximos = convierte_cadena_a_fecha($cadena_fecha_jqplot_local, FORMATO_FECHA_JQPLOT, ZONA_HORARIA_UTC);
            $fecha_valores_maximos->setTime(0, 0, 0);
            if ($fecha_anterior_valores_maximos !== NULL)
            {
                $dias_entre_fechas = $fecha_valores_maximos->diff($fecha_anterior_valores_maximos)->days;
                if ($dias_entre_fechas > 1)
                {
                    $fecha_anterior_valores_maximos->modify('+1 day');
                    $cadena_fecha_anterior_valores_maximos = convierte_fecha_a_cadena($fecha_anterior_valores_maximos, FORMATO_FECHA_BASE_DATOS);
                    $datos_valores_maximos->anyade_tupla_etiqueta_dato($cadena_fecha_anterior_valores_maximos, NULL);
                }
            }
            $fecha_anterior_valores_maximos = $fecha_valores_maximos;

            // Se añade el valor máximo
            $datos_valores_maximos->anyade_tupla_etiqueta_dato($cadena_fecha_jqplot_local, $max_valor_aux);

            // Media y contador de días
            $media_valores_maximos += $max_valor_aux;
            $contador_dias_valores_maximos += 1;
        }
        if ($contador_dias_valores_maximos > 0)
        {
            $media_valores_maximos /= $contador_dias_valores_maximos;
        }
        $grafica_valores_medias_maximos_minimos->anyade_dato($datos_valores_maximos->dame_datos());
        $nombres_grafica_valores_medias_maximos_minimos->anyade_etiqueta($idiomas->_("Máximo por hora"));

        // Cálculo de valores mínimos
        $contador_dias_valores_minimos = 0;
        $mayor_valor_minimo = (float) -INF;
        $cadena_fecha_hora_mayor_valor_minimo_base_datos_utc = NULL;
        $menor_valor_minimo = (float) INF;
        $cadena_fecha_hora_menor_valor_minimo_base_datos_utc = NULL;
        $media_valores_minimos = 0.0;
        $fecha_anterior_valores_minimos = NULL;
        foreach ($datos_valores_minimos_diarios as $dia => $datos)
        {
            // Fecha y valor mínimo máximo y mínimo
            $cadena_fecha_jqplot_local = $datos["cadena_fecha_jqplot_local"];
            $min_valor_aux = $datos["min_valor"];
            if ($min_valor_aux > $mayor_valor_minimo)
            {
                $mayor_valor_minimo = $min_valor_aux;
                $cadena_fecha_hora_mayor_valor_minimo_base_datos_utc = $datos["cadena_fecha_hora_base_datos_utc"];
            }
            if ($min_valor_aux < $menor_valor_minimo)
            {
                $menor_valor_minimo = $min_valor_aux;
                $cadena_fecha_hora_menor_valor_minimo_base_datos_utc = $datos["cadena_fecha_hora_base_datos_utc"];
            }

            // Adición de valor nulo si hay 'huecos' entre fechas
            $fecha_valores_minimos = convierte_cadena_a_fecha($cadena_fecha_jqplot_local, FORMATO_FECHA_JQPLOT, ZONA_HORARIA_UTC);
            $fecha_valores_minimos->setTime(0, 0, 0);
            if ($fecha_anterior_valores_minimos !== NULL)
            {
                $dias_entre_fechas = $fecha_valores_minimos->diff($fecha_anterior_valores_minimos)->days;
                if ($dias_entre_fechas > 1)
                {
                    $fecha_anterior_valores_minimos->modify('+1 day');
                    $cadena_fecha_anterior_valores_minimos = convierte_fecha_a_cadena($fecha_anterior_valores_minimos, FORMATO_FECHA_BASE_DATOS);
                    $datos_valores_minimos->anyade_tupla_etiqueta_dato($cadena_fecha_anterior_valores_minimos, NULL);
                }
            }
            $fecha_anterior_valores_minimos = $fecha_valores_minimos;

            // Se añade el valor
            $datos_valores_minimos->anyade_tupla_etiqueta_dato($cadena_fecha_jqplot_local, $min_valor_aux);

            // Media y contador de días
            $media_valores_minimos += $datos["min_valor"];
            $contador_dias_valores_minimos += 1;
        }
        if ($contador_dias_valores_minimos > 0)
        {
            $media_valores_minimos /= $contador_dias_valores_minimos;
        }
        $grafica_valores_medias_maximos_minimos->anyade_dato($datos_valores_minimos->dame_datos());
        $nombres_grafica_valores_medias_maximos_minimos->anyade_etiqueta($idiomas->_("Mínimo por hora"));

        // Campo y unidad de medida
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Tabla de máximos, mínimos y medias de medidas de valores por día
        $params_tabla_maximos_minimos_medias_medidas = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS_ANALISIS_DIARIO,
            "generar_valores_xml" => true
        );
        $titulo_tabla_maximos_minimos_medias_medidas = $idiomas->_("Máximos, mínimos y medias de medidas diarias");
        $tabla_maximos_minimos_medias_medidas = new TablaDatos(
            "tabla-maximos-minimos-medias-medidas-analisis-diario",
            $titulo_tabla_maximos_minimos_medias_medidas,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_maximos_minimos_medias_medidas
        );
        $cabecera_tabla_maximos_minimos_medias_medidas = array(
            $idiomas->_("Medida"),
            $idiomas->_("Máximo"),
            $idiomas->_("Mínimo"),
            $idiomas->_("Media")
        );
        $tabla_maximos_minimos_medias_medidas->anyade_cabecera("", $cabecera_tabla_maximos_minimos_medias_medidas);

        // Se rellena la tabla de máximos, mínimos y medias de medidas de valores por día
        if ($campo_incremental == true)
        {
            $datos_fila_sumas_valores_diarios = array(
                $idiomas->_("Total diario"),
                formatea_numero($mayor_suma_valores, 2).$cadena_unidad_medida." (".$fecha_hora_mayor_suma_valores.")",
                formatea_numero($menor_suma_valores, 2).$cadena_unidad_medida." (".$fecha_hora_menor_suma_valores.")",
                formatea_numero($media_sumas_valores, 2).$cadena_unidad_medida);
            $params_fila_sumas_valores_diarios = array(
                "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$fecha_hora_mayor_suma_valores.")",
                "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$fecha_hora_menor_suma_valores.")",
                "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
            $tabla_maximos_minimos_medias_medidas->anyade_fila("", $datos_fila_sumas_valores_diarios, $params_fila_sumas_valores_diarios);
        }
        $datos_fila_medias_valores_diarios = array(
            $idiomas->_("Media por hora"),
            formatea_numero($mayor_media_valores, 2).$cadena_unidad_medida." (".$fecha_hora_mayor_media_valores.")",
            formatea_numero($menor_media_valores, 2).$cadena_unidad_medida." (".$fecha_hora_menor_media_valores.")",
            formatea_numero($media_medias_valores, 2).$cadena_unidad_medida);
        $params_fila_medias_valores_diarios = array(
            "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".$fecha_hora_mayor_media_valores.")",
            "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".$fecha_hora_menor_media_valores.")",
            "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
        $tabla_maximos_minimos_medias_medidas->anyade_fila("", $datos_fila_medias_valores_diarios, $params_fila_medias_valores_diarios);
        $cadena_fecha_hora_mayor_valor_maximo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_mayor_valor_maximo_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        $cadena_fecha_hora_menor_valor_maximo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_menor_valor_maximo_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        $datos_fila_valores_maximos_diarios = array(
            $idiomas->_("Máximo por hora"),
            formatea_numero($mayor_valor_maximo, 2).$cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_mayor_valor_maximo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            formatea_numero($menor_valor_maximo, 2).$cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_menor_valor_maximo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            formatea_numero($media_valores_maximos, 2).$cadena_unidad_medida);
        $params_fila_valores_maximos_diarios = array(
            "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_mayor_valor_maximo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_menor_valor_maximo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
        $tabla_maximos_minimos_medias_medidas->anyade_fila("", $datos_fila_valores_maximos_diarios, $params_fila_valores_maximos_diarios);
        $cadena_fecha_hora_mayor_valor_minimo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_mayor_valor_minimo_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        $cadena_fecha_hora_menor_valor_minimo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_menor_valor_minimo_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        $datos_fila_valores_minimos_diarios = array(
            $idiomas->_("Mínimo por hora"),
            formatea_numero($mayor_valor_minimo, 2).$cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_mayor_valor_minimo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            formatea_numero($menor_valor_minimo, 2).$cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_menor_valor_minimo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            formatea_numero($media_valores_minimos, 2).$cadena_unidad_medida);
        $params_fila_valores_minimos_diarios = array(
            "texto_eliminar_valor_xml_1" => $cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_mayor_valor_minimo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            "texto_eliminar_valor_xml_2" => $cadena_unidad_medida." (".convierte_formato_fecha($cadena_fecha_hora_menor_valor_minimo_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]).")",
            "texto_eliminar_valor_xml_3" => $cadena_unidad_medida);
        $tabla_maximos_minimos_medias_medidas->anyade_fila("", $datos_fila_valores_minimos_diarios, $params_fila_valores_minimos_diarios);

        // Tabla de valores por día (total, media, máximo, mínimo)
        switch ($tipo_valores_campo)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $numero_columnas_tablas_valores_dia = NUMERO_COLUMNAS_TABLA_VALORES_DIA_PUNTUALES_ANALISIS_DIARIO;
                $cabecera_tabla_valores_dia = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Media"),
                    $idiomas->_("Máximo"),
                    $idiomas->_("Mínimo")
                );
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                $numero_columnas_tablas_valores_dia = NUMERO_COLUMNAS_TABLA_VALORES_DIA_INCREMENTALES_ANALISIS_DIARIO;
                $cabecera_tabla_valores_dia = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Total diario"),
                    $idiomas->_("Media por hora"),
                    $idiomas->_("Máximo por hora"),
                    $idiomas->_("Mínimo por hora")
                );
                break;
            }
        }
        $params_tabla_valores_dia = array(
            "numero_columnas" => $numero_columnas_tablas_valores_dia,
            "generar_valores_xml" => true
        );
        $titulo_tabla_valores_dia = $idiomas->_("Valores diarios");
        $tabla_valores_dia = new TablaDatos(
            "tabla-valores-dia-analisis-diario",
            $titulo_tabla_valores_dia,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_valores_dia
        );

        $tabla_valores_dia->anyade_cabecera("", $cabecera_tabla_valores_dia);
        $pie_tabla_valores_dia = $idiomas->_("Días").": ".count($dias_valores);

        // Se rellena la tabla de valores por día
        $suma_valores_dias = 0;
        foreach ($dias_valores as $dia_valores)
        {
            $suma_valores_dia = $datos_sumas_valores_diarios[$dia_valores]["suma_valores"];
            $media_valores_dia = $suma_valores_dia / $datos_sumas_valores_diarios[$dia_valores]["horas"];
            $max_valor_dia = $datos_valores_maximos_diarios[$dia_valores]["max_valor"];
            $min_valor_dia = $datos_valores_minimos_diarios[$dia_valores]["min_valor"];
            switch ($tipo_valores_campo)
            {
                case TIPO_VALORES_SENSOR_PUNTUALES:
                {
                    $datos_valores_dia = array(
                        $dia_valores,
                        formatea_numero($media_valores_dia, 2).$cadena_unidad_medida,
                        formatea_numero($max_valor_dia, 2).$cadena_unidad_medida,
                        formatea_numero($min_valor_dia, 2).$cadena_unidad_medida);
                    break;
                }
                case TIPO_VALORES_SENSOR_INCREMENTALES:
                {
                    $datos_valores_dia = array(
                        $dia_valores,
                        formatea_numero($suma_valores_dia, 2).$cadena_unidad_medida,
                        formatea_numero($media_valores_dia, 2).$cadena_unidad_medida,
                        formatea_numero($max_valor_dia, 2).$cadena_unidad_medida,
                        formatea_numero($min_valor_dia, 2).$cadena_unidad_medida);
                    $suma_valores_dias += $suma_valores_dia;
                    break;
                }
            }
            $params_fila_valores_dia = array("texto_eliminar_valores_xml" => $cadena_unidad_medida);
            $tabla_valores_dia->anyade_fila("", $datos_valores_dia, $params_fila_valores_dia);
        }

        // Pie de tabla (con suma de totales si corresponde)
        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
        {
            $pie_tabla_valores_dia .= " (".$idiomas->_("total").": ".
                formatea_numero($suma_valores_dias, 2).$cadena_unidad_medida.")";
        }
        $tabla_valores_dia->anyade_pie($pie_tabla_valores_dia);

        // Unidad de medida
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
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

        // Resultado del análisis diario
        $resultado_analisis_diario = array(
            "res" => "OK",
            "hay_datos" => true,
            "etiquetas_grafica_valores" => $etiquetas_grafica_valores->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "clase_procesado_valores" => $clase_procesado_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_sumas_valores" => $grafica_sumas_valores->dame_datos(),
            "max_suma_valores" => $max_suma_valores,
            "etiquetas_grafica_valores_medias_maximos_minimos" => $nombres_grafica_valores_medias_maximos_minimos->dame_datos(),
            "grafica_valores_medias_maximos_minimos" => $grafica_valores_medias_maximos_minimos->dame_datos(),
            "tabla_maximos_minimos_medias_medidas" => $tabla_maximos_minimos_medias_medidas->dame_tabla(),
            "tabla_valores_dia" => $tabla_valores_dia->dame_tabla(),
            "unidad_medida" => $unidad_medida);

        // Se recupera y añade la información de las medias de los valores del sensor
        $parametros["agrupacion_valores"] = AGRUPACION_VALORES_DIA_SEMANA;
        $resultado_medias_valores_sensor = dame_medias_valores_sensor($parametros);
        $resultado = array_merge($resultado_analisis_diario, $resultado_medias_valores_sensor);

        // Se devuelve el resultado
        return ($resultado);
    }


    // Devuelve la información de análisis de comportamiento de valores de sensores
    function dame_analisis_comportamiento_valores_sensores($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Se recupera el valor del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Variables
        $hay_datos = false;
        $grafica_coeficientes_estabilidad_valores = new VectorDatos();
        $grafica_amplitudes_valores = new VectorDatos();
        $grafica_alturas_relativas_valores_maximos = new VectorDatos();
        $etiquetas_graficas_valores = new VectorDatos();
        $max_amplitud_valores = 0.0;
        $max_altura_relativa_valores_maximos = 0.0;

        // Filtro de consulta de  horario semanal y la exclusión e inclusión de fechas
        $filtro_consulta_horario_semanal_fechas = dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Se recupera el valor del ratio (si aplica)
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    INTERVALO_VALORES_HORA,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Tabla de datos
            $tabla_datos = dame_nombre_tabla_datos_clase_sensor($clase_sensor).SUFIJO_TABLA_HORAS;

            // Valor para la consulta de valores
            $res_valor_consulta = dame_valor_consulta_campo_clase_sensor($clase_sensor, $campo, $parametros_extra_campo);
            $campo_valor = $res_valor_consulta["campo_valor"];
            $valor_consulta = $res_valor_consulta["valor_consulta"];

            // Consulta de valores del sensor
            $consulta_valores_sensor = "
                SELECT
                    hora AS fecha_hora,
                    (".$valor_consulta.") AS valor
                FROM ".$tabla_datos."
                WHERE
                    (".$bd_datos->_($campo_valor)." IS NOT NULL)
                    AND (sensor = '".$bd_datos->_($nombre_sensor)."')
                    AND (red = '".$_SESSION["id_red"]."')
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

            // Se añaden el horario semanal y la exclusión e inclusión de fechas
            $consulta_valores_sensor .= $filtro_consulta_horario_semanal_fechas;

            // Se ejecuta la consulta
            $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
            if ($res_valores_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
            }

            // Se recorren los valores del sensor
            $suma_valores = 0;
            $max_valor = -INF;
            $min_valor = INF;
            $valores_sensor = array();
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
            {
                // Fecha y valor
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor["valor"];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                if ($valor === NULL)
                {
                    continue;
                }

                // Sumas, máximo y mínimo de valores
                $suma_valores += $valor;
                if ($valor > $max_valor)
                {
                    $max_valor = $valor;
                }
                if ($valor < $min_valor)
                {
                    $min_valor = $valor;
                }

                // Número de valores del sensor
                array_push($valores_sensor, $valor);
            }

            // Si no hay valores, se ignora el sensor
            $numero_valores_sensor = count($valores_sensor);
            if ($numero_valores_sensor == 0)
            {
                continue;
            }

            // Media de valores y desviación estándar
            $media_valores = $suma_valores / $numero_valores_sensor;
            $suma_desviacion_estandar = 0;
            foreach ($valores_sensor as $valor_sensor)
            {
                $suma_desviacion_estandar += ($valor_sensor - $media_valores) * ($valor_sensor - $media_valores);
            }
            $desviacion_estandar = sqrt($suma_desviacion_estandar / $numero_valores_sensor);

            // Variables
            $hay_datos = true;
            $etiquetas_graficas_valores->anyade_etiqueta($nombre_sensor);

            // Cálculo de coeficiente de estabilidad de valores
            $rango_valores = abs($max_valor - $min_valor);
            if ($rango_valores == 0)
            {
                $coeficiente_estabilidad_valores = 100;
            }
            else
            {
                $coeficiente_estabilidad_valores = ($desviacion_estandar / $rango_valores) * 100;
            }

            // Amplitud y altura relativa de valores
            $amplitud_valores = $max_valor - $min_valor;
            $min_valor_altura_relativa_valores_maximos = $min_valor;
            $max_valor_altura_relativa_valores_maximos = $max_valor;
            $media_valores_altura_relativa_valores_maximos = $media_valores;
            if ($min_valor_altura_relativa_valores_maximos <> 0.0)
            {
                $max_valor_altura_relativa_valores_maximos -= $min_valor_altura_relativa_valores_maximos;
                $media_valores_altura_relativa_valores_maximos -= $min_valor_altura_relativa_valores_maximos;
            }
            $altura_relativa_valores_maximos = dame_porcentaje_valor_referencia($max_valor_altura_relativa_valores_maximos, $media_valores_altura_relativa_valores_maximos);

            if ($amplitud_valores > $max_amplitud_valores)
            {
                $max_amplitud_valores = (float) $amplitud_valores;
            }
            if ($altura_relativa_valores_maximos > $max_altura_relativa_valores_maximos)
            {
                $max_altura_relativa_valores_maximos = (float) $altura_relativa_valores_maximos;
            }

            $grafica_coeficientes_estabilidad_valores->anyade_tupla_dato($coeficiente_estabilidad_valores);
            $grafica_amplitudes_valores->anyade_tupla_dato($amplitud_valores);
            $grafica_alturas_relativas_valores_maximos->anyade_tupla_dato($altura_relativa_valores_maximos);
        }

        // Si no hay datos no se hace nada
        if ($hay_datos == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Valores máximos mínimos
        if ($max_amplitud_valores == 0)
        {
            $max_amplitud_valores = 1;
        }
        if ($max_altura_relativa_valores_maximos == 0)
        {
            $max_altura_relativa_valores_maximos = 1;
        }

        $texto_explicacion_coeficiente_estabilidad = "<i class='icon-info-sign color-azul'></i>"." ".
            $idiomas->_("El coeficiente de estabilidad representa la similitud de los valores durante el periodo [0 - 100] (a mayor coeficiente de estabilidad mayor similitud entre los valores)");
        $texto_explicacion_amplitud = "<i class='icon-info-sign color-azul'></i>"." ".
            $idiomas->_("La amplitud es la diferencia entre los valores máximo y mínimo durante el periodo");
        $texto_explicacion_altura_relativa_maxima = "<i class='icon-info-sign color-azul'></i>"." ".
            $idiomas->_("La altura relativa del valor máximo es el porcentaje del máximo con respecto a la media de valores del periodo");

        // Unidad de medida
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $ids_sensores[0], $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "etiquetas_graficas_valores" => $etiquetas_graficas_valores->dame_datos(),
            "grafica_coeficientes_estabilidad_valores" => $grafica_coeficientes_estabilidad_valores->dame_datos(),
            "grafica_amplitudes_valores" => $grafica_amplitudes_valores->dame_datos(),
            "grafica_alturas_relativas_valores_maximos" => $grafica_alturas_relativas_valores_maximos->dame_datos(),
            "max_amplitud_valores" => $max_amplitud_valores,
            "max_altura_relativa_valores_maximos" => $max_altura_relativa_valores_maximos,
            "texto_explicacion_coeficiente_estabilidad" => $texto_explicacion_coeficiente_estabilidad,
            "texto_explicacion_amplitud" => $texto_explicacion_amplitud,
            "texto_explicacion_altura_relativa_maxima" => $texto_explicacion_altura_relativa_maxima,
            "unidad_medida" => $unidad_medida);
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve las medias de valores de un sensor
    function dame_medias_valores_sensor($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $agrupacion_valores = $parametros["agrupacion_valores"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se recupera el ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                INTERVALO_VALORES_HORA,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Tabla de datos
        $tabla_datos = dame_nombre_tabla_datos_clase_sensor($clase_sensor).SUFIJO_TABLA_HORAS;

        // Valor para la consulta de valores
        $res_valor_consulta = dame_valor_consulta_campo_clase_sensor($clase_sensor, $campo, $parametros_extra_campo);
        $campo_valor = $res_valor_consulta["campo_valor"];
        $valor_consulta = $res_valor_consulta["valor_consulta"];

        // Se realiza la consulta de los valores del sensor
        $consulta_valores_sensor = "
            SELECT
                hora AS fecha_hora,
                HOUR(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS hora_dia,
                WEEKDAY(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) AS dia_semana,
                (".$valor_consulta.") AS valor
            FROM ".$tabla_datos."
            WHERE
                (".$campo_valor." IS NOT NULL)
                AND (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_valores_sensor .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se ejecuta la consulta
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Se recorren los valores del sensor
        $valores_sensor_agrupaciones = array();
        while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor["fecha_hora"];
            $valor = $fila_valor_sensor["valor"];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Agrupación de valores
            switch ($agrupacion_valores)
            {
                case AGRUPACION_VALORES_HORA:
                {
                    $clave_agrupacion = $fila_valor_sensor["hora_dia"];
                    break;
                }
                case AGRUPACION_VALORES_DIA_SEMANA:
                {
                    $clave_agrupacion = $fila_valor_sensor["dia_semana"];
                    break;
                }
                default:
                {
                    throw new Exception("Agrupación de valores desconocida: '".$agrupacion_valores."'");
                }
            }
            if (array_key_exists($clave_agrupacion, $valores_sensor_agrupaciones) == false)
            {
                $valores_sensor_agrupaciones[$clave_agrupacion] = array();
            }
            array_push($valores_sensor_agrupaciones[$clave_agrupacion], $valor);
        }

        // Variables
        $datos_medias_valores = new VectorDatos();
        $grafica_medias_valores = new VectorDatos();
        $datos_banda_valores = new VectorDatos();
        $banda_valores = new VectorDatos();
        $grafica_coeficientes_variacion_valores = new VectorDatos();
        $datos_coeficientes_variacion_valores = new VectorDatos();
        $min_media_valores = INF;
        $max_media_valores = -INF;
        $min_coeficiente_variacion_valores = INF;
        $max_coeficiente_variacion_valores = -INF;
        $hora_dia_anterior = NULL;
        $numero_dia_semana_anterior = NULL;

        // Se recorren las agrupaciones de valores
        ksort($valores_sensor_agrupaciones);
        foreach ($valores_sensor_agrupaciones AS $clave_agrupacion => $valores_sensor_agrupacion)
        {
            // Media de valores y desviación estándar
            $numero_valores_sensor = count($valores_sensor_agrupacion);
            $suma_valores = array_sum($valores_sensor_agrupacion);
            $media_valores = $suma_valores / $numero_valores_sensor;
            $suma_desviacion_estandar = 0;
            foreach ($valores_sensor_agrupacion as $valor_sensor)
            {
                $suma_desviacion_estandar += ($valor_sensor - $media_valores) * ($valor_sensor - $media_valores);
            }
            $desviacion_estandar = sqrt($suma_desviacion_estandar / $numero_valores_sensor);

            // Valor mínimos y máximos
            $coeficiente_variacion_valores = 0.0;
            if ($media_valores < $min_media_valores)
            {
                $min_media_valores = $media_valores;
            }
            if ($media_valores > $max_media_valores)
            {
                $max_media_valores = $media_valores;
            }
            if ($media_valores != 0)
            {
                $coeficiente_variacion_valores = ($desviacion_estandar / $media_valores);
            }
            if ($coeficiente_variacion_valores < $min_coeficiente_variacion_valores)
            {
                $min_coeficiente_variacion_valores = $coeficiente_variacion_valores;
            }
            if ($coeficiente_variacion_valores > $max_coeficiente_variacion_valores)
            {
                $max_coeficiente_variacion_valores = $coeficiente_variacion_valores;
            }
            switch ($agrupacion_valores)
            {
                case AGRUPACION_VALORES_HORA:
                {
                    $hora_dia = $clave_agrupacion;
                    if (($hora_dia_anterior !== NULL) && ($hora_dia - $hora_dia_anterior > 1))
                    {
                        $datos_medias_valores->anyade_tupla_pareja_datos($hora_dia_anterior + 1, NULL);
                        $datos_coeficientes_variacion_valores->anyade_tupla_pareja_datos($hora_dia_anterior + 1, NULL);
                        $datos_banda_valores->anyade_tupla_pareja_datos(NULL, NULL);
                    }
                    $hora_dia_anterior = $hora_dia;

                    $datos_medias_valores->anyade_tupla_pareja_datos($hora_dia, $media_valores);
                    $datos_coeficientes_variacion_valores->anyade_tupla_pareja_datos($hora_dia, $coeficiente_variacion_valores);
                    break;
                }
                case AGRUPACION_VALORES_DIA_SEMANA:
                {
                    $numero_dia_semana = $clave_agrupacion + 1;
                    if (($numero_dia_semana_anterior !== NULL) && ($numero_dia_semana - $numero_dia_semana_anterior > 1))
                    {
                        $datos_medias_valores->anyade_tupla_pareja_datos($numero_dia_semana_anterior + 1, NULL);
                        $datos_coeficientes_variacion_valores->anyade_tupla_pareja_datos($numero_dia_semana_anterior + 1, NULL);
                        $datos_banda_valores->anyade_tupla_pareja_datos(NULL, NULL);
                    }
                    $numero_dia_semana_anterior = $numero_dia_semana;

                    $datos_medias_valores->anyade_tupla_pareja_datos($numero_dia_semana, $media_valores);
                    $datos_coeficientes_variacion_valores->anyade_tupla_pareja_datos($numero_dia_semana, $coeficiente_variacion_valores);
                    break;
                }
            }
            $datos_banda_valores->anyade_tupla_pareja_datos(
                $media_valores + $desviacion_estandar,
                $media_valores - $desviacion_estandar);
        }
        $grafica_medias_valores->anyade_dato($datos_medias_valores->dame_datos());
        $banda_valores->anyade_dato($datos_banda_valores->dame_datos());
        $grafica_coeficientes_variacion_valores->anyade_dato($datos_coeficientes_variacion_valores->dame_datos());

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_media_valores == INF)
        {
            $min_media_valores = "ND";
        }
        if ($max_media_valores == -INF)
        {
            $max_media_valores = "ND";
        }
        if ($min_coeficiente_variacion_valores == INF)
        {
            $min_coeficiente_variacion_valores = "ND";
        }
        if ($max_coeficiente_variacion_valores == -INF)
        {
            $max_coeficiente_variacion_valores = "ND";
        }

        // Resultado
        $resultado = array(
            "grafica_medias_valores" => $grafica_medias_valores->dame_datos(),
            "min_media_valores" => $min_media_valores,
            "max_media_valores" => $max_media_valores,
            "banda_valores" => $banda_valores->dame_datos(),
            "grafica_coeficientes_variacion_valores" => $grafica_coeficientes_variacion_valores->dame_datos(),
            "min_coeficiente_variacion_valores" => $min_coeficiente_variacion_valores,
            "max_coeficiente_variacion_valores" => $max_coeficiente_variacion_valores);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_sensores_analisis_horario()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_MAPA_CALOR_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_MEDIAS_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_LORENZ_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_TABLA_PERCENTILES_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_PORCENTAJES_VALORES);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_analisis_diario()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_MAPA_CALOR_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_MEDIAS_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_SUMAS_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES_MEDIAS_MAXIMOS_MINIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_VALORES_DIA);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_analisis_comportamiento()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_COEFICIENTES_ESTABILIDAD_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_COEFICIENTE_ESTABILIDAD);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_AMPLITUDES_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_AMPLITUD);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_ALTURAS_RELATIVAS_VALORES_MAXIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_ALTURA_RELATIVA_MAXIMA);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_sensores_analisis_horario($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_MAPA_CALOR_VALORES:
            {
                $descripcion = "Mapa de calor de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_MEDIAS_VALORES:
            {
                $descripcion = "Gráfica de medias de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES:
            {
                $descripcion = "Gráfica de coeficientes de variación de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_LORENZ_VALORES:
            {
                $descripcion = "Gráfica de Lorenz de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_TABLA_PERCENTILES_VALORES:
            {
                $descripcion = "Tabla de percentiles de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_GRAFICA_PORCENTAJES_VALORES:
            {
                $descripcion = "Gráfica de porcentajes de valores";
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


    function dame_descripcion_elemento_informe_sensores_analisis_diario($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_MAPA_CALOR_VALORES:
            {
                $descripcion = "Mapa de calor de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_MEDIAS_VALORES:
            {
                $descripcion = "Gráfica de medias de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_COEFICIENTES_VARIACION_VALORES:
            {
                $descripcion = "Gráfica de coeficientes de variación de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_SUMAS_VALORES:
            {
                $descripcion = "Gráfica de totales de valores diarios";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_GRAFICA_VALORES_MEDIAS_MAXIMOS_MINIMOS:
            {
                $descripcion = "Gráfica de medias de valores, valores máximos y mínimos por hora diarios";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_MAXIMOS_MINIMOS_MEDIAS_MEDIDAS:
            {
                $descripcion = "Tabla de medias de máximos, mínimos y medias de medidas diarias";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_TABLA_VALORES_DIA:
            {
                $descripcion = "Tabla de valores diarios";
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


    function dame_descripcion_elemento_informe_sensores_analisis_comportamiento($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_COEFICIENTES_ESTABILIDAD_VALORES:
            {
                $descripcion = "Gráfica de coeficientes de estabilidad de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_COEFICIENTE_ESTABILIDAD:
            {
                $descripcion = "Texto de explicación de coeficiente de estabilidad";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_AMPLITUDES_VALORES:
            {
                $descripcion = "Gráfica de amplitudes máximas de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_AMPLITUD:
            {
                $descripcion = "Texto de explicación de amplitud";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_GRAFICA_ALTURAS_RELATIVAS_VALORES_MAXIMOS:
            {
                $descripcion = "Gráfica de alturas relativas de valores máximos";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TEXTO_EXPLICACION_ALTURA_RELATIVA_MAXIMA:
            {
                $descripcion = "Texto de explicación de altura relativa máxima";
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


    function dame_html_informe_tipo_sensores_analisis_horario($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-analisis-horario'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-analisis-horario' hidden>
                        <div class='grafica100' id='grafica-valores-analisis-horario'></div>
                        <div class='mapa-calor100' id='mapa-calor-valores-analisis-horario'></div>
                        <div class='grafica100' id='grafica-medias-valores-analisis-horario'></div>
                        <div class='grafica100' id='grafica-coeficientes-variacion-valores-analisis-horario'></div>
                        <div class='grafica100' id='grafica-lorenz-valores-analisis-horario'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-percentiles-valores-analisis-horario'></div>
                        <div class='grafica100' id='grafica-porcentajes-valores-analisis-horario'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Análisis horario (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-horario-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_HORARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-horario-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-analisis-horario'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-valores-analisis-horario-1'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis horario (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-horario-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_HORARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-horario-2'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-valores-analisis-horario-2'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis horario (3)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-horario-3'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_HORARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-horario-3'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-medias-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coeficientes-variacion-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-lorenz-valores-analisis-horario'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-percentiles-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-valores-analisis-horario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_analisis_diario($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-analisis-diario'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-analisis-diario' hidden>
                        <div class='grafica100' id='grafica-valores-analisis-diario'></div>
                        <div class='mapa-calor100' id='mapa-calor-valores-analisis-diario'></div>
                        <div class='grafica100' id='grafica-medias-valores-analisis-diario'></div>
                        <div class='grafica100' id='grafica-coeficientes-variacion-valores-analisis-diario'></div>
                        <div class='grafica100' id='grafica-sumas-valores-analisis-diario'></div>
                        <div class='grafica100' id='grafica-valores-medias-maximos-minimos-analisis-diario'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-valores-dia-analisis-diario'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Análisis diario (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-diario-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_DIARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-diario-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-analisis-diario'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-valores-analisis-diario-1'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis diario (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-diario-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_DIARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-diario-2'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-valores-analisis-diario-2'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis diario (3)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-diario-3'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_DIARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-diario-3'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-medias-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coeficientes-variacion-valores-analisis-diario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis diario (4)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-diario-4'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_DIARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-diario-4'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-sumas-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-medias-maximos-minimos-analisis-diario'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Análisis diario (5)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-diario-5'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_DIARIO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-diario-5'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-valores-dia-analisis-diario'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_analisis_comportamiento($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe .= "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-analisis-comportamiento'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-analisis-comportamiento' hidden>
                        <div class='grafica100' id='grafica-coeficientes-estabilidad-valores-analisis-comportamiento'></div>
                        <div class='texto100' id='texto-explicacion-coeficiente-estabilidad-analisis-comportamiento'></div>
                        <div class='grafica100' id='grafica-amplitudes-valores-analisis-comportamiento'></div>
                        <div class='texto100' id='texto-explicacion-amplitud-analisis-comportamiento'></div>
                        <div class='grafica100' id='grafica-alturas-relativas-valores-maximos-analisis-comportamiento'></div>
                        <div class='texto100' id='texto-explicacion-altura-relativa-maxima-analisis-comportamiento'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de análisis de comportamiento
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-analisis-informe-fichero-analisis-comportamiento'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_analisis(TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-analisis-comportamiento'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coeficientes-estabilidad-valores-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-explicacion-coeficiente-estabilidad-analisis-comportamiento'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-amplitudes-valores-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-explicacion-amplitud-analisis-comportamiento'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-alturas-relativas-valores-maximos-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-explicacion-altura-relativa-maxima-analisis-comportamiento'></div>
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


    function dame_html_elemento_plantilla_informe_tipo_sensores_analisis_horario(
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-horario'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-valores-analisis-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-medias-valores-analisis-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-variacion-valores-analisis-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-lorenz-valores-analisis-horario'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-percentiles-valores-analisis-horario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-valores-analisis-horario'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-horario'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-medias-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-variacion-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-lorenz-valores-analisis-horario'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-percentiles-valores-analisis-horario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-valores-analisis-horario'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_analisis_diario(
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-diario'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-valores-analisis-diario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-medias-valores-analisis-diario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-variacion-valores-analisis-diario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-sumas-valores-analisis-diario'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-medias-maximos-minimos-analisis-diario'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-dia-analisis-diario'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-analisis-diario'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-medias-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-variacion-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-sumas-valores-analisis-diario'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-medias-maximos-minimos-analisis-diario'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-maximos-minimos-medias-medidas-analisis-diario'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-dia-analisis-diario'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-estabilidad-valores-analisis-comportamiento'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-explicacion-coeficiente-estabilidad-analisis-comportamiento'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-amplitudes-valores-analisis-comportamiento'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-explicacion-amplitud-analisis-comportamiento'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-alturas-relativas-valores-maximos-analisis-comportamiento'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-explicacion-altura-relativa-maxima-analisis-comportamiento'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-coeficientes-estabilidad-valores-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-explicacion-coeficiente-estabilidad-analisis-comportamiento'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-amplitudes-valores-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-explicacion-amplitud-analisis-comportamiento'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-alturas-relativas-valores-maximos-analisis-comportamiento'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-explicacion-altura-relativa-maxima-analisis-comportamiento'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_horario(
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
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_analisis_horario_valores_sensor($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_diario(
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
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_analisis_diario_valores_sensor($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_analisis_comportamiento(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_sensores"]) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_sensores"] as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_seleccionados = true;
                    break;
                }
            }
        }
        if ($hay_sensores_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_analisis_comportamiento_valores_sensores($parametros_informe);
        return ($datos_elemento);
    }
?>
