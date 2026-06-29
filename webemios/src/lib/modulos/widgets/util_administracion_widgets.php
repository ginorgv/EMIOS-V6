<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    // Devuelve los tipos de widget disponibles
    function dame_tipos_widget_disponibles($modulo)
    {
        $idiomas = new Idiomas();

        $tipos_widget = array();
        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
        $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);

        // Se añaden los tipos de widget dependiendo del módulo
        array_push($tipos_widget, array(TIPO_NINGUNO, $idiomas->_("Ninguno")));
        array_push($tipos_widget, array(TIPO_WIDGET_IMAGEN, dame_descripcion_tipo_widget(TIPO_WIDGET_IMAGEN)));
        switch ($modulo)
        {
            case MODULO_PERSONAL:
            {
                // Se añaden los widgets del módulo de Localizaciones
                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                {
                    // Widgets de la sección de ratios
                    if ((count($secciones_usuario[MODULO_LOCALIZACIONES]) == 0) || (in_array(SECCION_LOCALIZACIONES_RATIOS, $secciones_usuario[MODULO_LOCALIZACIONES]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_RATIO, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_RATIO)));
                    }
                }

                // Se añaden los widgets del módulo de Sensores
                if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
                {
                    // Widgets de la sección de información
                    if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_DIGITAL_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_DIGITAL_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_ANALOGICO_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_ANALOGICO_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_VALORES_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_VALORES_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_MAPA_CALOR_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_MAPA_CALOR_SENSOR)));
                    }

                    // Widgets de la sección de comparación
                    if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES)));
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES)));
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES)));
                        array_push($tipos_widget, array(TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES, dame_descripcion_tipo_widget(TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES)));
                        array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES)));
                    }
                }

                // Se añaden los widgets del módulo de Actuadores
                if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
                {
                    // Widgets de la sección de información
                    if ((count($secciones_usuario[MODULO_ACTUADORES]) == 0) || (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_INFORMACION_ACTUADOR, dame_descripcion_tipo_widget(TIPO_WIDGET_INFORMACION_ACTUADOR)));
                        array_push($tipos_widget, array(TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES, dame_descripcion_tipo_widget(TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES)));
                    }
                }

                // Se añaden los widgets del módulo de SmartMeter
                if (in_array(MODULO_SMARTMETER, $modulos_usuario) == true)
                {
                    // Características de tarifas
                    $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
                    $caracteristicas_tarifas_gas = dame_caracteristicas_tarifas_gas_pais();

                    // Widgets de la sección de consumos y costes
                    if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == true))
                    {
                        if ($caracteristicas_tarifas_electricas["tramos"] == true)
                        {
                            array_push($tipos_widget, array(TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR)));
                        }
                    }

                    // Widgets de la sección de facturas
                    if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == true))
                    {
                        if (($caracteristicas_tarifas_electricas["facturas"] == true) || ($caracteristicas_tarifas_gas["facturas"] == true))
                        {
                            array_push($tipos_widget, array(TIPO_WIDGET_COSTE_FACTURA_SENSOR, dame_descripcion_tipo_widget(TIPO_WIDGET_COSTE_FACTURA_SENSOR)));
                        }
                    }
                }

                // Se añaden los widgets del módulo de Proyectos
                if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
                {
                    // Widgets de la sección de líneas base
                    if ((count($secciones_usuario[MODULO_PROYECTOS]) == 0) || (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_SIMULADOR_LINEA_BASE, dame_descripcion_tipo_widget(TIPO_WIDGET_SIMULADOR_LINEA_BASE)));
                    }

                    // Widgets de la sección de información
                    if ((count($secciones_usuario[MODULO_PROYECTOS]) == 0) || (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == true))
                    {
                        array_push($tipos_widget, array(TIPO_WIDGET_INFORMACION_PROYECTO, dame_descripcion_tipo_widget(TIPO_WIDGET_INFORMACION_PROYECTO)));
                    }
                }
            }
        }
        return ($tipos_widget);
    }


    // Devuelve la descripción del tipo de widget
    function dame_descripcion_tipo_widget($tipo_widget)
    {
        switch ($tipo_widget)
        {
            // Widgets "generales" (sin módulo asociado)
            case TIPO_WIDGET_IMAGEN:
            {
                $descripcion_tipo_widget = "Imagen";
                break;
            }
            // Widgets de localizaciones (Ratios)
            case TIPO_WIDGET_VALOR_RATIO:
            {
                $descripcion_tipo_widget = "Valor de ratio";
                break;
            }
            // Widgets de sensores (Información)
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            {
                $descripcion_tipo_widget = "Valor digital";
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            {
                $descripcion_tipo_widget = "Valor digital medio / acumulado";
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            {
                $descripcion_tipo_widget = "Valor analógico";
                break;
            }
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            {
                $descripcion_tipo_widget = "Valor analógico medio / acumulado";
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            {
                $descripcion_tipo_widget = "Gráfica de valores";
                break;
            }
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            {
                $descripcion_tipo_widget = "Mapa de calor";
                break;
            }
            // Widgets de sensores (Comparación)
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            {
                $descripcion_tipo_widget = "Gráfica de comparación de periodos";
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            {
                $descripcion_tipo_widget = "Evolución de valores de comparación de periodos";
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            {
                $descripcion_tipo_widget = "Gráfica de comparación de campos iguales";
                break;
            }
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            {
                $descripcion_tipo_widget = "Gráfica de comparación de campos diferentes";
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            {
                $descripcion_tipo_widget = "Gráfica de valores generales";
                break;
            }
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            {
                $descripcion_tipo_widget = "Valor agregado de valores generales";
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                $descripcion_tipo_widget = "Gráfica de incrementos totales";
                break;
            }
            // Widgets de actuadores (Información)
            case TIPO_WIDGET_INFORMACION_ACTUADOR:
            {
                $descripcion_tipo_widget = "Información de actuador";
                break;
            }
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
            {
                $descripcion_tipo_widget = "Información de grupo de actuadores";
                break;
            }
            // Widgets de SmartMeter (Consumos y costes)
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
            {
                $descripcion_tipo_widget = "Consumos y costes por tramo";
                break;
            }
            // Widgets de SmartMeter (Facturas)
            case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
            {
                $descripcion_tipo_widget = "Factura";
                break;
            }
            // Widgets de proyectos (Líneas base)
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
            {
                $descripcion_tipo_widget = "Simulación de línea base";
                break;
            }
            // Widgets de proyectos (Información)
            case TIPO_WIDGET_INFORMACION_PROYECTO:
            {
                $descripcion_tipo_widget = "Información de proyecto";
                break;
            }
            default:
            {
                $descripcion_tipo_widget = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        $descripcion_tipo_widget = $idiomas->_($descripcion_tipo_widget);

        // Se añade la descripción del módulo (si es necesario)
        switch ($tipo_widget)
        {
            // Widgets de localizaciones
            case TIPO_WIDGET_VALOR_RATIO:
            {
                $modulo = MODULO_LOCALIZACIONES;
                break;
            }
            // Widgets de sensores
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                $modulo = MODULO_SENSORES;
                break;
            }
            // Widgets de actuadores
            case TIPO_WIDGET_INFORMACION_ACTUADOR:
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
            {
                $modulo = MODULO_ACTUADORES;
                break;
            }
            // Widgets de SmartMeter
            case TIPO_WIDGET_GRAFICA_CONSUMOS_COSTES_TRAMOS_SENSOR:
            case TIPO_WIDGET_COSTE_FACTURA_SENSOR:
            {
                $modulo = MODULO_SMARTMETER;
                break;
            }
            // Widgets de proyectos
            case TIPO_WIDGET_SIMULADOR_LINEA_BASE:
            case TIPO_WIDGET_INFORMACION_PROYECTO:
            {
                $modulo = MODULO_PROYECTOS;
                break;
            }
            default:
            {
                $modulo = NULL;
                break;
            }
        }
        if ($modulo !== NULL)
        {
           $nombre_modulo = dame_nombre_modulo($modulo);
           $descripcion_tipo_widget = "(".$nombre_modulo.") ".$descripcion_tipo_widget;
        }
        return ($descripcion_tipo_widget);
    }


    // Devuelve la lista de clases de sensor según el tipo de widget
    function dame_lista_clases_sensor_widget($tipo_widget, &$clase_sensor_seleccionada)
    {
        $idiomas = new Idiomas();

        $lista_clases_sensor = "";
        $lista_clases_sensor .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_sensor_seleccionada);
        $clases_sensor = dame_clases_sensor_usuario_actual(false);
        if (($clase_sensor_seleccionada != CLASE_NINGUNA) && (in_array($clase_sensor_seleccionada, $clases_sensor) == False))
        {
            array_push($clases_sensor, $clase_sensor_seleccionada);

            // Se reordenan las clases de sensor
            $clases_sensor_reordenadas = array();
            $clases_sensor_ordenadas = NodoSensor::dame_clases_sensor();
            foreach ($clases_sensor_ordenadas as $clase_sensor)
            {
                if (in_array($clase_sensor, $clases_sensor) == true)
                {
                    array_push($clases_sensor_reordenadas, $clase_sensor);
                }
            }
            $clases_sensor = $clases_sensor_reordenadas;
        }

        // Se recorren las clases de sensor y se excluyen aquellas no visibles en el tipo de widget correspondiente
        foreach ($clases_sensor as $clase_sensor)
        {
            $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $anyadir_clase_sensor = true;
            switch ($tipo_widget)
            {
                case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                {
                    switch ($clase_sensor)
                    {
                        case CLASE_SENSOR_CORTES_TENSION:
                        {
                            $anyadir_clase_sensor = false;
                            break;
                        }
                    }
                    break;
                }
                case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                {
                    switch ($clase_sensor)
                    {
                        case CLASE_SENSOR_CORTES_TENSION:
                        {
                            $anyadir_clase_sensor = false;
                            break;
                        }
                    }
                    break;
                }
                case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                {
                    $campos_incrementos = dame_todos_campos_incrementos_clase_sensor_parametros_extra($clase_sensor);
                    if (count($campos_incrementos) == 0)
                    {
                        $anyadir_clase_sensor = false;
                    }
                    break;
                }
            }
            if ($anyadir_clase_sensor == true)
            {
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($nombre_clase_sensor, $clase_sensor, $clase_sensor_seleccionada);
            }
            else
            {
                if ($clase_sensor_seleccionada == $clase_sensor)
                {
                    $clase_sensor_seleccionada = CLASE_NINGUNA;
                }
            }
        }

        return ($lista_clases_sensor);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget
    function dame_lista_campos_sensor_widget(
        $tipo_widget,
        $clase_sensor,
        $granularidad_sensor,
        $intervalo_valores,
        $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_TEMPERATURA), CAMPO_TEMPERATURA, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_GRADOS_HORA_CALEFACCION), CAMPO_GRADOS_HORA_CALEFACCION, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_GRADOS_HORA_REFRIGERACION), CAMPO_GRADOS_HORA_REFRIGERACION, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_GRADOS_DIA_CALEFACCION), CAMPO_GRADOS_DIA_CALEFACCION, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_GRADOS_DIA_REFRIGERACION), CAMPO_GRADOS_DIA_REFRIGERACION, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_HUMEDAD), CAMPO_HUMEDAD, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        break;
                    }
                }
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_ILUMINACION), CAMPO_ILUMINACION, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_LUZ_ARTIFICIAL), CAMPO_LUZ_ARTIFICIAL, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_ILUMINACION), CAMPO_ILUMINACION, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        break;
                    }
                }
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_VELOCIDAD), CAMPO_VELOCIDAD, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_DIRECCION), CAMPO_DIRECCION, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $lista_campos = dame_lista_campos_sensor_widget_energia_activa($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $lista_campos = dame_lista_campos_sensor_widget_energia_reactiva($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_CORTES), CAMPO_CORTES, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $lista_campos = dame_lista_campos_sensor_widget_compra_energia($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $lista_campos = dame_lista_campos_sensor_widget_gas($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $lista_campos = dame_lista_campos_sensor_widget_agua($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                $lista_campos = dame_lista_campos_sensor_widget_generica($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado);
                break;
            }
        }

        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (energía activa)
    function dame_lista_campos_sensor_widget_energia_activa($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            // España
            case PAIS_ESPANYA:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    {
                        switch ($granularidad_sensor)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                break;
                            }
                            case GRANULARIDAD_CUARTOHORARIA:
                            case GRANULARIDAD_HORARIA:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA), CAMPO_SOBREPOTENCIA, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                                break;
                            }
                            break;
                        }
                        break;
                    }
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA), CAMPO_SOBREPOTENCIA, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA), CAMPO_SOBREPOTENCIA, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_TIEMPO_REAL:
                            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                            {
                                break;
                            }
                            default:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_SOBREPOTENCIA), CAMPO_SOBREPOTENCIA, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (energía reactiva)
    function dame_lista_campos_sensor_widget_energia_reactiva($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            // España
            case PAIS_ESPANYA:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    {
                        switch ($granularidad_sensor)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                break;
                            }
                            case GRANULARIDAD_CUARTOHORARIA:
                            case GRANULARIDAD_HORARIA:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI), CAMPO_COSENO_PHI, $campo_seleccionado);
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_PENALIZABLE), CAMPO_PENALIZABLE, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                                break;
                            }
                            break;
                        }
                        break;
                    }
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI), CAMPO_COSENO_PHI, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_PENALIZABLE), CAMPO_PENALIZABLE, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_TRAMO), CAMPO_TRAMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI), CAMPO_COSENO_PHI, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_TIEMPO_REAL:
                            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                            {
                                break;
                            }
                            default:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_COSENO_PHI), CAMPO_COSENO_PHI, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_PENALIZABLE), CAMPO_PENALIZABLE, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA, CAMPO_INCREMENTO_POTENCIA), CAMPO_INCREMENTO_POTENCIA, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }

        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (compra de energía)
    function dame_lista_campos_sensor_widget_compra_energia($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            // España
            case PAIS_ESPANYA:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    {
                        switch ($granularidad_sensor)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_ABSOLUTO), CAMPO_CONSUMO_ESTIMADO, $campo_seleccionado);
                                break;
                            }
                            case GRANULARIDAD_HORARIA:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_ESTIMADO), CAMPO_CONSUMO_ESTIMADO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_REAL), CAMPO_CONSUMO_REAL, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_DESVIO_CONSUMO), CAMPO_DESVIO_CONSUMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_COSTE_DESVIO), CAMPO_COSTE_DESVIO, $campo_seleccionado);
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_PENALIZABLE), CAMPO_PENALIZABLE, $campo_seleccionado);
                                        break;
                                    }
                                }
                                break;
                            }
                            break;
                        }
                        break;
                    }
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_ESTIMADO), CAMPO_CONSUMO_ESTIMADO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_REAL), CAMPO_CONSUMO_REAL, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_DESVIO_CONSUMO), CAMPO_DESVIO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_COSTE_DESVIO), CAMPO_COSTE_DESVIO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_PENALIZABLE), CAMPO_PENALIZABLE, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_ESTIMADO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_REAL), CAMPO_CONSUMO_REAL, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_DESVIO_CONSUMO), CAMPO_DESVIO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_COSTE_DESVIO), CAMPO_COSTE_DESVIO, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_ESTIMADO), CAMPO_CONSUMO_ESTIMADO, $campo_seleccionado);
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_TIEMPO_REAL:
                            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                            {
                                break;
                            }
                            default:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_REAL), CAMPO_CONSUMO_REAL, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_DESVIO_CONSUMO), CAMPO_DESVIO_CONSUMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_COSTE_DESVIO), CAMPO_COSTE_DESVIO, $campo_seleccionado);
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_ESTIMADO), CAMPO_CONSUMO_ESTIMADO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_CONSUMO_REAL), CAMPO_CONSUMO_REAL, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_DESVIO_CONSUMO), CAMPO_DESVIO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, CAMPO_COSTE_DESVIO), CAMPO_COSTE_DESVIO, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }

        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (gas)
    function dame_lista_campos_sensor_widget_gas($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            // España
            case PAIS_ESPANYA:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    {
                        switch ($granularidad_sensor)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO)." (".$idiomas->_("por hora").")", CAMPO_INCREMENTO, $campo_seleccionado);
                                break;
                            }
                            case GRANULARIDAD_CUARTOHORARIA:
                            case GRANULARIDAD_HORARIA:
                            {
                                switch ($tipo_widget)
                                {
                                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                                    {
                                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                                        break;
                                    }
                                }
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_CONSUMO), CAMPO_CONSUMO, $campo_seleccionado);
                                break;
                            }
                            break;
                        }
                        break;
                    }
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_CONSUMO), CAMPO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_CONSUMO), CAMPO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_TIEMPO_REAL:
                            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                            {
                                break;
                            }
                            default:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_CONSUMO), CAMPO_CONSUMO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                                break;
                            }
                        }
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_CONSUMO), CAMPO_CONSUMO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, CAMPO_COSTE), CAMPO_COSTE, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (agua)
    function dame_lista_campos_sensor_widget_agua($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
        switch ($pais_tarifas_agua)
        {
            // España
            case PAIS_ESPANYA:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
                    {
                        switch ($granularidad_sensor)
                        {
                            case GRANULARIDAD_TIEMPO_REAL:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO)." (".$idiomas->_("por hora").")", CAMPO_INCREMENTO, $campo_seleccionado);
                                break;
                            }
                            case GRANULARIDAD_CUARTOHORARIA:
                            case GRANULARIDAD_HORARIA:
                            {
                                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                                break;
                            }
                            break;
                        }
                        break;
                    }
                    case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
                    case TIPO_WIDGET_MAPA_CALOR_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
                    case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
                    case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_ABSOLUTO), CAMPO_ABSOLUTO, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        break;
                    }
                    case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
            }
        }
        return ($lista_campos);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de widget (generica)
    function dame_lista_campos_sensor_widget_generica($tipo_widget, $granularidad_sensor, $intervalo_valores, $campo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_campos = "";
        switch ($granularidad_sensor)
        {
            case GRANULARIDAD_TIEMPO_REAL:
            {
                switch ($tipo_widget)
                {
                    case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple($idiomas->_("Todos"), CAMPO_TODOS, $campo_seleccionado);
                        break;
                    }
                }
            }
        }
        switch ($tipo_widget)
        {
            case TIPO_WIDGET_VALOR_DIGITAL_SENSOR:
            case TIPO_WIDGET_VALOR_ANALOGICO_SENSOR:
            case TIPO_WIDGET_MAPA_CALOR_SENSOR:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR), CAMPO_VALOR, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO)." (".$idiomas->_("por hora").")", CAMPO_INCREMENTO, $campo_seleccionado);
                break;
            }
            case TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR:
            case TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR_MEDIA), CAMPO_VALOR_MEDIA, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR_SUMA), CAMPO_VALOR_SUMA, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO_SUMA), CAMPO_INCREMENTO_SUMA, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO_MEDIA), CAMPO_INCREMENTO_MEDIA, $campo_seleccionado);
                break;
            }
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES:
            case TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES:
            case TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES:
            {
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL:
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                    case INTERVALO_VALORES_HORA:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR), CAMPO_VALOR, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                        break;
                    }
                    default:
                    {
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR_MEDIA), CAMPO_VALOR_MEDIA, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR_SUMA), CAMPO_VALOR_SUMA, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO_SUMA), CAMPO_INCREMENTO_SUMA, $campo_seleccionado);
                        $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO_MEDIA), CAMPO_INCREMENTO_MEDIA, $campo_seleccionado);
                        break;
                    }
                }
                break;
            }
            case TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                break;
            }
            default:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_VALOR), CAMPO_VALOR, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                break;
            }
        }
        return ($lista_campos);
    }


    // Devuelve los controles de los colores para los widgets de sensor
    function dame_controles_colores_fondo_widget_sensor($id_controles, $utilizar_colores_fondo, $colores_seleccionados)
    {
        $idiomas = new Idiomas();

        $controles = "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Utilizar colores de fondo").": "."</span><br/>
					<select id='utilizar_colores_fondo_".$id_controles."' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($utilizar_colores_fondo);
		$controles .= "
					</select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid' id='controles_colores_fondo_".$id_controles."'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de fondo").": "."</span><br/>
                    <input type='color' id='color_fondo_1_".$id_controles."'
                        class='TLNT_input_hex_color input-administracion-33-izda selector-color-administracion' value='".$colores_seleccionados[0]."'>
                    <input type='color' id='color_fondo_2_".$id_controles."'
                        class='TLNT_input_hex_color input-administracion-33-centro selector-color-administracion' value='".$colores_seleccionados[1]."'>
                    <input type='color' id='color_fondo_3_".$id_controles."'
                        class='TLNT_input_hex_color input-administracion-33-dcha selector-color-administracion' value='".$colores_seleccionados[2]."'>
                </div>
            </div>";
        return ($controles);
    }


    // Devuelve los controles de los límites de colores para los widgets de sensor
    function dame_controles_valores_limites_colores_fondo_widget_sensor($id_controles, $valor_limite_colores_fondo_1, $valor_limite_colores_fondo_2)
    {
        $idiomas = new Idiomas();

        $controles = "
            <div class='row-fluid' id='controles_valores_limites_colores_fondo_".$id_controles."'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores límite de colores de fondo").": "."</span><br/>
                    <input type='text' id='valor_limite_colores_fondo_1_".$id_controles."'
                        class='TLNT_input_float input-administracion-50-izda' value='".$valor_limite_colores_fondo_1."'>
                    <input type='text' id='valor_limite_colores_fondo_2_".$id_controles."'
                        class='TLNT_input_float input-administracion-50-dcha' value='".$valor_limite_colores_fondo_2."'>
                </div>
            </div>";
        return ($controles);
    }


    // Devuelve la lista con las opciones de valores digitales a mostrar en los widgets de tipo valor analógico de un sensor
    function dame_lista_valores_digitales_tipo_widget_valor_analogico_sensor($clase_sensor, $valor_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_valores_digitales = "";
        $lista_valores_digitales .= dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO, $valor_seleccionado);
        $lista_valores_digitales .= dame_opcion_valor_lista_simple($idiomas->_("Seleccionado"), VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO, $valor_seleccionado);
        return ($lista_valores_digitales);
    }


    // Devuelve la lista con las opciones de valores digitales a mostrar en los widgets de tipo valor analógico medio / acumulado de un sensor
    function dame_lista_valores_digitales_tipo_widget_valor_analogico_medio_acumulado_sensor($valor_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_valores_digitales = "";
        $lista_valores_digitales .= dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO, $valor_seleccionado);
        $lista_valores_digitales .= dame_opcion_valor_lista_simple($idiomas->_("Seleccionado"), VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO, $valor_seleccionado);
        return ($lista_valores_digitales);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de valores de ratios
    function dame_lista_periodos_tiempo_tipo_widget_valor_ratio($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de valores medios / acumulados
    function dame_lista_periodos_tiempo_tipo_widget_valores_medios_acumulados_sensor($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de tipo gráficas de sensor
    function dame_lista_periodos_tiempo_tipo_widget_graficas_sensor($tipo_widget, $periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_HORA), PERIODO_TIEMPO_HORA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de mapa de calor
    function dame_lista_periodos_tiempo_tipo_widget_mapa_calor_sensor($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de comparación de periodos
    function dame_lista_periodos_tiempo_tipo_widgets_comparacion_periodos_sensor($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_HORA), PERIODO_TIEMPO_HORA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de coste de factura de un sensor
    function dame_lista_periodos_tiempo_tipo_widget_coste_factura_sensor($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de simulador de línea base
    function dame_lista_periodos_tiempo_tipo_widget_simulador_linea_base($periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista con los periodos de tiempo para los widgets de información de proyecto
    function dame_lista_periodos_tiempo_tipo_widget_informacion_proyecto($periodo_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista_periodos_tiempo = "";
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple($idiomas->_("Automático")." (".$idiomas->_("desde el inicio del proyecto").")", ID_NINGUNO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
        $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
        return ($lista_periodos_tiempo);
    }


    // Devuelve la lista de intervalos de valores de sensor según el tipo de widget
    function dame_lista_intervalos_valores_sensor_widget(
        $tipo_widget,
        $clase_sensor,
        $campo,
        $periodo_tiempo,
        $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        if ($clase_sensor == CLASE_NINGUNA)
        {
            array_push($intervalos_valores, array(INTERVALO_VALORES_NINGUNO, dame_descripcion_intervalo_valores(INTERVALO_VALORES_NINGUNO)));
            $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
            return ($lista_intervalo_valores);
        }

        switch ($periodo_tiempo)
        {
            case PERIODO_TIEMPO_HORA:
            case PERIODO_TIEMPO_DIA:
            case PERIODO_TIEMPO_SEMANA:
            {
                $permitir_intervalo_valores_tiempo_real = true;
                $permitir_intervalo_valores_hora = true;
                break;
            }
            case PERIODO_TIEMPO_MES:
            {
                $permitir_intervalo_valores_tiempo_real = false;
                $permitir_intervalo_valores_hora = true;
                break;
            }
            case PERIODO_TIEMPO_ANYO:
            {
                $permitir_intervalo_valores_tiempo_real = false;
                $permitir_intervalo_valores_hora = false;
                break;
            }
            case PERIODO_TIEMPO_FECHA_INICIO:
            {
                $permitir_intervalo_valores_tiempo_real = false;
                $permitir_intervalo_valores_hora = true;
                break;
            }
            default:
            {
                throw new Exception("Periodo de tiempo incorrecto: '".$periodo_tiempo."'");
            }
        }
        switch ($tipo_widget)
        {
            case TIPO_WIDGET_GRAFICA_VALORES_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR:
            case TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES:
            {
                $tipo_widget_grafica_valores = true;
                break;
            }
            case TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR:
            {
                $permitir_intervalo_valores_tiempo_real = false;
                break;
            }
            default:
            {
                $tipo_widget_grafica_valores = false;
                break;
            }
        }

        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
        $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];
        $clase_valores_clase = $caracteristicas_clase_sensor["valores_clase"];

        if ($clase_valores_clase == true)
        {
            $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
            if (in_array($campo, $campos_clase_sensor) == true)
            {
                if ($permitir_intervalo_valores_tiempo_real == true)
                {
                    if ($tipo_widget_grafica_valores == true)
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                    }
                    else
                    {
                        array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                    }
                }
            }
            if ($clase_granularidad_cuartohoraria == true)
            {
                if ($permitir_intervalo_valores_hora == true)
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
                }
            }
            if ($permitir_intervalo_valores_hora == true)
            {
                array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
            }
            array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
            array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
            array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
        }
        else
        {
            if (($permitir_intervalo_valores_tiempo_real == true) || ($clase_procesado_valores == false))
            {
                if ($tipo_widget_grafica_valores == true)
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_LINEAS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_LINEAS)));
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL_PUNTOS)));
                }
                else
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_TIEMPO_REAL, dame_descripcion_intervalo_valores(INTERVALO_VALORES_TIEMPO_REAL)));
                }
            }
            if (($clase_procesado_valores == true) || ($clase_sensor == CLASE_NINGUNA))
            {
                if ($permitir_intervalo_valores_hora == true)
                {
                    array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
                }
                array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
                array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
                array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
            }
        }

        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve las granularidades de un widget
    function dame_lista_granularidades_sensor_widget($clase_sensor, $granularidad_seleccionada)
    {
        $granularidades_widget = array();

        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        $clase_procesado_valores = $caracteristicas_clase_sensor["procesado_valores"];
        $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

        array_push($granularidades_widget, GRANULARIDAD_TIEMPO_REAL);
        if ($clase_granularidad_cuartohoraria == true)
        {
            array_push($granularidades_widget, GRANULARIDAD_CUARTOHORARIA);
        }
        if ($clase_procesado_valores == true)
        {
            array_push($granularidades_widget, GRANULARIDAD_HORARIA);
        }
        foreach ($granularidades_widget as $granularidad_widget)
        {
            $lista .= "<option value='".$granularidad_widget."'";
            if ($granularidad_widget == $granularidad_seleccionada)
            {
                $lista .= " selected";
            }
            $lista .= ">".dame_descripcion_granularidad($granularidad_widget)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de clases de actuador
    function dame_lista_clases_actuador_widget($clase_seleccionada)
    {
        $idiomas = new Idiomas();

        $lista_clases_actuador = "";
        $lista_clases_actuador .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
        $clases_actuador = dame_clases_actuador_usuario_actual(false);
        if (($clase_seleccionada != CLASE_NINGUNA) && (in_array($clase_seleccionada, $clases_actuador) == False))
        {
            array_push($clases_actuador, $clase_seleccionada);

            // Se reordenan las clases de actuador
            $clases_actuador_reordenadas = array();
            $clases_actuador_ordenadas = NodoActuador::dame_clases_actuador();
            foreach($clases_actuador_ordenadas as $clase_actuador)
            {
                if (in_array($clase_actuador, $clases_actuador) == true)
                {
                    array_push($clases_actuador_reordenadas, $clase_actuador);
                }
            }
            $clases_actuador = $clases_actuador_reordenadas;
        }

        // Se recorren las clases de actuador
        foreach ($clases_actuador as $clase_actuador)
        {
            $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
            $lista_clases_actuador .= dame_opcion_valor_lista_simple($nombre_clase_actuador, $clase_actuador, $clase_seleccionada);
        }

        return ($lista_clases_actuador);
    }


    // Devuelve la lista de grupos de actuadores
    function dame_lista_grupos_actuadores_widget($clase_actuador, $id_grupo_actuadores_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_grupos_actuadores = "
            SELECT
                id,
                nombre
            FROM grupos_actuadores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (clase = '".$bd_red->_($clase_actuador)."')
                AND ((id = '".$bd_red->_($id_grupo_actuadores_seleccionado)."') OR ".dame_condicion_consulta_grupos_actuadores_usuario_actual(false).")
            ORDER BY nombre ASC";
        $res_grupos_actuadores = $bd_red->ejecuta_consulta($consulta_grupos_actuadores);
        if ($res_grupos_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos_actuadores."'");
        }

        $lista_grupos_actuadores = "";
        $lista_grupos_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_grupo_actuadores = $res_grupos_actuadores->dame_siguiente_fila())
        {
            $lista_grupos_actuadores .= "<option value='".$fila_grupo_actuadores['id']."'";
			if ($fila_grupo_actuadores['id'] == $id_grupo_actuadores_seleccionado)
			{
				$lista_grupos_actuadores .= " selected";
			}
			$lista_grupos_actuadores .= ">".htmlspecialchars($fila_grupo_actuadores['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos_actuadores);
    }


    // Devuelve la lista de conceptos de factura del widget de coste de factura de un sensor
    function dame_lista_conceptos_factura_widget_coste_factura_sensor($medicion, $concepto_factura)
    {
        $idiomas = new Idiomas();

        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_conceptos = dame_lista_valores(
                            array(
                                array(CONCEPTO_FACTURA_ELECTRICA_TOTAL_ESPANYA, $idiomas->_("Total")),
                                array(CONCEPTO_FACTURA_ELECTRICA_ENERGIA_POTENCIA_ESPANYA, $idiomas->_("Energía y potencia")),
                                array(CONCEPTO_FACTURA_ELECTRICA_ENERGIA_ACTIVA_ESPANYA, $idiomas->_("Energía activa")),
                                array(CONCEPTO_FACTURA_ELECTRICA_POTENCIA_ESPANYA, $idiomas->_("Potencia")),
                                array(CONCEPTO_FACTURA_ELECTRICA_EXCESOS_POTENCIA_ESPANYA, $idiomas->_("Excesos de potencia")),
                                array(CONCEPTO_FACTURA_ELECTRICA_ENERGIA_REACTIVA_ESPANYA, $idiomas->_("Energía reactiva")),
                                array(CONCEPTO_FACTURA_ELECTRICA_OTROS_CONCEPTOS_ESPANYA, $idiomas->_("Otros conceptos"))),
                            array($concepto_factura));
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_conceptos = dame_lista_valores(
                            array(
                                array(CONCEPTO_FACTURA_GAS_TOTAL_ESPANYA, $idiomas->_("Total")),
                                array(CONCEPTO_FACTURA_GAS_CONSUMO_ESPANYA, $idiomas->_("Consumo")),
                                array(CONCEPTO_FACTURA_GAS_TERMINO_FIJO_ESPANYA, $idiomas->_("Término fijo")),
                                array(CONCEPTO_FACTURA_GAS_EXCESOS_CAUDAL_ESPANYA, $idiomas->_("Excesos de caudal")),
                                array(CONCEPTO_FACTURA_GAS_OTROS_CONCEPTOS_ESPANYA, $idiomas->_("Otros conceptos"))),
                            array($concepto_factura));
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_conceptos = dame_lista_valores(
                            array(
                                array(CONCEPTO_FACTURA_AGUA_TOTAL_ESPANYA, $idiomas->_("Total")),
                                array(CONCEPTO_FACTURA_AGUA_CONSUMO_ESPANYA, $idiomas->_("Consumo")),
                                array(CONCEPTO_FACTURA_AGUA_OTROS_CONCEPTOS_ESPANYA, $idiomas->_("Otros conceptos"))),
                            array($concepto_factura));
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            case MEDICION_NINGUNA:
            {
                $lista_conceptos = "";
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($lista_conceptos);
    }


    //
    // Iconos de widgets
    //


    function dame_lista_iconos_widget($icono_seleccionado)
    {
        $idiomas = new Idiomas();

        $lista = "";
        $lista .= dame_entrada_lista(ID_NINGUNO, $idiomas->_("Ninguno"), $icono_seleccionado);
        $lista .= dame_entrada_lista("agua", dame_descripcion_icono_widget("agua"), $icono_seleccionado);
        $lista .= dame_entrada_lista("arbol", dame_descripcion_icono_widget("arbol"), $icono_seleccionado);
        $lista .= dame_entrada_lista("bombilla", dame_descripcion_icono_widget("bombilla"), $icono_seleccionado);
        $lista .= dame_entrada_lista("coche", dame_descripcion_icono_widget("coche"), $icono_seleccionado);
        $lista .= dame_entrada_lista("edificio", dame_descripcion_icono_widget("edificio"), $icono_seleccionado);
        $lista .= dame_entrada_lista("electrico", dame_descripcion_icono_widget("electrico"), $icono_seleccionado);
        $lista .= dame_entrada_lista("fuego", dame_descripcion_icono_widget("fuego"), $icono_seleccionado);
        $lista .= dame_entrada_lista("nube", dame_descripcion_icono_widget("nube"), $icono_seleccionado);
        $lista .= dame_entrada_lista("persona", dame_descripcion_icono_widget("persona"), $icono_seleccionado);
        $lista .= dame_entrada_lista("reciclaje", dame_descripcion_icono_widget("reciclaje"), $icono_seleccionado);
        $lista .= dame_entrada_lista("reloj", dame_descripcion_icono_widget("reloj"), $icono_seleccionado);
        $lista .= dame_entrada_lista("residuos", dame_descripcion_icono_widget("residuos"), $icono_seleccionado);
        $lista .= dame_entrada_lista("temperatura", dame_descripcion_icono_widget("temperatura"), $icono_seleccionado);
        $lista .= dame_entrada_lista("radiacion", dame_descripcion_icono_widget("radiacion"), $icono_seleccionado);
        return ($lista);
    }


    function dame_descripcion_icono_widget($icono)
    {
        switch ($icono)
        {
            case "agua":
            {
                $descripcion = "Agua";
                break;
            }
            case "arbol":
            {
                $descripcion = "Árbol";
                break;
            }
            case "bombilla":
            {
                $descripcion = "Bombilla";
                break;
            }
            case "coche":
            {
                $descripcion = "Coche";
                break;
            }
            case "edificio":
            {
                $descripcion = "Edificio";
                break;
            }
            case "electrico":
            {
                $descripcion = "Eléctrico";
                break;
            }
            case "fuego":
            {
                $descripcion = "Fuego";
                break;
            }
            case "nube":
            {
                $descripcion = "Nube";
                break;
            }
            case "persona":
            {
                $descripcion = "Persona";
                break;
            }
            case "reciclaje":
            {
                $descripcion = "Reciclaje";
                break;
            }
            case "reloj":
            {
                $descripcion = "Reloj";
                break;
            }
            case "residuos":
            {
                $descripcion = "Residuos";
                break;
            }
            case "temperatura":
            {
                $descripcion = "Temperatura";
                break;
            }
            case "radiacion":
            {
                $descripcion = "Radiación";
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
?>