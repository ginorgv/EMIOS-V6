<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_informes_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_DATOS_INFORME_PLANTILLA_INFORME, $_POST);

    // Parámetros
    $parametros = $_POST;

    // Se recupera y devuelven los datos del informe de plantilla de informe
    $log = dame_log();
    $log->info("[".$_SESSION["id_usuario"]."] "."Inicio de obtención de datos del informe de plantilla de informe");
    $resultado = dame_datos_informe_plantilla_informe($parametros);
    $log->info("[".$_SESSION["id_usuario"]."] "."Fin de obtención de datos del informe de plantilla de informe");

    // https://stackoverflow.com/questions/17186675/why-i-get-err-response-headers-too-big-on-chrome/30227876
    header_remove('Set-Cookie');
    $resultado_json = json_encode($resultado);
    print($resultado_json);
?>
