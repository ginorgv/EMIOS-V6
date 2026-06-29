<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/util_resultados_mensuales.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_ESTUDIO_GENERAL_SENSOR, $_POST);

    $log=dame_log();
    $log->info("ELIMINAR FICHERO FICHERO ". $_POST);
    $log->debug(print_r($_POST,true));
    

    $parametros = $_POST;
    $resultado = elimina_fichero_excel($parametros);
    $log->info("FIN EJECUCION " . $resultado);
    print(json_encode($resultado));
?>