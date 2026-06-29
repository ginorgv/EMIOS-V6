<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_LOCALIZACIONES, $_POST);

    $filtro = $_POST["filtro"];
    $id_localizacion_detalles = $_POST["id_localizacion_detalles"];
    $html = Localizacion::dame_tabla_localizaciones($filtro, $id_localizacion_detalles);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
