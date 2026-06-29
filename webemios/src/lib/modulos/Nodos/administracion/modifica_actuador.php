<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_ACTUADOR, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_actuador = $_POST['id_actuador'];
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

    // Se comprueba si existe otro actuador con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM actuadores
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_actuador)."')";
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
        // Comprobaciones antes de modificar el actuador:
        // - Localizaciones de grupo y de actuador correctas
        $modificar_actuador = true;

        // Comprobación de localizaciones correctas
        if ($modificar_actuador == true)
        {
            if ($id_grupo != ID_NINGUNO)
            {
                $localizaciones_correctas = dame_localizaciones_correctas_grupo_localizacion_nodo(TIPO_NODO_ACTUADOR, $id_grupo, $id_localizacion);
                if ($localizaciones_correctas == false)
                {
                    $modificar_actuador = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Las localizaciones del actuador y del grupo no son correctas");
                }
            }
        }

        // Se modifica el actuador
        if ($modificar_actuador == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_actuador_anterior = dame_fila_actuador($id_actuador);
            $nombre_anterior = $fila_actuador_anterior["nombre"];

            // Se recupera el origen del mapa 'final'
            $parametros_origen_mapa = array("modulo" => MODULO_ACTUADORES);
            $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
            $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
            $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

            // Se recupera la información de mapa anterior (antes de la modificación)
            $info_posicion_mapa_anterior = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_ACTUADOR,
                $id_actuador,
                $origen_mapa,
                $id_origen_mapa);

            // Se modifica el actuador
            $operacion_modificacion = "
                UPDATE actuadores
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    tipo = '".$bd_red->_($tipo)."',
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo)."',
                    clase = '".$bd_red->_($clase)."',
                    parametros_clase = '".$bd_red->_($cadena_parametros_clase)."',
                    grupo = '".$bd_red->_($id_grupo)."',
                    localizacion = '".$bd_red->_($id_localizacion)."',
                    visible_localizaciones_hijas = '".$bd_red->_($visible_localizaciones_hijas)."',
                    programacion = '".$bd_red->_($id_programacion)."',
                    calibracion = '".$bd_red->_($calibracion)."',
                    ultimo_error_ejecucion_accion_json = ''
                WHERE
                    id = '".$bd_red->_($id_actuador)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se guarda o elimina la información de la posición en el mapa
                if ($mostrar_en_mapa == VALOR_SI)
                {
                    $info_posicion_mapa_actual = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_ACTUADOR,
                        "id_elemento" => $id_actuador,
                        "origen" => $origen_mapa,
                        "id_origen" => $id_origen_mapa,
                        "latitud" => $latitud_mapa,
                        "longitud" => $longitud_mapa,
                        "zoom" => $zoom_mapa);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
                }
                else
                {
                    elimina_info_posicion_mapa_base_datos(
                        TIPO_ELEMENTO_MAPA_ACTUADOR,
                        $id_actuador,
                        $origen_mapa,
                        $id_origen_mapa);
                }

                // Se recupera la fila actual
                $fila_actuador_actual = dame_fila_actuador($id_actuador);

                // Acciones a realizar al modificar un actuador
                realiza_acciones_actuador_modificado(
                    $id_actuador,
                    $fila_actuador_actual,
                    $fila_actuador_anterior);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_actuador(
                    $fila_actuador_actual,
                    $fila_actuador_anterior,
                    $info_posicion_mapa_actual,
                    $info_posicion_mapa_anterior);

                // Modificación correcta
                $res = "OK";
                $msg = $idiomas->_("Actuador modificado correctamente");

                // Avisos de modificación del actuador
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
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "tipo_mensaje" => $tipo_mensaje))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del actuador
    function anyade_accion_usuario_modificar_actuador(
        $fila_actual,
        $fila_anterior,
        $info_posicion_mapa_actual,
        $info_posicion_mapa_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_ACTUADOR;

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
        if ($fila_actual["localizacion"] != $fila_anterior["localizacion"])
        {
            $nombre_localizacion = dame_nombre_localizacion($fila_actual["localizacion"]);
            $nombre_localizacion_anterior = dame_nombre_localizacion($fila_anterior["localizacion"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion_anterior;
        }
        if ($fila_actual["visible_localizaciones_hijas"] != $fila_anterior["visible_localizaciones_hijas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila_actual["visible_localizaciones_hijas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VISIBLE_LOCALIZACIONES_HIJAS] = $fila_anterior["visible_localizaciones_hijas"];
        }
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_anterior["clase"];
        }
        if ($fila_actual["parametros_clase"] != $fila_anterior["parametros_clase"])
        {
            if ($fila_actual["parametros_clase"] != "")
            {
                $parametros_clase_actuales = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_clase"]);
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_ACTUADOR] = array(
                    "clase" => $fila_actual["clase"],
                    "parametros_clase" => $parametros_clase_actuales);
            }
            if ($fila_anterior["parametros_clase"] != "")
            {
                $parametros_clase_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_clase"]);
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_CLASE_ACTUADOR] = array(
                    "clase" => $fila_anterior["clase"],
                    "parametros_clase" => $parametros_clase_anteriores);
            }
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_ACTUADOR] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_ACTUADOR] = $fila_anterior["tipo"];
        }
        if ($fila_actual["parametros_tipo"] != $fila_anterior["parametros_tipo"])
        {
            if ($fila_actual["parametros_tipo"] != "")
            {
                $parametros_tipo_actuales = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_tipo"]);
                sustituye_ids_nombres_parametros_tipo_actuador_accion_usuario($fila_actual["tipo"], $parametros_tipo_actuales);
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_ACTUADOR] = array(
                    "tipo" => $fila_actual["tipo"],
                    "parametros_tipo" => $parametros_tipo_actuales);
            }
            if ($fila_anterior["parametros_tipo"] != "")
            {
                $parametros_tipo_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_tipo"]);
                sustituye_ids_nombres_parametros_tipo_actuador_accion_usuario($fila_anterior["tipo"], $parametros_tipo_anteriores);
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_ACTUADOR] = array(
                    "tipo" => $fila_anterior["tipo"],
                    "parametros_tipo" => $parametros_tipo_anteriores);
            }
        }
        if ($fila_actual["calibracion"] != $fila_anterior["calibracion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila_actual["calibracion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CALIBRACION] = $fila_anterior["calibracion"];
        }
        if ($fila_actual["grupo"] != $fila_anterior["grupo"])
        {
            $nombre_grupo = dame_nombre_grupo_actuadores($fila_actual["grupo"]);
            $nombre_grupo_anterior = dame_nombre_grupo_actuadores($fila_anterior["grupo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_GRUPO] = $nombre_grupo_anterior;
        }
        if ($fila_actual["programacion"] != $fila_anterior["programacion"])
        {
            $nombre_programacion = dame_nombre_programacion($fila_actual["programacion"]);
            $nombre_programacion_anterior = dame_nombre_programacion($fila_anterior["programacion"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion_anterior;
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
