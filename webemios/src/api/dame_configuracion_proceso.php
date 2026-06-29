<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_interno.php');


    // Devuelve la configuración del proceso
    // (realmente sólo del axón, pero se mantiene el nombre del script por compatibilidad con versiones de pasarela anteriores a v4.0.0.0)
    $parametros = $_GET;
    $log = dame_log("api_interno");
    $log->info("Petición API HTTP: 'dame_configuracion_proceso' (parámetros: '".json_encode($parametros)."')");
    $res = dame_configuracion_axon($parametros);
    print(json_encode($res));
?>
