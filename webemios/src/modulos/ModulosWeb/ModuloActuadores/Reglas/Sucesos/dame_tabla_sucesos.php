<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_SUCESOS_REGLA, $_POST);

    $id_regla = $_POST["id_regla"];
    $html = SucesoRegla::dame_tabla_sucesos($id_regla);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
