<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    //
    // Funciones de mediciones
    //


    // Devuelve la lista de mediciones
    function dame_lista_mediciones($medicion_seleccionada, $opciones_extra)
    {
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_MEDICIONES_TODAS:
            {
                $mediciones = array(
                    MEDICION_NINGUNA,
                    MEDICION_ELECTRICIDAD,
                    MEDICION_GAS,
                    MEDICION_AGUA);
                break;
            }
            case OPCIONES_EXTRA_LISTA_MEDICIONES_RED_ACTUAL:
            {
                $mediciones = array(MEDICION_NINGUNA);
                if ($_SESSION["pais_tarifas_electricas"] != PAIS_NINGUNO)
                {
                    array_push($mediciones, MEDICION_ELECTRICIDAD);
                }
                if ($_SESSION["pais_tarifas_gas"] != PAIS_NINGUNO)
                {
                    array_push($mediciones, MEDICION_GAS);
                }
                if ($_SESSION["pais_tarifas_agua"] != PAIS_NINGUNO)
                {
                    array_push($mediciones, MEDICION_AGUA);
                }
                break;
            }
            case OPCIONES_EXTRA_LISTA_MEDICIONES_CURVA_COSTE_RED_ACTUAL:
            {
                // Características de tarifas
                $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
                $caracteristicas_tarifas_gas = dame_caracteristicas_tarifas_gas_pais();
                $caracteristicas_tarifas_agua = dame_caracteristicas_tarifas_agua_pais();

                $mediciones = array(MEDICION_NINGUNA);
                if (($_SESSION["pais_tarifas_electricas"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_electricas["curva_coste"] == true))
                {
                    array_push($mediciones, MEDICION_ELECTRICIDAD);
                }
                if (($_SESSION["pais_tarifas_gas"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_gas["curva_coste"] == true))
                {
                    array_push($mediciones, MEDICION_GAS);
                }
                if (($_SESSION["pais_tarifas_agua"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_agua["curva_coste"] == true))
                {
                    array_push($mediciones, MEDICION_AGUA);
                }
                break;
            }
            case OPCIONES_EXTRA_LISTA_MEDICIONES_FACTURAS_RED_ACTUAL:
            {
                // Características de tarifas
                $caracteristicas_tarifas_electricas = dame_caracteristicas_tarifas_electricas_pais();
                $caracteristicas_tarifas_gas = dame_caracteristicas_tarifas_gas_pais();
                $caracteristicas_tarifas_agua = dame_caracteristicas_tarifas_agua_pais();

                $mediciones = array(MEDICION_NINGUNA);
                if (($_SESSION["pais_tarifas_electricas"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_electricas["facturas"] == true))
                {
                    array_push($mediciones, MEDICION_ELECTRICIDAD);
                }
                if (($_SESSION["pais_tarifas_gas"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_gas["facturas"] == true))
                {
                    array_push($mediciones, MEDICION_GAS);
                }
                if (($_SESSION["pais_tarifas_agua"] != PAIS_NINGUNO) && ($caracteristicas_tarifas_agua["facturas"] == true))
                {
                    array_push($mediciones, MEDICION_AGUA);
                }
                break;
            }
        }

        $lista_mediciones = "";
        foreach ($mediciones as $medicion)
        {
            $lista_mediciones .= dame_opcion_valor_lista_simple(dame_descripcion_medicion($medicion), $medicion, $medicion_seleccionada);
        }
        return ($lista_mediciones);
    }


    // Devuelve la descripción de una medición
    function dame_descripcion_medicion($descripcion)
    {
        switch ($descripcion)
        {
            case MEDICION_NINGUNA:
            {
                $descripcion_medicion = "Ninguna";
                break;
            }
            case MEDICION_ELECTRICIDAD:
            {
                $descripcion_medicion = "Electricidad";
                break;
            }
            case MEDICION_GAS:
            {
                $descripcion_medicion = "Gas";
                break;
            }
            case MEDICION_AGUA:
            {
                $descripcion_medicion = "Agua";
                break;
            }
            default:
            {
                $descripcion_medicion = "Desconocida";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_medicion));
    }


    // Devuelve el país de tarifas de la medición correspondiente
    function dame_pais_tarifas_medicion($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas = $_SESSION["pais_tarifas_electricas"];
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas = $_SESSION["pais_tarifas_gas"];
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas = $_SESSION["pais_tarifas_agua"];
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($pais_tarifas);
    }


    // Devuelve la medición por defecto
    function dame_medicion_defecto()
    {
        $medicion_defecto = $_SESSION["medicion_defecto"];
        return ($medicion_defecto);
    }


    // Devuelve la clase de sensor correspondiente a la medición
    function dame_clase_sensor_medicion($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
                break;
            }
            case MEDICION_GAS:
            {
                $clase_sensor = CLASE_SENSOR_GAS;
                break;
            }
            case MEDICION_AGUA:
            {
                $clase_sensor = CLASE_SENSOR_AGUA;
                break;
            }
            case MEDICION_NINGUNA:
            {
                $clase_sensor = CLASE_NINGUNA;
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($clase_sensor);
    }
?>
