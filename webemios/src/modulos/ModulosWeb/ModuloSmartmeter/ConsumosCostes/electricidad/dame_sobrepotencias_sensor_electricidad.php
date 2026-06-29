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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SOBREPOTENCIAS_SENSOR, $_POST);

    // Se recuperan las sobrepotencias del sensor
    $parametros = $_POST;
    $resultado = dame_sobrepotencias_sensor_electricidad($parametros);
    print(json_encode($resultado));    
?>
