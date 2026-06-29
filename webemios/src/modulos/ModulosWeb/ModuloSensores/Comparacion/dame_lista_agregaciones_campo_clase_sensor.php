<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    $clase_sensor = $_POST["clase_sensor"];
    $campo = $_POST["campo"];
    $tipos_agregacion = $_POST["tipos_agregacion"];
    $agregacion = $_POST["agregacion"];
    if ($clase_sensor == CLASE_NINGUNA)
    {
        $tipo_valores = NULL;
    }
    else
    {
        $tipo_valores = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
    }
    $html = dame_lista_agregaciones($tipo_valores, $tipos_agregacion, $agregacion);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

