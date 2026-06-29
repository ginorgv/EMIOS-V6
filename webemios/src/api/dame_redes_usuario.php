<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_externo.php');


    // Se recuperan y devuelven las redes a las que tiene acceso un usuario
    $parametros = $_GET;
    $log = dame_log("api_externo");
    $log->info("Petición API HTTP: 'dame_redes_usuario' (parámetros: '".json_encode($parametros)."')");
    $res = dame_redes_usuario($parametros, true);
    print(json_encode($res));
?>
