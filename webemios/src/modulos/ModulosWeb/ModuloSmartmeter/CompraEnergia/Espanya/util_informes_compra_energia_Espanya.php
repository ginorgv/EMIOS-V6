<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_informes_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/util_compra_energia_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones de información de compra de energía (Espanya)
    //


    // Devuelve la información de previsión de compra de energía de un sensor
    function dame_prevision_compra_energia_sensor_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $cadena_fecha_inicio_perfil_horario_local_local = $parametros["fecha_inicio_perfil_horario"];
        $cadena_fecha_fin_perfil_horario_local_local = $parametros["fecha_fin_perfil_horario"];
        $tipo_perfil_horario = $parametros["tipo_perfil_horario"];
        $agrupaciones_dias_semana = json_decode($parametros["agrupaciones_dias_semana"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Información del sensor asociado
        $fila_sensor = dame_fila_sensor($id_sensor);
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);
        $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
        $fila_sensor_asociado = dame_fila_sensor($id_sensor_asociado);
        $nombre_sensor_asociado = $fila_sensor_asociado["nombre"];

        // Parámetros para el informe
        $clase_sensor_asociado = CLASE_SENSOR_ENERGIA_ACTIVA;
        $campo_sensor_asociado = CAMPO_INCREMENTO;
        $intervalo_valores = INTERVALO_VALORES_HORA;

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_inicio_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_inicio_perfil_horario_local_local, $_SESSION["formato_fecha_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_fin_perfil_horario_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_fin_perfil_horario_local_local, $_SESSION["formato_fecha_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_inicio_perfil_horario_funciones_utc = convierte_formato_fecha($cadena_fecha_inicio_perfil_horario_local_utc, $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);
        $cadena_fecha_fin_perfil_horario_funciones_utc = convierte_formato_fecha($cadena_fecha_fin_perfil_horario_local_utc, $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_inicio_utc = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC);

        // Parámetros de la función a llamar
        $cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana($agrupaciones_dias_semana);
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_PERFIL_HORARIO,
                "id_red" => $_SESSION["id_red"],
                "clase_sensor" => $clase_sensor_asociado,
                "id_sensor" => $id_sensor_asociado,
                "nombre_sensor" => $nombre_sensor_asociado,
                "campo" => $campo_sensor_asociado,
                "parametros_extra_campo" => "",
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "intervalo_valores" => $intervalo_valores,
                "fecha_inicio_perfil_horario" => $cadena_fecha_inicio_perfil_horario_funciones_utc,
                "fecha_fin_perfil_horario" => $cadena_fecha_fin_perfil_horario_funciones_utc,
                "tipo_perfil_horario" => $tipo_perfil_horario,
                "agrupaciones_dias_semana" => $cadena_agrupaciones_dias_semana,
                "horario_semanal" => "",
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "incluir_valores_reales" => VALOR_NO
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de valores reales y simulados
        $valores_reales_simulados_perfil_horario = $resultado_funcion_externa["valores_reales_simulados_perfil_horario"];
        $numero_valores_reales_simulados_perfil_horario = count($valores_reales_simulados_perfil_horario);

        // Si no hay datos no se hace nada
        if ($numero_valores_reales_simulados_perfil_horario == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se crea el resultado del informe

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Unidad de medida y número de decimales
        $unidad_medida = $idiomas->_("MWh");

        // Segundos máximos entre consumos (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_graficas = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Valores mínimo y máximo
        $min_consumo_estimado = INF;
        $max_consumo_estimado = -INF;

        // Gráfica y mapa de calor de consumo estimado
        $datos_grafica_consumo_estimado = new VectorDatos();
        $valores_mapa_calor_consumos_estimados = new ValoresMapaCalor(TIPO_MAPA_CALOR_PERSONALIZADO);
        $valores_mapa_calor_consumos_estimados->pon_subperiodos(array(
            "H1", "H2", "H3", "H4", "H5", "H6", "H7", "H8",
            "H9", "H10", "H11", "H12", "H13", "H14", "H15",
            "H16", "H17", "H18", "H19", "H20", "H21", "H22", "H23", "H24",
            "H25"));
        $timestamp_fecha_hora_consumo_estimado_anterior = NULL;
        $numero_puntos_seguidos_grafica_consumo_estimado = 0;
        for ($i = 0; $i < $numero_valores_reales_simulados_perfil_horario; $i++)
        {
            $valor_real_simulado_perfil_horario = $valores_reales_simulados_perfil_horario[$i];
            $cadena_fecha_hora_consumo_estimado_funciones_utc = $valor_real_simulado_perfil_horario["fecha_hora_utc"];
            $cadena_fecha_hora_consumo_estimado_funciones_local = $valor_real_simulado_perfil_horario["fecha_hora_local"];
            $consumo_estimado = (float) $valor_real_simulado_perfil_horario["valor_simulado"];

            // El consumo estimado es en kWh (hay que convertirlo a MWh y en múltiplos de 100 kWh para el informe)
            $consumo_estimado = convierte_consumo_compra_energia_sensor_Espanya($consumo_estimado);
            if ($consumo_estimado > $max_consumo_estimado)
            {
                $max_consumo_estimado = $consumo_estimado;
            }
            if ($consumo_estimado < $min_consumo_estimado)
            {
                $min_consumo_estimado = $consumo_estimado;
            }

            $timestamp_fecha_hora_consumo_estimado = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_consumo_estimado_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_consumo_estimado -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica_consumo_estimado > 1) &&
                ($segundos_maximos_entre_valores_graficas !== NULL) && ($timestamp_fecha_hora_consumo_estimado_anterior !== NULL))
            {
                $segundos_entre_consumos_estimados = ($timestamp_fecha_hora_consumo_estimado - $timestamp_fecha_hora_consumo_estimado_anterior) / 1000;
                if ($segundos_entre_consumos_estimados > $segundos_maximos_entre_valores_graficas)
                {
                    $numero_puntos_seguidos_grafica_consumo_estimado = 0;
                    $datos_grafica_consumo_estimado->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_estimado_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_consumo_estimado_anterior = $timestamp_fecha_hora_consumo_estimado;
            $numero_puntos_seguidos_grafica_consumo_estimado += 1;

            // Datos de consumos estimados
            $datos_grafica_consumo_estimado->anyade_tupla_pareja_datos(
                $timestamp_fecha_hora_consumo_estimado,
                $consumo_estimado);

            // Mapa de calor de consumo estimado:
            // - Se establece el periodo como la diferencia de días entre la fecha del consumo estimado local y la fecha de inicio local
            // - Se establece el subperiodo como la diferencia de horas entre la fecha de consumo estimado utc y la fecha inicio del periodo en utc
            //   (porque pueden ser días de 23, 24 y 25 horas y el mapa de calor 'estándar' sólo funciona 24 horas)
            $fecha_hora_consumo_estimado_utc = convierte_cadena_a_fecha($cadena_fecha_hora_consumo_estimado_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $fecha_hora_consumo_estimado_local = convierte_cadena_a_fecha($cadena_fecha_hora_consumo_estimado_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $zona_horaria);
            $diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_local = $fecha_hora_consumo_estimado_local->diff($fecha_hora_inicio_local);
            $numero_dias_diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_local = dame_numero_dias_intervalo_tiempo($diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_local);
            $numero_periodo_consumo_estimado_mapa_calor = ($numero_dias_diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_local + 1);

            $fecha_hora_inicio_periodo_mapa_calor_utc = clone $fecha_hora_inicio_utc;
            $fecha_hora_inicio_periodo_mapa_calor_utc->add(new DateInterval("P".$numero_dias_diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_local."D"));
            $diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_periodo_mapa_calor_utc = $fecha_hora_consumo_estimado_utc->diff($fecha_hora_inicio_periodo_mapa_calor_utc);
            $numero_horas_diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_periodo_mapa_calor_utc = dame_horas_intervalo_tiempo($diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_periodo_mapa_calor_utc);
            $numero_subperiodo_consumo_estimado_mapa_calor = $numero_horas_diferencia_fecha_hora_consumo_estimado_fecha_hora_inicio_periodo_mapa_calor_utc;

            $cadena_fecha_consumo_estimado_local_local = convierte_formato_fecha($cadena_fecha_hora_consumo_estimado_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_local"]);
            $valores_mapa_calor_consumos_estimados->anyade_periodo($cadena_fecha_consumo_estimado_local_local);
            $valores_mapa_calor_consumos_estimados->anyade_valor_periodo_subperiodo(
                $numero_periodo_consumo_estimado_mapa_calor,
                $numero_subperiodo_consumo_estimado_mapa_calor,
                $consumo_estimado);
        }

        // Gráfica de consumos y mapas de calor correspondientes (consumos utilizados para el cálculo del perfil horario)
        $datos_grafica_valores_mapa_calor = dame_datos_grafica_valores_mapas_calor_valores_perfil_horario(
            $id_sensor_asociado,
            CAMPO_INCREMENTO,
            $idiomas->_("Consumo"),
            convierte_consumo_compra_energia_sensor_Espanya,
            2,
            $unidad_medida,
            $cadena_fecha_inicio_perfil_horario_local_local,
            $cadena_fecha_fin_perfil_horario_local_local,
            $intervalo_valores,
            NULL,
            $exclusion_fechas,
            $milisegundos_desfase_zonas_horarias_cliente_local);

        // Variables de gráfica de consumos y mapas de calor correspondientes
        $datos_grafica_consumos_perfil_horario_semanales = $datos_grafica_valores_mapa_calor["datos_grafica_valores_perfil_horario_semanales"];
        $datos_grafica_consumos_perfil_horario = $datos_grafica_valores_mapa_calor["datos_grafica_valores_perfil_horario"];
        $datos_banda_consumos_perfil_horario_semanales = $datos_grafica_valores_mapa_calor["datos_banda_valores_perfil_horario_semanales"];
        $min_consumo_perfil_horario = $datos_grafica_valores_mapa_calor["min_valor_perfil_horario"];
        $max_consumo_perfil_horario = $datos_grafica_valores_mapa_calor["max_valor_perfil_horario"];
        $valores_mapa_calor_consumos_perfil_horario_semanales = $datos_grafica_valores_mapa_calor["valores_mapa_calor_valores_perfil_horario_semanales"];
        $valores_mapa_calor_consumos_perfil_horario = $datos_grafica_valores_mapa_calor["valores_mapa_calor_valores_perfil_horario"];

        // Gráfica de consumos estimados
        $etiquetas_grafica_consumos_estimados = new VectorDatos();
        $etiquetas_grafica_consumos_estimados->anyade_etiqueta($nombre_sensor);
        $grafica_consumos_estimados = new VectorDatos();
        $grafica_consumos_estimados->anyade_dato($datos_grafica_consumo_estimado->dame_datos());

        // Gráfica de consumos de perfil horario
        $etiquetas_grafica_consumos_perfil_horario = new VectorDatos();
        $etiquetas_grafica_consumos_perfil_horario->anyade_etiqueta($idiomas->_("Media semanal"));
        $etiquetas_grafica_consumos_perfil_horario->anyade_etiqueta($idiomas->_("Consumos"));
        $grafica_consumos_perfil_horario = new VectorDatos();
        $grafica_consumos_perfil_horario->anyade_dato($datos_grafica_consumos_perfil_horario_semanales->dame_datos());
        $grafica_consumos_perfil_horario->anyade_dato($datos_grafica_consumos_perfil_horario->dame_datos());
        $bandas_consumos_perfil_horario = new VectorDatos();
        $bandas_consumos_perfil_horario->anyade_dato($datos_banda_consumos_perfil_horario_semanales->dame_datos());
        $bandas_consumos_perfil_horario->anyade_dato(array());

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_consumo_estimado == INF)
        {
            $min_consumo_estimado = "ND";
        }
        if ($max_consumo_estimado == -INF)
        {
            $max_consumo_estimado = "ND";
        }
        if ($min_consumo_perfil_horario == INF)
        {
            $min_consumo_perfil_horario = "ND";
        }
        if ($max_consumo_perfil_horario == -INF)
        {
            $max_consumo_perfil_horario = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "etiquetas_grafica_consumos_estimados" => $etiquetas_grafica_consumos_estimados->dame_datos(),
            "grafica_consumos_estimados" => $grafica_consumos_estimados->dame_datos(),
            "min_consumo_estimado" => $min_consumo_estimado,
            "max_consumo_estimado" => $max_consumo_estimado,
            "dias_mapa_calor_consumos_estimados" => $valores_mapa_calor_consumos_estimados->dame_periodos(),
            "horas_mapa_calor_consumos_estimados" => $valores_mapa_calor_consumos_estimados->dame_subperiodos(),
            "datos_mapa_calor_consumos_estimados" => $valores_mapa_calor_consumos_estimados->dame_datos_periodos_subperiodos(),
            "etiquetas_grafica_consumos_perfil_horario" => $etiquetas_grafica_consumos_perfil_horario->dame_datos(),
            "grafica_consumos_perfil_horario" => $grafica_consumos_perfil_horario->dame_datos(),
            "bandas_consumos_perfil_horario" => $bandas_consumos_perfil_horario->dame_datos(),
            "min_consumo_perfil_horario" => $min_consumo_perfil_horario,
            "max_consumo_perfil_horario" => $max_consumo_perfil_horario,
            "dias_mapa_calor_consumos_perfil_horario_semanales" => $valores_mapa_calor_consumos_perfil_horario_semanales->dame_dias(),
            "datos_mapa_calor_consumos_perfil_horario_semanales" => $valores_mapa_calor_consumos_perfil_horario_semanales->dame_datos(),
            "dias_mapa_calor_consumos_perfil_horario" => $valores_mapa_calor_consumos_perfil_horario->dame_dias(),
            "datos_mapa_calor_consumos_perfil_horario" => $valores_mapa_calor_consumos_perfil_horario->dame_datos(),
            "unidad_medida" => $unidad_medida);
        return ($resultado);
    }


    // Función que convierte el consumo para la compra de energía
    function convierte_consumo_compra_energia_sensor_Espanya($consumo_kwh)
    {
        // Se convierte de kWh a MWh (en múltiplos de 100 kWh redondeando hacia arriba)
        $consumo_mwh = ceil($consumo_kwh / 100) / 10;
        return ($consumo_mwh);
    }


    // Devuelve la información de desvíos de compra de energía de un sensor
    function dame_desvios_compra_energia_sensor_Espanya($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables
        $grafica_consumos = new VectorDatos();
        $grafica_consumos_acumulados = new VectorDatos();
        $grafica_desvios_consumo = new VectorDatos();
        $grafica_desvios_consumo_acumulados = new VectorDatos();
        $grafica_costes_desvios = new VectorDatos();
        $grafica_costes_desvios_acumulados = new VectorDatos();
        $valores_mapas_calor = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $valores_mapa_calor_desvios_consumo_visibles = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $valores_mapa_calor_costes_desvios_visibles = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Máximos y mínimos
        $max_consumos = (float) -INF;
        $max_consumos_acumulados = (float) -INF;
        $max_desvio_consumo = (float) -INF;
        $max_desvio_consumo_acumulado = (float) -INF;
        $max_coste_desvio = (float) -INF;
        $max_coste_desvio_acumulado = (float) -INF;
        $max_penalizable = (float) -INF;
        $min_consumos = (float) INF;
        $min_consumos_acumulados = (float) INF;
        $min_desvio_consumo = (float) INF;
        $min_desvio_consumo_acumulado = (float) INF;
        $min_coste_desvio = (float) INF;
        $min_coste_desvio_acumulado = (float) INF;
        $min_penalizable = (float) INF;

        // Número de valores y totales
        $numero_valores = 0;
        $consumo_estimado_total = 0;
        $consumo_real_total = 0;
        $desvio_consumo_total = 0;
        $coste_desvio_total = 0;

        // Datos para las gráficas y los mapas de calor
        $datos_sensor_consumos_estimados = new VectorDatos();
        $datos_sensor_consumos_reales = new VectorDatos();
        $datos_sensor_consumos_estimados_acumulados = new VectorDatos();
        $datos_sensor_consumos_reales_acumulados = new VectorDatos();
        $datos_sensor_desvios_consumo = new VectorDatos();
        $datos_sensor_desvios_consumo_acumulados = new VectorDatos();
        $datos_sensor_costes_desvios = new VectorDatos();
        $datos_sensor_costes_desvios_acumulados = new VectorDatos();

        // Se recorren las filas de valores
        $timestamp_fecha_hora_valores_anterior_utc = NULL;
        $numero_puntos_seguidos_graficas_valores = 0;
        foreach ($filas_valores_sensor as $fila_valores_sensor)
        {
            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $consumo_estimado = (float) $fila_valores_sensor['consumo_estimado'];

            // Valores de clase
            // (si no hay consumo real, es que no se han calculado los valores de clase, se ignora la fila)
            $valor_consumo_real = $fila_valores_sensor['consumo_real'];
            if ($valor_consumo_real === NULL)
            {
                continue;
            }
            $consumo_real = (float) $valor_consumo_real;
            $desvio_consumo = (float) $fila_valores_sensor['desvio_consumo'];
            $coste_desvio = (float) $fila_valores_sensor['coste_desvio'];
            $penalizable = $fila_valores_sensor['penalizable'];

            // Se incrementa el número de valores
            $numero_valores += 1;

            // Máximos y mínimos (de valores)
            if ($consumo_estimado > $max_consumos)
            {
                $max_consumos = $consumo_estimado;
            }
            if ($consumo_real > $max_consumos)
            {
                $max_consumos = $consumo_real;
            }
            if ($desvio_consumo > $max_desvio_consumo)
            {
                $max_desvio_consumo = $desvio_consumo;
            }
            if ($coste_desvio > $max_coste_desvio)
            {
                $max_coste_desvio = $coste_desvio;
            }
            if ($penalizable > $max_penalizable)
            {
                $max_penalizable = $penalizable;
            }
            if ($consumo_estimado < $min_consumos)
            {
                $min_consumos = $consumo_estimado;
            }
            if ($consumo_real < $min_consumos)
            {
                $min_consumos = $consumo_real;
            }
            if ($desvio_consumo < $min_desvio_consumo)
            {
                $min_desvio_consumo = $desvio_consumo;
            }
            if ($coste_desvio < $min_coste_desvio)
            {
                $min_coste_desvio = $coste_desvio;
            }
            if ($penalizable < $min_penalizable)
            {
                $min_penalizable = $penalizable;
            }

            // Máximos y mínimos (de valores acumulados)
            $consumo_estimado_total += $consumo_estimado;
            $consumo_real_total += $consumo_real;
            $desvio_consumo_total += $desvio_consumo;
            $coste_desvio_total += $coste_desvio;
            if ($consumo_estimado_total > $max_consumos_acumulados)
            {
                $max_consumos_acumulados = $consumo_estimado_total;
            }
            if ($consumo_real_total > $max_consumos_acumulados)
            {
                $max_consumos_acumulados = $consumo_real_total;
            }
            if ($desvio_consumo_total > $max_desvio_consumo_acumulado)
            {
                $max_desvio_consumo_acumulado = $desvio_consumo_total;
            }
            if ($coste_desvio_total > $max_coste_desvio_acumulado)
            {
                $max_coste_desvio_acumulado = $coste_desvio_total;
            }
            if ($consumo_estimado_total < $min_consumos_acumulados)
            {
                $min_consumos_acumulados = $consumo_estimado_total;
            }
            if ($consumo_real_total < $min_consumos_acumulados)
            {
                $min_consumos_acumulados = $consumo_real_total;
            }
            if ($desvio_consumo_total < $min_desvio_consumo_acumulado)
            {
                $min_desvio_consumo_acumulado = $desvio_consumo_total;
            }
            if ($coste_desvio_total < $min_coste_desvio_acumulado)
            {
                $min_coste_desvio_acumulado = $coste_desvio_total;
            }

            // Fechas de inicio y fin de consumos
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valores_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valores_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_graficas_valores > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valores_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valores_utc - $timestamp_fecha_hora_valores_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_graficas_valores = 0;
                    $datos_sensor_consumos_estimados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_consumos_estimados_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_consumos_reales->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_consumos_reales_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_desvios_consumo->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_desvios_consumo_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_costes_desvios->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                    $datos_sensor_costes_desvios_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valores_anterior_utc = $timestamp_fecha_hora_valores_utc;
            $numero_puntos_seguidos_graficas_valores += 1;

            // Se añaden los valores a las gráficas
            $datos_sensor_consumos_estimados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $consumo_estimado);
            $datos_sensor_consumos_estimados_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $consumo_estimado_total);
            $datos_sensor_consumos_reales->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $consumo_real);
            $datos_sensor_consumos_reales_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $consumo_real_total);
            $datos_sensor_desvios_consumo->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $desvio_consumo);
            $datos_sensor_desvios_consumo_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $desvio_consumo_total);
            $datos_sensor_costes_desvios->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $coste_desvio);
            $datos_sensor_costes_desvios_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_utc, $coste_desvio_total);

            // Datos para los mapas de calor
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            $valores_mapas_calor->anyade_valor_fecha_hora($fecha_hora_local, $penalizable);
            $valores_mapa_calor_desvios_consumo_visibles->anyade_valor_fecha_hora($fecha_hora_local, $desvio_consumo);
            $valores_mapa_calor_costes_desvios_visibles->anyade_valor_fecha_hora($fecha_hora_local, $coste_desvio);
        }

        // Si no hay valores no se hace nada más
        if ($numero_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se añaden los datos a las gráficas
        $grafica_consumos->anyade_dato($datos_sensor_consumos_estimados->dame_datos());
        $grafica_consumos->anyade_dato($datos_sensor_consumos_reales->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_sensor_consumos_estimados_acumulados->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_sensor_consumos_reales_acumulados->dame_datos());
        $grafica_desvios_consumo->anyade_dato($datos_sensor_desvios_consumo->dame_datos());
        $grafica_desvios_consumo_acumulados->anyade_dato($datos_sensor_desvios_consumo_acumulados->dame_datos());
        $grafica_costes_desvios->anyade_dato($datos_sensor_costes_desvios->dame_datos());
        $grafica_costes_desvios_acumulados->anyade_dato($datos_sensor_costes_desvios_acumulados->dame_datos());

        // Etiquetas
        $etiquetas_graficas_consumos = new VectorDatos();
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo estimado")." (".$nombre_sensor.")");
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo real")." (".$nombre_sensor.")");
        $etiquetas_sensor = new VectorDatos();
        $etiquetas_sensor->anyade_etiqueta($nombre_sensor);

        // Unidades de medida
        $unidad_medida_consumo = $idiomas->_("kWh");
        $unidad_medida_coste = $_SESSION["moneda"];

        // Días y datos de mapas de calor
        // (Nota: Si sólo hay 1 valor, el mapa de calor dibuja el último color de la escala)
        $dias_mapas_calor = $valores_mapas_calor->dame_dias();
        $datos_mapas_calor = $valores_mapas_calor->dame_datos();
        $datos_mapa_calor_desvios_consumo_visibles = $valores_mapa_calor_desvios_consumo_visibles->dame_datos();
        $datos_mapa_calor_costes_desvios_visibles = $valores_mapa_calor_costes_desvios_visibles->dame_datos();
        if ($min_penalizable == $max_penalizable)
        {
            if ($min_penalizable == VALOR_NO)
            {
                $colores_mapas_calor = COLORES_ROJO_VERDE;
            }
            else
            {
                $colores_mapas_calor = COLORES_VERDE_ROJO;
            }
        }
        else
        {
            $colores_mapas_calor = COLORES_VERDE_ROJO;
        }

        // Tabla de consumos y desvíos totales (de consumo y de costes acumulados)
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMOS_DESVIOS_TOTALES_COMPRA_ENERGIA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_CONSUMOS_DESVIOS_TOTALES_COMPRA_ENERGIA),
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Consumo estimado"),
            $idiomas->_("Consumo real"),
            $idiomas->_("Desvío de consumo"),
            $idiomas->_("Coste de desvío")
        );
        $titulo_tabla_consumos_desvios_totales = $idiomas->_("Consumos y desvíos totales");
        $tabla_consumos_desvios_totales = new TablaDatos(
            "tabla-consumos-desvios-totales-desvios-compra-energia",
            $titulo_tabla_consumos_desvios_totales,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_consumos_desvios_totales->anyade_cabecera("", $cabecera_tabla);

        // Fila de la tabla de consumos y desvíos totales
        $cadena_consumo_estimado_total = formatea_numero($consumo_estimado_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_consumo_real_total = formatea_numero($consumo_real_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_desvio_consumo_total = formatea_numero($desvio_consumo_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_coste_desvio_total = formatea_numero($coste_desvio_total, 2, false)." ".$unidad_medida_coste;
        $params_fila_consumos_desvios_totales = array(
            "texto_eliminar_valor_xml_1" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_2" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_3" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_4" => " ".$unidad_medida_coste);
        $fila_tabla_consumos_desvios_totales = array(
            $cadena_consumo_estimado_total,
            $cadena_consumo_real_total,
            $cadena_desvio_consumo_total,
            $cadena_coste_desvio_total);
        $tabla_consumos_desvios_totales->anyade_fila("", $fila_tabla_consumos_desvios_totales, $params_fila_consumos_desvios_totales);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tabla_consumos_desvios_totales" => $tabla_consumos_desvios_totales->dame_tabla(),
            "etiquetas_graficas_consumos" => $etiquetas_graficas_consumos->dame_datos(),
            "etiquetas_sensor" => $etiquetas_sensor->dame_datos(),
            "grafica_consumos" => $grafica_consumos->dame_datos(),
            "grafica_consumos_acumulados" => $grafica_consumos_acumulados->dame_datos(),
            "grafica_desvios_consumo" => $grafica_desvios_consumo->dame_datos(),
            "grafica_desvios_consumo_acumulados" => $grafica_desvios_consumo_acumulados->dame_datos(),
            "grafica_costes_desvios" => $grafica_costes_desvios->dame_datos(),
            "grafica_costes_desvios_acumulados" => $grafica_costes_desvios_acumulados->dame_datos(),
            "min_consumos" => $min_consumos,
            "min_consumos_acumulados" => $min_consumos_acumulados,
            "min_desvio_consumo" => $min_desvio_consumo,
            "min_desvio_consumo_acumulado" => $min_desvio_consumo_acumulado,
            "min_coste_desvio" => $min_coste_desvio,
            "min_coste_desvio_acumulado" => $min_coste_desvio_acumulado,
            "max_consumos" => $max_consumos,
            "max_consumos_acumulados" => $max_consumos_acumulados,
            "max_desvio_consumo" => $max_desvio_consumo,
            "max_desvio_consumo_acumulado" => $max_desvio_consumo_acumulado,
            "max_coste_desvio" => $max_coste_desvio,
            "max_coste_desvio_acumulado" => $max_coste_desvio_acumulado,
            "colores_mapas_calor" => $colores_mapas_calor,
            "dias_mapas_calor" => $dias_mapas_calor,
            "datos_mapas_calor" => $datos_mapas_calor,
            "datos_mapa_calor_desvios_consumo_visibles" => $datos_mapa_calor_desvios_consumo_visibles,
            "datos_mapa_calor_costes_desvios_visibles" => $datos_mapa_calor_costes_desvios_visibles,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste);
        return ($resultado);
    }


    // Devuelve la información de desvíos ponderados de compra de energía de un sensor
    function dame_desvios_ponderados_compra_energia_sensor_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_sensor_hijo = $parametros["id_sensor_hijo"];
        $nombre_sensor_hijo = $parametros["nombre_sensor_hijo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }
        if (in_array($id_sensor_hijo, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor hijo no visible por el usuario actual (id: '".$id_sensor_hijo."')");
        }

        // Se comprueba si el sensor hijo es realmente hijo del sensor
        // (puede ocurrir en plantillas de informe configurables)
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);
        $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
        $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
        if (in_array($id_sensor_hijo, $ids_sensores_hijos) == false)
        {
            $res = "ERROR";
            $msg = $idiomas->_("El sensor no es un sensor hijo del sensor de compra de energía");

            $resultado = array(
                "res" => $res,
                "msg" => $msg);
            return ($resultado);
        }

        // Se recupera el identificador de tarifa eléctrica del sensor en la fecha inicial
        $id_tarifa_sensor_inicial = dame_id_tarifa_id_sensor_fecha($id_sensor_hijo, $cadena_fecha_hora_inicio_local_local);
        if ($id_tarifa_sensor_inicial == ID_NINGUNO)
        {
            $msg = $idiomas->_("El sensor no tiene tarifa eléctrica asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $msg);
            return ($resultado);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

        // Parámetros de la función a llamar
        $cadena_horario_semanal = dame_cadena_horario_semanal($horario_semanal);
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);
        $cadena_inclusion_fechas = dame_cadena_fechas($inclusion_fechas);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_DESVIOS_PONDERADOS_COMPRA_ENERGIA_SENSOR_ESPANYA,
                "id_sensor_compra_energia" => $id_sensor,
                "id_sensor_energia_activa" => $id_sensor_hijo,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de desvios ponderados
        $datos_desvios_ponderados_compra_energia = $resultado_funcion_externa["datos_desvios_ponderados_compra_energia"];
        $numero_datos_desvios_ponderados_compra_energia = count($datos_desvios_ponderados_compra_energia);

        // Si no hay datos no se hace nada
        if ($numero_datos_desvios_ponderados_compra_energia == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se crea el resultado del informe

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Segundos máximos entre consumos y costes (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, $id_sensor_hijo);

        // Valores mínimos y máximos
        $min_consumos = INF;
        $max_consumos = -INF;
        $min_consumos_acumulados = INF;
        $max_consumos_acumulados = -INF;
        $min_coste_desvio_ponderado_sensor_asociado = INF;
        $max_coste_desvio_ponderado_sensor_asociado = -INF;
        $min_coste_desvio_ponderado_acumulado_sensor_asociado = INF;
        $max_coste_desvio_ponderado_acumulado_sensor_asociado = -INF;

        // Totales
        $consumo_energia_bruto_sensor_asociado_total = 0;
        $consumo_energia_neto_sensor_asociado_total = 0;
        $consumo_real_compra_energia_total = 0;
        $coste_desvio_ponderado_sensor_asociado_total = 0;

        // Gráficas de consumos y costes de desvíos
        $datos_consumos_energia_brutos_sensor_asociado = new VectorDatos();
        $datos_consumos_energia_netos_sensor_asociado = new VectorDatos();
        $datos_consumos_reales_compra_energia = new VectorDatos();
        $datos_consumos_energia_brutos_acumulados_sensor_asociado = new VectorDatos();
        $datos_consumos_energia_netos_acumulados_sensor_asociado = new VectorDatos();
        $datos_consumos_reales_acumulados_compra_energia = new VectorDatos();
        $datos_costes_desvios_ponderados_sensor_asociado = new VectorDatos();
        $datos_costes_desvios_ponderados_acumulados_sensor_asociado = new VectorDatos();
        $valores_mapa_calor_costes_desvios_ponderados_sensor_asociado = new ValoresMapaCalor(TIPO_MAPA_CALOR_DIARIO);
        $timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior = NULL;
        $numero_puntos_seguidos_grafica = 0;
        for ($i = 0; $i < $numero_datos_desvios_ponderados_compra_energia; $i++)
        {
            $datos_desvio_ponderado_compra_energia = $datos_desvios_ponderados_compra_energia[$i];
            $cadena_fecha_hora_desvio_ponderado_compra_energia_funciones_utc = $datos_desvio_ponderado_compra_energia["hora"];
            $consumo_energia_bruto_sensor_asociado = (float) $datos_desvio_ponderado_compra_energia["consumo_energia_bruto_sensor_asociado"];
            $consumo_energia_neto_sensor_asociado = (float) $datos_desvio_ponderado_compra_energia["consumo_energia_neto_sensor_asociado"];
            $consumo_real_compra_energia = (float) $datos_desvio_ponderado_compra_energia["consumo_real_compra_energia"];
            $coste_desvio_ponderado_sensor_asociado = (float) $datos_desvio_ponderado_compra_energia["coste_desvio_ponderado_sensor_asociado"];

            // Máximos y mínimos (de valores)
            if ($consumo_energia_bruto_sensor_asociado > $max_consumos)
            {
                $max_consumos = $consumo_energia_bruto_sensor_asociado;
            }
            if ($consumo_energia_neto_sensor_asociado > $max_consumos)
            {
                $max_consumos = $consumo_energia_neto_sensor_asociado;
            }
            if ($consumo_real_compra_energia > $max_consumos)
            {
                $max_consumos = $consumo_real_compra_energia;
            }
            if ($coste_desvio_ponderado_sensor_asociado > $max_coste_desvio_ponderado_sensor_asociado)
            {
                $max_coste_desvio_ponderado_sensor_asociado = $coste_desvio_ponderado_sensor_asociado;
            }
            if ($consumo_energia_bruto_sensor_asociado < $min_consumos)
            {
                $min_consumos = $consumo_energia_bruto_sensor_asociado;
            }
            if ($consumo_energia_neto_sensor_asociado < $min_consumos)
            {
                $min_consumos = $consumo_energia_neto_sensor_asociado;
            }
            if ($consumo_real_compra_energia < $min_consumos)
            {
                $min_consumos = $consumo_real_compra_energia;
            }
            if ($coste_desvio_ponderado_sensor_asociado < $min_coste_desvio_ponderado_sensor_asociado)
            {
                $min_coste_desvio_ponderado_sensor_asociado = $coste_desvio_ponderado_sensor_asociado;
            }

            // Máximos y mínimos (de valores acumulados)
            $consumo_energia_bruto_sensor_asociado_total += $consumo_energia_bruto_sensor_asociado;
            $consumo_energia_neto_sensor_asociado_total += $consumo_energia_neto_sensor_asociado;
            $consumo_real_compra_energia_total += $consumo_real_compra_energia;
            $coste_desvio_ponderado_sensor_asociado_total += $coste_desvio_ponderado_sensor_asociado;
            if ($consumo_energia_bruto_sensor_asociado_total > $max_consumos_acumulados)
            {
                $max_consumos_acumulados = $consumo_energia_bruto_sensor_asociado_total;
            }
            if ($consumo_energia_neto_sensor_asociado_total > $max_consumos_acumulados)
            {
                $max_consumos_acumulados = $consumo_energia_neto_sensor_asociado_total;
            }
            if ($consumo_real_compra_energia_total > $max_consumos_acumulados)
            {
                $max_consumos_acumulados = $consumo_real_compra_energia_total;
            }
            if ($coste_desvio_ponderado_sensor_asociado_total > $max_coste_desvio_ponderado_acumulado_sensor_asociado)
            {
                $max_coste_desvio_ponderado_acumulado_sensor_asociado = $coste_desvio_ponderado_sensor_asociado_total;
            }
            if ($consumo_energia_bruto_sensor_asociado_total < $min_consumos_acumulados)
            {
                $min_consumos_acumulados = $consumo_energia_bruto_sensor_asociado_total;
            }
            if ($consumo_energia_neto_sensor_asociado_total < $min_consumos_acumulados)
            {
                $min_consumos_acumulados = $consumo_energia_neto_sensor_asociado_total;
            }
            if ($consumo_real_compra_energia_total < $min_consumos_acumulados)
            {
                $min_consumos_acumulados = $consumo_real_compra_energia_total;
            }
            if ($coste_desvio_ponderado_sensor_asociado_total < $min_coste_desvio_ponderado_acumulado_sensor_asociado)
            {
                $min_coste_desvio_ponderado_acumulado_sensor_asociado = $coste_desvio_ponderado_sensor_asociado_total;
            }

            // Timestamp para las gráficas
            $timestamp_fecha_hora_desvio_ponderado_compra_energia = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_desvio_ponderado_compra_energia_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_desvio_ponderado_compra_energia -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior !== NULL))
            {
                $segundos_entre_desvios_ponderados_compra_energia = ($timestamp_fecha_hora_desvio_ponderado_compra_energia - $timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior) / 1000;
                if ($segundos_entre_desvios_ponderados_compra_energia > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_consumos_energia_brutos_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_consumos_energia_netos_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_consumos_reales_compra_energia->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_consumos_energia_brutos_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_consumos_energia_netos_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_consumos_reales_acumulados_compra_energia->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_costes_desvios_ponderados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                    $datos_costes_desvios_ponderados_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_desvio_ponderado_compra_energia_anterior = $timestamp_fecha_hora_desvio_ponderado_compra_energia;
            $numero_puntos_seguidos_grafica += 1;

            // Se añaden los valores a las gráficas
            $datos_consumos_energia_brutos_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_energia_bruto_sensor_asociado);
            $datos_consumos_energia_netos_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_energia_neto_sensor_asociado);
            $datos_consumos_reales_compra_energia->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_real_compra_energia);
            $datos_consumos_energia_brutos_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_energia_bruto_sensor_asociado_total);
            $datos_consumos_energia_netos_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_energia_neto_sensor_asociado_total);
            $datos_consumos_reales_acumulados_compra_energia->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $consumo_real_compra_energia_total);
            $datos_costes_desvios_ponderados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $coste_desvio_ponderado_sensor_asociado);
            $datos_costes_desvios_ponderados_acumulados_sensor_asociado->anyade_tupla_pareja_datos($timestamp_fecha_hora_desvio_ponderado_compra_energia, $coste_desvio_ponderado_sensor_asociado_total);

            // Mapa de calor de desvíos ponderados de costes
            $cadena_fecha_hora_desvio_ponderado_compra_energia_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_desvio_ponderado_compra_energia_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
            $fecha_hora_desvio_ponderado_compra_energia_local = convierte_cadena_a_fecha($cadena_fecha_hora_desvio_ponderado_compra_energia_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $zona_horaria);
            $valores_mapa_calor_costes_desvios_ponderados_sensor_asociado->anyade_valor_fecha_hora($fecha_hora_desvio_ponderado_compra_energia_local, $coste_desvio_ponderado_sensor_asociado);
        }

        // Gráficas de consumos
        $grafica_consumos = new VectorDatos();
        $grafica_consumos->anyade_dato($datos_consumos_energia_netos_sensor_asociado->dame_datos());
        $grafica_consumos->anyade_dato($datos_consumos_energia_brutos_sensor_asociado->dame_datos());
        $grafica_consumos->anyade_dato($datos_consumos_reales_compra_energia->dame_datos());
        $grafica_consumos_acumulados = new VectorDatos();
        $grafica_consumos_acumulados->anyade_dato($datos_consumos_energia_netos_acumulados_sensor_asociado->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_consumos_energia_brutos_acumulados_sensor_asociado->dame_datos());
        $grafica_consumos_acumulados->anyade_dato($datos_consumos_reales_acumulados_compra_energia->dame_datos());

        // Gráficas de costes de desvío ponderados
        $grafica_costes_desvios_ponderados = new VectorDatos();
        $grafica_costes_desvios_ponderados->anyade_dato($datos_costes_desvios_ponderados_sensor_asociado->dame_datos());
        $grafica_costes_desvios_ponderados_acumulados = new VectorDatos();
        $grafica_costes_desvios_ponderados_acumulados->anyade_dato($datos_costes_desvios_ponderados_acumulados_sensor_asociado->dame_datos());

        // Etiquetas
        $etiquetas_graficas_consumos = new VectorDatos();
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo neto del sensor")." (".$nombre_sensor_hijo.")");
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo bruto del sensor")." (".$nombre_sensor_hijo.")");
        $etiquetas_graficas_consumos->anyade_etiqueta($idiomas->_("Consumo real de compra de energía")." (".$nombre_sensor.")");
        $etiquetas_sensor_hijo = new VectorDatos();
        $etiquetas_sensor_hijo->anyade_etiqueta($nombre_sensor_hijo);

        // Unidades de medida
        $unidad_medida_consumo = $idiomas->_("kWh");
        $unidad_medida_coste = $_SESSION["moneda"];

        // Días y datos de mapas de calor
        // (Nota: Si sólo hay 1 valor, el mapa de calor dibuja el último color de la escala)
        $dias_mapa_calor_costes_desvios_ponderados = $valores_mapa_calor_costes_desvios_ponderados_sensor_asociado->dame_dias();
        $datos_mapa_calor_costes_desvios_ponderados = $valores_mapa_calor_costes_desvios_ponderados_sensor_asociado->dame_datos();

        // Tabla de consumos y costes de desvíos ponderados totales
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES_COMPRA_ENERGIA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES_COMPRA_ENERGIA),
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Consumo neto del sensor"),
            $idiomas->_("Consumo bruto del sensor"),
            $idiomas->_("Consumo real de compra de energía"),
            $idiomas->_("Coste de desvío ponderado")
        );
        $titulo_tabla_consumos_coste_desvio_ponderado_totales = $idiomas->_("Consumos y coste de desvío ponderado totales");
        $tabla_consumos_coste_desvio_ponderado_totales = new TablaDatos(
            "tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia",
            $titulo_tabla_consumos_coste_desvio_ponderado_totales,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_consumos_coste_desvio_ponderado_totales->anyade_cabecera("", $cabecera_tabla);

        // Fila de la tabla de consumos y costes de desvíos ponderados totales
        $cadena_consumo_energia_neto_sensor_asociado_total = formatea_numero($consumo_energia_neto_sensor_asociado_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_consumo_energia_bruto_sensor_asociado_total = formatea_numero($consumo_energia_bruto_sensor_asociado_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_consumo_real_compra_energia_total = formatea_numero($consumo_real_compra_energia_total, 2, false)." ".$unidad_medida_consumo;
        $cadena_coste_desvio_ponderado_sensor_asociado_total = formatea_numero($coste_desvio_ponderado_sensor_asociado_total, 2, false)." ".$unidad_medida_coste;
        $params_fila_consumos_coste_desvio_ponderado_totales = array(
            "texto_eliminar_valor_xml_1" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_2" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_3" => " ".$unidad_medida_consumo,
            "texto_eliminar_valor_xml_4" => " ".$unidad_medida_coste);
        $fila_tabla_consumos_coste_desvio_ponderado_totales = array(
            $cadena_consumo_energia_neto_sensor_asociado_total,
            $cadena_consumo_energia_bruto_sensor_asociado_total,
            $cadena_consumo_real_compra_energia_total,
            $cadena_coste_desvio_ponderado_sensor_asociado_total);
        $tabla_consumos_coste_desvio_ponderado_totales->anyade_fila("", $fila_tabla_consumos_coste_desvio_ponderado_totales, $params_fila_consumos_coste_desvio_ponderado_totales);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tabla_consumos_coste_desvio_ponderado_totales" => $tabla_consumos_coste_desvio_ponderado_totales->dame_tabla(),
            "etiquetas_graficas_consumos" => $etiquetas_graficas_consumos->dame_datos(),
            "etiquetas_sensor_hijo" => $etiquetas_sensor_hijo->dame_datos(),
            "grafica_consumos" => $grafica_consumos->dame_datos(),
            "grafica_consumos_acumulados" => $grafica_consumos_acumulados->dame_datos(),
            "grafica_costes_desvios_ponderados" => $grafica_costes_desvios_ponderados->dame_datos(),
            "grafica_costes_desvios_ponderados_acumulados" => $grafica_costes_desvios_ponderados_acumulados->dame_datos(),
            "min_consumos" => $min_consumos,
            "min_consumos_acumulados" => $min_consumos_acumulados,
            "min_coste_desvio_ponderado" => $min_coste_desvio_ponderado_sensor_asociado,
            "min_coste_desvio_ponderado_acumulado" => $min_coste_desvio_ponderado_acumulado_sensor_asociado,
            "max_consumos" => $max_consumos,
            "max_consumos_acumulados" => $max_consumos_acumulados,
            "max_coste_desvio_ponderado" => $max_coste_desvio_ponderado_sensor_asociado,
            "max_coste_desvio_ponderado_acumulado" => $max_coste_desvio_ponderado_acumulado_sensor_asociado,
            "dias_mapa_calor_costes_desvios_ponderados" => $dias_mapa_calor_costes_desvios_ponderados,
            "datos_mapa_calor_costes_desvios_ponderados" => $datos_mapa_calor_costes_desvios_ponderados,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_desvios_compra_energia_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_DESVIOS_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_DESVIOS_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_desvios_ponderados_compra_energia_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS_PONDERADOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_desvios_compra_energia_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_DESVIOS_TOTALES:
            {
                $descripcion = "Tabla de consumos y desvíos totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS:
            {
                $descripcion = "Gráfica de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de consumos acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO:
            {
                $descripcion = "Gráfica de desvíos de consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_DESVIOS_CONSUMO_ACUMULADOS:
            {
                $descripcion = "Gráfica de desvíos de consumo acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_DESVIOS_CONSUMO:
            {
                $descripcion = "Mapa de calor de desvíos de consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS:
            {
                $descripcion = "Gráfica de costes de desvíos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de costes de desvíos acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS:
            {
                $descripcion = "Mapa de calor de costes de desvíos";
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


    function dame_descripcion_elemento_informe_smartmeter_desvios_ponderados_compra_energia_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_TABLA_CONSUMOS_COSTE_DESVIO_PONDERADO_TOTALES:
            {
                $descripcion = "Tabla de consumos y coste de desvío ponderado totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS:
            {
                $descripcion = "Gráfica de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_CONSUMOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de consumos acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS:
            {
                $descripcion = "Gráfica de costes de desvíos ponderados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_GRAFICA_COSTES_DESVIOS_PONDERADOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de costes de desvíos ponderados acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ESPANYA_MAPA_CALOR_COSTES_DESVIOS_PONDERADOS:
            {
                $descripcion = "Mapa de calor de costes de desvíos ponderados";
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


    function dame_html_informe_tipo_smartmeter_prevision_compra_energia_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-prevision-compra-energia'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-prevision-compra-energia' hidden>
                        <div class='grafica100' id='grafica-consumos-estimados-prevision-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-consumos-estimados-prevision-compra-energia'></div>
                        <div class='grafica100' id='grafica-consumos-perfil-horario-prevision-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-consumos-perfil-horario-semanales-prevision-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-consumos-perfil-horario-prevision-compra-energia'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Previsión de compra de energía (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-prevision-compra-energia-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-prevision-compra-energia-1'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-estimados-prevision-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-consumos-estimados-prevision-compra-energia'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Previsión de compra de energía (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-prevision-compra-energia-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-prevision-compra-energia-2'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-perfil-horario-prevision-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-consumos-perfil-horario-semanales-prevision-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-consumos-perfil-horario-prevision-compra-energia'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_desvios_compra_energia_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-desvios-compra-energia'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-desvios-compra-energia' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-desvios-totales-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-consumos-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-consumos-acumulados-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-desvios-consumo-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-costes-desvios-desvios-compra-energia'></div>
                        <div class='grafica100' id='grafica-costes-desvios-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-costes-desvios-desvios-compra-energia'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Desvíos de compra de energía (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-compra-energia-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-compra-energia-1'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-desvios-totales-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-acumulados-desvios-compra-energia'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos de compra de energía (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-compra-energia-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-compra-energia-2'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-desvios-consumo-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-desvios-consumo-desvios-compra-energia-1'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos de compra de energía (3)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-compra-energia-3'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-compra-energia-3'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-desvios-consumo-desvios-compra-energia-2'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos de compra de energía (4)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-compra-energia-4'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-compra-energia-4'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-desvios-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-desvios-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-costes-desvios-desvios-compra-energia-1'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos de compra de energía (5)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-compra-energia-5'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-compra-energia-5'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-costes-desvios-desvios-compra-energia-2'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-desvios-ponderados-compra-energia'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-desvios-ponderados-compra-energia' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100' id='grafica-consumos-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100' id='grafica-consumos-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100' id='grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100' id='grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='mapa-calor100' id='mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Desvíos ponderados de compra de energía (1)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-ponderados-compra-energia-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-ponderados-compra-energia-1'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos ponderados de compra de energía (2)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-ponderados-compra-energia-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-ponderados-compra-energia-2'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia-1'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Desvíos ponderados de compra de energía (3)'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-desvios-ponderados-compra-energia-3'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_compra_energia(TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-desvios-ponderados-compra-energia-3'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia-2'></div>
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


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia_Espanya(
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
                        <div class='tabla-datos100' id='".$prefijo_elemento."contenedor-tabla-consumos-desvios-totales-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-desvios-consumo-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-desvios-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-costes-desvios-desvios-compra-energia'></div>
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
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-desvios-totales-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-desvios-consumo-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-desvios-consumo-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-desvios-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-acumulados-desvios-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-costes-desvios-desvios-compra-energia'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya(
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
                        <div class='tabla-datos100' id='".$prefijo_elemento."contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
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
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-coste-desvio-ponderado-totales-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-desvios-ponderados-acumulados-desvios-ponderados-compra-energia'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-costes-desvios-ponderados-desvios-ponderados-compra-energia'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe,
        &$filas_valores_sensores)
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
        $parametros_informe["intervalo_valores"] = INTERVALO_VALORES_HORA;
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $filas_valores_sensor = dame_filas_valores_sensores_elemento_plantilla_informe($parametros_informe, $filas_valores_sensores);
        $datos_elemento = dame_desvios_compra_energia_sensor_Espanya($parametros_informe, $filas_valores_sensor);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if (($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO) ||
            ($parametros_tipo_elemento["id_sensor_hijo"] == ID_NINGUNO))
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["id_sensor_hijo"] = $parametros_tipo_elemento["id_sensor_hijo"];
        $nombre_sensor_hijo = dame_nombre_sensor($parametros_tipo_elemento["id_sensor_hijo"]);
        $parametros_informe["nombre_sensor_hijo"] = $nombre_sensor_hijo;
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_desvios_ponderados_compra_energia_sensor_Espanya($parametros_informe);
        return ($datos_elemento);
    }
?>
