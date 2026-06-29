<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_actuadores.php');


    $tipo_actuador = $_POST["tipo_actuador"];
    $clase_interfaz = $_POST["clase_interfaz"];
    $html = dame_controles_clase_interfaz_actuador($tipo_actuador, $clase_interfaz, NULL, NULL);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

