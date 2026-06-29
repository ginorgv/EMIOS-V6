<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_sensores.php');


    $clase_interfaz = $_POST["clase_interfaz"];
    $html = dame_controles_clase_interfaz_sensor($clase_interfaz, NULL, NULL);

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

