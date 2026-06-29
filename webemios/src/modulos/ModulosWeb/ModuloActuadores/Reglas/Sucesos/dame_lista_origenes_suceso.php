<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');


    $causa = $_POST["causa"];
    $id_causa = $_POST["id_causa"];
    $origen = $_POST["origen"];
    $html = dame_lista_origenes_suceso_regla($causa, $id_causa, $origen);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

