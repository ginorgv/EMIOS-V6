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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_ACCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_accion = $_POST['id_accion'];

    // Se recupera la fila de la acción de la programación
    $fila_accion = dame_fila_accion_programacion($id_accion);

    // Se elimina la acción de la programación
	$operacion_borrado = "
        DELETE
        FROM acciones_programaciones
        WHERE
            id = '".$bd_red->_($id_accion)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_accion_programacion($fila_accion);

        $res = "OK";
        $msg = $idiomas->_("Acción eliminada correctamente");
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


    // Añade la acción de usuario de eliminación de la acción de la programación
    function anyade_accion_usuario_eliminar_accion_programacion($fila)
    {
        // Nombre de la programación
        $nombre_programacion = dame_nombre_programacion($fila["programacion"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_ACCION_PROGRAMACION;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_programacion.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
