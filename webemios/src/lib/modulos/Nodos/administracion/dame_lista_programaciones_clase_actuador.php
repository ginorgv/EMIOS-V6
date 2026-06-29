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


    $clase_actuador = $_POST["clase_actuador"];
    $html = dame_lista_programaciones_clase_actuador($clase_actuador, ID_NINGUNO);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
