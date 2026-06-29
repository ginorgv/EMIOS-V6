<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PROYECTOS, $_POST);

    $filtro = $_POST["filtro"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $estado_avance = $_POST["estado_avance"];
    $estado = $_POST["estado"];
    $html = Proyecto::dame_tabla_proyectos(
        $filtro,
        $intervalo_valores,
        $estado_avance,
        $estado);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
