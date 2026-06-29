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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_localizacion = $_POST['id_localizacion'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $mapa_personalizado = $_POST['mapa_personalizado'];
    $tipo_mapa = $_POST['tipo_mapa'];
    $nombre_mapa = $_POST['nombre_mapa'];
    $factor_reduccion_imagen_mapa_local = $_POST['factor_reduccion_imagen_mapa_local'];
    $etiquetas_mapa = $_POST['etiquetas_mapa'];
    $latitud_mapa_defecto = $_POST['latitud_mapa_defecto'];
    $longitud_mapa_defecto = $_POST['longitud_mapa_defecto'];
    $zoom_mapa_defecto = $_POST['zoom_mapa_defecto'];
    $mostrar_en_mapa = $_POST['mostrar_en_mapa'];
    $latitud_mapa = $_POST['latitud_mapa'];
    $longitud_mapa = $_POST['longitud_mapa'];
    $zoom_mapa = $_POST['zoom_mapa'];
    $info_ratios = $_POST['info_ratios'];

    // Parámetros auxiliares
    $mapa_personalizado_anterior = $_POST['mapa_personalizado_anterior'];
    $tipo_mapa_anterior = $_POST['tipo_mapa_anterior'];

    // Se comprueba si existe otra localización con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM localizaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_localizacion)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una localización con el mismo nombre");
    }
    else
    {
        // Se recuperan las filas de la localización y de los ratios de la localización anteriores (antes de la modificación)
        $fila_localizacion_anterior = dame_fila_localizacion($id_localizacion);
        $filas_ratios_localizacion_anteriores = dame_filas_ratios_localizacion($id_localizacion);

        // Se recupera la información de mapa anterior (antes de la modificación)
        $info_posicion_mapa_anterior = dame_info_posicion_mapa_base_datos(
            TIPO_ELEMENTO_MAPA_LOCALIZACION,
            $id_localizacion,
            ORIGEN_MAPA_RED,
            $_SESSION["id_red"]);

        // Si elimina la imagen del mapa local personalizado en los siguientes casos:
        // - Cambio de tipo de mapa de local a internet
        // - Eliminación de mapa personalizado con mapa local (anterior)
        $eliminar_imagen_mapa_local_personalizado = false;
        if (($mapa_personalizado_anterior == VALOR_SI) && ($mapa_personalizado == VALOR_SI) &&
            ($tipo_mapa_anterior == TIPO_MAPA_LOCAL) && ($tipo_mapa == TIPO_MAPA_INTERNET))
        {
            $eliminar_imagen_mapa_local_personalizado = true;
        }
        if (($mapa_personalizado_anterior == VALOR_SI) && ($mapa_personalizado == VALOR_NO) &&
            ($tipo_mapa_anterior == TIPO_MAPA_LOCAL))
        {
            $eliminar_imagen_mapa_local_personalizado = true;
        }
        if ($eliminar_imagen_mapa_local_personalizado == true)
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_LOCALIZACION_MAPA, $id_localizacion);
        }

        // Se eliminan las posiciones del mapa en los siguientes casos:
        // - Si antes había mapa personalizado y ahora no
        // - Si hay mapa personalizado y se ha modificado el tipo de mapa
        $eliminar_posiciones_mapa = false;
        if (($mapa_personalizado_anterior == VALOR_SI) && ($mapa_personalizado == VALOR_NO))
        {
            $eliminar_posiciones_mapa = true;
        }
        if (($mapa_personalizado == VALOR_SI) && ($tipo_mapa_anterior != $tipo_mapa))
        {
            $eliminar_posiciones_mapa = true;
        }
        if ($eliminar_posiciones_mapa == true)
        {
            elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_LOCALIZACION, $id_localizacion);
        }

        // Se modifica la localización
        $operacion_modificacion = "
            UPDATE localizaciones
            SET
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
                mapa_personalizado = '".$bd_red->_($mapa_personalizado)."',
                tipo_mapa = '".$bd_red->_($tipo_mapa)."',
                nombre_mapa = '".$bd_red->_($nombre_mapa)."',
                factor_reduccion_imagen_mapa_local = '".$bd_red->_($factor_reduccion_imagen_mapa_local)."',
                etiquetas_mapa = '".$bd_red->_($etiquetas_mapa)."',
                latitud_mapa_defecto = '".$bd_red->_($latitud_mapa_defecto)."',
                longitud_mapa_defecto = '".$bd_red->_($longitud_mapa_defecto)."',
                zoom_mapa_defecto = '".$bd_red->_($zoom_mapa_defecto)."'
            WHERE
                id = '".$bd_red->_($id_localizacion)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se guarda o elimina la información de la posición en el mapa
            if ($mostrar_en_mapa == VALOR_SI)
            {
                $info_posicion_mapa_actual = array(
                    "tipo_elemento" => TIPO_ELEMENTO_MAPA_LOCALIZACION,
                    "id_elemento" => $id_localizacion,
                    "origen" => ORIGEN_MAPA_RED,
                    "id_origen" => $_SESSION["id_red"],
                    "latitud" => $latitud_mapa,
                    "longitud" => $longitud_mapa,
                    "zoom" => $zoom_mapa);
                guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
            }
            else
            {
                elimina_info_posicion_mapa_base_datos(
                    TIPO_ELEMENTO_MAPA_LOCALIZACION,
                    $id_localizacion,
                    ORIGEN_MAPA_RED,
                    $_SESSION["id_red"]);
            }

            // Se actualiza la información de los ratios de la localización (se eliminan y se añaden)
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
            foreach ($info_ratios as $info_ratio)
            {
                $id_ratio = $info_ratio["id"];
                $tipo_ratio = $info_ratio["tipo"];
                $valor_sensor_ratio = $info_ratio["valor_sensor"];
                switch ($tipo_ratio)
                {
                    case TIPO_RATIO_FIJO:
                    {
                        $cadena_valor_ratio = "'".$bd_red->_($valor_sensor_ratio)."'";
                        $cadena_id_sensor_ratio = "NULL";
                        break;
                    }
                    case TIPO_RATIO_VARIABLE:
                    {
                        $cadena_valor_ratio = "NULL";
                        $cadena_id_sensor_ratio = "'".$bd_red->_($valor_sensor_ratio)."'";
                        break;
                    }
                }
                $operacion_insercion_ratio_localizacion = "
                    INSERT INTO ratios_localizaciones (
                        red,
                        localizacion,
                        ratio,
                        valor,
                        sensor
                    ) VALUES (
                        '".$_SESSION["id_red"]."',
                        '".$bd_red->_($id_localizacion)."',
                        '".$bd_red->_($id_ratio)."',
                        ".$cadena_valor_ratio.",
                        ".$cadena_id_sensor_ratio."
                    )";
                $res_insercion_ratio_localizacion = $bd_red->ejecuta_operacion($operacion_insercion_ratio_localizacion);
                if ($res_insercion_ratio_localizacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_ratio_localizacion."'");
                }
            }

            // Se recuperan las filas de la localización y de los ratios de la localización actuales
            $fila_localizacion_actual = dame_fila_localizacion($id_localizacion);
            $filas_ratios_localizacion_actuales = dame_filas_ratios_localizacion($id_localizacion);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_localizacion(
                $fila_localizacion_actual,
                $fila_localizacion_anterior,
                $filas_ratios_localizacion_actuales,
                $filas_ratios_localizacion_anteriores,
                $info_posicion_mapa_actual,
                $info_posicion_mapa_anterior);

            $res = "OK";
            $msg = $idiomas->_("Localización modificada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la localización
    function anyade_accion_usuario_modificar_localizacion(
        $fila_actual,
        $fila_anterior,
        $filas_ratios_localizacion_actuales,
        $filas_ratios_localizacion_anteriores,
        $info_posicion_mapa_actual,
        $info_posicion_mapa_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_LOCALIZACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }

        // Parámetros de ratios de la localización de la acción (se eliminan los ratios de localización que no hayan cambiado)
        $info_ratios_localizacion = dame_info_ratios_localizacion_accion_usuario($filas_ratios_localizacion_actuales);
        $info_ratios_localizacion_anteriores = dame_info_ratios_localizacion_accion_usuario($filas_ratios_localizacion_anteriores);
        foreach ($info_ratios_localizacion as $nombre_ratio => $info_ratio_localizacion)
        {
            if (array_key_exists($nombre_ratio, $info_ratios_localizacion_anteriores) == true)
            {
                if ($info_ratio_localizacion == $info_ratios_localizacion_anteriores[$nombre_ratio])
                {
                    unset($info_ratios_localizacion[$nombre_ratio]);
                    unset($info_ratios_localizacion_anteriores[$nombre_ratio]);
                }
            }
        }
        if (count($info_ratios_localizacion) > 0)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_RATIOS_LOCALIZACION] = $info_ratios_localizacion;
        }
        if (count($info_ratios_localizacion_anteriores) > 0)
        {
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_RATIOS_LOCALIZACION] = $info_ratios_localizacion_anteriores;
        }

        // Parámetros de opciones de mapa y mapa
        if ($fila_actual["mapa_personalizado"] != $fila_anterior["mapa_personalizado"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MAPA_PERSONALIZADO] = $fila_actual["mapa_personalizado"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MAPA_PERSONALIZADO] = $fila_anterior["mapa_personalizado"];
            if ($fila_actual["mapa_personalizado"] == VALOR_SI)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_MAPA] = $fila_actual["tipo_mapa"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_MAPA] = $fila_actual["nombre_mapa"];
                if ($fila_actual["tipo_mapa"] == TIPO_MAPA_LOCAL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN_MAPA_LOCAL] = $fila_actual["factor_reduccion_imagen_mapa_local"];
                }
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ETIQUETAS_MAPA] = $fila_actual["etiquetas_mapa"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA_DEFECTO] = $fila_actual["longitud_mapa_defecto"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA_DEFECTO] = $fila_actual["latitud_mapa_defecto"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA_DEFECTO] = $fila_actual["zoom_mapa_defecto"];
            }
            if ($fila_anterior["mapa_personalizado"] == VALOR_SI)
            {
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_MAPA] = $fila_anterior["tipo_mapa"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_MAPA] = $fila_anterior["nombre_mapa"];
                if ($fila_anterior["tipo_mapa"] == TIPO_MAPA_LOCAL)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN_MAPA_LOCAL] = $fila_anterior["factor_reduccion_imagen_mapa_local"];
                }
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ETIQUETAS_MAPA] = $fila_anterior["etiquetas_mapa"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA_DEFECTO] = $fila_anterior["longitud_mapa_defecto"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA_DEFECTO] = $fila_anterior["latitud_mapa_defecto"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA_DEFECTO] = $fila_anterior["zoom_mapa_defecto"];
            }
        }
        else
        {
            if ($fila_actual["mapa_personalizado"] == VALOR_SI)
            {
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
            }
        }

        // Información de posición en mapa
        if ($info_posicion_mapa_actual !== $info_posicion_mapa_anterior)
        {
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_actual, $parametros_accion_usuario);
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_anterior, $parametros_accion_usuario_anteriores);
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
