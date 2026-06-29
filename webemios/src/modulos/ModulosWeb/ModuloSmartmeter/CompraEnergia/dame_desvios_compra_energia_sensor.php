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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_DESVIOS_COMPRA_ENERGIA_SENSOR, $_POST);

    // Se recupera la información de desvíos de compra de energía de un sensor
    $parametros = $_POST;
    $parametros["intervalo_valores"] = INTERVALO_VALORES_HORA;
    $filas_valores_sensor = dame_filas_valores_sensor($parametros);
    $resultado = dame_desvios_compra_energia_sensor($parametros, $filas_valores_sensor);
    print(json_encode($resultado));
?>