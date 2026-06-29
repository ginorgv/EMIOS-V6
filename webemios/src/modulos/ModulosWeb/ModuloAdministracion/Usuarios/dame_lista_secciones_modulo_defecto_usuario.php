<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');


    $id_usuario = $_POST["id_usuario"];
    $id_red = $_POST["id_red"];
    $modulo = $_POST["modulo"];
    $html = dame_lista_secciones_modulo_defecto_usuario(
        $id_usuario,
        $id_red,
        $modulo,
        array());

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

