<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');


    $id_sensor_padre = $_POST["id_sensor_padre"];
    $clase_sensor_hijo = $_POST["clase_sensor_hijo"];
    $html = dame_lista_sensores_hijos_administracion(
        $id_sensor_padre,
        TIPO_SENSOR_PROCESADO,
        ID_NINGUNO,
        $clase_sensor_hijo);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

