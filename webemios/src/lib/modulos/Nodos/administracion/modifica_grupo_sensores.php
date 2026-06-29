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
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_GRUPO_SENSORES, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_grupo_sensores = $_POST['id_grupo_sensores'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $clase = $_POST['clase'];

	// Se comprueba si existe otro grupo de sensores con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM grupos_sensores
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_grupo_sensores)."')";
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
        // Comprobaciones antes de modificar el grupo de sensores:
        // - Si el grupo tiene sensores asignados, no se puede modificar la clase de sensor
        // - Localizaciones de grupo y de sensores correctas
        $modificar_grupo_sensores = true;

        // Comprobación de clase modificada con sensores asignados
        if ($modificar_grupo_sensores == true)
        {
            $consulta_sensores_grupo = "
                SELECT
                    COUNT(*) AS sensores_grupo
                FROM sensores
                WHERE
                    grupo = '".$bd_red->_($id_grupo_sensores)."'";
            $res_sensores_grupo = $bd_red->ejecuta_consulta($consulta_sensores_grupo);
            if (($res_sensores_grupo == false) || ($res_sensores_grupo->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores_grupo."'");
            }
            $fila_sensores_grupo = $res_sensores_grupo->dame_siguiente_fila();
            $numero_sensores_grupo = $fila_sensores_grupo["sensores_grupo"];
            if ($numero_sensores_grupo > 0)
            {
                // Se comprueba que si el grupo tiene hijos, no se modifique la clase de sensor
                $consulta_grupo = "
                    SELECT
                        clase
                    FROM grupos_sensores
                    WHERE
                        id = '".$bd_red->_($id_grupo_sensores)."'";
                $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                }
                $fila_grupo = $res_grupo->dame_siguiente_fila();

                // Si la clase es diferente, se devuelve error
                if ($fila_grupo["clase"] != $clase)
                {
                    $modificar_grupo_sensores = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede cambiar la clase porque el grupo tiene sensores asignados");
                }
            }
        }

        // Comprobación de localizaciones correctas
        if ($modificar_grupo_sensores == true)
        {
            $numero_nodos_modificados = 0;
            $localizaciones_nodos_inicializadas = inicializa_localizaciones_nodos_grupo_localizacion_grupo(
                TIPO_NODO_SENSOR,
                $id_grupo_sensores,
                $id_localizacion,
                $numero_nodos_modificados);
            if ($localizaciones_nodos_inicializadas == false)
            {
                $info_localizaciones_padres = NULL;
                $info_localizaciones_hijas = NULL;
                $localizacion_correcta = dame_localizacion_correcta_grupo_localizacion_grupo(
                    $info_localizaciones_padres,
                    $info_localizaciones_hijas,
                    TIPO_NODO_SENSOR,
                    $id_grupo_sensores,
                    $id_localizacion);
                if ($localizacion_correcta == false)
                {
                    $modificar_grupo_sensores = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Las localizaciones del grupo y de los sensores asignados al mismo grupo no son correctas");
                }
            }
        }

        // Se modifica el grupo de sensores
        if ($modificar_grupo_sensores == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_grupo_sensores_anterior = dame_fila_grupo_sensores($id_grupo_sensores);

            // Se modifica el grupo de sensores
            $operacion_modificacion = "
                UPDATE grupos_sensores
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    clase = '".$bd_red->_($clase)."',
                    localizacion = '".$bd_red->_($id_localizacion)."'
                WHERE
                    id = '".$bd_red->_($id_grupo_sensores)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_grupo_sensores_actual = dame_fila_grupo_sensores($id_grupo_sensores);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_grupo_sensores(
                    $fila_grupo_sensores_actual,
                    $fila_grupo_sensores_anterior);

                $res = "OK";
                $msg = $idiomas->_("Grupo modificado correctamente");
                if ($numero_nodos_modificados > 0)
                {
                    $msg .= "\n(".$idiomas->_("se han modificado las localizaciones de los sensores del grupo").")\n(".
                        $idiomas->_("sensores modificados").": ".$numero_nodos_modificados.")";
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


    // Añade la acción de usuario de modificación del grupo de sensores
    function anyade_accion_usuario_modificar_grupo_sensores($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_GRUPO_SENSORES;

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
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase"];
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
