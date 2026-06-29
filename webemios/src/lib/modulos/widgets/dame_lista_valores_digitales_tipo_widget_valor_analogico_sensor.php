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
    $valor_digital = $_POST["valor_digital"];
    $html = dame_lista_valores_digitales_tipo_widget_valor_analogico_sensor(
        $clase_sensor,
        $valor_digital);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

