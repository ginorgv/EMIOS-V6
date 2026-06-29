<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');



    $tipo = $_POST["tipo"];
    $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($tipo);
    $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];

    print(json_encode(array(
        "res" => "OK",
        "numero_tramos" => $numero_tramos,
        "tipo_calculo_coste_termino_fijo" => $tipo_calculo_coste_termino_fijo))
    );
?>

