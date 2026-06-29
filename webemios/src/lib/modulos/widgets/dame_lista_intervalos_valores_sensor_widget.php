<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_administracion_widgets.php');


    $tipo_widget = $_POST["tipo_widget"];
    $clase_sensor = $_POST["clase_sensor"];
    $campo = $_POST["campo"];
    $periodo_tiempo = $_POST["periodo_tiempo"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $html = dame_lista_intervalos_valores_sensor_widget(
        $tipo_widget,
        $clase_sensor,
        $campo,
        $periodo_tiempo,
        $intervalo_valores);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

