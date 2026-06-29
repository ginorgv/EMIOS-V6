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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_compra_energia.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_IMPORTAR_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR, $_POST);

    // Se importan los valores diarios de compra de energía de un sensor
    $resultado = importa_valores_diarios_compra_energia_sensor($_POST, $_FILES);
    print(json_encode($resultado));
?>