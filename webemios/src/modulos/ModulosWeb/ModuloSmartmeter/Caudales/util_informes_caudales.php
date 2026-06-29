<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/Espanya/util_informes_caudales_Espanya.php');


    //
    // Funciones de información de caudales
    //


    // Devuelve la información de coste y caudal diario óptimo de un sensor y una tarifa de gas
    function dame_coste_caudal_diario_optimo_sensor_tarifa_gas($parametros)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_coste_caudal_diario_optimo_sensor_tarifa_gas_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de coste y caudal diario óptimo de datos de fichero y una tarifa de gas
    function dame_coste_caudal_diario_optimo_fichero_tarifa_gas($parametros, $ficheros)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_coste_caudal_diario_optimo_fichero_tarifa_gas_Espanya($parametros, $ficheros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de coste y caudal diario seleccionado de un sensor y una tarifa de gas
    function dame_coste_caudal_diario_seleccionado_sensor_tarifa_gas($parametros)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_coste_caudal_diario_seleccionado_sensor_tarifa_gas_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de coste y caudal diario seleccionado de datos de fichero y una tarifa de gas
    function dame_coste_caudal_diario_seleccionado_fichero_tarifa_gas($parametros, $ficheros)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_coste_caudal_diario_seleccionado_fichero_tarifa_gas_Espanya($parametros, $ficheros);
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
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_optimizador_caudales_automatico($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_optimizador_caudales_automatico_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_optimizador_caudales_manual($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_optimizador_caudales_manual_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_caudales_automatico($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_simulador_caudales_automatico_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_caudales_manual($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_simulador_caudales_manual_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($html_informe);
    }
?>
