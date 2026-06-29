<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    $id_sensor = $_POST["id_sensor"];
    if ($id_sensor == ID_NINGUNO)
    {
        $id_tarifa = ID_NINGUNO;
    }
    else
    {
        $id_tarifa = dame_id_tarifa_id_sensor($_POST["id_sensor"]);
    }

    print(json_encode(array(
        "res" => "OK",
        "id_tarifa" => $id_tarifa))
    );
?>

