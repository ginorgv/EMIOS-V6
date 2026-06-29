<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_IMPORTACIONES_VALORES_SENSORES_PENDIENTES, $_POST);

    $modulo = $_POST["modulo"];
    $html = ImportacionValoresSensorPendiente::dame_tabla_importaciones_pendientes($modulo);

	print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
