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


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_DISPOSITIVO, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_dispositivo = $_POST["id_dispositivo"];
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

    // Se comprueba si existe otro dispositivo con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM dispositivos
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_dispositivo)."')";
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
        // Se modifica el dispositivo
        $operacion_modificacion = "
			UPDATE dispositivos
			SET
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
				direccion_mac = '".$bd_red->_($direccion_mac)."',
				arquitectura = '".$bd_red->_($arquitectura)."',
                red = '".$_SESSION["id_red"]."',
                ip_local = '".$bd_red->_($ip_local)."',
                frecuencia_actualizacion = '".$bd_red->_($frecuencia_actualizacion)."',
                frecuencia_envio_estado = '".$bd_red->_($frecuencia_envio_estado)."'
			WHERE
				id = '".$bd_red->_($id_dispositivo)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se guarda o elimina la información de la posición en el mapa
            if ($mostrar_en_mapa == VALOR_SI)
            {
                $info_posicion_mapa_actual = array(
                    "tipo_elemento" => TIPO_ELEMENTO_MAPA_DISPOSITIVO,
                    "id_elemento" => $id_dispositivo,
                    "origen" => ORIGEN_MAPA_RED,
                    "id_origen" => $_SESSION["id_red"],
                    "latitud" => $latitud_mapa,
                    "longitud" => $longitud_mapa,
                    "zoom" => $zoom_mapa);
                guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
            }
            else
            {
                elimina_info_posicion_mapa_base_datos(
                    TIPO_ELEMENTO_MAPA_DISPOSITIVO,
                    $id_dispositivo,
                    ORIGEN_MAPA_RED,
                    $_SESSION["id_red"]);
            }

            // Se envía mensaje MQTT de administración de dispositivo
            notifica_operacion_administracion_dispositivo(OPERACION_MODIFICACION, $id_dispositivo);

            $res = "OK";
            $msg = $idiomas->_("Dispositivo modificado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
