<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_INSTALACIONES, $_POST);

    $filtro = $_POST["filtro"];
    $id_localizacion = $_POST["id_localizacion"];
    $incluir_localizaciones_descendientes = $_POST["incluir_localizaciones_descendientes"];
    $html = Instalacion::dame_tabla_instalaciones(
        $filtro,
        $id_localizacion,
        $incluir_localizaciones_descendientes);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
