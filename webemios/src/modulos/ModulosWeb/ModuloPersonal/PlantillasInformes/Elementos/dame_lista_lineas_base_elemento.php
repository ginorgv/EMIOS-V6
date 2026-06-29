<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_administracion_elementos.php');


    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $tipo_seleccion_linea_base = $_POST["tipo_seleccion_linea_base"];
    $html = dame_lista_lineas_base_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_linea_base,
        ID_NINGUNO);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>