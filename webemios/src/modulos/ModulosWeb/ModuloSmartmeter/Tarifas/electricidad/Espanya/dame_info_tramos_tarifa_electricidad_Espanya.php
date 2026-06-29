<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    // Parámetros
    $id_tarifa = $_POST["id_tarifa"];

    // Se recupera la información de los tramos
    if ($id_tarifa == ID_NINGUNO)
    {
        $numero_tramos = 0;
        $info_tramos = array();
    }
    else
    {
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
        $info_tramos = dame_info_tramos_tarifa_electricidad_Espanya($id_tarifa);
    }

    print(json_encode(array(
        "res" => "OK",
        "numero_tramos" => $numero_tramos,
        "info_tramos" => $info_tramos))
    );
?>

