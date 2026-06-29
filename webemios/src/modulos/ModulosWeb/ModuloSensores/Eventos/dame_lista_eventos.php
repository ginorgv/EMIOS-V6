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
    $id_origen = $_POST["id_origen"];
    $granularidad = $_POST["granularidad"];
    $ids_eventos = $_POST["ids_eventos"];
    $html = dame_lista_eventos(
        $clase_sensor,
        $origen,
        $id_origen,
        $granularidad,
        $ids_eventos);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>