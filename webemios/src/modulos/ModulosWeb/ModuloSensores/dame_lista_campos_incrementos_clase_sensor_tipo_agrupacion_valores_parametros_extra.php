<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    $clase_sensor = $_POST["clase_sensor"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $campo = $_POST["campo"];
    $html = dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra(
        $clase_sensor,
        $intervalo_valores,
        $campo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
