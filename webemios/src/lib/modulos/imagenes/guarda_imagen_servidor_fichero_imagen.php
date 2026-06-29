<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GUARDAR_IMAGEN_SERVIDOR_FICHERO_IMAGEN, $_POST);

    // Parámetros
    $fichero_imagen = $_FILES["fichero_imagen"];

    // Se guarda la imagen en el servidor
    $ruta_fichero_imagen = guarda_imagen_servidor_fichero_imagen($fichero_imagen);

    // Se devuelve el resultado
    $resultado = array(
        "res" => "OK",
        "ruta_fichero_imagen" => $ruta_fichero_imagen);
    print(json_encode($resultado));
?>