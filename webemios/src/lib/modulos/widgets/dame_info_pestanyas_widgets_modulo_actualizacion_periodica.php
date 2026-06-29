<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');


    $modulo = $_POST["modulo"];
    $id_pestanya = $_POST["id_pestanya"];

    $info_pestanyas = dame_info_pestanyas_widgets_modulo_actualizacion_periodica($modulo, $id_pestanya);

    print(json_encode(
        array(
            "res" => "OK",
            "info_pestanyas" => $info_pestanyas
        )
    ));
?>

