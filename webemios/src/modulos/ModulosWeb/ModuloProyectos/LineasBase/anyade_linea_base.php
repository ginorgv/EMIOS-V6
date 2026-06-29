<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $clase_sensor = $_POST['clase_sensor'];
    $id_sensor = $_POST['id_sensor'];
    $campo_parametros_extra = $_POST['campo_parametros_extra'];
    $cadena_parametros_tipo = $_POST['parametros_tipo'];
    $parametros_tipo_json = $_POST['parametros_tipo_json'];
    $intervalo_valores = $_POST['intervalo_valores'];
    $cadena_fecha_inicio_periodo_referencia_local_local = $_POST['fecha_inicio_periodo_referencia'];
    $cadena_fecha_fin_periodo_referencia_local_local = $_POST['fecha_fin_periodo_referencia'];
    $error_estandar = $_POST['error_estandar'];
    $coeficiente_variacion = $_POST['coeficiente_variacion'];
    $coeficiente_correlacion = $_POST['coeficiente_correlacion'];
    $cadena_horario_semanal = $_POST['horario_semanal'];
    $cadena_exclusion_fechas = $_POST['exclusion_fechas'];
    $id_linea_base_anterior = $_POST["id_linea_base_anterior"];

    // Parámetros auxiliares
    $intervalo_valores_anterior = $_POST['intervalo_valores_anterior'];

    // Conversión de fechas
    $cadena_fecha_inicio_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_referencia_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $cadena_fecha_fin_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_fin_periodo_referencia_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Se comprueba si existe una línea base con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM lineas_base
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
        $msg = $idiomas->_("Ya existe una línea base con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de añadir la línea base:
        // - No se permite una clase sin procesado de valores
        // - No se permite la clase energía activa y coste (tiene que ser incremento)
        // - No se permite cambiar el intervalo de valores si tiene excepciones (sería diferente al de las excepciones)
        $anyadir_linea_base = true;

        // No se permite una clase sin procesado de valores
        if ($anyadir_linea_base == true)
        {
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
            if ($caracteristicas_clase_sensor["procesado_valores"] == false)
            {
                $anyadir_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("La clase de sensor seleccionada no tiene procesado de valores");
            }
        }

        // No se permite la clase de energía activa o de gas y coste (tiene que ser incremento)
        if ($anyadir_linea_base == true)
        {
            if ((($clase_sensor == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo == CAMPO_COSTE)) ||
                (($clase_sensor == CLASE_SENSOR_GAS) && ($campo == CAMPO_COSTE)))
            {
                $anyadir_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("El campo de sensor seleccionado debe ser consumo (el coste se calculará en el proyecto)");
            }
        }

        // No se permite cambiar el intervalo de valores si tiene excepciones (sería diferente al de las excepciones)
        if ($anyadir_linea_base == true)
        {
            if ($intervalo_valores != $intervalo_valores_anterior)
            {
                $consulta_excepciones = "
                    SELECT *
                    FROM excepciones_lineas_base
                    WHERE
                        linea_base_padre = '".$bd_red->_($id_linea_base_anterior)."'";
                $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
                if ($res_excepciones == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
                }
                if ($res_excepciones->dame_numero_filas() > 0)
                {
                    $anyadir_linea_base = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede modificar el intervalo de valores porque la línea base tiene excepciones");
                }
            }
        }

        // Se añade la línea base
        if ($anyadir_linea_base == true)
        {
            // Se añade la línea base
            $operacion_insercion = "
                INSERT INTO lineas_base (
                    nombre,
                    red,
                    descripcion,
                    clase_sensor,
                    sensor,
                    campo_parametros_extra,
                    tipo,
                    parametros_tipo,
                    parametros_tipo_json,
                    intervalo_valores,
                    fecha_inicio_periodo_referencia,
                    fecha_fin_periodo_referencia,
                    error_estandar,
                    coeficiente_variacion,
                    coeficiente_correlacion,
                    horario_semanal,
                    exclusion_fechas
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($descripcion)."',
                    '".$bd_red->_($clase_sensor)."',
                    '".$bd_red->_($id_sensor)."',
                    '".$bd_red->_($campo_parametros_extra)."',
                    '".$bd_red->_($tipo)."',
                    '".$bd_red->_($cadena_parametros_tipo)."',
                    '".$bd_red->_($parametros_tipo_json)."',
                    '".$bd_red->_($intervalo_valores)."',
                    '".$bd_red->_($cadena_fecha_inicio_periodo_referencia_base_datos_local)."',
                    '".$bd_red->_($cadena_fecha_fin_periodo_referencia_base_datos_local)."',
                    '".$bd_red->_($error_estandar)."',
                    '".$bd_red->_($coeficiente_variacion)."',
                    '".$bd_red->_($coeficiente_correlacion)."',
                    '".$bd_red->_($cadena_horario_semanal)."',
                    '".$bd_red->_($cadena_exclusion_fechas)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la línea base añadida
                $id_linea_base = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_linea_base = dame_fila_linea_base($id_linea_base);

                // Si el identificador de línea base existe, es un duplicado de una línea base existente:
                // - Se duplican las variables
                // - Se duplican las excepciones
                if ($id_linea_base_anterior != ID_NINGUNO)
                {
                    switch ($tipo)
                    {
                        case TIPO_LINEA_BASE_FUNCIONAL:
                        {
                            duplica_variables_linea_base_anterior($id_linea_base_anterior, $id_linea_base);
                            break;
                        }
                    }
                    duplica_excepciones_linea_base_anterior($id_linea_base_anterior, $id_linea_base);
                }

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_linea_base($fila_linea_base);

                $res = "OK";
                $msg = $idiomas->_("Línea base añadida correctamente");
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


    // Duplica las variables de la línea base anterior
    function duplica_variables_linea_base_anterior($id_linea_base_anterior, $id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las variables de la línea base anterior anterior, se cambia la línea base padre y se añaden
        $consulta_variables = "
            SELECT *
            FROM variables_lineas_base
            WHERE
                linea_base = '".$bd_red->_($id_linea_base_anterior)."'";
        $res_variables = $bd_red->ejecuta_consulta($consulta_variables);
        if ($res_variables == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_variables."'");
        }

        while ($fila_variable = $res_variables->dame_siguiente_fila())
        {
            $operacion_insercion_variable = "
                INSERT INTO variables_lineas_base (
                    nombre,
                    red,
                    linea_base,
                    clase_sensor,
                    sensor,
                    campo_parametros_extra
                ) VALUES (
                    '".$bd_red->_($fila_variable["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_linea_base)."',
                    '".$bd_red->_($fila_variable["clase_sensor"])."',
                    '".$bd_red->_($fila_variable["sensor"])."',
                    '".$bd_red->_($fila_variable["campo_parametros_extra"])."'
                )";
            $res_insercion_variable = $bd_red->ejecuta_operacion($operacion_insercion_variable);
            if ($res_insercion_variable == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_variable."'");
            }
        }
    }


    // Duplica las excepciones de la línea base anterior
    function duplica_excepciones_linea_base_anterior($id_linea_base_anterior, $id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las excepciones de la línea base anterior anterior, se cambia la línea base padre y se añaden
        $consulta_excepciones = "
            SELECT *
            FROM excepciones_lineas_base
            WHERE
                linea_base_padre = '".$bd_red->_($id_linea_base_anterior)."'";
        $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
        if ($res_excepciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
        }

        while ($fila_excepcion = $res_excepciones->dame_siguiente_fila())
        {
            $operacion_insercion_excepcion = "
                INSERT INTO excepciones_lineas_base (
                    nombre,
                    descripcion,
                    red,
                    linea_base_padre,
                    linea_base_hija,
                    horario_semanal,
                    inclusion_fechas
                ) VALUES (
                    '".$bd_red->_($fila_excepcion["nombre"])."',
                    '".$bd_red->_($fila_excepcion["descripcion"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_linea_base)."',
                    '".$bd_red->_($fila_excepcion["linea_base_hija"])."',
                    '".$bd_red->_($fila_excepcion["horario_semanal"])."',
                    '".$bd_red->_($fila_excepcion["inclusion_fechas"])."'
                )";
            $res_insercion_excepcion = $bd_red->ejecuta_operacion($operacion_insercion_excepcion);
            if ($res_insercion_excepcion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_excepcion."'");
            }
        }
    }
?>
