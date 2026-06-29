<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_externo.php');


    // Se recuperan y devuelven los valores actuales del sensor
    $parametros = $_GET;
    $log = dame_log("api_externo");
    $log->info("Petición API HTTP: 'dame_valores_actuales_sensor' (parámetros: '".json_encode($parametros)."')");
    $res = dame_valores_actuales_sensor_api($parametros, true);
    print(json_encode($res));
?>
