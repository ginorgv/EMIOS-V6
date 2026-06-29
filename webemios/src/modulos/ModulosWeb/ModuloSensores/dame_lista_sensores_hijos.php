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
    $id_sensor_padre = $_POST["id_sensor_padre"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_sensores_hijos(
        $clase_sensor,
        $id_sensor_padre,
        array(),
        $opciones_extra);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

