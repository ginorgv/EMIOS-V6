<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    $clase_sensor = $_POST["clase_sensor"];
    $clase_sensor_externo = $_POST["clase_sensor_externo"];
    $html = dame_lista_sensores_externos($clase_sensor, $clase_sensor_externo, array());

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

