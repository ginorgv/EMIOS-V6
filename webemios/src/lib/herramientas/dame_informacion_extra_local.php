<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sesion.php');


    // Informacion 'extra' local
    $resultado = dame_informacion_extra_local();
    $resultado["res"] = "OK";

    // Se devuelve el resultado
	print(json_encode($resultado));
?>
