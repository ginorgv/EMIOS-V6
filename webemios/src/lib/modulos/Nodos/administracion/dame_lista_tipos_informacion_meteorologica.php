<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sensores_software.php');


    $proveedor = $_POST["proveedor"];
    $tipo_seleccionado = $_POST["tipo_seleccionado"];
    $html = dame_lista_tipos_informacion_meteorologica($proveedor, $tipo_seleccionado);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
