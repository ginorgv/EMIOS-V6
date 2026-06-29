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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_COMPROBAR_IMAGEN_CORRECTA, $_POST);

    // Parámetros
    $origen = $_POST["origen"];
    $fichero_imagen = $_FILES["fichero_imagen"];

    // Se comprueba si la imagen es correcta
    $tipo_imagen = "";
    $anchura_imagen = 0;
    $altura_imagen = 0;
    $msg_error = "";
    $imagen_correcta = comprueba_imagen_correcta(
        $origen,
        $fichero_imagen,
        $tipo_imagen,
        $anchura_imagen,
        $altura_imagen,
        $msg_error);

    // Se crea y devuelve el resultado
    if ($imagen_correcta == true)
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $msg_error;
    }
    $resultado = array(
        "res" => $res,
        "msg" => $msg);
    print(json_encode($resultado));
?>