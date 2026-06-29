<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICA, $_POST);

    // Parámetros
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];

    $params = array();
    $params["id"] = $id_tarifa_electrica;
    $tarifa_electrica = new TarifaElectrica_Espanya($params);

    $res = "OK";
	$html = $tarifa_electrica->dame_tabla_periodos_calculo_costes_pass_pool();

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
