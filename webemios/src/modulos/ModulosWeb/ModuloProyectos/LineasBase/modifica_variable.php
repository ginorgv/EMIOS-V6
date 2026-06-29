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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_VARIABLE_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_variable = $_POST['id_variable'];
    $id_linea_base = $_POST['id_linea_base'];
    $nombre = $_POST['nombre'];
    $clase_sensor = $_POST['clase_sensor'];
    $id_sensor = $_POST['id_sensor'];
    $campo_parametros_extra = $_POST['campo_parametros_extra'];

    // Se comprueba si existe otra variable con el mismo nombre en la misma línea base
    $consulta_existe = "
        SELECT *
        FROM variables_lineas_base
        WHERE
            (linea_base = '".$bd_red->_($id_linea_base)."')
            AND (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_variable)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una variable con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar la variable de la línea base:
        // - Comprobación de sensor y campo de sensor no repetido en las variables de la línea base
        $modificar_variable = true;

        // Comprobación de sensor y campo de sensor no repetido en las variables de la línea base
        if ($modificar_variable == true)
        {
            $consulta_variables_sensor = "
                SELECT *
                FROM variables_lineas_base
                WHERE
                    (linea_base = '".$bd_red->_($id_linea_base)."')
                    AND (sensor = '".$bd_red->_($id_sensor)."')
                    AND (campo_parametros_extra = '".$bd_red->_($campo_parametros_extra)."')
                    AND (id <> '".$bd_red->_($id_variable)."')";
            $res_variables_sensor = $bd_red->ejecuta_consulta($consulta_variables_sensor);
            if ($res_variables_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_variables_sensor."'");
            }
            if ($res_variables_sensor->dame_numero_filas() > 0)
            {
                $modificar_variable = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una variable con el mismo sensor y campo");
            }
        }

        // Se modifica la variable de la línea base
        if ($modificar_variable == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_variable_anterior = dame_fila_variable_linea_base($id_variable);

            // Se modifica la variable de la línea base
            $operacion_modificacion = "
                UPDATE variables_lineas_base
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    clase_sensor = '".$bd_red->_($clase_sensor)."',
                    sensor = '".$bd_red->_($id_sensor)."',
                    campo_parametros_extra = '".$bd_red->_($campo_parametros_extra)."'
                WHERE
                    id = '".$bd_red->_($id_variable)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
                invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base);

                // Se recupera la fila anterior (antes de la modificación)
                $fila_variable_actual = dame_fila_variable_linea_base($id_variable);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_variable_linea_base(
                    $fila_variable_actual,
                    $fila_variable_anterior);

                $res = "OK";
                $msg = $idiomas->_("Variable modificada correctamente");
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


    // Añade la acción de usuario de modificación de variable de línea base
    function anyade_accion_usuario_modificar_variable_linea_base($fila_actual, $fila_anterior)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_VARIABLE_LINEA_BASE;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["clase_sensor"] != $fila_anterior["clase_sensor"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase_sensor"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase_sensor"];
        }
        if ($fila_actual["sensor"] != $fila_anterior["sensor"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = dame_nombre_sensor($fila_actual["sensor"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = dame_nombre_sensor($fila_anterior["sensor"]);
        }
        if ($fila_actual["campo_parametros_extra"] != $fila_anterior["campo_parametros_extra"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase_sensor"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase_sensor"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila_actual["campo_parametros_extra"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila_anterior["campo_parametros_extra"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Nombre de la línea base
        $nombre_linea_base = dame_nombre_linea_base($fila_actual["linea_base"]);

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"]." (".$nombre_linea_base.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"]." (".$nombre_linea_base.")",
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
