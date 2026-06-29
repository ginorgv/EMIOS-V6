<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_administracion_elementos.php');


    $tipo_elemento = $_POST["tipo_elemento"];
    $parametros_informe = $_POST["parametros_informe"];
    $elementos_informe = $_POST["elementos_informe"];
    $html = dame_lista_elementos_informe_elemento_plantilla_informe(
        $tipo_elemento,
        $parametros_informe,
        $elementos_informe);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

