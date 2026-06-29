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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_CARGAR_IMAGEN_BASE_DATOS, $_POST);

    // Parámetros
    $origen = $_POST["origen"];
    $id_origen = $_POST["id_origen"];
    $nombre_fichero_imagen = $_POST["nombre_fichero_imagen"];

    // Se carga la imagen de base de datos
    $info_imagen = carga_imagen_base_datos($origen, $id_origen, $nombre_fichero_imagen);

    // Se devuelve el resultado
    $resultado = array(
        "res" => "OK",
        "ruta_fichero_imagen" => $info_imagen["ruta_fichero_imagen"],
        "anchura_imagen" => $info_imagen["anchura_imagen"],
        "altura_imagen" => $info_imagen["altura_imagen"]);
    print(json_encode($resultado));
?>