<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    $clase_sensor = $_POST['clase_sensor'];
    $campo = $_POST['campo'];
    $html = dame_lista_campos_clase_sensor(
        $clase_sensor,
        $campo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

