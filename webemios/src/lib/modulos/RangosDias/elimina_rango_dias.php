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
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/RangoDias.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/util_rangos_dias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_RANGO_DIAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_rango_dias = $_POST['id_rango_dias'];

    // Se recupera la fila del rango de días
    $fila_rango_dias = dame_fila_rango_dias($id_rango_dias);

    // Se elimina el rango de días
	$operacion_borrado = "
        DELETE
        FROM rangos_dias
        WHERE
            id = '".$bd_red->_($id_rango_dias)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_rango_dias($fila_rango_dias);

        $res = "OK";
        $msg = $idiomas->_("Rango de días eliminado correctamente");
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


    // Añade la acción de usuario de eliminación del rango de días
    function anyade_accion_usuario_eliminar_rango_dias($fila)
    {
        // Nombre del origen del rango de días
        $nombre_origen = RangoDias::dame_nombre_id_origen_rango_dias($fila["origen"], $fila["id_origen"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_RANGO_DIAS;
        $objeto_accion_usuario = $nombre_origen;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ORIGEN_RANGO_DIAS] = $fila["origen"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ORIGEN] = $nombre_origen;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila["dia_anyo_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila["dia_anyo_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
