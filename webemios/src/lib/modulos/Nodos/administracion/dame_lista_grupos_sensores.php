<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    $clase_sensor = $_POST["clase_sensor"];
    $id_grupo_seleccionado = $_POST["id_grupo_seleccionado"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_grupos_sensores($clase_sensor, array($id_grupo_seleccionado), $opciones_extra);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
