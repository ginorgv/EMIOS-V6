<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_EVENTOS, $_POST);

    $filtro = $_POST["filtro"];
    $clase_sensor = $_POST["clase_sensor"];
    $alarma = $_POST["alarma"];
    $activacion = $_POST["activacion"];
    $actualizacion_periodica_activada = $_POST["actualizacion_periodica_activada"];
    $html = Evento::dame_tabla_eventos(
        $filtro,
        $clase_sensor,
        $alarma,
        $activacion,
        $actualizacion_periodica_activada);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
