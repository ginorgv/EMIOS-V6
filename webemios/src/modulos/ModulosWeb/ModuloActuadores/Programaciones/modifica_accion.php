<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_ACCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_accion = $_POST['id_accion'];
    $id_programacion = $_POST['id_programacion'];
    $id_accion_predefinida = $_POST['id_accion_predefinida'];
    $nombre = $_POST['nombre'];
    $contenido = $_POST['contenido'];
    $valor = $_POST['valor'];
    $dias_semana = $_POST['dias_semana'];
    $hora = $_POST['hora'];

    // Se comprueba si existe una acción para alguno de los días y hora especificados
    $existe_accion_dia_hora = false;
    $consulta_acciones = "
        SELECT
            dias_semana
        FROM acciones_programaciones
        WHERE
            (programacion = '".$bd_red->_($id_programacion)."')
            AND (hora = '".$bd_red->_($hora)."')
            AND (id <> '".$bd_red->_($id_accion)."')";
    $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
    if ($res_acciones == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_acciones."'");
    }
    while ($fila_accion = $res_acciones->dame_siguiente_fila())
    {
        $cadena_dias_semana_accion = $fila_accion["dias_semana"];
        if (($dias_semana[0] == "-1") || ($cadena_dias_semana_accion == "-1"))
        {
            $existe_accion_dia_hora = true;
            break;
        }
        else
        {
            $dias_semana_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_dias_semana_accion);
            if (count(array_intersect($dias_semana, $dias_semana_accion)) > 0)
            {
                $existe_accion_dia_hora = true;
                break;
            }
        }
    }

    if ($existe_accion_dia_hora == true)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una acción para ese día y hora");
    }
    else
    {
        // Si es una acción predefinida, se recuperan los datos de la acción predefinida
        if ($id_accion_predefinida != ID_NINGUNO)
        {
            $fila_accion_predefinida = dame_fila_accion_predefinida($id_accion_predefinida);
            $nombre = $fila_accion_predefinida['nombre'];
            $contenido = $fila_accion_predefinida['contenido'];
            $valor = $fila_accion_predefinida['valor'];
        }

        // Se recupera la fila anterior (antes de la modificación)
        $fila_accion_anterior = dame_fila_accion_programacion($id_accion);

        // Se modifica la acción de la programación
        $cadena_dias_semana = implode(SEPARADOR_PARAMETROS_SIMPLES, $dias_semana);
        $operacion_modificacion = "
            UPDATE acciones_programaciones
            SET
                nombre = '".$bd_red->_($nombre)."',
                contenido = '".$bd_red->_($contenido)."',
                valor = '".$bd_red->_($valor)."',
                dias_semana = '".$bd_red->_($cadena_dias_semana)."',
                hora = '".$bd_red->_($hora)."'
            WHERE
                id = '".$bd_red->_($id_accion)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se recupera la fila actual
            $fila_accion_actual = dame_fila_accion_programacion($id_accion);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_accion_programacion(
                $fila_accion_actual,
                $fila_accion_anterior);

            $res = "OK";
            $msg = $idiomas->_("Acción modificada correctamente");
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


    // Añade la acción de usuario de modificación de la acción
    function anyade_accion_usuario_modificar_accion_programacion($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_ACCION_PROGRAMACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["dias_semana"] != $fila_anterior["dias_semana"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIAS_SEMANA] = $fila_actual["dias_semana"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIAS_SEMANA] = $fila_anterior["dias_semana"];
        }
        $hora_fila_anterior = convierte_formato_fecha($fila_anterior["hora"], FORMATO_HORA, FORMATO_HORA_SIN_SEGUNDOS);
        if ($fila_actual["hora"] != $hora_fila_anterior)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA] = $fila_actual["hora"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_HORA] = $hora_fila_anterior;
        }
        if ($fila_actual["contenido"] != $fila_anterior["contenido"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila_actual["contenido"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila_anterior["contenido"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Nombre de la programación
        $nombre_programacion = dame_nombre_programacion($fila_actual["programacion"]);

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"]." (".$nombre_programacion.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"]." (".$nombre_programacion.")",
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
