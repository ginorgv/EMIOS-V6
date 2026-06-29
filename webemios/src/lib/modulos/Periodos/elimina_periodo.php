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
	include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/util_periodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PERIODO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_periodo = $_POST['id_periodo'];

    // Se recupera la fila del periodo
    $fila_periodo = dame_fila_periodo($id_periodo);

    // Se elimina el periodo
	$operacion_borrado = "
        DELETE
        FROM periodos
        WHERE
            id = '".$bd_red->_($id_periodo)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_periodo($fila_periodo);

        $res = "OK";
        $msg = $idiomas->_("Periodo eliminado correctamente");
        $msg .= "\n(".$idiomas->_("actualice la configuración manualmente si quiere que los cambios se apliquen inmediatamente").")";
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del periodo
    function anyade_accion_usuario_eliminar_periodo($fila)
    {
        // Nombre del origen del periodo
        $nombre_origen = Periodo::dame_nombre_id_origen_periodo($fila["origen"], $fila["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_PERIODO;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_PERIODO] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_INICIO] = $fila["hora_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA_FIN] = $fila["hora_fin"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_INICIO] = $fila["dia_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA_FIN] = $fila["dia_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
