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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GUARDAR_FECHA_RECALCULO_VALORES_CLASE_SENSOR, $_POST);

    $resultado_guardado_fecha = guarda_fecha_recalculo_valores_clase_sensor($_POST);
    print(json_encode($resultado_guardado_fecha));
?>
