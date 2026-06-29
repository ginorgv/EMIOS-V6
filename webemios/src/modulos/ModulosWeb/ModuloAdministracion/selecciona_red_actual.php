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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_SELECCIONAR_RED, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $id_red = $_POST["id_red"];

    // Si es la misma red no se hace nada
    if ($id_red == $_SESSION["id_red"])
    {
        $res = "ERROR";
        $msg = $idiomas->_("La red actual es la misma que la red seleccionada");

        print(json_encode(array(
            "res" => $res,
            "msg" => $msg)));
        return;
    }

    // Se guarda la red anterior
    $id_red_anterior = $_SESSION["id_red"];

    // Se establece la red
    $_SESSION["id_red"] = $id_red;

    // Tema y paleta de colores anteriores
    $tema_anterior = $_SESSION["tema"];
    $paleta_colores_graficas_anterior = $_SESSION["paleta_colores_graficas"];

    // Se cargan los parámetros de la red actual
    carga_parametros_red($_SESSION["id_red"]);

    // Tema y paleta de colores modificados
    $tema_modificado = ($tema_anterior != $_SESSION["tema"]);
    $paleta_colores_graficas_modificada = ($paleta_colores_graficas_anterior != $_SESSION["paleta_colores_graficas"]);

    // Se recargan los colores del tema (si se ha modificado)
    if ($tema_modificado == true)
    {
        carga_colores_tema_actual();
    }

    // Descripción de la red actual
    $nombre_red = dame_nombre_red($id_red);
    switch ($id_red)
    {
        case ID_NINGUNO:
        {
            $nombre_red = strtolower($nombre_red);
            break;
        }
    }

    // Se actualiza la descripción del usuario con la descripción de la red actual
    $html_descripcion_usuario = dame_descripcion_usuario();

    // Se recupera el menú de módulos de la red actual
    $html_menu_modulos = dame_menu_modulos(MODULO_ADMINISTRACION);

    // Se actualiza el logo de la red actual
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

    // Se establece la localización por defecto de la red actual
    establece_localizacion_defecto();

    // Se añade la acción de usuario
    anyade_accion_usuario_seleccion_red(
        $_SESSION["id_usuario"],
        $_SESSION["perfil"],
        $_SESSION["id_red"],
        $id_red_anterior);

    // Resultado
    $msg = $idiomas->_("Red actual establecida correctamente")." (".htmlspecialchars($nombre_red, ENT_QUOTES).")";
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


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de selección de red
    function anyade_accion_usuario_seleccion_red(
        $id_usuario,
        $perfil_usuario,
        $id_red_actual,
        $id_red_anterior)
    {
        // Nombre de usuario y de la red
        $nombre_usuario = dame_nombre_usuario($id_usuario);
        $nombre_red_actual = dame_nombre_red($id_red_actual);
        $nombre_red_anterior = dame_nombre_red($id_red_anterior);
        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_red_actual);
        sustituye_valor_parametro_nombre_elemento_accion_usuario($nombre_red_anterior);

        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_SELECCION_RED;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_USUARIO] = $nombre_usuario;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PERFIL_USUARIO] = $perfil_usuario;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_RED] = $nombre_red_actual;

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
            $nombre_red_actual,
            $nombre_red_anterior));

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
