<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_HISTOGRAMA_VALORES_SENSOR, $_POST);

    // Se recuperan los datos de histograma de valores de un sensor
    $parametros = $_POST;
    $resultado = dame_histograma_valores_sensor($parametros);
    print(json_encode($resultado));
?>
