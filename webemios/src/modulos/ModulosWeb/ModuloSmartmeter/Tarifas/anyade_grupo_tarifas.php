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
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_GRUPO_TARIFAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $medicion = $_POST['medicion'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    // Tabla de grupos de tarifas
    $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);

	// Se comprueba si existe un grupo de tarifas (de la medición correspondiente) con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ".$tabla_grupos_tarifas."
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
        // Se añade el grupo de tarifas
        $operacion_insercion = "
			INSERT INTO ".$tabla_grupos_tarifas." (
                nombre,
                descripcion,
                red
			) VALUES (
				'".$bd_red->_($nombre)."',
                '".$bd_red->_($descripcion)."',
                '".$_SESSION["id_red"]."'
			)";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del grupo de tarifas añadido
            $id_grupo_tarifas = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_grupo_tarifas = dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_grupo_tarifas($medicion, $fila_grupo_tarifas);

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


    // Añade la acción de usuario de adición del grupo de tarifas
    function anyade_accion_usuario_anyadir_grupo_tarifas($medicion, $fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_GRUPO_TARIFAS;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
