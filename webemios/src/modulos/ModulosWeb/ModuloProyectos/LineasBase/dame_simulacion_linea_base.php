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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_informes_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_SIMULACION_LINEA_BASE, $_POST);

    // Se recuperan los datos de simulación de línea base
    $parametros = $_POST;
    $resultado = dame_simulacion_linea_base($parametros);
    print(json_encode($resultado));
?>
