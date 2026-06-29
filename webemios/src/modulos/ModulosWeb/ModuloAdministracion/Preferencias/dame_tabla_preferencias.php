<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/Preferencias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PREFERENCIAS, $_POST);

    $html = Preferencias::dame_tabla_preferencias();

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
