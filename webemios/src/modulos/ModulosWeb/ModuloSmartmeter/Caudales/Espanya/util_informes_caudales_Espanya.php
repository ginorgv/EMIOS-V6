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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/Espanya/util_caudales_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/InformesFichero/util_caudales_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones de información de caudales (Espanya)
    //


    // Devuelve la información de coste y caudal diario óptimo de un sensor y una tarifa de gas
    function dame_coste_caudal_diario_optimo_sensor_tarifa_gas_Espanya($parametros)
    {
        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $diferencia_caudal = $parametros["diferencia_caudal"];
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
        $porcentaje_rango_caudales_diarios = dame_porcentaje_rango_caudales_diarios_optimizador_simulador_caudales_Espanya(RANGO_CAUDALES_DIARIOS_MAXIMO);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_SENSOR_TARIFA_GAS_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa_gas" => $id_tarifa,
                "porcentaje_rango_caudales_diarios" => $porcentaje_rango_caudales_diarios,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_base_datos_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_base_datos_utc,
                "diferencia_caudal" => $diferencia_caudal,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas,
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
            // Información de costes y caudales diarios
            $info_costes_caudales_diarios = $resultado_funcion_externa;

            // Se crea la tabla de caudal diario óptimo
            $caudal_diario_actual_optimo = false;
            $tabla_caudal_diario_optimo = dame_tabla_caudal_diario_optimo_Espanya(
                "tabla-caudal-diario-optimo-sensor-tarifa-gas",
                $info_costes_caudales_diarios,
                $caudal_diario_actual_optimo);

            // Se recupera el paso máximo de los datos de caudales diarios
            $paso_maximo_caudales_diarios = dame_paso_maximo_caudales_diarios_Espanya($info_costes_caudales_diarios);

            // Gráfica de caudales diarios
            $info_grafica_caudales_diarios = dame_info_grafica_caudales_diarios_gas_Espanya(
                $nombre_sensor,
                $resultado_funcion_externa["filas_caudales_diarios_sensor"],
                $minutos_desfase_utc);

            // Resultado
            $resultado_sin_grafica_caudales_diarios = array(
                "res" => "OK",
                "hay_datos" => true,
                "paso_maximo_caudales_diarios" => $paso_maximo_caudales_diarios,
                "tabla_caudal_diario_optimo" => $tabla_caudal_diario_optimo->dame_tabla(),
                "caudales_diarios_costes" => $info_costes_caudales_diarios["caudales_diarios_costes"],
                "coste_caudal_diario_optimo" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
                "caudal_diario_optimo" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
                "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
                "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
                "coste_minimo_caudal_diario" => $info_costes_caudales_diarios["coste_minimo_caudal_diario"],
                "coste_maximo_caudal_diario" => $info_costes_caudales_diarios["coste_maximo_caudal_diario"],
                "caudal_diario_minimo" => $info_costes_caudales_diarios["caudal_diario_minimo"],
                "caudal_diario_maximo" => $info_costes_caudales_diarios["caudal_diario_maximo"],
                "caudal_diario_actual_optimo" => $caudal_diario_actual_optimo);
            $resultado = array_merge($resultado_sin_grafica_caudales_diarios, $info_grafica_caudales_diarios);

            // Se devuelve el resultado
            return ($resultado);
        }
    }


    // Devuelve la información de coste y caudal diario óptimo de datos de fichero y una tarifa de gas
    function dame_coste_caudal_diario_optimo_fichero_tarifa_gas_Espanya($parametros, $ficheros)
    {
        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        // Fichero de caudales diarios máximos
        if (array_key_exists("fichero_caudales_maximos", $ficheros) == true)
        {
            $ruta_fichero_caudales_diarios_maximos = $ficheros["fichero_caudales_maximos"]["tmp_name"];
        }
        else
        {
            if (array_key_exists("ruta_fichero_caudales_maximos", $parametros) == true)
            {
                $ruta_fichero_caudales_diarios_maximos = $parametros["ruta_fichero_caudales_maximos"];
            }
            else
            {
                throw new Exception("No se ha podido recuperar la ruta del fichero de caudales diarios máximos");
            }
        }

        // Se lee el fichero de caudales diarios máximos
        $caudales_diarios_maximos_mensuales = array();
        $mensaje_error = NULL;
        $fichero_caudales_diarios_maximos_correcto = lee_fichero_caudales_diarios_maximos_mensuales_Espanya(
            $ruta_fichero_caudales_diarios_maximos,
            $caudales_diarios_maximos_mensuales,
            $mensaje_error);

        // Si no se ha leído el fichero correctamente se muestra un mensaje de error
        if ($fichero_caudales_diarios_maximos_correcto == false)
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        else
        {
            // Llamada a la función externa
            $porcentaje_rango_caudales_diarios = dame_porcentaje_rango_caudales_diarios_optimizador_simulador_caudales_Espanya(RANGO_CAUDALES_DIARIOS_MAXIMO);
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_OPTIMO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA,
                    "caudales_diarios_maximos_mensuales" => $caudales_diarios_maximos_mensuales,
                    "id_tarifa_gas" => $id_tarifa,
                    "porcentaje_rango_caudales_diarios" => $porcentaje_rango_caudales_diarios
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
                // Información de costes y caudales diarios
                $info_costes_caudales_diarios = $resultado_funcion_externa;

                // Se crea la tabla de caudal diario óptimo
                $caudal_diario_actual_optimo = false;
                $tabla_caudal_diario_optimo = dame_tabla_caudal_diario_optimo_Espanya(
                    "tabla-caudal-diario-optimo-fichero-tarifa-gas",
                    $info_costes_caudales_diarios,
                    $caudal_diario_actual_optimo);

                // Se recupera el paso máximo de los datos de caudales diarios
                $paso_maximo_caudales_diarios = dame_paso_maximo_caudales_diarios_Espanya($info_costes_caudales_diarios);

                // Resultado
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => true,
                    "paso_maximo_caudales_diarios" => $paso_maximo_caudales_diarios,
                    "tabla_caudal_diario_optimo" => $tabla_caudal_diario_optimo->dame_tabla(),
                    "caudales_diarios_costes" => $info_costes_caudales_diarios["caudales_diarios_costes"],
                    "coste_caudal_diario_optimo" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
                    "caudal_diario_optimo" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
                    "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
                    "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
                    "coste_minimo_caudal_diario" => $info_costes_caudales_diarios["coste_minimo_caudal_diario"],
                    "coste_maximo_caudal_diario" => $info_costes_caudales_diarios["coste_maximo_caudal_diario"],
                    "caudal_diario_minimo" => $info_costes_caudales_diarios["caudal_diario_minimo"],
                    "caudal_diario_maximo" => $info_costes_caudales_diarios["caudal_diario_maximo"],
                    "caudal_diario_actual_optimo" => $caudal_diario_actual_optimo);
                return ($resultado);
            }
        }
    }


    // Devuelve la información de coste y caudal diario seleccionado de un sensor y una tarifa de gas
    function dame_coste_caudal_diario_seleccionado_sensor_tarifa_gas_Espanya($parametros)
    {
        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $caudal_diario_seleccionado = $parametros["caudal_seleccionado"];
        $diferencia_caudal = $parametros["diferencia_caudal"];
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
        $porcentaje_rango_caudales_diarios = dame_porcentaje_rango_caudales_diarios_optimizador_simulador_caudales_Espanya(RANGO_CAUDALES_DIARIOS_MAXIMO);
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_SENSOR_TARIFA_GAS_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa_gas" => $id_tarifa,
                "porcentaje_rango_caudales_diarios" => $porcentaje_rango_caudales_diarios,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_base_datos_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_base_datos_utc,
                "caudal_diario_seleccionado" => $caudal_diario_seleccionado,
                "diferencia_caudal" => $diferencia_caudal,
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
            // Información de costes y caudales diarios
            $info_costes_caudales_diarios = $resultado_funcion_externa;

            // Se crea la tabla de caudal diario seleccionado
            $tabla_caudal_diario_seleccionado = dame_tabla_caudal_diario_seleccionado_Espanya(
                "tabla-caudal-diario-seleccionado-sensor-tarifa-gas",
                $info_costes_caudales_diarios);

            // Se recupera el paso máximo de los datos de caudales diarios
            $paso_maximo_caudales_diarios = dame_paso_maximo_caudales_diarios_Espanya($info_costes_caudales_diarios);

            // Gráfica de caudales diarios
            $info_grafica_caudales_diarios = dame_info_grafica_caudales_diarios_gas_Espanya(
                $nombre_sensor,
                $resultado_funcion_externa["filas_caudales_diarios_sensor"],
                $minutos_desfase_utc);

            // Resultado
            $resultado_sin_grafica_caudales_diarios = array(
                "res" => "OK",
                "hay_datos" => true,
                "paso_maximo_caudales_diarios" => $paso_maximo_caudales_diarios,
                "tabla_caudal_diario_seleccionado" => $tabla_caudal_diario_seleccionado->dame_tabla(),
                "caudales_diarios_costes" => $info_costes_caudales_diarios["caudales_diarios_costes"],
                "coste_caudal_diario_seleccionado" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
                "caudal_diario_seleccionado" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
                "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
                "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
                "coste_minimo_caudal_diario" => $info_costes_caudales_diarios["coste_minimo_caudal_diario"],
                "coste_maximo_caudal_diario" => $info_costes_caudales_diarios["coste_maximo_caudal_diario"],
                "caudal_diario_minimo" => $info_costes_caudales_diarios["caudal_diario_minimo"],
                "caudal_diario_maximo" => $info_costes_caudales_diarios["caudal_diario_maximo"]);
            $resultado = array_merge($resultado_sin_grafica_caudales_diarios, $info_grafica_caudales_diarios);

            // Se devuelve el resultado
            return ($resultado);
        }
    }


    // Devuelve la información de coste y caudal diario seleccionado de datos de fichero y una tarifa de gas
    function dame_coste_caudal_diario_seleccionado_fichero_tarifa_gas_Espanya($parametros, $ficheros)
    {
        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        // Caudal diario seleccionado
        $caudal_diario_seleccionado = $parametros["caudal_seleccionado"];

        // Fichero de caudales diarios máximos
        if (array_key_exists("fichero_caudales_maximos", $ficheros) == true)
        {
            $ruta_fichero_caudales_diarios_maximos = $ficheros["fichero_caudales_maximos"]["tmp_name"];
        }
        else
        {
            if (array_key_exists("ruta_fichero_caudales_maximos", $parametros) == true)
            {
                $ruta_fichero_caudales_diarios_maximos = $parametros["ruta_fichero_caudales_maximos"];
            }
            else
            {
                throw new Exception("No se ha podido recuperar la ruta del fichero de caudales diarios máximos");
            }
        }

        // Se lee el fichero de caudales diarios máximos
        $caudales_diarios_maximos_mensuales = array();
        $mensaje_error = NULL;
        $fichero_caudales_diarios_maximos_correcto = lee_fichero_caudales_diarios_maximos_mensuales_Espanya(
            $ruta_fichero_caudales_diarios_maximos,
            $caudales_diarios_maximos_mensuales,
            $mensaje_error);

        // Si no se ha leído el fichero correctamente se muestra un mensaje de error
        if ($fichero_caudales_diarios_maximos_correcto == false)
        {
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        else
        {
            // Llamada a la función externa
            $porcentaje_rango_caudales_diarios = dame_porcentaje_rango_caudales_diarios_optimizador_simulador_caudales_Espanya(RANGO_CAUDALES_DIARIOS_MAXIMO);
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_COSTE_CAUDAL_DIARIO_SELECCIONADO_CAUDALES_DIARIOS_MAXIMOS_MENSUALES_TARIFA_GAS_ESPANYA,
                    "caudales_diarios_maximos_mensuales" => $caudales_diarios_maximos_mensuales,
                    "id_tarifa_gas" => $id_tarifa,
                    "porcentaje_rango_caudales_diarios" => $porcentaje_rango_caudales_diarios,
                    "caudal_diario_seleccionado" => $caudal_diario_seleccionado
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
                // Información de costes y caudales diarios
                $info_costes_caudales_diarios = $resultado_funcion_externa;

                // Se crea la tabla de caudal diario seleccionado
                $tabla_caudal_diario_seleccionado = dame_tabla_caudal_diario_seleccionado_Espanya(
                    "tabla-caudal-diario-seleccionado-fichero-tarifa-gas",
                    $info_costes_caudales_diarios);

                // Se recupera el paso máximo de los datos de caudales diarios
                $paso_maximo_caudales_diarios = dame_paso_maximo_caudales_diarios_Espanya($info_costes_caudales_diarios);

                // Resultado
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => true,
                    "paso_maximo_caudales_diarios" => $paso_maximo_caudales_diarios,
                    "tabla_caudal_diario_seleccionado" => $tabla_caudal_diario_seleccionado->dame_tabla(),
                    "caudales_diarios_costes" => $info_costes_caudales_diarios["caudales_diarios_costes"],
                    "coste_caudal_diario_seleccionado" => $info_costes_caudales_diarios["coste_caudal_diario_seleccionado"],
                    "caudal_diario_seleccionado" => $info_costes_caudales_diarios["caudal_diario_seleccionado"],
                    "coste_caudal_diario_actual" => $info_costes_caudales_diarios["coste_caudal_diario_actual"],
                    "caudal_diario_actual" => $info_costes_caudales_diarios["caudal_diario_actual"],
                    "coste_minimo_caudal_diario" => $info_costes_caudales_diarios["coste_minimo_caudal_diario"],
                    "coste_maximo_caudal_diario" => $info_costes_caudales_diarios["coste_maximo_caudal_diario"],
                    "caudal_diario_minimo" => $info_costes_caudales_diarios["caudal_diario_minimo"],
                    "caudal_diario_maximo" => $info_costes_caudales_diarios["caudal_diario_maximo"]);
                return ($resultado);
            }
        }
    }


    //
    // Funciones auxiliares
    //


    // Devuelve la información de gráfica de caudales diarios
    function dame_info_grafica_caudales_diarios_gas_Espanya(
        $nombre_sensor,
        $filas_caudales_diarios_sensor,
        $minutos_desfase_utc)
    {
        // Milisegundos de desfase de zonas horarias
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables para la gráfica de caudales diarios
        $datos_grafica_caudales_diarios = new VectorDatos();
        $max_caudal_diario = -INF;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_DIA, NULL);

        // Se recorren los caudales diarios
        $timestamp_fecha_hora_caudal_diario_anterior = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_caudales_diarios_sensor as $fila)
        {
            $caudal_diario = (float) $fila["caudal"];
            if ($caudal_diario > $max_caudal_diario)
            {
                $max_caudal_diario = $caudal_diario;
            }

            // Hora
            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_local = $fila["hora"];
            $cadena_hora_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_local, FORMATO_FECHA_HORA_FUNCIONES, $zona_horaria, ZONA_HORARIA_UTC);

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_caudal_diario = dame_timestamp_cadena_fecha_milisegundos($cadena_hora_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_caudal_diario -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_caudal_diario_anterior !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_caudal_diario - $timestamp_fecha_hora_caudal_diario_anterior) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_caudales_diarios->anyade_tupla_pareja_datos($timestamp_fecha_hora_caudal_diario_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_caudal_diario_anterior = $timestamp_fecha_hora_caudal_diario;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el caudal diario
            $datos_grafica_caudales_diarios->anyade_tupla_pareja_datos($timestamp_fecha_hora_caudal_diario, $caudal_diario);
        }

        // Variables para dibujar las gráficas
        $etiquetas_grafica_caudales_diarios = new VectorDatos();
        $etiquetas_grafica_caudales_diarios->anyade_etiqueta($nombre_sensor);

        $grafica_caudales_diarios = new VectorDatos();
        $grafica_caudales_diarios->anyade_dato($datos_grafica_caudales_diarios->dame_datos());

        // Resultado
        $info_grafica_caudales_diarios = array(
            "max_caudal_diario" => $max_caudal_diario,
            "etiquetas_grafica_caudales_diarios" => $etiquetas_grafica_caudales_diarios->dame_datos(),
            "grafica_caudales_diarios" => $grafica_caudales_diarios->dame_datos());
        return ($info_grafica_caudales_diarios);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_optimizador_caudales_automatico_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-optimizador-caudales-automatico'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-optimizador-caudales-automatico' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-caudal-diario-optimo-optimizador-caudales-automatico'></div>
                        <div class='grafica100' id='grafica-caudales-diarios-optimizador-caudales-automatico'></div>
                        <div class='grafica100' id='grafica-costes-caudales-diarios-optimizador-caudales-automatico'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de optimización de caudales automático
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-optimizador-caudales-automatico'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_caudales(TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-optimizador-caudales-automatico'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-caudal-diario-optimo-optimizador-caudales-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-caudales-diarios-optimizador-caudales-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-caudales-diarios-optimizador-caudales-automatico'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_optimizador_caudales_manual_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-optimizador-caudales-manual'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-optimizador-caudales-manual' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-caudal-diario-optimo-optimizador-caudales-manual'></div>
                        <div class='grafica100' id='grafica-costes-caudales-diarios-optimizador-caudales-manual'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de optimización de caudales manual
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-optimizador-caudales-manual'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_caudales(TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-optimizador-caudales-manual'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-caudal-diario-optimo-optimizador-caudales-manual'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-caudales-diarios-optimizador-caudales-manual'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_caudales_automatico_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-caudales-automatico'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-caudales-automatico' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-automatico'></div>
                        <div class='grafica100' id='grafica-caudales-diarios-simulador-caudales-automatico'></div>
                        <div class='grafica100' id='grafica-costes-caudales-diarios-simulador-caudales-automatico'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de caudales automático
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-caudales-automatico'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_caudales(TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-caudales-automatico'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-caudales-diarios-simulador-caudales-automatico'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-caudales-diarios-simulador-caudales-automatico'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_caudales_manual_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-caudales-manual'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-caudales-manual' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-manual'></div>
                        <div class='grafica100' id='grafica-costes-caudales-diarios-simulador-caudales-manual'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de caudales manual
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-caudales-manual'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_caudales(TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-caudales-manual'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-caudal-diario-seleccionado-simulador-caudales-manual'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-costes-caudales-diarios-simulador-caudales-manual'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
