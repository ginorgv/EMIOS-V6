<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_externo.php');


    // Se recuperan y devuelven los sensores a los que tiene acceso un usuario en una red
    $parametros = $_GET;
    $log = dame_log("api_externo");
    $log->info("Petición API HTTP: 'dame_sensores' (parámetros: '".json_encode($parametros)."')");
    $res = dame_sensores($parametros, true);
    print(json_encode($res));
?>
