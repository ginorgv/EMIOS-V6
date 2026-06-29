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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST["nombre"];
    $clase_actuador = $_POST["clase_actuador"];
    $id_programacion_anterior = $_POST["id_programacion_anterior"];

    // Se comprueba si existe una programación con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM programaciones
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
        $msg = $idiomas->_("Ya existe una programación con el mismo nombre");
    }
    else
    {
        // Se añade la programación
        $operacion_insercion = "
            INSERT INTO programaciones (
                nombre,
                red,
                clase
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($clase_actuador)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila de la programación añadida
            $id_programacion = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_programacion = dame_fila_programacion($id_programacion);

            // Si el identificador de programacion existe, es un duplicado de una programacion existente:
            // - Se duplican las acciones y las excepciones (si los hay)
            if ($id_programacion_anterior != ID_NINGUNO)
            {
                // Se recupera el id de la programacion añadida
                $id_programacion = $bd_red->dame_id_autoincremental_ultima_insercion();

                // Se recupera la fila anterior (antes de la modificación)
                $consulta_programacion_anterior = dame_fila_programacion($id_programacion_anterior);
                $clase_actuador_anterior = $fila_programacion_anterior["clase"];

                // Duplica las acciones y las excepciones de la programación anterior
                if ($clase_actuador == $clase_actuador_anterior)
                {
                    duplica_acciones_programacion_anterior($id_programacion_anterior, $id_programacion);
                }
                duplica_excepciones_programacion_anterior($id_programacion_anterior, $id_programacion);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_programacion($fila_programacion);

            $res = "OK";
            $msg = $idiomas->_("Programación añadida correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Duplica las acciones de la programación anterior
    function duplica_acciones_programacion_anterior($id_programacion_anterior, $id_programacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las acciones de la programación anterior (origen de la actual), se cambia la programación y se añaden
        $consulta_acciones = "
            SELECT *
            FROM acciones_programaciones
            WHERE
                programacion = '".$bd_red->_($id_programacion_anterior)."'";
        $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
        if ($res_acciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_acciones."'");
        }

        while ($fila_accion = $res_acciones->dame_siguiente_fila())
        {
            $operacion_insercion_accion = "
                INSERT INTO acciones_programaciones (
                    nombre,
                    red,
                    programacion,
                    contenido,
                    valor,
                    dias_semana,
                    hora
                ) VALUES (
                    '".$bd_red->_($fila_accion["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_programacion)."',
                    '".$bd_red->_($fila_accion["contenido"])."',
                    '".$bd_red->_($fila_accion["valor"])."',
                    '".$bd_red->_($fila_accion["dias_semana"])."',
                    '".$bd_red->_($fila_accion["hora"])."'
                )";
            $res_insercion_accion = $bd_red->ejecuta_operacion($operacion_insercion_accion);
            if ($res_insercion_accion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_accion."'");
            }
        }
    }


    // Duplica las excepciones de la programación anterior
    function duplica_excepciones_programacion_anterior($id_programacion_anterior, $id_programacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las excepciones de la programación anterior (origen de la actual), se cambia la programación y se añaden
        $consulta_excepciones = "
            SELECT *
            FROM excepciones_programaciones
            WHERE
                programacion = '".$bd_red->_($id_programacion_anterior)."'";
        $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
        if ($res_excepciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
        }

        while ($fila_excepcion = $res_excepciones->dame_siguiente_fila())
        {
            $operacion_insercion_excepcion = "
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
                    '".$bd_red->_($fila_excepcion["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_programacion)."',
                    '".$bd_red->_($fila_excepcion["tipo"])."',
                    '".$bd_red->_($fila_excepcion["fecha"])."',
                    '".$bd_red->_($fila_excepcion["fecha_inicio"])."',
                    '".$bd_red->_($fila_excepcion["fecha_fin"])."',
                    '".$bd_red->_($fila_excepcion["dia_anyo"])."',
                    '".$bd_red->_($fila_excepcion["dia_anyo_inicio"])."',
                    '".$bd_red->_($fila_excepcion["dia_anyo_fin"])."',
                    '".$bd_red->_($fila_excepcion["dia_semana"])."'
                )";
            $res_insercion_excepcion = $bd_red->ejecuta_operacion($operacion_insercion_excepcion);
            if ($res_insercion_excepcion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_excepcion."'");
            }
        }
    }


    // Añade la acción de usuario de adición de la programacion
    function anyade_accion_usuario_anyadir_programacion($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_PROGRAMACION;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila["clase"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
