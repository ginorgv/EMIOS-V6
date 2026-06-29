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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/util_informes_caudales.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COSTES_CAUDALES_SELECCIONADOS_SENSOR_TARIFA_GAS, $_POST);

    // Se recuperan los costes y caudales seleccionados de un sensor
    $parametros = $_POST;
    $resultado = dame_coste_caudal_diario_seleccionado_sensor_tarifa_gas($parametros);
    print(json_encode($resultado));
?>
