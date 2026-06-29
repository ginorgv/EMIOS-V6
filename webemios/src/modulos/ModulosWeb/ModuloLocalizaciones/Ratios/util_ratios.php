<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratio/Ratio.php');


    //
    // Funciones de listas de ratios
    //


    function dame_lista_tipos_ratio($tipo_ratio_seleccionado)
    {
        $tipos_ratio = Ratio::dame_tipos_ratio();

        foreach ($tipos_ratio as $tipo_ratio)
        {
            $nombre_tipo_ratio = Ratio::dame_descripcion_tipo_ratio($tipo_ratio);
            $lista .= "<option value='".$tipo_ratio."'";
            if ($tipo_ratio == $tipo_ratio_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_tipo_ratio, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    function dame_lista_clases_sensor_ratio_variable($clase_sensor_seleccionada)
    {
        $idiomas = new Idiomas();

        $lista_clases_sensor = "";
        $lista_clases_sensor .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_sensor_seleccionada);
        $clases_sensor = dame_clases_sensor_usuario_actual(false);
        if (($clase_sensor_seleccionada != CLASE_NINGUNA) && (in_array($clase_sensor_seleccionada, $clases_sensor) == False))
        {
            array_push($clases_sensor, $clase_sensor_seleccionada);

            // Se reordenan las clases de sensor
            $clases_sensor_reordenadas = array();
            $clases_sensor_ordenadas = NodoSensor::dame_clases_sensor();
            foreach ($clases_sensor_ordenadas as $clase_sensor)
            {
                if (in_array($clase_sensor, $clases_sensor) == true)
                {
                    array_push($clases_sensor_reordenadas, $clase_sensor);
                }
            }
            $clases_sensor = $clases_sensor_reordenadas;
        }

        // Se recorren las clases de sensor y se excluyen aquellas no visibles en el tipo de widget correspondiente
        foreach ($clases_sensor as $clase_sensor)
        {
            $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $anyadir_clase_sensor = false;
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_GENERICA:
                {
                    $anyadir_clase_sensor = true;
                    break;
                }
            }
            if ($anyadir_clase_sensor == true)
            {
                $lista_clases_sensor .= dame_opcion_valor_lista_simple($nombre_clase_sensor, $clase_sensor, $clase_sensor_seleccionada);
            }
            else
            {
                if ($clase_sensor_seleccionada == $clase_sensor)
                {
                    $clase_sensor_seleccionada = CLASE_NINGUNA;
                }
            }
        }

        return ($lista_clases_sensor);
    }


    function dame_lista_campos_sensor_ratio_variable($clase_sensor, $campo_seleccionado)
    {
        $lista_campos = "";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_GENERICA:
            {
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_VALOR), CAMPO_VALOR, $campo_seleccionado);
                $lista_campos .= dame_opcion_valor_lista_simple(dame_descripcion_campo_clase_sensor($clase_sensor, CAMPO_INCREMENTO), CAMPO_INCREMENTO, $campo_seleccionado);
                break;
            }
        }

        return ($lista_campos);
    }


    //
    // Funciones de obtención de información de ratios
    //


    function dame_fila_ratio($id_ratio)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ratio = "
            SELECT *
            FROM ratios
            WHERE
                id = '".$bd_red->_($id_ratio)."'";
        $res_ratio = $bd_red->ejecuta_consulta($consulta_ratio);
        if (($res_ratio == false) || ($res_ratio->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_ratio."'");
        }
        $fila_ratio = $res_ratio->dame_siguiente_fila();
        return ($fila_ratio);
    }


    //
    // Funciones de acciones de usuario
    //


    function anyade_parametros_accion_usuario_parametros_tipo_ratio($fila, &$parametros_accion_usuario)
    {
        switch ($fila["tipo"])
        {
            case TIPO_RATIO_FIJO:
            {
                if ($fila["valor_defecto"] != "")
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_DEFECTO_RATIO] = $fila["valor_defecto"];
                }
                break;
            }
            case TIPO_RATIO_VARIABLE:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase_sensor"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_SENSOR] = $fila["campo_sensor"];
                $nombre_sensor_defecto = dame_nombre_sensor($fila["sensor_defecto"]);
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_DEFECTO_RATIO] = $nombre_sensor_defecto;
                break;
            }
        }
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de los ratios visibles para el usuario actual
    function dame_ids_ratios_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de ratios
        $ids_ratios = array();
        $consulta = Ratio::dame_consulta_ratios();
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        while ($fila = $res->dame_siguiente_fila())
        {
            $id_ratio = $fila["id"];
            array_push($ids_ratios, $id_ratio);
        }
        return ($ids_ratios);
    }
?>
