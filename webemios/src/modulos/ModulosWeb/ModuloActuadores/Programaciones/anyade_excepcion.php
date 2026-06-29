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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_EXCEPCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
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

    // Se comprueba si existe una excepción con el mismo nombre en la misma programación
    $consulta_existe = "
        SELECT *
        FROM excepciones_programaciones
        WHERE
            (programacion = '".$bd_red->_($id_programacion)."')
            AND (nombre = '".$bd_red->_($nombre)."')";
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
        // Comprobaciones antes de añadir la excepción:
        // - No se permite una excepción del mismo tipo y fecha/día
        $anyadir_excepcion = true;

        // No se permite una excepción del mismo tipo y fecha/día
        if ($anyadir_excepcion == true)
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
            $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
            if ($res_excepciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
            }
            if ($res_excepciones->dame_numero_filas() > 0)
            {
                $anyadir_excepcion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una excepción del mismo tipo y fecha/día");
            }
        }

        // Se añade la excepción
        if ($anyadir_excepcion == true)
        {
            // Se añade la excepción
            $operacion_insercion = "
                INSERT INTO excepciones_programaciones (
                    nombre,
                    red,
                    programacion,
                    tipo,
                    fecha,
                    fecha_inicio,
                    fecha_fin,
                    dia_anyo,
                    dia_anyo_inicio,
                    dia_anyo_fin,
                    dia_semana
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_programacion)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($cadena_fecha_base_datos)."',
                    '".$bd_red->_($cadena_fecha_inicio_base_datos)."',
                    '".$bd_red->_($cadena_fecha_fin_base_datos)."',
                    '".$bd_red->_($cadena_dia_anyo_base_datos)."',
                    '".$bd_red->_($cadena_dia_anyo_inicio_base_datos)."',
                    '".$bd_red->_($cadena_dia_anyo_fin_base_datos)."',
                    '".$bd_red->_($dia_semana)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recupera el id de la excepción añadida
                $id_excepcion = $bd_red->dame_id_autoincremental_ultima_insercion();

                // Se añade la acción de usuario
                $fila_excepcion = dame_fila_excepcion_programacion($id_excepcion);
                anyade_accion_usuario_anyadir_excepcion_programacion($fila_excepcion);

                $res = "OK";
                $msg = $idiomas->_("Excepción añadida correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
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


    // Añade la acción de usuario de adición de la excepción
    function anyade_accion_usuario_anyadir_excepcion_programacion($fila)
    {
        // Nombre de la programación
        $nombre_programacion = dame_nombre_programacion($fila["programacion"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_EXCEPCION_PROGRAMACION;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_programacion.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_EXCEPCION_PROGRAMACION] = $fila["tipo"];
        switch ($fila["tipo"])
        {
            case TIPO_EXCEPCION_PROGRAMACION_FECHA:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA] = $fila["fecha"];
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila["fecha_inicio"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila["fecha_fin"];
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO] = $fila["dia_anyo"];
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_INICIO] = $fila["dia_anyo_inicio"];
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_ANYO_FIN] = $fila["dia_anyo_fin"];
                break;
            }
            case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIA_SEMANA] = $fila["dia_semana"];
                break;
            }
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
