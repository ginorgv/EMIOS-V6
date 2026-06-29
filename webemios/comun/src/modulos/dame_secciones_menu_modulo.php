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
    $seccion = $_POST["seccion"];
    $parametros_extra = $_POST["parametros_extra"];

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

    // Se recuperan las secciones del menú
	$modulo_web = dame_modulo_web($modulo);
	$secciones_menu = $modulo_web->dame_secciones_menu($parametros_extra);
    $enlaces_secciones_menu = $modulo_web->dame_enlaces_secciones_menu($secciones_menu, $parametros_extra);

    // Se devuelven las secciones del menú
    print(json_encode(array(
        "res" => "OK",
        "secciones_menu" => $secciones_menu,
        "enlaces_secciones_menu" => $enlaces_secciones_menu))
    );
?>
