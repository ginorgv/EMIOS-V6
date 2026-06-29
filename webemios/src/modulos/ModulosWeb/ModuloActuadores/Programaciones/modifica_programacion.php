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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_programacion = $_POST["id_programacion"];
    $nombre = $_POST["nombre"];
    $clase_actuador = $_POST["clase_actuador"];

    // Se comprueba si existe otra programación con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM programaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_programacion)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una programación con el mismo nombre");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_programacion_anterior = dame_fila_programacion($id_programacion);

        // Se modifica la programación
        $operacion_modificacion = "
            UPDATE programaciones
            SET
                nombre = '".$bd_red->_($nombre)."',
                clase = '".$bd_red->_($clase_actuador)."'
            WHERE
                id = '".$bd_red->_($id_programacion)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se añade la acción de usuario
            $fila_programacion_actual = dame_fila_programacion($id_programacion);
            anyade_accion_usuario_modificar_programacion(
                $fila_programacion_actual,
                $fila_programacion_anterior);

            $res = "OK";
            $msg = $idiomas->_("Programación modificada correctamente");
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


    // Añade la acción de usuario de modificación de la programación
    function anyade_accion_usuario_modificar_programacion($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_PROGRAMACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["clase"] != $fila_anterior["clase"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_actual["clase"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila_anterior["clase"];
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
