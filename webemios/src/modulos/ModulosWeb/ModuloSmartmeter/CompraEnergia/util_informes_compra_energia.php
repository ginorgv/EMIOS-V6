<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/Espanya/util_informes_compra_energia_Espanya.php');


    //
    // Funciones de información de compra de energía
    //


    // Devuelve la información de previsión de compra de energía de un sensor
    function dame_prevision_compra_energia_sensor($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_prevision_compra_energia_sensor_Espanya($parametros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de desvíos de compra de energía de un sensor
    function dame_desvios_compra_energia_sensor($parametros, $filas_valores_sensor)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_desvios_compra_energia_sensor_Espanya($parametros, $filas_valores_sensor);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    // Devuelve la información de desvíos ponderados de compra de energía de un sensor
    function dame_desvios_ponderados_compra_energia_sensor($parametros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = dame_desvios_ponderados_compra_energia_sensor_Espanya($parametros);
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
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_desvios_compra_energia()
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_desvios_compra_energia_Espanya();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_elementos_informe_smartmeter_desvios_ponderados_compra_energia()
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $elementos_informe = dame_elementos_informe_smartmeter_desvios_ponderados_compra_energia_Espanya();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_desvios_compra_energia($elemento_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_desvios_compra_energia_Espanya($elemento_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($descripcion_elemento);
    }


    function dame_descripcion_elemento_informe_smartmeter_desvios_ponderados_compra_energia($elemento_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $descripcion_elemento = dame_descripcion_elemento_informe_smartmeter_desvios_ponderados_compra_energia_Espanya($elemento_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($descripcion_elemento);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_prevision_compra_energia($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_prevision_compra_energia_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_desvios_compra_energia($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_desvios_compra_energia_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_smartmeter_desvios_ponderados_compra_energia($tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya($tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $html_informe = dame_html_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya(
                    $numero_elemento,
                    $nombre_elemento,
                    $parametros_tipo_elemento,
                    $tipo_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($html_informe);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe,
        &$filas_valores_sensores)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_compra_energia_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe,
                    $filas_valores_sensores);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_desvios_ponderados_compra_energia_Espanya(
                    $numero_elemento,
                    $parametros_tipo_elemento,
                    $parametros_informe);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($datos_elemento);
    }
?>
