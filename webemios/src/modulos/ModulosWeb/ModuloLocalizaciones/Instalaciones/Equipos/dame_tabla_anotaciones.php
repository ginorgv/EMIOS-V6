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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    

    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_ANOTACIONES_EQUIPO_INSTALACION, $_POST);

    $id_equipo = $_POST['id_equipo'];
    $fila_equipo = dame_fila_equipo_instalacion($id_equipo);
    $equipo = new EquipoInstalacion($fila_equipo);

	$res = "OK";
	$html = $equipo->dame_tabla_anotaciones();

	print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
