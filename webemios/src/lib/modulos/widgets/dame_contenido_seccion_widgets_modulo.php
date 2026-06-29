<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_pestanyas_widgets.php');


    // Parámetros
    $modulo = $_POST["modulo"];
    $id_pestanya = $_POST["id_pestanya"];
    $parametros_extra = $_POST["parametros_extra"];
    if (is_string($parametros_extra) === true)
    {
        $parametros_extra = json_decode($parametros_extra, true);
    }

    // Módulo
    $contenido = "<div id='modulo' name='".$modulo."' hidden></div>";

    // Se añade la tabla de selección de localización actual (sin seleccion de ratio)
    $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
    if ($mostrar_controles_localizaciones == true)
    {
        $mostrar_seleccion_ratio = false;
        $seleccion_ratio_visible = false;
        $contenido_oculto = true;
        if (array_key_exists("seleccion_localizacion_actual_desplegada", $parametros_extra) == true)
        {
            $contenido_oculto = ($parametros_extra["seleccion_localizacion_actual_desplegada"] == VALOR_NO);
        }
        $contenido .= dame_tabla_seleccion_localizacion_actual_ratio(
            $mostrar_seleccion_ratio,
            $seleccion_ratio_visible,
            $contenido_oculto);
    }
    $contenido .= dame_pestanyas_widgets_modulo($modulo, $id_pestanya);

	print(json_encode(array(
        "contenido" => $contenido))
    );
?>



