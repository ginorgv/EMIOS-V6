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
	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_REGLA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_regla = $_POST['id_regla'];

    // Comprobaciones antes de eliminar la regla:
    // - Se comprueba si existe algun suceso asignado a esta regla
    // - Sucesos administrables por el usuario actual
    // - Acciones administrables por el usuario actual
    $eliminar_regla = true;

    // Se comprueba si existe algun suceso asignado a esta regla
    if ($eliminar_regla == true)
    {
        $consulta_sucesos = "
            SELECT *
            FROM sucesos_reglas
            WHERE
                (causa = '".CAUSA_SUCESO_REGLA."')
                AND (id_causa = '".$bd_red->_($id_regla)."')
            ORDER BY nombre ASC";
        $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
        if ($res_sucesos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
        }
        if ($res_sucesos->dame_numero_filas() > 0)
        {
            $eliminar_regla = false;

            $fila_suceso = $res_sucesos->dame_siguiente_fila();
            $nombre_suceso = $fila_suceso["nombre"];
            $id_regla_suceso = $fila_suceso["regla"];
            $nombre_regla_suceso = dame_nombre_regla($id_regla_suceso);

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la regla porque tiene sucesos asignados")."\n(".
                $idiomas->_("suceso").": ".$nombre_suceso.", ".
                $idiomas->_("regla").": ".$nombre_regla_suceso.")";
        }
    }

    // Sucesos administrables por el usuario actual
    if ($eliminar_regla == true)
    {
        $consulta_sucesos = SucesoRegla::dame_consulta_sucesos($id_regla);
        $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
        if ($res_sucesos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
        }
        while ($fila_suceso = $res_sucesos->dame_siguiente_fila())
        {
            $suceso = new SucesoRegla($fila_suceso);
            if ($suceso->dame_administracion_suceso_usuario_actual() == false)
            {
                $eliminar_regla = false;

                $nombre_suceso = $fila_suceso["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("No se puede eliminar la regla porque tiene sucesos no administrables por el usuario actual")."\n(".
                    $nombre_suceso.")";
                break;
            }
        }
    }

    // Acciones administrables por el usuario actual
    if ($eliminar_regla == true)
    {
        $consulta_acciones = AccionRegla::dame_consulta_acciones($id_regla, ID_NINGUNO);
        $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }
        while ($fila_accion = $res_acciones->dame_siguiente_fila())
        {
            $accion = new AccionRegla($fila_accion);
            if ($accion->dame_administracion_accion_usuario_actual() == false)
            {
                $eliminar_regla = false;

                $nombre_accion = $fila_accion["nombre"];

                $res = "ERROR";
                $msg = $idiomas->_("No se puede eliminar la regla porque tiene acciones no administrables por el usuario actual")."\n(".
                    $nombre_accion.")";
                break;
            }
        }
    }

    // Se elimina la regla
    if ($eliminar_regla == true)
    {
        // Se recupera la fila de la regla
        $fila_regla = dame_fila_regla($id_regla);

        // Se elimina la regla
        $operacion_borrado = "
            DELETE
            FROM reglas
            WHERE
                id = '".$bd_red->_($id_regla)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan los periodos de la regla
            $operacion_borrado_periodos_regla = "
                DELETE
                FROM periodos
                WHERE
                    (origen = '".ORIGEN_PERIODOS_REGLA."')
                    AND (id_origen = '".$bd_red->_($id_regla)."')";
            $res_borrado_periodos_regla = $bd_red->ejecuta_operacion($operacion_borrado_periodos_regla);
            if ($res_borrado_periodos_regla == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_periodos_regla."'");
            }

            // Se eliminan los rangos de días de la regla
            $operacion_borrado_rangos_dias_regla = "
                DELETE
                FROM rangos_dias
                WHERE
                    (origen = '".ORIGEN_RANGOS_DIAS_REGLA."')
                    AND (id_origen = '".$bd_red->_($id_regla)."')";
            $res_borrado_rangos_dias_regla = $bd_red->ejecuta_operacion($operacion_borrado_rangos_dias_regla);
            if ($res_borrado_rangos_dias_regla == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_rangos_dias_regla."'");
            }

            // Se eliminan los sucesos de la regla
            $operacion_borrado_sucesos_regla = "
                DELETE
                FROM sucesos_reglas
                WHERE
                    regla = '".$bd_red->_($id_regla)."'";
            $res_borrado_sucesos_regla = $bd_red->ejecuta_operacion($operacion_borrado_sucesos_regla);
            if ($res_borrado_sucesos_regla == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_sucesos_regla."'");
            }

            // Se eliminan las acciones de la regla
            $operacion_borrado_acciones_regla = "
                DELETE
                FROM acciones_reglas
                WHERE
                    regla = '".$bd_red->_($id_regla)."'";
            $res_borrado_acciones_regla = $bd_red->ejecuta_operacion($operacion_borrado_acciones_regla);
            if ($res_borrado_acciones_regla == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_acciones_regla."'");
            }

            // Se envía el mensaje MQTT
            notifica_operacion_administracion_regla(OPERACION_BORRADO, $id_regla);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_regla($fila_regla);

            $res = "OK";
            $msg = $idiomas->_("Regla eliminada correctamente");
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


    // Añade la acción de usuario de eliminación de la regla
    function anyade_accion_usuario_eliminar_regla($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_REGLA;
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
