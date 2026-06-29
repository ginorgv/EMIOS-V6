<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    $id_ratio = $_POST["id_ratio"];
    $fila_ratio = dame_fila_ratio($id_ratio);

    print(json_encode(array(
        "res" => "OK",
        "fila_ratio" => $fila_ratio))
    );
?>
