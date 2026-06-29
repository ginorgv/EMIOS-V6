<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    $log = dame_log();
    $log->info("[".$_SESSION["id_usuario"]."] "."Fin de sesión");

    realiza_acciones_fin_sesion();
    session_unset();
    session_destroy();

    print(json_encode(array(
        "res" => "OK"))
    );
?>
