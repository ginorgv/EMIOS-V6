<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');


    $clase_actuador = $_POST["clase_actuador"];
    $contenido_accion = $_POST["contenido_accion"];
    $html = dame_botones_opcion_acciones_predefinidas($clase_actuador, $contenido_accion);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

