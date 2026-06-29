<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/util_compra_energia_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/util_consumos_costes_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/util_informes_personalizados_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    //
    // Funciones de información de sensores
    //


    // Devuelve las cadenas del último coste calculado del sensor
    function dame_cadenas_ultimo_coste_calculado_sensor($nombre_sensor, $granularidad)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Consulta de último coste calculado
        switch ($granularidad)
        {
            case GRANULARIDAD_HORARIA:
            {
                $tabla_datos_energia_activa = TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS;
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $tabla_datos_energia_activa = TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_CUARTOSHORA;
                break;
            }
        }
        $consulta = "
            SELECT
                hora,
                coste
            FROM ".$tabla_datos_energia_activa."
            WHERE
                (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')
                AND (coste IS NOT NULL)
            ORDER BY hora DESC
            LIMIT 1";
        $res = $bd_datos->ejecuta_consulta($consulta);
        if ($res == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta."'");
		}
        if ($res->dame_numero_filas() == 0)
        {
            return (NULL);
        }
        else
        {
            $fila = $res->dame_siguiente_fila();
            $hora = $fila["hora"];
            $coste = $fila["coste"];

            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_coste_utc = convierte_formato_fecha($hora, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_coste_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_coste_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_coste = formatea_numero($coste, 2, false)." ".$unidad_medida_coste;

            return (array(
                "cadena_hora_coste_local" => $cadena_hora_coste_local,
                "cadena_coste" => $cadena_coste
            ));
        }
    }


    //
    // Funciones de plantillas de informes
    //


    // Devuelve el identificador de ratio del elemento de la plantilla de informe especificado
    function dame_id_ratio_elemento_plantilla_informe_smartmeter($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recupera el índice del identificador del ratio de los parámetros de tipo
        $indice_parametros_tipo_id_ratio = NULL;
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_RATIO;
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recupera el identificador de ratio de los parámetros de tipo
        if ($indice_parametros_tipo_id_ratio !== NULL)
        {
            $id_ratio = $parametros_tipo[$indice_parametros_tipo_id_ratio];
        }
        else
        {
            $id_ratio = ID_NINGUNO;
        }
        return ($id_ratio);
    }


    // Elimina el identificador de ratio del elemento de plantillas de informe (se establece a ninguno)
    function elimina_id_ratio_elemento_plantilla_informe_smartmeter(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $id_ratio_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            default:
            {
                break;
            }
        }

        // Se modifican los parámetros de tipo (si es necesario)
        $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
        if ($cadena_parametros_tipo_modificada != $cadena_parametros_tipo)
        {
            $operacion_modificacion_elementos_plantillas_informes = "
                UPDATE elementos_plantillas_informes
                SET
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada) ."'
                WHERE
                    id = '".$bd_red->_($id_elemento)."'";
            $res_modificacion_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_modificacion_elementos_plantillas_informes);
            if ($res_modificacion_elementos_plantillas_informes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_elementos_plantillas_informes."'");
            }
        }
    }


    // Devuelve los identificadores de sensores del elemento de la plantilla de informe especificado
    function dame_ids_sensores_elemento_plantilla_informe_smartmeter($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los sensores de los parámetros de tipo (con el tipo de selección de sensor fija)
        $ids_sensores_elemento = array();
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES]);
                    $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES]);
                    $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                $tipo_seleccion_sensores_reparto_costes = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES];
                if ($tipo_seleccion_sensores_reparto_costes == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores_reparto_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES]);
                    $ids_sensores_elemento = array_merge($ids_sensores_reparto_costes, $ids_sensores_elemento);
                }
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_sensores_elemento);
    }


    // Elimina los identificadores de sensores del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_sensores_elemento_plantilla_informe_smartmeter(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_sensores_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES];
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                    $ids_sensores_modificados = array();
                    for ($i = 0; $i < count($ids_sensores); $i++)
                    {
                        $id_sensor = $ids_sensores[$i];
                        if (in_array($id_sensor, $ids_sensores_eliminar) == false)
                        {
                            array_push($ids_sensores_modificados, $id_sensor);
                        }
                    }
                    $cadena_ids_sensores_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES];
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                    $ids_sensores_modificados = array();
                    for ($i = 0; $i < count($ids_sensores); $i++)
                    {
                        $id_sensor = $ids_sensores[$i];
                        if (in_array($id_sensor, $ids_sensores_eliminar) == false)
                        {
                            array_push($ids_sensores_modificados, $id_sensor);
                        }
                    }
                    $cadena_ids_sensores_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                $tipo_seleccion_sensores_reparto_costes = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES];
                if ($tipo_seleccion_sensores_reparto_costes == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores_reparto_costes = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES];
                    $ids_sensores_reparto_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_reparto_costes);
                    $ids_sensores_reparto_costes_modificados = array();
                    for ($i = 0; $i < count($ids_sensores_reparto_costes); $i++)
                    {
                        $id_sensor_reparto_costes = $ids_sensores_reparto_costes[$i];
                        if (in_array($id_sensor_reparto_costes, $ids_sensores_eliminar) == false)
                        {
                            array_push($ids_sensores_reparto_costes_modificados, $id_sensor_reparto_costes);
                        }
                    }
                    $cadena_ids_sensores_reparto_costes_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores_reparto_costes_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES] = $cadena_ids_sensores_reparto_costes_modificada;
                }
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }

        // Se modifican los parámetros de tipo (si es necesario)
        $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
        if ($cadena_parametros_tipo_modificada != $cadena_parametros_tipo)
        {
            $operacion_modificacion_elementos_plantillas_informes = "
                UPDATE elementos_plantillas_informes
                SET
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada) ."'
                WHERE
                    id = '".$bd_red->_($id_elemento)."'";
            $res_modificacion_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_modificacion_elementos_plantillas_informes);
            if ($res_modificacion_elementos_plantillas_informes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_elementos_plantillas_informes."'");
            }
        }
    }


    // Devuelve los identificadores de tarifas del elemento de la plantilla de informe especificado
    function dame_ids_tarifas_elemento_plantilla_informe_smartmeter($medicion, $tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de las tarifas de los parámetros de tipo
        $ids_tarifas_elemento = array();
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $medicion_elemento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_MEDICION];
                if ($medicion_elemento == $medicion)
                {
                    $ids_tarifas_elemento = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_IDS_TARIFAS]);
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_tarifas_elemento);
    }


    // Elimina los identificadores de tarifas del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_tarifas_elemento_plantilla_informe_smartmeter(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_tarifas_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $cadena_ids_tarifas = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_IDS_TARIFAS];
                $ids_tarifas = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_tarifas);
                $ids_tarifas_modificados = array();
                for ($i = 0; $i < count($ids_tarifas); $i++)
                {
                    $id_tarifa = $ids_tarifas[$i];
                    if (in_array($id_tarifa, $ids_tarifas_eliminar) == false)
                    {
                        array_push($ids_tarifas_modificados, $id_tarifa);
                    }
                }
                $cadena_ids_tarifas_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_tarifas_modificados);
                $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_IDS_TARIFAS] = $cadena_ids_tarifas_modificada;
                break;
            }
            default:
            {
                break;
            }
        }

        // Se modifican los parámetros de tipo (si es necesario)
        $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo);
        if ($cadena_parametros_tipo_modificada != $cadena_parametros_tipo)
        {
            $operacion_modificacion_elementos_plantillas_informes = "
                UPDATE elementos_plantillas_informes
                SET
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada) ."'
                WHERE
                    id = '".$bd_red->_($id_elemento)."'";
            $res_modificacion_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_modificacion_elementos_plantillas_informes);
            if ($res_modificacion_elementos_plantillas_informes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_elementos_plantillas_informes."'");
            }
        }
    }


    //
    // Funciones de informes automáticos
    //


    // Devuelve el identificador de ratio del informe automático especificado
    function dame_id_ratio_informe_automatico_smartmeter($tipo, $cadena_parametros_tipo)
    {
        // Se recupera el índice de identificador de ratio de los parámetros de tipo
        $indice_parametros_tipo_id_ratio = array();
        switch ($tipo)
        {
            // Consumos y costes
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_RATIO;
                break;
            }
            // Informes personalizados
            case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_RATIO;
                break;
            }
            default:
            {
                $indice_parametros_tipo_id_ratio = NULL;
                break;
            }
        }

        // Se recupera el id de ratio parámetros de tipo
        $id_ratio = NULL;
        if ($indice_parametros_tipo_id_ratio !== NULL)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            $id_ratio = $parametros_tipo[$indice_parametros_tipo_id_ratio];
        }
        return ($id_ratio);
    }


    // Devuelve los identificadores de los sensores del informe automático especificado
    function dame_ids_sensores_informe_automatico_smartmeter($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        $indices_parametros_tipo_ids_sensores = array();
        switch ($tipo)
        {
            // Consumos y costes
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES);
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES);
                break;
            }
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_POTENCIA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_CORTES_TENSION_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_EXCESOS_CAUDAL_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR);
                break;
            }
            // Compra de energía
            case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_COMPRA_ENERGIA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR);
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_ID_SENSOR_HIJO);
                break;
            }
            // Facturas
            case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR);
                break;
            }
            // Informes personalizados
            case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_SENSOR);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de sensores de los parámetros de tipo
        $ids_sensores = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_sensores as $indice_parametros_tipo_ids_sensores)
        {
            $ids_sensores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_sensores]);
            $ids_sensores = array_merge($ids_sensores, $ids_sensores_parametro_tipo);
        }
        return ($ids_sensores);
    }


    // Devuelve los identificadores de las tarifas del informe automático especificado
    function dame_ids_tarifas_informe_automatico_smartmeter($medicion, $tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los índices de los identificadores de las tarifas de los parámetros de tipo
        $indices_parametros_tipo_ids_tarifas = array();
        switch ($tipo)
        {
            // Facturas
            case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $medicion_informe_automatico = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_MEDICION];
                if ($medicion_informe_automatico == $medicion)
                {
                    array_push($indices_parametros_tipo_ids_tarifas, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_TARIFA);
                }
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de tarifas de los parámetros de tipo
        $ids_tarifas = array();
        foreach ($indices_parametros_tipo_ids_tarifas as $indice_parametros_tipo_ids_tarifas)
        {
            $ids_tarifas_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_tarifas]);
            $ids_tarifas = array_merge($ids_tarifas, $ids_tarifas_parametro_tipo);
        }
        return ($ids_tarifas);
    }


    //
    // Funciones de información de consumo
    //


    // Devuelve el campo de consumo según la clase de sensor
    function dame_campo_consumo_clase_sensor($clase_sensor)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $campo = CAMPO_INCREMENTO;
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $campo = CAMPO_CONSUMO;
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $campo = CAMPO_INCREMENTO;
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor sin consumo: '".$clase_sensor."'");
            }
        }
        return ($campo);
    }


    // Devuelve la unidad de medida de consumo según la clase de sensor
    function dame_unidad_medida_consumo_clase_sensor($clase_sensor)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $unidad = "kWh";
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $unidad = "kVArh";
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $unidad = "kWh";
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $unidad = "m3";
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor sin consumo: '".$clase_sensor."'");
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($unidad));
    }


    //
    // Funciones de características de tarifas
    //


    // Devuelve las características de las tarifas del país de la medición especificada
    function dame_caracteristicas_tarifas_pais_medicion($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $caracteristicas_tarifas = dame_caracteristicas_tarifas_electricas_pais();
                break;
            }
            case MEDICION_GAS:
            {
                $caracteristicas_tarifas = dame_caracteristicas_tarifas_gas_pais();
                break;
            }
            case MEDICION_AGUA:
            {
                $caracteristicas_tarifas = dame_caracteristicas_tarifas_agua_pais();
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($caracteristicas_tarifas);
    }


    // Devuelve las características de las tarifas eléctricas según el país
    function dame_caracteristicas_tarifas_electricas_pais()
    {
        $tarifas = false;
        $tramos = false;
        $autoconsumo = false;
        $potencias = false;
        $energia_reactiva = false;
        $cortes_tension = false;
        $compra_energia = false;
        $facturas = false;
        $validacion_facturas = false;
        $informe_estudio_general = false;
        $curva_coste = false;
        $conceptos_adicionales_factura_consumo = false;
        $impuesto_conceptos_adicionales_factura = false;

        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"] ?? PAIS_TARIFAS_ELECTRICAS_DEFECTO;
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $tarifas = true;
                $tramos = true;
                $autoconsumo = true;
                $potencias = true;
                $energia_reactiva = true;
                $cortes_tension = true;
                $compra_energia = true;
                $facturas = true;
                $validacion_facturas = true;
                $informe_estudio_general = true;
                $curva_coste = true;
                break;
            }
            case PAIS_PORTUGAL:
            {
                $tarifas = true;
                $tramos = true;
                $autoconsumo = false;
                $potencias = false;
                $energia_reactiva = false;
                $cortes_tension = false;
                $compra_energia = false;
                $facturas = true;
                $validacion_facturas = false;
                $informe_estudio_general = false;
                $curva_coste = true;
                break;
            }
            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas desconocido: '".$pais_tarifas_electricas."'");
            }
        }

        $caracteristicas_tarifas_electricas = array(
            "tarifas" => $tarifas,
            "tramos" => $tramos,
            "autoconsumo" => $autoconsumo,
            "potencias" => $potencias,
            "energia_reactiva" => $energia_reactiva,
            "cortes_tension" => $cortes_tension,
            "compra_energia" => $compra_energia,
            "facturas" => $facturas,
            "validacion_facturas" => $validacion_facturas,
            "informe_estudio_general" => $informe_estudio_general,
            "curva_coste" => $curva_coste,
            "conceptos_adicionales_factura_consumo" => $conceptos_adicionales_factura_consumo,
            "impuesto_conceptos_adicionales_factura" => $impuesto_conceptos_adicionales_factura
        );
        return ($caracteristicas_tarifas_electricas);
    }


    // Devuelve las características de las tarifas de gas según el país
    function dame_caracteristicas_tarifas_gas_pais()
    {
        $tarifas = false;
        $tramos = false;
        $autoconsumo = false;
        $caudales = false;
        $facturas = false;
        $validacion_facturas = false;
        $informe_estudio_general = false;
        $curva_coste = false;
        $conceptos_adicionales_factura_consumo = false;
        $impuesto_conceptos_adicionales_factura = false;

        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"] ?? PAIS_TARIFAS_GAS_DEFECTO;
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $tarifas = true;
                $autoconsumo = true;
                $caudales = true;
                $facturas = true;
                $informe_estudio_general = true;
                $curva_coste = true;
                break;
            }
            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas desconocido: '".$pais_tarifas_gas."'");
            }
        }

        $caracteristicas_tarifas_gas = array(
            "tarifas" => $tarifas,
            "tramos" => $tramos,
            "autoconsumo" => $autoconsumo,
            "caudales" => $caudales,
            "facturas" => $facturas,
            "validacion_facturas" => $validacion_facturas,
            "informe_estudio_general" => $informe_estudio_general,
            "curva_coste" => $curva_coste,
            "conceptos_adicionales_factura_consumo" => $conceptos_adicionales_factura_consumo,
            "impuesto_conceptos_adicionales_factura" => $impuesto_conceptos_adicionales_factura
        );
        return ($caracteristicas_tarifas_gas);
    }


    // Devuelve las características de las tarifas de agua según el país
    function dame_caracteristicas_tarifas_agua_pais()
    {
        $tarifas = false;
        $autoconsumo = false;
        $facturas = false;
        $validacion_facturas = false;
        $informe_estudio_general = false;
        $curva_coste = false;
        $conceptos_adicionales_factura_consumo = false;
        $impuesto_conceptos_adicionales_factura = false;

        // Selección de país
        $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"] ?? PAIS_TARIFAS_AGUA_DEFECTO;
        switch ($pais_tarifas_agua)
        {
            case PAIS_ESPANYA:
            {
                $tarifas = true;
                $autoconsumo = true;
                $facturas = true;
                $informe_estudio_general = true;
                $conceptos_adicionales_factura_consumo = true;
                $impuesto_conceptos_adicionales_factura = true;
                break;
            }
            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de agua desconocido: '".$pais_tarifas_agua."'");
            }
        }

        $caracteristicas_tarifas_agua = array(
            "tarifas" => $tarifas,
            "autoconsumo" => $autoconsumo,
            "facturas" => $facturas,
            "validacion_facturas" => $validacion_facturas,
            "informe_estudio_general" => $informe_estudio_general,
            "curva_coste" => $curva_coste,
            "conceptos_adicionales_factura_consumo" => $conceptos_adicionales_factura_consumo,
            "impuesto_conceptos_adicionales_factura" => $impuesto_conceptos_adicionales_factura
        );
        return ($caracteristicas_tarifas_agua);
    }


    //
    // Funciones de descripciones
    //


    // Devuelve la descripción del tipo de autoconsumo
    function dame_descripcion_tipo_autoconsumo($tipo_autoconsumo)
    {
        switch ($tipo_autoconsumo)
        {
            case TIPO_AUTOCONSUMO_SIN_ACUMULACION:
            {
                $descripcion_tipo_autoconsumo = "Sin acumulación";
                break;
            }
            case TIPO_AUTOCONSUMO_CON_ACUMULACION:
            {
                $descripcion_tipo_autoconsumo = "Con acumulación";
                break;
            }
            default:
            {
                $descripcion_tipo_autoconsumo = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_tipo_autoconsumo));
    }


    // Devuelve la descripción del rango de potencias
    function dame_descripcion_rango_potencias($rango_potencias)
    {
        switch ($rango_potencias)
        {
            case RANGO_POTENCIAS_MINIMO:
            {
                $descripcion_rango_potencias = "Mínimo";
                break;
            }
            case RANGO_POTENCIAS_MEDIO:
            {
                $descripcion_rango_potencias = "Medio";
                break;
            }
            case RANGO_POTENCIAS_MAXIMO:
            {
                $descripcion_rango_potencias = "Máximo";
                break;
            }
            default:
            {
                $descripcion_rango_potencias = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_rango_potencias));
    }


    //
    // Funciones de listas
    //


    // Devuelve la lista de intervalos de valores para los informes de consumos y costes según la clase del sensor
    function dame_lista_intervalos_valores_informes_consumos_costes_clase_sensor($clase_sensor, $intervalo_seleccionado)
    {
        $intervalos_valores = array();
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        if ($caracteristicas_clase_sensor["granularidad_cuartohoraria"] == true)
        {
            array_push($intervalos_valores, array(INTERVALO_VALORES_CUARTOHORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_CUARTOHORA)));
        }
        array_push($intervalos_valores, array(INTERVALO_VALORES_HORA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_HORA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_DIA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_DIA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_SEMANA, dame_descripcion_intervalo_valores(INTERVALO_VALORES_SEMANA)));
        array_push($intervalos_valores, array(INTERVALO_VALORES_MES, dame_descripcion_intervalo_valores(INTERVALO_VALORES_MES)));
        $lista_intervalo_valores = dame_lista_valores($intervalos_valores, array($intervalo_seleccionado));
        return ($lista_intervalo_valores);
    }


    // Devuelve la lista de granularidades para el informe de excesos de potencia
    function dame_lista_granularidades_informe_excesos_potencia($granularidad_seleccionada)
    {
        $granularidades = array();
        array_push($granularidades, array(GRANULARIDAD_CUARTOHORARIA, dame_descripcion_granularidad(GRANULARIDAD_CUARTOHORARIA)));
        array_push($granularidades, array(GRANULARIDAD_HORARIA, dame_descripcion_granularidad(GRANULARIDAD_HORARIA)));
        $lista_granularidades = dame_lista_valores($granularidades, array($granularidad_seleccionada));
        return ($lista_granularidades);
    }
?>
