<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/Espanya/util_informes_potencias_Espanya.php');


    //
    // Funciones de información de potencias
    //


    // Devuelve la información de costes y potencias óptimas de un sensor y una tarifa eléctrica
    function dame_costes_potencias_optimas_sensor_tarifa_electricidad($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_costes_potencias_optimas_sensor_tarifa_electricidad_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de costes y potencias óptimas de datos de fichero y una tarifa eléctrica
    function dame_costes_potencias_optimas_fichero_tarifa_electricidad($parametros, $ficheros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_costes_potencias_optimas_fichero_tarifa_electricidad_Espanya($parametros, $ficheros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de costes y potencias seleccionadas de un sensor y una tarifa eléctrica
    function dame_costes_potencias_seleccionadas_sensor_tarifa_electricidad($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_costes_potencias_seleccionadas_sensor_tarifa_electricidad_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de costes y potencias seleccionadas de datos de fichero y una tarifa eléctrica
    function dame_costes_potencias_seleccionadas_fichero_tarifa_electricidad($parametros, $ficheros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_costes_potencias_seleccionadas_fichero_tarifa_electricidad_Espanya($parametros, $ficheros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_optimizador_potencias_automatico($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_optimizador_potencias_automatico_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_optimizador_potencias_manual($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_optimizador_potencias_manual_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_potencias_automatico($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_simulador_potencias_automatico_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_simulador_potencias_manual($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_simulador_potencias_manual_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }
?>
