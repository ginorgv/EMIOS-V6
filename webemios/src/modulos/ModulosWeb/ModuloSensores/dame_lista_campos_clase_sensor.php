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
    $tipo_agrupacion_valores = $_POST["tipo_agrupacion_valores"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $campo = $_POST["campo"];
    if ($tipo_agrupacion_valores == true)
    {
        $html = dame_lista_campos_clase_sensor_tipo_agrupacion_valores($clase_sensor, $intervalo_valores, $campo);
    }
    else
    {
        $html = dame_lista_campos_clase_sensor($clase_sensor, $campo);
    }

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

