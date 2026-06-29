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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Analisis/util_informes_analisis.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_ANALISIS_COMPORTAMIENTO_VALORES_SENSORES, $_POST);

    // Se recuperan los datos de análisis de comportamiento de sensores
    $parametros = $_POST;
    $resultado = dame_analisis_comportamiento_valores_sensores($parametros);
    print(json_encode($resultado));
?>
