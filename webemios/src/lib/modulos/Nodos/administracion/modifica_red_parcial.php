<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_RED_PARCIAL, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_red = $_POST['id_red'];
    $logo_personalizado = $_POST['logo_personalizado'];
    $nombre_logo = $_POST['nombre_logo'];
    $url_logo = $_POST['url_logo'];
    $titulo_web = $_POST['titulo_web'];
    $tema = $_POST['tema'];
    $paleta_colores_graficas = $_POST['paleta_colores_graficas'];
    $periodo_completo_informes_defecto = $_POST['periodo_completo_informes_defecto'];
    $tipo_mapa = $_POST['tipo_mapa'];
    $nombre_mapa = $_POST['nombre_mapa'];
    $factor_reduccion_imagen_mapa_local = $_POST['factor_reduccion_imagen_mapa_local'];
    $etiquetas_mapa = $_POST['etiquetas_mapa'];
    $latitud_mapa_defecto = $_POST['latitud_mapa_defecto'];
    $longitud_mapa_defecto = $_POST['longitud_mapa_defecto'];
    $zoom_mapa_defecto = $_POST['zoom_mapa_defecto'];

    // Parámetros auxiliares
    $logo_personalizado_anterior = $_POST['logo_personalizado_anterior'];
    $tipo_mapa_anterior = $_POST['tipo_mapa_anterior'];

    // Si antes había logo personalizado y ahora no, se eliminan los logos anteriores
    if (($logo_personalizado_anterior == VALOR_SI) && ($logo_personalizado == VALOR_NO))
    {
        elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO, $id_red);
        elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_LOGO_PDF, $id_red);
    }

    // Si el tipo de mapa anterior era local y ahora es internet, se elimina la imagen anterior
    if (($tipo_mapa_anterior == TIPO_MAPA_LOCAL) && ($tipo_mapa == TIPO_MAPA_INTERNET))
    {
        elimina_imagen_base_datos(ORIGEN_IMAGEN_RED_MAPA, $id_red);
    }

    // Se elimina las posiciones del mapa si se ha modificado el tipo de mapa
    if ($tipo_mapa_anterior != $tipo_mapa)
    {
        elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_RED, $id_red);

        // Se recorren las localizaciones y si no tienen mapa personalizado
        // se eliminan las posiciones del mapa de esa localización (están utilizando el mapa de la red)
        $consulta_localizaciones = "
            SELECT id
            FROM localizaciones
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (mapa_personalizado = '".VALOR_NO."')";
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_LOCALIZACION, $fila_localizacion["id"]);
        }
    }

    // Se recuperan la fila de la red (antes de la modificación)
    $fila_red_anterior = dame_fila_red($id_red);

    // Se modifica la red
    $operacion_modificacion = "
        UPDATE redes
        SET
            logo_personalizado = '".$bd_red->_($logo_personalizado)."',
            nombre_logo = '".$bd_red->_($nombre_logo)."',
            url_logo = '".$bd_red->_($url_logo)."',
            titulo_web = '".$bd_red->_($titulo_web)."',
            tema = '".$bd_red->_($tema)."',
            paleta_colores_graficas = '".$bd_red->_($paleta_colores_graficas)."',
            periodo_completo_informes_defecto = '".$bd_red->_($periodo_completo_informes_defecto)."',
            tipo_mapa = '".$bd_red->_($tipo_mapa)."',
            nombre_mapa = '".$bd_red->_($nombre_mapa)."',
            factor_reduccion_imagen_mapa_local = '".$bd_red->_($factor_reduccion_imagen_mapa_local)."',
            etiquetas_mapa = '".$bd_red->_($etiquetas_mapa)."',
            latitud_mapa_defecto = '".$bd_red->_($latitud_mapa_defecto)."',
            longitud_mapa_defecto = '".$bd_red->_($longitud_mapa_defecto)."',
            zoom_mapa_defecto = '".$bd_red->_($zoom_mapa_defecto)."'
        WHERE
            id = '".$bd_red->_($id_red)."'";
    $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
    if ($res_modificacion == true)
    {
        // Se recupera la fila de la red actual
        $fila_red_actual = dame_fila_red($id_red);

        // Se añade la acción de usuario
        anyade_accion_usuario_modificar_red_parcial(
            $fila_red_actual,
            $fila_red_anterior);

        $res = "OK";
        $msg = $idiomas->_("Red modificada correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación parcial de la red
    function anyade_accion_usuario_modificar_red_parcial($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_RED_PARCIAL;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["logo_personalizado"] != $fila_anterior["logo_personalizado"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LOGO_PERSONALIZADO] = $fila_actual["logo_personalizado"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LOGO_PERSONALIZADO] = $fila_anterior["logo_personalizado"];
        }
        if ($fila_actual["nombre_logo"] != $fila_anterior["nombre_logo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOGO] = $fila_actual["nombre_logo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LOGO] = $fila_anterior["nombre_logo"];
        }
        if ($fila_actual["url_logo"] != $fila_anterior["url_logo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_URL_LOGO] = $fila_actual["url_logo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_URL_LOGO] = $fila_anterior["url_logo"];
        }
        if ($fila_actual["titulo_web"] != $fila_anterior["titulo_web"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TITULO_WEB] = $fila_actual["titulo_web"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TITULO_WEB] = $fila_anterior["titulo_web"];
        }
        if ($fila_actual["tema"] != $fila_anterior["tema"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TEMA] = $fila_actual["tema"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TEMA] = $fila_anterior["tema"];
        }
        if ($fila_actual["paleta_colores_graficas"] != $fila_anterior["paleta_colores_graficas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PALETA_COLORES_GRAFICAS] = $fila_actual["paleta_colores_graficas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PALETA_COLORES_GRAFICAS] = $fila_anterior["paleta_colores_graficas"];
        }

        // Parámetros de opciones de mapa y mapa
        if ($fila_actual["tipo_mapa"] != $fila_anterior["tipo_mapa"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_MAPA] = $fila_actual["tipo_mapa"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_MAPA] = $fila_anterior["tipo_mapa"];
        }
        if ($fila_actual["nombre_mapa"] != $fila_anterior["nombre_mapa"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_MAPA] = $fila_actual["nombre_mapa"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_MAPA] = $fila_anterior["nombre_mapa"];
        }
        if ($fila_actual["factor_reduccion_imagen_mapa_local"] != $fila_anterior["factor_reduccion_imagen_mapa_local"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN_MAPA_LOCAL] = $fila_actual["factor_reduccion_imagen_mapa_local"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN_MAPA_LOCAL] = $fila_anterior["factor_reduccion_imagen_mapa_local"];
        }
        if ($fila_actual["etiquetas_mapa"] != $fila_anterior["etiquetas_mapa"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ETIQUETAS_MAPA] = $fila_actual["etiquetas_mapa"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ETIQUETAS_MAPA] = $fila_anterior["etiquetas_mapa"];
        }
        if ($fila_actual["longitud_mapa_defecto"] != $fila_anterior["longitud_mapa_defecto"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA_DEFECTO] = $fila_actual["longitud_mapa_defecto"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA_DEFECTO] = $fila_anterior["longitud_mapa_defecto"];
        }
        if ($fila_actual["latitud_mapa_defecto"] != $fila_anterior["latitud_mapa_defecto"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA_DEFECTO] = $fila_actual["latitud_mapa_defecto"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA_DEFECTO] = $fila_anterior["latitud_mapa_defecto"];
        }
        if ($fila_actual["zoom_mapa_defecto"] != $fila_anterior["zoom_mapa_defecto"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA_DEFECTO] = $fila_actual["zoom_mapa_defecto"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA_DEFECTO] = $fila_anterior["zoom_mapa_defecto"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
