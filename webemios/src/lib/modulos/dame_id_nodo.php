<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');


    $tipo = $_POST["tipo_nodo"];
    $nombre = $_POST["nombre_nodo"];
    $id = dame_id_nodo($tipo, $nombre, $_SESSION["id_red"]);

    print(json_encode(array(
        "res" => "OK",
        "id" => $id))
    );
?>

