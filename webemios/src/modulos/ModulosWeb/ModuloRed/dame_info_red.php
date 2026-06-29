<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_RED, $_POST);

    $tabla_informacion_red_actual = dame_tabla_informacion_red_actual();
	$html = $tabla_informacion_red_actual->dame_tabla();

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
