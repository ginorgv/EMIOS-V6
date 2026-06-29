<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_EVENTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_evento = $_POST["id_evento"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $clase_sensor = $_POST["clase_sensor"];
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $granularidad = $_POST["granularidad"];
    $tipo = $_POST["tipo"];
    $parametros = $_POST["parametros"];
    $alarma = $_POST["alarma"];

    // Se comprueba si existe otro evento con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM eventos
        WHERE
            (nombre = '".$bd_red->_($$nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_evento)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un evento con el mismo nombre");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_evento_anterior = dame_fila_evento($id_evento);

        // Se modifica el evento
        $operacion_modificacion = "
            UPDATE eventos
            SET
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
                clase = '".$bd_red->_($clase_sensor)."',
                origen = '".$bd_red->_($origen)."',
                id_origen = '".$bd_red->_($id_origen)."',
                granularidad = '".$bd_red->_($granularidad)."',
                tipo = '".$bd_red->_($tipo)."',
                parametros = '".$bd_red->_($parametros)."',
                alarma = '".$bd_red->_($alarma)."'
            WHERE
                id = '".$bd_red->_($id_evento)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se actualizan los orígenes y los identificadores de los orígenes de los sucesos asignados a este evento
            $operacion_modificacion_sucesos = "
                UPDATE sucesos_reglas
                SET
                    origen = '".$bd_red->_($origen)."',
                    id_origen = '".$bd_red->_($id_origen)."'
                WHERE
                    (causa = '".$bd_red->_(CAUSA_SUCESO_EVENTO)."')
                    AND (id_causa = '".$bd_red->_($id_evento)."')";
            $res_modificacion_sucesos = $bd_red->ejecuta_operacion($operacion_modificacion_sucesos);
            if ($res_modificacion_sucesos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_sucesos."'");
            }

            // Se recupera la fila actual
            $fila_evento_actual = dame_fila_evento($id_evento);

            // Acciones a realizar al modificar un evento
            realiza_acciones_evento_modificado($fila_evento_actual);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_evento(
                $fila_evento_actual,
                $fila_evento_anterior);

            $res = "OK";
            $msg = $idiomas->_("Evento modificado correctamente");
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


    //
    // Funciones auxiliares
    //


    // Realiza acciones al modificar un evento
    function realiza_acciones_evento_modificado($fila_actual)
    {
        // Se recargan las configuraciones de los sensores del origen del evento
        recarga_configuraciones_sensores_origen_evento($fila_actual["origen"], $fila_actual["id_origen"]);
    }


    // Añade la acción de usuario de modificación del evento
    function anyade_accion_usuario_modificar_evento($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_EVENTO;

        // Nombres de origenes del evento
        $nombre_origen = Evento::dame_nombre_origen_evento($fila_actual["origen"], $fila_actual["id_origen"]);
        $nombre_origen_anterior = Evento::dame_nombre_origen_evento($fila_anterior["origen"], $fila_anterior["id_origen"]);

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase"];
        }
        if (($fila_actual["origen"] != $fila_anterior["origen"]) ||
            ($fila_actual["id_origen"] != $fila_anterior["id_origen"]))
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_EVENTO] = $fila_actual["origen"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ORIGEN_EVENTO] = $fila_anterior["origen"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen_anterior;
        }
        if (($fila_actual["tipo"] != $fila_anterior["tipo"]) ||
            ($fila_actual["parametros"] != $fila_anterior["parametros"]))
        {
            if ($fila_actual["tipo"] != $fila_anterior["tipo"])
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_EVENTO] = $fila_actual["tipo"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_EVENTO] = $fila_anterior["tipo"];
            }
            $descripcion_parametros_evento = Evento::dame_descripcion_parametros_evento(
                $fila_actual["clase"],
                $fila_actual["granularidad"],
                $fila_actual["tipo"],
                $fila_actual["parametros"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_EVENTO] = $descripcion_parametros_evento;
            $descripcion_parametros_anteriores_evento = Evento::dame_descripcion_parametros_evento(
                $fila_anterior["clase"],
                $fila_anterior["granularidad"],
                $fila_anterior["tipo"],
                $fila_anterior["parametros"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_EVENTO] = $descripcion_parametros_anteriores_evento;
        }
        if ($fila_actual["alarma"] != $fila_anterior["alarma"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ALARMA] = $fila_actual["alarma"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ALARMA] = $fila_anterior["alarma"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
