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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_DISPOSITIVO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_dispositivo = $_POST["id_dispositivo"];
    $tipo_dispositivo = NULL;
    // NUEVA RAMA
    // Si el dispositivo es de tipo Bye Radon, se ignora el resto del programa y va por un nuevo camino
    $consulta_dispositivo = "
        SELECT arquitectura, imei
        FROM dispositivos
        WHERE
            id = '".$bd_red->_($id_dispositivo)."'";
    $res_dispositivo = $bd_red->ejecuta_consulta($consulta_dispositivo);
    if ($res_dispositivo == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_dispositivo."'");
    }
    if ($res_dispositivo->dame_numero_filas() > 0)
    {
        $fila_dispositivo = $res_dispositivo->dame_siguiente_fila();
        $tipo_dispositivo = $fila_dispositivo['arquitectura'];
        
    }
    if ($tipo_dispositivo != ARQUITECTURA_DISPOSITIVO_BYE_RADON){

        // Se comprueba si existe algun axón asignado
        $consulta_axones = "
            SELECT nombre
            FROM axones
            WHERE
                dispositivo = '".$bd_red->_($id_dispositivo)."'";
        $res_axones = $bd_red->ejecuta_consulta($consulta_axones);
        if ($res_axones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_axones."'");
        }
        if ($res_axones->dame_numero_filas() > 0)
        {
            $fila_axon = $res_axones->dame_siguiente_fila();
            $nombre_axon = $fila_axon["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el dispositivo porque tiene un axón asignado")."\n(".
                $nombre_axon.")";
        }
        else
        {
            // Se borra el dispositivo
            $operacion_borrado = "
                DELETE
                FROM dispositivos
                WHERE
                    id = '".$bd_red->_($id_dispositivo)."'";
            $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
            if ($res_borrado == true)
            {
                // Se eliminan las posiciones de mapa del dispositivo
                elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_DISPOSITIVO, $id_dispositivo);

                // Se envía mensaje MQTT de administración de dispositivo
                notifica_operacion_administracion_dispositivo(OPERACION_BORRADO, $id_dispositivo);

                $res = "OK";
                $msg = $idiomas->_("Dispositivo eliminado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_borrado."'");
            }
        }

    }
    // Si es dispositivo de tipo Radon se elimina tanto el dispositivo como los sensores dependientes del mismo
    else
    {
        $log = dame_log();
        
        // Se borran los sensores dependientes
        $imei_dispositivo = $fila_dispositivo['imei'];
        // Lo sensores de radon dependientes de un dispositivo
        // Tienen por nombre '{imei}-%'

        $operacion_select = "
            SELECT *
            FROM sensores
            WHERE
                nombre LIKE '".$bd_red->_($imei_dispositivo)."-%'";
        $res = $bd_red->ejecuta_operacion($operacion_select);
        
        if ($res == false)
        {
             
            throw new Exception("Error en la consulta: '".$operacion_select."'");

        }
        else
        {
            
            while ($row = mysqli_fetch_assoc($res))
            {
                $log->error('hola');
                $id_sensor = $row['id'];
                // Se elimina el sensor
                elimina_sensor($id_sensor, $row);
            }
            //Eliminación correcta
            $res = "OK";
            $msg = $idiomas->_("Sensor eliminado correctamente");
        }
        

        // Se borra el dispositivo
            $operacion_borrado = "
                DELETE
                FROM dispositivos
                WHERE
                    id = '".$bd_red->_($id_dispositivo)."'";
            $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
            if ($res_borrado == true)
            {
                // Se eliminan las posiciones de mapa del dispositivo
                elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_DISPOSITIVO, $id_dispositivo);

                // Se envía mensaje MQTT de administración de dispositivo
                notifica_operacion_administracion_dispositivo(OPERACION_BORRADO, $id_dispositivo);

                // Se notifica al servidor externo que deje de leer datos del dispositivo
                notifica_servidor_remoto_subscripcion_dispositivo(OPERACION_BORRADO, $imei_dispositivo);

                $res = "OK";
                $msg = $idiomas->_("Dispositivo y sensores eliminados correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_borrado."'");
            }


    }
    

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
