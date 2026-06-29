<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');


    // Comprobación de sesión correcta
    $respuesta_sesion_correcta = dame_sesion_correcta();
    if ($respuesta_sesion_correcta["sesion_correcta"] == false)
    {
        print($respuesta_sesion_correcta["respuesta_script"]);
        return;
    }

	print(json_encode(array(
        "res" => "OK"))
    );
?>
