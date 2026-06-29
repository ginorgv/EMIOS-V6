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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_informes_compra_energia.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_DESVIOS_PONDERADOS_COMPRA_ENERGIA_SENSOR, $_POST);

    // Se recupera la información de desvíos ponderados de compra de energía de un sensor
    $parametros = $_POST;
    $resultado = dame_desvios_ponderados_compra_energia_sensor($parametros);
    print(json_encode($resultado));
?>