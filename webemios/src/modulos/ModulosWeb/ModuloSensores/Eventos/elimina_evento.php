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
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_EVENTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_evento = $_POST['id_evento'];

    // Comprobaciones antes de eliminar el evento:
    // - Se comprueba si existe algun suceso asignado a este evento
    $eliminar_evento = true;

    // Se comprueba si existe algun suceso asignado a este evento
    if ($eliminar_evento == true)
    {
        $consulta_sucesos = "
            SELECT *
            FROM sucesos_reglas
            WHERE
                (causa = '".CAUSA_SUCESO_EVENTO."')
                AND (id_causa = '".$bd_red->_($id_evento)."')
            ORDER BY nombre ASC";
        $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
        if ($res_sucesos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
        }
        if ($res_sucesos->dame_numero_filas() > 0)
        {
            $eliminar_evento = false;

            $fila_suceso = $res_sucesos->dame_siguiente_fila();
            $nombre_suceso = $fila_suceso["nombre"];
            $id_regla_suceso = $fila_suceso["regla"];
            $nombre_regla_suceso = dame_nombre_regla($id_regla_suceso);

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar el evento porque tiene sucesos asignados")."\n(".
                $idiomas->_("suceso").": ".$nombre_suceso.", ".
                $idiomas->_("regla").": ".$nombre_regla_suceso.")";
        }
    }

    // Se elimina el evento
    if ($eliminar_evento == true)
    {
        // Se recupera la fila del evento
        $fila_evento = dame_fila_evento($id_evento);

        // Se elimina el evento
        $operacion_borrado = "
            DELETE
            FROM eventos
            WHERE
                id = '".$bd_red->_($id_evento)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan los rangos de días del evento
            $operacion_borrado_rangos_dias_evento = "
                DELETE
                FROM rangos_dias
                WHERE
                    (origen = '".ORIGEN_RANGOS_DIAS_EVENTO."')
                    AND (id_origen = '".$bd_red->_($id_evento)."')";
            $res_borrado_rangos_dias_evento = $bd_red->ejecuta_operacion($operacion_borrado_rangos_dias_evento);
            if ($res_borrado_rangos_dias_evento == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_rangos_dias_evento."'");
            }

            // Se eliminan los periodos del evento
            $operacion_borrado_periodos_evento = "
                DELETE
                FROM periodos
                WHERE
                    (origen = '".ORIGEN_PERIODOS_EVENTO."')
                    AND (id_origen = '".$bd_red->_($id_evento)."')";
            $res_borrado_periodos_evento = $bd_red->ejecuta_operacion($operacion_borrado_periodos_evento);
            if ($res_borrado_periodos_evento == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_periodos_evento."'");
            }

            // Acciones a realizar al eliminar un evento
            realiza_acciones_evento_eliminado($id_evento, $fila_evento);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_evento($fila_evento);

            $res = "OK";
            $msg = $idiomas->_("Evento eliminado correctamente");
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


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un evento
    function realiza_acciones_evento_eliminado($id_evento, $fila_evento)
    {
        // Se modifican los elementos de plantillas de informes que contengan este evento (se establece a ninguno)
        modifica_elementos_plantillas_informes_evento_eliminado($id_evento);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_evento_eliminado($id_evento);

        // Se recargan las configuraciones de los sensores del origen del evento
        recarga_configuraciones_sensores_origen_evento(
            $fila_evento["origen"],
            $fila_evento["id_origen"]);
    }


    // Añade la acción de usuario de eliminación del evento
    function anyade_accion_usuario_eliminar_evento($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_EVENTO;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
