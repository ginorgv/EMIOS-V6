<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    $id_tarifa = $_POST["id_tarifa"];
    if ($id_tarifa == ID_NINGUNO)
    {
        $tipo_tarifa_agua = "";
        $caudal_diario_tarifa_agua = "";
    }
    else
    {
        $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);
        $tipo_tarifa_agua = $fila_tarifa_agua["tipo"];
        $caudal_diario_tarifa_agua = $fila_tarifa_agua["caudal_diario"];
    }

    print(json_encode(array(
        "res" => "OK",
        "tipo_tarifa_agua" => $tipo_tarifa_agua,
        "caudal_diario_tarifa_agua" => $caudal_diario_tarifa_agua))
    );
?>

