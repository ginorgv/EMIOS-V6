<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');


    $origen_comentarios = $_POST["origen_comentarios"];
    $tipo_comentarios = $_POST["tipo_comentarios"];
    $clase_objetos = $_POST["clase_objetos"];
    $cadena_ids_objetos = $_POST["ids_objetos"];
    $html = dame_lista_objetos_comentarios(
        $origen_comentarios,
        $tipo_comentarios,
        $clase_objetos,
        $cadena_ids_objetos,
        NULL);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>