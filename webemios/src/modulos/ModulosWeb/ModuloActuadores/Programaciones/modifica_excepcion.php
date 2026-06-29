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
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_EXCEPCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_excepcion = $_POST['id_excepcion'];
    $id_programacion = $_POST['id_programacion'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cadena_fecha_local = $_POST['fecha'];
    $cadena_fecha_inicio_local = $_POST['fecha_inicio'];
    $cadena_fecha_fin_local = $_POST['fecha_fin'];
    $cadena_dia_anyo_local = $_POST['dia_anyo'];
    $cadena_dia_anyo_inicio_local = $_POST['dia_anyo_inicio'];
    $cadena_dia_anyo_fin_local = $_POST['dia_anyo_fin'];
    $dia_semana = $_POST['dia_semana'];

    // Se comprueba si existe otra excepción con el mismo nombre en la misma programación
    $consulta_existe = "
        SELECT *
        FROM excepciones_programaciones
        WHERE
            (programacion = '".$bd_red->_($id_programacion)."')
            AND (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_excepcion)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una excepción con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar la excepción:
        // - No se permite una excepción del mismo tipo y fecha/día
        $modificar_excepcion = true;

        // No se permite una excepción del mismo tipo y fecha/día
        if ($modificar_excepcion == true)
        {
            $consulta_excepciones = "
                SELECT *
                FROM excepciones_programaciones
                WHERE
                    (programacion = '".$bd_red->_($id_programacion)."')
                    AND (tipo = '".$bd_red->_($tipo)."')";
            switch ($tipo)
            {
                case TIPO_EXCEPCION_PROGRAMACION_FECHA:
                {
                    $cadena_fecha_base_datos = convierte_formato_fecha($cadena_fecha_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
                    $consulta_excepciones .= " AND (fecha = '".$bd_red->_($cadena_fecha_base_datos)."')";
                    $cadena_fecha_inicio_base_datos = NULL;
                    $cadena_fecha_fin_base_datos = NULL;
                    $cadena_dia_anyo_base_datos = NULL;
                    $cadena_dia_anyo_inicio_base_datos = NULL;
                    $cadena_dia_anyo_fin_base_datos = NULL;
                    $dia_semana = NULL;
                    break;
                }
                case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
                {
                    $cadena_fecha_inicio_base_datos = convierte_formato_fecha($cadena_fecha_inicio_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
                    $cadena_fecha_fin_base_datos = convierte_formato_fecha($cadena_fecha_fin_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
                    $consulta_excepciones .= " AND (fecha_inicio = '".$bd_red->_($cadena_fecha_inicio_base_datos)."')
                        AND (fecha_fin = '".$bd_red->_($cadena_fecha_fin_base_datos)."')";
                    $cadena_fecha_base_datos = NULL;
                    $cadena_dia_anyo_base_datos = NULL;
                    $cadena_dia_anyo_inicio_base_datos = NULL;
                    $cadena_dia_anyo_fin_base_datos = NULL;
                    $dia_semana = NULL;
                    break;
                }
                case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
                {
                    $cadena_dia_anyo_base_datos = convierte_formato_fecha($cadena_dia_anyo_local, $_SESSION["formato_dia_anyo_local"], FORMATO_DIA_ANYO_BASE_DATOS);
                    $consulta_excepciones .= " AND (dia_anyo = '".$bd_red->_($cadena_dia_anyo_base_datos)."')";
                    $cadena_fecha_base_datos = NULL;
                    $cadena_fecha_inicio_base_datos = NULL;
                    $cadena_fecha_fin_base_datos = NULL;
                    $cadena_dia_anyo_inicio_base_datos = NULL;
                    $cadena_dia_anyo_fin_base_datos = NULL;
                    $dia_semana = NULL;
                    break;
                }
                case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
                {
                    $cadena_dia_anyo_inicio_base_datos = convierte_formato_fecha($cadena_dia_anyo_inicio_local, $_SESSION["formato_dia_anyo_local"], FORMATO_DIA_ANYO_BASE_DATOS);
                    $cadena_dia_anyo_fin_base_datos = convierte_formato_fecha($cadena_dia_anyo_fin_local, $_SESSION["formato_dia_anyo_local"], FORMATO_DIA_ANYO_BASE_DATOS);
                    $consulta_excepciones .= " AND (dia_anyo_inicio = '".$bd_red->_($cadena_dia_anyo_inicio_base_datos)."')
                        AND (dia_anyo_fin = '".$bd_red->_($cadena_dia_anyo_fin_base_datos)."')";
                    $cadena_fecha_base_datos = NULL;
                    $cadena_fecha_inicio_base_datos = NULL;
                    $cadena_fecha_fin_base_datos = NULL;
                    $cadena_dia_anyo_base_datos = NULL;
                    $dia_semana = NULL;
                    break;
                }
                case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
                {
                    $consulta_excepciones .= " AND (dia_semana = '".$bd_red->_($dia_semana)."')";
                    $cadena_fecha_base_datos = NULL;
                    $cadena_fecha_inicio_base_datos = NULL;
                    $cadena_fecha_fin_base_datos = NULL;
                    $cadena_dia_anyo_base_datos = NULL;
                    $cadena_dia_anyo_inicio_base_datos = NULL;
                    $cadena_dia_anyo_fin_base_datos = NULL;
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de excepción de programación desconocido: '".$tipo."'");
                }
            }
            $consulta_excepciones .= "
                    AND (id <> '".$bd_red->_($id_excepcion)."')";
            $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
            if ($res_excepciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
            }
            if ($res_excepciones->dame_numero_filas() > 0)
            {
                $modificar_excepcion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una excepción del mismo tipo y fecha/día");
            }
        }

        // Se modifica la excepción
        if ($modificar_excepcion == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_excepcion_anterior = dame_fila_excepcion_programacion($id_excepcion);

            // Se modifica la excepción de la programación
            $operacion_modificacion = "
                UPDATE excepciones_programaciones
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    tipo = '".$bd_red->_($tipo)."',
                    fecha = '".$bd_red->_($cadena_fecha_base_datos)."',
                    fecha_inicio = '".$bd_red->_($cadena_fecha_inicio_base_datos)."',
                    fecha_fin = '".$bd_red->_($cadena_fecha_fin_base_datos)."',
                    dia_anyo = '".$bd_red->_($cadena_dia_anyo_base_datos)."',
                    dia_anyo_inicio = '".$bd_red->_($cadena_dia_anyo_inicio_base_datos)."',
                    dia_anyo_fin = '".$bd_red->_($cadena_dia_anyo_fin_base_datos)."',
                    dia_semana = '".$bd_red->_($dia_semana)."'
                WHERE
                    id = '".$bd_red->_($id_excepcion)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_excepcion_actual = dame_fila_excepcion_programacion($id_excepcion);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_excepcion_programacion(
                    $fila_excepcion_actual,
                    $fila_excepcion_anterior);

                $res = "OK";
                $msg = $idiomas->_("Excepción modificada correctamente");
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


    // Añade la acción de usuario de modificación de la excepción
    function anyade_accion_usuario_modificar_excepcion_programacion($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_EXCEPCION_PROGRAMACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_EXCEPCION_PROGRAMACION] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_EXCEPCION_PROGRAMACION] = $fila_anterior["tipo"];
        }
        switch ($fila_actual["tipo"])
        {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha"] != $fila_anterior["fecha"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA] = $fila_actual["fecha"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha_inicio"] != $fila_anterior["fecha_inicio"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_actual["fecha_inicio"];
                }
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha_fin"] != $fila_anterior["fecha_fin"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_actual["fecha_fin"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo"] != $fila_anterior["dia_anyo"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO] = $fila_actual["dia_anyo"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo_inicio"] != $fila_anterior["dia_anyo_inicio"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila_actual["dia_anyo_inicio"];
                }
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo_fin"] != $fila_anterior["dia_anyo_fin"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila_actual["dia_anyo_fin"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_semana"] != $fila_anterior["dia_semana"]))
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA] = $fila_actual["dia_semana"];
                }
                break;
            }
        }
        switch ($fila_anterior["tipo"])
        {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha"] != $fila_anterior["fecha"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA] = $fila_anterior["fecha"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha_inicio"] != $fila_anterior["fecha_inicio"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_anterior["fecha_inicio"];
                }
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["fecha_fin"] != $fila_anterior["fecha_fin"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_anterior["fecha_fin"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo"] != $fila_anterior["dia_anyo"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_ANYO] = $fila_anterior["dia_anyo"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo_inicio"] != $fila_anterior["dia_anyo_inicio"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila_anterior["dia_anyo_inicio"];
                }
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_anyo_fin"] != $fila_anterior["dia_anyo_fin"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila_actual["dia_anyo_fin"];
                }
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
            {
                if (($fila_actual["tipo"] != $fila_anterior["tipo"]) || ($fila_actual["dia_semana"] != $fila_anterior["dia_semana"]))
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DIA_SEMANA] = $fila_anterior["dia_semana"];
                }
                break;
            }
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
