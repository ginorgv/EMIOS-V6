<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_USUARIO, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_usuario = $_POST["id_usuario"];

    // No se permite eliminar al usurio actual
    if (strtolower($id_usuario) == $_SESSION["id_usuario"])
    {
        $res = "ERROR";
        $msg = $idiomas->_("No se permite eliminar el usuario actual");
    }
    else
    {
        // Comprobaciones antes de eliminar el usuario:
        // - Se comprueba que un administrador no pueda borrar un usuario estándar con redes no visibles por el administrador
        $eliminar_usuario = true;

        // Se recupera el perfil del usuario a eliminar
        if ($eliminar_usuario == true)
        {
            // Se recupera el perfil del usuario
            $consulta_usuario = "
                SELECT perfil
                FROM usuarios
                WHERE
                    id = '".$bd_red->_($id_usuario)."'";
            $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
            if (($res_usuario == false) || ($res_usuario->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_usuario."'");
            }
            $fila_usuario = $res_usuario->dame_siguiente_fila();
            $perfil = $fila_usuario["perfil"];

            // Si un administrador intenta eliminar un usuario estándar con redes asignadas que no están asignadas a él mismo,
            // no se permite borrar al usuario estándar
            if (($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR) && ($perfil == PERFIL_USUARIO_ESTANDAR))
            {
                // Se recuperan las redes del usuario a eliminar y del usuario actual
                $ids_redes_usuario_eliminar = dame_ids_redes_usuario($id_usuario, $perfil);
                $ids_redes_usuario_actual = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);

                // Se recorren las redes del usuario a eliminar
                foreach ($ids_redes_usuario_eliminar as $id_red_usuario_eliminar)
                {
                    if (in_array($id_red_usuario_eliminar, $ids_redes_usuario_actual) == false)
                    {
                        $posible_eliminar_usuario = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("No se puede eliminar al usuario porque tiene redes no asignadas al usuario actual");
                        break;
                    }
                }
            }
        }

        // Eliminar usuario
        if ($eliminar_usuario == true)
        {
            $operacion_borrado = "
                DELETE
                FROM usuarios
                WHERE
                    id = '".$bd_red->_($id_usuario)."'";
            $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
            if ($res_borrado == true)
            {
                // Elimina los elementos del usuario
                elimina_elementos_usuario($id_usuario, $perfil);

                $res = "OK";
                $msg = $idiomas->_("Usuario eliminado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_borrado."'");
            }

            // Se elimina el directorio temporal del usuario
            elimina_ficheros_temporales_usuario($id_usuario, true);
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Elimina los elementos del usuario especificado en la base de datos
    function elimina_elementos_usuario($id_usuario, $perfil)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_redes_usuarios = "
            DELETE
            FROM redes_usuarios
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_redes_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_redes_usuarios);
        if ($res_borrado_redes_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_redes_usuarios."'");
        }

        $operacion_borrado_licencias_usuarios = "
            DELETE
            FROM licencias_usuarios
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_licencias_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_licencias_usuarios);
        if ($res_borrado_licencias_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_licencias_usuarios."'");
        }

        $operacion_borrado_secciones_usuarios = "
            DELETE
            FROM secciones_usuarios
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_secciones_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_secciones_usuarios);
        if ($res_borrado_secciones_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_secciones_usuarios."'");
        }

        $operacion_borrado_parametros_modulos_usuarios = "
            DELETE
            FROM modulos_usuarios
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_parametros_modulos_usuarios = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulos_usuarios);
        if ($res_borrado_parametros_modulos_usuarios == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulos_usuarios."'");
        }

        $operacion_borrado_imagenes_pestanyas_widgets = "
            DELETE imagenes
            FROM
                imagenes,
                pestanyas_widgets
            WHERE
                (imagenes.origen = '".ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO."')
                AND (imagenes.id_origen = pestanyas_widgets.id)
                AND (pestanyas_widgets.usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_imagenes_pestanyas_widgets = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_pestanyas_widgets);
        if ($res_borrado_imagenes_pestanyas_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_pestanyas_widgets."'");
        }

        $operacion_borrado_pestanyas_widgets = "
            DELETE
            FROM pestanyas_widgets
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_pestanyas_widgets = $bd_red->ejecuta_operacion($operacion_borrado_pestanyas_widgets);
        if ($res_borrado_pestanyas_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_pestanyas_widgets."'");
        }

        $operacion_borrado_imagenes_widgets = "
            DELETE imagenes
            FROM
                imagenes,
                widgets
            WHERE
                (imagenes.origen = '".ORIGEN_IMAGEN_WIDGET_IMAGEN."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(imagenes.id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(1 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = widgets.id)
                AND (widgets.usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_imagenes_widgets = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_widgets);
        if ($res_borrado_imagenes_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_widgets."'");
        }

        $operacion_borrado_widgets = "
            DELETE
            FROM widgets
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_widgets = $bd_red->ejecuta_operacion($operacion_borrado_widgets);
        if ($res_borrado_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_widgets."'");
        }

        // - Si el usuario es estándar: Se eliminan las plantillas de informes de este usuario
        // - Si el usuario es administrador: Se establece el usuario a ninguno
        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $operacion_borrado_parametros_plantillas_informes = "
                    DELETE parametros_plantillas_informes
                    FROM
                        parametros_plantillas_informes,
                        plantillas_informes
                    WHERE
                        (parametros_plantillas_informes.plantilla_informe = plantillas_informes.id)
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_parametros_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_parametros_plantillas_informes);
                if ($res_borrado_parametros_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_parametros_plantillas_informes."'");
                }

                $operacion_borrado_imagenes_elementos_plantillas_informes = "
                    DELETE imagenes
                    FROM
                        imagenes,
                        elementos_plantillas_informes,
                        plantillas_informes
                    WHERE
                        (imagenes.origen = '".ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(imagenes.id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(0 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = plantillas_informes.id)
                        AND (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_imagenes_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_elementos_plantillas_informes);
                if ($res_borrado_imagenes_elementos_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_elementos_plantillas_informes."'");
                }

                $operacion_borrado_elementos_plantillas_informes = "
                    DELETE elementos_plantillas_informes
                    FROM
                        elementos_plantillas_informes,
                        plantillas_informes
                    WHERE
                        (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_elementos_plantillas_informes);
                if ($res_borrado_elementos_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_elementos_plantillas_informes."'");
                }

                $operacion_borrado_imagenes_plantillas_informes = "
                    DELETE imagenes
                    FROM
                        imagenes,
                        plantillas_informes
                    WHERE
                        (imagenes.origen = '".ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF."')
                        AND (imagenes.id_origen = plantillas_informes.id)
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_imagenes_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_plantillas_informes);
                if ($res_borrado_imagenes_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_plantillas_informes."'");
                }

                $operacion_borrado_plantillas_informes = "
                    DELETE
                    FROM plantillas_informes
                    WHERE
                        usuario = '".$bd_red->_($id_usuario)."'";
                $res_borrado_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_plantillas_informes);
                if ($res_borrado_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_plantillas_informes."'");
                }
                break;
            }
            default:
            {
                $operacion_modificacion_plantillas_informes = "
                    UPDATE plantillas_informes
                    SET
                        usuario = ''
                    WHERE
                        usuario = '".$bd_red->_($id_usuario)."'";
                $res_modificacion_plantillas_informes = $bd_red->ejecuta_operacion($operacion_modificacion_plantillas_informes);
                if ($res_modificacion_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_plantillas_informes."'");
                }
                break;
            }
        }

        $operacion_borrado_imagenes_informes_automaticos = "
            DELETE imagenes
            FROM
                imagenes,
                informes_automaticos
            WHERE
                (imagenes.origen = '".ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(imagenes.id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(0 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = informes_automaticos.id)
                AND (informes_automaticos.usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_imagenes_informes_automaticos = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_informes_automaticos);
        if ($res_borrado_imagenes_informes_automaticos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_informes_automaticos."'");
        }

        $operacion_borrado_informes_automaticos = "
            DELETE
            FROM informes_automaticos
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_informes_automaticos = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos);
        if ($res_borrado_informes_automaticos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos."'");
        }

        $operacion_borrado_peticiones_api_http = "
            DELETE
            FROM peticiones_api_http
            WHERE
                usuario = '".$bd_red->_($id_usuario)."'";
        $res_borrado_peticiones_api_http = $bd_red->ejecuta_operacion($operacion_borrado_peticiones_api_http);
        if ($res_borrado_peticiones_api_http == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_peticiones_api_http."'");
        }
    }
?>
