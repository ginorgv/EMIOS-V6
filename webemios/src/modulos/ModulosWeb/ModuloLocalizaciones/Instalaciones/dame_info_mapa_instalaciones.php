<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFO_MAPA_INSTALACIONES, $_POST);

    // Se recupera la información del mapa de instalaciones
    $parametros = $_POST;
    $resultado = dame_info_mapa_instalaciones($parametros);

    // Se devuelve el resultado
    print(json_encode($resultado));
?>
