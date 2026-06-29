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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/util_informes_informacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COSTES_CONSUMO_SENSOR_TARIFAS, $_POST);

    // Se recuperan los costes de consumo actuales y de tarifas de un sensor entre el rango de fechas
    $parametros = $_POST;
    $resultado = dame_costes_consumo_sensor_tarifas($parametros);
    print(json_encode($resultado));
?>
