<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    $clase_sensor = $_POST["clase_sensor"];
    $campo_sensor = $_POST["campo_sensor"];
    $html = dame_lista_campos_sensor_ratio_variable($clase_sensor, $campo_sensor);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>