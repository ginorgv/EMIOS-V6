<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_TOPOLOGIA_INSTALACION, $_POST);

    // Parámetros
    $id_instalacion = $_POST["id_instalacion"];

    // Se recupera la topología de la instalación
    $fila_instalacion = dame_fila_instalacion($id_instalacion);
    $instalacion = new Instalacion($fila_instalacion);
    $topologia_instalacion = $instalacion->dame_info_topologia_instalacion();

	print(json_encode(array(
        "res" => "OK",
        "info" => json_encode($topologia_instalacion)))
    );
?>
