<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_PARAMETROS_ENERGIA_ELECTRICA, $_POST);

    $tabla_informacion_parametros_energia_electrica = dame_tabla_informacion_parametros_energia_electricidad_Espanya();
	$html = $tabla_informacion_parametros_energia_electrica->dame_tabla();

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
