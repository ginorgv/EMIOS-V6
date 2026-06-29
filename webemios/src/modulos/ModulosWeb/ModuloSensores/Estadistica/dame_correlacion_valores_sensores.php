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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_informes_estadistica.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CORRELACION_VALORES_SENSORES, $_POST);

    // Se recuperan los datos de correlación de valores de sensores
    $parametros = $_POST;
    $resultado = dame_correlacion_valores_sensores($parametros);
    print(json_encode($resultado));
?>
