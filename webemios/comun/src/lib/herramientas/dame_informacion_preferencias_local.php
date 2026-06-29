<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sesion.php');


    // Se recupera la información de preferencias y local
    $informacion_preferencias = dame_informacion_preferencias();
    $informacion_local = dame_informacion_local();
    $resultado = array_merge($informacion_preferencias, $informacion_local);
    $resultado["res"] = "OK";

    // Se devuelve el resultado
	print(json_encode($resultado));
?>
