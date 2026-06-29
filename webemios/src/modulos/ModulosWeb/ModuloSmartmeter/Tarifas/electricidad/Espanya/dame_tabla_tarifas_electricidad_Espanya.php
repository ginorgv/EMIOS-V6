<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/Espanya/TarifaElectrica_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_TARIFAS, $_POST);

    $filtro = $_POST["filtro"];
    $tipo = $_POST["tipo"];
    $contrato = $_POST["contrato"];
    $id_grupo = $_POST["id_grupo"];
    $estado = $_POST["estado"];
    $html = TarifaElectrica_Espanya::dame_tabla_tarifas_electricas(
        $filtro,
        $tipo,
        $contrato,
        $id_grupo,
        $estado);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
