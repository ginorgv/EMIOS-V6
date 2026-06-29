<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');


    // Parámetros
    $accion_inicial = $_POST["accion_inicial"];
    $parametros_accion_inicial = $_POST["parametros_accion_inicial"];

    // Se guarda la acción inicial en la sesión
    guarda_accion_inicial_sesion($accion_inicial, $parametros_accion_inicial);

    // Se devuelve OK
	print(json_encode(array(
        "res" => "OK"))
    );
?>
