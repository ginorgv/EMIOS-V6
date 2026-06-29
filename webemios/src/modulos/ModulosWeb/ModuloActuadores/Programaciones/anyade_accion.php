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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_ACCION_PROGRAMACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_programacion = $_POST['id_programacion'];
    $nombre = $_POST['nombre'];
    $id_accion_predefinida = $_POST['id_accion_predefinida'];
    $contenido = $_POST['contenido'];
    $valor = $_POST['valor'];
    $dias_semana = $_POST['dias_semana'];
    $hora = $_POST['hora'];

    // Se comprueba si existe una acción para alguno de los días y hora especificados
    $existe_accion_dia_hora = false;
    $consulta_acciones = "
        SELECT dias_semana
        FROM acciones_programaciones
        WHERE
            (programacion = '".$bd_red->_($id_programacion)."')
            AND (hora = '".$bd_red->_($hora)."')";
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

        // Se añade la acción de la programación
        $cadena_dias_semana = implode(SEPARADOR_PARAMETROS_SIMPLES, $dias_semana);
        $operacion_insercion = "
            INSERT INTO acciones_programaciones (
                nombre,
                red,
                programacion,
                contenido,
                valor,
                dias_semana,
                hora
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_programacion)."',
                '".$bd_red->_($contenido)."',
                '".$bd_red->_($valor)."',
                '".$bd_red->_($cadena_dias_semana)."',
                '".$bd_red->_($hora)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila de la acción añadida
            $id_accion = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_accion = dame_fila_accion_programacion($id_accion);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_accion_programacion($fila_accion);

            $res = "OK";
            $msg = $idiomas->_("Acción añadida correctamente");
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


    // Añade la acción de usuario de adición de la acción
    function anyade_accion_usuario_anyadir_accion_programacion($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_ACCION_PROGRAMACION;
        $objeto_accion_usuario = $fila["nombre"]." (".dame_nombre_programacion($fila["programacion"]).")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIAS_SEMANA] = $fila["dias_semana"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORA] = $fila["hora"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $fila["contenido"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
