<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');


    // Devuelve si es usuario interno
    $usuario_interno = isset($_SESSION["usuario_interno"]);

	print(json_encode(array(
        "res" => "OK",
        "usuario_interno" => $usuario_interno))
    );
?>
