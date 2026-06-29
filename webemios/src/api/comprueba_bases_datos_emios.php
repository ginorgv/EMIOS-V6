<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    try
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $bd_datos = BaseDatosDatos::dame_base_datos();
        print("OK");
        header("HTTP/1.1 200 OK");
    }
    catch (Exception $e)
    {
        print("ERROR");
        header("HTTP/1.1 500 Internal Server Error");
    }
?>
