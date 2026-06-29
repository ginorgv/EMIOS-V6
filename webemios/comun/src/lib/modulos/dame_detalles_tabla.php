<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_detalles_tabla.php');


    // Devuelve los detalles de una fila de una tabla
    $id_datos = $_POST["id_datos"];

    $detalles = dame_detalles_tabla($id_datos);
	print(json_encode($detalles));
?>
