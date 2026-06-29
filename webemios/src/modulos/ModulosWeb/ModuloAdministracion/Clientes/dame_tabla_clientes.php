<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Clientes/Cliente.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_CLIENTES, $_POST);

    $html = Cliente::dame_tabla_clientes();

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
