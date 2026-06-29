<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    $clase_sensor = $_POST["clase_sensor"];
    $id_sensor = $_POST["id_sensor"];
    $granularidad = $_POST["granularidad"];
    $campo = $_POST["campo"];
    $html = dame_lista_campos_sensor_activaciones_eventos(
        $clase_sensor,
        $id_sensor,
        $granularidad,
        $campo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>