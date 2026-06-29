<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');


    // Recupera y borra la acción inicial de la sesión
    $res = dame_elimina_accion_inicial_sesion();
    $accion_inicial = $res["accion_inicial"];
    $parametros_accion_inicial = $res["parametros_accion_inicial"];

    // Se devuelve la acción inicial
	print(json_encode(array(
        "res" => "OK",
        "accion_inicial" => $accion_inicial,
        "parametros_accion_inicial" => $parametros_accion_inicial))
    );
?>
