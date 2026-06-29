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


    $funcion_hijo_sensor_procesado = $_POST["funcion_hijo_sensor_procesado"];
    $html = dame_controles_parametros_funcion_hijo_sensor_procesado(ID_NINGUNO, $funcion_hijo_sensor_procesado, "");

    print(json_encode(
        array(
            "res" => "OK",
            "html" => $html
        )
    ));
?>

