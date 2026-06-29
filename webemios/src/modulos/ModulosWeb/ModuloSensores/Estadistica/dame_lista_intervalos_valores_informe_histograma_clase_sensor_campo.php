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
    $campo = $_POST["campo"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $html = dame_lista_intervalos_valores_informe_histograma_clase_sensor_campo($clase_sensor, $campo, $intervalo_valores);

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

