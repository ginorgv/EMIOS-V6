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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/util_informes_autoconsumo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SIMULACION_AUTOCONSUMO_SENSOR, $_POST);

    // Se recupera la información de simulación de autoconsumo
    $parametros = $_POST;
    $resultado = dame_simulacion_autoconsumo_sensor($parametros);
    print(json_encode($resultado));
?>