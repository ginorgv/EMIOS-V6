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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_EXCEPCION_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_linea_base_padre = $_POST['id_linea_base_padre'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_linea_base_hija = $_POST['id_linea_base_hija'];
    $cadena_horario_semanal = $_POST['horario_semanal'];
    $cadena_inclusion_fechas = $_POST['inclusion_fechas'];

    // Se comprueba si existe una excepción con el mismo nombre en la misma línea base
    $consulta_existe = "
        SELECT nombre
        FROM excepciones_lineas_base
        WHERE
            (linea_base_padre = '".$bd_red->_($id_linea_base_padre)."')
            AND (nombre = '".$bd_red->_($nombre)."')";
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
        // Comprobaciones antes de añadir la excepcion:
        // - Comprobación de bucle en las líneas base hijas
        $anyadir_excepcion = true;

        // Comprobación de bucle en las líneas base hijas
        if ($anyadir_excepcion == true)
        {
            $info_lineas_base_padres = NULL;
            $info_lineas_base_hijas = NULL;
            carga_informacion_lineas_base_padres_hijas_excepciones($info_lineas_base_padres, $info_lineas_base_hijas);
            anyade_linea_base_padre($info_lineas_base_padres, $id_linea_base_padre, $id_linea_base_hija);
            anyade_localizacion_hija($info_lineas_base_hijas, $id_linea_base_padre, $id_linea_base_hija);

            $existe_bucle = existe_bucle_lineas_base_hijas($info_lineas_base_hijas);
            if ($existe_bucle == true)
            {
                $anyadir_excepcion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Hay un bucle en las excepciones de las líneas base");
            }
        }

        // Se añade la excepción de la línea base
        if ($anyadir_excepcion == true)
        {
            // Se añade la excepción de la línea base
            $operacion_insercion = "
                INSERT INTO excepciones_lineas_base (
                    nombre,
                    descripcion,
                    red,
                    linea_base_padre,
                    linea_base_hija,
                    horario_semanal,
                    inclusion_fechas
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$bd_red->_($descripcion)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_linea_base_padre)."',
                    '".$bd_red->_($id_linea_base_hija)."',
                    '".$bd_red->_($cadena_horario_semanal)."',
                    '".$bd_red->_($cadena_inclusion_fechas)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la excepción añadida
                $id_excepcion = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_excepcion = dame_fila_excepcion_linea_base($id_excepcion);

                // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
                invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base_padre);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_excepcion_linea_base($fila_excepcion);

                $res = "OK";
                $msg = $idiomas->_("Excepción añadida correctamente");
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


    // Añade la acción de usuario de adición de excepción de línea base
    function anyade_accion_usuario_anyadir_excepcion_linea_base($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_EXCEPCION_LINEA_BASE;
        $objeto_accion_usuario = $fila["nombre"]." (".dame_nombre_linea_base($fila["linea_base_padre"]).")";

        // Horario semanal
        $fila_linea_base_padre = dame_fila_linea_base($fila["linea_base_padre"]);
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
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LINEA_BASE] = dame_nombre_linea_base($fila["linea_base_hija"]);
        if ($mostrar_horario_semanal == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
                "cadena_horario_semanal" => $fila["horario_semanal"],
                "mostrar_horas" => $mostrar_horas_horario_semanal);
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INCLUSION_FECHAS] = $fila["inclusion_fechas"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
