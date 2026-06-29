<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');


    $tipo = $_POST["tipo"];
    $html = dame_lista_tarifas_tipo_agua_Espanya($tipo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

