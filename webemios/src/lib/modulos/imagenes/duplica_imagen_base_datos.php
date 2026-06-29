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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_DUPLICAR_IMAGEN_BASE_DATOS, $_POST);

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen_anterior = $_POST["id_origen_anterior"];
    $id_origen = $_POST["id_origen"];

    // Se duplica la imagen en base de datos
    $resultado = duplica_imagen_base_datos(
        $origen,
        $id_origen_anterior,
        $id_origen);
    print(json_encode($resultado));
?>