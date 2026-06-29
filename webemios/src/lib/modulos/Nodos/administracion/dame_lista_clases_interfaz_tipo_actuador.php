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


    $tipo = $_POST["tipo"];
    $clase = NINGUNO;
    $html = dame_lista_clases_interfaz_tipo_actuador($tipo, $clase);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
