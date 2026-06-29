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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_localizacion = $_POST['id_localizacion'];

    // Comprobaciones antes de eliminar la localización:
    // - Se comprueba si existen nodos en la localización
    $eliminar_localizacion = true;

    // Se comprueba si existen nodos en la localización
    if ($eliminar_localizacion == true)
    {
        $nombres_sensores = Localizacion::dame_nombres_nodos($id_localizacion, TIPO_NODO_SENSOR);
        if (count($nombres_sensores) > 0)
        {
            $eliminar_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la localización porque tiene sensores asignados")."\n(".
                $nombres_sensores[0].")";
        }
    }
    if ($eliminar_localizacion == true)
    {
        $nombres_grupos_sensores = Localizacion::dame_nombres_nodos($id_localizacion, TIPO_NODO_GRUPO_SENSORES);
        if (count($nombres_grupos_sensores) > 0)
        {
            $eliminar_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la localización porque tiene grupos de sensores asignados")."\n(".
                $nombres_grupos_sensores[0].")";
        }
    }
    if ($eliminar_localizacion == true)
    {
        $nombres_actuadores = Localizacion::dame_nombres_nodos($id_localizacion, TIPO_NODO_ACTUADOR);
        if (count($nombres_actuadores) > 0)
        {
            $eliminar_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la localización porque tiene actuadores asignados")."\n(".
                $nombres_actuadores[0].")";
        }
    }
    if ($eliminar_localizacion == true)
    {
        $nombres_grupos_actuadores = Localizacion::dame_nombres_nodos($id_localizacion, TIPO_NODO_GRUPO_ACTUADORES);
        if (count($nombres_grupos_actuadores) > 0)
        {
            $eliminar_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la localización porque tiene grupos de actuadores asignados")."\n(".
                $nombres_grupos_actuadores[0].")";
        }
    }

    // Se elimina la localización
    if ($eliminar_localizacion == true)
    {
        // Carga la información de las localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);
        $ids_localizaciones_padre = $info_localizaciones_padres[$id_localizacion];

        // Se recupera la fila de la localización
        $fila_localizacion = dame_fila_localizacion($id_localizacion);

        // Se elimina la localización
        $operacion_borrado = "
            DELETE
            FROM localizaciones
            WHERE
                id = '".$bd_red->_($id_localizacion)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se borra la información de localizaciones padres e hijas de esta localización
            $operacion_borrado_hijas_localizaciones = "
                DELETE
                FROM hijas_localizaciones
                WHERE
                    (localizacion_padre = '".$bd_red->_($id_localizacion)."')
                    OR (localizacion_hija = '".$bd_red->_($id_localizacion)."')";
            $res_borrado_hijas_localizaciones = $bd_red->ejecuta_operacion($operacion_borrado_hijas_localizaciones);
            if ($res_borrado_hijas_localizaciones == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_hijas_localizaciones."'");
            }

            // Si había localizaciones padre
            if (count($ids_localizaciones_padre) > 0)
            {
                // Recarga la información de las localizaciones padres e hijas
                $info_localizaciones_padres = NULL;
                $info_localizaciones_hijas = NULL;
                carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas, true);

                // Se actualiza el orden de las localizaciones padre (y sus padres recursivamente)
                $ordenes_localizaciones = NULL;
                carga_ordenes_localizaciones_padres_hijas($ordenes_localizaciones);
                foreach ($ids_localizaciones_padre as $id_localizacion_padre)
                {
                    actualiza_orden_localizaciones_ascendientes(
                        $info_localizaciones_padres,
                        $info_localizaciones_hijas,
                        $ordenes_localizaciones,
                        $id_localizacion_padre);
                }
            }

            // Se eliminan los ratios de esta localización
            $operacion_borrado_ratios_localizacion = "
                DELETE
                FROM ratios_localizaciones
                WHERE
                    localizacion = '".$bd_red->_($id_localizacion)."'";
            $res_borrado_ratios_localizacion = $bd_red->ejecuta_operacion($operacion_borrado_ratios_localizacion);
            if ($res_borrado_ratios_localizacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_ratios_localizacion."'");
            }

            // Acciones a realizar al eliminar una localización
            realiza_acciones_localizacion_eliminada($id_localizacion);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_localizacion($fila_localizacion);

            $res = "OK";
            $msg = $idiomas->_("Localización eliminada correctamente");
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


    // Realiza acciones al eliminar una localización
    function realiza_acciones_localizacion_eliminada($id_localizacion)
    {
        // Se eliminan los widgets correspondientes
        elimina_widgets_localizacion_eliminada($id_localizacion);

        // Se elimina la localización de los parámetros del módulo Localizaciones de los usuarios (si es necesario)
        elimina_localizacion_parametros_modulo_localizaciones_usuarios($id_localizacion);

        // Se eliminan las imágenes de la localización
        elimina_imagen_base_datos(ORIGEN_IMAGEN_LOCALIZACION_MAPA, $id_localizacion);

        // Se eliminan las posiciones de mapa de la localización
        elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_LOCALIZACION, $id_localizacion);
        elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_LOCALIZACION, $id_localizacion);
    }


    // Añade la acción de usuario de eliminación de la localización
    function anyade_accion_usuario_eliminar_localizacion($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_LOCALIZACION;
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
