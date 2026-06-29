<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/Espanya/util_potencias_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/InformesFichero/util_potencias_informes_fichero.php');


    //
    // Funciones de información de potencias (Espanya)
    //


    // Devuelve la información de costes y potencias óptimas de un sensor y una tarifa eléctrica
    function dame_costes_potencias_optimas_sensor_tarifa_electricidad_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $granularidad = $parametros["granularidad"];
        $rango_potencias = $parametros["rango_potencias"];
        $diferencia_potencia = $parametros["diferencia_potencia"];
        $cadena_horario_semanal = $parametros["horario_semanal"];
        $cadena_exclusion_fechas = $parametros["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros["inclusion_fechas"];
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

        // Llamada a función 'externa'
        $porcentaje_rango_potencias_tramo = dame_porcentaje_rango_potencias_tramo_optimizador_simulador_potencias_Espanya($rango_potencias);
        $porcentaje_rango_potencias_potencia_optima_tramo_contiguo = dame_porcentaje_rango_potencias_potencia_optima_tramo_contiguo_optimizador_potencias_Espanya($rango_potencias);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_OPTIMAS_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa_electrica" => $id_tarifa,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_base_datos_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_base_datos_utc,
                "porcentaje_rango_potencias_tramo" => $porcentaje_rango_potencias_tramo,
                "porcentaje_rango_potencias_potencia_optima_tramo_contiguo" => $porcentaje_rango_potencias_potencia_optima_tramo_contiguo,
                "granularidad" => $granularidad,
                "diferencia_potencia" => $diferencia_potencia,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas
            );
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si no hay datos de consumo, no se hace nada
        if ($resultado_funcion_externa["hay_datos_consumo"] == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }
        else
        {
            // Se crea la tabla de potencias óptimas por tramo
            $potencias_actuales_optimas = false;
            $tabla_potencias_optimas_tramos = dame_tabla_potencias_optimas_tramos_Espanya(
                "tabla-potencias-optimas-tarifa-electrica",
                $resultado_funcion_externa["datos_potencias_tramos"],
                $potencias_actuales_optimas);

            // Se recupera el paso máximo de los datos de potencias
            $paso_maximo_potencias = dame_paso_maximo_potencias_tramos_Espanya($resultado_funcion_externa["datos_potencias_tramos"]);

            // Gráfica de potencias
            $info_grafica_potencias = dame_info_grafica_potencias_electricidad_Espanya(
                $nombre_sensor,
                $resultado_funcion_externa["filas_potencias_sensor"],
                $granularidad,
                $minutos_desfase_utc);
            $periodo_fechas = $info_grafica_potencias["periodo_fechas"];
            if ($periodo_fechas->y < 1)
            {
                $msg_aviso = $idiomas->_("El periodo de datos de potencia es menor que 1 año (se recomienda tener al menos 1 año de datos de potencia)");
            }
            else
            {
                $msg_aviso = "";
            }

            // Resultado
            $resultado_sin_grafica_potencias = array(
                "res" => "OK",
                "msg_aviso" => $msg_aviso,
                "paso_maximo_potencias" => $paso_maximo_potencias,
                "tabla_potencias_optimas_tramos" => $tabla_potencias_optimas_tramos->dame_tabla(),
                "datos_potencias_tramos" => $resultado_funcion_externa["datos_potencias_tramos"],
                "potencia_minima" => $resultado_funcion_externa["potencia_minima"],
                "potencia_maxima" => $resultado_funcion_externa["potencia_maxima"],
                "potencias_actuales_optimas" => $potencias_actuales_optimas);
            $resultado = array_merge($resultado_sin_grafica_potencias, $info_grafica_potencias);

            // Se devuelve el resultado
            return ($resultado);
        }
    }


    // Devuelve la información de costes y potencias óptimas de datos de fichero y una tarifa eléctrica
    function dame_costes_potencias_optimas_fichero_tarifa_electricidad_Espanya($parametros, $ficheros)
    {
        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        // Fichero de potencias máximas
        if (array_key_exists("fichero_potencias_maximas", $ficheros) == true)
        {
            $ruta_fichero_potencias_maximas = $ficheros["fichero_potencias_maximas"]["tmp_name"];
        }
        else
        {
            if (array_key_exists("ruta_fichero_potencias_maximas", $parametros) == true)
            {
                $ruta_fichero_potencias_maximas = $parametros["ruta_fichero_potencias_maximas"];
            }
            else
            {
                throw new Exception("No se ha podido recuperar la ruta del fichero de potencias máximas");
            }
        }

        // Número de tramos (sólo para tarifas eléctricas de 3 tramos)
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];

        // Se lee el fichero de potencias máximas
        $potencias_maximas_mensuales = array();
        $mensaje_error = NULL;
        $fichero_potencias_maximas_correcto = lee_fichero_potencias_maximas_mensuales_Espanya(
            $ruta_fichero_potencias_maximas,
            $numero_tramos,
            $potencias_maximas_mensuales,
            $mensaje_error);

        // Si no se ha leído el fichero correctamente se muestra un mensaje de error
        if ($fichero_potencias_maximas_correcto == false)
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        else
        {
            // Llamada a la función externa
            $porcentaje_rango_potencias_tramo = dame_porcentaje_rango_potencias_tramo_optimizador_simulador_potencias_Espanya(RANGO_POTENCIAS_MAXIMO);
            $porcentaje_rango_potencias_potencia_optima_tramo_contiguo = dame_porcentaje_rango_potencias_potencia_optima_tramo_contiguo_optimizador_potencias_Espanya(RANGO_POTENCIAS_MAXIMO);
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_OPTIMAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA,
                    "numero_tramos" => $numero_tramos,
                    "potencias_maximas_mensuales" => $potencias_maximas_mensuales,
                    "id_tarifa_electrica" => $id_tarifa,
                    "porcentaje_rango_potencias_tramo" => $porcentaje_rango_potencias_tramo,
                    "porcentaje_rango_potencias_potencia_optima_tramo_contiguo" => $porcentaje_rango_potencias_potencia_optima_tramo_contiguo
                );
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si no hay datos de consumo, no se hace nada
            if ($resultado_funcion_externa["hay_datos_consumo"] == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
            else
            {
                // Se crea la tabla de potencias óptimas por tramo
                $potencias_actuales_optimas = false;
                $tabla_potencias_optimas_tramos = dame_tabla_potencias_optimas_tramos_Espanya(
                    "tabla-potencias-optimas-tarifa-electrica",
                    $resultado_funcion_externa["datos_potencias_tramos"],
                    $potencias_actuales_optimas);

                // Se recupera el paso máximo de los datos de potencias
                $paso_maximo_potencias = dame_paso_maximo_potencias_tramos_Espanya($resultado_funcion_externa["datos_potencias_tramos"]);

                // Resultado
                $resultado = array(
                    "res" => "OK",
                    "msg_aviso" => "",
                    "paso_maximo_potencias" => $paso_maximo_potencias,
                    "tabla_potencias_optimas_tramos" => $tabla_potencias_optimas_tramos->dame_tabla(),
                    "datos_potencias_tramos" => $resultado_funcion_externa["datos_potencias_tramos"],
                    "potencia_minima" => $resultado_funcion_externa["potencia_minima"],
                    "potencia_maxima" => $resultado_funcion_externa["potencia_maxima"],
                    "potencias_actuales_optimas" => $potencias_actuales_optimas);
                return ($resultado);
            }
        }
    }


    // Devuelve la información de costes y potencias seleccionadas de un sensor y una tarifa eléctrica
    function dame_costes_potencias_seleccionadas_sensor_tarifa_electricidad_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $granularidad = $parametros["granularidad"];
        $potencias_seleccionadas = $parametros["potencias_seleccionadas"];
        $rango_potencias = $parametros["rango_potencias"];
        $diferencia_potencia = $parametros["diferencia_potencia"];
        $cadena_horario_semanal = $parametros["horario_semanal"];
        $cadena_exclusion_fechas = $parametros["exclusion_fechas"];
        $cadena_inclusion_fechas = $parametros["inclusion_fechas"];
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

        // Llamada a función 'externa'
        $porcentaje_rango_potencias_tramo = dame_porcentaje_rango_potencias_tramo_optimizador_simulador_potencias_Espanya($rango_potencias);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_SELECCIONADAS_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa_electrica" => $id_tarifa,
                "potencias_seleccionadas" => $potencias_seleccionadas,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_base_datos_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_base_datos_utc,
                "porcentaje_rango_potencias_tramo" => $porcentaje_rango_potencias_tramo,
                "granularidad" => $granularidad,
                "diferencia_potencia" => $diferencia_potencia,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas
            );
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si no hay datos de consumo, no se hace nada
        if ($resultado_funcion_externa["hay_datos_consumo"] == false)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }
        else
        {
            // Se crea la tabla de potencias seleccionadas por tramo
            $tabla_potencias_seleccionadas_tramos = dame_tabla_potencias_seleccionadas_tramos_Espanya(
                "tabla-potencias-seleccionadas-tarifa-electrica",
                $resultado_funcion_externa["datos_potencias_tramos"]);

            // Se recupera el paso máximo de los datos de potencias
            $paso_maximo_potencias = dame_paso_maximo_potencias_tramos_Espanya($resultado_funcion_externa["datos_potencias_tramos"]);

            // Gráfica de potencias
            $info_grafica_potencias = dame_info_grafica_potencias_electricidad_Espanya(
                $nombre_sensor,
                $resultado_funcion_externa["filas_potencias_sensor"],
                $granularidad,
                $minutos_desfase_utc);
            $periodo_fechas = $info_grafica_potencias["periodo_fechas"];
            if ($periodo_fechas->y < 1)
            {
                $msg_aviso = $idiomas->_("El periodo de datos de potencia es menor que 1 año (se recomienda tener al menos 1 año de datos de potencia)");
            }
            else
            {
                $msg_aviso = "";
            }

            // Resultado
            $resultado_sin_grafica_potencias = array(
                "res" => "OK",
                "msg_aviso" => $msg_aviso,
                "paso_maximo_potencias" => $paso_maximo_potencias,
                "tabla_potencias_seleccionadas_tramos" => $tabla_potencias_seleccionadas_tramos->dame_tabla(),
                "datos_potencias_tramos" => $resultado_funcion_externa["datos_potencias_tramos"],
                "potencia_minima" => $resultado_funcion_externa["potencia_minima"],
                "potencia_maxima" => $resultado_funcion_externa["potencia_maxima"]);
            $resultado = array_merge($resultado_sin_grafica_potencias, $info_grafica_potencias);

            // Se devuelve el resultado
            return ($resultado);
        }
    }


    // Devuelve la información de costes y potencias seleccionadas de datos de fichero y una tarifa eléctrica
    function dame_costes_potencias_seleccionadas_fichero_tarifa_electricidad_Espanya($parametros, $ficheros)
    {
        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        // Potencias seleccionadas
        // Nota: Si hay fichero, las potencias seleccionadas se pasan separadas por comas (se utiliza 'ajax' directamente, no 'POST')
        if (count($ficheros) == 0)
        {
            $potencias_seleccionadas = $parametros["potencias_seleccionadas"];
        }
        else
        {
            $potencias_seleccionadas = explode(",", $parametros["potencias_seleccionadas"]);
        }

        // Fichero de potencias máximas
        if (array_key_exists("fichero_potencias_maximas", $ficheros) == true)
        {
            $ruta_fichero_potencias_maximas = $ficheros["fichero_potencias_maximas"]["tmp_name"];
        }
        else
        {
            if (array_key_exists("ruta_fichero_potencias_maximas", $parametros) == true)
            {
                $ruta_fichero_potencias_maximas = $parametros["ruta_fichero_potencias_maximas"];
            }
            else
            {
                throw new Exception("No se ha podido recuperar la ruta del fichero de potencias máximas");
            }
        }

        // Número de tramos
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];

        // Se lee el fichero de potencias máximas
        $potencias_maximas_mensuales = array();
        $mensaje_error = NULL;
        $fichero_potencias_maximas_correcto = lee_fichero_potencias_maximas_mensuales_Espanya(
            $ruta_fichero_potencias_maximas,
            $numero_tramos,
            $potencias_maximas_mensuales,
            $mensaje_error);

        // Si no se ha leído el fichero correctamente se muestra un mensaje de error
        if ($fichero_potencias_maximas_correcto == false)
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        else
        {
            // Llamada a la función externa
            $porcentaje_rango_potencias_tramo = dame_porcentaje_rango_potencias_tramo_optimizador_simulador_potencias_Espanya(RANGO_POTENCIAS_MAXIMO);
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_COSTES_POTENCIAS_SELECCIONADAS_POTENCIAS_MAXIMAS_MENSUALES_TARIFA_ELECTRICA_ESPANYA,
                    "numero_tramos" => $numero_tramos,
                    "potencias_maximas_mensuales" => $potencias_maximas_mensuales,
                    "id_tarifa_electrica" => $id_tarifa,
                    "porcentaje_rango_potencias_tramo" => $porcentaje_rango_potencias_tramo,
                    "potencias_seleccionadas" => $potencias_seleccionadas
                );
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si no hay datos de consumo, no se hace nada
            if ($resultado_funcion_externa["hay_datos_consumo"] == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
            else
            {
                // Se crea la tabla de potencias seleccionadas
                $tabla_potencias_seleccionadas_tramos = dame_tabla_potencias_seleccionadas_tramos_Espanya(
                    "tabla-potencias-seleccionadas-fichero-tarifa-electrica",
                    $resultado_funcion_externa["datos_potencias_tramos"]);

                // Se recupera el paso máximo de los datos de potencias
                $paso_maximo_potencias = dame_paso_maximo_potencias_tramos_Espanya($resultado_funcion_externa["datos_potencias_tramos"]);

                // Resultado
                $resultado = array(
                    "res" => "OK",
                    "msg_aviso" => "",
                    "paso_maximo_potencias" => $paso_maximo_potencias,
                    "tabla_potencias_seleccionadas_tramos" => $tabla_potencias_seleccionadas_tramos->dame_tabla(),
                    "datos_potencias_tramos" => $resultado_funcion_externa["datos_potencias_tramos"],
                    "potencia_minima" => $resultado_funcion_externa["potencia_minima"],
                    "potencia_maxima" => $resultado_funcion_externa["potencia_maxima"]);
                return ($resultado);
            }
        }
    }


    //
    // Funciones auxiliares
    //


    // Devuelve la información de gráfica de potencias
    function dame_info_grafica_potencias_electricidad_Espanya(
        $nombre_sensor,
        $filas_potencias_sensor,
        $granularidad,
        $minutos_desfase_utc)
    {
        // Milisegundos de desfase de zonas horarias
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables para la gráfica de potencias
        $datos_grafica_potencias = new VectorDatos();
        $max_potencia = -INF;
        $cadena_min_fecha = NULL;
        $cadena_max_fecha = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        switch ($granularidad)
        {
            case GRANULARIDAD_HORARIA:
            {
                $intervalo_valores = INTERVALO_VALORES_HORA;
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                break;
            }
        }
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, NULL);

        // Se recorren las potencias
        $timestamp_fecha_hora_potencia_anterior = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_potencias_sensor as $fila)
        {
            // Potencia y fecha
            $potencia = (float) $fila["potencia"];
            if ($potencia > $max_potencia)
            {
                $max_potencia = $potencia;
            }
            if ($cadena_min_fecha === NULL)
            {
                $cadena_min_fecha = $fila["hora"];
            }
            $cadena_max_fecha = $fila["hora"];

            // Hora
            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_local = $fila["hora"];
            $cadena_hora_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_local, FORMATO_FECHA_HORA_FUNCIONES, $zona_horaria, ZONA_HORARIA_UTC);

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_potencia = dame_timestamp_cadena_fecha_milisegundos($cadena_hora_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_potencia -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_potencia_anterior !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_potencia - $timestamp_fecha_hora_potencia_anterior) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_potencias->anyade_tupla_pareja_datos($timestamp_fecha_hora_potencia_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_potencia_anterior = $timestamp_fecha_hora_potencia;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade la potencia
            $datos_grafica_potencias->anyade_tupla_pareja_datos($timestamp_fecha_hora_potencia, $potencia);
        }

        // Periodo de fechas
        if (($cadena_min_fecha !== NULL) && ($cadena_max_fecha !== NULL))
        {
            $min_fecha = convierte_cadena_a_fecha($cadena_min_fecha, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $max_fecha = convierte_cadena_a_fecha($cadena_max_fecha, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $periodo_fechas = $max_fecha->diff($min_fecha);
        }

        // Variables para dibujar las gráficas
        $etiquetas_grafica_potencias = new VectorDatos();
        $etiquetas_grafica_potencias->anyade_etiqueta($nombre_sensor);

        $grafica_potencias = new VectorDatos();
        $grafica_potencias->anyade_dato($datos_grafica_potencias->dame_datos());

        // Resultado
        $info_grafica_potencias = array(
            "max_potencia" => $max_potencia,
            "etiquetas_grafica_potencias" => $etiquetas_grafica_potencias->dame_datos(),
            "grafica_potencias" => $grafica_potencias->dame_datos(),
            "intervalo_valores" => $intervalo_valores,
            "periodo_fechas" => $periodo_fechas);
        return ($info_grafica_potencias);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_optimizador_potencias_automatico_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-optimizador-potencias-automatico'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-optimizador-potencias-automatico' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-automatico'></div>
                        <div class='grafica100' id='grafica-potencias-optimizador-potencias-automatico'></div>";
                    for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                    {
                        $html_informe .= "
                            <div class='grafica100' id='grafica-costes-potencias-tramos-optimizador-potencias-automatico-".$i."'></div>";
                    }
                $html_informe .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de optimización de potencias automático
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-optimizador-potencias-automatico'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_potencias(TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-optimizador-potencias-automatico'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-potencias-optimizador-potencias-automatico'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                {
                    $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-potencias-tramos-optimizador-potencias-automatico-".$i."'></div>";
                }
                $html_informe .= "
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_optimizador_potencias_manual_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-optimizador-potencias-manual'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-optimizador-potencias-manual' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-manual'></div>";
                    for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                    {
                        $html_informe .= "
                            <div class='grafica100' id='grafica-costes-potencias-tramos-optimizador-potencias-manual-".$i."'></div>";
                    }
                $html_informe .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-optimizador-potencias-manual'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_potencias(TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-optimizador-potencias-manual'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencias-optimas-tramos-optimizador-potencias-manual'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                {
                    $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-potencias-tramos-optimizador-potencias-manual-".$i."'></div>";
                }
                $html_informe .= "
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_potencias_automatico_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-potencias-automatico'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-potencias-automatico' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-automatico'></div>
                        <div class='grafica100' id='grafica-potencias-simulador-potencias-automatico'></div>";
                    for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                    {
                        $html_informe .= "
                            <div class='grafica100' id='grafica-costes-potencias-tramos-simulador-potencias-automatico-".$i."'></div>";
                    }
                $html_informe .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de potencias automático
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-potencias-automatico'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_potencias(TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-potencias-automatico'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-potencias-simulador-potencias-automatico'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                {
                    $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-potencias-tramos-simulador-potencias-automatico-".$i."'></div>";
                }
                $html_informe .= "
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_potencias_manual_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-potencias-manual'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-potencias-manual' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-manual'></div>";
                    for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                    {
                        $html_informe .= "
                            <div class='grafica100' id='grafica-costes-potencias-tramos-simulador-potencias-manual-".$i."'></div>";
                    }
                $html_informe .= "
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de potencias manual
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-potencias-manual'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_potencias(TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-potencias-manual'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencias-seleccionadas-tramos-simulador-potencias-manual'></div>";
                for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
                {
                    $html_informe .= "
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-potencias-tramos-simulador-potencias-manual-".$i."'></div>";
                }
                $html_informe .= "
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
