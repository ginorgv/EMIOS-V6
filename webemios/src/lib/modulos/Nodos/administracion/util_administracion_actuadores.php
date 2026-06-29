<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    //
    // Funciones utilizadas en la administración de actuadores
    //


    function dame_posible_eliminar_actuador($id_actuador, $fila_actuador, &$msg)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Comprobaciones antes de eliminar el actuador:
        // - Se comprueba si existe alguna acción asignada a este actuador
        $posible_eliminar_actuador = true;

        // Se comprueba si existe alguna acción asignada a este actuador
        if ($posible_eliminar_actuador == true)
        {
            $consulta_acciones = "
                SELECT nombre
                FROM acciones_reglas
                WHERE
                    (destino = '".DESTINO_ACCION_ACTUADOR."')
                    AND (id_destino = '".$bd_red->_($id_actuador)."')
                ORDER BY nombre ASC";
            $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
            if ($res_acciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_acciones."'");
            }
            if ($res_acciones->dame_numero_filas() > 0)
            {
                $posible_eliminar_actuador = false;

                $fila_accion = $res_acciones->dame_siguiente_fila();
                $nombre_accion = $fila_accion["nombre"];

                $msg = $idiomas->_("No se puede eliminar el actuador porque tiene acciones asignadas")."\n(".
                    $nombre_accion.")";
            }
        }

        // Se devuelve si es posible eliminar el actuador
        return ($posible_eliminar_actuador);
    }


    function elimina_actuador($id_actuador, $fila_actuador)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se borra el actuador
        $operacion_borrado = "
            DELETE
            FROM actuadores
            WHERE
                id = '".$bd_red->_($id_actuador)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Acciones a realizar al eliminar un actuador
            realiza_acciones_actuador_eliminado($id_actuador, $fila_actuador);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    //
    // Funciones de acciones al realizar operaciones de administración de actuadores
    //


    // Realiza acciones al añadir un actuador
    function realiza_acciones_actuador_anyadido($id_actuador, $fila)
    {
        // Información del actuador
        $tipo_actuador = $fila["tipo"];
        $id_localizacion = $fila["localizacion"];
        $id_grupo = $fila["grupo"];

        // Se envía mensaje MQTT de administración de actuadores
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                $id_dispositivo = dame_dispositivo_actuador_hardware($fila);
                recarga_configuracion_dispositivo($id_dispositivo);
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE:
            {
                notifica_operacion_administracion_actuador_software(OPERACION_ADICION, $id_actuador);
                break;
            }
        }

        // Se añade el actuador al usuario actual (si es necesario)
        if (($id_localizacion == ID_NINGUNO) && ($id_grupo == ID_NINGUNO))
        {
            anyade_actuador_grupo_parametros_modulo_actuadores_usuario_actual(TIPO_NODO_ACTUADOR, $id_actuador);
        }
    }


    // Realiza acciones al modificar un actuador
    function realiza_acciones_actuador_modificado(
        $id_actuador,
        $fila_actual,
        $fila_anterior)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Información del actuador
        $tipo_actuador = $fila_actual["tipo"];

        // Si se ha cambiado el nombre, se modifican las acciones enviadas y los comentarios del actuador
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $operacion_modificacion_acciones_enviadas = "
                UPDATE acciones_actuadores
                SET
                    actuador = '".$bd_datos->_($fila_actual["nombre"])."'
                WHERE
                    (actuador = '".$bd_datos->_($fila_anterior["nombre"])."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_modificacion_acciones_enviadas = $bd_datos->ejecuta_operacion($operacion_modificacion_acciones_enviadas);
            if ($res_modificacion_acciones_enviadas == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_acciones_enviadas."'");
            }

            modifica_comentarios_nodo(TIPO_NODO_ACTUADOR, $fila_anterior["nombre"], $fila_actual["nombre"]);
        }

        // Se notifica la operación de administración del actuador
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_SOFTWARE:
            {
                notifica_operacion_administracion_actuador_software(OPERACION_MODIFICACION, $id_actuador);
                break;
            }
        }

        // Se recargan las configuraciones de los dispositivo del actuador (anterior y actual)
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                $id_dispositivo_actual = dame_dispositivo_actuador_real($fila_actual);
                $id_dispositivo_anterior = dame_dispositivo_actuador_real($fila_anterior);
                recarga_configuracion_dispositivo($id_dispositivo_actual);
                if ($id_dispositivo_actual != $id_dispositivo_anterior)
                {
                    recarga_configuracion_dispositivo($id_dispositivo_anterior);
                }
                break;
            }
        }

        // Si se ha modifica el grupo o la localización del actuador,
        // se eliminan los elementos que han dejado de ser visibles por los usuarios
        // (pueden dejar de ver el sensor actual)
        $comprobar_elementos_no_visibles_parametros_modulos_usuarios = false;
        if (($fila_anterior["grupo"] != ID_NINGUNO) && ($fila_anterior["grupo"] != $fila_actual["grupo"]))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if (($fila_anterior["localizacion"] != ID_NINGUNO) && ($fila_anterior["localizacion"] != $fila_actual["localizacion"]))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if (($fila_anterior["visible_localizaciones_hijas"] == VALOR_SI) && ($fila_actual["visible_localizaciones_hijas"] == VALOR_NO))
        {
            $comprobar_elementos_no_visibles_parametros_modulos_usuarios = true;
        }
        if ($comprobar_elementos_no_visibles_parametros_modulos_usuarios == true)
        {
            elimina_modifica_elementos_no_visibles_parametros_modulos_usuarios();
        }

        // Si se ha modificado la localización, se elimina el actuador de los equipos de las instalaciones (si es necesario)
        if (($fila_anterior["localizacion"] != ID_NINGUNO) && ($fila_anterior["localizacion"] != $fila_actual["localizacion"]))
        {
            elimina_id_nodo_equipos_instalaciones(TIPO_NODO_ACTUADOR, $id_actuador);
        }
    }


    // Realiza acciones al eliminar un actuador
    function realiza_acciones_actuador_eliminado($id_actuador, $fila)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Información del actuador
        $nombre_actuador = $fila["nombre"];
        $tipo_actuador = $fila["tipo"];

        // Se eliminan las acciones enviadas del actuador
        $operacion_borrado_acciones_enviadas = "
            DELETE
            FROM acciones_actuadores
            WHERE
                (actuador = '".$bd_datos->_($fila["nombre"])."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_borrado_acciones_enviadas = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_enviadas);
        if ($res_borrado_acciones_enviadas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_enviadas."'");
        }

        // Se notifica la operación de administración del actuador
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_SOFTWARE:
            {
                notifica_operacion_administracion_actuador_software(OPERACION_BORRADO, $id_actuador);
                break;
            }
        }

        // Se recarga la configuración del dispositivo del actuador
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                $id_dispositivo = dame_dispositivo_actuador_hardware($fila);
                recarga_configuracion_dispositivo($id_dispositivo);
                break;
            }
        }

        // Se eliminan los comentarios del actuador
        elimina_comentarios_nodo(TIPO_NODO_ACTUADOR, $nombre_actuador);

        // Se eliminan los widgets correspondientes
        elimina_widgets_actuador_eliminado($id_actuador);

        // Se modifican los elementos de plantillas de informes que contengan este actuador (se establece a ninguno)
        modifica_elementos_plantillas_informes_actuador_eliminado($id_actuador);

        // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este actuador seleccionado en algún parámetro
        modifica_informes_automaticos_plantillas_informes_actuador_eliminado($id_actuador);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_actuador_eliminado($id_actuador);

        // Se elimina el sensor de los equipos de las instalaciones (si es necesario)
        elimina_id_nodo_equipos_instalaciones(TIPO_NODO_ACTUADOR, $id_actuador);

        // Se elimina el actuador de los parámetros del módulo Actuadores de los usuarios (si es necesario)
        elimina_actuador_grupo_parametros_modulo_actuadores_usuarios(TIPO_NODO_ACTUADOR, $id_actuador);

        // Se eliminan las posiciones de mapa del actuador
        elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_ACTUADOR, $id_actuador);
    }
?>