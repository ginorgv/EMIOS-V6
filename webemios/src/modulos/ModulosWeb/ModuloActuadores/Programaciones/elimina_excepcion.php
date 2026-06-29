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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_EXCEPCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_excepcion = $_POST['id_excepcion'];

    // Se recupera la fila de la excepción de la programación
    $fila_excepcion_programacion = dame_fila_excepcion_programacion($id_excepcion);

    // Se elimina la excepción de la programación
	$operacion_borrado = "
        DELETE
        FROM excepciones_programaciones
        WHERE
            id = '".$bd_red->_($id_excepcion)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_excepcion_programacion($fila_excepcion_programacion);

        $res = "OK";
        $msg = $idiomas->_("Excepción eliminada correctamente");
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


    // Añade la acción de usuario de eliminación de la excepción de la programación
    function anyade_accion_usuario_eliminar_excepcion_programacion($fila)
    {
        // Nombre de la programación
        $nombre_programacion = dame_nombre_programacion($fila["programacion"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_EXCEPCION_PROGRAMACION;
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
