<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_dispositivos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_DISPOSITIVO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $arquitectura = $_POST["arquitectura"];
    $direccion_IMEI = $_POST["direccion_IMEI"];
    $id_localizacion = $_POST["id_localizacion"];
    $frecuencia_actualizacion = $_POST["frecuencia_actualizacion"];
    $frecuencia_envio_estado = $_POST["frecuencia_envio_estado"];
    $mostrar_en_mapa = $_POST["mostrar_en_mapa"];
    $latitud_mapa = $_POST["latitud_mapa"];
    $longitud_mapa = $_POST["longitud_mapa"];
    $zoom_mapa = $_POST["zoom_mapa"];

    // Se comprueba si existen el número máximo de dispositivos
    $consulta_numero_dispositivos = "
        SELECT
            COUNT(*) AS numero_dispositivos
        FROM dispositivos
        WHERE
            red = '".$_SESSION["id_red"]."'";
    $res_numero_dispositivos = $bd_red->ejecuta_consulta($consulta_numero_dispositivos);
    if (($res_numero_dispositivos == false) || ($res_numero_dispositivos->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_dispositivos."'");
    }
    $fila_numero_dispositivos = $res_numero_dispositivos->dame_siguiente_fila();
    $numero_maximo_dispositivos = dame_numero_maximo_elementos_modulo(MODULO_RED);
    if (($numero_maximo_dispositivos != 0) &&
        ($fila_numero_dispositivos['numero_dispositivos'] >= $numero_maximo_dispositivos))
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen el número máximo de dispositivos");
    }
    else
    {
        // Se comprueba si existe un dispositivo con el mismo nombre
        $consulta_existe = "
            SELECT nombre
            FROM dispositivos
            WHERE
                (nombre = '".$bd_red->_($nombre)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
        if ($res_existe == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_existe."'");
        }
        if ($res_existe->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un dispositivo con el mismo nombre");
        }
        else
        {
            // Se añade el dispositivo BYE_RADON
            $operacion_insercion = "
                INSERT INTO dispositivos (
                    nombre,
                    red,
                    descripcion,
                    arquitectura,
                    frecuencia_actualizacion,
                    frecuencia_envio_estado,
                    conexion,
                    latencia,
                    hora_ultimo_estado,
                    ultimo_estado,
                    hora_timeout_envio_estado,
                    timeout_envio_estado,
                    imei,
                    localizacion
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($arquitectura)."',
                    '".$bd_red->_($frecuencia_actualizacion)."',
                    '".$bd_red->_($frecuencia_envio_estado)."',
                    'NA',
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    '0',
                    '".$bd_red->_($direccion_IMEI)."',
                    '".$bd_red->_($id_localizacion)."'
                )";

            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recupera el id del dispositivo añadido
                $id_dispositivo = $bd_red->dame_id_autoincremental_ultima_insercion();

                // Se guarda la información de la posición en el mapa
                if ($mostrar_en_mapa == VALOR_SI)
                {
                    // Se recupera el origen del mapa 'final'
                    $parametros_origen_mapa = array("modulo" => MODULO_SENSORES);
                    $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
                    $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
                    $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

                    // Se guarda la información de la posición en el mapa en base de datos
                    $info_posicion_mapa = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_DISPOSITIVO,
                        "id_elemento" => $id_dispositivo,
                        "origen" => ORIGEN_MAPA_RED,
                        "id_origen" => $_SESSION["id_red"],
                        "latitud" => $latitud_mapa,
                        "longitud" => $longitud_mapa,
                        "zoom" => $zoom_mapa);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
                }
                else
                {
                    $info_posicion_mapa = NULL;
                }


                $res = "OK";
                $msg = $idiomas->_("Dispositivo añadido correctamente");


                // PARA LA DEMO
                // Se comprueba si existe un axón con el mismo nombre
                // Comprobar si ya hay un axón en el dispositivo (no se pueden tener más de 1 axón en un dispositivo)
                
                $nombre_sensor_temp = $direccion_IMEI."-Temp";
                $nombre_sensor_hum = $direccion_IMEI."-Hum";
                $nombre_sensor_radon = $direccion_IMEI."-Radon";

                // IDs sensor 
                # Se obtiene el ultimo valor del sensor externo

                $consulta_id_externo = "
                SELECT
                    MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1), SIGNED INTEGER)) AS id_externo
                FROM sensores
                WHERE
                    tipo = '".TIPO_SENSOR_EXTERNO."'";
                $res_id_externo = $bd_red->ejecuta_consulta($consulta_id_externo);
                if ($res_id_externo == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_id_externo."'");
                }
                $fila_id_externo = $res_id_externo->dame_siguiente_fila();
                $id_externo = $fila_id_externo["id_externo"];
                if ($id_externo === NULL)
                {
                    $id_externo = 1;
                }
                else
                {
                    $id_externo += 1;
                }

                $id_mensaje_mqtt_temp = $id_externo;
                $id_mensaje_mqtt_hum = $id_mensaje_mqtt_temp+1;
                $id_mensaje_mqtt_radon = $id_mensaje_mqtt_hum+1;


                $parametros_tipo_temperatura = $id_mensaje_mqtt_temp."&NINGUNA&&";
                $parametros_tipo_humedad = $id_mensaje_mqtt_hum."&NINGUNA&&";
                $parametros_tipo_radon = $id_mensaje_mqtt_radon."&NINGUNA&&";
        
                $tipo = TIPO_SENSOR_EXTERNO;
        
                // Se comprueba si existe un sensor con el mismo nombre
                $consulta_existe = "
                    SELECT nombre
                    FROM sensores
                    WHERE
                        (nombre = '".$bd_red->_($nombre_sensor_temp)."')
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
                if ($res_existe == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_existe."'");
                }
                if ($res_existe->dame_numero_filas() > 0)
                {
                    $res = "ERROR";
                    $msg = $idiomas->_("Ya existe un sensor con el mismo nombre");
                }
                else
                {
                // Identificador de sensor externo único
                    if ($anyadir_sensor == true)
                    {
                        switch ($tipo)
                        {
                            case TIPO_SENSOR_EXTERNO:
                            {
                                // Id de sensor externo
                                $id_sensor_externo = $parametros_tipo_temperatura[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
                                // Se comprueba si existe un sensor externo con el mismo identificador externo
                                $consulta_existe = "
                                    SELECT
                                        nombre,
                                        red
                                    FROM sensores
                                    WHERE
                                        (tipo = '".TIPO_SENSOR_EXTERNO."')
                                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_mensaje_mqtt_temp)."')";
                                $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
                                if ($res_existe == false)
                                {
                                    throw new Exception("Error en la consulta: '".$consulta_existe."'");
                                }
                                if ($res_existe->dame_numero_filas() > 0)
                                {
                                    $anyadir_sensor = false;
                                    $fila_sensor_externo = $res_existe->dame_siguiente_fila();
                                    $nombre_sensor_externo = $fila_sensor_externo["nombre"];
                                    $id_red_sensor_externo = $fila_sensor_externo["red"];
                                    $res = "ERROR";
                                    if ($id_red_sensor_externo == $_SESSION["id_red"])
                                    {
                                        $msg = $idiomas->_("Ya existe un sensor externo con el mismo identificador externo")."\n(".
                                            $nombre_sensor_externo.")";
                                    }
                                    else
                                    {
                                        $msg = $idiomas->_("Ya existe un sensor externo con el mismo identificador externo en otra red");
                                    }
                                }
                                break;
                            }
                        }
                    }
            $insercion_sensores_dispositivos = "
                    INSERT INTO sensores (
                        nombre,
                        red,
                        tipo,
                        parametros_tipo,
                        orden,
                        grupo,
                        localizacion,
                        visible_localizaciones_hijas,
                        frecuencia_muestreo,
                        frecuencia_envio,
                        calibracion,
                        clase,
                        parametros_clase,
                        tipo_valores,
                        cambio_valores_puntuales,
                        incrementos_tiempo_real_horarios,
                        incrementos_negativos_validos,
                        guardar_valores_base_datos,
                        notificar_todos_eventos,
                        granularidad_cuartohoraria,
                        administrable,
                        eventos_activados,
                        timeout_envio
                        ) VALUES (
                            '".$bd_red->_($nombre_sensor_temp)."',
                            '".$_SESSION["id_red"]."',
                            '".TIPO_SENSOR_EXTERNO."',
                            '".$bd_red->_($parametros_tipo_temperatura)."',
                            '0',
                            '-1',
                            '".$bd_red->_($id_localizacion)."',
                            '0',
                            '0',
                            '".$bd_red->_($frecuencia_actualizacion)."',
                            '',
                            '".CLASE_SENSOR_TEMPERATURA."',
                            '',
                            '".TIPO_VALORES_SENSOR_PUNTUALES."',
                            '".CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL."',
                            '1',
                            '0',
                            '1',
                            '1',
                            '0',
                            '1',
                            '',
                            '0'),
                            (
                            '".$bd_red->_($nombre_sensor_hum)."',
                            '".$_SESSION["id_red"]."',
                            '".TIPO_SENSOR_EXTERNO."',
                            '".$bd_red->_($parametros_tipo_humedad)."',
                            '0',
                            '-1',
                            '".$bd_red->_($id_localizacion)."',
                            '0',
                            '0',
                            '".$bd_red->_($frecuencia_actualizacion)."',
                            '',
                            '".CLASE_SENSOR_HUMEDAD."',
                            '',
                            '".TIPO_VALORES_SENSOR_PUNTUALES."',
                            '".CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL."',
                            '1',
                            '0',
                            '1',
                            '1',
                            '0',
                            '1',
                            '',
                            '0'),
                            (
                            '".$bd_red->_($nombre_sensor_radon)."',
                            '".$_SESSION["id_red"]."',
                            '".TIPO_SENSOR_EXTERNO."',
                            '".$bd_red->_($parametros_tipo_radon)."',
                            '0',
                            '-1',
                            '".$bd_red->_($id_localizacion)."',
                            '0',
                            '0',
                            '".$bd_red->_($frecuencia_actualizacion)."',
                            '',
                            '".CLASE_SENSOR_GENERICA."',
                            'Radon&becquerels&Sensor&AZUL_ROJO&AZUL_ROJO&1',
                            '".TIPO_VALORES_SENSOR_PUNTUALES."',
                            '".CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL."',
                            '1',
                            '1',
                            '1',
                            '1',
                            '1',
                            '1',
                            '',
                            '0')";
            $res_insercion_sensores = $bd_red->ejecuta_operacion($insercion_sensores_dispositivos);
            if ($res_insercion_sensores == true)
            {
                // Se recupera el id del sensor añadido
                $id_sensor = $bd_red->dame_id_autoincremental_ultima_insercion();
                for ($i=0; $i < 3; $i++) { 
                    // Se recupera la fila del sensor añadido
                    $fila_sensor = dame_fila_sensor($id_sensor);
                    // Acciones a realizar al añadir un sensor
                    realiza_acciones_sensor_anyadido($id_sensor, $fila_sensor);
                    $id_sensor +=1;
                }
                // Se envía mensaje MQTT de administración de dispositivo

                notifica_servidor_remoto_subscripcion_dispositivo(OPERACION_ADICION, $direccion_IMEI, array($id_mensaje_mqtt_temp,$id_mensaje_mqtt_hum,$id_mensaje_mqtt_radon));


                $log = dame_log();
                $log->info("Consulta exitosa");
                $res = "OK";
                $msg = $idiomas->_("Axón Bye Radon añadido correctamente");
            }
            else
            {   
                $log = dame_log();
                $log->error("Consulta erronea");
                $res = "ERROR";
                $msg = $idiomas->_("Fallo con la consulta ".$insercion_sensores_dispositivos);
            }
            }
                            }
                        }
                    }
    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
