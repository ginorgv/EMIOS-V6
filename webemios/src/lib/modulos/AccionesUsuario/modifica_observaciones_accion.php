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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_OBSERVACIONES_ACCION_USUARIO, $_POST);

	$idiomas = new Idiomas();
	$bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_accion = $_POST['id_accion'];
    $observaciones = $_POST['observaciones'];
    $observaciones_anteriores = $_POST['observaciones_anteriores'];

    $operacion_modificacion = "
        UPDATE acciones_usuario
        SET
            observaciones = '".$bd_datos->_($observaciones)."'
        WHERE
            id = '".$bd_datos->_($id_accion)."'";
    $res_modificacion = $bd_datos->ejecuta_operacion($operacion_modificacion);
    if ($res_modificacion == true)
    {
        $res = "OK";
        if ($observaciones_anteriores == "")
        {
            $msg = $idiomas->_("Observaciones añadidas correctamente");
        }
        else
        {
            $msg = $idiomas->_("Observaciones modificadas correctamente");
        }
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
