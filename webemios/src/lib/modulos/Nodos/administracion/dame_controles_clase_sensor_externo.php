<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores_externos.php');


    $clase_sensor_externo = $_POST["clase_sensor_externo"];
    $html = dame_controles_clase_sensor_externo($clase_sensor_externo, NULL, NULL);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

