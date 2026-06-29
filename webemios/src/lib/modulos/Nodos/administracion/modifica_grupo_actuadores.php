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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_GRUPO_ACTUADORES, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_grupo_actuadores = $_POST["id_grupo_actuadores"];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $clase = $_POST['clase'];
    $id_programacion = $_POST['id_programacion'];

	// Se comprueba si existe otro grupo de actuadores con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM grupos_actuadores
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_grupo_actuadores)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un grupo con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar el grupo de actuadores:
        // - Si el grupo tiene actuadores asignados, no se puede modificar la clase de actuador
        // - Localizaciones de grupo y de actuadores correctas
        $modificar_grupo = true;

        // Comprobación de clase modificada con actuadores asignados
        if ($modificar_grupo == true)
        {
            $consulta_actuadores_grupo = "
                SELECT
                    COUNT(*) AS actuadores_grupo
                FROM actuadores
                WHERE
                    grupo = '".$bd_red->_($id_grupo_actuadores)."'";
            $res_actuadores_grupo = $bd_red->ejecuta_consulta($consulta_actuadores_grupo);
            if (($res_actuadores_grupo == false) || ($res_actuadores_grupo->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuadores_grupo."'");
            }
            $fila_actuadores_grupo = $res_actuadores_grupo->dame_siguiente_fila();
            $numero_actuadores_grupo = $fila_actuadores_grupo["actuadores_grupo"];
            if ($numero_actuadores_grupo > 0)
            {
                // Se comprueba que si el grupo tiene hijos, no se modifique la clase de actuador
                $consulta_grupo = "
                    SELECT
                        clase
                    FROM grupos_actuadores
                    WHERE
                        id = '".$bd_red->_($id_grupo_actuadores)."'";
                $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                }
                $fila_grupo = $res_grupo->dame_siguiente_fila();

                // Si la clase es diferente, se devuelve error
                if ($fila_grupo["clase"] != $clase)
                {
                    $modificar_grupo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede cambiar el tipo porque el grupo tiene actuadores asignados");
                }
            }
        }

        // Comprobación de localizaciones correctas
        if ($modificar_grupo == true)
        {
            $numero_nodos_modificados = 0;
            $localizaciones_nodos_inicializadas = inicializa_localizaciones_nodos_grupo_localizacion_grupo(
                TIPO_NODO_ACTUADOR,
                $id_grupo_actuadores,
                $id_localizacion,
                $numero_nodos_modificados);
            if ($localizaciones_nodos_inicializadas == false)
            {
                $info_localizaciones_padres = NULL;
                $info_localizaciones_hijas = NULL;
                $localizacion_correcta = dame_localizacion_correcta_grupo_localizacion_grupo(
                    $info_localizaciones_padres,
                    $info_localizaciones_hijas,
                    TIPO_NODO_ACTUADOR,
                    $id_grupo_actuadores,
                    $id_localizacion);
                if ($localizacion_correcta == false)
                {
                    $modificar_grupo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Las localizaciones del grupo y de los actuadores asignados al mismo grupo no son correctas");
                }
            }
        }

        // Se modifica el grupo de actuadores
        if ($modificar_grupo == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_grupo_actuadores_anterior = dame_fila_grupo_actuadores($id_grupo_actuadores);
            $nombre_anterior = $fila_grupo_actuadores_anterior["nombre"];

            // Se modifica el grupo de actuadores
            $operacion_modificacion = "
                UPDATE grupos_actuadores
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    localizacion = '".$bd_red->_($id_localizacion)."',
                    clase = '".$bd_red->_($clase)."',
                    programacion = '".$bd_red->_($id_programacion)."'
                WHERE
                    id = '".$bd_red->_($id_grupo_actuadores)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Si se ha cambiado el nombre, se modifican las acciones enviadas y los comentarios
                if ($nombre != $nombre_anterior)
                {
                    $operacion_modificacion_acciones_enviadas = "
                        UPDATE acciones_grupos_actuadores
                        SET
                            grupo_actuadores = '".$bd_datos->_($nombre)."'
                        WHERE
                            (grupo_actuadores = '".$bd_datos->_($nombre_anterior)."')
                            AND (red = '".$_SESSION["id_red"]."')";
                    $res_modificacion_acciones_enviadas = $bd_datos->ejecuta_operacion($operacion_modificacion_acciones_enviadas);
                    if ($res_modificacion_acciones_enviadas == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_modificacion_acciones_enviadas."'");
                    }

                    modifica_comentarios_nodo(TIPO_NODO_GRUPO_ACTUADORES, $nombre_anterior, $nombre);
                }

                // Se recupera la fila actual
                $fila_grupo_actuadores_actual = dame_fila_grupo_actuadores($id_grupo_actuadores);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_grupo_actuadores(
                    $fila_grupo_actuadores_actual,
                    $fila_grupo_actuadores_anterior);

                $res = "OK";
                $msg = $idiomas->_("Grupo modificado correctamente");
                if ($numero_nodos_modificados > 0)
                {
                    $msg .= "\n(".$idiomas->_("se han modificado las localizaciones de los actuadores del grupo").")\n(".
                        $idiomas->_("actuadores modificados").": ".$numero_nodos_modificados.")";
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
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del grupo de actuadores
    function anyade_accion_usuario_modificar_grupo_actuadores($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_GRUPO_ACTUADORES;

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
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_anterior["clase"];
        }
        if ($fila_actual["programacion"] != $fila_anterior["programacion"])
        {
            $nombre_programacion = dame_nombre_programacion($fila_actual["programacion"]);
            $nombre_programacion_anterior = dame_nombre_programacion($fila_anterior["programacion"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion_anterior;
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
