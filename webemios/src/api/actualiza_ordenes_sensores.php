<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_interno.php');


    // Actualiza los órdenes de los sensores (virtuales y externos)
    $log = dame_log("api_interno");
    $log->info("Petición API HTTP: 'actualiza_ordenes_sensores'");
    $res = actualiza_ordenes_sensores();
    print(json_encode($res));
?>
