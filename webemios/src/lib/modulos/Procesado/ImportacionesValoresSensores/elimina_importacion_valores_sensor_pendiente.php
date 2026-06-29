<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_IMPORTACION_VALORES_SENSOR_PENDIENTE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_importacion_pendiente = $_POST["id_importacion_pendiente"];

    // Se recupera la información de la importación pendiente
    $fila_importacion_pendiente = dame_fila_importacion_valores_sensor_pendiente($id_importacion_pendiente);

    // Comprobaciones antes de eliminar la importacion de valores del sensor pendiente:
    // - Comprobación de que no se está ejecutando
    $eliminar_importacion_pendiente = true;

    // Comprobación de que no se está ejecutando
    if ($eliminar_importacion_pendiente == true)
    {
        switch ($fila_importacion_pendiente["estado"])
        {
            case ESTADO_IMPORTACION_PENDIENTE_EN_EJECUCION:
            {
                $eliminar_importacion_pendiente = false;

                $res = "ERROR";
                $msg = $idiomas->_("No se puede eliminar una importación de valores de sensor en ejecución");
                break;
            }
        }
    }

    // Se elimina la importación de valores del sensor pendiente
    if ($eliminar_importacion_pendiente == true)
    {
        // Se elimina la importación de valores del sensor pendiente
        $operacion_borrado = "
            DELETE
            FROM importaciones_valores_sensores_pendientes
            WHERE
                id = '".$bd_red->_($id_importacion_pendiente)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se notifica la operación de administración
            notifica_operacion_administracion_importacion_valores_sensor_pendiente(
                OPERACION_BORRADO,
                $id_importacion_pendiente);

            // Nombre de sensor
            $nombre_sensor = dame_nombre_sensor($fila_importacion_pendiente["sensor"]);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_importacion_valores_sensor_pendiente($fila_importacion_pendiente, $nombre_sensor);

            $res = "OK";
            $msg = $idiomas->_("Importación de valores del sensor eliminada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación de la importación de valores del sensor pendiente
    function anyade_accion_usuario_eliminar_importacion_valores_sensor_pendiente($fila, $nombre_sensor)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_IMPORTACION_VALORES_SENSOR_PENDIENTE;
        $objeto_accion_usuario = $nombre_sensor;

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($fila["hora"], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_HORA] = $cadena_fecha_hora_base_datos_local;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
