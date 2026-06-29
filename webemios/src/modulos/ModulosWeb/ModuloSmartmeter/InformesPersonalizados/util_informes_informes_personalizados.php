<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/electricidad/Espanya/util_informes_informes_personalizados_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/gas/Espanya/util_informes_informes_personalizados_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/agua/Espanya/util_informes_informes_personalizados_agua_Espanya.php');


    //
    // Funciones de informes de informes personalizados
    //


    // Devuelve el estudio general de un sensor
    function dame_estudio_general_sensor($parametros)
    {
        // Selección de medición y país
        $medicion = $parametros["medicion"];
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $resultado = dame_estudio_general_sensor_electricidad_Espanya($parametros);
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
                        $resultado = dame_estudio_general_sensor_gas_Espanya($parametros);
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
                        $resultado = dame_estudio_general_sensor_agua_Espanya($parametros);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($resultado);
    }


    //
    // Funciones para los apartados de informes personalizados
    //


    // Crea una lista doble para selección de los apartados del informe de estudio general
    function dame_control_lista_doble_apartados_estudio_general($id_controles, $medicion)
    {
        $idiomas = new Idiomas();

        // Nota: En las listas dobles es necesario el atributo 'name'
        $control_lista_doble_apartados = "<span>".$idiomas->_("Apartados").": "."</span><br/>";
        $control_lista_doble_apartados .= "<div id='select_apartados_no_visible_".$id_controles."' hidden></div>";
        $control_lista_doble_apartados .= "
            <select id='ids_apartados_".$id_controles."'
                name='ids_apartados_".$id_controles."'
                max_selected='".dame_numero_apartados_estudio_general($medicion)."' multiple='multiple'
                class='select100' hidden>";
        $control_lista_doble_apartados .= dame_lista_apartados_estudio_general($medicion);
        $control_lista_doble_apartados .= "
            </select>";

        return ($control_lista_doble_apartados);
    }


    // Crea los controles de texto para los apartados de estudio general
    function dame_controles_textos_estudio_general($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $controles_textos = dame_controles_textos_estudio_general_electricidad_Espanya();
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
                        $controles_textos = dame_controles_textos_estudio_general_gas_Espanya();
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
                        $controles_textos = dame_controles_textos_estudio_general_agua_Espanya();
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($controles_textos);
    }


    // Devuelve la lista de apartados del informe de estudio general
    function dame_lista_apartados_estudio_general($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_apartados = dame_lista_apartados_estudio_general_electricidad_Espanya();
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
                        $lista_apartados = dame_lista_apartados_estudio_general_gas_Espanya();
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
                        $lista_apartados = dame_lista_apartados_estudio_general_agua_Espanya();
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($lista_apartados);
    }


    // Devuelve el número de apartados del estudio general
    function dame_numero_apartados_estudio_general($medicion)
    {
        $lista_apartados = dame_lista_apartados_estudio_general($medicion);
        $numero_apartados = dame_numero_elementos_lista($lista_apartados);
        return ($numero_apartados);
    }


    // Devuelve la descripción del apartado del estudio general
    function dame_descripcion_apartado_estudio_general($medicion, $apartado)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $descripcion_apartado = dame_descripcion_apartado_estudio_general_electricidad_Espanya($apartado);
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
                        $descripcion_apartado = dame_descripcion_apartado_estudio_general_gas_Espanya($apartado);
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
                        $descripcion_apartado = dame_descripcion_apartado_estudio_general_agua_Espanya($apartado);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($descripcion_apartado);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_estudio_general($medicion, $tipo_informe)
    {
        // Selección de medición y de país
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_informe = dame_html_informe_tipo_smartmeter_estudio_general_electricidad_Espanya($tipo_informe);
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
                        $html_informe = dame_html_informe_tipo_smartmeter_estudio_general_gas_Espanya($tipo_informe);
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
                        $html_informe = dame_html_informe_tipo_smartmeter_estudio_general_agua_Espanya($tipo_informe);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($html_informe);
    }
?>
