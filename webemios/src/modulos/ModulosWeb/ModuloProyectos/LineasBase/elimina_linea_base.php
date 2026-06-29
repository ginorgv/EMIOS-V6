<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_linea_base = $_POST['id_linea_base'];

    // Flag de eliminar la línea base
    $eliminar_linea_base = true;

    // Comprobaciones antes de eliminar la línea base:
    // - Se comprueba si la línea base es hija de otra línea base (excepciones de líneas base)
    // - Se comprueba si la línea base está asignada a algún proyecto
    // - Se comprueba si la línea base está asignada a algún evento

    // Se comprueba si la línea base es hija de otra línea base (excepciones de líneas base)
    if ($eliminar_linea_base == true)
    {
        $consulta_excepciones_linea_base = "
            SELECT *
            FROM excepciones_lineas_base
            WHERE
                linea_base_hija = '".$bd_red->_($id_linea_base)."'
            ORDER BY nombre ASC";
        $res_excepciones_linea_base = $bd_red->ejecuta_consulta($consulta_excepciones_linea_base);
        if ($res_excepciones_linea_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_excepciones_linea_base."'");
        }
        if ($res_excepciones_linea_base->dame_numero_filas() > 0)
        {
            $eliminar_linea_base = false;

            $fila_excepcion_linea_base = $res_excepciones_linea_base->dame_siguiente_fila();
            $id_linea_base_padre_excepcion_linea_base = $fila_excepcion_linea_base["linea_base_padre"];
            $nombre_excepcion_linea_base = $fila_excepcion_linea_base["nombre"];
            $nombre_linea_base_padre_excepcion_linea_base = dame_nombre_linea_base($id_linea_base_padre_excepcion_linea_base);

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la línea base porque está en excepciones de alguna línea base")."\n(".
                $idiomas->_("excepción").": ".$nombre_excepcion_linea_base.", ".
                $idiomas->_("línea base").": ".$nombre_linea_base_padre_excepcion_linea_base.")";
        }
    }

    // Se comprueba si la línea base está asignada a algún proyecto
    if ($eliminar_linea_base == true)
    {
        $consulta_proyectos = "
            SELECT nombre
            FROM proyectos
            WHERE
                linea_base = '".$bd_red->_($id_linea_base)."'
            ORDER BY nombre ASC";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if ($res_proyectos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
        }
        if ($res_proyectos->dame_numero_filas() > 0)
        {
            $eliminar_linea_base = false;

            $fila_proyecto = $res_proyectos->dame_siguiente_fila();
            $nombre_proyecto = $fila_proyecto["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la línea base porque está asignada a algún proyecto")."\n(".
                $nombre_proyecto.")";
        }
    }

    // Se comprueba si la línea base está asignada a algún evento
    if ($eliminar_linea_base == true)
    {
        $consulta_eventos = "
            SELECT nombre
            FROM eventos
            WHERE
                (tipo = '".TIPO_EVENTO_LINEA_BASE."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_EVENTO_LINEA_BASE_ID_LINEA_BASE + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_linea_base)."')
            ORDER BY nombre ASC";
        $res_eventos = $bd_red->ejecuta_consulta($consulta_eventos);
        if ($res_eventos == false)
        {
            throw new Exception("Error en la consulta: '".$res_eventos."'");
        }
        if ($res_eventos->dame_numero_filas() > 0)
        {
            $eliminar_linea_base = false;

            $fila_evento = $res_eventos->dame_siguiente_fila();
            $nombre_evento = $fila_evento["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la línea base porque está asignada a algún evento")."\n(".
                $nombre_evento.")";
        }
    }

    // Se elimina la línea base
    if ($eliminar_linea_base == true)
    {
        // Se recupera la fila de la línea base
        $fila_linea_base = dame_fila_linea_base($id_linea_base);

        // Se elimina la línea base
        $operacion_borrado = "
            DELETE
            FROM lineas_base
            WHERE
                id = '".$bd_red->_($id_linea_base)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan las variables de la línea base
            $operacion_borrado_variables_lineas_base = "
                DELETE
                FROM variables_lineas_base
                WHERE
                    linea_base = '".$bd_red->_($id_linea_base)."'";
            $res_borrado_variables_lineas_base = $bd_red->ejecuta_operacion($operacion_borrado_variables_lineas_base);
            if ($res_borrado_variables_lineas_base == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_variables_lineas_base."'");
            }

            // Se eliminan las excepciones de la línea base
            $operacion_borrado_excepciones_lineas_base = "
                DELETE
                FROM excepciones_lineas_base
                WHERE
                    linea_base_padre = '".$bd_red->_($id_linea_base)."'";
            $res_borrado_excepciones_lineas_base = $bd_red->ejecuta_operacion($operacion_borrado_excepciones_lineas_base);
            if ($res_borrado_excepciones_lineas_base == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_excepciones_lineas_base."'");
            }

            // Acciones a realizar al eliminar una línea base
            realiza_acciones_linea_base_eliminada($id_linea_base);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_linea_base($fila_linea_base);

            $res = "OK";
            $msg = $idiomas->_("Línea base eliminada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar una línea base
    function realiza_acciones_linea_base_eliminada($id_linea_base)
    {
        // Se eliminan los widgets correspondientes
        elimina_widgets_linea_base_eliminada($id_linea_base);

        // Se modifican los elementos de plantillas de informes que contengan esta línea base (se establece a ninguno)
        modifica_elementos_plantillas_informes_linea_base_eliminada($id_linea_base);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan esta línea base seleccionada en algún parámetro
        modifica_informes_automaticos_plantillas_informes_linea_base_eliminada($id_linea_base);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_linea_base_eliminada($id_linea_base);
    }


    // Añade la acción de usuario de eliminación de la línea base
    function anyade_accion_usuario_eliminar_linea_base($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_LINEA_BASE;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
