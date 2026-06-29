<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


    // Parámetros
    $clase_sensor = $_POST["clase_sensor"];
    $opciones_extra = $_POST["opciones_extra"];
    $tipo_agrupacion_valores = $_POST["tipo_agrupacion_valores"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $campo = $_POST["campo"];

    // Lista de sensores y lista de campos
    $html_lista_sensores = dame_lista_sensores($clase_sensor, array(), $opciones_extra);
    if ($tipo_agrupacion_valores == true)
    {
        $html_lista_campos = dame_lista_campos_clase_sensor_tipo_agrupacion_valores_parametros_extra($clase_sensor, $intervalo_valores, $campo);
    }
    else
    {
        $html_lista_campos = dame_lista_campos_clase_sensor_parametros_extra($clase_sensor, $campo);
    }

    print(json_encode(array(
        "res" => "OK",
        "html_lista_sensores" => $html_lista_sensores,
        "html_lista_campos" => $html_lista_campos))
    );
?>



