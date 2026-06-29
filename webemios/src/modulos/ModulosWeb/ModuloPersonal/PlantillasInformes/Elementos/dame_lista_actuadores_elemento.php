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
    $tipo_seleccion_actuador = $_POST["tipo_seleccion_actuador"];
    $clase_actuador = $_POST["clase_actuador"];
    $opciones_extra = $_POST["opciones_extra"];
    $html = dame_lista_actuadores_elemento_plantilla_informe(
        $id_plantilla_informe,
        $tipo_seleccion_actuador,
        $clase_actuador,
        array(ID_NINGUNO),
        $opciones_extra);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>