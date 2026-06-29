<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_herramientas_nodos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ASIGNAR_LOCALIZACION_NODOS, $_POST);

    // Se asigna la localización a los nodos
    $parametros = $_POST;
    $resultado = asigna_localizacion_nodos($parametros);
    print(json_encode($resultado));
?>

