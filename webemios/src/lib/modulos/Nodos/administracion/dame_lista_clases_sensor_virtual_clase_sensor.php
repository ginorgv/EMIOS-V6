<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');


    $clase_sensor = $_POST["clase_sensor"];
    $clase_virtual = $_POST["clase_virtual"];
    $html = dame_lista_clases_sensor_virtual_clase_sensor($clase_sensor, $clase_virtual);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
