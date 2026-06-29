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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/util_informes_potencias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COSTES_POTENCIAS_OPTIMAS_SENSOR_TARIFA_ELECTRICA, $_POST);

    // Se recuperan los costes y potencias óptimas de un sensor
    $parametros = $_POST;
    $resultado = dame_costes_potencias_optimas_sensor_tarifa_electricidad($parametros);
    print(json_encode($resultado));
?>

