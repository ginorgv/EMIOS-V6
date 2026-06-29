<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');


    $modulo = $_POST["modulo"];
    $seccion = $_POST["seccion"];
    $html = dame_lista_acciones_iniciales_modulo_seccion(
        $modulo,
        $seccion,
        NULL);

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

