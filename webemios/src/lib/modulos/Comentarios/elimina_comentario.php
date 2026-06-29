<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_COMENTARIO, $_POST);

	$idiomas = new Idiomas();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_comentario = $_POST['id_comentario'];

    // Se elimina el comentario
    $operacion_borrado = "
        DELETE
        FROM comentarios
        WHERE
            (id = '".$bd_datos->_($id_comentario)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_borrado = $bd_datos->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Comentario eliminado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
