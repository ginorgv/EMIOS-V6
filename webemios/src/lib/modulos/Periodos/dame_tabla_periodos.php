<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/Periodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PERIODOS, $_POST);

    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $html = Periodo::dame_tabla_periodos($origen, $id_origen);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
