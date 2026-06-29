<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');


    $id_localizacion = $_POST["id_localizacion"];
    $id_instalacion = $_POST["id_instalacion"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_instalaciones_localizacion(
        $id_localizacion,
        $id_instalacion,
        $opciones_extra);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>