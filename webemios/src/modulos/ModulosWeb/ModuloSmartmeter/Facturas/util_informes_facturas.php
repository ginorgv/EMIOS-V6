<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/util_informes_facturas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_informes_facturas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/gas/Espanya/util_informes_facturas_gas_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Portugal/util_informes_facturas_electricidad_Portugal.php');


    //
    // Funciones de información de facturas
    //


    // Devuelve la información de simulación de factura de un sensor y tarifa
    function dame_simulacion_factura_sensor_tarifa($parametros)
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
                        $resultado = dame_simulacion_factura_sensor_tarifa_electricidad_Espanya($parametros);
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $resultado = dame_simulacion_factura_sensor_tarifa_electricidad_Portugal($parametros);
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
                        $resultado = dame_simulacion_factura_sensor_tarifa_gas_Espanya($parametros);
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
                        $resultado = dame_simulacion_factura_sensor_tarifa_agua_Espanya($parametros);
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


    // Valida las facturas
    function valida_facturas($parametros, $ficheros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $medicion = $parametros["medicion"];
        $numero_ficheros = $parametros["numero_ficheros_facturas"];
        $tipo_ficheros = $parametros["tipo_ficheros_facturas"];

        // Se copian los ficheros al servidor
        $tipos_ficheros = array();
        $rutas_ficheros_servidor_json = array();
        $error_subir_ficheros = false;
        for ($i = 1; $i <= $numero_ficheros; $i++)
        {
            $nombre_parametro_fichero = "fichero_factura_".$i;
            $ruta_fichero_cliente = $ficheros[$nombre_parametro_fichero]["tmp_name"];
            $directorio_usuario = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$_SESSION["id_usuario"];
            $nombre_fichero = basename($ficheros[$nombre_parametro_fichero]["name"]);
            $ruta_fichero_servidor = $directorio_usuario.'/'.$nombre_fichero;
            $res_copiar_fichero = move_uploaded_file($ruta_fichero_cliente, $ruta_fichero_servidor);
            if ($res_copiar_fichero == false)
            {
                $error_subir_ficheros = true;

                $res = "ERROR";
                $error = $ficheros[$nombre_parametro_fichero]["error"];
                $msg = $idiomas->_("No se han podido subir los ficheros al servidor")." (".$nombre_fichero.")".
                    " (".$idiomas->_("error").": ".$error.")";
                break;
            }

            // Sustitución de '\\' por '*' (para evitar problemas con json)
            array_push($tipos_ficheros, $tipo_ficheros);
            $ruta_fichero_servidor_json = str_replace("\\", "*", $ruta_fichero_servidor);
            array_push($rutas_ficheros_servidor_json, $ruta_fichero_servidor_json);
        }

        // Si los ficheros se han subido correctamente al servidor
        if ($error_subir_ficheros == false)
        {
            if (($tipo_ficheros == NULL) || (count($rutas_ficheros_servidor_json) == 0))
            {
                $res = "ERROR";
                $msg = $idiomas->_("Se ha producido un error al subir los ficheros de facturas y cierres");
            }
            else
            {
                // País de tarifas
                $pais_tarifas = dame_pais_tarifas_medicion($medicion);

                // Parámetros de la función a llamar
                $parametros_funcion_externa =
                    array(
                        "llamante" => "web_emios",
                        "nombre" => NOMBRE_FUNCION_VALIDA_FACTURAS_FICHEROS,
                        "medicion" => $medicion,
                        "pais_tarifas" => $pais_tarifas,
                        "tipos_ficheros" => $tipos_ficheros,
                        "rutas_ficheros" => $rutas_ficheros_servidor_json,
                        "nombres_sensores" => NULL,
                        "id_red" => $_SESSION["id_red"],
                        "id_usuario" => $_SESSION["id_usuario"]
                    );

                // Llamada a función 'externa'
                $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
                $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

                // Si hay ficheros que no se han podido leer se muestran en el mensaje de respuesta
                $info_ficheros_incorrectos = $resultado_funcion_externa["info_ficheros_incorrectos"];
                if (count($info_ficheros_incorrectos) > 0)
                {
                    $hay_ficheros_incorrectos = true;
                    $msg = $idiomas->_("No se han podido validar los siguientes ficheros de facturas").":\n";
                    $info_ficheros_incorrectos = $resultado_funcion_externa["info_ficheros_incorrectos"];
                    foreach ($info_ficheros_incorrectos as $info_fichero_incorrecto)
                    {
                        $msg .= "- ".$info_fichero_incorrecto["nombre_fichero"];
                    }
                }
                else
                {
                    $hay_ficheros_incorrectos = false;
                    $msg = $idiomas->_("Validaciones de facturas y cierres realizadas correctamente");
                }

                $res = "OK";
            }
        }

        // Resultado
        $resultado = array(
            "res" => $res,
            "hay_ficheros_incorrectos" => $hay_ficheros_incorrectos,
            "msg" => $msg);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_simulador_factura($medicion)
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
                        $elementos_informe = dame_elementos_informe_smartmeter_simulador_factura_electricidad_Espanya();
                        break;
                    }

                    /*case PAIS_PORTUGAL:
                    {
                        $elementos_informe = dame_elementos_informe_smartmeter_simulador_factura_electricidad_Portugal();
                        break;
                    }*/

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
                        $elementos_informe = dame_elementos_informe_smartmeter_simulador_factura_gas_Espanya();
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
                        $elementos_informe = dame_elementos_informe_smartmeter_simulador_factura_agua_Espanya();
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
                break;
            }
        }
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_smartmeter_simulador_factura($medicion, $elemento_informe)
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
                        $descripcion_elemento = dame_descripcion_elemento_informe_simulacion_factura_electricidad_Espanya($elemento_informe);
                        break;
                    }

                    /*case PAIS_PORTUGAL:
                    {
                        $descripcion_elemento = dame_descripcion_elemento_informe_simulacion_factura_electricidad_Portugal($elemento_informe);
                        break;
                    }*/

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
                        $descripcion_elemento = dame_descripcion_elemento_informe_simulacion_factura_gas_Espanya($elemento_informe);
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
                        $descripcion_elemento = dame_descripcion_elemento_informe_simulacion_factura_agua_Espanya($elemento_informe);
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
        return ($descripcion_elemento);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_simulador_factura($medicion, $tipo_informe)
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
                        $html_informe = dame_html_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya($tipo_informe);
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $html_informe = dame_html_informe_tipo_smartmeter_simulador_factura_electricidad_Portugal($tipo_informe);
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
                        $html_informe = dame_html_informe_tipo_smartmeter_simulador_factura_gas_Espanya($tipo_informe);
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
                        $html_informe = dame_html_informe_tipo_smartmeter_simulador_factura_agua_Espanya($tipo_informe);
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


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de medición y de país
        $medicion = $parametros_tipo_elemento["medicion"];
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }

                    /*case PAIS_PORTUGAL:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Portugal(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }*/

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_gas_Espanya(
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
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_agua_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
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
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de medición y de país
        $medicion = $parametros_tipo_elemento["medicion"];
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }

                    /*case PAIS_PORTUGAL:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Portugal(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }*/

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
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_gas_Espanya(
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
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_agua_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
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
        return ($datos_elemento);
    }


    //
    // Funciones de reparto de costes de facturas
    //


    function dame_informacion_reparto_costes_simulacion_factura($parametros_simulacion_factura_sensor_tarifa, $coste_total)
    {
        $idiomas = new Idiomas();

        // Identificadores y nombres de sensores
        $ids_sensores = $parametros_simulacion_factura_sensor_tarifa["ids_sensores_reparto_costes"];
        $nombres_sensores = $parametros_simulacion_factura_sensor_tarifa["nombres_sensores_reparto_costes"];

        // Tabla y gráfica de reparto de costes de la simulación factura
        if (count($ids_sensores) == 0)
        {
            $informacion_reparto_costes = array(
                "hay_datos_reparto_costes" => false,
                "datos_tabla_reparto_costes" => "",
                "datos_grafica_porcentajes_reparto_costes" => "",
                "datos_etiquetas_sensores_reparto_costes" => "");
            return ($informacion_reparto_costes);
        }

        // Se recuperan los consumos de los sensores
        $parametros_filas_valores_sensores = $parametros_simulacion_factura_sensor_tarifa;
        $medicion = $parametros_simulacion_factura_sensor_tarifa["medicion"];
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $parametros_filas_valores_sensores["clase_sensor"] = $clase_sensor;
        $parametros_filas_valores_sensores["ids_sensores"] = $ids_sensores;
        $parametros_filas_valores_sensores["nombres_sensores"] = $nombres_sensores;
        $parametros_filas_valores_sensores["intervalo_valores"] = INTERVALO_VALORES_HORA;
        $filas_valores_sensores = dame_filas_valores_sensores($parametros_filas_valores_sensores);

        // Campo de consumo
        $campo_consumo = dame_campo_consumo_clase_sensor($clase_sensor);

        // Se calcula el consumo total y de cada uno de los sensores
        $consumos_sensores = array();
        $consumo_total_sensores = 0.0;
        foreach ($nombres_sensores as $nombre_sensor)
        {
            // Consumo total
            $consumo_total = 0.0;

            // Se recorren las filas de valores
            $filas_valores_sensor = $filas_valores_sensores[$nombre_sensor];
            foreach ($filas_valores_sensor as $fila_valores_sensor)
            {
                $consumo = $fila_valores_sensor[$campo_consumo];
                if ($consumo !== NULL)
                {
                    $consumo = (float) $consumo;
                    $consumo_total += $consumo;
                }
            }

            // Se guarda el consumo total
            $consumos_sensores[$nombre_sensor] = $consumo_total;
            $consumo_total_sensores += $consumo_total;
        }

        // Cálculo de costes de cada uno de los sensores (dependiendo del porcentaje de consumo)
        $costes_sensores = array();
        foreach ($nombres_sensores as $nombre_sensor)
        {
            $consumo_sensor = $consumos_sensores[$nombre_sensor];
            if ($consumo_total_sensores == 0)
            {
                $porcentaje_consumo_sensor = 1 / count($nombres_sensores);
            }
            else
            {
                $porcentaje_consumo_sensor = $consumo_sensor / $consumo_total_sensores;
            }
            $coste_sensor = $coste_total * $porcentaje_consumo_sensor;
            $costes_sensores[$nombre_sensor] = $coste_sensor;
        }

        // Unidades de medida
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];

        // Tabla de reparto de costes por sensor
        $params_tabla_reparto_costes = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_REPARTO_COSTES_SIMULADOR_FACTURA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_REPARTO_COSTES_SIMULADOR_FACTURA),
            "generar_valores_xml" => true
        );
        $tabla_reparto_costes = new TablaDatos(
            "tabla-reparto-costes-simulador-factura",
            $idiomas->_("Reparto de costes"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_reparto_costes
        );
        $cabecera_tabla_reparto_costes = array(
            $idiomas->_("Sensor"),
            $idiomas->_("Consumo"),
            $idiomas->_("Coste")
        );
        $tabla_reparto_costes->anyade_cabecera("", $cabecera_tabla_reparto_costes);
        foreach ($nombres_sensores as $nombre_sensor)
        {
            $consumo_sensor = $consumos_sensores[$nombre_sensor];
            $coste_sensor = $costes_sensores[$nombre_sensor];
            $datos_fila_sensor = array(
                $nombre_sensor,
                formatea_numero($consumo_sensor, 2, false)." ".$unidad_medida_consumo,
                formatea_numero($coste_sensor, 2, false)." ".$unidad_medida_coste);
            $tabla_reparto_costes->anyade_fila("fila-igic-normal", $datos_fila_sensor);
        }
        $pie_tabla_reparto_costes = $idiomas->_("Coste total").": ".formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste;
        $tabla_reparto_costes->anyade_pie($pie_tabla_reparto_costes);

        // Gráfica de porcentajes de reparto de costes por sensor
        $grafica_porcentajes_reparto_costes = new VectorDatos();
        $datos_porcentajes_reparto_costes = new VectorDatos();
        foreach ($nombres_sensores as $nombre_sensor)
        {
            $coste_sensor = $costes_sensores[$nombre_sensor];
            $datos_porcentajes_reparto_costes->anyade_tupla_etiqueta_dato($nombre_sensor, $coste_sensor);
        }
        $grafica_porcentajes_reparto_costes->anyade_dato($datos_porcentajes_reparto_costes->dame_datos());

        // Etiquetas de sensores de reparto de costes
        $etiquetas_sensores_reparto_costes = new VectorDatos();
        foreach ($nombres_sensores as $nombre_sensor)
        {
            $etiquetas_sensores_reparto_costes->anyade_etiqueta($nombre_sensor);
        }

        // Se devuelve la información
        $informacion_reparto_costes = array(
            "hay_datos_reparto_costes" => true,
            "datos_tabla_reparto_costes" => $tabla_reparto_costes->dame_tabla(),
            "datos_grafica_porcentajes_reparto_costes" => $grafica_porcentajes_reparto_costes->dame_datos(),
            "datos_etiquetas_sensores_reparto_costes" => $etiquetas_sensores_reparto_costes->dame_datos());
        return ($informacion_reparto_costes);
    }
?>
