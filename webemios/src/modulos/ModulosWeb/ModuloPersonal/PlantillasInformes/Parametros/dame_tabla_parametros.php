<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/ParametroPlantillaInforme.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PARAMETROS_PLANTILLA_INFORME, $_POST);

    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $html = ParametroPlantillaInforme::dame_tabla_parametros($id_plantilla_informe);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
