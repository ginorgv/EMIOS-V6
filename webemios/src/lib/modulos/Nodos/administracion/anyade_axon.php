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
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_AXON, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    $log=dame_log();
    // Parámetros
    $nombre = $_POST["nombre"];
    $id_dispositivo = $_POST["id_dispositivo"];

	// Se comprueba si existe un axón con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM axones
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
        $msg = $idiomas->_("Ya existe un axón con el mismo nombre");
    }
    else
    {
        // Comprobar si ya hay un axón en el dispositivo (no se pueden tener más de 1 axón en un dispositivo)
        $consulta_axones = "
            SELECT nombre
            FROM axones
            WHERE
                (dispositivo = '".$bd_red->_($id_dispositivo)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_axones = $bd_red->ejecuta_consulta($consulta_axones);
        if ($res_axones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_axones."'");
        }
        if ($res_axones->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un axón en el dispositivo");
        }
        else
        {
            // Se añade el axón
            $operacion_insercion = "
                INSERT INTO axones (
                    nombre,
                    red,
                    conexion,
                    dispositivo
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    'NA',
                    '".$bd_red->_($id_dispositivo)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                $res = "OK";
                $msg = $idiomas->_("Axón añadido correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        } 
    }
/*    
    // Se comprueba si el dispositivo asociado es de tipo 'ARQUITECTURA_DISPOSITIVO_BYE_RADON'

    $consulta_arquitectura_dispositivo = "
            SELECT *
            FROM dispositivos
            WHERE
                (id = '".$bd_red->_($id_dispositivo)."')
                AND (red = '".$_SESSION["id_red"]."')";
    $res_arquitectura = $bd_red->ejecuta_consulta($consulta_arquitectura_dispositivo);
    if ($res_arquitectura == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_arquitectura_dispositivo."'");
    }
    if ($res_arquitectura->dame_numero_filas() > 0)
    {
        
        $fila_dispositivo = $res_arquitectura->dame_siguiente_fila();
     // Si lo es, se crean 3 sensores asociados al dispositivo
        if ($fila_dispositivo['arquitectura'] == ARQUITECTURA_DISPOSITIVO_BYE_RADON)
        {
            $log->warn("Hasta aqui llegamos");
            $nombre_sensor_temp = $fila_dispositivo['imei']."-Temp";
            $nombre_sensor_hum = $fila_dispositivo['imei']."-Hum";
            $nombre_sensor_radon = $fila_dispositivo['imei']."-Radon";
         // IDs sensor 
            $id_mensaje_mqtt_temp = 10001;
            $id_mensaje_mqtt_hum = 10002;
            $id_mensaje_mqtt_radon = 10003;
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
                            timeout_envio
                            ) VALUES (
                                '".$bd_red->_($nombre_sensor_temp)."',
                                '".$_SESSION["id_red"]."',
                                '".TIPO_SENSOR_EXTERNO."',
                                '".$bd_red->_($parametros_tipo_temperatura)."',
                                '0',
                                '-1',
                                '-1',
                                '0',
                                '0',
                                '900',
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
                                '0'),
                                (
                                '".$bd_red->_($nombre_sensor_hum)."',
                                '".$_SESSION["id_red"]."',
                                '".TIPO_SENSOR_EXTERNO."',
                                '".$bd_red->_($parametros_tipo_humedad)."',
                                '0',
                                '-1',
                                '-1',
                                '0',
                                '0',
                                '900',
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
                                '0'),
                                (
                                '".$bd_red->_($nombre_sensor_radon)."',
                                '".$_SESSION["id_red"]."',
                                '".TIPO_SENSOR_EXTERNO."',
                                '".$bd_red->_($parametros_tipo_radon)."',
                                '0',
                                '-1',
                                '-1',
                                '0',
                                '0',
                                '900',
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
                    $log = dame_log();
                    $log->error("Consulta exitosa");
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
*/
    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
