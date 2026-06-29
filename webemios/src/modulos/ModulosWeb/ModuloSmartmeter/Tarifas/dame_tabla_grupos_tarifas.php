<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/GrupoTarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_GRUPOS_TARIFAS, $_POST);

    $medicion = $_POST["medicion"];
    $filtro = $_POST["filtro"];
    $estado = $_POST["estado"];
    $html = GrupoTarifas::dame_tabla_grupos_tarifas(
        $medicion,
        $filtro,
        $estado);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
