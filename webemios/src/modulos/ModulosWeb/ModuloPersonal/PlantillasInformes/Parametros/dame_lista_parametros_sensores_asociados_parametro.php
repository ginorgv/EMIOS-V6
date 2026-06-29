<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_administracion_parametros.php');


    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $clase_sensor = $_POST["clase_sensor"];
    $id_parametro = $_POST["id_parametro"];
    $html = dame_lista_parametros_sensores_asociados_parametro_plantilla_informe(
        $id_plantilla_informe,
        $clase_sensor,
        $id_parametro);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>