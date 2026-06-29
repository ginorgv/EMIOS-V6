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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_programacion = $_POST['id_programacion'];

    // Comprobaciones antes de eliminar la programación
    // - No se permite eliminar la programación si está asignada a algún actuador o grupo de actuadores
    $eliminar_programacion = true;

    // No se permite eliminar la programación si está asignada a algún actuador o grupo de actuadores
    if ($eliminar_programacion == true)
    {
        $consulta_actuadores = "
            SELECT nombre
            FROM actuadores
            WHERE
                programacion = '".$bd_red->_($id_programacion)."'
            ORDER BY nombre ASC";
        $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
        if ($res_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
        }
        if ($res_actuadores->dame_numero_filas() > 0)
        {
            $eliminar_programacion = false;

            $fila_actuador = $res_actuadores->dame_siguiente_fila();
            $nombre_actuador = $fila_actuador["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la programación porque está asignada a algún actuador")."\n(".
                $nombre_actuador.")";
        }
    }
    if ($eliminar_programacion == true)
    {
        $consulta_grupos = "
            SELECT nombre
            FROM grupos_actuadores
            WHERE
                programacion = '".$bd_red->_($id_programacion)."'
            ORDER BY nombre ASC";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }
        if ($res_grupos->dame_numero_filas() > 0)
        {
            $eliminar_programacion = false;

            $fila_grupo = $res_grupos->dame_siguiente_fila();
            $nombre_grupo = $fila_grupo["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la programación porque está asignada a algún grupo de actuadores")."\n(".
                $nombre_grupo.")";
        }
    }

    // Se elimina la programación
    if ($eliminar_programacion == true)
    {
        // Se recupera la fila de la programación
        $fila_programacion = dame_fila_programacion($id_programacion);

        // Se elimina la programación
        $operacion_borrado = "
            DELETE
            FROM programaciones
            WHERE
                id = '".$bd_red->_($id_programacion)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se eliminan las acciones de la programación
            $operacion_borrado_acciones = "
                DELETE
                FROM acciones_programaciones
                WHERE
                    programacion = '".$bd_red->_($id_programacion)."'";
            $res_borrado_acciones = $bd_red->ejecuta_operacion($operacion_borrado_acciones);
            if ($res_borrado_acciones == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_acciones."'");
            }

            // Se eliminan las excepciones de la programación
            $operacion_borrado_excepciones = "
                DELETE
                FROM excepciones_programaciones
                WHERE
                    programacion = '".$bd_red->_($id_programacion)."'";
            $res_borrado_excepciones = $bd_red->ejecuta_operacion($operacion_borrado_excepciones);
            if ($res_borrado_excepciones == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_excepciones."'");
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_programacion($fila_programacion);

            $res = "OK";
            $msg = $idiomas->_("Programación eliminada correctamente");
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


    // Añade la acción de usuario de eliminación de la programación
    function anyade_accion_usuario_eliminar_programacion($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_PROGRAMACION;
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
