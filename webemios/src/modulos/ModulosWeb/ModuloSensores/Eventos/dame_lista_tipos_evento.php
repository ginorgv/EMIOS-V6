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
    $origen = $_POST["origen"];
    $granularidad = $_POST["granularidad"];
    $tipo = $_POST["tipo"];
    $html = dame_lista_tipos_evento(
        $clase_sensor,
        $origen,
        $granularidad,
        $tipo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>