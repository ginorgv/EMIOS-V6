<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_CAPAS_MAPA, $_POST);

    // Parámetros
    $modulo = $_POST["modulo"];
    $parametros_filtro_mapa = $_POST["parametros_filtro_mapa"];

    // Se recuperan las capas del mapa
    $capas = dame_capas_mapa($modulo, $parametros_filtro_mapa);

	print(json_encode(array(
        "res" => "OK",
        "capas" => json_encode($capas)))
    );
?>
