<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/util_informes_analisis.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_informes_eventos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_informes_compra_energia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/util_informes_consumos_costes_gas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_elementos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_informes_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    // Devuelve los tipos de elemento de plantillas de informes disponibles
    function dame_tipos_elemento_plantillas_informes_disponibles()
    {
        $idiomas = new Idiomas();

        $tipos_elemento = array();
        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
        $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);

        // Se añaden los tipos de elementos dependiendo del módulo
        array_push($tipos_elemento, array(TIPO_NINGUNO, $idiomas->_("Ninguno")));

        // Tipos de elementos disponibles independientes de los módulos y secciones visibles
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS)));
        array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN)));

        // Se añaden los elementos de varios módulos
        $anyadir_elemento_comentarios = false;
        if ($anyadir_elemento_comentarios == false)
        {
            if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
            {
                if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == true))
                {
                    $anyadir_elemento_comentarios = true;
                }
            }
        }
        if ($anyadir_elemento_comentarios == false)
        {
            if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
            {
                if ((count($secciones_usuario[MODULO_ACTUADORES]) == 0) || (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == true))
                {
                    $anyadir_elemento_comentarios = true;
                }
            }
        }
        if ($anyadir_elemento_comentarios == true)
        {
            array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS)));
        }

        // Se añaden los elementos del módulo de Sensores
        if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
        {
            // Elementos de la sección de eventos
            if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_EVENTOS, $secciones_usuario[MODULO_SENSORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS)));
            }

            // Elementos de la sección de información
            if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION)));
            }

            // Elementos de la sección de análisis
            if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_ANALISIS, $secciones_usuario[MODULO_SENSORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO)));
            }

            // Elementos de la sección de comparación
            if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES)));
            }

            // Elementos de la sección de estadística
            if ((count($secciones_usuario[MODULO_SENSORES]) == 0) || (in_array(SECCION_SENSORES_ESTADISTICA, $secciones_usuario[MODULO_SENSORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION)));
            }
        }

        // Se añaden los elementos del módulo de Actuadores
        if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
        {
            // Elementos de la sección de información
            if ((count($secciones_usuario[MODULO_ACTUADORES]) == 0) || (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS)));
            }
        }

        // Se añaden los elementos del módulo de SmartMeter
        if (in_array(MODULO_SMARTMETER, $modulos_usuario) == true)
        {
            // Características de tarifas
            $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
            $caracteristicas_tarifas_gas = dame_caracteristicas_tarifas_gas_pais();

            // Elementos de la sección de consumos y costes
            if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS)));
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS)));
                if ($caracteristicas_tarifas_electricas["tramos"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD)));
                }
                if ($caracteristicas_tarifas_electricas["cortes_tension"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD)));
                }
                if ($caracteristicas_tarifas_electricas["potencias"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD)));
                }
                if ($caracteristicas_tarifas_electricas["energia_reactiva"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD)));
                }
                if ($caracteristicas_tarifas_gas["caudales"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS)));
                }
            }

            // Elementos de la sección de compra de energía
            if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_COMPRA_ENERGIA, $secciones_usuario[MODULO_SMARTMETER]) == true))
            {
                if ($caracteristicas_tarifas_electricas["compra_energia"] == true)
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA)));
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA)));
                }
            }

            // Elementos de la sección de facturas
            if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == true))
            {
                if (($caracteristicas_tarifas_electricas["facturas"] == true) || ($caracteristicas_tarifas_gas["facturas"] == true))
                {
                    array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA)));
                }
            }

            // Elementos de la sección de tarifas
            if ((count($secciones_usuario[MODULO_SMARTMETER]) == 0) || (in_array(SECCION_SMARTMETER_TARIFAS, $secciones_usuario[MODULO_SMARTMETER]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION)));
            }
        }

        // Se añaden los elementos del módulo de Proyectos
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            // Elementos de la sección de información
            if ((count($secciones_usuario[MODULO_PROYECTOS]) == 0) || (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE)));
            }
        }
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            // Elementos de la sección de información
            if ((count($secciones_usuario[MODULO_PROYECTOS]) == 0) || (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == true))
            {
                array_push($tipos_elemento, array(TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO, ElementoPlantillaInforme::dame_descripcion_tipo_elemento(TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO)));
            }
        }

        return ($tipos_elemento);
    }


    // Devuelve la lista de clases de sensor según el tipo de elemento de plantilla de informe
    function dame_lista_clases_sensor_elemento_plantilla_informe($tipo_elemento_plantilla_informe, &$clase_seleccionada)
    {
        $idiomas = new Idiomas();

        $lista_clases_sensor = "";
        switch ($tipo_elemento_plantilla_informe)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($idiomas->_("Todas"), CLASE_TODAS, $clase_seleccionada);
                break;
            }
            default:
            {
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                break;
            }
        }
        $clases_sensor = dame_clases_sensor_usuario_actual(false);
        if (($clase_seleccionada != CLASE_NINGUNA) && ($clase_seleccionada != CLASE_TODAS) &&
            (in_array($clase_seleccionada, $clases_sensor) == False))
        {
            array_push($clases_sensor, $clase_seleccionada);

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

        // Se recorren las clases de sensor y se excluyen aquellas no visibles en el tipo de elemento correspondiente
        foreach ($clases_sensor as $clase_sensor)
        {
            $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $anyadir_clase_sensor = true;
            switch ($tipo_elemento_plantilla_informe)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
                    if ($caracteristicas_clase_sensor["procesado_valores"] == false)
                    {
                        $anyadir_clase_sensor = false;
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
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
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($nombre_clase_sensor, $clase_sensor, $clase_seleccionada);
            }
            else
            {
                if ($clase_seleccionada == $clase_sensor)
                {
                    switch ($tipo_elemento_plantilla_informe)
                    {
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                        {
                            $clase_seleccionada = CLASE_TODAS;
                            break;
                        }
                        default:
                        {
                            $clase_seleccionada = CLASE_NINGUNA;
                            break;
                        }
                    }
                }
            }
        }

        return ($lista_clases_sensor);
    }


    // Devuelve la lista de campos de una clase de sensor según el tipo de elemento de plantilla de informe
    function dame_lista_campos_sensor_elemento_plantilla_informe(
        $tipo_elemento_plantilla_informe,
        $clase_sensor,
        $intervalo_valores,
        $campo_seleccionado)
    {
        $lista_campos = NULL;
        $campos = array();
        switch ($tipo_elemento_plantilla_informe)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                switch ($clase_sensor)
                {
                    case CLASE_NINGUNA:
                    case CLASE_SENSOR_HUMEDAD:
                    case CLASE_SENSOR_VIENTO:
                    case CLASE_SENSOR_LUZ_INTERIOR:
                    case CLASE_SENSOR_CORTES_TENSION:
                    {
                        $campos = array();
                        break;
                    }
                    case CLASE_SENSOR_TEMPERATURA:
                    {
                        $campos_clase_sensor = dame_campos_clase_sensor($clase_sensor);
                        $campos_incrementos_calculados = dame_campos_incrementos_calculados_clase_sensor($clase_sensor);
                        $campos = array_merge($campos_clase_sensor, $campos_incrementos_calculados);
                        break;
                    }
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    case CLASE_SENSOR_COMPRA_ENERGIA:
                    case CLASE_SENSOR_GAS:
                    case CLASE_SENSOR_AGUA:
                    case CLASE_SENSOR_GENERICA:
                    {
                        switch ($intervalo_valores)
                        {
                            case INTERVALO_VALORES_TIEMPO_REAL:
                            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                            {
                                $campos = dame_campos_clase_sensor($clase_sensor);
                                break;
                            }
                            default:
                            {
                                $campos = dame_todos_campos_clase_sensor_tipo_agrupacion_valores($clase_sensor, $intervalo_valores);
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $lista_campos = dame_lista_campos_clase_sensor_parametros_extra($clase_sensor, $campo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $lista_campos = dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra(
                    $clase_sensor,
                    $intervalo_valores,
                    $campo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $lista_campos = dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra(
                    $clase_sensor,
                    $intervalo_valores,
                    $campo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $lista_campos = dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra(
                    $clase_sensor,
                    $intervalo_valores,
                    $campo_seleccionado);
                break;
            }
        }

        if ($lista_campos === NULL)
        {
            $lista_campos = "";
            foreach ($campos as $campo)
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, $campo), $campo, $campo_seleccionado);
            }
        }
        return ($lista_campos);
    }


    // Devuelve la lista de intervalos de valores de sensor según el tipo de elemento de plantilla de informe
    function dame_lista_intervalos_valores_sensor_elemento_plantilla_informe(
        $tipo_elemento_plantilla_informe,
        $clase_sensor,
        $campo,
        $intervalo_seleccionado)
    {
        $lista_intervalos_valores = NULL;
        $intervalos_valores = array();
        switch ($tipo_elemento_plantilla_informe)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo(
                    $clase_sensor,
                    $campo,
                    $intervalo_seleccionado,
                    OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_SIN_OPCIONES_EXTRA);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_comparacion_perfil_horario($intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_comparacion_campos_diferentes($intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_analisis_comparativo_clase_sensor($clase_sensor, $intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo(
                    $clase_sensor,
                    $campo,
                    $intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo(
                    $clase_sensor,
                    $campo,
                    $intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_histograma_clase_sensor_campo(
                    $clase_sensor,
                    $campo,
                    $intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informe_correlacion($intervalo_seleccionado);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo(
                    $clase_sensor,
                    $campo,
                    $intervalo_seleccionado,
                    OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $lista_intervalos_valores = dame_lista_intervalos_valores_informes_consumos_costes_clase_sensor(
                    $clase_sensor,
                    $intervalo_seleccionado);
                break;
            }
        }

        if ($lista_intervalos_valores === NULL)
        {
            $lista_intervalos_valores = "";
            foreach ($intervalos_valores as $intervalo_valores)
            {
                $lista_intervalos_valores .= dame_opcion_valor_lista_simple(dame_descripcion_intervalo_valores($intervalo_valores), $intervalo_valores, $intervalo_seleccionado);
            }
        }
        return ($lista_intervalos_valores);
    }


    // Devuelve la lista de elementos de informe (visibles) de un elemento de plantilla de informe
    function dame_lista_elementos_informe_elemento_plantilla_informe($tipo_elemento_plantilla_informe, $parametros_informe, $elementos_informe_seleccionados)
    {
        $elementos_informe = array();
        switch ($tipo_elemento_plantilla_informe)
        {
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $elementos_informe = dame_elementos_informe_sensores_activaciones_eventos();
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $clase_sensor = $parametros_informe["clase_sensor"];
                $elementos_informe = dame_elementos_informe_sensores_informacion($clase_sensor);
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $elementos_informe = dame_elementos_informe_sensores_analisis_horario();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $elementos_informe = dame_elementos_informe_sensores_analisis_diario();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $elementos_informe = dame_elementos_informe_sensores_analisis_comportamiento();
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $elementos_informe = dame_elementos_informe_sensores_comparacion_periodos();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $elementos_informe = dame_elementos_informe_sensores_comparacion_perfil_horario();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $elementos_informe = dame_elementos_informe_sensores_comparacion_campos_iguales();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $elementos_informe = dame_elementos_informe_sensores_comparacion_campos_diferentes();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $elementos_informe = dame_elementos_informe_sensores_analisis_comparativo();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $elementos_informe = dame_elementos_informe_sensores_valores_generales();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $elementos_informe = dame_elementos_informe_sensores_incrementos_totales();
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $elementos_informe = dame_elementos_informe_sensores_histograma();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $elementos_informe = dame_elementos_informe_sensores_correlacion();
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $elementos_informe = dame_elementos_informe_actuadores_informacion_acciones_enviadas();
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $medicion = $parametros_informe["medicion"];
                $elementos_informe = dame_elementos_informe_smartmeter_consumos_costes_generales($medicion);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $medicion = $parametros_informe["medicion"];
                $elementos_informe = dame_elementos_informe_smartmeter_consumos_costes_totales($medicion);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $medicion = $parametros_informe["medicion"];
                $elementos_informe = dame_elementos_informe_smartmeter_comparacion_periodos($medicion);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $medicion = $parametros_informe["medicion"];
                $elementos_informe = dame_elementos_informe_smartmeter_simulador_tarifas($medicion);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_consumos_costes_tramos_electricidad();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_cortes_tension_electricidad();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_potencia_electricidad();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_energia_reactiva_electricidad();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_caudal_gas();
                break;
            }
            // Elementos de SmartMeter (Compra de energía)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_desvios_compra_energia();
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_desvios_ponderados_compra_energia();
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $medicion = $parametros_informe["medicion"];
                $elementos_informe = dame_elementos_informe_smartmeter_simulador_factura($medicion);
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $elementos_informe = dame_elementos_informe_proyectos_simulador_linea_base();
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $elementos_informe = dame_elementos_informe_proyectos_informacion_proyecto();
                break;
            }
        }

        $lista_elementos_informe = "";
        $numero_elemento = 1;
        foreach ($elementos_informe AS $elemento_informe)
        {
            $cadena_identificador_ordenacion = $numero_elemento;
            if ($numero_elemento < 10)
            {
                $cadena_identificador_ordenacion = "0".$cadena_identificador_ordenacion;
            }
            $lista_elementos_informe .= "<option class='mightOverflow' value='".$elemento_informe."' sort_id='".$cadena_identificador_ordenacion."'";
			if (in_array($elemento_informe, $elementos_informe_seleccionados) == true)
			{
				$lista_elementos_informe .= " selected";
			}
            switch ($tipo_elemento_plantilla_informe)
            {
                // Elementos de sensores (Eventos)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_activaciones_eventos($elemento_informe);
                    break;
                }
                // Elementos de sensores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_informacion($elemento_informe);
                    break;
                }
                // Elementos de sensores (Análisis)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_analisis_horario($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_analisis_diario($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_analisis_comportamiento($elemento_informe);
                    break;
                }
                // Elementos de sensores (Comparación)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_comparacion_periodos($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_comparacion_perfil_horario($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_comparacion_campos_iguales($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_comparacion_campos_diferentes($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_analisis_comparativo($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_valores_generales($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_incrementos_totales($elemento_informe);
                    break;
                }
                // Elementos de sensores (Estadística)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_histograma($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_sensores_correlacion($elemento_informe);
                    break;
                }
                // Elementos de actuadores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_actuadores_informacion_acciones_enviadas($elemento_informe);
                    break;
                }
                // Elementos de SmartMeter (Consumos y costes)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_consumos_costes_generales($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_consumos_costes_totales($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_comparacion_periodos($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_simulador_tarifas($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_consumos_costes_tramos_electricidad($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_cortes_tension_electricidad($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_excesos_potencia_electricidad($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_excesos_energia_reactiva_electricidad($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_excesos_caudal_gas($elemento_informe);
                    break;
                }
                // Elementos de SmartMeter (Compra de energía)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_desvios_compra_energia($elemento_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_desvios_ponderados_compra_energia($elemento_informe);
                    break;
                }
                // Elementos de SmartMeter (Facturas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $medicion = $parametros_informe["medicion"];
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_smartmeter_simulador_factura($medicion, $elemento_informe);
                    break;
                }
                // Elementos de proyectos (Líneas base)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_proyectos_simulador_linea_base($elemento_informe);
                    break;
                }
                // Elementos de proyectos (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $descripcion_elemento_informe = dame_descripcion_elemento_informe_proyectos_informacion_proyecto($elemento_informe);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de elemento de plantilla de informe desconocido: '".$tipo_elemento_plantilla_informe."'");
                }
            }
			$lista_elementos_informe .= ">".htmlspecialchars($descripcion_elemento_informe, ENT_QUOTES)."</option>";
            $numero_elemento += 1;
        }
        return ($lista_elementos_informe);
    }


    // Devuelve la lista de tipos de selección de un elemento de plantilla de informe
    function dame_lista_tipos_seleccion_elemento_plantilla_informe($tipo_seleccion_seleccionado)
    {
        $tipos_seleccion_elemento = ElementoPlantillaInforme::dame_tipos_seleccion_elemento();
        foreach ($tipos_seleccion_elemento as $tipo_seleccion_elemento)
        {
            $lista .= "<option value='".$tipo_seleccion_elemento."'";
            if ($tipo_seleccion_elemento == $tipo_seleccion_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".ElementoPlantillaInforme::dame_descripcion_tipo_seleccion_elemento($tipo_seleccion_elemento)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de modos de visibilidad de un elemento de plantilla de informe
    function dame_lista_modos_visibilidad_elemento_plantilla_informe($modo_visibilidad_seleccionado)
    {
        $modos_visibilidad_elemento = ElementoPlantillaInforme::dame_modos_visibilidad_elemento();
        foreach ($modos_visibilidad_elemento as $modo_visibilidad_elemento)
        {
            $lista .= "<option value='".$modo_visibilidad_elemento."'";
            if ($modo_visibilidad_elemento == $modo_visibilidad_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".ElementoPlantillaInforme::dame_descripcion_modo_visibilidad_elemento($modo_visibilidad_elemento)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista con los periodos de tiempo para los elementos de información de proyecto
    function dame_lista_periodos_tiempo_elemento_plantilla_informe($tipo_elemento_plantilla_informe, $periodo_seleccionado)
    {
        $lista_periodos_tiempo = "";
        switch ($tipo_elemento_plantilla_informe)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
                break;
            }
            default:
            {
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_DIA), PERIODO_TIEMPO_DIA, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_SEMANA), PERIODO_TIEMPO_SEMANA, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_MES), PERIODO_TIEMPO_MES, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_ANYO), PERIODO_TIEMPO_ANYO, $periodo_seleccionado);
                $lista_periodos_tiempo .= dame_opcion_valor_lista_simple(dame_descripcion_periodo_tiempo(PERIODO_TIEMPO_FECHA_INICIO), PERIODO_TIEMPO_FECHA_INICIO, $periodo_seleccionado);
                break;
            }
        }
        return ($lista_periodos_tiempo);
    }


    //
    // Funciones de listas de parámetros de un elemento de plantilla de informe
    //


    // Devuelve la lista de sensores de un elemento de plantilla de informe
    function dame_lista_sensores_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_sensor,
        $clase_sensor,
        $ids_sensores_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_seleccion_sensor)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_sensores = dame_lista_sensores($clase_sensor, $ids_sensores_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_sensores = dame_lista_sensores_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $clase_sensor,
                    $ids_sensores_seleccionados,
                    $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de sensor desconocido: '".$tipo_seleccion_sensor."'");
            }
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores de los parámetros de un elemento de plantilla de informe
    function dame_lista_sensores_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $clase_sensor,
        $ids_parametros_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_parametros .= "
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_sensor)."')";
        }
        $consulta_parametros .= "
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_sensores = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_NODOS_NINGUNO:
            {
                $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_NODOS_TODOS:
            {
                $lista_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
                break;
            }
        }
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_parametro['id']."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores de un elemento de plantilla de informe (de múltiples clases)
    function dame_lista_sensores_clases_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_sensor,
        $clases_sensor,
        $ids_sensores_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_seleccion_sensor)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_sensores = dame_lista_sensores_clases($clases_sensor, $ids_sensores_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_sensores = dame_lista_sensores_clases_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $clases_sensor,
                    $ids_sensores_seleccionados,
                    $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de sensor desconocido: '".$tipo_seleccion_sensor."'");
            }
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de sensores de los parámetros de un elemento de plantilla de informe (de múltiples clases)
    function dame_lista_sensores_clases_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $clases_sensor,
        $ids_parametros_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR."')
                AND (";
        $clases_sensor = array_unique($clases_sensor);
        $numero_clase_sensor = 1;
        foreach ($clases_sensor as $clase_sensor)
        {
            if ($numero_clase_sensor > 1)
            {
                $consulta_parametros .= " OR ";
            }
            $consulta_parametros .= "(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_sensor)."')";
            $numero_clase_sensor += 1;
        }
        $consulta_parametros .= ")";
        $consulta_parametros .= "
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_sensores = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_NODOS_NINGUNO:
            {
                $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_NODOS_TODOS:
            {
                $lista_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
                break;
            }
        }
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_parametro['id']."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    // Devuelve la lista de grupos de sensores de un elemento de plantilla de informe
    function dame_lista_grupos_sensores_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_grupo_sensores,
        $clase_sensor,
        $ids_grupos_sensores_seleccionados,
        $opciones_extra)
    {
        if ($clase_sensor == CLASE_TODAS)
        {
            $clase_sensor = CLASE_NINGUNA;
        }
        switch ($tipo_seleccion_grupo_sensores)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_grupos_sensores = dame_lista_grupos_sensores($clase_sensor, $ids_grupos_sensores_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_grupos_sensores = dame_lista_grupos_sensores_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $clase_sensor,
                    $ids_grupos_sensores_seleccionados,
                    $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de grupo de sensores desconocido: '".$tipo_seleccion_grupo_sensores."'");
            }
        }
        return ($lista_grupos_sensores);
    }


    // Devuelve la lista de grupos de sensores de los parámetros de un elemento de plantilla de informe
    function dame_lista_grupos_sensores_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $clase_sensor,
        $ids_parametros_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES."')";
        if ($clase_sensor != CLASE_TODAS)
        {
            $consulta_parametros .= "
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES_CLASE_SENSOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_sensor)."')";
        }
        $consulta_parametros .= "
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_grupos_sensores = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_NODOS_NINGUNO:
            {
                $lista_grupos_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
                break;
            }
            case OPCIONES_EXTRA_LISTA_NODOS_TODOS:
            {
                $lista_grupos_sensores .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
                break;
            }
        }
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_grupos_sensores .= "<option value='".$fila_parametro['id']."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_grupos_sensores .= " selected";
			}
			$lista_grupos_sensores .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos_sensores);
    }


    // Devuelve la lista de actuadores de un elemento de plantilla de informe
    function dame_lista_actuadores_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_actuador,
        $clase_actuador,
        $ids_actuadores_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_seleccion_actuador)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_actuadores = dame_lista_actuadores($clase_actuador, $ids_actuadores_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_actuadores = dame_lista_actuadores_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $clase_actuador,
                    $ids_actuadores_seleccionados,
                    $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de actuador desconocido: '".$tipo_seleccion_actuador."'");
            }
        }
        return ($lista_actuadores);
    }


    // Devuelve la lista de actuadores de los parámetros de un elemento de plantilla de informe
    function dame_lista_actuadores_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $clase_actuador,
        $ids_parametros_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_parametros .= "
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR_CLASE_ACTUADOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_actuador)."')";
        }
        $consulta_parametros .= "
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_actuadores = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_NODOS_NINGUNO:
            {
                $lista_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
                break;
            }
        }
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_actuadores .= "<option value='".$fila_parametro['id']."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_actuadores .= " selected";
			}
			$lista_actuadores .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_actuadores);
    }


    // Devuelve la lista de grupos de actuadores de un elemento de plantilla de informe
    function dame_lista_grupos_actuadores_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_grupo_actuadores,
        $clase_actuador,
        $ids_grupos_actuadores_seleccionados,
        $opciones_extra)
    {
        switch ($tipo_seleccion_grupo_actuadores)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_grupos_actuadores = dame_lista_grupos_actuadores($clase_actuador, $ids_grupos_actuadores_seleccionados, $opciones_extra);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_grupos_actuadores = dame_lista_grupos_actuadores_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $clase_actuador,
                    $ids_grupos_actuadores_seleccionados,
                    $opciones_extra);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de grupo de actuadores desconocido: '".$tipo_seleccion_grupo_actuadores."'");
            }
        }
        return ($lista_grupos_actuadores);
    }


    // Devuelve la lista de grupos de actuadores de los parámetros de un elemento de plantilla de informe
    function dame_lista_grupos_actuadores_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $clase_actuador,
        $ids_parametros_seleccionados,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES."')";
        if ($clase_actuador != CLASE_TODAS)
        {
            $consulta_parametros .= "
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES_CLASE_ACTUADOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_actuador)."')";
        }
        $consulta_parametros .= "
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_grupos_actuadores = "";
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_NODOS_NINGUNO:
            {
                $lista_grupos_actuadores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
                break;
            }
        }
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_grupos_actuadores .= "<option value='".$fila_parametro['id']."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_grupos_actuadores .= " selected";
			}
			$lista_grupos_actuadores .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_grupos_actuadores);
    }


    // Devuelve la lista de líneas base de un elemento de plantilla de informe
    function dame_lista_lineas_base_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_linea_base,
        $id_linea_base_seleccionada)
    {
        switch ($tipo_seleccion_linea_base)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_actuadores = dame_lista_lineas_base($id_linea_base_seleccionada);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_actuadores = dame_lista_lineas_base_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $id_linea_base_seleccionada);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de línea base desconocido: '".$tipo_seleccion_linea_base."'");
            }
        }
        return ($lista_actuadores);
    }


    // Devuelve la lista de líneas base de los parámetros de un elemento de plantilla de informe
    function dame_lista_lineas_base_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $id_linea_base_seleccionada)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE."')
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_lineas_base = "";
        $lista_lineas_base .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_lineas_base .= "<option value='".$fila_parametro['id']."'";
			if ($fila_parametro['id'] == $id_linea_base_seleccionada)
			{
				$lista_lineas_base .= " selected";
			}
			$lista_lineas_base .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_lineas_base);
    }


    // Devuelve la lista de proyectos de un elemento de plantilla de informe
    function dame_lista_proyectos_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_proyecto,
        $id_proyecto_seleccionado)
    {
        switch ($tipo_seleccion_proyecto)
        {
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
            {
                $lista_proyectos = dame_lista_proyectos($id_proyecto_seleccionado);
                break;
            }
            case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
            {
                $lista_proyectos = dame_lista_proyectos_parametros_elemento_plantilla_informe(
                    $id_plantilla_informe,
                    $id_proyecto_seleccionado);
                break;
            }
            default:
            {
                throw new Exception("Tipo de selección de actuador desconocido: '".$tipo_seleccion_proyecto."'");
            }
        }
        return ($lista_proyectos);
    }


    // Devuelve la lista de proyectos de los parámetros de un elemento de plantilla de informe
    function dame_lista_proyectos_parametros_elemento_plantilla_informe(
        $id_plantilla_informe,
        $id_proyecto_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                AND (tipo = '".TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO."')
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_proyectos = "";
        $lista_proyectos .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $lista_proyectos .= "<option value='".$fila_parametro['id']."'";
			if ($fila_parametro['id'] == $id_proyecto_seleccionado)
			{
				$lista_proyectos .= " selected";
			}
			$lista_proyectos .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_proyectos);
    }


    // Elimina los parámetros de los parámetros de tipo del elemento de la plantilla de informe
    function elimina_parametros_elemento_plantilla_informe($id_plantilla_informe, $id_elemento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $tipo_plantilla_informe = $fila_plantilla_informe["tipo"];

        $fila_elemento = dame_fila_elemento_plantilla_informe($id_elemento);
        $tipo_elemento = $fila_elemento["tipo"];
        $cadena_parametros_tipo_elemento = $fila_elemento["parametros_tipo"];
        $parametros_tipo_elemento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_elemento);

        // Comprobación de parámetro utilizado en alguna selección configurable de elemento
        switch ($tipo_elemento)
        {
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES] = "";
                }
                $tipo_seleccion_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES];
                if ($tipo_seleccion_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES] = "";
                }
                $tipo_seleccion_grupos_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES];
                if ($tipo_seleccion_grupos_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES] = "";
                }
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $tipo_seleccion_origen_evento = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES] = "";
                }
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $tipo_seleccion_sensor_principal = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                if ($tipo_seleccion_sensor_principal == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL] = ID_NINGUNO;
                }
                $tipo_seleccion_sensores_secundarios = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                if ($tipo_seleccion_sensores_secundarios == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS] = "";
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $tipos_seleccion_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES]);
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES]);
                for ($i = 0; $i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; $i++)
                {
                    $tipo_seleccion_sensor = $tipos_seleccion_sensores[$i];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                        {
                            $tipos_seleccion_sensores[$i] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                        }
                        $ids_sensores[$i] = ID_NINGUNO;
                    }
                }
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES] = implode(SEPARADOR_PARAMETROS_SIMPLES, $tipos_seleccion_sensores);
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES] = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores);
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $tipo_seleccion_sensores_agregados = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                if ($tipo_seleccion_sensores_agregados == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS] = "";
                }
                $tipo_seleccion_sensor_destacado = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                if ($tipo_seleccion_sensor_destacado == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES] = "";
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES] = "";
                }
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $tipos_seleccion_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES]);
                $ids_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES]);
                for ($i = 0; $i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; $i++)
                {
                    $tipo_seleccion_sensor_independiente = $tipos_seleccion_sensores_independientes[$i];
                    if ($tipo_seleccion_sensor_independiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                        {
                            $tipos_seleccion_sensores_independientes[$i] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                        }
                        $ids_sensores_independientes[$i] = ID_NINGUNO;
                    }
                }
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES] = implode(SEPARADOR_PARAMETROS_SIMPLES, $tipos_seleccion_sensores_independientes);
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES] = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores_independientes);
                $tipo_seleccion_sensor_dependiente = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                if ($tipo_seleccion_sensor_dependiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $tipo_seleccion_destino_accion = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                if ($tipo_seleccion_destino_accion == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION] = ID_NINGUNO;
                }
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES] = "";
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES] = "";
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR] = ID_NINGUNO;
                }
                $tipos_seleccion_sensores_reparto_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES]);
                if ($tipos_seleccion_sensores_reparto_costes == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES] = "";
                }
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de Proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $tipo_seleccion_linea_base = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TIPO_SELECCION_LINEA_BASE];
                if ($tipo_seleccion_linea_base == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TIPO_SELECCION_LINEA_BASE] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de Proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $tipo_seleccion_proyecto = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_TIPO_SELECCION_PROYECTO];
                if ($tipo_seleccion_proyecto == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                    {
                        $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_TIPO_SELECCION_PROYECTO] = TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO;
                    }
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO] = ID_NINGUNO;
                }
                break;
            }
        }

        $cadena_parametros_tipo_elemento_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_elemento);
        $cadena_ids_parametros_requeridos_elemento_modificada = "";
        $operacion_modificacion = "
            UPDATE elementos_plantillas_informes
            SET
                parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_elemento_modificada)."',
                parametros_requeridos = '".$bd_red->_($cadena_ids_parametros_requeridos_elemento_modificada)."'
            WHERE
                id = '".$bd_red->_($id_elemento)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }
?>
