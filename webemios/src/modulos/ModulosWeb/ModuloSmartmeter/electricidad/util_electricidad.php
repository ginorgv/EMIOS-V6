<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Devuelve información del sensor de energía reactiva asociado
    function dame_info_sensor_energia_reactiva_asociado($id_sensor_energia_activa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera el sensor de energía reactiva
        $consulta_sensor_energia_reactiva = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')";
        $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
        if ($res_sensor_energia_reactiva == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensor_energia_reactiva."'");
        }

        if ($res_sensor_energia_reactiva->dame_numero_filas() > 0)
        {
            $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
            $info_sensor_energia_reactiva = $fila_sensor_energia_reactiva;
        }
        else
        {
            $info_sensor_energia_reactiva = NULL;
        }
        return ($info_sensor_energia_reactiva);
    }


    // Devuelve el nombre del sensor de energía activa asociado al sensor de corte de tensión
    function dame_info_sensor_energia_activa_asociado_sensor_cortes_tension($id_sensor_cortes_tension, $cadena_parametros_clase_cortes_tension)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los parámetros de clase del sensor de corte de tensión (si es necesario)
        if ($cadena_parametros_clase_cortes_tension === NULL)
        {
            $consulta_sensor_cortes_tension = "
                SELECT
                    parametros_clase
                FROM sensores
                WHERE
                    (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                    AND (id = '".$bd_red->_($id_sensor_cortes_tension)."')";
            $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
            if (($res_sensor_cortes_tension == false) || ($res_sensor_cortes_tension->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_cortes_tension."'");
            }
            $fila_sensor_cortes_tension = $res_sensor_cortes_tension->dame_siguiente_fila();
            $cadena_parametros_clase_cortes_tension = $fila_sensor_cortes_tension["parametros_clase"];
        }

        // Se recupera el sensor de energía activa asociado
        $parametros_clase_cortes_tension = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_cortes_tension);
        $id_sensor_energia_activa = $parametros_clase_cortes_tension[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
        if ($id_sensor_energia_activa == ID_NINGUNO)
        {
            return (NULL);
        }
        else
        {
            $consulta_sensor_energia_activa = "
                SELECT
                    id,
                    nombre,
                    granularidad_cuartohoraria
                FROM sensores
                WHERE
                    id = '".$bd_red->_($id_sensor_energia_activa)."'";
            $res_sensor_energia_activa = $bd_red->ejecuta_consulta($consulta_sensor_energia_activa);
            if (($res_sensor_energia_activa == false) || ($res_sensor_energia_activa->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_energia_activa."'");
            }
            $fila_sensor_energia_activa = $res_sensor_energia_activa->dame_siguiente_fila();
            $info_sensor_energia_activa = $fila_sensor_energia_activa;
            return ($info_sensor_energia_activa);
        }
    }


    // Devuelve el nombre del sensor de energía activa asociado al sensor de energía reactiva
    function dame_info_sensor_energia_activa_asociado_sensor_energia_reactiva($id_sensor_energia_reactiva, $cadena_parametros_clase_energia_reactiva)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los parámetros de clase del sensor de energía reactiva (si es necesario)
        if ($cadena_parametros_clase_energia_reactiva === NULL)
        {
            $consulta_sensor_energia_reactiva = "
                SELECT
                    parametros_clase
                FROM sensores
                WHERE
                    (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                    AND (id = '".$bd_red->_($id_sensor_energia_reactiva)."')";
            $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
            if (($res_sensor_energia_reactiva == false) || ($res_sensor_energia_reactiva->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_energia_reactiva."'");
            }
            $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
            $cadena_parametros_clase_energia_reactiva = $fila_sensor_energia_reactiva["parametros_clase"];
        }

        // Se recupera el sensor de energía activa asociado
        $parametros_clase_energia_reactiva = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_energia_reactiva);
        $id_sensor_energia_activa = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
        if ($id_sensor_energia_activa == ID_NINGUNO)
        {
            return (NULL);
        }
        else
        {
            $consulta_sensor_energia_activa = "
                SELECT
                    id,
                    nombre
                FROM sensores
                WHERE
                    id = '".$bd_red->_($id_sensor_energia_activa)."'";
            $res_sensor_energia_activa = $bd_red->ejecuta_consulta($consulta_sensor_energia_activa);
            if (($res_sensor_energia_activa == false) || ($res_sensor_energia_activa->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_energia_activa."'");
            }
            $fila_sensor_energia_activa = $res_sensor_energia_activa->dame_siguiente_fila();
            $info_sensor_energia_activa = $fila_sensor_energia_activa;
            return ($info_sensor_energia_activa);
        }
    }

    function dame_info_tipo_reactiva_sensor_energia_reactiva($id_sensor_energia_reactiva, $cadena_parametros_clase_energia_reactiva)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los parámetros de clase del sensor de energía reactiva (si es necesario)
        if ($cadena_parametros_clase_energia_reactiva === NULL)
        {
            $consulta_sensor_energia_reactiva = "
                SELECT
                    parametros_clase
                FROM sensores
                WHERE
                    (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                    AND (id = '".$bd_red->_($id_sensor_energia_reactiva)."')";
            $res_sensor_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensor_energia_reactiva);
            if (($res_sensor_energia_reactiva == false) || ($res_sensor_energia_reactiva->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor_energia_reactiva."'");
            }
            $fila_sensor_energia_reactiva = $res_sensor_energia_reactiva->dame_siguiente_fila();
            $cadena_parametros_clase_energia_reactiva = $fila_sensor_energia_reactiva["parametros_clase"];
        }
        
        // Se recupera el tipo de energia reactiva
        $parametros_clase_energia_reactiva = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_energia_reactiva);
        $tipo_energia_reactiva = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA];

        return($tipo_energia_reactiva);
    }

    // Devuelve información del sensor de cortes de tensión asociado
    function dame_info_sensor_cortes_tension_asociado($id_sensor_energia_activa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera el sensor de cortes de tensión
        $consulta_sensor_cortes_tension = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (clase = '".CLASE_SENSOR_CORTES_TENSION."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_sensor_energia_activa)."')";
        $res_sensor_cortes_tension = $bd_red->ejecuta_consulta($consulta_sensor_cortes_tension);
        if ($res_sensor_cortes_tension == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensor_cortes_tension."'");
        }

        if ($res_sensor_cortes_tension->dame_numero_filas() > 0)
        {
            $fila_sensor_cortes_tension = $res_sensor_cortes_tension->dame_siguiente_fila();
            $info_sensor_cortes_tension = $fila_sensor_cortes_tension;
        }
        else
        {
            $info_sensor_cortes_tension = NULL;
        }
        return ($info_sensor_cortes_tension);
    }
?>
