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

    // Se recupera la tabla de ficheros excel 
    $parametros = $_POST;
    $resultado = dame_tabla_ficheros_excel_disponibles($parametros);
    print(json_encode($resultado));
    
?>