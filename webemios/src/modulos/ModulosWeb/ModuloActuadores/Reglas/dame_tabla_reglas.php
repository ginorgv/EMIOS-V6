<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_REGLAS, $_POST);

    $filtro = $_POST["filtro"];
    $habilitacion = $_POST["habilitacion"];
    $activacion = $_POST["activacion"];
    $actualizacion_periodica_activada = $_POST["actualizacion_periodica_activada"];
    $html = Regla::dame_tabla_reglas(
        $filtro,
        $habilitacion,
        $activacion,
        $actualizacion_periodica_activada);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
