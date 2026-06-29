<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/util_modulos_web.php');


    $idiomas = new Idiomas();

    // Log
    $log = dame_log();
    $log->info("[".$_SESSION["id_usuario"]."] "."Parámetros: ".json_encode($_POST));

    // Comprobación de sesión correcta
    $respuesta_sesion_correcta = dame_sesion_correcta();
    if ($respuesta_sesion_correcta["sesion_correcta"] == false)
    {
        print($respuesta_sesion_correcta["respuesta_script"]);
        return;
    }

    // Parámetros
    $modulo = $_POST["modulo"];
    $parametros_extra = $_POST["parametros_extra"];
    if (is_string($parametros_extra) === true)
    {
        $parametros_extra = json_decode($parametros_extra, true);
    }
    if ($parametros_extra == "")
    {
        $parametros_extra = NULL;
    }

    // Si el módulo o la sección no está disponible se muestra un error
    if (dame_modulo_disponible_sesion($modulo) == false)
    {
        $log->warn("[".$_SESSION["id_usuario"]."] "."Se ha intentado acceder a un módulo no disponible".
            " (módulo: '".$modulo."')");

        $mensaje_error = $idiomas->_("Se ha intentado acceder a un módulo no disponible");
        print(json_encode(array(
            "res" => "ERROR",
            "msg" => $mensaje_error)));
        return;
    }

    // Se recupera el menú de secciones
	$modulo_web = dame_modulo_web($modulo);
	$menu_secciones = $modulo_web->dame_menu_secciones($parametros_extra);

    // Se devuelve el menú de secciones
    print(json_encode(array(
        "res" => "OK",
        "html" => $menu_secciones["html"],
        "secciones_menu" => $menu_secciones["secciones_menu"])));
?>
