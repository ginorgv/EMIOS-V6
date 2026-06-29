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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_facturas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_VALIDACION_FACTURA, $_POST);

	$idiomas = new Idiomas();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_validacion_factura = $_POST["id_validacion_factura"];

    // Se elimina la validación de factura
    $tabla_validaciones_facturas = dame_nombre_tabla_validaciones_facturas($medicion);
    $operacion_borrado = "
        DELETE
        FROM ".$tabla_validaciones_facturas."
        WHERE
            (id = '".$bd_datos->_($id_validacion_factura)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_borrado = $bd_datos->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Validación de factura o cierre eliminada correctamente");
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
