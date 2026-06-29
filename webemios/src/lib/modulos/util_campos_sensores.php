<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


	//
    // Funciones de campos de sensores
    //


    // Devuelve los campos puntuales de una clase de sensor
    function dame_campos_puntuales_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $campos_puntuales = $caracteristicas_clase_sensor["campos_puntuales"];
        return ($campos_puntuales);
    }


    // Devuelve los campos puntuales calculados de una clase de sensor
    // (Nota: No existen en la base de datos, se calculan a partir de los valores de otros campos)
    function dame_campos_puntuales_calculados_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $campos_puntuales = $caracteristicas_clase_sensor["campos_puntuales_calculados"];
        return ($campos_puntuales);
    }


    // Devuelve los campos de incrementos de una clase de sensor
    function dame_campos_incrementos_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $campos_incrementos = $caracteristicas_clase_sensor["campos_incrementos"];
        return ($campos_incrementos);
    }


    // Devuelve los campos incrementos calculados de una clase de sensor
    // (Nota: No existen en la base de datos, se calculan a partir de los valores de otros campos)
    function dame_campos_incrementos_calculados_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $campos_incrementos = $caracteristicas_clase_sensor["campos_incrementos_calculados"];
        return ($campos_incrementos);
    }


    // Devuelve los campos (puntuales e incrementos) de una clase de sensor
    function dame_campos_clase_sensor($clase_sensor)
    {
        $campos_puntuales = dame_campos_puntuales_clase_sensor($clase_sensor);
        $campos_incrementos = dame_campos_incrementos_clase_sensor($clase_sensor);
        $numero_campos_puntuales = count($campos_puntuales);
        $numero_campos_incrementos = count($campos_incrementos);
        if ($numero_campos_puntuales == 0)
        {
            $campos = $campos_incrementos;
        }
        else
        {
            if ($numero_campos_incrementos == 0)
            {
                $campos = $campos_puntuales;
            }
            else
            {
                $campos = array();
                if ($numero_campos_puntuales == $numero_campos_incrementos)
                {
                    for ($i = 0; $i < $numero_campos_puntuales; $i++)
                    {
                        array_push($campos, $campos_puntuales[$i]);
                        array_push($campos, $campos_incrementos[$i]);
                    }
                }
                else
                {
                    for ($i = 0; $i < $numero_campos_puntuales; $i++)
                    {
                        array_push($campos, $campos_puntuales[$i]);
                    }
                    for ($i = 0; $i < $numero_campos_incrementos; $i++)
                    {
                        array_push($campos, $campos_incrementos[$i]);
                    }
                }
            }
        }
        return ($campos);
    }


    // Devuelve los campos puntuales de clase de una clase de sensor
    function dame_campos_puntuales_clase_clase_sensor($clase_sensor)
    {
        $campos_puntuales_clase = array();
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_puntuales_clase, CAMPO_TRAMO);
                        array_push($campos_puntuales_clase, CAMPO_SOBREPOTENCIA);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_puntuales_clase, CAMPO_TRAMO);
                        array_push($campos_puntuales_clase, CAMPO_SOBREPOTENCIA);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_puntuales_clase, CAMPO_TRAMO);
                        array_push($campos_puntuales_clase, CAMPO_COSENO_PHI);
                        array_push($campos_puntuales_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_puntuales_clase, CAMPO_TRAMO);
                        array_push($campos_puntuales_clase, CAMPO_COSENO_PHI);
                        array_push($campos_puntuales_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_puntuales_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_puntuales_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
        }
        return ($campos_puntuales_clase);
    }


    // Devuelve los campos incrementos de clase de una clase de sensor
    function dame_campos_incrementos_clase_clase_sensor($clase_sensor)
    {
        $campos_incrementos_clase = array();
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO_ESTIMADO);
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO_REAL);
                        array_push($campos_incrementos_clase, CAMPO_DESVIO_CONSUMO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE_DESVIO);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO_ESTIMADO);
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO_REAL);
                        array_push($campos_incrementos_clase, CAMPO_DESVIO_CONSUMO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE_DESVIO);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_incrementos_clase, CAMPO_INCREMENTO);
                        array_push($campos_incrementos_clase, CAMPO_CONSUMO);
                        array_push($campos_incrementos_clase, CAMPO_COSTE);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
        }
        return ($campos_incrementos_clase);
    }


    // Devuelve los campos de clase de sensor
    function dame_campos_clase_clase_sensor($clase_sensor)
    {
        $campos_clase = array();
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_TRAMO);
                        array_push($campos_clase, CAMPO_COSTE);
                        array_push($campos_clase, CAMPO_SOBREPOTENCIA);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_TRAMO);
                        array_push($campos_clase, CAMPO_COSTE);
                        array_push($campos_clase, CAMPO_SOBREPOTENCIA);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_TRAMO);
                        array_push($campos_clase, CAMPO_COSENO_PHI);
                        array_push($campos_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_TRAMO);
                        array_push($campos_clase, CAMPO_COSENO_PHI);
                        array_push($campos_clase, CAMPO_PENALIZABLE);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_clase, CAMPO_CONSUMO_ESTIMADO);
                        array_push($campos_clase, CAMPO_CONSUMO_REAL);
                        array_push($campos_clase, CAMPO_DESVIO_CONSUMO);
                        array_push($campos_clase, CAMPO_COSTE_DESVIO);
                        array_push($campos_clase, CAMPO_PENALIZABLE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_clase, CAMPO_CONSUMO_ESTIMADO);
                        array_push($campos_clase, CAMPO_CONSUMO_REAL);
                        array_push($campos_clase, CAMPO_DESVIO_CONSUMO);
                        array_push($campos_clase, CAMPO_COSTE_DESVIO);
                        array_push($campos_clase, CAMPO_PENALIZABLE);
                        break;
                    }
                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    // España
                    case PAIS_ESPANYA:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_CONSUMO);
                        array_push($campos_clase, CAMPO_COSTE);
                        break;
                    }

                    // Portugal
                    case PAIS_PORTUGAL:
                    {
                        array_push($campos_clase, CAMPO_INCREMENTO);
                        array_push($campos_clase, CAMPO_CONSUMO);
                        array_push($campos_clase, CAMPO_COSTE);
                        break;
                    }

                    case PAIS_NINGUNO:
                    {
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
        }
        return ($campos_clase);
    }


    // Devuelve todos los campos puntuales de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    function dame_todos_campos_puntuales_clase_sensor($clase_sensor)
    {
        $campos = dame_campos_puntuales_clase_sensor($clase_sensor);
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_clase = dame_campos_puntuales_clase_clase_sensor($clase_sensor);
            $campos_con_duplicados = array_merge($campos, $campos_clase);
            $campos = array_unique($campos_con_duplicados);
        }
        return ($campos);
    }


    // Devuelve todos los campos incrementos de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    function dame_todos_campos_incrementos_clase_sensor($clase_sensor)
    {
        $campos = dame_campos_incrementos_clase_sensor($clase_sensor);
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_clase = dame_campos_incrementos_clase_clase_sensor($clase_sensor);
            $campos_con_duplicados = array_merge($campos, $campos_clase);
            $campos = array_unique($campos_con_duplicados);
        }
        return ($campos);
    }


    // Devuelve todos los campos (puntuales e incrementos) de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    function dame_todos_campos_clase_sensor($clase_sensor)
    {
        $campos = dame_campos_clase_sensor($clase_sensor);

        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_clase = dame_campos_clase_clase_sensor($clase_sensor);
            $campos_con_duplicados = array_merge($campos, $campos_clase);
            $campos = array_unique($campos_con_duplicados);
        }
        return ($campos);
    }


    //
    // Funciones de agrupaciones de valores y parámetros extra
    //


    // Devuelve todos los campos (puntuales e incrementos) de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    // con tipo de agrupacion de valores
    function dame_todos_campos_clase_sensor_tipo_agrupacion_valores($clase_sensor, $intervalo_valores)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        $campos = array(
                            CAMPO_VALOR_MEDIA,
                            CAMPO_VALOR_SUMA,
                            CAMPO_INCREMENTO_SUMA,
                            CAMPO_INCREMENTO_MEDIA);
                        break;
                    }
                    default:
                    {
                        $campos = dame_todos_campos_clase_sensor($clase_sensor);
                        break;
                    }
                }
                break;
            }
            default:
            {
                $campos = dame_todos_campos_clase_sensor($clase_sensor);
                break;
            }
        }
        return ($campos);
    }


    // Devuelve todos los campos incrementos de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    // (incluyendo los campos con parámetros extra - campos calculados)
    function dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor)
    {
        $campos_incrementos = dame_todos_campos_incrementos_clase_sensor($clase_sensor);
        $campos_incrementos_calculados = dame_campos_incrementos_calculados_clase_sensor($clase_sensor);
        $campos = array_merge($campos_incrementos, $campos_incrementos_calculados);
        return ($campos);
    }


    // Devuelve todos los campos (puntuales e incrementos) de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor)
    // (incluyendo los campos con parámetros extra)
    function dame_todos_campos_clase_sensor_parametros_extra($clase_sensor)
    {
        $todos_campos_clase_sensor = dame_todos_campos_clase_sensor($clase_sensor);
        $campos_puntuales_calculados = dame_campos_puntuales_calculados_clase_sensor($clase_sensor);
        $campos_incrementos_calculados = dame_campos_incrementos_calculados_clase_sensor($clase_sensor);
        $campos = array_merge($todos_campos_clase_sensor, $campos_puntuales_calculados, $campos_incrementos_calculados);
        return ($campos);
    }


    // Devuelve todos los campos incrementos de una clase de sensor con tipo de agrupacion de valores (campos de la clase de sensor y campos de clase de la clase de sensor)
    // (incluyendo los campos con parámetros extra)
    function dame_todos_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        $campos = array(
                            CAMPO_INCREMENTO_SUMA,
                            CAMPO_INCREMENTO_MEDIA);
                        break;
                    }
                    default:
                    {
                        $campos = dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor);
                        break;
                    }
                }
                break;
            }
            default:
            {
                $campos = dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor);
                break;
            }
        }
        return ($campos);
    }


    // Devuelve todos los campos de una clase de sensor (campos de la clase de sensor y campos de clase de la clase de sensor) con tipo de agrupacion de valores
    // (incluyendo los campos con parámetros extra)
    function dame_todos_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_DIA:
                    case INTERVALO_VALORES_SEMANA:
                    case INTERVALO_VALORES_MES:
                    {
                        $campos = array(
                            CAMPO_VALOR_MEDIA,
                            CAMPO_VALOR_SUMA,
                            CAMPO_INCREMENTO_SUMA,
                            CAMPO_INCREMENTO_MEDIA);
                        break;
                    }
                    default:
                    {
                        $campos = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
                        break;
                    }
                }
                break;
            }
            default:
            {
                $campos = dame_todos_campos_clase_sensor_parametros_extra($clase_sensor);
                break;
            }
        }
        return ($campos);
    }


    // Devuelve la operación para agrupar los valores de un campo de sensor
    function dame_operacion_agrupacion_valores_campo_clase_sensor($clase_sensor, $campo)
    {
        $tipo_valores = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        switch ($tipo_valores)
        {
            case TIPO_VALORES_SENSOR_PUNTUALES:
            {
                $operacion_agrupacion_valores = "AVG";
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES:
            {
                $operacion_agrupacion_valores = "SUM";
                break;
            }
        }

        // Campos especiales
        switch ($campo)
        {
            case CAMPO_SOBREPOTENCIA:
            {
                $operacion_agrupacion_valores = "MAX";
                break;
            }
        }

        return ($operacion_agrupacion_valores);
    }


    // Elimina el tipo de agrupación de valores del campo correspondiente (media o suma)
    function elimina_tipo_agrupacion_valores_campo_sensor($campo)
    {
        switch ($campo)
        {
            case CAMPO_VALOR_MEDIA:
            case CAMPO_VALOR_SUMA:
            {
                $campo = CAMPO_VALOR;
                break;
            }
            case CAMPO_INCREMENTO_MEDIA:
            case CAMPO_INCREMENTO_SUMA:
            {
                $campo = CAMPO_INCREMENTO;
                break;
            }
        }
        return ($campo);
    }


    //
    // Funciones para devolver valores 'horarios' de sensores
    //


    // Devuelve los campos puntuales 'horarios' de una clase de sensor
    function dame_campos_puntuales_horarios_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_puntuales_horarios = dame_campos_puntuales_clase_clase_sensor($clase_sensor);
        }
        else
        {
            $campos_puntuales_horarios = dame_campos_puntuales_clase_sensor($clase_sensor);
        }
        return ($campos_puntuales_horarios);
    }


    // Devuelve los campos incrementos 'horarios' de una clase de sensor
    function dame_campos_incrementos_horarios_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_incrementos_horarios = dame_campos_incrementos_clase_clase_sensor($clase_sensor);
        }
        else
        {
            $campos_incrementos_horarios = dame_campos_incrementos_clase_sensor($clase_sensor);
        }
        return ($campos_incrementos_horarios);
    }


    // Devuelve los campos 'horarios' de clase sensor
    function dame_campos_horarios_clase_sensor($clase_sensor)
    {
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_sensor_valores_clase = $caracteristicas_clase_sensor["valores_clase"];
        if ($clase_sensor_valores_clase == true)
        {
            $campos_horarios = dame_campos_clase_clase_sensor($clase_sensor);
        }
        else
        {
            $campos_horarios = dame_campos_clase_sensor($clase_sensor);
        }
        return ($campos_horarios);
    }


    //
    // Funciones de tipos de valores
    //


    // Devuelve el tipo de valores de la clase de sensor y campo especificados
    function dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo)
    {
        switch ($campo)
        {
            case CAMPO_VALOR_MEDIA:
            case CAMPO_INCREMENTO_MEDIA:
            {
                $tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
                break;
            }
            case CAMPO_VALOR_SUMA:
            case CAMPO_INCREMENTO_SUMA:
            {
                $tipo_valores = TIPO_VALORES_SENSOR_INCREMENTALES;
                break;
            }
            default:
            {
                $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                $campos_puntuales_clase_clase_sensor = dame_campos_puntuales_clase_clase_sensor($clase_sensor);
                $campos_incrementos_clase_clase_sensor = dame_campos_incrementos_clase_clase_sensor($clase_sensor);
                if ((in_array($campo, $caracteristicas_clase_sensor["campos_puntuales"]) == true) ||
                    (in_array($campo, $caracteristicas_clase_sensor["campos_puntuales_calculados"]) == true) ||
                    (in_array($campo, $campos_puntuales_clase_clase_sensor) == true))
                {
                    $tipo_valores = TIPO_VALORES_SENSOR_PUNTUALES;
                }
                else
                {
                    if ((in_array($campo, $caracteristicas_clase_sensor["campos_incrementos"]) == true) ||
                        (in_array($campo, $caracteristicas_clase_sensor["campos_incrementos_calculados"]) == true) ||
                        (in_array($campo, $campos_incrementos_clase_clase_sensor) == true))
                    {
                        $tipo_valores = TIPO_VALORES_SENSOR_INCREMENTALES;
                    }
                    else
                    {
                        throw new Exception("Campo incorrecto: '".$campo."' (clase de sensor: '".$clase_sensor."')");
                    }
                }
                break;
            }
        }
        return ($tipo_valores);
    }


    //
    // Funciones de descripciones
    //


    // Devuelve la descripcion de un campo de sensor
    function dame_descripcion_campo_clase_sensor($clase_sensor, $campo)
    {
        switch ($campo)
        {
            case CAMPO_NINGUNO:
            {
                $descripcion_campo = "Ninguno";
                break;
            }
            case CAMPO_TEMPERATURA:
            {
                $descripcion_campo = "Temperatura";
                break;
            }
            case CAMPO_GRADOS_HORA_CALEFACCION:
            {
                $descripcion_campo = "Grados hora calefacción";
                break;
            }
            case CAMPO_GRADOS_HORA_REFRIGERACION:
            {
                $descripcion_campo = "Grados hora refrigeración";
                break;
            }
            case CAMPO_GRADOS_DIA_CALEFACCION:
            {
                $descripcion_campo = "Grados día calefacción";
                break;
            }
            case CAMPO_GRADOS_DIA_REFRIGERACION:
            {
                $descripcion_campo = "Grados día refrigeración";
                break;
            }
            case CAMPO_HUMEDAD:
            {
                $descripcion_campo = "Humedad";
                break;
            }
            case CAMPO_ILUMINACION:
            {
                $descripcion_campo = "Iluminación";
                break;
            }
            case CAMPO_LUZ_ARTIFICIAL:
            {
                $descripcion_campo = "Luz artificial";
                break;
            }
            case CAMPO_VELOCIDAD:
            {
                $descripcion_campo = "Velocidad";
                break;
            }
            case CAMPO_DIRECCION:
            {
                $descripcion_campo = "Dirección";
                break;
            }
            case CAMPO_DIA_NOCHE:
            {
                $descripcion_campo = "Día / Noche";
                break;
            }
            case CAMPO_ABSOLUTO:
            {
                $descripcion_campo = "Absoluto";
                break;
            }
            case CAMPO_INCREMENTO:
            {
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    case CLASE_SENSOR_AGUA:
                    {
                        $descripcion_campo = "Consumo";
                        break;
                    }
                    case CLASE_SENSOR_GAS:
                    {
                        $descripcion_campo = "Volumen";
                        break;
                    }
                    case CLASE_SENSOR_GENERICA:
                    {
                        $descripcion_campo = "Incremento";
                        break;
                    }
                }
                break;
            }
            case CAMPO_INCREMENTO_POTENCIA:
            {
                $descripcion_campo = "Potencia";
                break;
            }
            case CAMPO_TRAMO:
            {
                $descripcion_campo = "Tramo";
                break;
            }
            case CAMPO_COSTE:
            {
                $descripcion_campo = "Coste";
                break;
            }
            case CAMPO_SOBREPOTENCIA:
            {
                $descripcion_campo = "Sobrepotencia";
                break;
            }
            case CAMPO_COSENO_PHI:
            {
                $descripcion_campo = "Coseno de phi";
                break;
            }
            case CAMPO_PENALIZABLE:
            {
                $descripcion_campo = "Penalizable";
                break;
            }
            case CAMPO_CORTES:
            {
                $descripcion_campo = "Cortes";
                break;
            }
            case CAMPO_CONSUMO_ESTIMADO:
            {
                $descripcion_campo = "Consumo estimado";
                break;
            }
            case CAMPO_CONSUMO_REAL:
            {
                $descripcion_campo = "Consumo real";
                break;
            }
            case CAMPO_DESVIO_CONSUMO:
            {
                $descripcion_campo = "Desvío de consumo";
                break;
            }
            case CAMPO_COSTE_DESVIO:
            {
                $descripcion_campo = "Coste de desvío";
                break;
            }
            case CAMPO_CONSUMO:
            {
                $descripcion_campo = "Consumo";
                break;
            }
            case CAMPO_VALOR:
            {
                $descripcion_campo = "Valor";
                break;
            }
            // Campos de clase genérica con tipos de agrupación de valores
            case CAMPO_VALOR_MEDIA:
            {
                $descripcion_campo = "Valor (media)";
                break;
            }
            case CAMPO_VALOR_SUMA:
            {
                $descripcion_campo = "Valor (suma)";
                break;
            }
            case CAMPO_INCREMENTO_MEDIA:
            {
                $descripcion_campo = "Incremento (media)";
                break;
            }
            case CAMPO_INCREMENTO_SUMA:
            {
                $descripcion_campo = "Incremento (suma)";
                break;
            }
            default:
            {
                throw new Exception("Campo desconocido: '".$campo."'");
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_campo));
    }


    // Devuelve la descripcion de parámetros extra de campo de sensor
    function dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo)
    {
        $descripcion = "";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                switch ($campo)
                {
                    case CAMPO_GRADOS_HORA_CALEFACCION:
                    case CAMPO_GRADOS_HORA_REFRIGERACION:
                    case CAMPO_GRADOS_DIA_CALEFACCION:
                    case CAMPO_GRADOS_DIA_REFRIGERACION:
                    {
                        $descripcion = "Referencia";
                        break;
                    }
                }
                break;
            }
        }
        if ($descripcion != "")
        {
            $idiomas = new Idiomas();
            $descripcion = $idiomas->_($descripcion);
        }
        return ($descripcion);
    }
?>
