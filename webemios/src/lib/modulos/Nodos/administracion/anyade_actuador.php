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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_ACTUADOR, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $visible_localizaciones_hijas = $_POST['visible_localizaciones_hijas'];
    $clase = $_POST['clase'];
    $cadena_parametros_clase = $_POST['parametros_clase'];
    $tipo = $_POST['tipo'];
    $cadena_parametros_tipo = $_POST['parametros_tipo'];
    $calibracion = $_POST['calibracion'];
    $id_grupo = $_POST['id_grupo'];
    $id_programacion = $_POST['id_programacion'];
    $mostrar_en_mapa = $_POST['mostrar_en_mapa'];
    $latitud_mapa = $_POST['latitud_mapa'];
    $longitud_mapa = $_POST['longitud_mapa'];
    $zoom_mapa = $_POST['zoom_mapa'];

    // Tipo de mensaje en la respuesta (correcta)
    $tipo_mensaje = TIPO_MENSAJE_INFORMACION;

    // Se comprueba si existen el número máximo de actuadores
    $consulta_numero_actuadores = "
        SELECT
            COUNT(*) AS numero_actuadores
        FROM actuadores
        WHERE
            red = '".$_SESSION["id_red"]."'";
    $res_numero_actuadores = $bd_red->ejecuta_consulta($consulta_numero_actuadores);
    if (($res_numero_actuadores == false) || ($res_numero_actuadores->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_actuadores."'");
    }

    $fila_numero_actuadores = $res_numero_actuadores->dame_siguiente_fila();
    $numero_maximo_actuadores = dame_numero_maximo_elementos_modulo(MODULO_ACTUADORES);
    if (($numero_maximo_actuadores != 0) &&
        ($fila_numero_actuadores['numero_actuadores'] >= $numero_maximo_actuadores))
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen el número máximo de actuadores");
    }
    else
    {
        // Se comprueba si existe un actuador con el mismo nombre
        $consulta_existe = "
            SELECT nombre
            FROM actuadores
            WHERE
                (nombre = '".$bd_red->_($nombre)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
        if ($res_existe == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_existe."'");
        }
        if ($res_existe->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un actuador con el mismo nombre");
        }
        else
        {
            // Comprobación antes de añadir el actuador:
            // - Localizaciones de grupo y de actuador correctas
            $anyadir_actuador = true;

            // Comprobación de localizaciones correctas
            if ($anyadir_actuador == true)
            {
                if ($id_grupo != ID_NINGUNO)
                {
                    $localizaciones_correctas = dame_localizaciones_correctas_grupo_localizacion_nodo(TIPO_NODO_ACTUADOR, $id_grupo, $id_localizacion);
                    if ($localizaciones_correctas == false)
                    {
                        $anyadir_actuador = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Las localizaciones del actuador y del grupo no son correctas");
                    }
                }
            }

            // Se añade el actuador
            if ($anyadir_actuador == true)
            {
                // Se añade el actuador
                $operacion_insercion = "
                    INSERT INTO actuadores (
                        nombre,
                        red,
                        descripcion,
                        tipo,
                        parametros_tipo,
                        clase,
                        parametros_clase,
                        grupo,
                        localizacion,
                        visible_localizaciones_hijas,
                        programacion,
                        calibracion,
                        id_ultima_accion,
                        contenido_ultima_accion,
                        valor_ultima_accion,
                        hora_ultima_accion,
                        estado_ejecucion_ultima_accion,
                        hora_fin_ultima_accion,
                        ultimo_error_ejecucion_accion_json
                    ) VALUES (
                        '".$bd_red->_($nombre)."',
                        '".$_SESSION["id_red"]."',
                        '".$bd_red->_($descripcion)."',
                        '".$bd_red->_($tipo)."',
                        '".$bd_red->_($cadena_parametros_tipo)."',
                        '".$bd_red->_($clase)."',
                        '".$bd_red->_($cadena_parametros_clase)."',
                        '".$bd_red->_($id_grupo)."',
                        '".$bd_red->_($id_localizacion)."',
                        '".$bd_red->_($visible_localizaciones_hijas)."',
                        '".$bd_red->_($id_programacion)."',
                        '".$bd_red->_($calibracion)."',
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        ''
                    )";
                $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
                if ($res_insercion == true)
                {
                    // Se recuperan el id y la fila del actuador añadido
                    $id_actuador = $bd_red->dame_id_autoincremental_ultima_insercion();
                    $fila_actuador = dame_fila_actuador($id_actuador);

                    // Se guarda la información de la posición en el mapa
                    if ($mostrar_en_mapa == VALOR_SI)
                    {
                        // Se recupera el origen del mapa 'final'
                        $parametros_origen_mapa = array("modulo" => MODULO_ACTUADORES);
                        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
                        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
                        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

                        // Se guarda la información de la posición en el mapa en base de datos
                        $info_posicion_mapa = array(
                            "tipo_elemento" => TIPO_ELEMENTO_MAPA_ACTUADOR,
                            "id_elemento" => $id_actuador,
                            "origen" => $origen_mapa,
                            "id_origen" => $id_origen_mapa,
                            "latitud" => $latitud_mapa,
                            "longitud" => $longitud_mapa,
                            "zoom" => $zoom_mapa);
                        guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
                    }
                    else
                    {
                        $info_posicion_mapa = NULL;
                    }

                    // Acciones a realizar al añadir un actuador
                    realiza_acciones_actuador_anyadido($id_actuador, $fila_actuador);

                    // Se añade la acción de usuario
                    anyade_accion_usuario_anyadir_actuador($fila_actuador, $info_posicion_mapa);

                    // Adición correcta
                    $res = "OK";
                    $msg = $idiomas->_("Actuador añadido correctamente");

                    // Avisos de adición del actuador
                    $aviso = "";
                    switch ($tipo)
                    {
                        case TIPO_ACTUADOR_HARDWARE:
                        {
                            $aviso = dame_aviso_comprobacion_ubicacion_actuador_hardware($id_actuador, $cadena_parametros_tipo);
                            break;
                        }
                    }
                    if ($aviso != "")
                    {
                        $tipo_mensaje = TIPO_MENSAJE_AVISO;
                        $msg .= "\n(".$aviso.")";
                    }
                }
                else
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion."'");
                }
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_nodo" => $id_actuador,
        "tipo_mensaje" => $tipo_mensaje))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del actuador
    function anyade_accion_usuario_anyadir_actuador($fila, $info_posicion_mapa)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_ACTUADOR;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_grupo = dame_nombre_grupo_actuadores($fila["grupo"]);
        $nombre_localizacion = dame_nombre_localizacion($fila["localizacion"]);
        $nombre_programacion = dame_nombre_programacion($fila["programacion"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila["visible_localizaciones_hijas"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila["clase"];
        if ($fila["parametros_clase"] != "")
        {
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_clase"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_ACTUADOR] = array(
                "clase" => $fila["clase"],
                "parametros_clase" => $parametros_clase);
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ACTUADOR] = $fila["tipo"];
        if ($fila["parametros_tipo"] != "")
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_tipo"]);
            sustituye_ids_nombres_parametros_tipo_actuador_accion_usuario($fila["tipo"], $parametros_tipo);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_ACTUADOR] = array(
                "tipo" => $fila["tipo"],
                "parametros_tipo" => $parametros_tipo);
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila["calibracion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion;

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
