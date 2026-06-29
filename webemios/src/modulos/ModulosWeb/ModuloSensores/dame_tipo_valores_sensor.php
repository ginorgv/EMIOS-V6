<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    $id_sensor = $_POST["id_sensor"];
    if ($id_sensor == ID_NINGUNO)
    {
        $tipo_valores_sensor = NULL;
    }
    else
    {
        // Información del sensor
        $fila_sensor = dame_fila_sensor($id_sensor);
        $tipo_valores_sensor = $fila_sensor["tipo_valores"];
    }

    print(json_encode(array(
        "res" => "OK",
        "tipo_valores_sensor" => $tipo_valores_sensor))
    );
?>

