<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');


    function exception_handler_api($exception)
    {
        // Se añade información de la excepción en el log y en el mensaje de respuesta y se devuelve resultado error
        $ip_remota = $_SERVER['REMOTE_ADDR'];

        $log = dame_log();
        $log->error("Excepción capturada en API (IP: '".$ip_remota."'):", $exception);
    }

    set_exception_handler('exception_handler_api');
?>
