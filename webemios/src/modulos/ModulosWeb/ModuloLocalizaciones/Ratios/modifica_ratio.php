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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_RATIO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_ratio = $_POST['id_ratio'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $sustituir_unidad_medida_sensor = $_POST['sustituir_unidad_medida_sensor'];
    $unidad_medida = $_POST['unidad_medida'];
    $tipo = $_POST['tipo'];
    $clase_sensor = $_POST['clase_sensor'];
    $campo_sensor = $_POST['campo_sensor'];
    $valor_defecto = $_POST['valor_defecto'];
    $id_sensor_defecto = $_POST['id_sensor_defecto'];

    // Se comprueba si existe otro ratio con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM ratios
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_ratio)."')";
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
        // Se recupera la fila anterior (antes de la modificación)
        $fila_ratio_anterior = dame_fila_ratio($id_ratio);

        // Se modifica el ratio
        if ($valor_defecto == "")
        {
            $cadena_valor_defecto = "NULL";
        }
        else
        {
            $cadena_valor_defecto = "'".$bd_red->_($valor_defecto)."'";
        }
        $operacion_modificacion = "
            UPDATE ratios
            SET
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
                sustituir_unidad_medida_sensor = '".$bd_red->_($sustituir_unidad_medida_sensor)."',
                unidad_medida = '".$bd_red->_($unidad_medida)."',
                tipo = '".$bd_red->_($tipo)."',
                clase_sensor = '".$bd_red->_($clase_sensor)."',
                campo_sensor = '".$bd_red->_($campo_sensor)."',
                valor_defecto = ".$cadena_valor_defecto.",
                sensor_defecto = '".$bd_red->_($id_sensor_defecto)."'
            WHERE
                id = '".$bd_red->_($id_ratio)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se recupera la fila actual
            $fila_ratio_actual = dame_fila_ratio($id_ratio);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_ratio(
                $fila_ratio_actual,
                $fila_ratio_anterior);

            $res = "OK";
            $msg = $idiomas->_("Ratio modificado correctamente");
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


    // Añade la acción de usuario de modificación del ratio
    function anyade_accion_usuario_modificar_ratio($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_RATIO;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["sustituir_unidad_medida_sensor"] != $fila_anterior["sustituir_unidad_medida_sensor"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_SUSTITUIR_UNIDAD_MEDIDA_SENSOR_RATIO] = $fila_actual["sustituir_unidad_medida_sensor"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_SUSTITUIR_UNIDAD_MEDIDA_SENSOR_RATIO] = $fila_anterior["sustituir_unidad_medida_sensor"];
        }
        if ($fila_actual["unidad_medida"] != $fila_anterior["unidad_medida"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_UNIDAD_MEDIDA_RATIO] = $fila_actual["unidad_medida"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_UNIDAD_MEDIDA_RATIO] = $fila_anterior["unidad_medida"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_RATIO] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_RATIO] = $fila_anterior["tipo"];
            anyade_parametros_accion_usuario_parametros_tipo_ratio($fila_actual, $parametros_accion_usuario);
            anyade_parametros_accion_usuario_parametros_tipo_ratio($fila_anterior, $parametros_accion_usuario_anteriores);
        }
        else
        {
            switch ($fila_actual["tipo"])
            {
                case TIPO_RATIO_FIJO:
                {
                    if ($fila_actual["valor_defecto"] != $fila_anterior["valor_defecto"])
                    {
                        if ($fila_actual["valor_defecto"] != "")
                        {
                            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_DEFECTO_RATIO] = $fila_actual["valor_defecto"];
                        }
                        if ($fila_anterior["valor_defecto"] != "")
                        {
                            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VALOR_DEFECTO_RATIO] = $fila_anterior["valor_defecto"];
                        }
                    }
                    break;
                }
                case TIPO_RATIO_VARIABLE:
                {
                    if ($fila_actual["clase_sensor"] != $fila_anterior["clase_sensor"])
                    {
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase_sensor"];
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_SENSOR] = $fila_actual["campo_sensor"];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase_sensor"];
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPO_SENSOR] = $fila_anterior["campo_sensor"];
                    }
                    else
                    {
                        if ($fila_actual["campo_sensor"] != $fila_anterior["campo_sensor"])
                        {
                            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_SENSOR] = $fila_actual["campo_sensor"];
                            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPO_SENSOR] = $fila_anterior["campo_sensor"];
                        }
                    }
                    if ($fila_actual["sensor_defecto"] != $fila_anterior["sensor_defecto"])
                    {
                        $nombre_sensor_defecto = dame_nombre_sensor($fila_actual["sensor_defecto"]);
                        $nombre_sensor_defecto_anterior = dame_nombre_sensor($fila_anterior["sensor_defecto"]);
                        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_DEFECTO_RATIO] = $nombre_sensor_defecto;
                        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR_DEFECTO_RATIO] = $nombre_sensor_defecto_anterior;
                    }
                    break;
                }
            }
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
