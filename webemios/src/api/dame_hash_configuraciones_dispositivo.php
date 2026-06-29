<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_interno.php');


    // Devuelve los 'hashes' de las configuraciones del dispositivo y del axón (si existe)
    $parametros = $_GET;
    $log = dame_log("api_interno");
    $log->info("Petición API HTTP: 'dame_hash_configuraciones_dispositivo' (parámetros: '".json_encode($parametros)."')");
    $res = dame_hash_configuraciones_dispositivo($parametros);
    print(json_encode($res));
?>
