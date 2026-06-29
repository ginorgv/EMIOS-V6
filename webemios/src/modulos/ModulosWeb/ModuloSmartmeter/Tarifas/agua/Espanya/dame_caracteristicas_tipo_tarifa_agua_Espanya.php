<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');


    // Parámetros
    $tipo = $_POST["tipo"];

    // Se recuperan las características del tipo de tarifa de agua
    $caracteristicas_tipo_tarifa_agua = TarifaAgua_Espanya::dame_caracteristicas_tipo_tarifa_agua($tipo);
    $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_agua["tipo_tarifa_canarias"];

    print(json_encode(array(
        "res" => "OK",
        "tipo_tarifa_canarias" => $tipo_tarifa_canarias))
    );
?>

