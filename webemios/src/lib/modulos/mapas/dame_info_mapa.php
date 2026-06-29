<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');


    // Parámetros
    $origen_mapa = $_POST["origen_mapa"];
    $id_origen_mapa = $_POST["id_origen_mapa"];

    // Se recupera la información del mapa
    $info_mapa = dame_info_mapa($origen_mapa, $id_origen_mapa);

    // Se devuelve el resultado
    $resultado = array(
        "res" => "OK",
        "tipo_mapa" => $info_mapa["tipo_mapa"],
        "nombre_mapa" => $info_mapa["nombre_mapa"],
        "factor_reduccion_imagen_mapa_local" => $info_mapa["factor_reduccion_imagen_mapa_local"],
        "latitud_mapa_defecto" => $info_mapa["latitud_mapa_defecto"],
		"longitud_mapa_defecto" => $info_mapa["longitud_mapa_defecto"],
		"zoom_mapa_defecto" => $info_mapa["zoom_mapa_defecto"],
        "origen_imagen" => $info_mapa["origen_imagen"],
        "id_origen_imagen" => $info_mapa["id_origen_imagen"]);
    print(json_encode($resultado));
?>
