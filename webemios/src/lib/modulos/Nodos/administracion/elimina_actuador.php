<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_ACTUADOR, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $id_actuador = $_POST['id_actuador'];

    // Se recupera la fila del actuador
    $fila_actuador = dame_fila_actuador($id_actuador);

    // Se recupera si es posible eliminar el actuador
    $msg = "";
    $eliminar_actuador = dame_posible_eliminar_actuador($id_actuador, $fila_actuador, $msg);

    // Se elimina el actuador
    if ($eliminar_actuador == true)
    {
        // Se elimina el actuador
        elimina_actuador($id_actuador, $fila_actuador);

        // Eliminación correcta
        $res = "OK";
        $msg = $idiomas->_("Actuador eliminado correctamente");

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_actuador($fila_actuador);
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación del actuador
    function anyade_accion_usuario_eliminar_actuador($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_ACTUADOR;
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
