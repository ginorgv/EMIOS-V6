<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    $tipo = $_POST["tipo"];
    $contrato = $_POST["contrato"];
    $html = dame_lista_tarifas_tipo_contrato_electricidad_Espanya($tipo, $contrato);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

