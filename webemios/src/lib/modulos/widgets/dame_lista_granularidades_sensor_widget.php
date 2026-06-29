<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_administracion_widgets.php');


    $clase_sensor = $_POST["clase_sensor"];
    $granularidad_sensor = $_POST["granularidad"];
    $html = dame_lista_granularidades_sensor_widget($clase_sensor, $granularidad_sensor);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>