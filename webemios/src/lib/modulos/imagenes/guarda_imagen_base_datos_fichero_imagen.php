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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GUARDAR_IMAGEN_BASE_DATOS_FICHERO_IMAGEN, $_POST);

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $fichero_imagen = $_FILES["fichero_imagen"];

    // Se guarda la imagen en base de datos
    $resultado = guarda_imagen_base_datos_fichero_imagen(
        $origen,
        $id_origen,
        $fichero_imagen);
    print(json_encode($resultado));
?>