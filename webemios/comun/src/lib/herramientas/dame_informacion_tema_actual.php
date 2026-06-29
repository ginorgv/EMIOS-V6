<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');


    // Información del tema actual
	print(json_encode(array(
        "res" => "OK",
        "color_tema_oscuro" => $_SESSION["colores"]["color_tema_oscuro"],
        "color_tema_intermedio" => $_SESSION["colores"]["color_tema_intermedio"],
        "color_tema_claro" => $_SESSION["colores"]["color_tema_claro"],
        "color_tema_fondo" => $_SESSION["colores"]["color_tema_fondo"]))
    );
?>
