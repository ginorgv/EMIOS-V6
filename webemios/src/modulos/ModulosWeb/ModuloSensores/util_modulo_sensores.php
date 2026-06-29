<?php
    session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/util_analisis_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/util_comparacion_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/util_estadistica_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/util_informacion_informes_fichero.php');


    //
    // Funciones de plantillas de informes
    //


    // Devuelve el identificador de ratio del elemento de la plantilla de informe especificado
    function dame_id_ratio_elemento_plantilla_informe_sensores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recupera el índice del identificador del ratio de los parámetros de tipo
        $indice_parametros_tipo_id_ratio = NULL;
        switch ($tipo)
        {
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_RATIO;
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO;
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_ID_RATIO;
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_RATIO;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_RATIO;
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
    function elimina_id_ratio_elemento_plantilla_informe_sensores(
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
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_RATIO] = ID_NINGUNO;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_RATIO];
                if ($id_ratio == $id_ratio_eliminar)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_RATIO] = ID_NINGUNO;
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
    function dame_ids_sensores_elemento_plantilla_informe_sensores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los sensores de los parámetros de tipo (con el tipo de selección de sensor fija)
        $ids_sensores_elemento = array();
        switch ($tipo)
        {
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES];
                    if ($cadena_ids_sensores != "")
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                        $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                    }
                }
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                if ($origen_evento == ORIGEN_EVENTO_SENSOR)
                {
                    $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
                        array_push($ids_sensores_elemento, $id_sensor);
                    }
                }
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES];
                    if ($cadena_ids_sensores != "")
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                        $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                    }
                }
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $tipo_seleccion_sensor_principal = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                if ($tipo_seleccion_sensor_principal == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor_principal = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL];
                    array_push($ids_sensores_elemento, $id_sensor_principal);
                }
                $tipo_seleccion_sensores_secundarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                if ($tipo_seleccion_sensores_secundarios == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores_secundarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS];
                    if ($cadena_ids_sensores_secundarios != "")
                    {
                        $ids_sensores_secundarios = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_secundarios);
                        $ids_sensores_elemento = array_merge($ids_sensores_secundarios, $ids_sensores_elemento);
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $tipos_seleccion_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES]);
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES]);
                for ($i = 0; $i < count($tipos_seleccion_sensores); $i++)
                {
                    $tipo_seleccion_sensor = $tipos_seleccion_sensores[$i];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor = $ids_sensores[$i];
                        array_push($ids_sensores_elemento, $id_sensor);
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $tipo_seleccion_sensores_agregados = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                if ($tipo_seleccion_sensores_agregados == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores_agregados = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS];
                    if ($cadena_ids_sensores_agregados != "")
                    {
                        $ids_sensores_agregados = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_agregados);
                        $ids_sensores_elemento = array_merge($ids_sensores_agregados, $ids_sensores_elemento);
                    }
                }
                $tipo_seleccion_sensor_destacado = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                if ($tipo_seleccion_sensor_destacado == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor_destacado = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO];
                    array_push($ids_sensores_elemento, $id_sensor_destacado);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES];
                    if ($cadena_ids_sensores != "")
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                        $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $cadena_ids_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES];
                    if ($cadena_ids_sensores != "")
                    {
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores);
                        $ids_sensores_elemento = array_merge($ids_sensores, $ids_sensores_elemento);
                    }
                }
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $tipos_seleccion_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES]);
                $ids_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES]);
                for ($i = 0; $i < count($tipos_seleccion_sensores_independientes); $i++)
                {
                    $tipo_seleccion_sensor_independiente = $tipos_seleccion_sensores_independientes[$i];
                    if ($tipo_seleccion_sensor_independiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor_independiente = $ids_sensores_independientes[$i];
                        array_push($ids_sensores_elemento, $id_sensor_independiente);
                    }
                }
                $tipo_seleccion_sensor_dependiente = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                if ($tipo_seleccion_sensor_dependiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor_dependiente = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE];
                    array_push($ids_sensores_elemento, $id_sensor_dependiente);
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
    function elimina_ids_sensores_elemento_plantilla_informe_sensores(
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
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                if ($origen_evento == ORIGEN_EVENTO_SENSOR)
                {
                    $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
                        if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                        {
                            $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO] = ID_NINGUNO;
                        }
                    }
                }
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $tipo_seleccion_sensor_principal = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                if ($tipo_seleccion_sensor_principal == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL] = ID_NINGUNO;
                    }
                }
                $tipo_seleccion_sensores_secundarios = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                if ($tipo_seleccion_sensores_secundarios == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $tipos_seleccion_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES]);
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES]);
                for ($i = 0; $i < count($tipos_seleccion_sensores); $i++)
                {
                    $tipo_seleccion_sensor = $tipos_seleccion_sensores[$i];
                    if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor = $ids_sensores[$i];
                        if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                        {
                            $ids_sensores[$i] = ID_NINGUNO;
                        }
                    }
                }
                $cadena_ids_sensores_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores);
                $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $tipo_seleccion_sensores_agregados = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                if ($tipo_seleccion_sensores_agregados == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS] = $cadena_ids_sensores_modificada;
                }
                $tipo_seleccion_sensor_destacado = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                if ($tipo_seleccion_sensor_destacado == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES]);
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
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES] = $cadena_ids_sensores_modificada;
                }
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR] = ID_NINGUNO;
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $tipos_seleccion_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES]);
                $ids_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES]);
                for ($i = 0; $i < count($tipos_seleccion_sensores_independientes); $i++)
                {
                    $tipo_seleccion_sensor_independiente = $tipos_seleccion_sensores_independientes[$i];
                    if ($tipo_seleccion_sensor_independiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_sensor_independiente = $ids_sensores_independientes[$i];
                        if (in_array($id_sensor_independiente, $ids_sensores_eliminar) == true)
                        {
                            $ids_sensores_independientes[$i] = ID_NINGUNO;
                        }
                    }
                }
                $cadena_ids_sensores_independientes_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores_independientes);
                $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES] = $cadena_ids_sensores_independientes_modificada;
                $tipo_seleccion_sensor_dependiente = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                if ($tipo_seleccion_sensor_dependiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE] = ID_NINGUNO;
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


    // Devuelve los identificadores de grupos de sensores del elemento de la plantilla de informe especificado
    function dame_ids_grupos_sensores_elemento_plantilla_informe_sensores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los sensores de los parámetros de tipo (con el tipo de selección de sensor fija)
        $ids_grupos_sensores_elemento = array();
        switch ($tipo)
        {
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                if ($origen_evento == ORIGEN_EVENTO_GRUPO_SENSORES)
                {
                    $tipo_seleccion_grupo_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    if ($tipo_seleccion_grupo_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_grupo_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
                        array_push($ids_grupos_sensores_elemento, $id_grupo_sensores);
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_grupos_sensores_elemento);
    }


    // Elimina los identificadores de grupos de sensores del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_grupos_sensores_elemento_plantilla_informe_sensores(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_grupos_sensores_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                if ($origen_evento == ORIGEN_EVENTO_GRUPO_SENSORES)
                {
                    $tipo_seleccion_grupo_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    if ($tipo_seleccion_grupo_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_grupo_sensores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
                        if (in_array($id_grupo_sensores, $ids_grupos_sensores_eliminar) == true)
                        {
                            $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO] = ID_NINGUNO;
                        }
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


    // Devuelve los identificadores de eventos del elemento de la plantilla de informe especificado
    function dame_ids_eventos_elemento_plantilla_informe_sensores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los eventos de los parámetros de tipo
        $ids_eventos_elemento = array();
        switch ($tipo)
        {
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $tipo_seleccion_origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_eventos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS]);
                    $ids_eventos_elemento = array_merge($ids_eventos, $ids_eventos_elemento);
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_eventos_elemento);
    }


    // Elimina los identificadores de eventos del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_eventos_elemento_plantilla_informe_sensores(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_eventos_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $tipo_seleccion_origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_eventos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS]);
                    $ids_eventos_modificados = array();
                    for ($i = 0; $i < count($ids_eventos); $i++)
                    {
                        $id_evento = $ids_eventos[$i];
                        if (in_array($id_evento, $ids_eventos_eliminar) == false)
                        {
                            array_push($ids_eventos_modificados, $id_evento);
                        }
                    }
                    $cadena_ids_eventos_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_eventos_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS] = $cadena_ids_eventos_modificada;
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


    //
    // Funciones de informes automáticos
    //


    // Devuelve el identificador de ratio del informe automático especificado
    function dame_id_ratio_informe_automatico_sensores($tipo, $cadena_parametros_tipo)
    {
        // Se recupera el índice de identificador de ratio de los parámetros de tipo
        $indice_parametros_tipo_id_ratio = array();
        switch ($tipo)
        {
            // Información
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_RATIO;
                break;
            }
            // Análisis
            case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_ID_RATIO;
                break;
            }
            // Comparación
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_VALORES_GENERALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_ID_RATIO;
                break;
            }
            // Estadística
            case TIPO_INFORME_SENSORES_HISTOGRAMA:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_RATIO;
                break;
            }
            case TIPO_INFORME_SENSORES_CORRELACION:
            {
                $indice_parametros_tipo_id_ratio = INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_RATIO;
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
    function dame_ids_sensores_informe_automatico_sensores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $indices_parametros_tipo_ids_sensores = array();
        switch ($tipo)
        {
            // Eventos
            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                switch ($origen_evento)
                {
                    case ORIGEN_EVENTO_SENSOR:
                    {
                        array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO);
                        break;
                    }
                }
                break;
            }
            // Información
            case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_TEMPERATURA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_HUMEDAD_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_LUZ_INTERIOR_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VIENTO_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_ENERGIA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_CORTES_TENSION_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_COMPRA_ENERGIA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GAS_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_AGUA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INFORMACION_GENERICA_ID_SENSOR);
                break;
            }
            // Análisis
            case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_HORARIO_ID_RATIO);
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_DIARIO_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES);
                break;
            }
            // Comparación
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERIODOS_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES);
                break;
            }
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES);
                break;
            }
            case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS);
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO);
                break;
            }
            case TIPO_INFORME_SENSORES_VALORES_GENERALES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_VALORES_GENERALES_IDS_SENSORES);
                break;
            }
            case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES);
                break;
            }
            // Estadística
            case TIPO_INFORME_SENSORES_HISTOGRAMA:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_HISTOGRAMA_ID_SENSOR);
                break;
            }
            case TIPO_INFORME_SENSORES_CORRELACION:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES);
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de sensores de los parámetros de tipo
        $ids_sensores = array();
        foreach ($indices_parametros_tipo_ids_sensores as $indice_parametros_tipo_ids_sensores)
        {
            $ids_sensores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_sensores]);
            $ids_sensores = array_merge($ids_sensores, $ids_sensores_parametro_tipo);
        }
        return ($ids_sensores);
    }


    // Devuelve los identificadores de los grupos de sensores del informe automático especificado
    function dame_ids_grupos_sensores_informe_automatico_sensores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $indices_parametros_tipo_ids_grupos_sensores = array();
        switch ($tipo)
        {
            // Eventos
            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
                switch ($origen_evento)
                {
                    case ORIGEN_EVENTO_GRUPO_SENSORES:
                    {
                        array_push($indices_parametros_tipo_ids_grupos_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO);
                        break;
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de grupos de sensores de los parámetros de tipo
        $ids_grupos_sensores = array();
        foreach ($indices_parametros_tipo_ids_grupos_sensores as $indice_parametros_tipo_ids_grupos_sensores)
        {
            $ids_grupos_sensores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_grupos_sensores]);
            $ids_grupos_sensores = array_merge($ids_grupos_sensores, $ids_grupos_sensores_parametro_tipo);
        }
        return ($ids_grupos_sensores);
    }


    // Devuelve los identificadores de los eventos del informe automático especificado
    function dame_ids_eventos_informe_automatico_sensores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $indices_parametros_tipo_ids_eventos = array();
        switch ($tipo)
        {
            // Eventos
            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                array_push($indices_parametros_tipo_ids_eventos, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de eventos de los parámetros de tipo
        $ids_eventos = array();
        foreach ($indices_parametros_tipo_ids_eventos as $indice_parametros_tipo_ids_eventos)
        {
            $ids_eventos_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_eventos]);
            $ids_eventos = array_merge($ids_eventos, $ids_eventos_parametro_tipo);
        }
        return ($ids_eventos);
    }


    //
    // Funciones para dibujado de gráficas de sensores
    //


    // Devuelve el número de decimales de valores según el campo de clase de sensor
    function dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                switch ($campo)
                {
                    case CAMPO_ILUMINACION:
                    {
                        $numero_decimales = 0;
                        break;
                    }
                    default:
                    {
                        $numero_decimales = 2;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($campo)
                {
                    case CAMPO_COSENO_PHI:
                    {
                        $numero_decimales = 3;
                        break;
                    }
                    default:
                    {
                        $numero_decimales = 2;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $numero_decimales = 0;
                break;
            }
            default:
            {
                $numero_decimales = 2;
                break;
            }
        }

        return ($numero_decimales);
    }


    // Devuelve el tipo de líneas de valores según el intervalo y campo de clase de sensor
    function dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
        $intervalo_valores,
        $clase_sensor,
        $id_sensor,
        $campo)
    {
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            {
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_LUZ_INTERIOR:
                    {
                        switch ($campo)
                        {
                            case CAMPO_LUZ_ARTIFICIAL:
                            {
                                $tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                                break;
                            }
                            default:
                            {
                                $tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                                break;
                            }
                        }
                        break;
                    }
                    case CLASE_SENSOR_CORTES_TENSION:
                    {
                        $tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                        break;
                    }
                    case CLASE_SENSOR_GENERICA:
                    {
                        $fila_sensor = dame_fila_sensor($id_sensor);
                        $cambio_valores_puntuales_sensor = $fila_sensor["cambio_valores_puntuales"];
                        switch ($cambio_valores_puntuales_sensor)
                        {
                            case CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL:
                            {
                                $tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                                break;
                            }
                            case CAMBIO_VALORES_PUNTUALES_SENSOR_INSTANTANEO:
                            {
                                $tipo_lineas_valores = TIPO_LINEAS_VALORES_CUADRADAS;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        $tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                        break;
                    }
                }
                break;
            }
            default:
            {
                $tipo_lineas_valores = TIPO_LINEAS_VALORES_ESTANDAR;
                break;
            }
        }

        return ($tipo_lineas_valores);
    }
?>
