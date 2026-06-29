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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_GRUPO_SENSORES, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_localizacion = $_POST['id_localizacion'];
    $clase = $_POST['clase'];

	// Se comprueba si existe un grupo de sensores con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM grupos_sensores
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
        // Se añade el grupo de sensores
        $operacion_insercion = "
			INSERT INTO grupos_sensores (
                nombre,
                red,
                descripcion,
                localizacion,
                clase
			) VALUES (
				'".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion)."',
                '".$bd_red->_($id_localizacion)."',
                '".$bd_red->_($clase)."'
			)";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del grupo añadido
            $id_grupo = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_grupo_sensores = dame_fila_grupo_sensores($id_grupo);

            // Se añade el grupo de sensores al usuario actual (si es necesario)
            if ($id_localizacion == ID_NINGUNO)
            {
                anyade_sensor_grupo_parametros_modulo_sensores_usuario_actual(TIPO_NODO_GRUPO_SENSORES, $id_grupo);
            }

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_grupo_sensores($fila_grupo_sensores);

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
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del grupo de sensores
    function anyade_accion_usuario_anyadir_grupo_sensores($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_GRUPO_SENSORES;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_localizacion = dame_nombre_localizacion($fila["localizacion"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION] = $nombre_localizacion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
