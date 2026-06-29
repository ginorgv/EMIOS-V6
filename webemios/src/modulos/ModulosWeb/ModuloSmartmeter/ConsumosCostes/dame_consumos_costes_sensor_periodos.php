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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CONSUMOS_COSTES_SENSOR_PERIODOS, $_POST);

    // Se recuperan los consumos y costes del sensor de comparación de periodos
    $parametros = $_POST;
    $medicion = $parametros["medicion"];
    $clase_sensor = dame_clase_sensor_medicion($medicion);
    $parametros["clase_sensor"] = $clase_sensor;

    $resultado = dame_consumos_costes_sensor_periodos($parametros);
    print(json_encode($resultado));
?>