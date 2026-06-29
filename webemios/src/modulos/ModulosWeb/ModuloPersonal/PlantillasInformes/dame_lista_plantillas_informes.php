<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_plantillas_informes($id_plantilla_informe, $opciones_extra);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

