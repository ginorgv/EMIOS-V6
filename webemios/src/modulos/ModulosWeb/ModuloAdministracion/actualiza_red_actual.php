<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_pie_pagina.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sesion.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_sesion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ACTUALIZAR_RED_ACTUAL, $_POST);

	$idiomas = new Idiomas();

    // Tema y paleta de colores anteriores
    $tema_anterior = $_SESSION["tema"];
    $paleta_colores_graficas_anterior = $_SESSION["paleta_colores_graficas"];

    // Se cargan los parámetros de la red
    carga_parametros_red($_SESSION["id_red"]);

    // Tema y paleta de colores modificados
    $tema_modificado = ($tema_anterior != $_SESSION["tema"]);
    $paleta_colores_graficas_modificada = ($paleta_colores_graficas_anterior != $_SESSION["paleta_colores_graficas"]);

    // Se recargan los colores del tema (si se ha modificado)
    if ($tema_modificado == true)
    {
        carga_colores_tema_actual();
    }

    // Se actualiza el nombre de red actual
    $html_descripcion_usuario = dame_descripcion_usuario();

    // Se recupera el menú de módulos de la red actual
    $html_menu_modulos = dame_menu_modulos(MODULO_ADMINISTRACION);

    // Se actualiza el logo de la red
    if (isset($_SESSION["ruta_logo"]))
    {
        $ruta_logo = $_SESSION["ruta_logo"];
        $url_logo = $_SESSION["url_logo"];
    }
    else
    {
        $ruta_logo = "./rsc/imagenes/logo_web.png";
        $url_logo = WEB_ENERGY_MINUS;
    }
    $html_logo = '<img src="'.$ruta_logo.'">';
    if ($url_logo != "")
    {
        $url_logo_http = dame_url_http($url_logo);
        $html_logo = "<a href='".$url_logo_http."'>".$html_logo."</a>";
    }

    // Título de la web actual
    $titulo_web = $_SESSION["titulo_web"];

    // Pie de páinga
    $texto_pie_pagina = dame_texto_pie_pagina();

    // Resultado
    $msg = $idiomas->_("Red actual modificada correctamente");
    $resultado = array(
        "res" => "OK",
        "msg" => $msg,
        "tema_modificado" => $tema_modificado,
        "paleta_colores_graficas_modificada" => $paleta_colores_graficas_modificada,
        "html_descripcion_usuario" => $html_descripcion_usuario,
        "html_menu_modulos" => $html_menu_modulos,
        "html_logo" => $html_logo,
        "titulo_web" => $titulo_web,
        "texto_pie_pagina" => $texto_pie_pagina);
    if ($tema_modificado == true)
    {
        $informacion_tema_actual = array(
            "color_tema_oscuro" => $_SESSION["colores"]["color_tema_oscuro"],
            "color_tema_intermedio" => $_SESSION["colores"]["color_tema_intermedio"],
            "color_tema_claro" => $_SESSION["colores"]["color_tema_claro"],
            "color_tema_fondo" => $_SESSION["colores"]["color_tema_fondo"]);
        $resultado = array_merge($resultado, $informacion_tema_actual);
    }
    if ($paleta_colores_graficas_modificada == true)
    {
        $informacion_extra_preferencias_actuales = dame_informacion_extra_preferencias_actuales();
        $resultado = array_merge($resultado, $informacion_extra_preferencias_actuales);
    }

    // Se añade la información local
    $informacion_local = dame_informacion_local();
    $resultado_con_informacion_local = array_merge($resultado, $informacion_local);

    // Se devuelve el resultado
    print(json_encode($resultado_con_informacion_local));
?>
