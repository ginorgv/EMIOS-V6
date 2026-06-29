<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/util_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/InformesFichero/util_energia_reactiva_informes_fichero.php');


    //
    // Funciones de información de energía reactiva (Espanya)
    //


    // Devuelve la información de simulación de batería de condensadores de un sensor
    function dame_simulacion_bateria_condensadores_sensor_Espanya($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $cadena_fecha_hora_inicio_local_local = $parametros['fecha_hora_inicio'];
        $cadena_fecha_hora_fin_local_local = $parametros['fecha_hora_fin'];
        $diferencia_capacidad = (int) $parametros["diferencia_capacidad"];
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

        // Se obtiene el sensor de energía activa asociado (si no hay se devuelve error)
        $info_sensor_energia_activa = dame_info_sensor_energia_activa_asociado_sensor_energia_reactiva($id_sensor, NULL);
        if ($info_sensor_energia_activa === NULL)
        {
            $mensaje_error = $idiomas->_("No hay sensor de energía activa asociado");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }
        $id_sensor_energia_activa = $info_sensor_energia_activa['id'];

        // Tramos penalizables de energía reactiva
        $id_tarifa_sensor = dame_id_tarifa_id_sensor($id_sensor_energia_activa);
        if ($id_tarifa_sensor != ID_NINGUNO)
        {
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_sensor);
            $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
            $tramos_penalizables_energia_reactiva = $caracteristicas_tipo_tarifa_electrica["tramos_penalizables_energia_reactiva"];
        }

        // Variables
        $max_consumo = -INF;
        $max_coseno_phi = -INF;
        $min_coseno_phi = INF;
        $ahorro_total = 0.0;
        $datos_consumo_energia_activa = new VectorDatos();
        $datos_consumo_energia_reactiva = new VectorDatos();
        $datos_consumo_energia_reactiva_simulado = new VectorDatos();
        $datos_coseno_phi = new VectorDatos();
        $datos_coseno_phi_simulado = new VectorDatos();
        $datos_penalizable = new VectorDatos();
        $datos_coste_energia_reactiva_tramo = array();
        $grafica_consumos_energia = new VectorDatos();
        $grafica_coseno_phi = new VectorDatos();
        $grafica_penalizable = new VectorDatos();
        $etiquetas_grafica_consumos_energia = new VectorDatos();
        $etiquetas_grafica_coseno_phi = new VectorDatos();

        // Consulta de valores del sensor de energía activa
        $consulta_valores_sensor_energia_activa = dame_consulta_valores_sensor(
            $id_sensor_energia_activa,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_HORA,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            NULL);
        $res_valores_sensor_energia_activa = $bd_datos->ejecuta_consulta($consulta_valores_sensor_energia_activa);
        if ($res_valores_sensor_energia_activa == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor_energia_activa."'");
        }

        // Datos de energía activa por horas
        $datos_energia_activa_horas = array();
        while ($fila_valores_sensor_energia_activa = $res_valores_sensor_energia_activa->dame_siguiente_fila())
        {
            // Sólo se procesa la fila si se han calculado valores de clase
            if ($fila_valores_sensor_energia_activa[CAMPO_TRAMO] === NULL)
            {
                continue;
            }

            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor_energia_activa['fecha_hora'];
            $tramo = $fila_valores_sensor_energia_activa[CAMPO_TRAMO];
            $consumo = (float) $fila_valores_sensor_energia_activa[CAMPO_INCREMENTO];

            $timestamp_fecha_hora_hora_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_hora_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            array_push($datos_energia_activa_horas,
                array(
                    "timestamp_fecha_hora_utc" => $timestamp_fecha_hora_hora_utc,
                    "tramo" => $tramo,
                    "consumo" => $consumo)
            );
        }

        // Si no hay datos no se hace nada
        if (count($datos_energia_activa_horas) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se guardan los datos de energía activa y reactiva (por horas y por tramo) (en las horas en las que hay datos de los dos sensores)
        $tramos = array();
        $consumo_energia_activa_tramos = array();
        $consumo_energia_reactiva_tramos = array();
        $consumo_energia_reactiva_simulado_tramos = array();

        // Consulta de valores del sensor de energía reactiva
        $consulta_valores_sensor_energia_reactiva = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            INTERVALO_VALORES_HORA,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            NULL);
        $res_valores_sensor_energia_reactiva = $bd_datos->ejecuta_consulta($consulta_valores_sensor_energia_reactiva);
        if ($res_valores_sensor_energia_reactiva == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor_energia_reactiva."'");
        }

        // Si no hay datos no se hace nada
        if ($res_valores_sensor_energia_reactiva->dame_numero_filas() == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Número de datos de energía activa y reactiva en las mismas horas
        $numero_datos_energia_activa_reactiva_mismas_horas = 0;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica(INTERVALO_VALORES_HORA, NULL);

        // Se recorren las filas de valores de energía reactiva
        $numero_dato_energia_activa_horas = 0;
        $timestamp_fecha_hora_energia_reactiva_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valores_sensor_energia_reactiva = $res_valores_sensor_energia_reactiva->dame_siguiente_fila())
        {
            // Sólo se procesa la fila si se han calculado valores de clase
            if ($fila_valores_sensor_energia_reactiva["tramo"] === NULL)
            {
                continue;
            }

            // Fecha y valores
            $cadena_fecha_hora_energia_reactiva_base_datos_utc = $fila_valores_sensor_energia_reactiva['fecha_hora'];
            $consumo_energia_reactiva = (float) $fila_valores_sensor_energia_reactiva[CAMPO_INCREMENTO];
            $tramo_energia_reactiva = $fila_valores_sensor_energia_reactiva[CAMPO_TRAMO];
            $coseno_phi = (float) $fila_valores_sensor_energia_reactiva[CAMPO_COSENO_PHI];
            $penalizable = $fila_valores_sensor_energia_reactiva[CAMPO_PENALIZABLE];

            // Timestamps
            $timestamp_fecha_hora_energia_reactiva_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_energia_reactiva_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_energia_reactiva_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;

            // Si la primera hora de energía activa es mayor que la fecha de energía reactiva, se pasa a la siguiente fila de energía reactiva
            if ($numero_dato_energia_activa_horas == 0)
            {
                $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
                if ($timestamp_fecha_hora_energia_activa_utc > $timestamp_fecha_hora_energia_reactiva_utc)
                {
                    continue;
                }
            }

            // Si la fecha de energía activa es menor, se incrementa el número de fila hasta que sea igual (si es mayor es que hay huecos)
            $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
            while ($timestamp_fecha_hora_energia_activa_utc < $timestamp_fecha_hora_energia_reactiva_utc)
            {
                $numero_dato_energia_activa_horas += 1;
                if (count($datos_energia_activa_horas) < ($numero_dato_energia_activa_horas + 1))
                {
                    break;
                }
                $timestamp_fecha_hora_energia_activa_utc = $datos_energia_activa_horas[$numero_dato_energia_activa_horas]["timestamp_fecha_hora_utc"];
            }

            // Si la fecha no es igual es que hay huecos (los datos son incompletos), se pasa a la siguiente fila de energía reactiva
            if ($timestamp_fecha_hora_energia_activa_utc != $timestamp_fecha_hora_energia_reactiva_utc)
            {
                continue;
            }

            // Hay datos de los dos sensores y la hora es la misma (se puede continuar)
            $numero_datos_energia_activa_reactiva_mismas_horas += 1;

            // Datos de energía activa
            $datos_energia_activa_hora = $datos_energia_activa_horas[$numero_dato_energia_activa_horas];
            $consumo_energia_activa = $datos_energia_activa_hora["consumo"];
            $tramo_energia_activa = $datos_energia_activa_hora["tramo"];

            // Si el tramo es diferente se devuelve error
            if ($tramo_energia_activa != $tramo_energia_reactiva)
            {
                $mensaje_error = $idiomas->_("Los tramos de energía activa y reactiva no coinciden").".\n".
                    $idiomas->_("Es posible que haya que recalcular los datos de la tarifa en la sección 'Tarifas'").".\n".
                    $idiomas->_("Si ya lo ha hecho, espere unos minutos y podrá ver el informe correctamente");
                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $mensaje_error);
                return ($resultado);
            }
            else
            {
                $tramo = $tramo_energia_activa;
            }

            // Consumo de energía reactiva con incremento de batería de condensadores
            $consumo_energia_reactiva_simulado = $consumo_energia_reactiva - $diferencia_capacidad;
            if ($consumo_energia_reactiva_simulado < 0)
            {
                $consumo_energia_reactiva_simulado = 0;
            }

            // Consumo máximo
            if ($consumo_energia_activa > $max_consumo)
            {
                $max_consumo = $consumo_energia_activa;
            }
            if ($consumo_energia_reactiva > $max_consumo)
            {
                $max_consumo = $consumo_energia_reactiva;
            }
            if ($consumo_energia_reactiva_simulado > $max_consumo)
            {
                $max_consumo = $consumo_energia_reactiva_simulado;
            }

            // Coseno de phi simulado
            if (($consumo_energia_activa == 0) && ($consumo_energia_reactiva_simulado == 0))
            {
                $coseno_phi_simulado = 1;
            }
            else
            {
                $coseno_phi_simulado = $consumo_energia_activa / sqrt(pow($consumo_energia_activa, 2) + pow($consumo_energia_reactiva_simulado, 2));
            }

            // Se guardan los consumos de energía activa y reactiva por tramo (en todos los tramos aunque no sean penalizables)

            // Tramo
            if (in_array($tramo, $tramos) == false)
            {
                array_push($tramos, $tramo);
            }

            // Energía activa
            if (array_key_exists($tramo, $consumo_energia_activa_tramos) == false)
            {
                $consumo_energia_activa_tramos[$tramo] = $consumo_energia_activa;
            }
            else
            {
                $consumo_energia_activa_tramos[$tramo] += $consumo_energia_activa;
            }

            // Energía reactiva
            if (array_key_exists($tramo, $consumo_energia_reactiva_tramos) == false)
            {
                $consumo_energia_reactiva_tramos[$tramo] = $consumo_energia_reactiva;
            }
            else
            {
                $consumo_energia_reactiva_tramos[$tramo] += $consumo_energia_reactiva;
            }

            // Energía reactiva simulada
            if (array_key_exists($tramo, $consumo_energia_reactiva_simulado_tramos) == false)
            {
                $consumo_energia_reactiva_simulado_tramos[$tramo] = $consumo_energia_reactiva_simulado;
            }
            else
            {
                $consumo_energia_reactiva_simulado_tramos[$tramo] += $consumo_energia_reactiva_simulado;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_energia_reactiva_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_energia_reactiva_utc - $timestamp_fecha_hora_energia_reactiva_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_consumo_energia_activa->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_consumo_energia_reactiva->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_consumo_energia_reactiva_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_coseno_phi->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_coseno_phi_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                    $datos_penalizable->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_energia_reactiva_anterior_utc = $timestamp_fecha_hora_energia_reactiva_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Datos para la gráfica de consumo
            $datos_consumo_energia_activa->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $consumo_energia_activa);
            $datos_consumo_energia_reactiva->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $consumo_energia_reactiva);
            $datos_consumo_energia_reactiva_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $consumo_energia_reactiva_simulado);

            // Datos para la gráfica del coseno de phi y penalizable
            $datos_coseno_phi->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $coseno_phi);
            $datos_coseno_phi_simulado->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $coseno_phi_simulado);
            if ($coseno_phi_simulado > $max_coseno_phi)
            {
                $max_coseno_phi = $coseno_phi_simulado;
            }
            if ($coseno_phi> $max_coseno_phi)
            {
                $max_coseno_phi = $coseno_phi;
            }
            if ($coseno_phi < $min_coseno_phi)
            {
                $min_coseno_phi = $coseno_phi;
            }
            if ($coseno_phi_simulado < $min_coseno_phi)
            {
                $min_coseno_phi = $coseno_phi_simulado;
            }
            if ($penalizable !== NULL)
            {
                $datos_penalizable->anyade_tupla_pareja_datos($timestamp_fecha_hora_energia_reactiva_utc, $penalizable);
            }

            // Se incrementa el número de dato de energía activa
            $numero_dato_energia_activa_horas += 1;
            if (count($datos_energia_activa_horas) < ($numero_dato_energia_activa_horas + 1))
            {
                break;
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_datos_energia_activa_reactiva_mismas_horas == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Tabla de energía reactiva por tramo (actual y simulado)
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_TRAMOS_SIMULADOR_BATERIA_CONDENSADORES,
            "generar_valores_xml" => true
        );
        $cabecera_tabla = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Coseno de phi actual"),
            $idiomas->_("Coseno de phi simulado"),
            $idiomas->_("Exceso actual"),
            $idiomas->_("Exceso simulado"),
            $idiomas->_("Coste actual"),
            $idiomas->_("Coste simulado"),
            $idiomas->_("Diferencia de coste")
        );
        $tabla_energia_reactiva_tramos = new TablaDatos(
            "tabla-coste-energia-reactiva-simulador-bateria-condensadores",
            $idiomas->_("Energía reactiva por tramo"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla_energia_reactiva_tramos->anyade_cabecera("", $cabecera_tabla);

        // Se rellena la tabla de energía reactiva (por tramo):
        // - Coseno phi actual
        // - Coseno phi simulado
        // - Exceso de energía reactiva actual
        // - Exceso de energía reactiva simulado
        // - Coste actual
        // - Coste simulado
        // - Diferencia de coste
        sort($tramos);
        foreach ($tramos as $tramo)
        {
            $consumo_energia_activa = $consumo_energia_activa_tramos[$tramo];
            $consumo_energia_reactiva = $consumo_energia_reactiva_tramos[$tramo];
            $consumo_energia_reactiva_simulado = $consumo_energia_reactiva_simulado_tramos[$tramo];

            if (($consumo_energia_activa == 0) && ($consumo_energia_reactiva == 0))
            {
                $coseno_phi = 1;
                $coseno_phi_simulado = 1;
            }
            else
            {
                $coseno_phi = $consumo_energia_activa / sqrt(pow($consumo_energia_activa, 2) + pow($consumo_energia_reactiva, 2));
                $coseno_phi_simulado = $consumo_energia_activa / sqrt(pow($consumo_energia_activa, 2) + pow($consumo_energia_reactiva_simulado, 2));
            }

            // Calculo exceso energía reactiva y coste
            if ($coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_1)
            {
                // Nota: Para el coseno de phi de 0.95 la fórmula sería '* 0.328' pero en la factura es '* 0.33'
                // (si el exceso está entre 0.328 y 0.33 puede ser negativo y se pone a 0)
                $exceso_energia_reactiva = $consumo_energia_reactiva - ($consumo_energia_activa * 0.33);
                if ($exceso_energia_reactiva < 0)
                {
                    $exceso_energia_reactiva = 0;
                }
                if ($coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_2)
                {
                    $coste_exceso_energia_reactiva = $exceso_energia_reactiva * PRECIO_EXCESO_ENERGIA_REACTIVA_2;
                }
                else
                {
                    $coste_exceso_energia_reactiva = $exceso_energia_reactiva * PRECIO_EXCESO_ENERGIA_REACTIVA_1;
                }
            }
            else
            {
                $exceso_energia_reactiva = 0;
                $coste_exceso_energia_reactiva = 0;
            }

            // Cálculo de exceso de energía reactiva y coste (simulado)
            if ($coseno_phi_simulado < MINIMO_COSENO_PHI_PENALIZABLE_1)
            {
                $exceso_energia_reactiva_simulado = $consumo_energia_reactiva_simulado - ($consumo_energia_activa * 0.33);
                if ($exceso_energia_reactiva_simulado < 0)
                {
                    $exceso_energia_reactiva_simulado = 0;
                }
                if ($coseno_phi < MINIMO_COSENO_PHI_PENALIZABLE_2)
                {
                    $coste_exceso_energia_reactiva_simulado = $exceso_energia_reactiva_simulado * PRECIO_EXCESO_ENERGIA_REACTIVA_2;
                }
                else
                {
                    $coste_exceso_energia_reactiva_simulado = $exceso_energia_reactiva_simulado * PRECIO_EXCESO_ENERGIA_REACTIVA_1;
                }
            }
            else
            {
                $exceso_energia_reactiva_simulado = 0;
                $coste_exceso_energia_reactiva_simulado = 0;
            }

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Cálculo ahorro y porcentaje de diferencia de coste
            $diferencia_costes_tramo = $coste_exceso_energia_reactiva_simulado - $coste_exceso_energia_reactiva;
            $cadena_diferencia_costes_tramo = formatea_numero($diferencia_costes_tramo, 2, false);

            if (($coste_exceso_energia_reactiva == 0) && ($coste_exceso_energia_reactiva_simulado != 0))
            {
               $cadena_porcentaje_diferencia_costes_tramo = "ND";
               $signo_porcentaje = "";
            }
            else
            {
                $porcentaje_diferencia_costes_tramo = dame_porcentaje_valor_referencia($coste_exceso_energia_reactiva_simulado, $coste_exceso_energia_reactiva);
                $cadena_porcentaje_diferencia_costes_tramo = formatea_numero($porcentaje_diferencia_costes_tramo, 2);
                if ($coste_exceso_energia_reactiva == $coste_exceso_energia_reactiva_simulado)
                {
                    $imagen_porcentaje = "<i class='icon-sort color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("igual"), ENT_QUOTES)."}"."</texto></i>";
                    $signo_porcentaje = "";
                }
                else
                {
                    if ($coste_exceso_energia_reactiva < $coste_exceso_energia_reactiva_simulado)
                    {
                        $imagen_porcentaje = "<i class='icon-caret-up color-rojo'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("superior"), ENT_QUOTES)."}"."</texto></i>";
                        $signo_porcentaje = "+";
                    }
                    else
                    {
                        $imagen_porcentaje = "<i class='icon-caret-down color-verde'>".
                            "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("inferior"), ENT_QUOTES)."}"."</texto></i>";
                        $signo_porcentaje = "-";
                    }
                }
            }

            $cadena_tramo = $tramo;
            $tramo_penalizable = true;
            if ($id_tarifa_sensor != ID_NINGUNO)
            {
                if (in_array($tramo, $tramos_penalizables_energia_reactiva) == false or $tramo == 6)
                {
                    $cadena_tramo .= " (".$idiomas->_("no penalizable").")";
                    $tramo_penalizable = false;
                }
            }
            $datos_coste_energia_reactiva_tramo[0] = "P".$cadena_tramo;
            $datos_coste_energia_reactiva_tramo[1] = formatea_numero($coseno_phi, 3);
            $datos_coste_energia_reactiva_tramo[2] = formatea_numero($coseno_phi_simulado, 3);
            $datos_coste_energia_reactiva_tramo[3] = formatea_numero($exceso_energia_reactiva, 2)." ".$idiomas->_("kVArh");
            $datos_coste_energia_reactiva_tramo[4] = formatea_numero($exceso_energia_reactiva_simulado, 2)." ".$idiomas->_("kVArh");
            if ($tramo_penalizable == true)
            {
                $datos_coste_energia_reactiva_tramo[5] = formatea_numero($coste_exceso_energia_reactiva, 2, false)." ".$unidad_medida_coste;
                $datos_coste_energia_reactiva_tramo[6] = formatea_numero($coste_exceso_energia_reactiva_simulado, 2, false)." ".$unidad_medida_coste;
                $datos_coste_energia_reactiva_tramo[7] = $imagen_porcentaje." ".$cadena_diferencia_costes_tramo." ".$unidad_medida_coste.
                    " (".$signo_porcentaje.$cadena_porcentaje_diferencia_costes_tramo." "."%".")";
            }
            else
            {
                $datos_coste_energia_reactiva_tramo[5] = $idiomas->_("NA");
                $datos_coste_energia_reactiva_tramo[6] = $idiomas->_("NA");
                $datos_coste_energia_reactiva_tramo[7] = $idiomas->_("NA");
            }
            $tabla_energia_reactiva_tramos->anyade_fila("", $datos_coste_energia_reactiva_tramo);

            // Coste y ahorro totales
            if ($tramo_penalizable == true)
            {
                $coste_exceso_energia_reactiva_total = $coste_exceso_energia_reactiva_total + $coste_exceso_energia_reactiva;
                $coste_exceso_energia_reactiva_total_simulado = $coste_exceso_energia_reactiva_total_simulado + $coste_exceso_energia_reactiva_simulado;
                $ahorro_total -= $diferencia_costes_tramo;
            }
        }

        $cadena_ahorro_total = formatea_numero($ahorro_total, 2, false);
        $porcentaje_ahorro_total = dame_porcentaje_valor_referencia($coste_exceso_energia_reactiva_total_simulado, $coste_exceso_energia_reactiva_total);
        $cadena_porcentaje_ahorro_total = formatea_numero($porcentaje_ahorro_total, 2);

        if (($coste_exceso_energia_reactiva_total == 0) && ($coste_exceso_energia_reactiva_total_simulado != 0))
        {
            $porcentaje_ahorro_total = "ND";
            $signo_porcentaje = "";
        }
        else
        {
            if ($coste_exceso_energia_reactiva_total > $coste_exceso_energia_reactiva_total_simulado)
            {
               $signo_porcentaje = "+";
            }
            else
            {
                if ($coste_exceso_energia_reactiva_total < $coste_exceso_energia_reactiva_total_simulado)
                {
                   $signo_porcentaje = "-";
                }
                else
                {
                   $signo_porcentaje = "";
                }
            }
        }
        $coste_exceso_energia_reactiva_total = formatea_numero($coste_exceso_energia_reactiva_total, 2, false);
        $coste_exceso_energia_reactiva_total_simulado = formatea_numero($coste_exceso_energia_reactiva_total_simulado, 2, false);

        $pie_tabla = $idiomas->_("Ahorro total").": ".$cadena_ahorro_total." ".$unidad_medida_coste." (".$signo_porcentaje.$cadena_porcentaje_ahorro_total." "."%".")";
        $pie_tabla .= " (".$idiomas->_("coste actual total").": ".$coste_exceso_energia_reactiva_total." ".$unidad_medida_coste.", ".
            $idiomas->_("coste simulado total").": ".$coste_exceso_energia_reactiva_total_simulado." ".$unidad_medida_coste.")";
        $tabla_energia_reactiva_tramos->anyade_pie($pie_tabla);

        // Datos de gráficas
        $grafica_consumos_energia->anyade_dato($datos_consumo_energia_activa->dame_datos());
        $grafica_consumos_energia->anyade_dato($datos_consumo_energia_reactiva->dame_datos());
        $grafica_consumos_energia->anyade_dato($datos_consumo_energia_reactiva_simulado->dame_datos());
        $grafica_coseno_phi->anyade_dato($datos_coseno_phi->dame_datos());
        $grafica_coseno_phi->anyade_dato($datos_coseno_phi_simulado->dame_datos());
        $grafica_penalizable->anyade_dato($datos_penalizable->dame_datos());

        // Etiquetas de las gráficas
        $etiquetas_grafica_consumos_energia->anyade_etiqueta($idiomas->_("Energía activa"));
        $etiquetas_grafica_consumos_energia->anyade_etiqueta($idiomas->_("Energía reactiva")." (".$idiomas->_("actual").") (".$idiomas->_("kVArh").")");
        $etiquetas_grafica_consumos_energia->anyade_etiqueta($idiomas->_("Energía reactiva")." (".$idiomas->_("simulado").") (".$idiomas->_("kVArh").")");
        $etiquetas_grafica_coseno_phi->anyade_etiqueta($idiomas->_("Actual"));
        $etiquetas_grafica_coseno_phi->anyade_etiqueta($idiomas->_("Simulado"));

        // Los valores 'INF' y '-INF' no se pueden convertir a cadena, se cambian por NA (ocurre cuando no hay datos)
        if ($max_consumo == -INF)
        {
            $max_consumo = "ND";
        }
        if ($max_coseno_phi == -INF)
        {
            $max_coseno_phi = "ND";
        }
        if ($min_coseno_phi == INF)
        {
            $min_coseno_phi = "ND";
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tabla_energia_reactiva_tramos" => $tabla_energia_reactiva_tramos->dame_tabla(),
            "grafica_consumos_energia" => $grafica_consumos_energia->dame_datos(),
            "grafica_coseno_phi" => $grafica_coseno_phi->dame_datos(),
            "grafica_penalizable" => $grafica_penalizable->dame_datos(),
            "etiquetas_consumos_energia" => $etiquetas_grafica_consumos_energia->dame_datos(),
            "etiquetas_coseno_phi" => $etiquetas_grafica_coseno_phi->dame_datos(),
            "max_consumo" => $max_consumo,
            "max_coseno_phi" => $max_coseno_phi,
            "min_coseno_phi" => $min_coseno_phi);
        return ($resultado);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_simulador_bateria_condensadores_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-bateria-condensadores'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-bateria-condensadores' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-coste-energia-reactiva-simulador-bateria-condensadores'></div>
                        <div class='grafica100' id='grafica-consumos-energia-simulador-bateria-condensadores'></div>
                        <div class='grafica100' id='grafica-cosenos-phi-simulador-bateria-condensadores'></div>
                        <div class='grafica100' id='grafica-penalizable-simulador-bateria-condensadores'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página simulador de batería de condensadores
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-bateria-condensadores'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_energia_reactiva(TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-bateria-condensadores'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-tramos-simulador-bateria-condensadores'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-energia-simulador-bateria-condensadores'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-coseno-phi-simulador-bateria-condensadores'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-penalizable-simulador-bateria-condensadores'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
