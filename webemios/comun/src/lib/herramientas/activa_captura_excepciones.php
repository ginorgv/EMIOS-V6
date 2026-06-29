<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_excepciones.php');


    function exception_handler($exception)
    {
        // Nota: Se reabre la escritura de la sesión
        // (por si ha habido una excepción al ejecutar un comando 'externo')
        session_start();

        // Se añade información de la excepción en el log y se devuelve resultado error y el mensaje correspondiente
        $log = dame_log();
        $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

        $res = "ERROR";
        $msg = dame_mensaje_error_excepcion($exception);

        print(json_encode(array(
            "res" => $res,
            "msg" => $msg))
        );
    }

    set_exception_handler('exception_handler');
?>
