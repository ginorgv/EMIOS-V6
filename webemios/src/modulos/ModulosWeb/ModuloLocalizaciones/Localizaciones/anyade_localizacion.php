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
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
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
    $id_localizacion_anterior = $_POST["id_localizacion_anterior"];

    // Se comprueba si existe una localización con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM localizaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")";
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
        // Se añade la localización
        $operacion_insercion = "
            INSERT INTO localizaciones (
                nombre,
                red,
                descripcion,
                orden,
                mapa_personalizado,
                tipo_mapa,
                nombre_mapa,
                factor_reduccion_imagen_mapa_local,
                etiquetas_mapa,
                latitud_mapa_defecto,
                longitud_mapa_defecto,
                zoom_mapa_defecto
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion)."',
                '0',
                '".$bd_red->_($mapa_personalizado)."',
                '".$bd_red->_($tipo_mapa)."',
                '".$bd_red->_($nombre_mapa)."',
                '".$bd_red->_($factor_reduccion_imagen_mapa_local)."',
                '".$bd_red->_($etiquetas_mapa)."',
                '".$bd_red->_($latitud_mapa_defecto)."',
                '".$bd_red->_($longitud_mapa_defecto)."',
                '".$bd_red->_($zoom_mapa_defecto)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila de la localización añadida
            $id_localizacion = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_localizacion = dame_fila_localizacion($id_localizacion);

            // Se guarda la información de la posición en el mapa
            if ($mostrar_en_mapa == VALOR_SI)
            {
                $info_posicion_mapa = array(
                    "tipo_elemento" => TIPO_ELEMENTO_MAPA_LOCALIZACION,
                    "id_elemento" => $id_localizacion,
                    "origen" => ORIGEN_MAPA_RED,
                    "id_origen" => $_SESSION["id_red"],
                    "latitud" => $latitud_mapa,
                    "longitud" => $longitud_mapa,
                    "zoom" => $zoom_mapa);
                guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
            }

            // Se añade la información de los ratios de la localización
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

            // Se recuperan las filas de los ratios de la localización añadida
            $filas_ratios_localizacion = dame_filas_ratios_localizacion($id_localizacion);

            // Si el identificador de localización existe, es un duplicado de una localización existente:
            // - Se duplican los ratios y las localizaciones hijas (si los hay)
            if ($id_localizacion_anterior != ID_NINGUNO)
            {
                // Se duplican las localizaciones hijas de la localización anterior
                duplica_hijas_localizacion_anterior($id_localizacion_anterior, $id_localizacion);

                // Se actualiza el orden de la localización duplicada
                actualiza_orden_localizacion_anterior($id_localizacion_anterior, $id_localizacion);
            }

            // Añade la localización al usuario actual (si es necesario)
            anyade_localizacion_parametros_modulo_localizaciones_usuario_actual($id_localizacion);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_localizacion(
                $fila_localizacion,
                $filas_ratios_localizacion,
                $info_posicion_mapa);

            $res = "OK";
            $msg = $idiomas->_("Localización añadida correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_localizacion" => $id_localizacion))
    );


    //
    // Funciones auxiliares
    //


    // Duplica las hijas de la localización anterior
    function duplica_hijas_localizacion_anterior($id_localizacion_anterior, $id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las hijas de la localización anterior, se cambia la localización padre y se añaden
        $consulta_hijas = "
            SELECT *
            FROM hijas_localizaciones
            WHERE
                localizacion_padre = '".$bd_red->_($id_localizacion_anterior)."'";
        $res_hijas = $bd_red->ejecuta_consulta($consulta_hijas);
        if ($res_hijas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_hijas."'");
        }

        while ($fila_hija = $res_hijas->dame_siguiente_fila())
        {
            $operacion_insercion_hija = "
                INSERT INTO hijas_localizaciones (
                    red,
                    localizacion_padre,
                    localizacion_hija
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_localizacion)."',
                    '".$bd_red->_($fila_hija["localizacion_hija"])."'
                )";
            $res_insercion_hija = $bd_red->ejecuta_operacion($operacion_insercion_hija);
            if ($res_insercion_hija == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_hija."'");
            }
        }
    }


    // Actualiza el orden de la localización anterior
    function actualiza_orden_localizacion_anterior($id_localizacion_anterior, $id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_localizacion = "
            SELECT
                orden
            FROM
                localizaciones
            WHERE
                id = '".$bd_red->_($id_localizacion_anterior)."'";
        $res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
        if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
        }
        $fila_localizacion = $res_localizacion->dame_siguiente_fila();
        $orden_localizacion = $fila_localizacion["orden"];

        $operacion_modificacion = "
            UPDATE localizaciones
            SET
                orden = '".$bd_red->_($orden_localizacion)."'
            WHERE
                id = '".$bd_red->_($id_localizacion)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }


    // Añade la acción de usuario de adición de la localización
    function anyade_accion_usuario_anyadir_localizacion(
        $fila,
        $filas_ratios_localizacion,
        $info_posicion_mapa)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_LOCALIZACION;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];

        // Parámetros de ratios de la localización de la acción
        $info_ratios_localizacion = dame_info_ratios_localizacion_accion_usuario($filas_ratios_localizacion);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_RATIOS_LOCALIZACION] = $info_ratios_localizacion;

        // Parámetros de opciones de mapa y mapa
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MAPA_PERSONALIZADO] = $fila["mapa_personalizado"];
        if ($fila["mapa_personalizado"] == VALOR_SI)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_MAPA] = $fila["tipo_mapa"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_MAPA] = $fila["nombre_mapa"];
            if ($fila["tipo_mapa"] == TIPO_MAPA_LOCAL)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FACTOR_REDUCCION_IMAGEN_MAPA_LOCAL] = $fila["factor_reduccion_imagen_mapa_local"];
            }
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ETIQUETAS_MAPA] = $fila["etiquetas_mapa"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LONGITUD_MAPA_DEFECTO] = $fila["longitud_mapa_defecto"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LATITUD_MAPA_DEFECTO] = $fila["latitud_mapa_defecto"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ZOOM_MAPA_DEFECTO] = $fila["zoom_mapa_defecto"];
        }

        // Información de posición en mapa
        anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
