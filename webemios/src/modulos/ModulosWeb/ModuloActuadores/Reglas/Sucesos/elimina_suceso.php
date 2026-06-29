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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_SUCESO_REGLA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_suceso = $_POST['id_suceso'];

    // Se recupera la información de la fila del suceso de la regla
    $fila_suceso = dame_fila_suceso_regla($id_suceso);

    // Se elimina el suceso de la regla
	$operacion_borrado = "
        DELETE
        FROM sucesos_reglas
        WHERE
            id = '".$bd_red->_($id_suceso)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_suceso_regla($fila_suceso);

        $res = "OK";
        $msg = $idiomas->_("Suceso eliminado correctamente");
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


    // Añade la acción de usuario de eliminación del suceso de una regla
    function anyade_accion_usuario_eliminar_suceso_regla($fila)
    {
        // Nombre de la regla
        $nombre_regla = dame_nombre_regla($fila["regla"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_SUCESO_REGLA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_regla.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
