<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/ParametroPlantillaInforme.php');


    //
    // Funciones de obtención de información de parámetro de plantilla de informe
    //


    // Devuelve la fila del parámetro de plantilla de informe
    function dame_fila_parametro_plantilla_informe($id_parametro)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametro = "
            SELECT *
            FROM parametros_plantillas_informes
            WHERE
                id = '".$bd_red->_($id_parametro)."'";
        $res_parametro = $bd_red->ejecuta_consulta($consulta_parametro);
        if ($res_parametro == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametro."'");
        }

        $fila_parametro = $res_parametro->dame_siguiente_fila();
        return ($fila_parametro);
    }


    // Devuelve el nombre del valor del parámetro de plantilla de informe
    function dame_nombre_valor_parametro_plantilla_informe($fila_parametro, $valor_parametro)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        if ($valor_parametro == ID_NINGUNO)
        {
            return ($idiomas->_("Ninguno"));
        }

        $tipo_parametro = $fila_parametro["tipo"];
        switch ($tipo_parametro)
        {
            case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
            {
                $tabla_valores_parametros = "sensores";
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
            {
                $tabla_valores_parametros = "grupos_sensores";
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
            {
                $tabla_valores_parametros = "actuadores";
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
            {
                $tabla_valores_parametros = "grupos_actuadores";
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE:
            {
                $tabla_valores_parametros = "lineas_base";
                break;
            }
            case TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO:
            {
                $tabla_valores_parametros = "proyectos";
                break;
            }
            default:
            {
                throw new Exception("Tipo de parámetro desconocido: '".$tipo_parametro."'");
            }
        }

        $consulta_valor_parametro = "
            SELECT nombre
            FROM ".$tabla_valores_parametros."
            WHERE
                id = '".$bd_red->_($valor_parametro)."'";
        $res_valor_parametro = $bd_red->ejecuta_consulta($consulta_valor_parametro);
        if ($res_valor_parametro == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valor_parametro."'");
        }

        $fila_valor_parametro = $res_valor_parametro->dame_siguiente_fila();
        $nombre_valor_parametro = $fila_valor_parametro["nombre"];
        return ($nombre_valor_parametro);
    }


    //
    // Funciones de parámetros asociados
    //


    // Devuelve una lista con los identificadores y valores de parámetros asociados
    function dame_ids_valores_parametros_asociados_valor_parametro(
        $tipo,
        $parametros_tipo,
        $id_valor_parametro,
        $ids_parametros_asociados)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $ids_valores_parametros_asociados = array();
        switch ($tipo)
        {
            case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
            {
                $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        foreach ($ids_parametros_asociados as $id_parametro_asociado)
                        {
                            if ($id_valor_parametro == ID_NINGUNO)
                            {
                                $id_valor_parametro_asociado = array(
                                    "id" => $id_parametro_asociado,
                                    "valor" => ID_NINGUNO);
                                array_push($ids_valores_parametros_asociados, $id_valor_parametro_asociado);
                            }
                            else
                            {
                                $fila_parametro_asociado = dame_fila_parametro_plantilla_informe($id_parametro_asociado);
                                $parametros_tipo_asociado = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_parametro_asociado["parametros_tipo"]);
                                $clase_sensor_asociado = $parametros_tipo_asociado[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                                switch ($clase_sensor_asociado)
                                {
                                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                                    case CLASE_SENSOR_CORTES_TENSION:
                                    {
                                        switch ($clase_sensor_asociado)
                                        {
                                            case CLASE_SENSOR_ENERGIA_REACTIVA:
                                            {
                                                $consulta_sensor_asociado = "
                                                    SELECT id
                                                    FROM sensores
                                                    WHERE
                                                        (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                                                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_valor_parametro)."')";
                                                break;
                                            }
                                            case CLASE_SENSOR_CORTES_TENSION:
                                            {
                                                $consulta_sensor_asociado = "
                                                    SELECT id
                                                    FROM sensores
                                                    WHERE
                                                        (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                                                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_valor_parametro)."')";
                                                break;
                                            }
                                        }
                                        $res_sensor_asociado = $bd_red->ejecuta_consulta($consulta_sensor_asociado);
                                        if ($res_sensor_asociado == false)
                                        {
                                            throw new Exception("Error en la consulta: '".$consulta_sensor_asociado."'");
                                        }

                                        if ($res_sensor_asociado->dame_numero_filas() == 0)
                                        {
                                            $id_sensor_asociado = ID_NINGUNO;
                                        }
                                        else
                                        {
                                            $fila_sensor_asociado = $res_sensor_asociado->dame_siguiente_fila();
                                            $id_sensor_asociado = $fila_sensor_asociado["id"];
                                        }
                                        $id_valor_parametro_asociado = array(
                                            "id" => $id_parametro_asociado,
                                            "valor" => $id_sensor_asociado);
                                        array_push($ids_valores_parametros_asociados, $id_valor_parametro_asociado);
                                        break;
                                    }
                                    default:
                                    {
                                        break;
                                    }
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
                break;
            }
        }
        return ($ids_valores_parametros_asociados);
    }
?>