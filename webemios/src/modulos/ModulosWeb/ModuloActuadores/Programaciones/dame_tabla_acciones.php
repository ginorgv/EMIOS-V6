<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/Programacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_ACCIONES_PROGRAMACION, $_POST);

    $params = array();
    $params["id"] = $_POST['id_programacion'];
    $programacion = new Programacion($params);

	$res = "OK";
	$html = $programacion->dame_tabla_acciones();

	print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
