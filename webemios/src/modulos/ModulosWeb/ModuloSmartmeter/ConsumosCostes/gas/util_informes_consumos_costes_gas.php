<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/util_informes_consumos_costes_gas_Espanya.php');


    //
    // Funciones de información de consumos y costes (gas)
    //


    // Devuelve información de sobrecaudales (excesos de caudal) de un sensor
    function dame_sobrecaudales_sensor_gas($parametros)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_sobrecaudales_sensor_gas_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_excesos_caudal_gas()
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_excesos_caudal_gas_Espanya();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_excesos_caudal_gas($elemento_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_excesos_caudal_gas_Espanya($elemento_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($descripcion_elemento);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_excesos_caudal_gas($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_excesos_caudal_gas_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_excesos_caudal_gas_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($datos_elemento);
    }
?>
