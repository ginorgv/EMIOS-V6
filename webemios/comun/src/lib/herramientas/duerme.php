<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    // Parámetros
    $segundos = $_POST["segundos"];

    // Se duerme los segundos especificados
    if ($segundos > 0)
    {
        usleep($segundos * 1000000);
    }

	print(json_encode(array(
        "res" => "OK"))
    );
?>
