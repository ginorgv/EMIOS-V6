<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    $id_tarifa = $_POST["id_tarifa"];
    if ($id_tarifa == ID_NINGUNO)
    {
        $tipo_tarifa_gas = "";
        $caudal_diario_tarifa_gas = "";
    }
    else
    {
        $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_gas = $fila_tarifa_gas["tipo"];
        $caudal_diario_tarifa_gas = $fila_tarifa_gas["caudal_diario"];
    }

    print(json_encode(array(
        "res" => "OK",
        "tipo_tarifa_gas" => $tipo_tarifa_gas,
        "caudal_diario_tarifa_gas" => $caudal_diario_tarifa_gas))
    );
?>

