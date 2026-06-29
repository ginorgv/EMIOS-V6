<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    $clase_actuador = $_POST["clase_actuador"];
    $destino = $_POST["destino"];
    $id_destino = $_POST["id_destino"];
    $html = dame_lista_ids_destinos_accion($clase_actuador, $destino, $id_destino);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

