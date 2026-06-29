<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/Espanya/util_informes_energia_reactiva_Espanya.php');


    //
    // Funciones de información de energía reactiva
    //


    // Devuelve la información de simulación de batería de condensadores de un sensor
    function dame_simulacion_bateria_condensadores_sensor($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_simulacion_bateria_condensadores_sensor_Espanya($parametros);
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


    function dame_html_informe_tipo_smartmeter_simulador_bateria_condensadores($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_simulador_bateria_condensadores_Espanya($tipo_informe);
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
