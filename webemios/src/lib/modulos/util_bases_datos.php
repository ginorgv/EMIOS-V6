<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	//
    // Funciones de base de datos
    //


    // Devuelve la cadena de identificadores para consulta de base de datos
    // (si es vacía se devuelve una cadena con un identificador inválido para que no falle la consulta)
    function dame_cadena_ids_consulta($ids)
    {
        $numero_ids = count($ids);
        if ($numero_ids == 0)
        {
            $cadena_ids = ID_INVALIDO;
        }
        else
        {
            $primer_id = array_values($ids)[0];
            if (($numero_ids == 1) && ($primer_id === NULL))
            {
                $cadena_ids = ID_INVALIDO;
            }
            else
            {
                $cadena_ids = implode(",", $ids);
            }
        }
        return ($cadena_ids);
    }


    // Devuelve la cadena de nombres para consulta de base de datos
    // (si es vacía se devuelve una cadena con un nombre vacío para que no falle la consulta)
    function dame_cadena_nombres_consulta($nombres, $bd)
    {
        $numero_nombres = count($nombres);
        if ($numero_nombres == 0)
        {
            $cadena_nombres = "''";
        }
        else
        {
            $primer_nombre = array_values($nombres)[0];
            if (($numero_nombres == 1) && ($primer_nombre === NULL))
            {
                $cadena_nombres = "''";
            }
            else
            {
                $lista_nombres = array_values($nombres);
                for ($i = 0; $i < count($lista_nombres); $i++)
                {
                    $lista_nombres[$i] = "'".$bd->_($lista_nombres[$i])."'";
                }
                $cadena_nombres = implode(",", $lista_nombres);
            }
        }
        return ($cadena_nombres);
    }


    // Devuelve la parte de la consulta de modificación de campos (si su valor no es nulo)
    function dame_clausula_modificacion_campos($bd, $info_campos)
    {
        $clausula_modificacion = "";
        foreach ($info_campos as $info_campo)
        {
            $nombre_campo = $info_campo["nombre"];
            $valor_campo = $info_campo["valor"];
            $valor_nulo_campo = $info_campo["valor_nulo"];

            if ($valor_campo !== $valor_nulo_campo)
            {
                if ($clausula_modificacion != "")
                {
                    $clausula_modificacion .= ", ";
                }
                $clausula_modificacion .= $nombre_campo." = '".$bd->_($valor_campo)."'";
            }
        }
        return ($clausula_modificacion);
    }


    // Devuelve la información de los campos modificados (si su valor no es nulo)
    function dame_info_campos_modificados($info_campos)
    {
        $info_campos_modificados = array();
        foreach ($info_campos as $info_campo)
        {
            if ($info_campo["valor"] != $info_campo["valor_nulo"])
            {
                array_push($info_campos_modificados, $info_campo);
            }
        }
        return ($info_campos_modificados);
    }


    // Devuelve la condición de consulta del filtro de búsqueda
    function dame_condicion_consulta_filtro_busqueda($campos, $filtro)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $filtro_busqueda = $filtro;
        $busqueda_multiple = "";
        $tipo_busqueda_multiple = "";
        $textos_busqueda = array();
        $longitud_cadena_busqueda = strlen($filtro_busqueda);
        if ($longitud_cadena_busqueda > 0)
        {
            if (($filtro_busqueda[0] == '+') || ($filtro_busqueda[0] == '*'))
            {
                $tipo_busqueda_multiple = $filtro_busqueda[0];
                $filtro_busqueda = preg_replace('/\s+/', '', $filtro_busqueda);
                $longitud_cadena_busqueda = strlen($filtro_busqueda);
                if (($filtro_busqueda[1] == "{") && ($filtro_busqueda[$longitud_cadena_busqueda - 1] == "}"))
                {
                    $busqueda_multiple = true;
                    $cadena_textos_busqueda = substr($filtro_busqueda, 2, -1);
                    $textos_busqueda = explode(",", $cadena_textos_busqueda);
                }
            }
        }

        $condicion_consulta_filtro = "";
        $numero_campos = count($campos);
        if ($numero_campos > 1)
        {
            $condicion_consulta_filtro .= "(";
        }
        $numero_campo = 1;
        foreach ($campos as $campo)
        {
            if ($numero_campo > 1)
            {
                $condicion_consulta_filtro .= " OR ";
            }
            if ($busqueda_multiple == false)
            {
                $condicion_consulta_filtro .= "(".$campo." LIKE '%".$bd_red->_($filtro)."%')";
            }
            else
            {
                $numero_textos_busqueda = count($textos_busqueda);
                if ($numero_textos_busqueda > 1)
                {
                    $condicion_consulta_filtro .= "(";
                }
                $numero_texto_busqueda = 1;
                foreach ($textos_busqueda as $texto_busqueda)
                {
                    if ($numero_texto_busqueda > 1)
                    {
                        switch ($tipo_busqueda_multiple)
                        {
                            case '+':
                            {
                                $condicion_consulta_filtro .= " OR ";
                                break;
                            }
                            case '*':
                            {
                                $condicion_consulta_filtro .= " AND ";
                                break;
                            }
                        }
                    }
                    $condicion_consulta_filtro .= "(".$campo." LIKE '%".$bd_red->_($texto_busqueda)."%')";
                    $numero_texto_busqueda += 1;
                }
                if ($numero_textos_busqueda > 1)
                {
                    $condicion_consulta_filtro .= ")";
                }
            }
            $numero_campo += 1;
        }
        if ($numero_campos > 1)
        {
            $condicion_consulta_filtro .= ")";
        }
        return ($condicion_consulta_filtro);
    }


    // Devuelve el filtro para la consulta de horario semanal, exclusion e inclusión de fechas
    function dame_filtro_consulta_horario_semanal_fechas($horario_semanal, $exclusion_fechas, $inclusion_fechas)
    {
        $filtro_consulta_horario_semanal = dame_filtro_consulta_horario_semanal($horario_semanal);
        $filtro_consulta_exclusion_fechas = dame_filtro_consulta_exclusion_fechas($exclusion_fechas);
        $filtro_consulta_inclusion_fechas = dame_filtro_consulta_inclusion_fechas($inclusion_fechas);

        $filtro_consulta_horario_semanal_fechas = "";
        if ($filtro_consulta_horario_semanal != "")
        {
            if ($filtro_consulta_inclusion_fechas == "")
            {
                $filtro_consulta_horario_semanal_fechas .= " AND (".$filtro_consulta_horario_semanal.")";
            }
            else
            {
                $filtro_consulta_horario_semanal_fechas .= " AND ((".$filtro_consulta_horario_semanal.")";
                $filtro_consulta_horario_semanal_fechas .= " OR (".$filtro_consulta_inclusion_fechas."))";
            }
        }
        else
        {
            if ($filtro_consulta_inclusion_fechas != "")
            {
                $filtro_consulta_horario_semanal_fechas .= " AND (".$filtro_consulta_inclusion_fechas.")";
            }
        }
        if ($filtro_consulta_exclusion_fechas != "")
        {
            $filtro_consulta_horario_semanal_fechas .= " AND (".$filtro_consulta_exclusion_fechas.")";
        }
        return ($filtro_consulta_horario_semanal_fechas);
    }


    // Devuelve el filtro para la consulta de horario semanal
    function dame_filtro_consulta_horario_semanal($horario_semanal)
    {
        if ($horario_semanal === NULL)
        {
            return ("");
        }

        $selecciones_dias_semana = $horario_semanal["selecciones_dias_semana"];
        $periodos_dias_semana = $horario_semanal["periodos_dias_semana"];

        $zona_horaria = dame_zona_horaria_local();

        $filtro_consulta = "";
        $numero_dias_seleccionados = 0;
        $horas_modificadas = false;
        for ($i = 0; $i < 7; $i++)
        {
            $seleccion_dia_semana = $selecciones_dias_semana[$i];
            if ($seleccion_dia_semana == VALOR_SI)
            {
                if ($numero_dias_seleccionados > 0)
                {
                    $filtro_consulta .= " OR ";
                }
                $numero_dias_seleccionados += 1;

                $periodos_dia_semana = $periodos_dias_semana[$i];
                if ((count($periodos_dia_semana) == 0) ||
                    (($periodos_dia_semana[0][0] == "00:00:00") && ($periodos_dia_semana[0][1] == "23:59:59")))
                {
                    $filtro_consulta .= "(WEEKDAY(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) = ".$i.")";
                }
                else
                {
                    $filtro_consulta .= "((WEEKDAY(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) = ".$i.") AND (";
                    for ($j = 0; $j < count($periodos_dia_semana); $j++)
                    {
                        $periodo_dia_semana = $periodos_dia_semana[$j];
                        if ($j > 0)
                        {
                            $filtro_consulta .= " OR ";
                        }
                        $filtro_consulta .= "(TIME(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) >= '".$periodo_dia_semana[0]."' AND TIME(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."')) <= '".$periodo_dia_semana[1]."')";
                    }
                    $filtro_consulta .= "))";
                    $horas_modificadas = true;
                }
            }
        }
        if (($numero_dias_seleccionados == 7) && ($horas_modificadas == false))
        {
            $filtro_consulta = "";
        }
        return ($filtro_consulta);
    }


    // Devuelve el filtro para la consulta de exclusión de fechas
    function dame_filtro_consulta_exclusion_fechas($exclusion_fechas)
    {
        if ($exclusion_fechas === NULL)
        {
            return ("");
        }

        $seleccion_exclusion = $exclusion_fechas["seleccion"];
        $periodos_fechas_exclusion = $exclusion_fechas["periodos_fechas"];
        $periodos_dias_anyo_exclusion = $exclusion_fechas["periodos_dias_anyo"];

        $zona_horaria = dame_zona_horaria_local();

        $filtro_consulta = "";
        if ($seleccion_exclusion == VALOR_SI)
        {
            foreach ($periodos_fechas_exclusion as $periodo_fechas_exclusion)
            {
                if ($filtro_consulta != "")
                {
                    $filtro_consulta .= " AND ";
                }
                $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_local = $periodo_fechas_exclusion[0];
                $cadena_fecha_hora_fin_periodo_exclusion_base_datos_local = $periodo_fechas_exclusion[1];
                if (count(explode(" ", $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_local)) == 1)
                {
                    $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_local .= " 00:00";
                }
                if (count(explode(" ", $cadena_fecha_hora_fin_periodo_exclusion_base_datos_local)) == 1)
                {
                    $cadena_fecha_hora_fin_periodo_exclusion_base_datos_local .= " 23:59";
                }
                $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_local .= ":00";
                $cadena_fecha_hora_fin_periodo_exclusion_base_datos_local .= ":59";

                $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora(
                    $cadena_fecha_hora_inicio_periodo_exclusion_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);
                $cadena_fecha_hora_fin_periodo_exclusion_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora(
                    $cadena_fecha_hora_fin_periodo_exclusion_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);

                $filtro_consulta .= "
                    ((hora < '".$cadena_fecha_hora_inicio_periodo_exclusion_base_datos_utc."')
                    OR (hora > '".$cadena_fecha_hora_fin_periodo_exclusion_base_datos_utc."'))";
            }
            foreach ($periodos_dias_anyo_exclusion as $periodo_dias_anyo_exclusion)
            {
                if ($filtro_consulta != "")
                {
                    $filtro_consulta .= " AND ";
                }
                $dia_anyo_inicio_periodo_exclusion_base_datos = $periodo_dias_anyo_exclusion[0];
                $dia_anyo_fin_periodo_exclusion_base_datos = $periodo_dias_anyo_exclusion[1];
                $filtro_consulta .= "
                    ((DATE_FORMAT(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), '%m-%d') < '".$dia_anyo_inicio_periodo_exclusion_base_datos."')
                    OR (DATE_FORMAT(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), '%m-%d') > '".$dia_anyo_fin_periodo_exclusion_base_datos."'))";
            }
        }
        return ($filtro_consulta);
    }


    // Devuelve el filtro para la consulta de inclusión de fechas
    function dame_filtro_consulta_inclusion_fechas($inclusion_fechas)
    {
        if ($inclusion_fechas === NULL)
        {
            return ("");
        }

        $seleccion_inclusion = $inclusion_fechas["seleccion"];
        $periodos_fechas_inclusion = $inclusion_fechas["periodos_fechas"];
        $periodos_dias_anyo_inclusion = $inclusion_fechas["periodos_dias_anyo"];

        $zona_horaria = dame_zona_horaria_local();

        $filtro_consulta = "";
        if ($seleccion_inclusion == VALOR_SI)
        {
            foreach ($periodos_fechas_inclusion as $periodo_fechas_inclusion)
            {
                if ($filtro_consulta != "")
                {
                    $filtro_consulta .= " OR ";
                }
                $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_local = $periodo_fechas_inclusion[0];
                $cadena_fecha_hora_fin_periodo_inclusion_base_datos_local = $periodo_fechas_inclusion[1];
                if (count(explode(" ", $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_local)) == 1)
                {
                    $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_local .= " 00:00";
                }
                if (count(explode(" ", $cadena_fecha_hora_fin_periodo_inclusion_base_datos_local)) == 1)
                {
                    $cadena_fecha_hora_fin_periodo_inclusion_base_datos_local .= " 23:59";
                }
                $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_local .= ":00";
                $cadena_fecha_hora_fin_periodo_inclusion_base_datos_local .= ":59";

                $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora(
                    $cadena_fecha_hora_inicio_periodo_inclusion_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);
                $cadena_fecha_hora_fin_periodo_inclusion_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora(
                    $cadena_fecha_hora_fin_periodo_inclusion_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);

                $filtro_consulta .= "
                    ((hora >= '".$cadena_fecha_hora_inicio_periodo_inclusion_base_datos_utc."')
                    AND (hora <= '".$cadena_fecha_hora_fin_periodo_inclusion_base_datos_utc."'))";
            }
            foreach ($periodos_dias_anyo_inclusion as $periodo_dias_anyo_inclusion)
            {
                if ($filtro_consulta != "")
                {
                    $filtro_consulta .= " OR ";
                }
                $dia_anyo_inicio_periodo_inclusion_base_datos = $periodo_dias_anyo_inclusion[0];
                $dia_anyo_fin_periodo_inclusion_base_datos = $periodo_dias_anyo_inclusion[1];
                $filtro_consulta .= "
                    ((DATE_FORMAT(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), '%m-%d') >= '".$dia_anyo_inicio_periodo_inclusion_base_datos."')
                    AND (DATE_FORMAT(CONVERT_TZ(hora, '".ZONA_HORARIA_UTC."', '".$zona_horaria."'), '%m-%d') <= '".$dia_anyo_fin_periodo_inclusion_base_datos."'))";
            }
        }
        return ($filtro_consulta);
    }


    //
    // Funciones de base de datos de clase de sensor
    //


    // Devuelve el nombre de la tabla de datos de la clase de sensor especificada
    function dame_nombre_tabla_datos_clase_sensor($clase_sensor)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $nombre_tabla = TABLA_DATOS_TEMPERATURA;
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                $nombre_tabla = TABLA_DATOS_HUMEDAD;
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                $nombre_tabla = TABLA_DATOS_LUZ_INTERIOR;
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                $nombre_tabla = TABLA_DATOS_VIENTO;
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $nombre_tabla = TABLA_DATOS_ENERGIA_ACTIVA;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $nombre_tabla = TABLA_DATOS_ENERGIA_REACTIVA;
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $nombre_tabla = TABLA_DATOS_CORTES_TENSION;
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $nombre_tabla = TABLA_DATOS_COMPRA_ENERGIA;
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $nombre_tabla = TABLA_DATOS_GAS;
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $nombre_tabla = TABLA_DATOS_AGUA;
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                $nombre_tabla = TABLA_DATOS_GENERICOS;
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }

        return ($nombre_tabla);
    }


    // Actualiza la hora en las tablas de recálculos de valores de clase de sensores
    function actualiza_hora_tablas_recalculos_valores_clase_sensores(
        $cadena_fecha_hora_base_datos_utc,
        $clase_sensor,
        $info_sensores)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Para cada uno de los sensores:
        // - Se eliminan las horas de recálculos anteriores (si existían)
        // - Se añaden las nuevas fechas de recálculos
        foreach ($info_sensores as $info_sensor)
        {
            $nombre_sensor = $info_sensor["nombre"];
            $tipo_sensor = $info_sensor["tipo"];
            $granularidad_cuartohoraria_sensor = $info_sensor["granularidad_cuartohoraria"];

            // Tablas de recálculos a modificar
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_COMPRA_ENERGIA:
                case CLASE_SENSOR_GAS:
                {
                    $tablas_recalculos = array();
                    if ($granularidad_cuartohoraria_sensor == VALOR_SI)
                    {
                        array_push($tablas_recalculos, TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR.SUFIJO_TABLA_CUARTOSHORA);
                    }
                    array_push($tablas_recalculos, TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR.SUFIJO_TABLA_HORAS);
                    break;
                }
                default:
                {
                    throw new Exception("La clase de sensor no tiene valores de clase: '".$clase_sensor."'");
                }
            }

            // Se guarda la información del recálculo en cada una de las tablas
            foreach ($tablas_recalculos as $tabla_recalculos)
            {
                if ($tipo_sensor == TIPO_SENSOR_PROCESADO)
                {
                    $valor_procesado = VALOR_SI;
                }
                else
                {
                    $valor_procesado = VALOR_NO;
                }

                $operacion_borrado_tabla_recalculos = "
                    DELETE
                    FROM ".$tabla_recalculos."
                    WHERE
                        (red = '".$_SESSION["id_red"]."')
                        AND (sensor = '".$bd_datos->_($nombre_sensor)."')";
                $res_borrado_tabla_recalculos = $bd_datos->ejecuta_operacion($operacion_borrado_tabla_recalculos);
                if ($res_borrado_tabla_recalculos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_tabla_recalculos."'");
                }

                $operacion_insercion_tabla_recalculos = "
                    INSERT INTO ".$tabla_recalculos." (
                        hora,
                        red,
                        sensor,
                        clase,
                        procesado
                    ) VALUES (
                        '".$bd_datos->_($cadena_fecha_hora_base_datos_utc)."',
                        '".$_SESSION["id_red"]."',
                        '".$bd_datos->_($nombre_sensor)."',
                        '".$bd_datos->_($clase_sensor)."',
                        '".$bd_datos->_($valor_procesado)."'
                    )";
                $res_insercion_tabla_recalculos = $bd_datos->ejecuta_operacion($operacion_insercion_tabla_recalculos);
                if ($res_insercion_tabla_recalculos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_tabla_recalculos."'");
                }
            }
        }
    }
?>
