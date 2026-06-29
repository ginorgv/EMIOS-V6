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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ENVIAR_VALORES_MANUALES_SENSOR, $_POST);

    $resultado_envio_valores = envia_valores_manuales_sensor($_POST);
    print(json_encode($resultado_envio_valores));
?>
