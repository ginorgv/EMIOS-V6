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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_RATIO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $sustituir_unidad_medida_sensor = $_POST['sustituir_unidad_medida_sensor'];
    $unidad_medida = $_POST['unidad_medida'];
    $tipo = $_POST['tipo'];
    $clase_sensor = $_POST['clase_sensor'];
    $campo_sensor = $_POST['campo_sensor'];
    $valor_defecto = $_POST['valor_defecto'];
    $id_sensor_defecto = $_POST['id_sensor_defecto'];

    // Se comprueba si existe un ratio con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM ratios
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un ratio con el mismo nombre");
    }
    else
    {
        // Se añade el ratio
        if ($valor_defecto == "")
        {
            $cadena_valor_defecto = "NULL";
        }
        else
        {
            $cadena_valor_defecto = "'".$bd_red->_($valor_defecto)."'";
        }
        $operacion_insercion = "
            INSERT INTO ratios (
                nombre,
                red,
                descripcion,
                sustituir_unidad_medida_sensor,
                unidad_medida,
                tipo,
                clase_sensor,
                campo_sensor,
                valor_defecto,
                sensor_defecto
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($descripcion)."',
                '".$bd_red->_($sustituir_unidad_medida_sensor)."',
                '".$bd_red->_($unidad_medida)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($clase_sensor)."',
                '".$bd_red->_($campo_sensor)."',
                ".$cadena_valor_defecto.",
                '".$bd_red->_($id_sensor_defecto)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del ratio añadido
            $id_ratio = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_ratio = dame_fila_ratio($id_ratio);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_ratio($fila_ratio);

            $res = "OK";
            $msg = $idiomas->_("Ratio añadido correctamente");
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


    // Añade la acción de usuario de adición del ratio
    function anyade_accion_usuario_anyadir_ratio($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_RATIO;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_SUSTITUIR_UNIDAD_MEDIDA_SENSOR_RATIO] = $fila["sustituir_unidad_medida_sensor"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_UNIDAD_MEDIDA_RATIO] = $fila["unidad_medida"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_RATIO] = $fila["tipo"];
        anyade_parametros_accion_usuario_parametros_tipo_ratio($fila, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
