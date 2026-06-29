<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    $nivel_log = $_POST["nivel"];
    $mensaje_log = "[".$_SESSION["id_usuario"]."] {Mensaje externo}: '".$_POST["mensaje"]."'";

    $log = dame_log();
    switch ($nivel_log)
    {
        case "INFO":
        {
            $log->info($mensaje_log);
            break;
        }
        case "DEBUG":
        {
            $log->debug($mensaje_log);
            break;
        }
        default:
        {
            $log->error($mensaje_log);
            break;
        }
    }

    print(json_encode(array(
        "res" => "OK"))
    );
?>
