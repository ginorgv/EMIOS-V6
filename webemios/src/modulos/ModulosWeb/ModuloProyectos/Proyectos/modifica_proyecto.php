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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PROYECTO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_proyecto = $_POST['id_proyecto'];
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

    // Conversión de fechas
    $cadena_fecha_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);
    $cadena_fecha_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], FORMATO_FECHA_BASE_DATOS);

    // Se comprueba si existe otro proyecto con el mismo nombre
    $consulta_existe = "
        SELECT *
        FROM proyectos
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = ".$_SESSION["id_red"].")
            AND (id <> '".$bd_red->_($id_proyecto)."')";
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
        // Comprobaciones antes de modificar el proyecto:
        // - Si el campo es puntual el tipo de objetivo no puede ser absoluto
        // - El sensor y el campo del proyecto y la línea base deben ser el mismo
        $modificar_proyecto = true;

        // Si el campo es puntual el tipo de objetivo no puede ser absoluto
        if ($modificar_proyecto == true)
        {
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
            if (($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES) && ($tipo_objetivo == TIPO_OBJETIVO_PROYECTO_PORCENTUAL))
            {
                $modificar_proyecto = false;

                $res = "ERROR";
                $msg = $idiomas->_("Si el campo de sensor es puntual el tipo de objetivo sólo puede ser absoluto");
            }
        }

        // El sensor y el campo del proyecto y la línea base deben ser el mismo
        // - Nota: Se permite el campo 'coste' en el proyecto e 'incremento' en la línea base en la clase de sensor de energía activa o de gas
        if ($modificar_proyecto == true)
        {
            if ($id_linea_base != ID_NINGUNO)
            {
                $fila_linea_base = dame_fila_linea_base($id_linea_base);
                if ($id_sensor != $fila_linea_base["sensor"])
                {
                    $modificar_proyecto = false;

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
                        $modificar_proyecto = false;

                        $res = "ERROR";
                        $msg = $idiomas->_("Los campos de sensor del proyecto y de la línea base no son compatibles");
                    }
                }
            }
        }

        // See modifica el proyecto
        if ($modificar_proyecto == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_proyecto_anterior = dame_fila_proyecto($id_proyecto);

            // Valor objetivo
            if ($valor_objetivo == "")
            {
                $cadena_valor_objetivo = "NULL";
            }
            else
            {
                $cadena_valor_objetivo = "'".$valor_objetivo."'";
            }

            // Se modifica el proyecto
            $operacion_modificacion = "
                UPDATE proyectos
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    clase_sensor = '".$bd_red->_($clase_sensor)."',
                    sensor = '".$bd_red->_($id_sensor)."',
                    campo = '".$bd_red->_($campo)."',
                    intervalo_valores = '".$bd_red->_($intervalo_valores)."',
                    linea_base = '".$bd_red->_($id_linea_base)."',
                    tipo_objetivo = '".$bd_red->_($tipo_objetivo)."',
                    tipo_valor_objetivo = '".$bd_red->_($tipo_valor_objetivo)."',
                    valor_objetivo = ".$cadena_valor_objetivo.",
                    fecha_inicio = '".$bd_red->_($cadena_fecha_inicio_base_datos_local)."',
                    fecha_fin = '".$bd_red->_($cadena_fecha_fin_base_datos_local)."',
                    hora_ultimo_calculo_avance = NULL,
                    hora_fin_valores_avance = NULL,
                    hora_ultimos_valores_avance = NULL,
                    valor_real_avance = NULL,
                    valor_simulado_avance = NULL,
                    porcentaje_finalizacion = NULL,
                    estado_avance = '".ESTADO_AVANCE_PROYECTO_NINGUNO."',
                    estado = '".ESTADO_PROYECTO_NINGUNO."'
                WHERE
                    id = '".$bd_red->_($id_proyecto)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_proyecto_actual = dame_fila_proyecto($id_proyecto);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_proyecto(
                    $fila_proyecto_actual,
                    $fila_proyecto_anterior);

                $res = "OK";
                $msg = $idiomas->_("Proyecto modificado correctamente");
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


    // Añade la acción de usuario de adición del proyecto
    function anyade_accion_usuario_modificar_proyecto($fila_actual, $fila_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_PROYECTO;

        // Parámetros de la acción
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
            $nombre_sensor = dame_nombre_sensor($fila_actual["sensor"]);
            $nombre_sensor_anterior = dame_nombre_sensor($fila_anterior["sensor"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = $nombre_sensor;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = $nombre_sensor_anterior;
        }
        if ($fila_actual["campo"] != $fila_anterior["campo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO] = $fila_actual["campo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_CAMPO] = $fila_anterior["campo"];
        }
        if ($fila_actual["intervalo_valores"] != $fila_anterior["intervalo_valores"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila_actual["intervalo_valores"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila_anterior["intervalo_valores"];
        }
        if ($fila_actual["linea_base"] != $fila_anterior["linea_base"])
        {
            $nombre_linea_base = dame_nombre_linea_base($fila_actual["linea_base"]);
            $nombre_linea_base_anterior = dame_nombre_linea_base($fila_anterior["linea_base"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = $nombre_linea_base;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = $nombre_linea_base_anterior;
        }
        if ($fila_actual["tipo_objetivo"] != $fila_anterior["tipo_objetivo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_OBJETIVO_PROYECTO] = $fila_actual["tipo_objetivo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_OBJETIVO_PROYECTO] = $fila_anterior["tipo_objetivo"];
        }
        if ($fila_actual["tipo_valor_objetivo"] != $fila_anterior["tipo_valor_objetivo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_VALOR_OBJETIVO_PROYECTO] = $fila_actual["tipo_valor_objetivo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_VALOR_OBJETIVO_PROYECTO] = $fila_anterior["tipo_valor_objetivo"];
        }
        if ($fila_actual["valor_objetivo"] != $fila_anterior["valor_objetivo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_VALOR_OBJETIVO_PROYECTO] = $fila_actual["valor_objetivo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_VALOR_OBJETIVO_PROYECTO] = $fila_anterior["valor_objetivo"];
        }
        if ($fila_actual["fecha_inicio"] != $fila_anterior["fecha_inicio"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_actual["fecha_inicio"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila_anterior["fecha_inicio"];
        }
        if ($fila_actual["fecha_fin"] != $fila_anterior["fecha_fin"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_actual["fecha_fin"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila_anterior["fecha_fin"];
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
