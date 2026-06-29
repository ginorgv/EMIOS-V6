<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_herramientas_actuadores.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_BORRAR_ACCIONES_ENVIADAS, $_POST);

    $resultado_borrado_acciones = borra_acciones_enviadas($_POST);
    print(json_encode($resultado_borrado_acciones));
?>
