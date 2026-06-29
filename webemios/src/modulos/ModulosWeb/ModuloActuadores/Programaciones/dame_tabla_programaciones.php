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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PROGRAMACIONES, $_POST);

    $filtro = $_POST["filtro"];
    $clase_actuador = $_POST["clase_actuador"];
    $html = Programacion::dame_tabla_programaciones($filtro, $clase_actuador);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
