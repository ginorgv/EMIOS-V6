<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_OPERACIONES_DATOS_SENSORES, $_POST);

	$idiomas = new Idiomas();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Se borran las operaciones de datos de sensores
    $operacion_borrado = "
        DELETE
        FROM operaciones_datos_sensores";
    $res_borrado = $bd_datos->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Operaciones de datos de sensores eliminadas correctamente");
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
