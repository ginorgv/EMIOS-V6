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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PROYECTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $clase_sensor = $_POST['clase_sensor'];
    $id_sensor = $_POST['id_sensor'];
    $campo = $_POST['campo'];
    $intervalo_valores = $_POST['intervalo_valores'];
    $id_linea_base = $_POST['id_linea_base'];
    $tipo_objetivo = $_POST['tipo_objetivo'];
    $tipo_valor_objetivo = $_POST['tipo_valor_objetivo'];
    $valor_objetivo = $_POST['valor_objetivo'];
    $cadena_fecha_inicio_local_local = $_POST['fecha_inicio'];
    $cadena_fecha_fin_local_local = $_POST['fecha_fin'];
    $id_proyecto_anterior = $_POST["id_proyecto_anterior"];

    // Conversión de fechas
    $cadena_fecha_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $cadena_fecha_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Se comprueba si existen el número máximo de proyectos
    $consulta_numero_proyectos = "
        SELECT
            COUNT(*) AS numero_proyectos
        FROM proyectos
        WHERE
            red = '".$_SESSION["id_red"]."'";
    $res_numero_proyectos = $bd_red->ejecuta_consulta($consulta_numero_proyectos);
    if (($res_numero_proyectos == false) || ($res_numero_proyectos->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_proyectos."'");
    }

    $fila_numero_proyectos = $res_numero_proyectos->dame_siguiente_fila();
    $numero_maximo_proyectos = dame_numero_maximo_elementos_modulo(MODULO_PROYECTOS);
    if (($numero_maximo_proyectos != 0) &&
        ($fila_numero_proyectos['numero_proyectos'] >= $numero_maximo_proyectos))
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen el número máximo de proyectos");
    }
    else
    {
        // Se comprueba si existe un proyecto con el mismo nombre
        $consulta_existe = "
            SELECT nombre
            FROM proyectos
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
            $msg = $idiomas->_("Ya existe un proyecto con el mismo nombre");
        }
        else
        {
            // Tipo de valores de campo
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);

            // Comprobaciones antes de añadir el proyecto:
            // - Si el campo es puntual, el tipo de objetivo no puede ser absoluto
            // - El sensor y el campo del proyecto y la línea base deben ser el mismo
            $anyadir_proyecto = true;

            // Si el campo es puntual, el tipo de objetivo no puede ser absoluto
            if ($anyadir_proyecto == true)
            {
                if (($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES) && ($tipo_objetivo == TIPO_OBJETIVO_PROYECTO_PORCENTUAL))
                {
                    $anyadir_proyecto = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Si el campo de sensor es puntual el tipo de objetivo sólo puede ser absoluto");
                }
            }

            // El sensor y el campo del proyecto y la línea base deben ser el mismo
            // - Nota: Se permite el campo 'coste' en el proyecto e 'incremento' en la línea base en la clase de sensor de energía activa o de gas
            if ($anyadir_proyecto == true)
            {
                if ($id_linea_base != ID_NINGUNO)
                {
                    $fila_linea_base = dame_fila_linea_base($id_linea_base);
                    if ($id_sensor != $fila_linea_base["sensor"])
                    {
                        $anyadir_proyecto = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Los sensores del proyecto y de la línea base deben ser el mismo");
                    }
                    else
                    {
                        $campo_parametros_extra_linea_base = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $fila_linea_base["campo_parametros_extra"]);
                        $campo_linea_base = $campo_parametros_extra_linea_base[0];
                        if (($campo == $campo_linea_base) ||
                            (($clase_sensor == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo == CAMPO_COSTE) && ($campo_linea_base == CAMPO_INCREMENTO)) ||
                            (($clase_sensor == CLASE_SENSOR_GAS) && ($campo == CAMPO_COSTE) && ($campo_linea_base == CAMPO_CONSUMO)))
                        {
                            $campo_correcto = true;
                        }
                        else
                        {
                            $campo_correcto = false;
                        }
                        if ($campo_correcto == false)
                        {
                            $anyadir_proyecto = false;

                            $res = "ERROR";
                            $msg = $idiomas->_("Los campos de sensor del proyecto y de la línea base no son compatibles");
                        }
                    }
                }
            }

            // Se añade el proyecto
            if ($anyadir_proyecto == true)
            {
                // Se añade el proyecto
                if ($valor_objetivo == "")
                {
                    $cadena_valor_objetivo = "NULL";
                }
                else
                {
                    $cadena_valor_objetivo = "'".$valor_objetivo."'";
                }
                $operacion_insercion = "
                    INSERT INTO proyectos (
                        nombre,
                        red,
                        descripcion,
                        clase_sensor,
                        sensor,
                        campo,
                        intervalo_valores,
                        linea_base,
                        tipo_objetivo,
                        tipo_valor_objetivo,
                        valor_objetivo,
                        fecha_inicio,
                        fecha_fin,
                        hora_ultimo_calculo_avance,
                        hora_fin_valores_avance,
                        hora_ultimos_valores_avance,
                        valor_real_avance,
                        valor_simulado_avance,
                        porcentaje_finalizacion,
                        estado_avance,
                        estado
                    ) VALUES (
                        '".$bd_red->_($nombre)."',
                        '".$_SESSION["id_red"]."',
                        '".$bd_red->_($descripcion)."',
                        '".$bd_red->_($clase_sensor)."',
                        '".$bd_red->_($id_sensor)."',
                        '".$bd_red->_($campo)."',
                        '".$bd_red->_($intervalo_valores)."',
                        '".$bd_red->_($id_linea_base)."',
                        '".$bd_red->_($tipo_objetivo)."',
                        '".$bd_red->_($tipo_valor_objetivo)."',
                        ".$cadena_valor_objetivo.",
                        '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."',
                        '".$bd_red->_($cadena_fecha_fin_base_datos_local)."',
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        NULL,
                        '".ESTADO_AVANCE_PROYECTO_NINGUNO."',
                        '".ESTADO_PROYECTO_NINGUNO."'
                    )";
                $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
                if ($res_insercion == true)
                {
                    // Se recuperan el id y la fila del proyecto añadido
                    $id_proyecto = $bd_red->dame_id_autoincremental_ultima_insercion();
                    $fila_proyecto = dame_fila_proyecto($id_proyecto);

                    // Si el identificador de proyecto existe, es un duplicado de un proyecto existente:
                    // - Se duplican los valores adicionales del proyecto (sólo si el tipo de valores es incremental)
                    if (($id_proyecto_anterior != ID_NINGUNO) && ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES))
                    {
                        // Se duplican los valores adicionales del proyecto anterior
                        duplica_valores_adicionales_proyecto_anterior($id_proyecto_anterior, $id_proyecto);
                    }

                    // Se añade la acción de usuario
                    anyade_accion_usuario_anyadir_proyecto($fila_proyecto);

                    $res = "OK";
                    $msg = $idiomas->_("Proyecto añadido correctamente");
                }
                else
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion."'");
                }
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


    // Duplica los valores adicionales del proyecto anterior
    function duplica_valores_adicionales_proyecto_anterior($id_proyecto_anterior, $id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los valores adicionales del proyecto anterior, se cambia el proyecto y se añaden
        $consulta_valores_adicionales = "
            SELECT *
            FROM valores_adicionales_proyectos
            WHERE
                proyecto = '".$bd_red->_($id_proyecto_anterior)."'";
        $res_valores_adicionales = $bd_red->ejecuta_consulta($consulta_valores_adicionales);
        if ($res_valores_adicionales == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_adicionales."'");
        }

        while ($fila_valor_adicional = $res_valores_adicionales->dame_siguiente_fila())
        {
            $operacion_insercion_valor_adicional = "
                INSERT INTO valores_adicionales_proyectos (
                    nombre,
                    red,
                    proyecto,
                    destino,
                    valor,
                    periodicidad,
                    fecha_inicio,
                    fecha_fin,
                    aplicar_intervalos_sin_valores
                ) VALUES (
                    '".$bd_red->_($fila_valor_adicional["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_proyecto)."',
                    '".$bd_red->_($fila_valor_adicional["destino"])."',
                    '".$bd_red->_($fila_valor_adicional["valor"])."',
                    '".$bd_red->_($fila_valor_adicional["periodicidad"])."',
                    '".$bd_red->_($fila_valor_adicional["fecha_inicio"])."',
                    '".$bd_red->_($fila_valor_adicional["fecha_fin"])."',
                    '".$bd_red->_($fila_valor_adicional["aplicar_intervalos_sin_valores"])."'
                )";
            $res_insercion_valor_adicional = $bd_red->ejecuta_operacion($operacion_insercion_valor_adicional);
            if ($res_insercion_valor_adicional == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_valor_adicional."'");
            }
        }
    }


    // Añade la acción de usuario de adición del proyecto
    function anyade_accion_usuario_anyadir_proyecto($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_PROYECTO;
        $objeto_accion_usuario = $fila["nombre"];

        // Nombres de parámetros
        $nombre_sensor = dame_nombre_sensor($fila["sensor"]);
        $nombre_linea_base = dame_nombre_linea_base($fila["linea_base"]);

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase_sensor"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = $nombre_sensor;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO] = $fila["campo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila["intervalo_valores"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = $nombre_linea_base;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_OBJETIVO_PROYECTO] = $fila["tipo_objetivo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_VALOR_OBJETIVO_PROYECTO] = $fila["tipo_valor_objetivo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_OBJETIVO_PROYECTO] = $fila["valor_objetivo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila["fecha_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila["fecha_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
