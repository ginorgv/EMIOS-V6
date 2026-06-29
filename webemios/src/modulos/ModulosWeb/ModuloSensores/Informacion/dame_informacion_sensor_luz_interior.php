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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/util_informes_informacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFORMACION_LUZ_INTERIOR_SENSOR, $_POST);

    // Se recupera y devuelve la información de luz interior de un sensor
    $parametros = $_POST;
    $parametros["clase_sensor"] = CLASE_SENSOR_LUZ_INTERIOR;
    $filas_valores_sensor = dame_filas_valores_sensor($parametros);
    $resultado = dame_informacion_sensor_luz_interior($parametros, $filas_valores_sensor);
    print(json_encode($resultado));
?>