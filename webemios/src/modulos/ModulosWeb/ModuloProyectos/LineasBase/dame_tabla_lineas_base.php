<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_LINEAS_BASE, $_POST);

    $filtro = $_POST["filtro"];
    $tipo = $_POST["tipo"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $html = LineaBase::dame_tabla_lineas_base(
        $filtro,
        $tipo,
        $intervalo_valores);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
