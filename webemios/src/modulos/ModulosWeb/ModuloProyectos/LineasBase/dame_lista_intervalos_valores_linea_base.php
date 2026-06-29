<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    $tipo = $_POST["tipo"];
    $opciones_extra = $_POST["opciones_extra"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $html = dame_lista_intervalos_valores_linea_base($tipo, $opciones_extra, $intervalo_valores);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

