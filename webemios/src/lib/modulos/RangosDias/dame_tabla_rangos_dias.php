<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/RangoDias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_RANGOS_DIAS, $_POST);

    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $html = RangoDias::dame_tabla_rangos_dias($origen, $id_origen);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
