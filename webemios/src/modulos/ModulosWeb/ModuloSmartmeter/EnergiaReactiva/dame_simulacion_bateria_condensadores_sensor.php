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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/util_informes_energia_reactiva.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SIMULACION_BATERIA_CONDENSADORES_SENSOR, $_POST);

    // Se recupera la información de simulación de batería de condensadores de un sensor
    $parametros = $_POST;
    $resultado = dame_simulacion_bateria_condensadores_sensor($parametros);
    print(json_encode($resultado));
?>