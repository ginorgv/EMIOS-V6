<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_administracion_widgets.php');


    $medicion = $_POST["medicion"];
    $concepto_factura = $_POST["concepto_factura"];
    $html = dame_lista_conceptos_factura_widget_coste_factura_sensor($medicion, $concepto_factura);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>