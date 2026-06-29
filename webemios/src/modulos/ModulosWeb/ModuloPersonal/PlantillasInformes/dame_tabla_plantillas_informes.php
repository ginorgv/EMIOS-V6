<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInforme/PlantillaInforme.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_PLANTILLAS_INFORMES, $_POST);

    $filtro = $_POST["filtro"];
    $html = PlantillaInforme::dame_tabla_plantillas_informes($filtro);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
