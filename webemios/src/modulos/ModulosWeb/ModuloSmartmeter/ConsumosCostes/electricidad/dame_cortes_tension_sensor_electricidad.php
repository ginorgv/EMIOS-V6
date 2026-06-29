<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CORTES_TENSION_SENSOR, $_POST);

    // Se recuperan los cortes de tensión
    $parametros = $_POST;
    $resultado = dame_cortes_tension_sensor_electricidad($parametros, NULL);
    print(json_encode($resultado));
?>
