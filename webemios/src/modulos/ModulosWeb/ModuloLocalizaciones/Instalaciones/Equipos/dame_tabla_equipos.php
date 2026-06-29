<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_EQUIPOS_INSTALACIONES, $_POST);

    $id_instalacion = $_POST["id_instalacion"];
    $html = EquipoInstalacion::dame_tabla_equipos($id_instalacion);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
