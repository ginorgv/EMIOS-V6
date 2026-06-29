<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_interno.php');


    // Devuelve la configuración del fichero '.ini'
    $log = dame_log("api_interno");
    $log->info("Petición API HTTP: 'dame_configuracion_ini'");
    $res = dame_configuracion_ini();
    print(json_encode($res));
?>

