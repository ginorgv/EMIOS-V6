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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_GRUPO_ACTUADORES, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $clase = $_POST['clase'];
    $id_programacion = $_POST['id_programacion'];

	// Se comprueba si existe un grupo de actuadores con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM grupos_actuadores
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
        $msg = $idiomas->_("Ya existe un grupo con el mismo nombre");
    }
    else
    {
        // Se añade el grupo de actuadores
        $operacion_insercion = "
			INSERT INTO grupos_actuadores (
                nombre,
                red,
                descripcion,
                localizacion,
                clase,
                programacion
			) VALUES (
				'".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion)."',
                '".$bd_red->_($id_localizacion)."',
                '".$bd_red->_($clase)."',
                '".$bd_red->_($id_programacion)."'
			)";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del grupo añadido
            $id_grupo = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_grupo_actuadores = dame_fila_grupo_actuadores($id_grupo);

            // Se añade el grupo de actuadores al usuario actual (si es necesario)
            if ($id_localizacion == ID_NINGUNO)
            {
                anyade_actuador_grupo_parametros_modulo_actuadores_usuario_actual(TIPO_NODO_GRUPO_ACTUADORES, $id_grupo);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_grupo_actuadores($fila_grupo_actuadores);

            $res = "OK";
            $msg = $idiomas->_("Grupo añadido correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_nodo" => $id_grupo))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del grupo de actuadores
    function anyade_accion_usuario_anyadir_grupo_actuadores($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_GRUPO_ACTUADORES;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_localizacion = dame_nombre_localizacion($fila["localizacion"]);
        $nombre_programacion = dame_nombre_programacion($fila["programacion"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $fila["clase"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_PROGRAMACION] = $nombre_programacion;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
