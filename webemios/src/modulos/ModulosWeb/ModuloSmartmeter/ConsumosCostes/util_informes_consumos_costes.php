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
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/util_consumos_costes_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');


    //
    // Funciones de información de consumos y costes (generales para todas las clases de sensor)
    //


    function dame_consumos_costes_sensores_generales($parametros, $filas_valores_sensores)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $agregacion = $parametros["agregacion"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores as $id_sensor)
        {
            if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
            }
        }

        // Se recupera si aplica el ratio
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, CAMPO_TODOS);
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
        $numero_sensor_consumos = 0;
        $numero_sensor_costes = 0;
        $grafica_consumos = new VectorDatos();
        $grafica_consumos_acumulados = new VectorDatos();
        $grafica_costes = new VectorDatos();
        $grafica_costes_acumulados = new VectorDatos();
        $grafica_precios = new VectorDatos();
        $nombres_sensores_consumos = new VectorDatos();
        $nombres_sensores_costes = new VectorDatos();
        $max_consumos_sensores = 0.0;
        $max_costes_sensores = 0.0;
        $max_precios_sensores = 0.0;
        $max_consumos_totales = 0.0;
        $max_costes_totales = 0.0;
        $cadena_fecha_hora_inicio_consumos_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_consumos_base_datos_utc = NULL;

        // Campo de consumo
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
        }
        $unidad_medida_precio = $_SESSION["moneda"]."/".$unidad_medida_consumo;

        // Tablas de consumos y costes máximos y mínimos
        $titulo_columna_sensor = $idiomas->_("Sensor");
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMOS_COSTES_MAXIMOS_MINIMOS,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_CONSUMOS_COSTES_MAXIMOS_MINIMOS),
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $titulo_columna_sensor,
            $idiomas->_("Máximo"),
            $idiomas->_("Mínimo"),
            $idiomas->_("Media por hora"),
            $idiomas->_("Total")
        );

        // Tabla de consumos máximos y mínimos
        $titulo_tabla_consumos_maximos_minimos = $idiomas->_("Consumos máximos y mínimos");
        $tabla_consumos_maximos_minimos = new TablaDatos(
            "tabla-consumos-maximos-minimos-consumos-costes-generales",
            $titulo_tabla_consumos_maximos_minimos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_consumos_maximos_minimos->anyade_cabecera("", $cabecera_tabla);

        // Tabla de costes máximos y mínimos
        $titulo_tabla_costes_maximos_minimos = $idiomas->_("Costes máximos y mínimos");
        $tabla_costes_maximos_minimos = new TablaDatos(
            "tabla-costes-maximos-minimos-consumos-costes-generales",
            $titulo_tabla_costes_maximos_minimos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_costes_maximos_minimos->anyade_cabecera("", $cabecera_tabla);

        // Tabla de precios máximos y mínimos
        $params_tabla_precios = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_PRECIOS_MAXIMOS_MINIMOS,
            "generar_valores_xml" => true
        );
        $cabecera_tabla_precios = array(
            $titulo_columna_sensor,
            $idiomas->_("Máximo"),
            $idiomas->_("Mínimo"),
            $idiomas->_("Media"),
        );
        $titulo_tabla_precios_maximos_minimos = $idiomas->_("Precios máximos y mínimos");
        $tabla_precios_maximos_minimos = new TablaDatos(
            "tabla-precios-maximos-minimos-consumos-costes-generales",
            $titulo_tabla_precios_maximos_minimos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_precios
        );
        $tabla_precios_maximos_minimos->anyade_cabecera("", $cabecera_tabla_precios);

        // Identificadores de los sensores 'originales' (se recuperan aquí por si luego hay agregación se cambian los ids de sensores para las etiquetas)
        $ids_sensores_originales = $ids_sensores;

        // Agregación de consumos y costes (si la hay)
        if ($agregacion != AGREGACION_NINGUNA)
        {
            $info_sensores_agregacion = dame_info_sensores_agregacion_consumos_costes(
                $id_ratio,
                $agregacion,
                $ids_sensores,
                $nombres_sensores,
                $clase_sensor,
                $campo_consumo,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $filas_valores_sensores);
            if ($info_sensores_agregacion["hay_datos"] == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
            else
            {
                $ids_sensores = $info_sensores_agregacion["ids_sensores"];
                $nombres_sensores = $info_sensores_agregacion["nombres_sensores"];
                $filas_valores_sensores = $info_sensores_agregacion["filas_valores_sensores"];
                $numeros_sensores_valores_agregaciones_campos = $info_sensores_agregacion["numeros_sensores_valores_agregaciones_campos"];
            }
        }

        // Flag de número máximo de sensores para dibujado de gráficas superado
        $limite_sensores_graficas_superado = false;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, NULL);

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Flag de datos de costes
            $hay_datos_costes = false;

            // Máximos y mínimos
            $max_consumo = (float) -INF;
            $max_coste = (float) -INF;
            $max_precio = (float) -INF;
            $cadena_fecha_hora_max_consumo_base_datos_utc = "";
            $cadena_fecha_hora_max_coste_base_datos_utc = "";
            $cadena_fecha_hora_max_precio_base_datos_utc = "";
            $min_consumo = (float) INF;
            $min_coste = (float) INF;
            $min_precio = (float) INF;
            $cadena_fecha_hora_min_consumo_base_datos_utc = "";
            $cadena_fecha_hora_min_coste_base_datos_utc = "";
            $cadena_fecha_hora_min_precio_base_datos_utc = "";

            // Horas y totales
            $horas_totales = NULL;
            $consumo_total = NULL;
            $coste_total = NULL;
            $precio_total = NULL;

            // Filas de valores del sensor (si no hay datos para el sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Número máximo de sensores para dibujado de gráficas
            if ($numero_sensor_consumos == NUMERO_MAXIMO_SENSORES_GRAFICAS_CONSUMOS_COSTES_GENERALES)
            {
                $limite_sensores_graficas_superado = true;

                // Se eliminan todos los datos de las gráficas (no se van a dibujar)
                $grafica_consumos = new VectorDatos();
                $grafica_consumos_acumulados = new VectorDatos();
                $grafica_costes = new VectorDatos();
                $grafica_costes_acumulados = new VectorDatos();
                $grafica_precios = new VectorDatos();
            }
            if ($limite_sensores_graficas_superado == false)
            {
                $datos_sensor_consumos = new VectorDatos();
                $datos_sensor_consumos_acumulados = new VectorDatos();
                $datos_sensor_costes = new VectorDatos();
                $datos_sensor_costes_acumulados = new VectorDatos();
                $datos_sensor_precios = new VectorDatos();
            }

            // Se recupera la información del ratio (si aplica)
            if (($aplicar_ratio == true) && ($id_sensor > 0))
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Se recorren las filas de valores
            $timestamp_fecha_hora_consumo_anterior_utc = NULL;
            $timestamp_fecha_hora_coste_anterior_utc = NULL;
            $numero_puntos_seguidos_grafica_consumo = 0;
            $numero_puntos_seguidos_grafica_coste = 0;
            foreach ($filas_valores_sensor as $fila_valores_sensor)
            {
                // Fecha
                $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];

                // Valor del ratio
                if ($aplicar_ratio == true)
                {
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, true);
                }

                // Fecha y consumo (si no hay consumo se ignora la fila)
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $consumo = $fila_valores_sensor[$campo_consumo];
                if ($consumo !== NULL)
                {
                    $consumo = (float) $consumo;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_valor($valor_ratio, $consumo);
                    }
                }
                if ($consumo === NULL)
                {
                    continue;
                }

                // Suma de consumos, máximos y mínimmos
                if ($horas_totales === NULL)
                {
                    $horas_totales = 0.0;
                }
                $horas_totales += $fila_valores_sensor["horas"];

                // Consumo
                if ($consumo > $max_consumos_sensores)
                {
                    $max_consumos_sensores = $consumo;
                }
                if ($consumo > $max_consumo)
                {
                    $max_consumo = $consumo;
                    $cadena_fecha_hora_max_consumo_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($consumo < $min_consumo)
                {
                    $min_consumo = $consumo;
                    $cadena_fecha_hora_min_consumo_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                if ($consumo_total == NULL)
                {
                    $consumo_total = 0.0;
                }
                $consumo_total += $consumo;

                // Fechas de inicio y fin de consumos
                if ($cadena_fecha_hora_inicio_consumos_base_datos_utc === NULL)
                {
                    $cadena_fecha_hora_inicio_consumos_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                $cadena_fecha_hora_fin_consumos_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

                // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
                if ($limite_sensores_graficas_superado == false)
                {
                    // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                    $timestamp_fecha_hora_consumo_coste_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $timestamp_fecha_hora_consumo_coste_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
                    if (($numero_puntos_seguidos_grafica_consumo > 1) &&
                        ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_consumo_anterior_utc !== NULL))
                    {
                        $segundos_entre_consumos = ($timestamp_fecha_hora_consumo_coste_utc - $timestamp_fecha_hora_consumo_anterior_utc) / 1000;
                        if ($segundos_entre_consumos > $segundos_maximos_entre_valores_grafica)
                        {
                            $numero_puntos_seguidos_grafica_consumo = 0;
                            $datos_sensor_consumos->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_anterior_utc + 1, NULL);
                            $datos_sensor_consumos_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_anterior_utc + 1, NULL);
                        }
                    }
                    $timestamp_fecha_hora_consumo_anterior_utc = $timestamp_fecha_hora_consumo_coste_utc;
                    $numero_puntos_seguidos_grafica_consumo += 1;

                    // Se añade el consumo
                    switch ($agregacion)
                    {
                        case AGREGACION_NINGUNA:
                        {
                            $datos_sensor_consumos->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_coste_utc, $consumo);
                            $datos_sensor_consumos_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_coste_utc, $consumo_total);
                            break;
                        }
                        case AGREGACION_SUMA:
                        case AGREGACION_MEDIA:
                        {
                            $tooltips_agregacion = dame_tooltips_agregacion_sensores(
                                NULL,
                                $cadena_fecha_hora_base_datos_utc,
                                $consumo,
                                $consumo_total,
                                $unidad_medida_consumo,
                                NULL,
                                $fila_valores_sensor["numero_sensores_sin_consumo"]);

                            $datos_sensor_consumos->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_consumo_coste_utc,
                                $consumo,
                                $tooltips_agregacion["tooltip_valor"]);
                            $datos_sensor_consumos_acumulados->anyade_tupla_pareja_datos_etiqueta(
                                $timestamp_fecha_hora_consumo_coste_utc,
                                $consumo_total,
                                $tooltips_agregacion["tooltip_suma_valores"]);
                            break;
                        }
                    }
                }

                // Coste (si no hay coste se ignora)
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $coste = $fila_valores_sensor['coste'];
                if ($coste !== NULL)
                {
                    $coste = (float) $coste;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_valor($valor_ratio, $coste);
                    }
                }
                if ($coste !== NULL)
                {
                    // Flag de datos de costes
                    $hay_datos_costes = true;

                    // Sumas de costes, máximos y mínimos
                    if ($coste > $max_costes_sensores)
                    {
                        $max_costes_sensores = $coste;
                    }
                    if ($coste > $max_coste)
                    {
                        $max_coste = $coste;
                        $cadena_fecha_hora_max_coste_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                    }
                    if ($coste < $min_coste)
                    {
                        $min_coste = $coste;
                        $cadena_fecha_hora_min_coste_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                    }
                    if ($coste_total === NULL)
                    {
                        $coste_total = 0.0;
                    }
                    $coste_total += $coste;

                    // Precio
                    if (($coste == 0) || ($consumo == 0))
                    {
                        $precio = NULL;
                    }
                    else
                    {
                        $precio = $coste / $consumo;
                        if ($precio > $max_precios_sensores)
                        {
                            $max_precios_sensores = $precio;
                        }
                        if ($precio > $max_precio)
                        {
                            $max_precio = $precio;
                            $cadena_fecha_hora_max_precio_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                        }
                        if ($precio < $min_precio)
                        {
                            $min_precio = $precio;
                            $cadena_fecha_hora_min_precio_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                        }
                        if ($precio_total === NULL)
                        {
                            $precio_total = 0.0;
                        }
                        $precio_total += $precio;
                    }

                    // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
                    if ($limite_sensores_graficas_superado == false)
                    {
                        // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                        if (($numero_puntos_seguidos_grafica_coste > 1) &&
                            ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_coste_anterior_utc !== NULL))
                        {
                            $segundos_entre_costes = ($timestamp_fecha_hora_consumo_coste_utc - $timestamp_fecha_hora_coste_anterior_utc) / 1000;
                            if ($segundos_entre_costes > $segundos_maximos_entre_valores_grafica)
                            {
                                $numero_puntos_seguidos_grafica_coste = 0;
                                $datos_sensor_costes->anyade_tupla_pareja_datos($timestamp_fecha_hora_coste_anterior_utc + 1, NULL);
                                $datos_sensor_costes_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_coste_anterior_utc + 1, NULL);
                                $datos_sensor_precios->anyade_tupla_pareja_datos($timestamp_fecha_hora_coste_anterior_utc + 1, NULL);
                            }
                        }
                        $timestamp_fecha_hora_coste_anterior_utc = $timestamp_fecha_hora_consumo_coste_utc;
                        $numero_puntos_seguidos_grafica_coste += 1;

                        // Se añade el coste
                        switch ($agregacion)
                        {
                            case AGREGACION_NINGUNA:
                            {
                                $datos_sensor_costes->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_coste_utc, $coste);
                                $datos_sensor_costes_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_coste_utc, $coste_total);
                                break;
                            }
                            case AGREGACION_SUMA:
                            case AGREGACION_MEDIA:
                            {
                                $tooltips_agregacion = dame_tooltips_agregacion_sensores(
                                    NULL,
                                    $cadena_fecha_hora_base_datos_utc,
                                    $coste,
                                    $coste_total,
                                    $unidad_medida_coste,
                                    NULL,
                                    $fila_valores_sensor["numero_sensores_sin_coste"]);

                                $datos_sensor_costes->anyade_tupla_pareja_datos_etiqueta(
                                    $timestamp_fecha_hora_consumo_coste_utc,
                                    $coste,
                                    $tooltips_agregacion["tooltip_valor"]);
                                $datos_sensor_costes_acumulados->anyade_tupla_pareja_datos_etiqueta(
                                    $timestamp_fecha_hora_consumo_coste_utc,
                                    $coste_total,
                                    $tooltips_agregacion["tooltip_suma_valores"]);
                                break;
                            }
                        }

                        // Se añade el precio
                        $datos_sensor_precios->anyade_tupla_pareja_datos($timestamp_fecha_hora_consumo_coste_utc, $precio);
                    }
                }
            }

            // Si no hay consumo se ignora el sensor
            if ($consumo_total === NULL)
            {
                continue;
            }

            // Coste y totales
            if ($consumo_total <> 0.0)
            {
                $coste_sensor = 100.0 * ($coste_total / $consumo_total);
            }
            else
            {
                $coste_sensor = 0.0;
            }
            if ($consumo_total > $max_consumos_totales)
            {
                $max_consumos_totales = $consumo_total;
            }
            if ($coste_total > $max_costes_totales)
            {
                $max_costes_totales = $coste_total;
            }

            // Granularidad
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_CUARTOHORA:
                {
                    $granularidad = GRANULARIDAD_CUARTOHORARIA;
                    break;
                }
                default:
                {
                    $granularidad = GRANULARIDAD_HORARIA;
                    break;
                }
            }

            // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
            if ($limite_sensores_graficas_superado == false)
            {
                $grafica_consumos->anyade_dato($datos_sensor_consumos->dame_datos());
                $grafica_consumos_acumulados->anyade_dato($datos_sensor_consumos_acumulados->dame_datos());
            }

            // Formatos de fecha
            $formato_fecha_origen = FORMATO_FECHA_HORA_BASE_DATOS;
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                case INTERVALO_VALORES_SEMANA:
                case INTERVALO_VALORES_MES:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_local"];
                    break;
                }
                default:
                {
                    $formato_fecha_destino = $_SESSION["formato_fecha_hora_local"];
                    break;
                }
            }

            // Se añade la fila de la tabla de consumos máximos, mínimos y media por hora
            $media_consumo_por_hora = $consumo_total / $horas_totales;
            $sensor_fila_tabla_consumos_maximos_minimos = htmlspecialchars($nombre_sensor, ENT_QUOTES);
            $cadena_fecha_hora_max_consumo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_max_consumo_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_max_consumo_local_local = convierte_formato_fecha($cadena_fecha_hora_max_consumo_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $cadena_fecha_hora_min_consumo_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_min_consumo_base_datos_utc, $formato_fecha_origen, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_min_consumo_local_local = convierte_formato_fecha($cadena_fecha_hora_min_consumo_base_datos_local, $formato_fecha_origen, $formato_fecha_destino);
            $maximo_fila_tabla_consumos_maximos_minimos = formatea_numero($max_consumo, 2)." ".$unidad_medida_consumo." (".$cadena_fecha_hora_max_consumo_local_local.")";
            $minimo_fila_tabla_consumos_maximos_minimos = formatea_numero($min_consumo, 2)." ".$unidad_medida_consumo." (".$cadena_fecha_hora_min_consumo_local_local.")";
            $media_horaria_fila_tabla_consumos_maximos_minimos = formatea_numero($media_consumo_por_hora, 2)." ".$unidad_medida_consumo;
            $total_fila_tabla_consumos_maximos_minimos = formatea_numero($consumo_total, 2)." ".$unidad_medida_consumo;
            $fila_tabla_consumos_maximos_minimos = array(
                $sensor_fila_tabla_consumos_maximos_minimos,
                $maximo_fila_tabla_consumos_maximos_minimos,
                $minimo_fila_tabla_consumos_maximos_minimos,
                $media_horaria_fila_tabla_consumos_maximos_minimos,
                $total_fila_tabla_consumos_maximos_minimos);
            $params_fila_consumos_maximos_minimos = array(
                "texto_eliminar_valor_xml_1" => " ".$unidad_medida_consumo." (".$cadena_fecha_hora_max_consumo_local_local.")",
                "texto_eliminar_valor_xml_2" => " ".$unidad_medida_consumo." (".$cadena_fecha_hora_min_consumo_local_local.")",
                "texto_eliminar_valor_xml_3" => " ".$unidad_medida_consumo,
                "texto_eliminar_valor_xml_4" => " ".$unidad_medida_consumo);
            $tabla_consumos_maximos_minimos->anyade_fila("", $fila_tabla_consumos_maximos_minimos, $params_fila_consumos_maximos_minimos);

            // Se incrementa el número de sensor de consumos
            $numero_sensor_consumos++;

            // Si hay datos de coste del sensor
            if ($coste_total > 0.0)
            {
                // Sólo se guardan los datos de las gráficas si no se ha superado el número máximo de sensores par el dibujado de gráficas
                if ($limite_sensores_graficas_superado == false)
                {
                    $grafica_costes->anyade_dato($datos_sensor_costes->dame_datos());
                    $grafica_costes_acumulados->anyade_dato($datos_sensor_costes_acumulados->dame_datos());
                    $grafica_precios->anyade_dato($datos_sensor_precios->dame_datos());
                }

                // Se añade la fila de la tabla de costes máximos, mínimos y media por hora
                $media_coste_por_hora = $coste_total / $horas_totales;
                $sensor_tabla_costes_maximos_minimos = htmlspecialchars($nombre_sensor, ENT_QUOTES);
                $cadena_fecha_hora_max_coste_local_utc = convierte_formato_fecha($cadena_fecha_hora_max_coste_base_datos_utc, $formato_fecha_origen, $formato_fecha_destino);
                $cadena_fecha_hora_max_coste_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_max_coste_local_utc, $formato_fecha_destino, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_min_coste_local_utc = convierte_formato_fecha($cadena_fecha_hora_min_coste_base_datos_utc, $formato_fecha_origen, $formato_fecha_destino);
                $cadena_fecha_hora_min_coste_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_min_coste_local_utc, $formato_fecha_destino, ZONA_HORARIA_UTC, $zona_horaria);
                $maximo_tabla_costes_maximos_minimos = formatea_numero($max_coste, 2, false)." ".$unidad_medida_coste." (".$cadena_fecha_hora_max_coste_local_local.")";
                $minimo_tabla_costes_maximos_minimos = formatea_numero($min_coste, 2, false)." ".$unidad_medida_coste." (".$cadena_fecha_hora_min_coste_local_local.")";
                $media_horaria_tabla_costes_maximos_minimos = formatea_numero($media_coste_por_hora, 2, false)." ".$unidad_medida_coste;
                $total_tabla_costes_maximos_minimos = formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste;
                $fila_tabla_costes_maximos_minimos = array(
                    $sensor_tabla_costes_maximos_minimos,
                    $maximo_tabla_costes_maximos_minimos,
                    $minimo_tabla_costes_maximos_minimos,
                    $media_horaria_tabla_costes_maximos_minimos,
                    $total_tabla_costes_maximos_minimos);
                $params_fila_costes_maximos_minimos = array(
                    "texto_eliminar_valor_xml_1" => " ".$unidad_medida_coste." (".$cadena_fecha_hora_max_coste_local_local.")",
                    "texto_eliminar_valor_xml_2" => " ".$unidad_medida_coste." (".$cadena_fecha_hora_min_coste_local_local.")",
                    "texto_eliminar_valor_xml_3" => " ".$unidad_medida_coste,
                    "texto_eliminar_valor_xml_4" => " ".$unidad_medida_coste);
                $tabla_costes_maximos_minimos->anyade_fila("", $fila_tabla_costes_maximos_minimos, $params_fila_costes_maximos_minimos);

                // Se añade la fila de la tabla de precios máximos, mínimos y medio
                $media_precio = $coste_total / $consumo_total;
                $sensor_fila_tabla_precios_maximos_minimos = htmlspecialchars($nombre_sensor, ENT_QUOTES);
                $cadena_fecha_hora_max_precio_local_utc = convierte_formato_fecha($cadena_fecha_hora_max_precio_base_datos_utc, $formato_fecha_origen, $formato_fecha_destino);
                $cadena_fecha_hora_max_precio_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_max_precio_local_utc, $formato_fecha_destino, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_min_precio_local_utc = convierte_formato_fecha($cadena_fecha_hora_min_precio_base_datos_utc, $formato_fecha_origen, $formato_fecha_destino);
                $cadena_fecha_hora_min_precio_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_min_precio_local_utc, $formato_fecha_destino, ZONA_HORARIA_UTC, $zona_horaria);
                $maximo_fila_tabla_precios_maximos_minimos = formatea_numero($max_precio, 4)." ".$unidad_medida_precio." (".$cadena_fecha_hora_max_precio_local_local.")";
                $minimo_fila_tabla_precios_maximos_minimos = formatea_numero($min_precio, 4)." ".$unidad_medida_precio." (".$cadena_fecha_hora_min_precio_local_local.")";
                $media_fila_tabla_precios_maximos_minimos = formatea_numero($media_precio, 4)." ".$unidad_medida_precio;
                $fila_tabla_precios_maximos_minimos = array(
                    $sensor_fila_tabla_precios_maximos_minimos,
                    $maximo_fila_tabla_precios_maximos_minimos,
                    $minimo_fila_tabla_precios_maximos_minimos,
                    $media_fila_tabla_precios_maximos_minimos);
                $params_fila_precios_maximos_minimos = array(
                    "texto_eliminar_valor_xml_1" => " ".$unidad_medida_precio." (".$cadena_fecha_hora_max_precio_local_local.")",
                    "texto_eliminar_valor_xml_2" => " ".$unidad_medida_precio." (".$cadena_fecha_hora_min_precio_local_local.")",
                    "texto_eliminar_valor_xml_3" => " ".$unidad_medida_precio,
                    "texto_eliminar_valor_xml_4" => " ".$unidad_medida_precio);
                $tabla_precios_maximos_minimos->anyade_fila("", $fila_tabla_precios_maximos_minimos, $params_fila_precios_maximos_minimos);

                // Se incrementa el número de sensor de costes
                $numero_sensor_costes++;
            }

            // Nombres de sensores
            $nombres_sensores_consumos->anyade_etiqueta($nombre_sensor);
            if ($hay_datos_costes == true)
            {
                $nombres_sensores_costes->anyade_etiqueta($nombre_sensor);
            }
        }

        // Se añaden los pies de tablas
        if ($agregacion == AGREGACION_NINGUNA)
        {
            $numero_sensores_consumo_pie_pagina = $numero_sensor_consumos;
            $numero_sensores_coste_pie_pagina = $numero_sensor_costes;
        }
        else
        {
            $numero_sensores_consumo_pie_pagina = $numeros_sensores_valores_agregaciones_campos[$campo_consumo]." (".$idiomas->_("agregados").")";
            $numero_sensores_coste_pie_pagina = $numeros_sensores_valores_agregaciones_campos[CAMPO_COSTE]." (".$idiomas->_("agregados").")";
        }
        $tabla_consumos_maximos_minimos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_consumo_pie_pagina);
        $tabla_costes_maximos_minimos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_coste_pie_pagina);
        $tabla_precios_maximos_minimos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensores_coste_pie_pagina);

        // Si no hay datos no se hace nada
        if ($numero_sensor_consumos == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Flag de datos de costes
        $hay_datos_costes = ($numero_sensor_costes > 0);

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Fechas de inicio y fin de consumos
        $cadena_fecha_hora_inicio_consumos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_consumos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
        $cadena_fecha_hora_fin_consumos_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_consumos_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

        $cadena_fecha_inicio_consumos_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_consumos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        $cadena_hora_inicio_consumos_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_consumos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
        $cadena_fecha_fin_consumos_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_consumos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        $cadena_hora_fin_consumos_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_consumos_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

        // Se recuperan las filas de comentarios si es necesario
        // - Nota: No se utiliza el horario semanal ni las fechas para mostrar todos los comentarios entre la fecha de inicio y fin del informe
        //   (puede ser que haya comentarios en periodos que no se visualicen en la gráfica pero que puedan ser relevantes)
        switch ($comentarios)
        {
            case COMENTARIOS_GRAFICA:
            case COMENTARIOS_GRAFICA_TABLA:
            {
                $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios($ids_sensores_originales);
                $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                    ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES,
                    $nombres_sensores_comentarios,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    NULL,
                    NULL,
                    NULL);
                $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                    $filas_comentarios,
                    false,
                    $milisegundos_desfase_zonas_horarias_cliente_local);
                break;
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES;
                    $parametros_origen_comentarios = NULL;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = $numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-smartmeter-consumos-costes-generales",
                    $filas_comentarios,
                    $ids_sensores,
                    $nombres_sensores_comentarios,
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripciones de los sensores
        $descripciones_sensores = dame_descripciones_sensores_informe($ids_sensores_originales);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "hay_datos_costes" => $hay_datos_costes,
            "limite_sensores_graficas_superado" => $limite_sensores_graficas_superado,
            "grafica_consumos" => $grafica_consumos->dame_datos(),
            "grafica_consumos_acumulados" => $grafica_consumos_acumulados->dame_datos(),
            "tabla_consumos_maximos_minimos" => $tabla_consumos_maximos_minimos->dame_tabla(),
            "grafica_costes" => $grafica_costes->dame_datos(),
            "grafica_costes_acumulados" => $grafica_costes_acumulados->dame_datos(),
            "tabla_costes_maximos_minimos" => $tabla_costes_maximos_minimos->dame_tabla(),
            "grafica_precios" => $grafica_precios->dame_datos(),
            "tabla_precios_maximos_minimos" => $tabla_precios_maximos_minimos->dame_tabla(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_consumos" => $cadena_fecha_inicio_consumos_local_local,
            "hora_inicio_consumos" => $cadena_hora_inicio_consumos_local_local,
            "fecha_fin_consumos" => $cadena_fecha_fin_consumos_local_local,
            "hora_fin_consumos" => $cadena_hora_fin_consumos_local_local,
            "max_consumo" => $max_consumos_sensores,
            "max_coste" => $max_costes_sensores,
            "max_precio" => $max_precios_sensores,
            "max_consumos_totales" => $max_consumos_totales,
            "max_costes_totales" => $max_costes_totales,
            "etiquetas_consumos" => $nombres_sensores_consumos->dame_datos(),
            "etiquetas_costes" => $nombres_sensores_costes->dame_datos(),
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste,
            "unidad_medida_precio" => $unidad_medida_precio,
            "descripciones_sensores" => $descripciones_sensores);
        return ($resultado);
    }


    function dame_consumos_costes_sensores_totales($parametros, $filas_valores_sensores)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $ids_sensores = $parametros["ids_sensores"];
        $nombres_sensores = $parametros["nombres_sensores"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
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

        // Se recupera si aplica el ratio
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, CAMPO_TODOS);
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

        // Variables
        $numero_sensor_consumos = 0;
        $numero_sensor_costes = 0;
        $grafica_consumos_totales = new VectorDatos();
        $grafica_costes_totales = new VectorDatos();
        $grafica_precios_medios = new VectorDatos();
        $datos_porcentajes_consumos = new VectorDatos();
        $datos_porcentajes_costes = new VectorDatos();
        $grafica_porcentajes_consumos = new VectorDatos();
        $grafica_porcentajes_costes = new VectorDatos();
        $nombres_sensores_consumos = new VectorDatos();
        $nombres_sensores_costes = new VectorDatos();
        $max_consumos_sensores = 0.0;
        $max_costes_sensores = 0.0;
        $max_consumos_totales = 0.0;
        $max_costes_totales = 0.0;
        $max_precios_medios = 0.0;
        $total_consumos = 0.0;
        $total_costes = 0.0;
        $info_consumos_totales = array();
        $info_costes_totales = array();

        // Campo de consumo
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
        }
        $unidad_medida_precio = $idiomas->_("cents").".".$_SESSION["moneda"]."/".$unidad_medida_consumo;

        // Tabla de consumos totales y porcentajes
        $params_tabla_consumos = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_CONSUMOS_CONSUMOS_COSTES_TOTALES,
            "generar_valores_xml" => true
        );
        $titulo_tabla_consumos = $idiomas->_("Consumos");
        $tabla_consumos = new TablaDatos(
            "tabla-consumos-consumos-costes-totales",
            $titulo_tabla_consumos,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_consumos
        );
        $cabecera_tabla_consumos = array(
            $idiomas->_("Sensor"),
            $idiomas->_("Total"),
            $idiomas->_("Porcentaje")
        );
        $tabla_consumos->anyade_cabecera("", $cabecera_tabla_consumos);

        // Tabla de costes totales y porcentajes
        $params_tabla_costes = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_COSTES_CONSUMOS_COSTES_TOTALES,
            "generar_valores_xml" => true
        );
        $titulo_tabla_costes = $idiomas->_("Costes");
        $tabla_costes = new TablaDatos(
            "tabla-costes-consumos-costes-totales",
            $titulo_tabla_costes,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_costes
        );
        $cabecera_tabla_costes = array(
            $idiomas->_("Sensor"),
            $idiomas->_("Total"),
            $idiomas->_("Porcentaje"),
            $idiomas->_("Precio medio")
        );
        $tabla_costes->anyade_cabecera("", $cabecera_tabla_costes);

        // Se recorren los sensores
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Consumo y coste totales
            $consumo_total = NULL;
            $coste_total = NULL;

            // Filas de valores del sensor (si no hay datos del sensor, no se muestra ni en gráficas ni en tablas)
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            if (count($filas_valores_sensor) == 0)
            {
                continue;
            }

            // Se recupera la información del ratio (si aplica)
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Se recorren las filas de valores
            foreach ($filas_valores_sensor as $fila_valores_sensor)
            {
                // Fecha de la fila
                $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];

                // Valor del ratio
                if ($aplicar_ratio == true)
                {
                    $valor_ratio = dame_valor_ratio_fecha($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, true);
                }

                // Consumo (si no hay consumo se ignora la fila)
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $consumo = $fila_valores_sensor[$campo_consumo];
                if ($consumo !== NULL)
                {
                    $consumo = (float) $consumo;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_valor($valor_ratio, $consumo);
                    }
                }
                if ($consumo === NULL)
                {
                    continue;
                }

                // Suma de consumos, máximos y mínimmos
                if ($consumo > $max_consumos_sensores)
                {
                    $max_consumos_sensores = $consumo;
                }
                if ($consumo_total === NULL)
                {
                    $consumo_total = 0.0;
                }
                $consumo_total += $consumo;

                // Coste (si no hay coste se ignora)
                // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
                $coste = $fila_valores_sensor['coste'];
                if ($coste !== NULL)
                {
                    $coste = (float) $coste;
                    if (($aplicar_ratio == true) && ($id_sensor > 0))
                    {
                        aplica_ratio_valor($valor_ratio, $coste);
                    }
                }
                if ($coste !== NULL)
                {
                    // Sumas de costes, máximos y mínimos
                    if ($coste > $max_costes_sensores)
                    {
                        $max_costes_sensores = $coste;
                    }
                    if ($coste_total === NULL)
                    {
                        $coste_total = 0.0;
                    }
                    $coste_total += $coste;
                }
            }

            // Si no hay datos de consumo se ignora el sensor
            if ($consumo_total === NULL)
            {
                continue;
            }

            // Máximo de consumos totales
            if ($consumo_total > $max_consumos_totales)
            {
                $max_consumos_totales = $consumo_total;
            }

            // Se añaden los datos a las gráficas (totales y porcentajes)
            $grafica_consumos_totales->anyade_tupla_dato($consumo_total);
            $datos_porcentajes_consumos->anyade_tupla_etiqueta_dato($nombre_sensor, $consumo_total);

            // Información de consumos totales
            $info_consumo_total = array(
                htmlspecialchars($nombre_sensor, ENT_QUOTES),
                $consumo_total);
            array_push($info_consumos_totales, $info_consumo_total);

            // Total de consumos y número de sensor de consumos
            $total_consumos += $consumo_total;
            $numero_sensor_consumos++;

            // Nombres de sensores de consumos
            $nombres_sensores_consumos->anyade_etiqueta($nombre_sensor);

            // Si hay costes
            if ($coste_total !== NULL)
            {
                if ($consumo_total > 0.0)
                {
                    $precio_medio = 100.0 * ($coste_total / $consumo_total);
                }
                else
                {
                    $precio_medio = 0.0;
                }

                // Maximos de costes totales y precios medios
                if ($coste_total > $max_costes_totales)
                {
                    $max_costes_totales = $coste_total;
                }
                if ($precio_medio > $max_precios_medios)
                {
                    $max_precios_medios = $precio_medio;
                }

                // Se añaden los datos a las gráficas (totales, precios medios y porcentajes)
                $grafica_costes_totales->anyade_tupla_dato($coste_total);
                $grafica_precios_medios->anyade_tupla_dato($precio_medio);
                $datos_porcentajes_costes->anyade_tupla_etiqueta_dato($nombre_sensor, $coste_total);

                // Información de costes totales
                $info_coste_total = array(
                    htmlspecialchars($nombre_sensor, ENT_QUOTES),
                    $coste_total,
                    $precio_medio);
                array_push($info_costes_totales, $info_coste_total);

                // Total de costes y número de sensor de costes
                $total_costes += $coste_total;
                $numero_sensor_costes++;

                // Nombres de sensores de costes
                $nombres_sensores_costes->anyade_etiqueta($nombre_sensor);
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_sensor_consumos == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se añaden los datos a las gráficas de porcentajes
        $grafica_porcentajes_consumos->anyade_dato($datos_porcentajes_consumos->dame_datos());
        $grafica_porcentajes_costes->anyade_dato($datos_porcentajes_costes->dame_datos());

        // Tablas de consumos y costes (totales y porcentajes)
        foreach ($info_consumos_totales as $info_consumo_total)
        {
            $porcentaje_consumo = ($info_consumo_total[1] * 100) / $total_consumos;
            $fila_tabla_consumos = array(
                $info_consumo_total[0],
                formatea_numero($info_consumo_total[1], 2)." ".$unidad_medida_consumo,
                formatea_numero($porcentaje_consumo, 2)." %");
            $params_fila_consumos = array("textos_eliminar_valores_xml" => array(
                " ".$unidad_medida_consumo,
                " %"));
            $tabla_consumos->anyade_fila("", $fila_tabla_consumos, $params_fila_consumos);
        }
        foreach ($info_costes_totales as $info_coste_total)
        {
            $porcentaje_coste = ($info_coste_total[1] * 100) / $total_costes;
            $fila_tabla_costes = array(
                $info_coste_total[0],
                formatea_numero($info_coste_total[1], 2, false)." ".$unidad_medida_coste,
                formatea_numero($porcentaje_coste, 2)." "."%",
                formatea_numero($info_coste_total[2], 4)." ".$unidad_medida_precio);
            $params_fila_costes = array("textos_eliminar_valores_xml" => array(
                " ".$unidad_medida_coste,
                " "."%",
                " ".$unidad_medida_precio));
            $tabla_costes->anyade_fila("", $fila_tabla_costes, $params_fila_costes);
        }

        // Se añade los pies de las tablas
        $tabla_consumos->anyade_pie($idiomas->_("Sensores").": ".$numero_sensor_consumos);
        $tabla_costes->anyade_pie($idiomas->_("Sensores").": ".$numero_sensor_costes);

        // Flag de datos de costes
        $hay_datos_costes = ($numero_sensor_costes > 0);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "hay_datos_costes" => $hay_datos_costes,
            "grafica_consumos_totales" => $grafica_consumos_totales->dame_datos(),
            "grafica_porcentajes_consumos" => $grafica_porcentajes_consumos->dame_datos(),
            "tabla_consumos" => $tabla_consumos->dame_tabla(),
            "grafica_costes_totales" => $grafica_costes_totales->dame_datos(),
            "grafica_porcentajes_costes" => $grafica_porcentajes_costes->dame_datos(),
            "grafica_precios_medios" => $grafica_precios_medios->dame_datos(),
            "tabla_costes" => $tabla_costes->dame_tabla(),
            "max_consumos_totales" => $max_consumos_totales,
            "max_costes_totales" => $max_costes_totales,
            "max_precios_medios" => $max_precios_medios,
            "etiquetas_consumos" => $nombres_sensores_consumos->dame_datos(),
            "etiquetas_costes" => $nombres_sensores_costes->dame_datos(),
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste,
            "unidad_medida_precio" => $unidad_medida_precio);
        return ($resultado);
    }


    function dame_consumos_costes_sensor_periodos($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $medicion = $parametros["medicion"];
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $cadena_fecha_hora_inicio_anterior_local_local = $parametros["fecha_hora_inicio_anterior"];
        $cadena_fecha_hora_inicio_posterior_local_local = $parametros["fecha_hora_inicio_posterior"];
        $cadena_fecha_inicio_anterior_local_local = $parametros["fecha_inicio_anterior"];
        $cadena_fecha_inicio_posterior_local_local = $parametros["fecha_inicio_posterior"];
        $numero_dias_periodo = $parametros["numero_dias_periodo"];
        $numero_dias_periodo_anterior = $parametros["numero_dias_periodo_anterior"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, CAMPO_TODOS);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
        }

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        //$minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        //$minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        //$milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Fechas iniciales de los periodos anterior y posterior
        
        //Comprobamos si tenemos la informacion de fecha y hora
        if($cadena_fecha_hora_inicio_posterior_local_local === NULL || $cadena_fecha_hora_inicio_posterior_local_local === NULL)
        {
            // Anyadimos la hora a la cadena de fecha 
            // TO DO ACCIONA : Quitamos las 06:00 como hora de GAS para acciona
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];
            
            if ($medicion == MEDICION_GAS && $nombre_cliente != 'Acciona')
            {
                $cadena_fecha_hora_inicio_anterior_local_local = $cadena_fecha_inicio_anterior_local_local.", 06:00:00";
                $cadena_fecha_hora_inicio_posterior_local_local = $cadena_fecha_inicio_posterior_local_local.", 06:00:00";
            }
            else{
                $cadena_fecha_hora_inicio_anterior_local_local = $cadena_fecha_inicio_anterior_local_local.", 00:00:00";
                $cadena_fecha_hora_inicio_posterior_local_local = $cadena_fecha_inicio_posterior_local_local.", 00:00:00";
            }
        }
        
        $fecha_hora_inicio_anterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_anterior_local_local, $_SESSION["formato_fecha_hora_local"],  $zona_horaria);
        $fecha_hora_inicio_posterior_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_inicio_anterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_inicio_anterior_local, ZONA_HORARIA_UTC);
        $fecha_hora_inicio_posterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_inicio_posterior_local, ZONA_HORARIA_UTC);
        
        // Horas de separacion de periodos (diferencia entre las fechas iniciales)
        $duracion_periodo_posterior = new DateInterval("P".$numero_dias_periodo."D");
        if ($numero_dias_periodo_anterior === NULL)
        {
            $duracion_periodo_anterior = $duracion_periodo_posterior;
        }
        else
        {
            $duracion_periodo_anterior = new DateInterval("P".$numero_dias_periodo_anterior."D");
        }
        $separacion_periodos = $fecha_hora_inicio_anterior_utc->diff($fecha_hora_inicio_posterior_utc);
        $horas_separacion_periodos = new DateInterval('PT'.($separacion_periodos->days * 24 + $separacion_periodos->h).'H');

        // Se calculan las fechas finales de los periodos anterior y posterior
        $intervalo_segundo = new DateInterval("PT1S");
        $fecha_hora_fin_anterior_local = clone $fecha_hora_inicio_anterior_local;
        $fecha_hora_fin_anterior_local->add($duracion_periodo_anterior);
        $fecha_hora_fin_anterior_local->sub($intervalo_segundo);
        $fecha_hora_fin_anterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_fin_anterior_local, ZONA_HORARIA_UTC);
        $fecha_hora_fin_posterior_local = clone $fecha_hora_inicio_posterior_local;
        $fecha_hora_fin_posterior_local->add($duracion_periodo_posterior);
        $fecha_hora_fin_posterior_local->sub($intervalo_segundo);
        $fecha_hora_fin_posterior_utc = cambia_zona_horaria_fecha_hora($fecha_hora_fin_posterior_local, ZONA_HORARIA_UTC);

        // Conversión de fechas iniciales y finales a UTC
        $cadena_fecha_hora_inicio_anterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_inicio_posterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_anterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_fin_anterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_posterior_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_fin_posterior_utc, FORMATO_FECHA_HORA_BASE_DATOS);

        // Comprobación de desfase entre días de la semana de las fechas iniciales de los periodos (si el intervalo es semana)
        $msg_aviso = "";
        $posible_calcular_evolucion_valores = true;
        if ($intervalo_valores == INTERVALO_VALORES_SEMANA)
        {
            $dia_semana_fecha_hora_inicio_anterior_local = $fecha_hora_inicio_anterior_local->format("w");
            $dia_semana_fecha_hora_inicio_posterior_local = $fecha_hora_inicio_posterior_local->format("w");
            $desfase_dias_semana_fechas_horas_iniciales = $dia_semana_fecha_hora_inicio_anterior_local - $dia_semana_fecha_hora_inicio_posterior_local;
            if ($desfase_dias_semana_fechas_horas_iniciales != 0)
            {
                $posible_calcular_evolucion_valores = false;
                $msg_aviso = $idiomas->_("No se pueden calcular los datos de evolución de consumos y costes porque los periodos empiezan en diferentes días de la semana");
            }
        }

        // Variables
        $hay_consumo_anterior = false;
        $hay_consumo_posterior = false;
        $hay_coste_anterior = false;
        $hay_coste_posterior = false;
        $min_consumo_anterior = INF;
        $max_consumo_anterior = -INF;
        $min_consumo_anterior_calculo_valores = INF;
        $max_consumo_anterior_calculo_valores = -INF;
        $min_consumo_posterior = INF;
        $max_consumo_posterior = -INF;
        $min_consumo_posterior_calculo_valores = INF;
        $max_consumo_posterior_calculo_valores = -INF;
        $min_coste_anterior = INF;
        $max_coste_anterior = -INF;
        $min_coste_anterior_calculo_valores = INF;
        $max_coste_anterior_calculo_valores = -INF;
        $min_coste_posterior = INF;
        $max_coste_posterior = -INF;
        $min_coste_posterior_calculo_valores = INF;
        $max_coste_posterior_calculo_valores = -INF;
        $total_consumo_anterior = NULL;
        $total_consumo_posterior = NULL;
        $total_consumo_anterior_calculo_valores = NULL;
        $total_consumo_posterior_calculo_valores = NULL;
        $numero_consumos_anterior_calculo_valores = 0;
        $numero_consumos_posterior_calculo_valores = 0;
        $total_coste_anterior = NULL;
        $total_coste_posterior = NULL;
        $total_coste_anterior_calculo_valores = NULL;
        $total_coste_posterior_calculo_valores = NULL;
        $numero_costes_anterior_calculo_valores = 0;
        $numero_costes_posterior_calculo_valores = 0;

        // Nombres para las leyendas de las gráficas
        $cadena_fecha_inicio_anterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_anterior_local_local, $_SESSION["formato_fecha_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_inicio_posterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_fin_anterior_local, $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_fin_posterior_local, $_SESSION["formato_fecha_local"]);
        $nombres_graficas = new VectorDatos();
        $nombres_graficas->anyade_etiqueta($idiomas->_("Periodo anterior")." (".$cadena_fecha_inicio_anterior_local_local." - ".$cadena_fecha_fin_anterior_local_local.")");
        $nombres_graficas->anyade_etiqueta($idiomas->_("Periodo posterior")." (".$cadena_fecha_inicio_posterior_local_local." - ".$cadena_fecha_fin_posterior_local_local.")");
        $nombres_tooltips_graficas = new VectorDatos();
        $nombres_tooltips_graficas->anyade_etiqueta($idiomas->_("Periodo anterior"));
        $nombres_tooltips_graficas->anyade_etiqueta($idiomas->_("Periodo posterior"));

        // Campo de consumo
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, NULL);

        // Información del ratio del periodo anterior
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor_anterior = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_anterior_base_datos_utc,
                $cadena_fecha_hora_fin_anterior_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                NULL);
        }

        // Consumos y costes del periodo anterior
        $consulta_periodo_anterior = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_anterior_base_datos_utc,
            $cadena_fecha_hora_fin_anterior_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            NULL,
            NULL);
        $res_periodo_anterior = $bd_datos->ejecuta_consulta($consulta_periodo_anterior);
        if ($res_periodo_anterior == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodo_anterior."'");
        }
        $filas_periodo_anterior = array();
        $horario_verano_fecha_hora_inicial_periodo_anterior = NULL;
        $horario_verano_fecha_hora_inicial_adelantada_periodo_anterior = NULL;
        $claves_periodos_adelantados_consumos_periodo_anterior = array();
        $claves_periodos_adelantados_costes_periodo_anterior = array();
        while ($fila_periodo_anterior = $res_periodo_anterior->dame_siguiente_fila())
        {
            // Fecha de la fila
            $cadena_fecha_hora_anterior_base_datos_utc = $fila_periodo_anterior['fecha_hora'];

            // Valor del ratio
            if ($aplicar_ratio == true)
            {
                $valor_ratio_anterior = dame_valor_ratio_fecha($info_ratio_sensor_anterior, $cadena_fecha_hora_anterior_base_datos_utc, true);
            }

            // Consumo (si no hay consumo se ignora la fila)
            // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
            $consumo_anterior = $fila_periodo_anterior[$campo_consumo];
            if ($consumo_anterior !== NULL)
            {
                $consumo_anterior = (float) $consumo_anterior;
                if ($aplicar_ratio == true)
                {
                    if (($valor_ratio_anterior !== NULL) && ($valor_ratio_anterior > 0))
                    {
                        $consumo_anterior /= $valor_ratio_anterior;
                    }
                    else
                    {
                        $consumo_anterior = NULL;
                    }
                }
            }
            if ($consumo_anterior === NULL)
            {
                continue;
            }
            $fila_periodo_anterior[$campo_consumo] = $consumo_anterior;

            // Se añade información de las fechas
            $fecha_hora_periodo_anterior_utc = convierte_cadena_a_fecha($cadena_fecha_hora_anterior_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_periodo_anterior_local = dame_fecha_hora_local($fecha_hora_periodo_anterior_utc);

            // Si el intervalo es día, semana o mes, establecer la hora a la primera hora del intervalo
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_periodo_anterior_local->modify('Monday this week');
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_periodo_anterior_local->modify('first day of this month');
                    $fecha_hora_periodo_anterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_anterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_anterior_local);
                    break;
                }
            }

            // Fecha y hora adelantada (paras las gráficas y las diferencias)
            $fecha_hora_adelantada_utc = clone $fecha_hora_periodo_anterior_utc;
            $fecha_hora_adelantada_utc->add($horas_separacion_periodos);

            // Nota: Se ajusta la fecha y hora adelantada UTC según el horario de verano para que coincidan las fechas y horas locales
            // en ambos periodos
            $horario_verano_fecha_hora_periodo_anterior = dame_horario_verano_fecha_hora_utc($fecha_hora_periodo_anterior_utc, $zona_horaria);
            if ($horario_verano_fecha_hora_inicial_periodo_anterior === NULL)
            {
                $horario_verano_fecha_hora_inicial_periodo_anterior = $horario_verano_fecha_hora_periodo_anterior;
            }
            if (($horario_verano_fecha_hora_inicial_periodo_anterior == false) && ($horario_verano_fecha_hora_periodo_anterior == true))
            {
                $fecha_hora_adelantada_utc->add(new DateInterval('PT1H'));
            }
            else
            {
                if (($horario_verano_fecha_hora_inicial_periodo_anterior == true) && ($horario_verano_fecha_hora_periodo_anterior == false))
                {
                    $fecha_hora_adelantada_utc->sub(new DateInterval('PT1H'));
                }
            }
            $horario_verano_fecha_hora_adelantada_periodo_anterior = dame_horario_verano_fecha_hora_utc($fecha_hora_adelantada_utc, $zona_horaria);
            if ($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior === NULL)
            {
                $horario_verano_fecha_hora_inicial_adelantada_periodo_anterior = $horario_verano_fecha_hora_adelantada_periodo_anterior;
            }
            if (($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior == false) && ($horario_verano_fecha_hora_adelantada_periodo_anterior == true))
            {
                $fecha_hora_adelantada_utc->sub(new DateInterval('PT1H'));
            }
            else
            {
                if (($horario_verano_fecha_hora_inicial_adelantada_periodo_anterior == true) && ($horario_verano_fecha_hora_adelantada_periodo_anterior == false))
                {
                    $fecha_hora_adelantada_utc->add(new DateInterval('PT1H'));
                }
            }
            $fecha_hora_adelantada_local = dame_fecha_hora_local($fecha_hora_adelantada_utc);
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_MES:
                {
                    // Si el intervalo de valores es mes hay que "redondear" la fecha hora adelantada para el cálculo correcto de las diferencias al día 1 del mes
                    $dia_mes_fecha_hora_adelantada_local = $fecha_hora_adelantada_local->format("d");
                    if ($dia_mes_fecha_hora_adelantada_local != 1)
                    {
                        $numero_dias_mes_fecha_hora_adelantada_local = cal_days_in_month(
                            CAL_GREGORIAN,
                            $fecha_hora_adelantada_local->format('m'),
                            $fecha_hora_adelantada_local->format('y'));
                        if ($dia_mes_fecha_hora_adelantada_local < ($numero_dias_mes_fecha_hora_adelantada_local / 2))
                        {
                            $fecha_hora_adelantada_local->setDate(
                                $fecha_hora_adelantada_local->format('Y'),
                                $fecha_hora_adelantada_local->format('m'),
                                1);
                        }
                        else
                        {
                            $fecha_hora_adelantada_local->modify('first day of next month');
                        }
                    }
                    $fecha_hora_adelantada_utc = dame_fecha_hora_utc($fecha_hora_adelantada_local);
                    break;
                }
            }
            $timestamp_fecha_hora_adelantada_utc = dame_timestamp_fecha_milisegundos($fecha_hora_adelantada_utc);

            // Se guarda la "clave" para determinar si hay valores en los mismos periodos (según el intervalo de valores)
            $clave_periodo_adelantado_periodo_anterior = dame_clave_periodo_comparacion_periodos($fecha_hora_adelantada_local, $intervalo_valores);
            array_push($claves_periodos_adelantados_consumos_periodo_anterior, $clave_periodo_adelantado_periodo_anterior);

            // Fila del periodo anterior
            $fila_periodo_anterior["fecha_hora_local"] = $fecha_hora_periodo_anterior_local;
            $fila_periodo_anterior["fecha_hora_adelantada_utc"] = $fecha_hora_adelantada_utc;
            $fila_periodo_anterior["fecha_hora_adelantada_local"] = $fecha_hora_adelantada_local;
            $fila_periodo_anterior["timestamp_fecha_hora_adelantada_utc"] = $timestamp_fecha_hora_adelantada_utc;
            $fila_periodo_anterior["clave_periodo_adelantado"] = $clave_periodo_adelantado_periodo_anterior;

            // Fechas adelantadas con consumos y costes del periodo anterior
            $coste_anterior = $fila_periodo_anterior[CAMPO_COSTE];
            if ($coste_anterior !== NULL)
            {
                $coste_anterior = (float) $coste_anterior;
                if (($aplicar_ratio == true) && ($id_sensor > 0))
                {
                    if (($valor_ratio_anterior !== NULL) && ($valor_ratio_anterior > 0))
                    {
                        $coste_anterior /= $valor_ratio_anterior;
                    }
                    else
                    {
                        $coste_anterior = NULL;
                    }
                }
            }
            if ($coste_anterior !== NULL)
            {
                array_push($claves_periodos_adelantados_costes_periodo_anterior, $clave_periodo_adelantado_periodo_anterior);
            }
            $fila_periodo_anterior[CAMPO_COSTE] = $coste_anterior;

            // Se añade la fila del periodo anterior
            array_push($filas_periodo_anterior, $fila_periodo_anterior);
        }

        // Información del ratio del periodo posterior
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor_posterior = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_posterior_base_datos_utc,
                $cadena_fecha_hora_fin_posterior_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                NULL);
        }

        // Consumos y costes del periodo posterior
        $consulta_periodo_posterior = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_posterior_base_datos_utc,
            $cadena_fecha_hora_fin_posterior_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            NULL,
            NULL);
        $res_periodo_posterior = $bd_datos->ejecuta_consulta($consulta_periodo_posterior);
        if ($res_periodo_posterior == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_periodo_posterior."'");
        }
        $filas_periodo_posterior = array();
        $claves_periodos_consumos_periodo_posterior = array();
        $claves_periodos_costes_periodo_posterior = array();
        while ($fila_periodo_posterior = $res_periodo_posterior->dame_siguiente_fila())
        {
            // Fecha de la fila
            $cadena_fecha_hora_posterior_base_datos_utc = $fila_periodo_posterior['fecha_hora'];

            // Valor del ratio
            if ($aplicar_ratio == true)
            {
                $valor_ratio_posterior = dame_valor_ratio_fecha($info_ratio_sensor_posterior, $cadena_fecha_hora_posterior_base_datos_utc, true);
            }

            // Consumo (si no hay consumo se ignora la fila)
            // (Nota: Se aplica el ratio sólo si es un sensor individual (ya se ha aplicado en las agregaciones) (si aplica))
            $consumo_posterior = $fila_periodo_posterior[$campo_consumo];
            if ($consumo_posterior !== NULL)
            {
                $consumo_posterior = (float) $consumo_posterior;
                if ($aplicar_ratio == true)
                {
                    if (($valor_ratio_posterior !== NULL) && ($valor_ratio_posterior > 0))
                    {
                        $consumo_posterior /= $valor_ratio_posterior;
                    }
                    else
                    {
                        $consumo_posterior = NULL;
                    }
                }
            }
            if ($consumo_posterior === NULL)
            {
                continue;
            }
            $fila_periodo_posterior[$campo_consumo] = $consumo_posterior;

            // Se añade información de las fechas
            $fecha_hora_periodo_posterior_utc = convierte_cadena_a_fecha($cadena_fecha_hora_posterior_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_periodo_posterior_local = dame_fecha_hora_local($fecha_hora_periodo_posterior_utc);

            // Si el intervalo es día, semana o mes, establecer la hora a la primera hora del intervalo
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_periodo_posterior_local->modify('Monday this week');
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_periodo_posterior_local->modify('first day of this month');
                    $fecha_hora_periodo_posterior_local->setTime(0, 0, 0);
                    $fecha_hora_periodo_posterior_utc = dame_fecha_hora_utc($fecha_hora_periodo_posterior_local);
                    break;
                }
            }

            // Timestamp de la fecha
            $timestamp_fecha_hora_periodo_posterior_utc = dame_timestamp_fecha_milisegundos($fecha_hora_periodo_posterior_utc);

            // Se guarda la "clave" para determinar si hay valores en los mismos periodos (según el intervalo de valores)
            $clave_periodo_periodo_posterior = dame_clave_periodo_comparacion_periodos($fecha_hora_periodo_posterior_local, $intervalo_valores);
            array_push($claves_periodos_consumos_periodo_posterior, $clave_periodo_periodo_posterior);

            // Fila del periodo posterior
            $fila_periodo_posterior["fecha_hora_utc"] = $fecha_hora_periodo_posterior_utc;
            $fila_periodo_posterior["fecha_hora_local"] = $fecha_hora_periodo_posterior_local;
            $fila_periodo_posterior["timestamp_fecha_hora_utc"] = $timestamp_fecha_hora_periodo_posterior_utc;
            $fila_periodo_posterior["clave_periodo"] = $clave_periodo_periodo_posterior;

            // Fechas con consumos y costes del periodo posterior
            $coste_posterior = $fila_periodo_posterior[CAMPO_COSTE];
            if ($coste_posterior !== NULL)
            {
                $coste_posterior = (float) $coste_posterior;
                if (($aplicar_ratio == true) && ($id_sensor > 0))
                {
                    if (($valor_ratio_posterior !== NULL) && ($valor_ratio_posterior > 0))
                    {
                        $coste_posterior /= $valor_ratio_posterior;
                    }
                    else
                    {
                        $coste_posterior = NULL;
                    }
                }
            }
            if ($coste_posterior !== NULL)
            {
                array_push($claves_periodos_costes_periodo_posterior, $clave_periodo_periodo_posterior);
            }
            $fila_periodo_posterior[CAMPO_COSTE] = $coste_posterior;

            // Se añade la fila del periodo posterior
            array_push($filas_periodo_posterior, $fila_periodo_posterior);
        }

        // Claves de periodos con consumos y costes en ambos periodos
        if ($posible_calcular_evolucion_valores == true)
        {
            $claves_periodos_consumos_ambos_periodos = array_intersect(
                $claves_periodos_adelantados_consumos_periodo_anterior,
                $claves_periodos_consumos_periodo_posterior);
            $claves_periodos_costes_ambos_periodos = array_intersect(
                $claves_periodos_adelantados_costes_periodo_anterior,
                $claves_periodos_costes_periodo_posterior);
        }
        else
        {
            $claves_periodos_consumos_ambos_periodos = array();
            $claves_periodos_costes_ambos_periodos = array();
        }

        // Formato de fechas locales (para los tooltips)
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            case INTERVALO_VALORES_DIA:
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_local"];
                break;
            }
            default:
            {
                throw new Exception("Intervalo de valores incorrecto: '".$intervalo_valores."'");
            }
        }

        // Se recorren los valores del periodo anterior
        $fecha_hora_inicio_consumo_anterior_local = NULL;
        $fecha_hora_inicio_coste_anterior_local = NULL;
        $consumo_periodo_anterior = new VectorDatos();
        $coste_periodo_anterior = new VectorDatos();
        $timestamp_fecha_hora_adelantada_consumo_anterior_utc = NULL;
        $timestamp_fecha_hora_adelantada_coste_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica_consumo_anterior = 0;
        $numero_puntos_seguidos_grafica_coste_anterior = 0;
        foreach ($filas_periodo_anterior as $fila_periodo_anterior)
        {
            // Fecha y consumo
            $fecha_hora_adelantada_utc = $fila_periodo_anterior["fecha_hora_adelantada_utc"];
            $fecha_hora_adelantada_local = $fila_periodo_anterior["fecha_hora_adelantada_local"];
            $consumo_anterior = $fila_periodo_anterior[$campo_consumo];
            if ($consumo_anterior > $max_consumo_anterior)
            {
                $max_consumo_anterior = $consumo_anterior;
            }
            if ($consumo_anterior < $min_consumo_anterior)
            {
                $min_consumo_anterior = $consumo_anterior;
            }

            // Fecha de inicio y fin de consumos
            if ($fecha_hora_inicio_consumo_anterior_local === NULL)
            {
                $fecha_hora_inicio_consumo_anterior_local = $fecha_hora_adelantada_local;
            }
            $fecha_hora_fin_consumo_anterior_local = $fecha_hora_adelantada_local;

            // Flag de consumo anterior
            $hay_consumo_anterior = true;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_adelantada_utc = $fila_periodo_anterior["timestamp_fecha_hora_adelantada_utc"];
            $timestamp_fecha_hora_adelantada_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica_consumo_anterior > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_adelantada_consumo_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_adelantada_utc - $timestamp_fecha_hora_adelantada_consumo_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica_consumo_anterior = 0;
                    $consumo_periodo_anterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_adelantada_consumo_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_adelantada_consumo_anterior_utc = $timestamp_fecha_hora_adelantada_utc;
            $numero_puntos_seguidos_grafica_consumo_anterior += 1;

            // Se añade el consumo (para la gráfica de valores)
            $consumo_periodo_anterior->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_adelantada_utc,
                $consumo_anterior,
                convierte_fecha_a_cadena($fila_periodo_anterior["fecha_hora_local"], $formato_fecha_hora_local));

            // Total de consumo
            if ($total_consumo_anterior === NULL)
            {
                $total_consumo_anterior = 0.0;
            }
            $total_consumo_anterior += $consumo_anterior;

            // Consumos máximo y mínimo (con valores en ambos periodos)´
            if (in_array($fila_periodo_anterior["clave_periodo_adelantado"], $claves_periodos_consumos_ambos_periodos) == true)
            {
                if ($consumo_anterior > $max_consumo_anterior_calculo_valores)
                {
                    $max_consumo_anterior_calculo_valores = $consumo_anterior;
                }
                if ($consumo_anterior < $min_consumo_anterior_calculo_valores)
                {
                    $min_consumo_anterior_calculo_valores = $consumo_anterior;
                }
                $numero_consumos_anterior_calculo_valores += 1;
            }

            // Consumo total (sólo si la fecha es anterior a la fecha de fin del periodo posterior)
            if ($fecha_hora_adelantada_utc <= $fecha_hora_periodo_posterior_utc)
            {
                if ($total_consumo_anterior_calculo_valores === NULL)
                {
                    $total_consumo_anterior_calculo_valores = 0.0;
                }
                $total_consumo_anterior_calculo_valores += $consumo_anterior;
            }

            // Si hay coste
            if ($fila_periodo_anterior[CAMPO_COSTE] !== NULL)
            {
                // Coste
                $coste_anterior = $fila_periodo_anterior[CAMPO_COSTE];
                if ($coste_anterior > $max_coste_anterior)
                {
                    $max_coste_anterior = $coste_anterior;
                }
                if ($coste_anterior < $min_coste_anterior)
                {
                    $min_coste_anterior = $coste_anterior;
                }

                // Fecha de inicio y fin de costes
                if ($fecha_hora_inicio_coste_anterior_local === NULL)
                {
                    $fecha_hora_inicio_coste_anterior_local = $fecha_hora_adelantada_local;
                }
                $fecha_hora_fin_coste_anterior_local = $fecha_hora_adelantada_local;

                // Flag de coste anterior
                $hay_coste_anterior = true;

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                if (($numero_puntos_seguidos_grafica_coste_anterior > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_adelantada_coste_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_adelantada_utc - $timestamp_fecha_hora_adelantada_coste_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica_coste_anterior = 0;
                        $coste_periodo_anterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_adelantada_coste_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_adelantada_coste_anterior_utc = $timestamp_fecha_hora_adelantada_utc;
                $numero_puntos_seguidos_grafica_coste_anterior += 1;

                // Se añade el coste (para la gráfica de valores)
                $coste_periodo_anterior->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_adelantada_utc,
                    $coste_anterior,
                    convierte_fecha_a_cadena($fila_periodo_anterior["fecha_hora_local"], $formato_fecha_hora_local));

                // Total de coste
                if ($total_coste_anterior === NULL)
                {
                    $total_coste_anterior = 0.0;
                }
                $total_coste_anterior += $coste_anterior;

                // Costes máximo y mínimo(con valores en ambos periodos)
                if (in_array($fila_periodo_anterior["clave_periodo_adelantado"], $claves_periodos_costes_ambos_periodos) == true)
                {
                    if ($coste_anterior > $max_coste_anterior_calculo_valores)
                    {
                        $max_coste_anterior_calculo_valores = $coste_anterior;
                    }
                    if ($coste_anterior < $min_coste_anterior_calculo_valores)
                    {
                        $min_coste_anterior_calculo_valores = $coste_anterior;
                    }
                    $numero_costes_anterior_calculo_valores += 1;
                }

                // Coste total (sólo si la fecha es anterior a la fecha de fin del periodo posterior)
                if ($fecha_hora_adelantada_utc <= $fecha_hora_periodo_posterior_utc)
                {
                    if ($total_coste_anterior_calculo_valores === NULL)
                    {
                        $total_coste_anterior_calculo_valores = 0.0;
                    }
                    $total_coste_anterior_calculo_valores += $coste_anterior;
                }
            }
        }

        // Si no hay fecha de inicio de consumos es que no hay datos
        if ($fecha_hora_inicio_consumo_anterior_local === NULL)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recorren los valores del periodo posterior
        $fecha_hora_inicio_consumo_posterior_local = NULL;
        $fecha_hora_inicio_coste_posterior_local = NULL;
        $consumo_periodo_posterior = new VectorDatos();
        $coste_periodo_posterior = new VectorDatos();
        $timestamp_fecha_hora_periodo_posterior_consumo_anterior_utc = NULL;
        $timestamp_fecha_hora_periodo_posterior_coste_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica_consumo_posterior = 0;
        $numero_puntos_seguidos_grafica_coste_posterior = 0;
        foreach ($filas_periodo_posterior as $fila_periodo_posterior)
        {
            // Fecha y consumo
            $fecha_hora_periodo_posterior_local = $fila_periodo_posterior["fecha_hora_local"];
            $consumo_posterior = $fila_periodo_posterior[$campo_consumo];
            if ($consumo_posterior > $max_consumo_posterior)
            {
                $max_consumo_posterior = $consumo_posterior;
            }
            if ($consumo_posterior < $min_consumo_posterior)
            {
                $min_consumo_posterior = $consumo_posterior;
            }

            // Fecha de inicio y fin de consumos
            if ($fecha_hora_inicio_consumo_posterior_local === NULL)
            {
                $fecha_hora_inicio_consumo_posterior_local = $fecha_hora_periodo_posterior_local;
            }
            $fecha_hora_fin_consumo_posterior_local = $fecha_hora_periodo_posterior_local;

            // Flag de consumo posterior
            $hay_consumo_posterior = true;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_periodo_posterior_utc = $fila_periodo_posterior["timestamp_fecha_hora_utc"];
            $timestamp_fecha_hora_periodo_posterior_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica_consumo_posterior > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_periodo_posterior_consumo_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_periodo_posterior_utc - $timestamp_fecha_hora_periodo_posterior_consumo_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica_consumo_posterior = 0;
                    $consumo_periodo_posterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_periodo_posterior_consumo_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_periodo_posterior_consumo_anterior_utc = $timestamp_fecha_hora_periodo_posterior_utc;
            $numero_puntos_seguidos_grafica_consumo_posterior += 1;

            // Se añade el consumo
            $consumo_periodo_posterior->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_periodo_posterior_utc,
                $consumo_posterior,
                convierte_fecha_a_cadena($fecha_hora_periodo_posterior_local, $formato_fecha_hora_local));

            // Total de consumo
            if ($total_consumo_posterior === NULL)
            {
                $total_consumo_posterior = 0.0;
            }
            $total_consumo_posterior += $consumo_posterior;

            // Consumos máximo y mínimo (con valores en ambos periodos)
            if (in_array($fila_periodo_posterior["clave_periodo"], $claves_periodos_consumos_ambos_periodos) == true)
            {
                if ($consumo_posterior > $max_consumo_posterior_calculo_valores)
                {
                    $max_consumo_posterior_calculo_valores = $consumo_posterior;
                }
                if ($consumo_posterior < $min_consumo_posterior_calculo_valores)
                {
                    $min_consumo_posterior_calculo_valores = $consumo_posterior;
                }
                $numero_consumos_posterior_calculo_valores += 1;
            }
            else
            {
                // Fecha sólo en el periodo anterior
                $fecha_hora_periodo_posterior_local = $fila_periodo_posterior["fecha_hora_local"];
            }

            // Consumo total
            if ($total_consumo_posterior_calculo_valores === NULL)
            {
                $total_consumo_posterior_calculo_valores = 0.0;
            }
            $total_consumo_posterior_calculo_valores += $consumo_posterior;

            // Si hay coste
            if ($fila_periodo_posterior[CAMPO_COSTE] !== NULL)
            {
                $coste_posterior = $fila_periodo_posterior[CAMPO_COSTE];
                if ($coste_posterior > $max_coste_posterior)
                {
                    $max_coste_posterior = $coste_posterior;
                }
                if ($coste_posterior < $min_coste_posterior)
                {
                    $min_coste_posterior = $coste_posterior;
                }

                // Fecha de inicio y fin de costes
                if ($fecha_hora_inicio_coste_posterior_local === NULL)
                {
                    $fecha_hora_inicio_coste_posterior_local = $fecha_hora_periodo_posterior_local;
                }
                $fecha_hora_fin_coste_posterior_local = $fecha_hora_periodo_posterior_local;

                // Flag de coste posterior
                $hay_coste_posterior = true;

                // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
                if (($numero_puntos_seguidos_grafica_coste_posterior > 1) &&
                    ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_periodo_posterior_coste_anterior_utc !== NULL))
                {
                    $segundos_entre_valores = ($timestamp_fecha_hora_periodo_posterior_utc - $timestamp_fecha_hora_periodo_posterior_coste_anterior_utc) / 1000;
                    if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                    {
                        $numero_puntos_seguidos_grafica_coste_posterior = 0;
                        $coste_periodo_posterior->anyade_tupla_pareja_datos($timestamp_fecha_hora_periodo_posterior_coste_anterior_utc + 1, NULL);
                    }
                }
                $timestamp_fecha_hora_periodo_posterior_coste_anterior_utc = $timestamp_fecha_hora_periodo_posterior_utc;
                $numero_puntos_seguidos_grafica_coste_posterior += 1;

                // Se añade el coste (para la gráfica de valores)
                $coste_periodo_posterior->anyade_tupla_pareja_datos_etiqueta(
                    $timestamp_fecha_hora_periodo_posterior_utc,
                    $coste_posterior,
                    convierte_fecha_a_cadena($fecha_hora_periodo_posterior_local, $formato_fecha_hora_local));

                // Total de coste
                if ($total_coste_posterior === NULL)
                {
                    $total_coste_posterior = 0.0;
                }
                $total_coste_posterior += $coste_posterior;

                // Costes máximo y mínimo (con valores en ambos periodos)
                if (in_array($fila_periodo_posterior["clave_periodo"], $claves_periodos_costes_ambos_periodos) == true)
                {
                    if ($coste_posterior > $max_coste_posterior_calculo_valores)
                    {
                        $max_coste_posterior_calculo_valores = $coste_posterior;
                    }
                    if ($coste_posterior < $min_consumo_posterior_calculo_valores)
                    {
                        $min_coste_posterior_calculo_valores = $coste_posterior;
                    }
                    $numero_costes_posterior_calculo_valores += 1;
                }
                else
                {
                    // Fecha sólo en el periodo anterior
                    $fecha_hora_periodo_posterior_local = $fila_periodo_posterior["fecha_hora_local"];
                }

                // Coste total
                if ($total_coste_posterior_calculo_valores === NULL)
                {
                    $total_coste_posterior_calculo_valores = 0.0;
                }
                $total_coste_posterior_calculo_valores += $coste_posterior;
            }
        }

        // Si no hay fecha de inicio de consumos es que no hay datos
        if ($fecha_hora_inicio_consumo_posterior_local === NULL)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Periodo de tiempo de consumos del periodo posterior (el de costes si los hay, se asume el mismo - para el título de la tabla)
        if ($fecha_hora_fin_consumo_posterior_local !== NULL)
        {
            $fecha_hora_fin_consumo_posterior_local_aux = clone $fecha_hora_fin_consumo_posterior_local;
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_CUARTOHORA:
                {
                    $fecha_hora_fin_consumo_posterior_local_aux->modify('+900 seconds');
                    break;
                }
                case INTERVALO_VALORES_HORA:
                {
                    $fecha_hora_fin_consumo_posterior_local_aux->modify('+1 hour');
                    break;
                }
                case INTERVALO_VALORES_DIA:
                {
                    $fecha_hora_fin_consumo_posterior_local_aux->modify('+1 day');
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                {
                    $fecha_hora_fin_consumo_posterior_local_aux->modify('+1 week');
                    break;
                }
                case INTERVALO_VALORES_MES:
                {
                    $fecha_hora_fin_consumo_posterior_local_aux->modify('+1 month');
                    break;
                }
            }
            $periodo_tiempo_consumos_posterior = $fecha_hora_inicio_consumo_posterior_local->diff($fecha_hora_fin_consumo_posterior_local_aux);
        }

        // Flag de datos de costes
        if (($fecha_hora_inicio_coste_anterior_local !== NULL) && ($fecha_hora_inicio_coste_posterior_local !== NULL))
        {
            $hay_datos_costes = true;
        }
        else
        {
            $hay_datos_costes = false;
        }

        // Rango de fechas de los periodos
        if (($fecha_hora_inicio_consumo_anterior_local !== NULL) && ($fecha_hora_inicio_consumo_posterior_local !== NULL))
        {
            $min_fecha_hora_consumo_periodos_local = clone min(array($fecha_hora_inicio_consumo_anterior_local, $fecha_hora_inicio_consumo_posterior_local));
        }
        else
        {
            $min_fecha_hora_consumo_periodos_local = NULL;
        }
        if (($fecha_hora_fin_consumo_anterior_local !== NULL) && ($fecha_hora_fin_consumo_posterior_local !== NULL))
        {
            $max_fecha_hora_consumo_periodos_local = clone max(array($fecha_hora_fin_consumo_anterior_local, $fecha_hora_fin_consumo_posterior_local));
        }
        else
        {
            $max_fecha_hora_consumo_periodos_local = NULL;
        }
        if (($fecha_hora_inicio_coste_anterior_local !== NULL) && ($fecha_hora_inicio_coste_posterior_local !== NULL))
        {
            $min_fecha_hora_coste_periodos_local = clone min(array($fecha_hora_inicio_coste_anterior_local, $fecha_hora_inicio_coste_posterior_local));
        }
        else
        {
            $min_fecha_hora_coste_periodos_local = NULL;
        }
        if (($fecha_hora_fin_coste_anterior_local !== NULL) && ($fecha_hora_fin_coste_posterior_local !== NULL))
        {
            $max_fecha_hora_coste_periodos_local = clone max(array($fecha_hora_fin_coste_anterior_local, $fecha_hora_fin_coste_posterior_local));
        }
        else
        {
            $max_fecha_hora_coste_periodos_local = NULL;
        }

        // Fechas mínima y máxima
        // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
        $cadena_min_fecha_hora_consumo_jqplot_local = NULL;
        $cadena_max_fecha_hora_consumo_jqplot_local = NULL;
        if (($min_fecha_hora_consumo_periodos_local !== NULL) && ($max_fecha_hora_consumo_periodos_local !== NULL))
        {
            if ($min_fecha_hora_consumo_periodos_local == $max_fecha_hora_consumo_periodos_local)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_hora_consumo_periodos_local->sub($intervalo_fecha);
                $max_fecha_hora_consumo_periodos_local->add($intervalo_fecha);
            }
        }
        if ($min_fecha_hora_consumo_periodos_local !== NULL)
        {
            $cadena_min_fecha_hora_consumo_jqplot_local = convierte_fecha_a_cadena($min_fecha_hora_consumo_periodos_local, FORMATO_FECHA_HORA_JQPLOT);
        }
        if ($max_fecha_hora_consumo_periodos_local !== NULL)
        {
            $cadena_max_fecha_hora_consumo_jqplot_local = convierte_fecha_a_cadena($max_fecha_hora_consumo_periodos_local, FORMATO_FECHA_HORA_JQPLOT);
        }
        $cadena_min_fecha_hora_coste_jqplot_local = NULL;
        $cadena_max_fecha_hora_coste_jqplot_local = NULL;
        if (($min_fecha_hora_coste_periodos_local !== NULL) && ($max_fecha_hora_coste_periodos_local !== NULL))
        {
            if ($min_fecha_hora_coste_periodos_local == $max_fecha_hora_coste_periodos_local)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_hora_coste_periodos_local->sub($intervalo_fecha);
                $max_fecha_hora_coste_periodos_local->add($intervalo_fecha);
            }
        }
        if ($min_fecha_hora_coste_periodos_local !== NULL)
        {
            $cadena_min_fecha_hora_coste_jqplot_local = convierte_fecha_a_cadena($min_fecha_hora_coste_periodos_local, FORMATO_FECHA_HORA_JQPLOT);
        }
        if ($max_fecha_hora_coste_periodos_local !== NULL)
        {
            $cadena_max_fecha_hora_coste_jqplot_local = convierte_fecha_a_cadena($max_fecha_hora_coste_periodos_local, FORMATO_FECHA_HORA_JQPLOT);
        }

        // Variables para dibujar las gráficas
        $grafica_consumos = new VectorDatos();
        $grafica_consumos->anyade_dato($consumo_periodo_anterior->dame_datos());
        $grafica_consumos->anyade_dato($consumo_periodo_posterior->dame_datos());
        if (($hay_consumo_posterior == true) && ($hay_consumo_anterior == true))
        {
            $max_consumo = max(array($max_consumo_posterior, $max_consumo_anterior));
        }
        else
        {
            $max_consumo = "ND";
        }

        $grafica_costes = new VectorDatos();
        $grafica_costes->anyade_dato($coste_periodo_anterior->dame_datos());
        $grafica_costes->anyade_dato($coste_periodo_posterior->dame_datos());
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            $max_coste = max(array($max_coste_posterior, $max_coste_anterior));
        }
        else
        {
            $max_coste = "ND";
        }

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
        }
        $unidad_medida_precio = $idiomas->_("cents").".".$_SESSION["moneda"]."/".$unidad_medida_consumo;

        // Si hay consumos en los dos periodos
        if (($hay_consumo_posterior == true) && ($hay_consumo_anterior == true))
        {
            // Valores y textos de evolución de consumos

            // Diferencia de consumos totales
            if ($total_consumo_posterior_calculo_valores == $total_consumo_anterior_calculo_valores)
            {
                $texto_diferencia_consumos_totales = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0 ".$unidad_medida_consumo." (0 "."%".")";
            }
            else
            {
                $diferencia_consumos_totales = abs($total_consumo_posterior_calculo_valores - $total_consumo_anterior_calculo_valores);
                $cadena_diferencia_consumos_totales = formatea_numero($diferencia_consumos_totales, 2);
                $porcentaje_diferencia_consumos_totales = dame_porcentaje_valor_referencia($total_consumo_posterior_calculo_valores, $total_consumo_anterior_calculo_valores);
                $cadena_porcentaje_diferencia_consumos_totales = formatea_numero($porcentaje_diferencia_consumos_totales, 2);

                if ($total_consumo_posterior_calculo_valores > $total_consumo_anterior_calculo_valores)
                {
                    $texto_diferencia_consumos_totales = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_consumos_totales." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_consumos_totales." "."%".")";
                }
                else
                {
                    $texto_diferencia_consumos_totales = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_consumos_totales." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_consumos_totales." "."%".")";
                    $diferencia_consumos_totales *= -1;
                    $porcentaje_diferencia_consumos_totales *= -1;
                }
            }

            // Sólo si hay consumos solapados en ambos periodos
            $hay_consumos_solapados_periodos = (count($claves_periodos_consumos_ambos_periodos) > 0);
            if ($hay_consumos_solapados_periodos == true)
            {
                // Diferencia de máximos de consumos
                if ($max_consumo_posterior_calculo_valores == $max_consumo_anterior_calculo_valores)
                {
                    $texto_diferencia_consumos_maximos = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_consumos_maximos = abs($max_consumo_posterior_calculo_valores - $max_consumo_anterior_calculo_valores);
                    $cadena_diferencia_consumos_maximos = formatea_numero($diferencia_consumos_maximos, 2);
                    $porcentaje_diferencia_consumos_maximos = dame_porcentaje_valor_referencia($max_consumo_posterior_calculo_valores, $max_consumo_anterior_calculo_valores);
                    $cadena_porcentaje_diferencia_consumos_maximos = formatea_numero($porcentaje_diferencia_consumos_maximos, 2);

                    if ($max_consumo_posterior_calculo_valores > $max_consumo_anterior_calculo_valores)
                    {
                        $texto_diferencia_consumos_maximos = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_consumos_maximos." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_consumos_maximos." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_consumos_maximos = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_consumos_maximos." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_consumos_maximos." "."%".")";
                        $diferencia_consumos_maximos *= -1;
                        $porcentaje_diferencia_consumos_maximos *= -1;
                    }
                }

                // Diferencia de mínimos de consumos
                if ($min_consumo_posterior_calculo_valores == $min_consumo_anterior_calculo_valores)
                {
                    $texto_diferencia_consumos_minimos = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_consumo." (0 "."%".")";
                }
                else
                {
                    $diferencia_consumos_minimos = abs($min_consumo_posterior_calculo_valores - $min_consumo_anterior_calculo_valores);
                    $cadena_diferencia_consumos_minimos = formatea_numero($diferencia_consumos_minimos, 2);
                    $porcentaje_diferencia_consumos_minimos = dame_porcentaje_valor_referencia($min_consumo_posterior_calculo_valores, $min_consumo_anterior_calculo_valores);
                    $cadena_porcentaje_diferencia_consumos_minimos = formatea_numero($porcentaje_diferencia_consumos_minimos, 2);

                    if ($min_consumo_posterior_calculo_valores > $min_consumo_anterior_calculo_valores)
                    {
                        $texto_diferencia_consumos_minimos = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_consumos_minimos." ".$unidad_medida_consumo." (+".$cadena_porcentaje_diferencia_consumos_minimos." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_consumos_minimos = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_consumos_minimos." ".$unidad_medida_consumo." (-".$cadena_porcentaje_diferencia_consumos_minimos." "."%".")";
                        $diferencia_consumos_minimos *= -1;
                        $porcentaje_diferencia_consumos_minimos *= -1;
                    }
                }
            }
            else
            {
                $texto_diferencia_consumos_maximos = $idiomas->_("ND");
                $texto_diferencia_consumos_minimos = $idiomas->_("ND");
            }
        }

        // Si hay costes en los dos periodos
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            // Diferencia de costes totales
            if ($total_coste_posterior_calculo_valores == $total_coste_anterior_calculo_valores)
            {
                $texto_diferencia_costes_totales = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0 ".$unidad_medida_coste." (0 "."%".")";
            }
            else
            {
                $diferencia_costes_totales = abs($total_coste_posterior_calculo_valores - $total_coste_anterior_calculo_valores);
                $cadena_diferencia_costes_totales = formatea_numero($diferencia_costes_totales, 2, false);
                $porcentaje_diferencia_costes_totales = dame_porcentaje_valor_referencia($total_coste_posterior_calculo_valores, $total_coste_anterior_calculo_valores);
                $cadena_porcentaje_diferencia_costes_totales = formatea_numero($porcentaje_diferencia_costes_totales, 2);

                if ($total_coste_posterior_calculo_valores > $total_coste_anterior_calculo_valores)
                {
                    $texto_diferencia_costes_totales = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_costes_totales." ".$unidad_medida_coste." (+".$cadena_porcentaje_diferencia_costes_totales." "."%".")";
                }
                else
                {
                    $texto_diferencia_costes_totales = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_costes_totales." ".$unidad_medida_coste." (-".$cadena_porcentaje_diferencia_costes_totales." "."%".")";
                    $diferencia_costes_totales *= -1;
                    $porcentaje_diferencia_costes_totales *= -1;
                }
            }

            // Sólo si hay costes solapados en ambos periodos
            $hay_costes_solapados_periodos = (count($claves_periodos_costes_ambos_periodos) > 0);
            if ($hay_costes_solapados_periodos == true)
            {
                // Diferencia de máximos de costes
                if ($max_coste_posterior_calculo_valores == $max_coste_anterior_calculo_valores)
                {
                    $texto_diferencia_costes_maximos = "<i class='icon-sort color-gris-claro'></i> "."0 ".$unidad_medida_coste." (0 "."%".")";
                }
                else
                {
                    $diferencia_costes_maximos = abs($max_coste_posterior_calculo_valores - $max_coste_anterior_calculo_valores);
                    $cadena_diferencia_costes_maximos = formatea_numero($diferencia_costes_maximos, 2, false);
                    $porcentaje_diferencia_costes_maximos = dame_porcentaje_valor_referencia($max_coste_posterior_calculo_valores, $max_coste_anterior_calculo_valores);
                    $cadena_porcentaje_diferencia_costes_maximos = formatea_numero($porcentaje_diferencia_costes_maximos, 2);

                    if ($max_coste_posterior_calculo_valores > $max_coste_anterior_calculo_valores)
                    {
                        $texto_diferencia_costes_maximos = "<i class='icon-caret-up color-rojo'></i> +".
                            $cadena_diferencia_costes_maximos." ".$unidad_medida_coste." (+".$cadena_porcentaje_diferencia_costes_maximos." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_costes_maximos = "<i class='icon-caret-down color-verde'></i> -".
                            $cadena_diferencia_costes_maximos." ".$unidad_medida_coste." (-".$cadena_porcentaje_diferencia_costes_maximos." "."%".")";
                        $diferencia_costes_maximos *= -1;
                        $porcentaje_diferencia_costes_maximos *= -1;
                    }
                }

                // Diferencia de mínimos de costes
                if ($min_coste_posterior_calculo_valores == $min_coste_anterior_calculo_valores)
                {
                    $texto_diferencia_costes_minimos = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                        "0 ".$unidad_medida_coste." (0 "."%".")";
                }
                else
                {
                    $diferencia_costes_minimos = abs($min_coste_posterior_calculo_valores - $min_coste_anterior_calculo_valores);
                    $cadena_diferencia_costes_minimos = formatea_numero($diferencia_costes_minimos, 2, false);
                    $porcentaje_diferencia_costes_minimos = dame_porcentaje_valor_referencia($min_coste_posterior_calculo_valores, $min_coste_anterior_calculo_valores);
                    $cadena_porcentaje_diferencia_costes_minimos = formatea_numero($porcentaje_diferencia_costes_minimos, 2);

                    if ($min_coste_posterior_calculo_valores > $min_coste_anterior_calculo_valores)
                    {
                        $texto_diferencia_costes_minimos = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                            $cadena_diferencia_costes_minimos." ".$unidad_medida_coste." (+".$cadena_porcentaje_diferencia_costes_minimos." "."%".")";
                    }
                    else
                    {
                        $texto_diferencia_costes_minimos = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                            $cadena_diferencia_costes_minimos." ".$unidad_medida_coste." (-".$cadena_porcentaje_diferencia_costes_minimos." "."%".")";
                        $diferencia_costes_minimos *= -1;
                        $porcentaje_diferencia_costes_minimos *= -1;
                    }
                }
            }
            else
            {
                $texto_diferencia_costes_maximos = $idiomas->_("ND");
                $texto_diferencia_costes_minimos = $idiomas->_("ND");
            }
        }

        // Tabla de evolución de consumos y costes
        $params_tabla_evolucion_consumos_costes = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVOLUCION_CONSUMOS_COSTES,
            "generar_valores_xml" => true
        );
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            $titulo_tabla_evolucion_consumos_costes = $idiomas->_("Evolución de consumos y costes");
        }
        else
        {
            $titulo_tabla_evolucion_consumos_costes = $idiomas->_("Evolución de consumos");
        }
        $segundos_duracion_periodos = dame_segundos_intervalo_tiempo($duracion_periodo_posterior);
        $segundos_periodo_tiempo_consumos_posterior = dame_segundos_intervalo_tiempo($periodo_tiempo_consumos_posterior);
        if ($segundos_duracion_periodos > $segundos_periodo_tiempo_consumos_posterior)
        {
            $texto_periodo = dame_texto_periodo($segundos_periodo_tiempo_consumos_posterior);
            $titulo_tabla_evolucion_consumos_costes .= " (".$texto_periodo.")";
        }
        else
        {
            $texto_periodo = "";
        }
        $tabla_evolucion_consumos_costes = new TablaDatos(
            "tabla-evolucion-consumos-costes-comparacion-periodos",
            $titulo_tabla_evolucion_consumos_costes,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_evolucion_consumos_costes
        );
        $cabecera_tabla_evolucion_consumos_costes = array(
            $idiomas->_("Medida"),
            $idiomas->_("Total"),
            $idiomas->_("Máximo"),
            $idiomas->_("Mínimo")
        );
        $tabla_evolucion_consumos_costes->anyade_cabecera("", $cabecera_tabla_evolucion_consumos_costes);
        if (($hay_consumo_posterior == true) && ($hay_consumo_anterior == true))
        {
            $fila_consumo = array(
                $idiomas->_("Consumo"),
                $texto_diferencia_consumos_totales,
                $texto_diferencia_consumos_maximos,
                $texto_diferencia_consumos_minimos);
            $tabla_evolucion_consumos_costes->anyade_fila("", $fila_consumo);
        }
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            $fila_coste = array(
                $idiomas->_("Coste"),
                $texto_diferencia_costes_totales,
                $texto_diferencia_costes_maximos,
                $texto_diferencia_costes_minimos);
            $tabla_evolucion_consumos_costes->anyade_fila("", $fila_coste);
        }

        // Grafica de consumos totales
        if (($hay_consumo_posterior == true) && ($hay_consumo_anterior == true))
        {
            $grafica_consumos_totales = new VectorDatos();
            $grafica_consumos_totales->anyade_tupla_dato($total_consumo_anterior);
            $grafica_consumos_totales->anyade_tupla_dato($total_consumo_posterior);

            // Máximos para calcular la escala de las tablas a mostrar
            $max_total_consumo = (float) max(array($total_consumo_anterior, $total_consumo_posterior));
        }
        else
        {
            $grafica_consumos_totales = new VectorDatos();
            $max_total_consumo = "ND";
        }

        // Tabla de evolución de precios medios entre periodos (diferencias entre periodo posterior y periodo anterior)
        $params_tabla_evolucion_precios_medios = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_EVOLUCION_PRECIOS_MEDIOS,
            "generar_valores_xml" => true
        );
        $titulo_tabla_evolucion_precios_medios = $idiomas->_("Evolución de precios medios");
        $tabla_evolucion_precios_medios = new TablaDatos(
            "tabla-evolucion-precios-medios-comparacion-periodos",
            $titulo_tabla_evolucion_precios_medios,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_evolucion_precios_medios
        );
        $cabecera_tabla_evolucion_precios_medios = array(
            $idiomas->_("Precio medio"),
            $idiomas->_("Coste con el precio medio del periodo anterior")
        );
        $tabla_evolucion_precios_medios->anyade_cabecera("", $cabecera_tabla_evolucion_precios_medios);

        // Graficas de costes totales y precios medios
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            $grafica_costes_totales = new VectorDatos();
            $grafica_costes_totales->anyade_tupla_dato($total_coste_anterior);
            $grafica_costes_totales->anyade_tupla_dato($total_coste_posterior);

            $precio_medio_anterior = 100 * ($total_coste_anterior / $total_consumo_anterior);
            $precio_medio_posterior = 100 * ($total_coste_posterior / $total_consumo_posterior);

            $grafica_precios_medios = new VectorDatos();
            $grafica_precios_medios->anyade_tupla_dato($precio_medio_anterior);
            $grafica_precios_medios->anyade_tupla_dato($precio_medio_posterior);

            // Textos para tabla de evolución de precios entre periodos (diferencias entre periodo posterior y periodo anterior)
            if ($precio_medio_posterior == $precio_medio_anterior)
            {
                $texto_diferencia_precio_medio_periodos = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0 ".$unidad_medida_precio." (0 "."%".")";
                $texto_diferencia_coste_periodos_precio_medio_anterior = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0 ".$unidad_medida_coste." (0 "."%".")";
            }
            else
            {
                $diferencia_precio_medio_periodos = abs($precio_medio_posterior - $precio_medio_anterior);
                $cadena_diferencia_precio_medio_periodos = formatea_numero($diferencia_precio_medio_periodos, 4);
                $porcentaje_diferencia_precio_medio_periodos = dame_porcentaje_valor_referencia($precio_medio_posterior, $precio_medio_anterior);
                $cadena_porcentaje_diferencia_precio_medio_periodos = formatea_numero($porcentaje_diferencia_precio_medio_periodos, 2);
                if ($precio_medio_posterior > $precio_medio_anterior)
                {
                    $texto_diferencia_precio_medio_periodos = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_precio_medio_periodos." ".$unidad_medida_precio." (+".$cadena_porcentaje_diferencia_precio_medio_periodos." "."%".")";
                }
                else
                {
                    $texto_diferencia_precio_medio_periodos = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_precio_medio_periodos." ".$unidad_medida_precio." (-".$cadena_porcentaje_diferencia_precio_medio_periodos." "."%".")";
                }

                $total_coste_posterior_precios_medios_anterior = $total_consumo_posterior * $precio_medio_anterior / 100.0;
                $diferencia_economica = abs($total_coste_anterior - $total_coste_posterior_precios_medios_anterior);
                $cadena_diferencia_economica = formatea_numero($diferencia_economica, 2);
                $porcentaje_diferencia_economica = dame_porcentaje_valor_referencia($total_coste_anterior, $total_coste_posterior_precios_medios_anterior);
                $cadena_porcentaje_diferencia_economica = formatea_numero($porcentaje_diferencia_economica, 2);
                if ($total_coste_posterior_precios_medios_anterior > $total_coste_anterior)
                {
                    $texto_diferencia_coste_periodos_precio_medio_anterior = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_economica." ".$unidad_medida_coste." (+".$cadena_porcentaje_diferencia_economica." "."%".")";
                }
                else
                {
                    $texto_diferencia_coste_periodos_precio_medio_anterior = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_economica." ".$unidad_medida_coste." (-".$cadena_porcentaje_diferencia_economica." "."%".")";
                }
            }

            $fila_evolucion_precios_medios = array($texto_diferencia_precio_medio_periodos, $texto_diferencia_coste_periodos_precio_medio_anterior);
            $tabla_evolucion_precios_medios->anyade_fila("", $fila_evolucion_precios_medios);

            // Máximos para calcular la escala de las tablas a mostrar
            $max_total_coste = (float) max(array($total_coste_anterior, $total_coste_posterior));
            $max_precio_medio = (float) max(array($precio_medio_anterior, $precio_medio_posterior));
        }
        else
        {
            $grafica_costes_totales = new VectorDatos();
            $grafica_precios_medios = new VectorDatos();
            $max_total_coste = "ND";
            $max_precio_medio = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "hay_datos_costes" => $hay_datos_costes,
            "msg_aviso" => $msg_aviso,
            "min_fecha_consumo" => $cadena_min_fecha_hora_consumo_jqplot_local,
            "max_fecha_consumo" => $cadena_max_fecha_hora_consumo_jqplot_local,
            "min_fecha_coste" => $cadena_min_fecha_hora_coste_jqplot_local,
            "max_fecha_coste" => $cadena_max_fecha_hora_coste_jqplot_local,
            "grafica_consumos" => $grafica_consumos->dame_datos(),
            "grafica_costes" => $grafica_costes->dame_datos(),
            "tabla_evolucion_consumos_costes" => $tabla_evolucion_consumos_costes->dame_tabla(),
            "titulo_tabla_evolucion_consumos_costes" => $titulo_tabla_evolucion_consumos_costes,
            "max_consumo" => $max_consumo,
            "max_coste" => $max_coste,
            "etiquetas" => $nombres_graficas->dame_datos(),
            "etiquetas_tooltips" => $nombres_tooltips_graficas->dame_datos(),
            "grafica_consumos_totales" => $grafica_consumos_totales->dame_datos(),
            "grafica_costes_totales" => $grafica_costes_totales->dame_datos(),
            "grafica_precios_medios" => $grafica_precios_medios->dame_datos(),
            "tabla_evolucion_precios_medios" => $tabla_evolucion_precios_medios->dame_tabla(),
            "max_total_consumo" => $max_total_consumo,
            "max_total_coste" => $max_total_coste,
            "max_precio_medio" => $max_precio_medio,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "unidad_medida_coste" => $unidad_medida_coste,
            "unidad_medida_precio" => $unidad_medida_precio);

        // Se recupera la tabla de evolución de consumos por tramo (si es necesario)
        $datos_tabla_evolucion_consumos_tramos = NULL;
        if (($hay_coste_posterior == true) && ($hay_coste_anterior == true))
        {
            // Tabla de evolución de consumos por tramo
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
                    if ($caracteristicas_tarifas_electricas["tramos"] == true)
                    {
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_CUARTOHORA:
                            case INTERVALO_VALORES_HORA:
                            {
                                $tabla_evolucion_consumos_tramos = dame_tabla_evolucion_consumos_tramos(
                                    $texto_periodo,
                                    $unidad_medida_consumo,
                                    $filas_periodo_anterior,
                                    $filas_periodo_posterior,
                                    $campo_consumo,
                                    $claves_periodos_consumos_ambos_periodos);
                                $datos_tabla_evolucion_consumos_tramos = $tabla_evolucion_consumos_tramos->dame_tabla();
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }
        $resultado["tabla_evolucion_consumos_tramos"] = $datos_tabla_evolucion_consumos_tramos;

        // Se devuelve el resultado
        return ($resultado);
    }


    function dame_costes_consumo_sensor_tarifas($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $medicion = $parametros["medicion"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $ids_tarifas = $parametros["ids_tarifas"];
        $nombres_tarifas = $parametros["nombres_tarifas"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
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

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables para guardar las etiquetas y los datos de las tarifas para dibujar las gráficas
        $numero_tarifa = 0;
        $etiquetas_costes_tarifas = new VectorDatos();
        $grafica_costes_tarifas = new VectorDatos();
        $max_coste_tarifas = 0;
        $etiquetas_costes_totales_tarifas = new VectorDatos();
        $grafica_costes_totales_tarifas = new VectorDatos();
        $max_coste_total_tarifas = 0;

        // Se recupera la información del coste actual
        $datos_costes_sensor = NULL;
        $coste_total = 0;
        $max_coste = 0;
        dame_costes_consumo_sensor_fechas_tarifa(
            $medicion,
            $nombre_sensor,
            $cadena_fecha_hora_inicio_local_utc,
            $cadena_fecha_hora_fin_local_utc,
            $milisegundos_desfase_zonas_horarias_cliente_local,
            ID_NINGUNO,
            $datos_costes_sensor,
            $coste_total,
            $max_coste);

        // Si no hay datos no se hace nada
        if ($datos_costes_sensor->dame_numero_datos() == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Variables para comparar los costes totales y mostrar los textos de comparación entre las tarifas y el coste real
        $coste_total_actual = $coste_total;
        $costes_totales_tarifas = array();
        $min_coste_total = $coste_total;
        $nombre_tarifa_min_coste_total = "";

        // Se añade la información del coste actual para las gráficas de costes
        if ($max_coste > $max_coste_tarifas)
        {
            $max_coste_tarifas = $max_coste;
        }
        $etiquetas_costes_tarifas->anyade_etiqueta($idiomas->_("Coste actual"));
        $grafica_costes_tarifas->anyade_dato($datos_costes_sensor->dame_datos());

        // Se añade la información del coste actual para las gráficas de costes totales
        if ($coste_total > $max_coste_total_tarifas)
        {
            $max_coste_total_tarifas = $coste_total;
        }
        $etiquetas_costes_totales_tarifas->anyade_etiqueta($idiomas->_("Coste actual"));
        $grafica_costes_totales_tarifas->anyade_tupla_dato($coste_total);

        // Se recorren las tarifas y se actualiza la información de costes
        foreach ($ids_tarifas as $id_tarifa)
        {
            // Nombre de la tarifa
            $nombre_tarifa = $nombres_tarifas[$numero_tarifa];

            // Se recupera la información de la tarifa
            dame_costes_consumo_sensor_fechas_tarifa(
                $medicion,
                $nombre_sensor,
                $cadena_fecha_hora_inicio_local_utc,
                $cadena_fecha_hora_fin_local_utc,
                $milisegundos_desfase_zonas_horarias_cliente_local,
                $id_tarifa,
                $datos_costes_sensor,
                $coste_total,
                $max_coste);

            // Si no hay datos no se hace nada
            if ($datos_costes_sensor->dame_numero_datos() == 0)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }

            // Se actualiza la información de costes totales mínimos para los textos de comparaciones
            // (Nota: Se compara con dos decimales para evitar problemas de redondeos)
            if (round($coste_total, 2) < round($min_coste_total, 2))
            {
                $min_coste_total = round($coste_total, 2);
                $nombre_tarifa_min_coste_total = $nombre_tarifa;
            }
            $costes_totales_tarifas[$nombre_tarifa] = $coste_total;

            // Se añade la información de la tarifa para las gráficas de costes
            if ($max_coste > $max_coste_tarifas)
            {
                $max_coste_tarifas = $max_coste;
            }
            $etiquetas_costes_tarifas->anyade_etiqueta($nombre_tarifa);
            $grafica_costes_tarifas->anyade_dato($datos_costes_sensor->dame_datos());

            // Se añade la información de la tarifa para las gráficas de costes totales
            if ($coste_total > $max_coste_total_tarifas)
            {
                $max_coste_total_tarifas = $coste_total;
            }
            $etiquetas_costes_totales_tarifas->anyade_etiqueta($nombre_tarifa);
            $grafica_costes_totales_tarifas->anyade_tupla_dato($coste_total);

            $numero_tarifa++;
        }

        // Tabla de comparación con el coste actual
        $params_tabla_comparacion_coste_actual = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_COMPARACION_COSTE_ACTUAL_SIMULADOR_TARIFAS,
            "generar_valores_xml" => true
        );
        $tabla_comparacion_coste_actual = new TablaDatos(
            "tabla-comparacion-coste-actual-simulador-tarifas",
            $idiomas->_("Comparación con el coste actual"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_comparacion_coste_actual
        );
        $cabecera_tabla_comparacion_coste_actual = array(
            $idiomas->_("Tarifa"),
            $idiomas->_("Diferencia")
        );
        $tabla_comparacion_coste_actual->anyade_cabecera("", $cabecera_tabla_comparacion_coste_actual);
        foreach ($costes_totales_tarifas as $nombre_tarifa => $coste_total_tarifa)
        {
            if (round($coste_total_tarifa, 2) == round($coste_total_actual, 2))
            {
                $texto_comparacion_coste_actual = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0.00"." ".$unidad_medida_coste.
                    " (<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0.00"." "."%".")";
            }
            else
            {
                if (($coste_total_tarifa == 0) || ($coste_total_actual == 0))
                {
                    $porcentaje = "INF";
                }
                else
                {
                    $porcentaje = dame_porcentaje_valor_referencia($coste_total_tarifa, $coste_total_actual);
                    $cadena_porcentaje = formatea_numero($porcentaje, 2);
                    $diferencia_coste = abs($coste_total_tarifa - $coste_total_actual);
                    $cadena_diferencia_porcentaje = formatea_numero($diferencia_coste, 2);
                }

                if (round($coste_total_tarifa, 2) > round($coste_total_actual, 2))
                {
                    $texto_comparacion_coste_actual = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                        " (<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_porcentaje." "."%".")";
                }
                else
                {
                    $texto_comparacion_coste_actual = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                        " (<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_porcentaje." "."%".")";
                }
            }
            $fila_tabla_comparacion_coste_actual = array(htmlspecialchars($nombre_tarifa, ENT_QUOTES), $texto_comparacion_coste_actual);
            $tabla_comparacion_coste_actual->anyade_fila("", $fila_tabla_comparacion_coste_actual);
        }
        $tabla_comparacion_coste_actual->anyade_pie($idiomas->_("Tarifas").": ".count($costes_totales_tarifas));

        // Tabla de comparación con la opción más barata
        $params_tabla_comparacion_mejor_opcion = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_COMPARACION_COSTE_ACTUAL_SIMULADOR_TARIFAS,
            "generar_valores_xml" => true
        );
        $tabla_comparacion_mejor_opcion = new TablaDatos(
            "tabla-comparacion-mejor-opcion-simulador-tarifas",
            $idiomas->_("Comparación con la opción más barata"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_comparacion_mejor_opcion
        );
        $cabecera_tabla_comparacion_mejor_opcion = array(
            $idiomas->_("Opción"),
            $idiomas->_("Diferencia")
        );
        $tabla_comparacion_mejor_opcion->anyade_cabecera("", $cabecera_tabla_comparacion_mejor_opcion);

        // Se crean los textos a mostrar de comparación con la opción más económica
        if ($nombre_tarifa_min_coste_total == "")
        {
            $texto_comparacion_mejor_opcion = "<i class='icon-sort color-gris-claro'>".
                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                "0.00"." ".$unidad_medida_coste.
                " (<i class='icon-sort color-gris-claro'>".
                "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                "0.00"." "."%".")";
        }
        else
        {
            if (($coste_total_actual == 0) || ($min_coste_total == 0))
            {
                $porcentaje = "INF";
            }
            else
            {
                $porcentaje = dame_porcentaje_valor_referencia($coste_total_actual, $min_coste_total);
                $cadena_porcentaje = formatea_numero($porcentaje, 2);
                $diferencia_coste = abs($coste_total_actual - $min_coste_total);
                $cadena_diferencia_porcentaje = formatea_numero($diferencia_coste, 2);
            }

            if (round($coste_total_actual, 2) > round($min_coste_total, 2))
            {
                $texto_comparacion_mejor_opcion = "<i class='icon-caret-up color-rojo'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                    $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                    " (<i class='icon-caret-up color-rojo'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                    $cadena_porcentaje." "."%".")";
            }
            else
            {
                $texto_comparacion_mejor_opcion = "<i class='icon-caret-down color-verde'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                    $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                    " (<i class='icon-caret-down color-verde'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                    $cadena_porcentaje." "."%".")";
            }
        }
        $fila_tabla_comparacion_mejor_opcion = array($idiomas->_("Coste actual"), $texto_comparacion_mejor_opcion);
        $tabla_comparacion_mejor_opcion->anyade_fila("", $fila_tabla_comparacion_mejor_opcion);
        foreach ($costes_totales_tarifas as $nombre_tarifa => $coste_total_tarifa)
        {
            if (round($coste_total_tarifa, 2) == round($min_coste_total, 2))
            {
                $texto_comparacion_mejor_opcion = "<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0.00"." ".$unidad_medida_coste.
                    " (<i class='icon-sort color-gris-claro'>".
                    "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i> ".
                    "0.00"." "."%".")";
            }
            else
            {
                if (($coste_total_tarifa == 0) || ($min_coste_total == 0))
                {
                    $cadena_porcentaje = "INF";
                }
                else
                {
                    $porcentaje = dame_porcentaje_valor_referencia($coste_total_tarifa, $min_coste_total);
                    $cadena_porcentaje = formatea_numero($porcentaje, 2);
                    $diferencia_coste = abs($coste_total_tarifa - $min_coste_total);
                    $cadena_diferencia_porcentaje = formatea_numero($diferencia_coste, 2);
                }

                if (round($coste_total_tarifa, 2) > round($min_coste_total, 2))
                {
                    $texto_comparacion_mejor_opcion = "<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                        " (<i class='icon-caret-up color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i> +".
                        $cadena_porcentaje." "."%".")";
                }
                else
                {
                    $texto_comparacion_mejor_opcion = "<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_diferencia_porcentaje." ".$unidad_medida_coste.
                        " (<i class='icon-caret-down color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i> -".
                        $cadena_porcentaje." "."%".")";
                }
            }
            $fila_tabla_comparacion_mejor_opcion = array(htmlspecialchars($nombre_tarifa, ENT_QUOTES), $texto_comparacion_mejor_opcion);
            $tabla_comparacion_mejor_opcion->anyade_fila("", $fila_tabla_comparacion_mejor_opcion);
        }
        $tabla_comparacion_mejor_opcion->anyade_pie($idiomas->_("Opciones").": ".(count($costes_totales_tarifas) + 1));

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "grafica_costes" => $grafica_costes_tarifas->dame_datos(),
            "max_coste" => $max_coste_tarifas,
            "etiquetas_costes" => $etiquetas_costes_tarifas->dame_datos(),
            "grafica_costes_totales" => $grafica_costes_totales_tarifas->dame_datos(),
            "max_coste_total" => $max_coste_total_tarifas,
            "etiquetas_costes_totales" => $etiquetas_costes_totales_tarifas->dame_datos(),
            "tabla_comparacion_coste_actual" => $tabla_comparacion_coste_actual->dame_tabla(),
            "tabla_comparacion_mejor_opcion" => $tabla_comparacion_mejor_opcion->dame_tabla());
        return ($resultado);
    }


    function dame_info_mapa_consumos_costes($parametros)
    {
        // Parámetros
        $medicion = $parametros["medicion"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_ratio = $parametros["id_ratio"];
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

        // Se recupera si aplica el ratio
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, CAMPO_TODOS);
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

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_consumo);
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida_coste);
        }

        // Origen de mapa
        $parametros_origen_mapa = array("modulo" => MODULO_SMARTMETER);
        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_SECCION, $parametros_origen_mapa);
        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

        // Nota: en el mapa de consumos y costes siempre se muestran las etiquetas
        $etiquetas_mapa = $_SESSION["etiquetas_mapa"];
        $_SESSION["etiquetas_mapa"] = VALOR_SI;

        // Se recorren los sensores
        $info_mapa_sensores = array();
        for ($i = 0; $i < count($ids_sensores); $i++)
        {
            // Identificador y nombre de sensor
            $id_sensor = $ids_sensores[$i];
            $nombre_sensor = $nombres_sensores[$i];

            // Se recuperación la información de mapa del sensor
            $info_mapa_sensor = dame_info_mapa_sensor_mapa_consumos_costes(
                $origen_mapa,
                $id_origen_mapa,
                $id_ratio,
                $aplicar_ratio,
                $clase_sensor,
                $id_sensor,
                $nombre_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas,
                $unidad_medida_consumo,
                $unidad_medida_coste);
            if ($info_mapa_sensor !== NULL)
            {
                array_push($info_mapa_sensores, $info_mapa_sensor);
            }
        }

        // Se restaura el valor de las etiquetas
        $_SESSION["etiquetas_mapa"] = $etiquetas_mapa;

        // Comprobación de existencia de datos
        if (count($info_mapa_sensores) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recuperan las características de las tarifas
        $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "info_mapa_sensores" => $info_mapa_sensores,
            "unidad_medida_consumo" => $unidad_medida_consumo,
            "curva_coste" => $caracteristicas_tarifas["curva_coste"],
            "unidad_medida_coste" => $unidad_medida_coste);
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Calculo de costes de un sensor con la tarifa especificada
    function dame_costes_consumo_sensor_fechas_tarifa(
        $medicion,
        $nombre_sensor,
        $cadena_fecha_hora_inicio_local_utc,
        $cadena_fecha_hora_fin_local_utc,
        $milisegundos_desfase_zonas_horarias_cliente_local,
        $id_tarifa,
        &$datos_costes,
        &$coste_total,
        &$max_coste)
    {
        // Conversión de fechas
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

        // País de tarifas
        $pais_tarifas = dame_pais_tarifas_medicion($medicion);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_CONSUMO_SENSOR_TARIFA,
                "medicion" => $medicion,
                "pais_tarifas" => $pais_tarifas,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa" => $id_tarifa,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de valores
        $fechas_sensor = $resultado_funcion_externa["fechas_sensor"];
        $costes_sensor = $resultado_funcion_externa["costes_sensor"];
        $coste_total = $resultado_funcion_externa["coste_total_sensor"];

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, NULL);

        // Se recorren los costes del sensor
        $datos_costes = new VectorDatos();
        $max_coste = 0;
        $timestamp_fecha_hora_coste_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        for ($i = 0; $i < count($costes_sensor); $i++)
        {
            // Fecha y coste
            $cadena_fecha_hora_base_datos_utc = $fechas_sensor[$i];
            $coste = $costes_sensor[$i];
            if ($coste > $max_coste)
            {
                $max_coste = $coste;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_coste_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_coste_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_coste_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_coste_utc - $timestamp_fecha_hora_coste_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_costes->anyade_tupla_pareja_datos($timestamp_fecha_hora_coste_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_coste_anterior_utc = $timestamp_fecha_hora_coste_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el coste
            $datos_costes->anyade_tupla_pareja_datos($timestamp_fecha_hora_coste_utc, $coste);
        }
	}


    // Devuelve información de mapa del sensor para el mapa de consumos y costes
	function dame_info_mapa_sensor_mapa_consumos_costes(
        $origen_mapa,
        $id_origen_mapa,
        $id_ratio,
        $aplicar_ratio,
        $clase_sensor,
        $id_sensor,
        $nombre_sensor,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $unidad_medida_consumo,
        $unidad_medida_coste)
	{
		$idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Información de posición en el mapa
        $info_posicion_mapa_sensor = dame_info_posicion_mapa_base_datos(
            TIPO_ELEMENTO_MAPA_SENSOR,
            $id_sensor,
            $origen_mapa,
            $id_origen_mapa);
        if ($info_posicion_mapa_sensor === NULL)
        {
            return (NULL);
        }

        // Se recuperan los datos de consumo y coste del sensor de la base de datos de 'datos'
        // y los datos de estado y posición GPS de la base de datos de 'red'

        // Campo de consumo
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Se realiza la consulta de valores del sensor
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

        // Se recupera la información del ratio (si aplica)
        if ($res_valores_sensor->dame_numero_filas() > 0)
        {
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }
        }

        // Se recorren las filas del sensor
        $total_consumo = NULL;
        $total_coste = NULL;
		while ($fila_valores_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y consumo (si no hay consumo se ignora la fila)
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $consumo = $fila_valores_sensor[$campo_consumo];
            if ($consumo !== NULL)
            {
                $consumo = (float) $consumo;
                if (($aplicar_ratio == true) && ($id_sensor > 0))
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $consumo);
                }
            }
            if ($consumo === NULL)
            {
                continue;
            }

            // Total de consumo
            if ($total_consumo === NULL)
            {
                $total_consumo = 0;
            }
            $total_consumo += $consumo;

            // Coste (si no hay coste se ignora)
            $coste = $fila_valores_sensor['coste'];
            if ($coste !== NULL)
            {
                $coste = (float) $coste;
                if (($aplicar_ratio == true) && ($id_sensor > 0))
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $coste);
                }
            }
            if ($coste !== NULL)
            {
                // Total de coste
                if ($total_coste === NULL)
                {
                    $total_coste = 0;
                }
                $total_coste += $coste;
            }
        }

        // Info de mapa del sensor para el mapa de consumos y costes
        $info_mapa_sensor = array();
        $info_mapa_sensor["consumo"] = $total_consumo;
        $info_mapa_sensor["coste"] = $total_coste;
        $info_mapa_sensor["tooltip"] = "
			<b>".$idiomas->_("Sensor")."</b><br/>".
            $idiomas->_("Nombre").": ".$nombre_sensor."<br/>";
        if (($info_mapa_sensor["consumo"] === NULL) && ($info_mapa_sensor["coste"] === NULL))
        {
            $info_mapa_sensor["tooltip"] .= "<i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("Sin datos")."<br/>";
        }
        else
        {
            if ($info_mapa_sensor["consumo"] !== NULL)
            {
                $info_mapa_sensor["tooltip"] .= "<i class='icon-info-sign color-azul'></i>: ".$idiomas->_("Consumo").": ".
                    formatea_numero($info_mapa_sensor["consumo"], 2)." ".$unidad_medida_consumo."<br/>";
            }
            if ($info_mapa_sensor["coste"] !== NULL)
            {
                $info_mapa_sensor["tooltip"] .= "<i class='icon-info-sign color-azul'></i>: ".$idiomas->_("Coste").": ".
                    formatea_numero($info_mapa_sensor["coste"], 2, false)." ".$unidad_medida_coste."<br/>";
            }
        }

        // Nombre y posición en el mapa
        $info_mapa_sensor["nombre"] = $nombre_sensor;
		$info_mapa_sensor["latitud"] = $info_posicion_mapa_sensor["latitud"];
		$info_mapa_sensor["longitud"] = $info_posicion_mapa_sensor["longitud"];

        // Icono del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $nodo_sensor = Nodo::crea_nodo($id_sensor, TIPO_NODO_SENSOR, $fila_sensor);
        if (($info_mapa_sensor["consumo"] === NULL) && ($info_mapa_sensor["coste"] === NULL))
        {
            $texto_auxiliar_mapa = $idiomas->_("sin datos");
        }
        else
        {
            if ($info_mapa_sensor["consumo"] !== NULL)
            {
                $texto_auxiliar_mapa = formatea_numero($total_consumo, 2)." ".$unidad_medida_consumo;
            }
            if ($info_mapa_sensor["coste"] !== NULL)
            {
                $texto_auxiliar_mapa .= ", ".formatea_numero($total_coste, 2, false)." ".$unidad_medida_coste;
            }
        }
        $nodo_sensor->establece_texto_auxiliar_mapa($texto_auxiliar_mapa);
        $datos_imagen_sensor = $nodo_sensor->dame_datos_imagen_mapa();
        $info_mapa_sensor["icono"] = $datos_imagen_sensor["imagen"];
        $info_mapa_sensor["anchura_icono"] = $datos_imagen_sensor["tamanyo"][0];
        $info_mapa_sensor["altura_icono"] = $datos_imagen_sensor["tamanyo"][1];

        // Se devuelve la información del sensor
        return ($info_mapa_sensor);
	}


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_consumos_costes_generales($medicion)
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_DESCRIPCIONES_SENSORES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_CONSUMOS_MAXIMOS_MINIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES_ACUMULADOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COSTES_MAXIMOS_MINIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_PRECIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_PRECIOS_MAXIMOS_MINIMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COMENTARIOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_consumos_costes_totales($medicion)
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_CONSUMOS_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_COSTES_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PRECIOS_MEDIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_COSTES);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_comparacion_periodos($medicion)
    {
        // Flag para añadir la tabla de evolución de consumos por tramo
        $anyadir_tabla_evolucion_consumos_tramos = false;
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
                if ($caracteristicas_tarifas_electricas["tramos"] == true)
                {
                    $anyadir_tabla_evolucion_consumos_tramos = true;
                    break;
                }
            }
        }

        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_COSTES);
        if ($anyadir_tabla_evolucion_consumos_tramos == true)
        {
            array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_TRAMOS);
        }
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_PRECIOS_MEDIOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_PRECIOS_MEDIOS);
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_simulador_tarifas($medicion)
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES_TOTALES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_COSTE_ACTUAL);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_MEJOR_OPCION);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_consumos_costes_generales($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS:
            {
                $descripcion = "Gráfica de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_CONSUMOS_ACUMULADOS:
            {
                $descripcion = "Gráfica de consumos acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_DESCRIPCIONES_SENSORES:
            {
                $descripcion = "Descripciones de sensores";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_CONSUMOS_MAXIMOS_MINIMOS:
            {
                $descripcion = "Tabla de consumos máximos y mínimos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES:
            {
                $descripcion = "Gráfica de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_COSTES_ACUMULADOS:
            {
                $descripcion = "Gráfica de costes acumulados";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COSTES_MAXIMOS_MINIMOS:
            {
                $descripcion = "Tabla de costes máximos y mínimos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_GRAFICA_PRECIOS:
            {
                $descripcion = "Gráfica de precios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_PRECIOS_MAXIMOS_MINIMOS:
            {
                $descripcion = "Precios máximos y mínimos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COMENTARIOS:
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


    function dame_descripcion_elemento_informe_smartmeter_consumos_costes_totales($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_CONSUMOS_TOTALES:
            {
                $descripcion = "Gráfica de consumos totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_CONSUMOS:
            {
                $descripcion = "Gráfica de porcentajes de consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_CONSUMOS:
            {
                $descripcion = "Tabla de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_COSTES_TOTALES:
            {
                $descripcion = "Gráfica de costes totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PORCENTAJES_COSTES:
            {
                $descripcion = "Gráfica de porcentajes de coste";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_GRAFICA_PRECIOS_MEDIOS:
            {
                $descripcion = "Gráfica de precios medios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TABLA_COSTES:
            {
                $descripcion = "Tabla de costes";
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


    function dame_descripcion_elemento_informe_smartmeter_comparacion_periodos($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS:
            {
                $descripcion = "Gráfica de consumos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES:
            {
                $descripcion = "Gráfica de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_COSTES:
            {
                $descripcion = "Tabla de evolución de consumos y costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_CONSUMOS_TRAMOS:
            {
                $descripcion = "Tabla de evolución de consumos por tramo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_CONSUMOS_TOTALES:
            {
                $descripcion = "Gráfica de consumos totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_COSTES_TOTALES:
            {
                $descripcion = "Gráfica de costes totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_GRAFICA_PRECIOS_MEDIOS:
            {
                $descripcion = "Gráfica de precios medios";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_COMPARACION_PERIODOS_TABLA_EVOLUCION_PRECIOS_MEDIOS:
            {
                $descripcion = "Tabla de evolución de precios medios";
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


    function dame_descripcion_elemento_informe_smartmeter_simulador_tarifas($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES:
            {
                $descripcion = "Gráfica de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_GRAFICA_COSTES_TOTALES:
            {
                $descripcion = "Gráfica de costes totales";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_COSTE_ACTUAL:
            {
                $descripcion = "Tabla de comparación con el coste actual";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TABLA_COMPARACION_MEJOR_OPCION:
            {
                $descripcion = "Tabla de comparación con la mejor opción";
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


    function dame_html_informe_tipo_smartmeter_consumos_costes_generales($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-consumos-costes-generales'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-consumos-costes-generales' hidden>
                        <div class='grafica100' id='grafica-consumos-consumos-costes-generales'></div>
                        <div class='grafica100' id='grafica-consumos-acumulados-consumos-costes-generales'></div>
                        <div class='texto100' id='descripciones-sensores-consumos-costes-generales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100' id='grafica-costes-consumos-costes-generales'></div>
                        <div class='grafica100' id='grafica-costes-acumulados-consumos-costes-generales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-costes-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100' id='grafica-precios-consumos-costes-generales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-precios-maximos-minimos-consumos-costes-generales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-comentarios-consumos-costes-generales'></div>
                        <div id='parametros-resultado-informe-consumos-costes-generales' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Consumos'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-generales-consumos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-generales-consumos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-acumulados-consumos-costes-generales'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripciones-sensores-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Costes'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-generales-costes'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-generales-costes'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-acumulados-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-costes-maximos-minimos-consumos-costes-generales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Precios'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-generales-precios'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-generales-precios'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-precios-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-precios-maximos-minimos-consumos-costes-generales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Comentarios'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-generales-comentarios'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-generales-comentarios'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-consumos-costes-generales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_consumos_costes_totales($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-consumos-costes-totales'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-consumos-costes-totales' hidden>
                        <div class='grafica100' id='grafica-consumos-totales-consumos-costes-totales'></div>
                        <div class='grafica100' id='grafica-porcentajes-consumos-consumos-costes-totales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-consumos-consumos-costes-totales'></div>
                        <div class='grafica100' id='grafica-costes-totales-consumos-costes-totales'></div>
                        <div class='grafica100' id='grafica-porcentajes-costes-consumos-costes-totales'></div>
                        <div class='grafica100' id='grafica-precios-medios-consumos-costes-totales'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-costes-consumos-costes-totales'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Consumos'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-totales-consumos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-totales-consumos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-totales-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-consumos-consumos-costes-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumos-consumos-costes-totales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Costes'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-consumos-costes-totales-costes'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-consumos-costes-totales-costes'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-totales-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-costes-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-precios-medios-consumos-costes-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-costes-consumos-costes-totales'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_comparacion_periodos($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-comparacion-periodos'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-comparacion-periodos' hidden>
                        <div class='grafica100' id='grafica-consumos-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-evolucion-consumos-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-consumos-totales-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-costes-totales-comparacion-periodos'></div>
                        <div class='grafica100' id='grafica-precios-medios-comparacion-periodos'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-evolucion-precios-medios-comparacion-periodos'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página 'Consumos y costes generales'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-periodos-consumos-costes-generales'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-periodos-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-consumos-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página 'Consumos y costes totales'
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-comparacion-periodos-consumos-costes-totales'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-comparacion-periodos-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-totales-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-totales-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-precios-medios-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-evolucion-precios-medios-comparacion-periodos'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_tarifas($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-tarifas'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-tarifas' hidden>
                        <div class='grafica100' id='grafica-costes-simulador-tarifas'></div>
                        <div class='grafica100' id='grafica-costes-totales-simulador-tarifas'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-comparacion-coste-actual-simulador-tarifas'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulador de tarifas
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-tarifas'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_consumos_costes(TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-tarifas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-simulador-tarifas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-totales-simulador-tarifas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comparacion-coste-actual-simulador-tarifas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales(
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-consumos-costes-generales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-consumos-costes-generales'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripciones-sensores-consumos-costes-generales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-consumos-costes-generales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-acumulados-consumos-costes-generales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-precios-consumos-costes-generales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-precios-maximos-minimos-consumos-costes-generales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-consumos-costes-generales'></div>
                        <div id='".$prefijo_elemento."parametros-resultado-informe-consumos-costes-generales' hidden></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-acumulados-consumos-costes-generales'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripciones-sensores-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-acumulados-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-maximos-minimos-consumos-costes-generales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-precios-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-precios-maximos-minimos-consumos-costes-generales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-consumos-costes-generales'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales(
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-totales-consumos-costes-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-consumos-consumos-costes-totales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-consumos-costes-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-consumos-costes-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-consumos-costes-totales'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-precios-medios-consumos-costes-totales'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-consumos-costes-totales'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-totales-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-consumos-consumos-costes-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-consumos-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-consumos-costes-totales'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-precios-medios-consumos-costes-totales'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-costes-consumos-costes-totales'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos(
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
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-consumos-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-consumos-totales-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-comparacion-periodos'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-precios-medios-comparacion-periodos'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-precios-medios-comparacion-periodos'></div>
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
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-consumos-costes-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-consumos-tramos-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-consumos-totales-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-comparacion-periodos'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-precios-medios-comparacion-periodos'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-evolucion-precios-medios-comparacion-periodos'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifas-seleccionadas-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifas seleccionadas")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-simulador-tarifas'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-simulador-tarifas'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comparacion-coste-actual-simulador-tarifas'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas'></div>
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifas-seleccionadas-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifas seleccionadas")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-simulador-tarifas'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-costes-totales-simulador-tarifas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comparacion-coste-actual-simulador-tarifas'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comparacion-mejor-opcion-simulador-tarifas'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_generales(
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
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["agregacion"] = $parametros_tipo_elemento["agregacion"];
        $parametros_informe["comentarios"] = $parametros_tipo_elemento["comentarios"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Se añade la clase a los parámetros
        $medicion = $parametros_tipo_elemento["medicion"];
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $parametros_informe["clase_sensor"] = $clase_sensor;

        $filas_valores_sensores = dame_filas_valores_sensores($parametros_informe);
        $datos_elemento = dame_consumos_costes_sensores_generales($parametros_informe, $filas_valores_sensores);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_consumos_costes_totales(
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
        $parametros_informe["ids_sensores"] = $parametros_tipo_elemento["ids_sensores"];
        $nombres_sensores = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores"]);
        $parametros_informe["nombres_sensores"] = $nombres_sensores;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Se añade la clase a los parámetros
        $medicion = $parametros_tipo_elemento["medicion"];
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $parametros_informe["clase_sensor"] = $clase_sensor;

        $filas_valores_sensores = dame_filas_valores_sensores($parametros_informe);
        $datos_elemento = dame_consumos_costes_sensores_totales($parametros_informe, $filas_valores_sensores);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_comparacion_periodos(
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
        $parametros_informe["medicion"] = $parametros_tipo_elemento["medicion"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Se añade la clase a los parámetros
        $clase_sensor = dame_clase_sensor_medicion($parametros_tipo_elemento["medicion"]);
        $parametros_informe["clase_sensor"] = $clase_sensor;

        $datos_elemento = dame_consumos_costes_sensor_periodos($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_tarifas(
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

        // Si no hay tarifas seleccionadas, se devuelve sin tarifas
        $hay_tarifas_seleccionadas = false;
        if (count($parametros_tipo_elemento["ids_tarifas"]) > 0)
        {
            // Nota: En principio no debería haber ids de tarifas a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (tarifas eliminadas)
            foreach ($parametros_tipo_elemento["ids_tarifas"] as $id_tarifa)
            {
                if ($id_tarifa != ID_NINGUNO)
                {
                    $hay_tarifas_seleccionadas = true;
                    break;
                }
            }
        }
        if ($hay_tarifas_seleccionadas == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_tarifas_seleccionadas" => true);
            return ($resultado);
        }

        $parametros_informe["medicion"] = $parametros_tipo_elemento["medicion"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["ids_tarifas"] = $parametros_tipo_elemento["ids_tarifas"];
        $tabla_tarifas = dame_nombre_tabla_tarifas($parametros_tipo_elemento["medicion"]);
        $nombres_tarifas = dame_nombres_tarifas($tabla_tarifas, $parametros_tipo_elemento["ids_tarifas"]);
        $parametros_informe["nombres_tarifas"] = $nombres_tarifas;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_costes_consumo_sensor_tarifas($parametros_informe, NULL);
        return ($datos_elemento);
    }


    //
    // Funciones auxiliares de informes
    //


    //
    // Funciones de consumos y costes generales y totales
    //


    // Devuelve la información de sensores con la agregación especificada
    function dame_info_sensores_agregacion_consumos_costes(
        $id_ratio,
        $agregacion,
        $ids_sensores,
        $nombres_sensores,
        $clase_sensor,
        $campo_consumo,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $intervalo_valores,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $filas_valores_sensores)
    {
        // Agregación de valores
        switch ($agregacion)
        {
            case AGREGACION_SUMA:
            case AGREGACION_MEDIA:
            {
                // Se recupera la información de agregaciones de sensores
                $res_info_agregaciones = dame_info_agregaciones_sensores_campos(
                    $id_ratio,
                    $ids_sensores,
                    $nombres_sensores,
                    $clase_sensor,
                    array($campo_consumo, CAMPO_COSTE),
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas,
                    $filas_valores_sensores);
                $info_agregaciones_sensores_campos = $res_info_agregaciones["info_agregaciones_sensores"];
                $numeros_sensores_valores_agregaciones_campos = $res_info_agregaciones["numeros_sensores_valores"];

                // Si no hay datos no se hace nada
                if (count($info_agregaciones_sensores_campos[$campo_consumo]) == 0)
                {
                    $resultado = array(
                        "res" => "OK",
                        "hay_datos" => false);
                    return ($resultado);
                }

                // Se simula un solo sensor (con los valores de la agregación correspondiente)
                $filas_valores_agregados = array();
                $horas_agregacion = (dame_segundos_intervalo_valores($intervalo_valores) / 3600);
                $info_agregaciones_sensores_consumo = $info_agregaciones_sensores_campos[$campo_consumo];
                $info_agregaciones_sensores_coste = $info_agregaciones_sensores_campos[CAMPO_COSTE];
                $numero_agregaciones_sensores_coste = count($info_agregaciones_sensores_coste);
                $indice_agregaciones_sensores_coste = 0;
                for ($i = 0; $i < count($info_agregaciones_sensores_campos[$campo_consumo]); $i++)
                {
                    $info_agregacion_sensores_consumo = $info_agregaciones_sensores_consumo[$i];
                    $cadena_fecha_hora_agregacion_consumo_base_datos_utc = $info_agregacion_sensores_consumo["cadena_fecha_hora_agregacion_base_datos_utc"];
                    $timestamp_fecha_hora_agregacion_consumo_utc = $info_agregacion_sensores_consumo["timestamp_fecha_agregacion_utc"];
                    switch ($agregacion)
                    {
                        case AGREGACION_SUMA:
                        {
                            $valor_agregacion_consumo = array_sum($info_agregacion_sensores_consumo["valores"]);
                            break;
                        }
                        case AGREGACION_MEDIA:
                        {
                            $valor_agregacion_consumo = array_sum($info_agregacion_sensores_consumo["valores"]) / count($info_agregacion_sensores_consumo["valores"]);
                            break;
                        }
                    }
                    $numero_sensores_sin_consumo = count($ids_sensores) - count($info_agregacion_sensores_consumo["ids_sensores"]);

                    // Se busca la agregación de coste correspondiente (puede no existir)
                    $agregacion_sensores_coste_encontrada = false;
                    while ($agregacion_sensores_coste_encontrada == false)
                    {
                        if ($indice_agregaciones_sensores_coste >= $numero_agregaciones_sensores_coste)
                        {
                            break;
                        }
                        $info_agregacion_sensores_coste = $info_agregaciones_sensores_coste[$indice_agregaciones_sensores_coste];
                        $timestamp_fecha_hora_agregacion_coste_utc = $info_agregacion_sensores_coste["timestamp_fecha_agregacion_utc"];
                        if ($timestamp_fecha_hora_agregacion_coste_utc < $timestamp_fecha_hora_agregacion_consumo_utc)
                        {
                            $indice_agregaciones_sensores_coste += 1;
                            continue;
                        }
                        else
                        {
                            if ($timestamp_fecha_hora_agregacion_coste_utc == $timestamp_fecha_hora_agregacion_consumo_utc)
                            {
                                $agregacion_sensores_coste_encontrada = true;
                                switch ($agregacion)
                                {
                                    case AGREGACION_SUMA:
                                    {
                                        $valor_agregacion_coste = array_sum($info_agregacion_sensores_coste["valores"]);
                                        break;
                                    }
                                    case AGREGACION_MEDIA:
                                    {
                                        $valor_agregacion_coste = array_sum($info_agregacion_sensores_coste["valores"]) / count($info_agregacion_sensores_coste["valores"]);
                                        break;
                                    }
                                    $numero_sensores_sin_coste = count($ids_sensores) - count($info_agregacion_sensores_coste["ids_sensores"]);
                                }
                            }
                            break;
                        }
                    }

                    // Fila de valores (con los valores agregados de consumos y costes)
                    $fila = array(
                        "fecha_hora" => $cadena_fecha_hora_agregacion_consumo_base_datos_utc,
                        $campo_consumo => $valor_agregacion_consumo,
                        "horas" => $horas_agregacion,
                        "numero_sensores_sin_consumo" => $numero_sensores_sin_consumo);
                    if ($agregacion_sensores_coste_encontrada == true)
                    {
                        $fila[CAMPO_COSTE] = $valor_agregacion_coste;
                        $fila["numero_sensores_sin_coste"] = $numero_sensores_sin_coste;
                    }
                    else
                    {
                        $fila[CAMPO_COSTE] = NULL;
                    }
                    array_push($filas_valores_agregados, $fila);
                }

                // Nombre de agregación
                $res_cadenas_inicio_fin = dame_cadenas_inicio_fin_nombre_agregacion();
                $cadena_inicio_nombre_agregacion = $res_cadenas_inicio_fin["cadena_inicio"];
                $cadena_fin_nombre_agregacion = $res_cadenas_inicio_fin["cadena_fin"];
                $nombre_agregacion =
                    $cadena_inicio_nombre_agregacion.
                    dame_descripcion_agregacion($agregacion).
                    $cadena_fin_nombre_agregacion;

                // Simulación de un sólo sensor (con los valores agregados)
                $ids_sensores = array(NULL);
                $nombres_sensores = array($nombre_agregacion);
                $filas_valores_sensores = array($nombre_agregacion => $filas_valores_agregados);
                break;
            }
            default:
            {
                throw new Exception("Agregación incorrecta: '".$agregacion."'");
            }
        }
        $info_sensores_clase_agregacion = array(
            "hay_datos" => true,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "filas_valores_sensores" => $filas_valores_sensores,
            "numeros_sensores_valores_agregaciones_campos" => $numeros_sensores_valores_agregaciones_campos);
        return ($info_sensores_clase_agregacion);
    }
?>
