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
	include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/util_localizaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_TOPOLOGIA_LOCALIZACION, $_POST);

    // Parámetros
    $id_localizacion = $_POST["id_localizacion"];

    // Se recupera la topología de la localización
    $fila_localizacion = dame_fila_localizacion($id_localizacion);
    $localizacion = new Localizacion($fila_localizacion);
    $topologia_localizacion = $localizacion->dame_info_topologia_localizacion();

	print(json_encode(array(
        "res" => "OK",
        "info" => json_encode($topologia_localizacion)))
    );
?>
