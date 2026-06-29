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
    $direccion_mac = $_POST["direccion_mac"];
    $arquitectura = $_POST["arquitectura"];
    $ip_local = $_POST["ip_local"];
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
            // Se añade el dispositivo
            $operacion_insercion = "
                INSERT INTO dispositivos (
                    nombre,
                    red,
                    descripcion,
                    direccion_mac,
                    arquitectura,
                    ip_local,
                    frecuencia_actualizacion,
                    frecuencia_envio_estado,
                    conexion,
                    latencia,
                    hora_ultimo_estado,
                    ultimo_estado,
                    hora_timeout_envio_estado,
                    timeout_envio_estado
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($direccion_mac)."',
                    '".$bd_red->_($arquitectura)."',
                    '".$bd_red->_($ip_local)."',
                    '".$bd_red->_($frecuencia_actualizacion)."',
                    '".$bd_red->_($frecuencia_envio_estado)."',
                    'NA',
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    '0'
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

                // Se envía mensaje MQTT de administración de dispositivo
                notifica_operacion_administracion_dispositivo(OPERACION_ADICION, $id_dispositivo);

                $res = "OK";
                $msg = $idiomas->_("Dispositivo añadido correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
