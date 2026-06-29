<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
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
    if (is_string($parametros_extra) === true)
    {
        $parametros_extra = json_decode($parametros_extra, true);
    }

    // Si el módulo o la sección no están disponibles se muestra un error
    if ((dame_modulo_disponible_sesion($modulo) == false) ||
        (dame_seccion_disponible_sesion($modulo, $seccion) == false))
    {
        $log->warn("[".$_SESSION["id_usuario"]."] "."Se ha intentado acceder a una sección no disponible".
            " (módulo: '".$modulo."', sección: '".$seccion."')");

        $mensaje_error = $idiomas->_("Se ha intentado acceder a una sección no disponible");
        $contenido_seccion_error = dame_contenido_seccion_error($mensaje_error);
        print(json_encode(array(
            "res" => "OK",
            "msg_error" => htmlspecialchars($mensaje_error, ENT_QUOTES),
            "html" => $contenido_seccion_error)));
        return;
    }

    // Se recupera el contenido de la sección (si ocurre un error, se muestra un contenido de error)
    try
    {
        $modulo_web = dame_modulo_web($modulo);
        $modulo_web->dame_contenido_seccion($seccion, $parametros_extra);
    }
    catch (Exception $exception)
    {
        $log = dame_log();
        $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

        $descripcion_seccion = $modulo_web->dame_descripcion_seccion($seccion);
        $mensaje_error = $idiomas->_("Ha ocurrido un error al cargar el contenido de la sección")." (".$descripcion_seccion.")";
        $contenido_seccion_error = dame_contenido_seccion_error($mensaje_error);
        print(json_encode(array(
            "res" => "OK",
            "msg_error" => htmlspecialchars($mensaje_error, ENT_QUOTES),
            "html" => $contenido_seccion_error)));
        return;
    }


    // Devuelve el contenido sección con error
    function dame_contenido_seccion_error($mensaje_error)
    {
        $idiomas = new Idiomas();
        $tabla = new TablaDatos(
            "tabla-contenido-seccion-error",
            $idiomas->_("Error"),
            TIPO_TABLA_DATOS_CONTENEDOR,
            array()
        );
        $parametros_contenido = array(
            "sin_margenes" => true
        );
        $texto_contenido_seccion_error = "
            <div class='texto-contenido-seccion-error'>
                <i class='icon-warning-sign color-rojo'></i> ".htmlspecialchars($mensaje_error, ENT_QUOTES)."
            </div>";
        $contenedor_texto_contenido_seccion_error = "<div>".$texto_contenido_seccion_error."</div>";
        $tabla->anyade_contenido("", $contenedor_texto_contenido_seccion_error, $parametros_contenido);

        $contenido = $tabla->dame_tabla();
        return($contenido);
    }
?>
