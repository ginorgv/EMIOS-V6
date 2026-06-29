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
    $log->info("SUBIR FICHERO ". $_POST);
    $log->info(print_r($_POST,true));
    $log->info(print_r($_FILES,true));

    	
    // Cómo subir el archivo
    $fichero = $_FILES["fichero_valores"];
    // Cargando el fichero en la carpeta "subidas"
    $log->info("TMP " . $fichero["tmp_name"]);
    $log->info("NAME " . $fichero["name"]);
    move_uploaded_file($fichero["tmp_name"], "subidas/".$fichero["name"]);
    chmod("subidas/".$fichero["name"], 0777);
    
    // Se recupera el estudio general
    $parametros = $_POST;
    $resultado = sube_fichero_excel($parametros);
    $log->info("FIN EJECUCION " . $resultado);
    print(json_encode($resultado));
?>