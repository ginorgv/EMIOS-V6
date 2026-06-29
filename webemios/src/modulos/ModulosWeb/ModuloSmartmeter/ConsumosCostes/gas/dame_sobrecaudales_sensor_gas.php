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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/Espanya/util_informes_consumos_costes_gas_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SOBRECAUDALES_SENSOR, $_POST);

    // Se recuperan los sobrecaudales del sensor
    $parametros = $_POST;
    $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
    switch ($pais_tarifas_gas)
    {
        case PAIS_ESPANYA:
        {
            $resultado = dame_sobrecaudales_sensor_gas_Espanya($parametros);
            break;
        }
        default:
        {
            throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
        }
    }
    print(json_encode($resultado));
?>
