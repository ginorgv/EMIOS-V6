<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_herramientas_sensores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_IMPORTACION_VALORES_SENSOR_PENDIENTE, $_POST);

    $resultado_adicion_importacion = anyade_importacion_valores_sensor_pendiente($_POST, $_FILES);
    print(json_encode($resultado_adicion_importacion));
?>
