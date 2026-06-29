<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    $clase_sensor = $_POST["clase_sensor"];
    $campo = $_POST["campo"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo($clase_sensor, $campo, $intervalo_valores, $opciones_extra);

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

