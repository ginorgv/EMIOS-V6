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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_linea_base = $_POST['id_linea_base'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $clase_sensor = $_POST['clase_sensor'];
    $id_sensor = $_POST['id_sensor'];
    $campo_parametros_extra = $_POST['campo_parametros_extra'];
    $tipo = $_POST['tipo'];
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

    // Parámetros auxiliares
    $id_sensor_anterior = $_POST['id_sensor_anterior'];
    $campo_anterior = $_POST['campo_anterior'];
    $intervalo_valores_anterior = $_POST['intervalo_valores_anterior'];
    $parametros_auxiliares = $_POST['parametros_auxiliares'];

    // Conversión de fechas
    $cadena_fecha_inicio_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_inicio_periodo_referencia_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $cadena_fecha_fin_periodo_referencia_base_datos_local = convierte_formato_fecha($cadena_fecha_fin_periodo_referencia_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Tipo de mensaje en la respuesta (correcta)
    $tipo_mensaje = TIPO_MENSAJE_INFORMACION;

    // Mensaje extra en la respuesta y cerrar ventana de modificación
    $msg_extra = NULL;
    $cerrar_ventana = true;

    // Se comprueba si existe otra línea base con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM lineas_base
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_linea_base)."')";
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
        // Comprobaciones antes de modificar la línea base:
        // - No se permite una clase sin procesado de valores
        // - No se permite la clase energía activa y coste (tiene que ser incremento)
        // - Si la línea base es funcional, se valida la fórmula de cálculo de valores
        // - No se permite cambiar el intervalo de valores si tiene excepciones (sería diferente al de las excepciones)
        // - No se permite cambiar el intervalo de valores si está en alguna excepción (sería diferente al de la línea base padre)
        // - No se permite modificar el sensor, campo o intervalo de valores si está en algún proyecto (serían diferentes a los del proyecto)
        $modificar_linea_base = true;

        // No se permite una clase sin procesado de valores
        if ($modificar_linea_base == true)
        {
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
            if ($caracteristicas_clase_sensor["procesado_valores"] == false)
            {
                $modificar_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("La clase de sensor seleccionada no tiene procesado de valores");
            }
        }

        // No se permite la clase de energía activa o de gas y coste (tiene que ser consumo)
        if ($modificar_linea_base == true)
        {
            if ((($clase_sensor == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo == CAMPO_COSTE)) ||
                (($clase_sensor == CLASE_SENSOR_GAS) && ($campo == CAMPO_COSTE)))
            {
                $modificar_linea_base = false;

                $res = "ERROR";
                $msg = $idiomas->_("El campo de sensor seleccionado debe ser consumo (el coste se calculará en el proyecto)");
            }
        }

        // Comprobación de operador condicional
        if (($modificar_linea_base == true) && ($tipo == TIPO_LINEA_BASE_FUNCIONAL))
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            $funcion_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];

            if (strpos($funcion_valores, " if ") !== false)
            {
                $modificar_linea_base = False;

                $res = "ERROR";
                $msg = $idiomas->_("Operador condicional no permitido en la función de valores");
            }
        }

        // Si los datos son correctos se evalua la función de valores
        if (($modificar_linea_base == true) && ($tipo == TIPO_LINEA_BASE_FUNCIONAL))
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            $funcion_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];

            // Se recuperan las variables de la línea base (para evaluar la función)
            $consulta_variables_linea_base = "
                SELECT nombre
                FROM variables_lineas_base
                WHERE
                    linea_base = '".$bd_red->_($id_linea_base)."'
                ORDER BY nombre ASC";
            $res_variables_linea_base = $bd_red->ejecuta_consulta($consulta_variables_linea_base);
            if ($res_variables_linea_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_variables_linea_base."'");
            }
            $numero_variables_linea_base = $res_variables_linea_base->dame_numero_filas();
            $nombres_variables_linea_base = array();
            while ($fila_variable_linea_base = $res_variables_linea_base->dame_siguiente_fila())
            {
                array_push($nombres_variables_linea_base, $fila_variable_linea_base["nombre"]);
            }

            // Se recuperan los valores de prueba de la función de valores (si los hay)
            if ($parametros_auxiliares == "")
            {
                if ($numero_variables_linea_base > 0)
                {
                    $valores_variables_linea_base = array_fill(0, $numero_variables_linea_base, VALOR_PRUEBA_DEFECTO_FUNCION_LINEA_BASE);
                }
                else
                {
                    $valores_variables_linea_base = array();
                }
                $mostrar_valor_evaluado_funcion_valores = False;
            }
            else
            {
                $valores_variables_linea_base = explode(",", $parametros_auxiliares);
                $mostrar_valor_evaluado_funcion_valores = True;
            }

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FUNCION_VALORES,
                    "funcion_valores" => $funcion_valores,
                    "nombres_variables" => $nombres_variables_linea_base,
                    "valores_variables" => $valores_variables_linea_base
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la función de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["funcion_correcta"] == 0)
            {
                $modificar_linea_base = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la función de valores")."\n(".
                    $descripcion_error.")";
            }
            else
            {
                if ($mostrar_valor_evaluado_funcion_valores == True)
                {
                    $valor_evaluado_funcion_valores = $resultado_funcion_externa["valor"];
                    $cadena_valor_evaluado_funcion_valores = formatea_numero($valor_evaluado_funcion_valores, 2);
                    $msg_extra = $idiomas->_("valor de prueba evaluado de la función de valores").": ".$cadena_valor_evaluado_funcion_valores;
                    $cerrar_ventana = false;
                }
            }
        }

        // No se permite cambiar el intervalo de valores si hay excepciones (sería diferente al de las excepciones)
        if ($modificar_linea_base == true)
        {
            if ($intervalo_valores != $intervalo_valores_anterior)
            {
                $consulta_excepciones = "
                    SELECT *
                    FROM excepciones_lineas_base
                    WHERE
                        linea_base_padre = '".$bd_red->_($id_linea_base)."'";
                $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
                if ($res_excepciones == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
                }
                if ($res_excepciones->dame_numero_filas() > 0)
                {
                    $modificar_linea_base = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede modificar el intervalo de valores porque la línea base tiene excepciones");
                }
            }
        }

        // No se permite cambiar el intervalo de valores si está en alguna excepción (sería diferente al de la línea base padre)
        if ($modificar_linea_base == true)
        {
            if ($intervalo_valores != $intervalo_valores_anterior)
            {
                $consulta_excepciones = "
                    SELECT *
                    FROM excepciones_lineas_base
                    WHERE
                        linea_base_hija = '".$bd_red->_($id_linea_base)."'
                    ORDER BY nombre ASC";
                $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
                if ($res_excepciones == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_excepciones."'");
                }
                if ($res_excepciones->dame_numero_filas() > 0)
                {
                    $modificar_linea_base = false;

                    $fila_excepcion_linea_base = $res_excepciones_linea_base->dame_siguiente_fila();
                    $id_linea_base_padre_excepcion_linea_base = $fila_excepcion_linea_base["linea_base_padre"];
                    $nombre_excepcion_linea_base = $fila_excepcion_linea_base["nombre"];
                    $nombre_linea_base_padre_excepcion_linea_base = dame_nombre_linea_base($id_linea_base_padre_excepcion_linea_base);

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede modificar el intervalo de valores porque la línea base está en alguna excepción")."\n(".
                        $idiomas->_("excepción").": ".$nombre_excepcion_linea_base.", ".
                        $idiomas->_("línea base").": ".$nombre_linea_base_padre_excepcion_linea_base.")";
                }
            }
        }

        // No se permite modificar el sensor, campo o intervalo de valores si está en algún proyecto (serían diferentes a los del proyecto)
        if ($modificar_linea_base == true)
        {
            if (($id_sensor != $id_sensor_anterior) || ($campo != $campo_anterior) ||
                ($intervalo_valores != $intervalo_valores_anterior))
            {
                $consulta_proyectos = "
                    SELECT nombre
                    FROM proyectos
                    WHERE
                        linea_base = '".$bd_red->_($id_linea_base)."'";
                $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
                if ($res_proyectos == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
                }
                if ($res_proyectos->dame_numero_filas() > 0)
                {
                    $modificar_linea_base = false;

                    $fila_proyecto = $res_proyectos->dame_siguiente_fila();
                    $nombre_proyecto = $fila_proyecto["nombre"];

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede modificar el sensor, el campo de sensor o el intervalo de valores porque la línea base está asignada a algún proyecto")."\n(".
                        $nombre_proyecto.")";
                }
            }
        }

        // Se modifica la línea base
        if ($modificar_linea_base == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_linea_base_anterior = dame_fila_linea_base($id_linea_base);

            // Se modifica la línea base
            $operacion_modificacion = "
                UPDATE lineas_base
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    clase_sensor = '".$bd_red->_($clase_sensor)."',
                    sensor = '".$bd_red->_($id_sensor)."',
                    campo_parametros_extra = '".$bd_red->_($campo_parametros_extra)."',
                    tipo = '".$bd_red->_($tipo)."',
                    parametros_tipo = '".$bd_red->_($cadena_parametros_tipo)."',
                    parametros_tipo_json = '".$bd_red->_($parametros_tipo_json)."',
                    intervalo_valores = '".$bd_red->_($intervalo_valores)."',
                    fecha_inicio_periodo_referencia = '".$bd_red->_($cadena_fecha_inicio_periodo_referencia_base_datos_local)."',
                    fecha_fin_periodo_referencia = '".$bd_red->_($cadena_fecha_fin_periodo_referencia_base_datos_local)."',
                    error_estandar = '".$bd_red->_($error_estandar)."',
                    coeficiente_variacion = '".$bd_red->_($coeficiente_variacion)."',
                    coeficiente_correlacion = '".$bd_red->_($coeficiente_correlacion)."',
                    horario_semanal = '".$bd_red->_($cadena_horario_semanal)."',
                    exclusion_fechas = '".$bd_red->_($cadena_exclusion_fechas)."'
                WHERE
                    id = '".$bd_red->_($id_linea_base)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
                invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base);

                // Se recupera la fila actual
                $fila_linea_base_actual = dame_fila_linea_base($id_linea_base);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_linea_base(
                    $fila_linea_base_actual,
                    $fila_linea_base_anterior);

                $res = "OK";
                $msg = $idiomas->_("Línea base modificada correctamente");
                if ($msg_extra !== NULL)
                {
                    $msg .= "\n(".$msg_extra.")";
                }
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "cerrar_ventana" => $cerrar_ventana))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación de la línea base
    function anyade_accion_usuario_modificar_linea_base($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_LINEA_BASE;

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
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila_actual["campo_parametros_extra"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila_anterior["campo_parametros_extra"];
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_actual["clase_sensor"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila_anterior["clase_sensor"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_LINEA_BASE] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_LINEA_BASE] = $fila_anterior["tipo"];
        }
        if ($fila_actual["parametros_tipo"] != $fila_anterior["parametros_tipo"])
        {
            $parametros_tipo_actuales = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actual["parametros_tipo"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_LINEA_BASE] = array(
                "tipo" => $fila_actual["tipo"],
                "parametros_tipo" => $parametros_tipo_actuales);
            $parametros_tipo_anteriores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_anterior["parametros_tipo"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_LINEA_BASE] = array(
                "tipo" => $fila_anterior["tipo"],
                "parametros_tipo" => $parametros_tipo_anteriores);
        }
        if ($fila_actual["intervalo_valores"] != $fila_anterior["intervalo_valores"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila_actual["intervalo_valores"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila_anterior["intervalo_valores"];
        }
        if ($fila_actual["fecha_inicio_periodo_referencia"] != $fila_anterior["fecha_inicio_periodo_referencia"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO_PERIODO_REFERENCIA_LINEA_BASE] = $fila_actual["fecha_inicio_periodo_referencia"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_INICIO_PERIODO_REFERENCIA_LINEA_BASE] = $fila_anterior["fecha_inicio_periodo_referencia"];
        }
        if ($fila_actual["fecha_fin_periodo_referencia"] != $fila_anterior["fecha_fin_periodo_referencia"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN_PERIODO_REFERENCIA_LINEA_BASE] = $fila_actual["fecha_fin_periodo_referencia"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_FIN_PERIODO_REFERENCIA_LINEA_BASE] = $fila_anterior["fecha_fin_periodo_referencia"];
        }
        if ($fila_actual["error_estandar"] != $fila_anterior["error_estandar"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ERROR_ESTANDAR_LINEA_BASE] = $fila_actual["error_estandar"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ERROR_ESTANDAR_LINEA_BASE] = $fila_anterior["error_estandar"];
        }
        if ($fila_actual["coeficiente_variacion"] != $fila_anterior["coeficiente_variacion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTE_VARIACION_LINEA_BASE] = $fila_actual["coeficiente_variacion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COEFICIENTE_VARIACION_LINEA_BASE] = $fila_anterior["coeficiente_variacion"];
        }
        if ($fila_actual["coeficiente_correlacion"] != $fila_anterior["coeficiente_correlacion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTE_CORRELACION_LINEA_BASE] = $fila_actual["coeficiente_correlacion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COEFICIENTE_CORRELACION_LINEA_BASE] = $fila_anterior["coeficiente_correlacion"];
        }
        if ($fila_actual["horario_semanal"] != $fila_anterior["horario_semanal"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
                "cadena_horario_semanal" => $fila_actual["horario_semanal"],
                "mostrar_horas" => true);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
                "cadena_horario_semanal" => $fila_anterior["horario_semanal"],
                "mostrar_horas" => true);
        }
        if ($fila_actual["exclusion_fechas"] != $fila_anterior["exclusion_fechas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXCLUSION_FECHAS] = $fila_actual["exclusion_fechas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_EXCLUSION_FECHAS] = $fila_anterior["exclusion_fechas"];
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
