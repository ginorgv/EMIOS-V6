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


    // Parámetros
    $id_pestanya = $_POST['id_pestanya'];
    $opciones_pantalla_completa = dame_opciones_pantalla_completa_pestanya_widgets($id_pestanya);

    print(json_encode(
        array(
            "res" => "OK",
            "opciones_pantalla_completa" => $opciones_pantalla_completa
        )
    ));
?>

