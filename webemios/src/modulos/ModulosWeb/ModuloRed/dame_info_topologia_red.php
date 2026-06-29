<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_TOPOLOGIA_RED, $_POST);

    $clase_sensor = $_POST["clase_sensor"];
    $clase_actuador = $_POST["clase_actuador"];

    // Se recupera el nodo de la red y se recupera la topología
	$nodo_red = Nodo::dame_nodo_red();
    $topologia_red = $nodo_red->dame_info_topologia_red($clase_sensor, $clase_actuador);

	print(json_encode(array(
        "res" => "OK",
        "info" => json_encode($topologia_red)))
    );
?>
