<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_ACCIONES_REGLA, $_POST);

    $id_regla = $_POST["id_regla"];
    $tipo = $_POST["tipo"];
    $html = AccionRegla::dame_tabla_acciones($id_regla, $tipo);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
