<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_api_externo.php');


    // Se recupera y devuelve la simulación de factura del sensor y una tarifa entre el rango de fechas
    $parametros = $_GET;
    $log = dame_log("api_externo");
    $log->info("Petición API HTTP: 'dame_simulacion_factura_sensor_tarifa_api' (parámetros: '".json_encode($parametros)."')");
    $res = dame_simulacion_factura_sensor_tarifa_api($parametros);
    print(json_encode($res));
?>
