<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/ParametroPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_elementos.php');


    // Devuelve los tipos de parámetro de plantillas de informes disponibles
    function dame_tipos_parametro_plantillas_informes_disponibles()
    {
        $idiomas = new Idiomas();

        $tipos_parametro = array();
        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);

        // Se añaden los tipos de parámetro dependiendo del módulo
        array_push($tipos_parametro, array(TIPO_NINGUNO, $idiomas->_("Ninguno")));

        // Se añaden los elementos del módulo Sensores
        if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
        {
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR)));
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES)));
        }

        // Se añaden los elementos del módulo Actuadores
        if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
        {
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR)));
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES)));
        }

        // Se añaden los elementos del módulo Proyectos
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE)));
            array_push($tipos_parametro, array(TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO, ParametroPlantillaInforme::dame_descripcion_tipo_parametro(TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO)));
        }

        return ($tipos_parametro);
    }


    // Devuelve si el parámetro se utiliza en algún elemento de plantilla de informe
    function dame_parametro_utilizado_elementos_plantilla_informe($id_plantilla_informe, $id_parametro, &$nombre_elemento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_elementos = "
            SELECT
                nombre,
                tipo,
                parametros_tipo,
                parametros_requeridos
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
            ORDER BY posicion ASC";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        $parametro_utilizado = false;
        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $nombre_elemento = $fila_elemento["nombre"];
            $tipo_elemento = $fila_elemento["tipo"];
            $cadena_parametros_tipo_elemento = $fila_elemento["parametros_tipo"];
            $parametros_tipo_elemento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_elemento);
            $cadena_ids_parametros_requeridos_elemento = $fila_elemento["parametros_requeridos"];
            $ids_parametros_requeridos_elemento = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_requeridos_elemento);

            // Comprobación de parámetro utilizado en alguna selección configurable de elemento
            switch ($tipo_elemento)
            {
                // Elementos de varios módulos
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    if ($parametro_utilizado == false)
                    {
                        $tipo_seleccion_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES];
                        if ($tipo_seleccion_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                        {
                            $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES]);
                            if (in_array($id_parametro, $ids_actuadores) == true)
                            {
                                $parametro_utilizado = true;
                            }
                        }
                    }
                    if ($parametro_utilizado == false)
                    {
                        $tipo_seleccion_grupos_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES];
                        if ($tipo_seleccion_grupos_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                        {
                            $ids_grupos_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES]);
                            if (in_array($id_parametro, $ids_grupos_actuadores) == true)
                            {
                                $parametro_utilizado = true;
                            }
                        }
                    }
                    break;
                }
                // Elementos de sensores (Eventos)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $tipo_seleccion_origen_evento = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_origen_evento = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR];
                        if ($id_parametro == $id_origen_evento)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de sensores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de sensores (Análisis)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES];
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de sensores (Comparación)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                {
                    $tipo_seleccion_sensor_principal = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                    if ($tipo_seleccion_sensor_principal == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    if ($parametro_utilizado == false)
                    {
                        $tipo_seleccion_sensores_secundarios = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                        if ($tipo_seleccion_sensores_secundarios == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                        {
                            $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS]);
                            if (in_array($id_parametro, $ids_sensores) == true)
                            {
                                $parametro_utilizado = true;
                            }
                        }
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
                            $id_sensor = $ids_sensores[$i];
                            if ($id_parametro == $id_sensor)
                            {
                                $parametro_utilizado = true;
                                break;
                            }
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                {
                    $tipo_seleccion_sensores_agregados = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                    if ($tipo_seleccion_sensores_agregados == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    if ($parametro_utilizado == false)
                    {
                        $tipo_seleccion_sensor_destacado = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                        if ($tipo_seleccion_sensor_destacado == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                        {
                            $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO];
                            if ($id_parametro == $id_sensor)
                            {
                                $parametro_utilizado = true;
                            }
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de sensores (Estadística)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
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
                            $id_sensor_independiente = $ids_sensores_independientes[$i];
                            if ($id_parametro == $id_sensor_independiente)
                            {
                                $parametro_utilizado = true;
                                break;
                            }
                        }
                    }
                    $tipo_seleccion_sensor_dependiente = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                    if ($tipo_seleccion_sensor_dependiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor_dependiente = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE];
                        if ($id_parametro == $id_sensor_dependiente)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de actuadores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $tipo_seleccion_destino_accion = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    if ($tipo_seleccion_destino_accion == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_destino_accion = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                        if ($id_parametro == $id_destino_accion)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de SmartMeter (Consumos y costes)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                    if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES]);
                        if (in_array($id_parametro, $ids_sensores) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de SmartMeter (Facturas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    $tipo_seleccion_sensores_reparto_costes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES];
                    if ($tipo_seleccion_sensores_reparto_costes == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $ids_sensores_reparto_costes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES];
                        if (in_array($id_parametro, $ids_sensores_reparto_costes) == true)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de SmartMeter (Tarifas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR];
                        if ($id_parametro == $id_sensor)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de Proyectos (Líneas base)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $tipo_seleccion_linea_base = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TIPO_SELECCION_LINEA_BASE];
                    if ($tipo_seleccion_linea_base == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_linea_base = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                        if ($id_parametro == $id_linea_base)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
                // Elementos de Proyectos (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $tipo_seleccion_proyecto = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_TIPO_SELECCION_PROYECTO];
                    if ($tipo_seleccion_proyecto == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                    {
                        $id_proyecto = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                        if ($id_parametro == $id_proyecto)
                        {
                            $parametro_utilizado = true;
                        }
                    }
                    break;
                }
            }

            // Comprobación de parámetro en algún parámetro requeridos
            if (in_array($id_parametro, $ids_parametros_requeridos_elemento) == true)
            {
                $parametro_utilizado = true;
            }

            // Si se utiliza el parámetro en algún elemento no hace falta comprobar más
            if ($parametro_utilizado == true)
            {
                break;
            }
        }
        return ($parametro_utilizado);
    }


    // Devuelve la lista de parámetros de sensor asociado de un parámetro de plantilla de informe
    function dame_lista_parametros_sensores_asociados_parametro_plantilla_informe(
        $id_plantilla_informe,
        $clase_sensor,
        $id_parametro_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $clase_sensor_asociado = CLASE_SENSOR_ENERGIA_ACTIVA;
                break;
            }
            default:
            {
                $clase_sensor_asociado = CLASE_NINGUNA;
                break;
            }
        }

        $lista_parametros = "<option value='".ID_NINGUNO."'";
        if (ID_NINGUNO == $id_parametro_seleccionado)
        {
            $lista_parametros .= " selected";
        }
        $lista_parametros .= ">".$idiomas->_("Ninguno")."</option>";
        if ($clase_sensor_asociado != CLASE_NINGUNA)
        {
            $consulta_parametros = "
                SELECT
                    id,
                    nombre
                FROM parametros_plantillas_informes
                WHERE
                    (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
                    AND (tipo = '".$bd_red->_(TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR)."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($clase_sensor_asociado)."')
                ORDER BY nombre ASC";
            $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
            if ($res_parametros == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_parametros."'");
            }

            while ($fila_parametro = $res_parametros->dame_siguiente_fila())
            {
                $lista_parametros .= "<option value='".$fila_parametro['id']."'";
                if ($fila_parametro['id'] == $id_parametro_seleccionado)
                {
                    $lista_parametros .= " selected";
                }
                $lista_parametros .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_parametros);
    }
?>