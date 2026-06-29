<?php
    session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/InformesFichero/util_informacion_informes_fichero.php');


    //
    // Funciones de plantillas de informes
    //


    // Devuelve los identificadores de líneas base del elemento de la plantilla de informe especificado
    function dame_ids_lineas_base_elemento_plantilla_informe_proyectos($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de las líneas base de los parámetros de tipo (con el tipo de selección de línea base fija)
        $ids_lineas_base_elemento = array();
        switch ($tipo)
        {
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $id_linea_base = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                array_push($ids_lineas_base_elemento, $id_linea_base);
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_lineas_base_elemento);
    }


    // Elimina los identificadores de líneas base del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_lineas_base_elemento_plantilla_informe_proyectos(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_lineas_base_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $id_linea_base = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                if (in_array($id_linea_base, $ids_lineas_base_eliminar) == true)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE] = ID_NINGUNO;
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


    // Devuelve los identificadores de proyectos del elemento de la plantilla de informe especificado
    function dame_ids_proyectos_elemento_plantilla_informe_proyectos($tipo, $cadena_parametros_tipo)
    {
        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se recuperan los identificadores de los proyectos de los parámetros de tipo (con el tipo de selección de proyecto fija)
        $ids_proyectos_elemento = array();
        switch ($tipo)
        {
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                array_push($ids_proyectos_elemento, $id_proyecto);
                break;
            }
            default:
            {
                break;
            }
        }
        return ($ids_proyectos_elemento);
    }


    // Elimina los identificadores de proyectos del elemento de plantillas de informe (se establecen a ninguno)
    function elimina_ids_proyectos_elemento_plantilla_informe_proyectos(
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $ids_proyectos_eliminar)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros de tipo
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);

        // Se modifican los identificadores correspondientes
        switch ($tipo)
        {
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                if (in_array($id_proyecto, $ids_proyectos_eliminar) == true)
                {
                    $parametros_tipo[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO] = ID_NINGUNO;
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


    // Devuelve los identificadores de sensores del informe automático especificado
    function dame_ids_sensores_informe_automatico_proyectos($tipo, $cadena_parametros_tipo)
    {
        // Identificadores de sensores
        $ids_sensores = array();

        // Se recuperan los identificadores de sensores del informe automático correspondiente
        switch ($tipo)
        {
            // Simulación de línea base
            case TIPO_INFORME_PROYECTOS_SIMULACION_LINEA_BASE:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                $id_sensor_proyecto = dame_id_sensor_proyecto($id_proyecto);
                $ids_sensores = array($id_sensor_proyecto);
                break;
            }
            // Información de proyecto
            case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
                $id_proyecto = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                $id_sensor_proyecto = dame_id_sensor_proyecto($id_proyecto);
                $ids_sensores = array($id_sensor_proyecto);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se devuelven los sensores del informe automático
        return ($ids_sensores);
    }


    // Devuelve los identificadores de las líneas base del informe automático especificado
    function dame_ids_lineas_base_informe_automatico_proyectos($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de las líneas base de los parámetros de tipo
        $indices_parametros_tipo_ids_lineas_base = array();
        switch ($tipo)
        {
            // Líneas base
            case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                array_push($indices_parametros_tipo_ids_lineas_base, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de líneas base de los parámetros de tipo
        $ids_lineas_base = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_lineas_base as $indice_parametros_tipo_ids_lineas_base)
        {
            $ids_lineas_base_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_lineas_base]);
            $ids_lineas_base = array_merge($ids_lineas_base, $ids_lineas_base_parametro_tipo);
        }
        return ($ids_lineas_base);
    }


    // Devuelve los identificadores de los proyectos del informe automático especificado
    function dame_ids_proyectos_informe_automatico_proyectos($tipo, $cadena_parametros_tipo)
    {
        // Se recuperan los índices de los identificadores de los proyectos de los parámetros de tipo
        $indices_parametros_tipo_ids_proyectos = array();
        switch ($tipo)
        {
            // Información
            case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                array_push($indices_parametros_tipo_ids_proyectos, INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO);
                break;
            }
            default:
            {
                break;
            }
        }

        // Se recuperan los identificadores de proyectos de los parámetros de tipo
        $ids_proyectos = array();
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        foreach ($indices_parametros_tipo_ids_proyectos as $indice_parametros_tipo_ids_proyectos)
        {
            $ids_proyectos_parametro_tipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[$indice_parametros_tipo_ids_proyectos]);
            $ids_proyectos = array_merge($ids_proyectos, $ids_proyectos_parametro_tipo);
        }
        return ($ids_proyectos);
    }
?>
