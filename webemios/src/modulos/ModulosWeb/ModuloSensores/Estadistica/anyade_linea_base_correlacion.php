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
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_LINEA_BASE_CORRELACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre_linea_base = $_POST['nombre_linea_base'];
    $clases_sensores_independientes = $_POST["clases_sensores_independientes"];
    $ids_sensores_independientes = $_POST["ids_sensores_independientes"];
    $nombres_sensores_independientes = $_POST["nombres_sensores_independientes"];
    $campos_independientes = $_POST["campos_independientes"];
    $parametros_extra_campos_independientes = $_POST["parametros_extra_campos_independientes"];
    $clase_sensor_dependiente = $_POST["clase_sensor_dependiente"];
    $id_sensor_dependiente = $_POST["id_sensor_dependiente"];
    $nombre_sensor_dependiente = $_POST["nombre_sensor_dependiente"];
    $campo_dependiente = $_POST["campo_dependiente"];
    $parametros_extra_campo_dependiente = $_POST["parametros_extra_campo_dependiente"];
    $cadena_fecha_hora_inicio_local_local = $_POST["fecha_hora_inicio"];
    $cadena_fecha_hora_fin_local_local = $_POST["fecha_hora_fin"];
    $intervalo_valores = $_POST["intervalo_valores"];
    $cadena_horario_semanal = $_POST["cadena_horario_semanal"];
    $cadena_exclusion_fechas = $_POST["cadena_exclusion_fechas"];
    $cadena_funcion_correlacion = $_POST["cadena_funcion_correlacion"];
    $error_estandar = $_POST["error_estandar"];
    $coeficiente_variacion = $_POST["coeficiente_variacion"];
    $coeficiente_correlacion = $_POST["coeficiente_correlacion"];

    // Se comprueba si existe una línea base con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM lineas_base
        WHERE
            (nombre = '".$bd_red->_($nombre_linea_base)."')
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
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor_dependiente);
            if ($caracteristicas_clase_sensor["procesado_valores"] == false)
            {
                $anyadir_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("La clase de sensor seleccionada no tiene procesado de valores");
            }
        }

        // No se permiten las clases de energía activa o de gas y coste (tiene que ser consumo)
        if ($anyadir_linea_base == true)
        {
            if ((($clase_sensor_dependiente == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo_dependiente == CAMPO_COSTE)) ||
                (($clase_sensor_dependiente == CLASE_SENSOR_GAS) && ($campo_dependiente == CAMPO_COSTE)))
            {
                $anyadir_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("El campo de sensor seleccionado debe ser consumo (el coste se calculará en el proyecto)");
            }
        }

        // See añade la línea base
        if ($anyadir_linea_base == true)
        {
            // Tipo y función de línea base (se eliminan el 'y = ' de la cadena de la función de correlación)
            $tipo_linea_base = TIPO_LINEA_BASE_FUNCIONAL;
            $cadena_funcion_correlacion = substr($cadena_funcion_correlacion, 4);
            $cadena_parametros_tipo_linea_base = $cadena_funcion_correlacion;

            // Conversión de formatos de fechas
            $cadena_fecha_inicio_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_BASE_DATOS);
            $cadena_fecha_fin_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_BASE_DATOS);

            // Conversiones de variables estadísticas
            if ($error_estandar === NULL)
            {
                $error_estandar = ID_NINGUNO;
            }
            if ($coeficiente_variacion === NULL)
            {
                $coeficiente_variacion = 0;
            }
            if ($coeficiente_correlacion === NULL)
            {
                $coeficiente_correlacion = 0;
            }

            // Se añade la línea base
            $campo_parametros_extra_dependiente = implode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, array(
                $campo_dependiente,
                $parametros_extra_campo_dependiente));
            $operacion_insercion = "
                INSERT INTO lineas_base (
                    nombre,
                    descripcion,
                    red,
                    clase_sensor,
                    sensor,
                    campo_parametros_extra,
                    tipo,
                    parametros_tipo,
                    intervalo_valores,
                    fecha_inicio_periodo_referencia,
                    fecha_fin_periodo_referencia,
                    error_estandar,
                    coeficiente_variacion,
                    coeficiente_correlacion,
                    horario_semanal,
                    exclusion_fechas
                ) VALUES (
                    '".$bd_red->_($nombre_linea_base)."',
                    '',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($clase_sensor_dependiente)."',
                    '".$bd_red->_($id_sensor_dependiente)."',
                    '".$bd_red->_($campo_parametros_extra_dependiente)."',
                    '".$bd_red->_($tipo_linea_base)."',
                    '".$bd_red->_($cadena_parametros_tipo_linea_base)."',
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

                // Se añaden las variables de la línea base
                anyade_variables_linea_base_sensores_independientes(
                    $id_linea_base,
                    $clases_sensores_independientes,
                    $ids_sensores_independientes,
                    $campos_independientes,
                    $parametros_extra_campos_independientes);

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


    // Añade las variables de la línea base (sensores independientes)
    function anyade_variables_linea_base_sensores_independientes($id_linea_base,
        $clases_sensores_independientes,
        $ids_sensores_independientes,
        $campos_independientes,
        $parametros_extra_campos_independientes)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $numero_variables = count($clases_sensores_independientes);
        $numero_variable = 1;
        for ($i = 0; $i < $numero_variables; $i++)
        {
            $nombre_variable = "x";
            if ($numero_variables > 1)
            {
                $nombre_variable .= $numero_variable;
            }
            $clase_sensor = $clases_sensores_independientes[$i];
            $id_sensor = $ids_sensores_independientes[$i];
            $campo = $campos_independientes[$i];
            $parametros_extra_campo = $parametros_extra_campos_independientes[$i];
            $campo_parametros_extra = implode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, array(
                $campo,
                $parametros_extra_campo));

            $operacion_insercion_variable = "
                INSERT INTO variables_lineas_base (
                    nombre,
                    red,
                    linea_base,
                    clase_sensor,
                    sensor,
                    campo_parametros_extra
                ) VALUES (
                    '".$bd_red->_($nombre_variable)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_linea_base)."',
                    '".$bd_red->_($clase_sensor)."',
                    '".$bd_red->_($id_sensor)."',
                    '".$bd_red->_($campo_parametros_extra)."'
                )";
            $res_insercion_variable = $bd_red->ejecuta_operacion($operacion_insercion_variable);
            if ($res_insercion_variable == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_variable."'");
            }
            $numero_variable += 1;
        }
    }
?>
