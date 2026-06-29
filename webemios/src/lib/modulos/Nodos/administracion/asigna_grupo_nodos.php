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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ASIGNAR_GRUPO_NODOS, $_POST);

    // Se asigna el grupo a los nodos
    $parametros = $_POST;
    $resultado = asigna_grupo_nodos($parametros);
    print(json_encode($resultado));
?>

