<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');


    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $numero_activaciones = $_POST["numero_activaciones"];
    $html = dame_lista_numero_activaciones_suceso_regla($origen, $id_origen, $numero_activaciones);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

