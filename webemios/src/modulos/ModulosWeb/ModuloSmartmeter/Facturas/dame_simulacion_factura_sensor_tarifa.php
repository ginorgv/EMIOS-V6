<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SIMULACION_FACTURA_SENSOR_TARIFA, $_POST);

    // Se recupera la simulación de la factura
    $parametros = $_POST;
    $resultado = dame_simulacion_factura_sensor_tarifa($parametros);
    print(json_encode($resultado));
?>
