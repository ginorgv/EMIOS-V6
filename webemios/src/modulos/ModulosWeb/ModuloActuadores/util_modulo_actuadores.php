<?php
    session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/util_informacion_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');


    //
    // Funciones de plantillas de informes
    //


    // Devuelve los identificadores de actuadores del elemento de la plantilla de informe especificado
    function dame_ids_actuadores_elemento_plantilla_informe_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los actuadores de los parámetros de tipo (con el tipo de selección de actuador fija)
        $ids_actuadores_elemento = array();
        switch ($tipo)
        {
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES];
                if ($tipo_seleccion_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES]);
                    $ids_actuadores_elemento = array_merge($ids_actuadores, $ids_actuadores_elemento);
                }
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                if ($destino_accion == DESTINO_ACCION_ACTUADOR)
                {
                    $tipo_seleccion_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    if ($tipo_seleccion_actuador == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                        array_push($ids_actuadores_elemento, $id_actuador);
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_actuadores_elemento);
    }


    // Elimina los identificadores de actuadores del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_actuadores_elemento_plantilla_informe_actuadores(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_actuadores_eliminar)
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
                $tipo_seleccion_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES];
                if ($tipo_seleccion_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES]);
                    $ids_actuadores_modificados = array();
                    for ($i = 0; $i < count($ids_actuadores); $i++)
                    {
                        $id_actuador = $ids_actuadores[$i];
                        if (in_array($id_actuador, $ids_actuadores_eliminar) == false)
                        {
                            array_push($ids_actuadores_modificados, $id_actuador);
                        }
                    }
                    $cadena_ids_actuadores_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_actuadores_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES] = $cadena_ids_actuadores_modificada;
                }
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                if ($destino_accion == DESTINO_ACCION_ACTUADOR)
                {
                    $tipo_seleccion_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    if ($tipo_seleccion_actuador == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                        if (in_array($id_actuador, $ids_actuadores_eliminar) == true)
                        {
                            $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION] = ID_NINGUNO;
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


    // Devuelve los identificadores de grupos de actuadores del elemento de la plantilla de informe especificado
    function dame_ids_grupos_actuadores_elemento_plantilla_informe_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los actuadores de los parámetros de tipo (con el tipo de selección de actuador fija)
        $ids_grupos_actuadores_elemento = array();
        switch ($tipo)
        {
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_grupos_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES];
                if ($tipo_seleccion_grupos_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_grupos_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES]);
                    $ids_grupos_actuadores_elemento = array_merge($ids_grupos_actuadores, $ids_grupos_actuadores_elemento);
                }
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                if ($destino_accion == DESTINO_ACCION_GRUPO_ACTUADORES)
                {
                    $tipo_seleccion_grupo_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    if ($tipo_seleccion_grupo_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_grupo_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                        array_push($ids_grupos_actuadores_elemento, $id_grupo_actuadores);
                    }
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_grupos_actuadores_elemento);
    }


    // Elimina los identificadores de grupos de actuadores del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_grupos_actuadores_eliminar)
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
                $tipo_seleccion_grupos_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES];
                if ($tipo_seleccion_grupos_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $ids_grupos_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES]);
                    $ids_grupos_actuadores_modificados = array();
                    for ($i = 0; $i < count($ids_grupos_actuadores); $i++)
                    {
                        $id_grupo_actuadores = $ids_grupos_actuadores[$i];
                        if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_eliminar) == false)
                        {
                            array_push($ids_grupos_actuadores_modificados, $id_grupo_actuadores);
                        }
                    }
                    $cadena_ids_grupos_actuadores_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_grupos_actuadores_modificados);
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES] = $cadena_ids_grupos_actuadores_modificada;
                }
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_accion = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                if ($destino_accion == DESTINO_ACCION_GRUPO_ACTUADORES)
                {
                    $tipo_seleccion_grupo_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    if ($tipo_seleccion_grupo_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                    {
                        $id_grupo_actuadores = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                        if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_eliminar) == true)
                        {
                            $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION] = ID_NINGUNO;
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


    // Devuelve los identificadores de sensores del elemento de la plantilla de informe especificado
    function dame_ids_sensores_elemento_plantilla_informe_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los sensores de los parámetros de tipo (con el tipo de selección de sensor fija)
        $ids_sensores_elemento = array();
        switch ($tipo)
        {
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR];
                    array_push($ids_sensores_elemento, $id_sensor);
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
    function elimina_ids_sensores_elemento_plantilla_informe_actuadores(
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
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR];
                    if (in_array($id_sensor, $ids_sensores_eliminar) == true)
                    {
                        $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR] = ID_NINGUNO;
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


    //
    // Funciones de informes automáticos
    //


    // Devuelve los identificadores de los actuadores del informe automático especificado
    function dame_ids_actuadores_informe_automatico_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los actuadores de los parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $indices_parametros_tipo_ids_actuadores = array();
        switch ($tipo)
        {
            // Información
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_acciones = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                switch ($destino_acciones)
                {
                    case DESTINO_ACCION_ACTUADOR:
                    {
                        array_push($indices_parametros_tipo_ids_actuadores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION);
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

        // Se recuperan los identificadores de actuadores de los parámetros de tipo
        $ids_actuadores = array();
        foreach ($indices_parametros_tipo_ids_actuadores as $indice_parametros_tipo_ids_actuadores)
        {
            $ids_actuadores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_actuadores]);
            $ids_actuadores = array_merge($ids_actuadores, $ids_actuadores_parametro_tipo);
        }
        return ($ids_actuadores);
    }


    // Devuelve los identificadores de los grupos de actuadores del informe automático especificado
    function dame_ids_grupos_actuadores_informe_automatico_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los actuadores de los parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $indices_parametros_tipo_ids_grupos_actuadores = array();
        switch ($tipo)
        {
            // Información
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $destino_acciones = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_DESTINO_ACCION];
                switch ($destino_acciones)
                {
                    case DESTINO_ACCION_GRUPO_ACTUADORES:
                    {
                        array_push($indices_parametros_tipo_ids_grupos_actuadores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION);
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

        // Se recuperan los identificadores de grupos de actuadores de los parámetros de tipo
        $ids_grupos_actuadores = array();
        foreach ($indices_parametros_tipo_ids_grupos_actuadores as $indice_parametros_tipo_ids_grupos_actuadores)
        {
            $ids_grupos_actuadores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_grupos_actuadores]);
            $ids_grupos_actuadores = array_merge($ids_grupos_actuadores, $ids_grupos_actuadores_parametro_tipo);
        }
        return ($ids_grupos_actuadores);
    }


    // Devuelve los identificadores de los sensores del informe automático especificado
    function dame_ids_sensores_informe_automatico_actuadores($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los sensores de los parámetros de tipo
        $indices_parametros_tipo_ids_sensores = array();
        switch ($tipo)
        {
            // Información
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                array_push($indices_parametros_tipo_ids_sensores, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de sensores de los parámetros de tipo
        $ids_sensores = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_sensores as $indice_parametros_tipo_ids_sensores)
        {
            $ids_sensores_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_sensores]);
            $ids_sensores = array_merge($ids_sensores, $ids_sensores_parametro_tipo);
        }
        return ($ids_sensores);
    }
?>
