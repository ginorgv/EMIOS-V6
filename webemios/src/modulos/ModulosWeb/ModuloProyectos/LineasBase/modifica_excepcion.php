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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_hijas_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_EXCEPCION_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_excepcion = $_POST['id_excepcion'];
    $id_linea_base_padre = $_POST['id_linea_base_padre'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_linea_base_hija = $_POST['id_linea_base_hija'];
    $id_linea_base_hija_anterior = $_POST['id_linea_base_hija_anterior'];
    $cadena_horario_semanal = $_POST['horario_semanal'];
    $cadena_inclusion_fechas = $_POST['inclusion_fechas'];

    // Se comprueba si existe otra excepción con el mismo nombre en la misma línea base
    $consulta_existe = "
        SELECT *
        FROM excepciones_lineas_base
        WHERE
            (linea_base_padre = '".$bd_red->_($id_linea_base_padre)."')
            AND (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_excepcion)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una excepción con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar la excepción de la línea base:
        // - Comprobación de bucle en las líneas base hijas
        $modificar_excepcion = true;

        // Comprobación de bucle en las líneas base hijas
        if ($modificar_excepcion == true)
        {
            $info_lineas_base_padres = NULL;
            $info_lineas_base_hijas = NULL;
            carga_informacion_lineas_base_padres_hijas_excepciones($info_lineas_base_padres, $info_lineas_base_hijas);
            elimina_linea_base_padre($info_lineas_base_padres, $id_linea_base_padre, $id_linea_base_hija_anterior);
            elimina_linea_base_hija($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija_anterior);
            anyade_linea_base_padre($info_lineas_base_padres, $id_linea_base_padre, $id_linea_base_hija);
            anyade_localizacion_hija($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija);

            $existe_bucle = existe_bucle_lineas_base_hijas($info_lineas_base_hijas);
            if ($existe_bucle == true)
            {
                $modificar_excepcion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Hay un bucle en las excepciones de las líneas base");
            }
        }

        // Se modifica la excepción de la línea base
        if ($modificar_excepcion == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_excepcion_anterior = dame_fila_excepcion_linea_base($id_excepcion);

            // Se modifica la excepción de la línea base
            $operacion_modificacion = "
                UPDATE excepciones_lineas_base
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    linea_base_hija = '".$bd_red->_($id_linea_base_hija)."',
                    horario_semanal = '".$bd_red->_($cadena_horario_semanal)."',
                    inclusion_fechas = '".$bd_red->_($cadena_inclusion_fechas)."'
                WHERE
                    id = '".$bd_red->_($id_excepcion)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
                invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base_padre);

                // Se recupera la fila actual
                $fila_excepcion_actual = dame_fila_excepcion_linea_base($id_excepcion);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_excepcion_linea_base(
                    $fila_excepcion_actual,
                    $fila_excepcion_anterior);

                $res = "OK";
                $msg = $idiomas->_("Excepción modificada correctamente");
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


    // Añade la acción de usuario de modificación de excepción de línea base
    function anyade_accion_usuario_modificar_excepcion_linea_base($fila_actual, $fila_anterior)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_EXCEPCION_LINEA_BASE;

        // Horario semanal
        $fila_linea_base_padre = dame_fila_linea_base($fila_actual["linea_base_padre"]);
        $intervalo_valores_linea_base_padre = $fila_linea_base_padre["intervalo_valores"];
        switch ($intervalo_valores_linea_base_padre)
        {
            case INTERVALO_VALORES_HORA:
            {
                $mostrar_horario_semanal = true;
                $mostrar_horas_horario_semanal = true;
                break;
            }
            case INTERVALO_VALORES_DIA:
            {
                $mostrar_horario_semanal = true;
                $mostrar_horas_horario_semanal = false;
                break;
            }
            case INTERVALO_VALORES_SEMANA:
            case INTERVALO_VALORES_MES:
            {
                $mostrar_horario_semanal = false;
                $mostrar_horas_horario_semanal = false;
                break;
            }
        }

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
        if ($fila_actual["linea_base_hija"] != $fila_anterior["linea_base_hija"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = dame_nombre_linea_base($fila_actual["linea_base_hija"]);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = dame_nombre_linea_base($fila_anterior["linea_base_hija"]);
        }
        if ($mostrar_horario_semanal == true)
        {
            if ($fila_actual["horario_semanal"] != $fila_anterior["horario_semanal"])
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
                    "cadena_horario_semanal" => $fila_actual["horario_semanal"],
                    "mostrar_horas" => $mostrar_horas_horario_semanal);
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
                    "cadena_horario_semanal" => $fila_anterior["horario_semanal"],
                    "mostrar_horas" => $mostrar_horas_horario_semanal);
            }
        }
        if ($fila_actual["inclusion_fechas"] != $fila_anterior["inclusion_fechas"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCLUSION_FECHAS] = $fila_actual["inclusion_fechas"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_INCLUSION_FECHAS] = $fila_anterior["inclusion_fechas"];
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Nombre de la línea base
        $nombre_linea_base = dame_nombre_linea_base($fila_actual["linea_base_padre"]);

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
