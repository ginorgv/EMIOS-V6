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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_VARIABLE_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_linea_base = $_POST['id_linea_base'];
    $nombre = $_POST['nombre'];
    $clase_sensor = $_POST['clase_sensor'];
    $id_sensor = $_POST['id_sensor'];
    $campo_parametros_extra = $_POST['campo_parametros_extra'];

    // Se comprueba si existe una variable con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM variables_lineas_base
        WHERE
            (linea_base = '".$bd_red->_($id_linea_base)."')
            AND (nombre = '".$bd_red->_($nombre)."')";
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
        // Comprobaciones antes de añadir la variable de la línea base:
        // - Comprobación de sensor y campo de sensor no repetido en las variables de la línea base
        $anyadir_variable = true;

        // Comprobación de sensor y campo de sensor no repetido en las variables de la línea base
        if ($anyadir_variable == true)
        {
            $consulta_variables_sensor = "
                SELECT *
                FROM variables_lineas_base
                WHERE
                    (linea_base = '".$bd_red->_($id_linea_base)."')
                    AND (sensor = '".$bd_red->_($id_sensor)."')
                    AND (campo_parametros_extra = '".$bd_red->_($campo_parametros_extra)."')";
            $res_variables_sensor = $bd_red->ejecuta_consulta($consulta_variables_sensor);
            if ($res_variables_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_variables_sensor."'");
            }
            if ($res_variables_sensor->dame_numero_filas() > 0)
            {
                $anyadir_variable = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe una variable con el mismo sensor y campo");
            }
        }

        // Se añade la variable de la línea base
        if ($anyadir_variable == true)
        {
            // Se añade la variable de la línea base
            $operacion_insercion = "
                INSERT INTO variables_lineas_base (
                    nombre,
                    red,
                    linea_base,
                    clase_sensor,
                    sensor,
                    campo_parametros_extra
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_linea_base)."',
                    '".$bd_red->_($clase_sensor)."',
                    '".$bd_red->_($id_sensor)."',
                    '".$bd_red->_($campo_parametros_extra)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la variable añadida
                $id_variable = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_variable = dame_fila_variable_linea_base($id_variable);

                // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
                invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_variable_linea_base($fila_variable);

                $res = "OK";
                $msg = $idiomas->_("Variable añadida correctamente");
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


    // Añade la acción de usuario de adición de variable de línea base
    function anyade_accion_usuario_anyadir_variable_linea_base($fila)
    {
        // Nombre de la línea base
        $nombre_linea_base = dame_nombre_linea_base($fila["linea_base"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_VARIABLE_LINEA_BASE;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_linea_base.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase_sensor"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = dame_nombre_sensor($fila["sensor"]);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila["campo_parametros_extra"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
