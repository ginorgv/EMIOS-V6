<?php
    session_start();

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes


    // Sufijo del sensor asociado al sensor de compra de energía
    define("SUFIJO_SENSOR_ASOCIADO_SENSOR_COMPRA_ENERGIA", "consumo total bruto");


    //
    // Funciones de administración de elementos adicionales de sensores (según la clase de sensor)
    //


    function dame_posible_anyadir_elementos_adicionales_clase_sensor(
        $nombre_sensor,
        $clase_sensor,
        $parametros_clase,
        &$msg,
        &$aviso)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $posible_anyadir_elementos_adicionales = dame_posible_anyadir_elementos_adicionales_clase_sensor_compra_energia(
                    $nombre_sensor,
                    $parametros_clase,
                    $msg,
                    $aviso);
                break;
            }
            default:
            {
                $posible_anyadir_elementos_adicionales = true;
                break;
            }
        }
        return ($posible_anyadir_elementos_adicionales);
    }


    function anyade_elementos_adicionales_clase_sensor(
        $id_sensor,
        $nombre_sensor,
        $clase_sensor,
        &$parametros_clase)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Cadena con los parámetros de clase
        $cadena_parametros_clase = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_clase);

        // Se añaden los elementos adicionales según la clase de sensor
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                anyade_elementos_adicionales_clase_sensor_compra_energia(
                    $id_sensor,
                    $nombre_sensor,
                    $parametros_clase);
                break;
            }
            default:
            {
                break;
            }
        }

        // Si se han modificado los parámetros de clase de sensor, se actualizan
        $cadena_parametros_clase_modificados = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_clase);
        if ($cadena_parametros_clase != $cadena_parametros_clase_modificados)
        {
            $operacion_modificacion = "
                UPDATE sensores
                SET
                    parametros_clase = '".$bd_red->_($cadena_parametros_clase_modificados)."'
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }


    function dame_posible_modificar_elementos_adicionales_clase_sensor(
        $nombre_sensor,
        $clase_sensor,
        $parametros_clase,
        &$msg,
        &$aviso)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $posible_modificar_elementos_adicionales = dame_posible_modificar_elementos_adicionales_clase_sensor_compra_energia(
                    $nombre_sensor,
                    $parametros_clase,
                    $msg,
                    $aviso);
                break;
            }
            default:
            {
                $posible_modificar_elementos_adicionales = true;
                break;
            }
        }
        return ($posible_modificar_elementos_adicionales);
    }


    function modifica_elementos_adicionales_clase_sensor(
        $id_sensor,
        $nombre_sensor,
        $clase_sensor,
        &$parametros_clase)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                modifica_elementos_adicionales_clase_sensor_compra_energia(
                    $id_sensor,
                    $nombre_sensor,
                    $parametros_clase);
                break;
            }
            default:
            {
                break;
            }
        }
    }


    function dame_posible_eliminar_elementos_adicionales_clase_sensor(
        $nombre_sensor,
        $clase_sensor,
        $parametros_clase,
        &$msg)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $posible_eliminar_elementos_adicionales = dame_posible_eliminar_elementos_adicionales_clase_sensor_compra_energia(
                    $nombre_sensor,
                    $parametros_clase,
                    $msg);
                break;
            }
            default:
            {
                $posible_eliminar_elementos_adicionales = true;
                break;
            }
        }
        return ($posible_eliminar_elementos_adicionales);
    }


    function elimina_elementos_adicionales_clase_sensor(
        $id_sensor,
        $nombre_sensor,
        $clase_sensor,
        &$parametros_clase)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                elimina_elementos_adicionales_clase_sensor_compra_energia(
                    $id_sensor,
                    $nombre_sensor,
                    $parametros_clase);
                break;
            }
            default:
            {
                break;
            }
        }
    }


    //
    // Funciones de administración de elementos adicionales de sensores de compra de energía
    //


    function dame_posible_anyadir_elementos_adicionales_clase_sensor_compra_energia(
        $nombre_sensor,
        $parametros_clase,
        &$msg,
        &$aviso)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Comprobaciones a realizar:
        // - 1: Se comprueba que los sensores hijos no estén asignados a ningún otro sensor de compra de energía y
        //   que no sean sensores asociados de sensores de compra de energía
        // - 2: Se comprueba que no exista ya un sensor de energía activa con el mismo nombre que el nombre que se le va a asignar al sensor asociado

        // Flag de posible añadir elementos adicionales
        $posible_anyadir_elementos_adicionales = true;

        // Se comprueba que los sensores hijos no estén asignados a ningún otro sensor de compra de energía y
        // que no sean sensores asociados de sensores de compra de energía
        if ($posible_anyadir_elementos_adicionales == true)
        {
            $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
            $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);

            $consulta_sensores = "
                SELECT
                    nombre,
                    parametros_clase
                FROM sensores
                WHERE
                    (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                    AND (red = '".$_SESSION["id_red"]."')
                ORDER BY nombre ASC";
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $nombre_sensor_bucle = $fila_sensor["nombre"];
                $cadena_parametros_clase_bucle = $fila_sensor["parametros_clase"];
                $parametros_clase_bucle = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_bucle);
                $cadena_ids_sensores_hijos_bucle = $parametros_clase_bucle[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                $ids_sensores_hijos_bucle = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos_bucle);
                $id_sensor_asociado_bucle = $parametros_clase_bucle[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];

                $ids_sensores_hijos_comunes = array_intersect($ids_sensores_hijos, $ids_sensores_hijos_bucle);
                if (count($ids_sensores_hijos_comunes) > 0)
                {
                    $posible_anyadir_elementos_adicionales = false;
                    $msg = $idiomas->_("Hay sensores hijos asignados a otro sensor de compra de energía")."\n(".
                        $nombre_sensor_bucle.")";
                    break;
                }
                if (in_array($id_sensor_asociado_bucle, $ids_sensores_hijos) == true)
                {
                    $posible_anyadir_elementos_adicionales = false;
                    $msg = $idiomas->_("Los sensores hijos no pueden ser sensor asociado de un sensor de compra de energía")."\n(".
                        $nombre_sensor_bucle.")";
                    break;
                }
            }

            // Se muestra un aviso si algún sensor hijo no tiene tarifa eléctrica asignada
            $cadena_ids_sensores_hijos_consulta = dame_cadena_ids_consulta($ids_sensores_hijos);
            $consulta_sensores_hijos = "
                SELECT
                    nombre,
                    parametros_clase
                FROM sensores
                WHERE
                    id IN (".$cadena_ids_sensores_hijos_consulta.")
                ORDER BY nombre ASC";
            $res_sensores_hijos = $bd_red->ejecuta_consulta($consulta_sensores_hijos);
            while ($fila_sensor_hijo = $res_sensores_hijos->dame_siguiente_fila())
            {
                $nombre_sensor_bucle = $fila_sensor_hijo["nombre"];
                $cadena_parametros_clase_bucle = $fila_sensor_hijo["parametros_clase"];
                $parametros_clase_bucle = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_bucle);
                $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, $parametros_clase_bucle);
                if ($id_tarifa == ID_NINGUNO)
                {
                    $aviso = $idiomas->_("los sensores hijos deben tener tarifa eléctrica asignada para poder realizar los cálculos de compra de energía")."\n(".
                        $nombre_sensor_bucle.")";
                    break;
                }
            }
        }

        // Se comprueba que no exista ya un sensor de energía activa con el mismo nombre que el nombre que se le va a asignar al sensor asociado
        if ($posible_anyadir_elementos_adicionales == true)
        {
            $nombre_sensor_asociado = $nombre_sensor." (".$idiomas->_(SUFIJO_SENSOR_ASOCIADO_SENSOR_COMPRA_ENERGIA).")";
            $consulta_existe = "
                SELECT nombre
                FROM sensores
                WHERE
                    (nombre = '".$bd_red->_($nombre_sensor_asociado)."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
            if ($res_existe == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_existe."'");
            }
            if ($res_existe->dame_numero_filas() > 0)
            {
                $posible_anyadir_elementos_adicionales = false;
                $msg = $idiomas->_("Ya existe un sensor con el mismo nombre que el sensor asociado")."\n(".
                    $nombre_sensor_asociado.")";
            }
        }

        // Se devuelve si es posible añadir los elementos adicionales
        return ($posible_anyadir_elementos_adicionales);
    }


    function anyade_elementos_adicionales_clase_sensor_compra_energia(
        $id_sensor,
        $nombre_sensor,
        &$parametros_clase)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se realizan las siguientes acciones:
        // - 1. Se añade el sensor asociado
        // - 2. Se añaden los hijos de sensor al sensor asociado
        // - 3. Se realizan las acciones de adición del sensor asociado

        // Se recupera la fila del sensor de compra de energía
        $fila_sensor = dame_fila_sensor($id_sensor);

        // Información del sensor asociado
        $nombre_sensor_asociado = $nombre_sensor." (".$idiomas->_(SUFIJO_SENSOR_ASOCIADO_SENSOR_COMPRA_ENERGIA).")";
        $descripcion_sensor_asociado = $idiomas->_("Sensor asociado al sensor de compra de energía correspondiente").
            " (".$idiomas->_(SUFIJO_SENSOR_ASOCIADO_SENSOR_COMPRA_ENERGIA).")";
        $tipo_sensor_asociado = TIPO_SENSOR_PROCESADO;
        $parametros_tipo_sensor_asociado = array(
            CLASE_SENSOR_PROCESADO_SUMA_VALORES,
            "",
            VALOR_SI,
            "");
        $cadena_parametros_tipo_sensor_asociado = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_sensor_asociado);
        $clase_sensor_asociado = CLASE_SENSOR_ENERGIA_ACTIVA;
        $parametros_clase_sensor_asociado = array(
            ID_NINGUNO,
            ID_NINGUNO,
            "",
            0,
            0,
            0,
            TIPO_NINGUNO,
            "");
        $cadena_parametros_clase_sensor_asociado = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_clase_sensor_asociado);
        $id_grupo_sensor_asociado = ID_NINGUNO;
        $id_localizacion_sensor_asociado = $fila_sensor["localizacion"];
        $visible_localizaciones_hijas_sensor_asociado = $fila_sensor["visible_localizaciones_hijas"];
        $tipo_valores_sensor_asociado = TIPO_VALORES_SENSOR_INCREMENTALES;
        $cambio_valores_puntuales_sensor_asociado = CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL;
        $incrementos_tiempo_real_horarios_sensor_asociado = VALOR_SI;
        $incrementos_negativos_validos_sensor_asociado = VALOR_NO;
        $guardar_valores_base_datos_sensor_asociado = VALOR_SI;
        $notificar_todos_eventos_sensor_asociado = VALOR_SI;
        $granularidad_cuartohoraria_sensor_asociado = VALOR_NO;

        // Nota: El orden del sensor asociado en principio se establece a un valor máximo (p.e. 999) porque no se comprueba el orden de los sensores hijos
        // que se van a añadir
        $orden_sensor_asociado = 999;

        // Se añade el sensor asociado
        $operacion_insercion_sensor_asociado = "
            INSERT INTO sensores (
                nombre,
                red,
                descripcion,
                tipo,
                parametros_tipo,
                clase,
                parametros_clase,
                orden,
                grupo,
                localizacion,
                visible_localizaciones_hijas,
                frecuencia_muestreo,
                frecuencia_envio,
                calibracion,
                tipo_valores,
                cambio_valores_puntuales,
                incrementos_tiempo_real_horarios,
                incrementos_negativos_validos,
                guardar_valores_base_datos,
                notificar_todos_eventos,
                granularidad_cuartohoraria,
                administrable,
                hora_ultimos_valores,
                ultimos_valores,
                eventos_activados,
                eventos_alarma_activados,
                hora_ultimos_valores_clase_cuartoshora,
                ultimos_valores_clase_cuartoshora,
                eventos_activados_clase_cuartoshora,
                eventos_alarma_activados_clase_cuartoshora,
                hora_ultimos_valores_clase_horas,
                ultimos_valores_clase_horas,
                eventos_activados_clase_horas,
                eventos_alarma_activados_clase_horas,
                hora_timeout_envio,
                timeout_envio,
                ultimo_error_valores_tiempo_real_json,
                ultimo_error_valores_horarios_json,
                ultimo_error_valores_cuartohorarios_json,
                ultimo_error_valores_clase_horarios_json,
                ultimo_error_valores_clase_cuartohorarios_json
            ) VALUES (
                '".$bd_red->_($nombre_sensor_asociado)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion_sensor_asociado)."',
                '".$bd_red->_($tipo_sensor_asociado)."',
                '".$bd_red->_($cadena_parametros_tipo_sensor_asociado)."',
                '".$bd_red->_($clase_sensor_asociado)."',
                '".$bd_red->_($cadena_parametros_clase_sensor_asociado)."',
                '".$bd_red->_($orden_sensor_asociado)."',
                '".$bd_red->_($id_grupo_sensor_asociado)."',
                '".$bd_red->_($id_localizacion_sensor_asociado)."',
                '".$bd_red->_($visible_localizaciones_hijas_sensor_asociado)."',
                '".$bd_red->_(0)."',
                '".$bd_red->_(0)."',
                '".$bd_red->_("")."',
                '".$bd_red->_($tipo_valores_sensor_asociado)."',
                '".$bd_red->_($cambio_valores_puntuales_sensor_asociado)."',
                '".$bd_red->_($incrementos_tiempo_real_horarios_sensor_asociado)."',
                '".$bd_red->_($incrementos_negativos_validos_sensor_asociado)."',
                '".$bd_red->_($guardar_valores_base_datos_sensor_asociado)."',
                '".$bd_red->_($notificar_todos_eventos_sensor_asociado)."',
                '".$bd_red->_($granularidad_cuartohoraria_sensor_asociado)."',
                '".VALOR_NO."',
                NULL,
                NULL,
                '',
                '".VALOR_NO."',
                NULL,
                NULL,
                '',
                '".VALOR_NO."',
                NULL,
                NULL,
                '',
                '".VALOR_NO."',
                NULL,
                '".VALOR_NO."',
                '',
                '',
                '',
                '',
                ''
            )";
        $res_insercion_sensor_asociado = $bd_red->ejecuta_operacion($operacion_insercion_sensor_asociado);
        if ($res_insercion_sensor_asociado == true)
        {
            // Se recuperan el id y la fila del sensor añadido
            $id_sensor_asociado = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_sensor_asociado = dame_fila_sensor($id_sensor_asociado);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_sensor_asociado."'");
        }

        // Se guarda el identificador de sensor asociado en los parámetros de clase del sensor de compra de energía
        $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO] = $id_sensor_asociado;

        // Se añaden los hijos de sensor al sensor asociado
        $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
        $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
        $numero_sensor_hijo = 0;
        foreach ($ids_sensores_hijos as $id_sensor_hijo)
        {
            $numero_sensor_hijo += 1;

            // Información del hijo de sensor
            $parametros_tipo_hijo_sensor = array(
                CAMPO_INCREMENTO,
                FUNCION_HIJO_SENSOR_PROCESADO_CONSUMO_ENERGIA_BRUTO,
                "",
                "x".$numero_sensor_hijo,
                VALOR_SI);
            $cadena_parametros_tipo_hijo_sensor = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_hijo_sensor);

            // Se añade el hijo de sensor
            $operacion_insercion_hijo_sensor = "
                INSERT INTO hijos_sensores (
                    red,
                    sensor_padre,
                    sensor_hijo,
                    parametros_tipo
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_sensor_asociado)."',
                    '".$bd_red->_($id_sensor_hijo)."',
                    '".$bd_red->_($cadena_parametros_tipo_hijo_sensor)."'
                )";
            $res_insercion_hijo_sensor = $bd_red->ejecuta_operacion($operacion_insercion_hijo_sensor);
            if ($res_insercion_hijo_sensor == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_hijo_sensor."'");
            }
        }

        // Se realizan las acciones de adición del sensor asociado
        realiza_acciones_sensor_anyadido($id_sensor_asociado, $fila_sensor_asociado);
    }


    function dame_posible_modificar_elementos_adicionales_clase_sensor_compra_energia(
        $nombre_sensor,
        $parametros_clase,
        &$msg,
        &$aviso)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Comprobaciones a realizar:
        // - 1: Se comprueba que los sensores hijos no estén asignados a ningún otro sensor de compra de energía y
        //   que no sean sensores asociados de sensores de compra de energía

        // Flag de posible modificar elementos adicionales
        $posible_modificar_elementos_adicionales = true;

        // Se comprueba que los sensores hijos no estén asignados a ningún otro sensor de compra de energía y
        // que no sean sensores asociados de sensores de compra de energía
        if ($posible_modificar_elementos_adicionales == true)
        {
            $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
            $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);

            $consulta_sensores = "
                SELECT
                    nombre,
                    parametros_clase
                FROM sensores
                WHERE
                    (clase = '".CLASE_SENSOR_COMPRA_ENERGIA."')
                    AND (red = '".$_SESSION["id_red"]."')
                ORDER BY nombre ASC";
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $nombre_sensor_bucle = $fila_sensor["nombre"];
                $cadena_parametros_clase_bucle = $fila_sensor["parametros_clase"];
                $parametros_clase_bucle = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_bucle);
                $cadena_ids_sensores_hijos_bucle = $parametros_clase_bucle[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
                $ids_sensores_hijos_bucle = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos_bucle);
                $id_sensor_asociado_bucle = $parametros_clase_bucle[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];

                if ($nombre_sensor_bucle != $nombre_sensor)
                {
                    $ids_sensores_hijos_comunes = array_intersect($ids_sensores_hijos, $ids_sensores_hijos_bucle);
                    if (count($ids_sensores_hijos_comunes) > 0)
                    {
                        $posible_modificar_elementos_adicionales = false;
                        $msg = $idiomas->_("Hay sensores hijos asignados a otro sensor de compra de energía")."\n(".
                            $nombre_sensor_bucle.")";
                        break;
                    }
                }
                if (in_array($id_sensor_asociado_bucle, $ids_sensores_hijos) == true)
                {
                    $posible_modificar_elementos_adicionales = false;
                    $msg = $idiomas->_("Los sensores hijos no pueden ser sensor asociado de un sensor de compra de energía")."\n(".
                        $nombre_sensor_bucle.")";
                    break;
                }
            }

            // Se muestra un aviso si algún sensor hijo no tiene tarifa eléctrica asignada
            $cadena_ids_sensores_hijos_consulta = dame_cadena_ids_consulta($ids_sensores_hijos);
            $consulta_sensores_hijos = "
                SELECT
                    nombre,
                    parametros_clase
                FROM sensores
                WHERE
                    id IN (".$cadena_ids_sensores_hijos_consulta.")
                ORDER BY nombre ASC";
            $res_sensores_hijos = $bd_red->ejecuta_consulta($consulta_sensores_hijos);
            while ($fila_sensor_hijo = $res_sensores_hijos->dame_siguiente_fila())
            {
                $nombre_sensor_bucle = $fila_sensor_hijo["nombre"];
                $cadena_parametros_clase_bucle = $fila_sensor_hijo["parametros_clase"];
                $parametros_clase_bucle = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_bucle);
                $id_tarifa = dame_id_tarifa_parametros_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA, $parametros_clase_bucle);
                if ($id_tarifa == ID_NINGUNO)
                {
                    $aviso = $idiomas->_("los sensores hijos deben tener tarifa eléctrica asignada para poder realizar los cálculos de compra de energía")."\n(".
                        $nombre_sensor_bucle.")";
                    break;
                }
            }
        }

        // Se devuelve si es posible modificar los elementos adicionales
        return ($posible_modificar_elementos_adicionales);
    }


    function modifica_elementos_adicionales_clase_sensor_compra_energia(
        $id_sensor,
        $nombre_sensor,
        &$parametros_clase)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se realizan las siguientes acciones:
        // - 1. Se actualiza la localización del sensor asociado (por si hubiera cambiado)
        // - 2. Se actualizan los hijos de sensores del sensor asociado
        // - 3. Se realizan las acciones del sensor modificado

        // Parámetros de clase
        $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
        $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
        $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);

        // Fila del sensor asociado anterior
        $fila_sensor_asociado_anterior = dame_fila_sensor($id_sensor_asociado);

        // Se actualiza la localización del sensor asociado (por si hubiera cambiado)
        $fila_sensor = dame_fila_sensor($id_sensor);
        $id_localizacion_sensor = $fila_sensor["localizacion"];
        $operacion_modificacion_sensor_asociado = "
            UPDATE sensores
            SET
                localizacion = '".$bd_red->_($id_localizacion_sensor)."'
            WHERE
                id = '".$bd_red->_($id_sensor_asociado)."'";
        $res_modificacion_sensor_asociado = $bd_red->ejecuta_operacion($operacion_modificacion_sensor_asociado);
        if ($res_modificacion_sensor_asociado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion_sensor_asociado."'");
        }

        // Se actualizan los hijos de sensores del sensor asociado
        // (se eliminan los anteriores y se añaden los actuales)

        // Se elimina los hijos de sensor
        $operacion_borrado_hijos_sensores = "
            DELETE
            FROM hijos_sensores
            WHERE
                sensor_padre = '".$bd_red->_($id_sensor_asociado)."'";
        $res_borrado_hijos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_hijos_sensores);
        if ($res_borrado_hijos_sensores == false)
        {
            throw new Exception("Error en la operación: '".$res_borrado_hijos_sensores."'");
        }

        // Se añaden los hijos de sensor al sensor asociado
        $numero_sensor_hijo = 0;
        foreach ($ids_sensores_hijos as $id_sensor_hijo)
        {
            $numero_sensor_hijo += 1;

            // Información del hijo de sensor
            $parametros_tipo_hijo_sensor = array(
                CAMPO_INCREMENTO,
                FUNCION_HIJO_SENSOR_PROCESADO_CONSUMO_ENERGIA_BRUTO,
                "",
                "x".$numero_sensor_hijo,
                VALOR_SI);
            $cadena_parametros_tipo_hijo_sensor = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_hijo_sensor);

            // Se añade el hijo de sensor
            $operacion_insercion_hijo_sensor = "
                INSERT INTO hijos_sensores (
                    red,
                    sensor_padre,
                    sensor_hijo,
                    parametros_tipo
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_sensor_asociado)."',
                    '".$bd_red->_($id_sensor_hijo)."',
                    '".$bd_red->_($cadena_parametros_tipo_hijo_sensor)."'
                )";
            $res_insercion_hijo_sensor = $bd_red->ejecuta_operacion($operacion_insercion_hijo_sensor);
            if ($res_insercion_hijo_sensor == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_hijo_sensor."'");
            }
        }

        // Se realizan las acciones de modificación del sensor asociado
        $fila_sensor_asociado_actual = dame_fila_sensor($id_sensor_asociado);
        realiza_acciones_sensor_modificado(
            $id_sensor_asociado,
            $fila_sensor_asociado_anterior,
            $fila_sensor_asociado_actual);
    }


    function dame_posible_eliminar_elementos_adicionales_clase_sensor_compra_energia(
        $nombre_sensor,
        $parametros_clase,
        &$msg,
        &$aviso)
    {
        $idiomas = new Idiomas();

        // Comprobaciones a realizar:
        // - 1: Se comprueba si se puede eliminar el sensor asociado

        // Flag de posible eliminar elementos adicionales
        $posible_eliminar_elementos_adicionales = true;

        // Se comprueba si se puede eliminar el sensor asociado
        if ($posible_eliminar_elementos_adicionales == true)
        {
            // Nota: La comprobación de si existe el sensor asociado no debería ser necesaria cuando se añade el sensor asociado
            // al añadir el sensor de compra de energía
            $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
            if ($id_sensor_asociado != ID_NINGUNO)
            {
                $fila_sensor_asociado = dame_fila_sensor($id_sensor_asociado);

                $msg = "";
                $sufijo_mensaje_aviso = $idiomas->_("sensor asociado").": ".$nombre_sensor;
                $posible_eliminar_elementos_adicionales = dame_posible_eliminar_sensor(
                    $id_sensor_asociado,
                    $fila_sensor_asociado,
                    $msg,
                    $sufijo_mensaje_aviso);
            }
        }

        // Se devuelve si es posible eliminar los elementos adicionales
        return ($posible_eliminar_elementos_adicionales);
    }


    function elimina_elementos_adicionales_clase_sensor_compra_energia(
        $id_sensor,
        $nombre_sensor,
        &$parametros_clase)
    {
        // Se realizan las siguientes acciones:
        // - 1. Se elimina el sensor asociado (y se realizan las acciones correspondientes al eliminar el sensor)
        // - 2. Se realizan las acciones de eliminación del sensor asociado

        // Se recupera la fila del sensor asociado
        $id_sensor_asociado = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
        $fila_sensor_asociado = dame_fila_sensor($id_sensor_asociado);

        // Se elimina el sensor asociado
        elimina_sensor($id_sensor_asociado, $fila_sensor_asociado);
    }
?>