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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CONSUMOS_COSTES_SENSORES_GENERALES, $_POST);

    // Se recuperan los consumos y costes de sensores generales
    $parametros = $_POST;
    $medicion = $parametros["medicion"];
    $clase_sensor = dame_clase_sensor_medicion($medicion);
    $parametros["clase_sensor"] = $clase_sensor;

    $filas_valores_sensores = dame_filas_valores_sensores($parametros);
    $resultado = dame_consumos_costes_sensores_generales($parametros, $filas_valores_sensores);
    print(json_encode($resultado));
?>
