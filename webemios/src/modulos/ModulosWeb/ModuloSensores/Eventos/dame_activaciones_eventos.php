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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_informes_eventos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_ACTIVACIONES_EVENTOS, $_POST);

    // Se recuperan los datos de activaciones de eventos
    $parametros = $_POST;
    $resultado = dame_activaciones_eventos($parametros);
    print(json_encode($resultado));
?>
