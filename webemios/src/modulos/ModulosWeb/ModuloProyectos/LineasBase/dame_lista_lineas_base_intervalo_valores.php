<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    $intervalo_valores = $_POST["intervalo_valores"];
    $id_linea_base = $_POST["id_linea_base"];
    $html = dame_lista_lineas_base_intervalo_valores($intervalo_valores, $id_linea_base);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

