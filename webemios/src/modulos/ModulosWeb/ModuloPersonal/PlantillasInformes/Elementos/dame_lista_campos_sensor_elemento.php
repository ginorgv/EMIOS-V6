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
    $clase_sensor = $_POST["clase_sensor"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $campo = $_POST["campo"];
    $html = dame_lista_campos_sensor_elemento_plantilla_informe(
        $tipo_elemento,
        $clase_sensor,
        $intervalo_valores,
        $campo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>