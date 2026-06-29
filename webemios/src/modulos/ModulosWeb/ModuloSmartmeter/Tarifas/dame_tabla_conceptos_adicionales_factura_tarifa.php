<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFA, $_POST);

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_tarifa = $_POST["id_tarifa"];

    $res = "OK";
    $html = dame_tabla_conceptos_adicionales_factura_tarifa($medicion, $id_tarifa);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
