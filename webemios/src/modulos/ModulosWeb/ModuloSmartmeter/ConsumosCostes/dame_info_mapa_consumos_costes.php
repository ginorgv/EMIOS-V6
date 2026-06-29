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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_MAPA_CONSUMOS_COSTES, $_POST);

    // Se recupera la información del mapa de consumos y costes
    $parametros = $_POST;
    $medicion = $parametros["medicion"];
    switch ($medicion)
    {
        case MEDICION_ELECTRICIDAD:
        {
            $clase_sensor = CLASE_SENSOR_ENERGIA_ACTIVA;
            break;
        }
        case MEDICION_GAS:
        {
            $clase_sensor = CLASE_SENSOR_GAS;
            break;
        }
        case MEDICION_AGUA:
        {
            $clase_sensor = CLASE_SENSOR_AGUA;
            break;
        }
        default:
        {
            throw new Exception("Medición desconocida: '".$medicion."'");
        }
    }
    $parametros["clase_sensor"] = $clase_sensor;
    $resultado = dame_info_mapa_consumos_costes($parametros);

    // Se devuelve el resultado
    print(json_encode($resultado));
?>
