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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Informacion/util_informes_informacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFORMACION_PROYECTO, $_POST);

    // Se recuperan los datos de información del proyecto
    $parametros = $_POST;
    $resultado = dame_informacion_proyecto($parametros);
    print(json_encode($resultado));
?>
